<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Competition;
use App\Models\Listing;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\UserContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['data' => Booking::where('user_id', $request->user()->id)->latest()->paginate(min((int) $request->integer('per_page', 20), 100))]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['item_type' => ['required', Rule::in(['competition', 'stay', 'experience', 'product'])],
            'item_id' => ['required', 'uuid'], 'booking_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:today'],
            'guests' => ['sometimes', 'integer', 'min:1', 'max:100'], 'quantity' => ['sometimes', 'integer', 'min:1', 'max:100'], 'notes' => ['sometimes', 'nullable', 'string', 'max:2000']]);

        $booking = DB::transaction(function () use ($request, $data) {
            $hostId = null; $listingId = null; $amount = 0; $itemType = $data['item_type'];
            if ($itemType === 'competition') {
                $item = Competition::whereKey($data['item_id'])->lockForUpdate()->firstOrFail();
                abort_unless(in_array($item->status, ['open', 'upcoming'], true), 422, 'This competition is not accepting registrations.');
                abort_if($item->capacity && $item->participants_count >= $item->capacity, 422, 'This competition is full.');
                $hostId = $item->host_id; $amount = (int) $item->entry_fee;
            } else {
                $item = Listing::whereKey($data['item_id'])->where('type', $itemType)->where('status', 'active')->firstOrFail();
                abort_if($item->host_id === $request->user()->id, 403, 'You cannot book your own listing.');
                $hostId = $item->host_id; $listingId = $item->id;
                $quantity = $itemType === 'product' ? (int) ($data['quantity'] ?? 1) : (int) ($data['guests'] ?? 1);
                abort_if($item->capacity && $quantity > $item->capacity, 422, 'Requested quantity exceeds capacity.');
                $amount = (int) round($item->price * (100 - min(100, (int) $item->discount)) / 100) * $quantity;
            }

            $booking = Booking::create(['user_id' => $request->user()->id, 'item_type' => $itemType, 'item_id' => $item->id,
                'host_id' => $hostId, 'listing_id' => $listingId, 'status' => 'pending', 'amount' => $amount,
                'booking_date' => $data['booking_date'] ?? null, 'guests' => (int) ($data['quantity'] ?? $data['guests'] ?? 1), 'notes' => $data['notes'] ?? null]);
            return $booking;
        });

        Notification::create(['user_id' => $booking->user_id, 'type' => 'booking_status', 'title' => 'Booking confirmation',
            'body' => 'Your booking has been received.', 'link' => '/payment/'.$booking->id, 'read' => false]);
        return response()->json(['data' => $this->present($booking)], 201);
    }

    public function show(Request $request, Booking $booking)
    {
        $this->authorizeBooking($request, $booking);
        return response()->json(['data' => $this->present($booking)]);
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled', 'completed'])], 'notes' => ['sometimes', 'nullable', 'string', 'max:2000']]);
        $userId = $request->user()->id;
        $isHost = $booking->host_id === $userId;
        $isOwner = $booking->user_id === $userId;
        $isAdmin = $request->user()->is_super_admin || $request->user()->userRoleByUserId()->where('role', 'admin')->exists();
        abort_unless($isAdmin || ($isHost && in_array($data['status'], ['confirmed', 'cancelled', 'completed'], true)) || ($isOwner && in_array($data['status'], ['confirmed', 'cancelled'], true)), 403);
        if ($data['status'] === 'confirmed') {
            abort_unless(Payment::where('booking_id', $booking->id)->where('status', 'paid')->exists(), 422, 'Only a verified payment webhook can confirm this booking.');
        }
        if ($data['status'] === 'cancelled') {
            abort_unless(!Payment::where('booking_id', $booking->id)->where('status', 'paid')->exists(), 422, 'Paid bookings cannot be cancelled until refunds are supported.');
            abort_unless(!Payment::where('booking_id', $booking->id)->where('status', 'pending')->where('expires_at', '>', now())->exists(), 422, 'Wait for the active payment attempt to finish before cancelling.');
        }
        abort_if($booking->status === 'completed' || $booking->status === 'cancelled', 422, 'This booking can no longer be changed.');
        $booking->update($data);
        if ($booking->status === 'cancelled') {
            Payment::where('booking_id', $booking->id)->where('status', 'pending')->update(['status' => 'cancelled']);
        }
        if ($booking->status !== 'pending' && $booking->user_id !== $userId) {
            Notification::create(['user_id' => $booking->user_id, 'type' => 'booking_status', 'title' => 'Booking status updated',
                'body' => 'Your booking is '.$booking->status.'.', 'link' => '/dashboard', 'read' => false]);
        }
        return response()->json(['data' => $this->present($booking->refresh())]);
    }

    public function destroy(Request $request, Booking $booking)
    {
        $isAdmin = $request->user()->is_super_admin || $request->user()->userRoleByUserId()->where('role', 'admin')->exists();
        abort_unless($isAdmin || $booking->host_id === $request->user()->id, 403);
        abort_if(Payment::where('booking_id', $booking->id)->exists(), 422, 'A booking with a payment attempt must be retained for payment records.');
        $booking->delete();
        return response()->noContent();
    }

    public function hostIndex(Request $request)
    {
        $rows = Booking::where('host_id', $request->user()->id)->with('userByUserId')->latest()->paginate(min((int) $request->integer('per_page', 100), 100));
        $rows->getCollection()->transform(function (Booking $booking) {
            $booking->guest_name = $booking->userByUserId?->profileById?->display_name;
            $booking->guest_phone = UserContact::where('user_id', $booking->user_id)->value('phone');
            return $booking;
        });
        return response()->json(['data' => $rows]);
    }

    public function participants(Request $request, Competition $competition)
    {
        abort_unless($competition->host_id === $request->user()->id || $request->user()->userRoleByUserId()->where('role', 'admin')->exists(), 403);
        return response()->json(['data' => Booking::with('userByUserId')->where('item_type', 'competition')->where('item_id', $competition->id)->latest()->paginate(50)]);
    }

    public function hostStats(Request $request)
    {
        $bookings = Booking::where('host_id', $request->user()->id);
        $count = (clone $bookings)->count();
        $revenue = (clone $bookings)->whereNotIn('status', ['cancelled'])->sum('amount');
        return response()->json(['data' => ['revenue' => (int) $revenue, 'pending_revenue' => (int) (clone $bookings)->where('status', 'pending')->sum('amount'),
            'bookings_count' => $count, 'listings_count' => Listing::where('host_id', $request->user()->id)->count(), 'average_booking_value' => $count ? round($revenue / $count, 2) : 0]]);
    }

    private function authorizeBooking(Request $request, Booking $booking): void
    {
        abort_unless(in_array($request->user()->id, [$booking->user_id, $booking->host_id], true) || $request->user()->userRoleByUserId()->where('role', 'admin')->exists(), 403);
    }

    private function present(Booking $booking): array
    {
        $item = $booking->item_type === 'competition' ? Competition::find($booking->item_id) : Listing::find($booking->listing_id ?: $booking->item_id);
        return [...$booking->toArray(), 'item' => $item ? ['id' => $item->id, 'title' => $item->title, 'title_en' => $item->title_en ?? null, 'image' => $item->images[0] ?? $item->image_url ?? null] : null];
    }
}
