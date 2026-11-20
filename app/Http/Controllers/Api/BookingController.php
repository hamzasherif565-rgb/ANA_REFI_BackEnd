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
use Illuminate\Database\QueryException;
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
            'item_id' => ['required', 'uuid'], 'booking_date' => ['required_if:item_type,stay,experience', 'nullable', 'date', 'after_or_equal:today'],
            'guests' => ['sometimes', 'integer', 'min:1', 'max:100'], 'quantity' => ['sometimes', 'integer', 'min:1', 'max:100'], 'notes' => ['sometimes', 'nullable', 'string', 'max:2000']]);

        try {
            $booking = DB::transaction(function () use ($request, $data) {
            $hostId = null; $listingId = null; $amount = 0; $itemType = $data['item_type'];
            if ($itemType === 'competition') {
                $item = Competition::whereKey($data['item_id'])->lockForUpdate()->firstOrFail();
                abort_unless(in_array($item->status, ['open', 'upcoming'], true), 422, 'This competition is not accepting registrations.');
                abort_if($item->capacity && $item->participants_count >= $item->capacity, 422, 'This competition is full.');
                abort_if(Booking::where('user_id', $request->user()->id)->where('item_type', 'competition')
                    ->where('item_id', $item->id)->where('status', '!=', 'cancelled')->exists(), 422, 'You are already registered for this competition.');
                $hostId = $item->host_id; $amount = (int) $item->entry_fee;
            } else {
                $item = Listing::whereKey($data['item_id'])->where('type', $itemType)->where('status', 'active')->lockForUpdate()->firstOrFail();
                abort_if($item->host_id === $request->user()->id, 403, 'You cannot book your own listing.');
                $hostId = $item->host_id; $listingId = $item->id;
                $quantity = $itemType === 'product' ? (int) ($data['quantity'] ?? 1) : (int) ($data['guests'] ?? 1);
                abort_if($item->capacity && $quantity > $item->capacity, 422, 'Requested quantity exceeds capacity.');
                if ($item->capacity) {
                    $reserved = Booking::where('listing_id', $item->id)->whereIn('status', ['pending', 'confirmed', 'completed']);
                    if ($itemType === 'product') {
                        $reservedQuantity = (int) $reserved->sum('guests');
                    } else {
                        $reservedQuantity = (int) $reserved->whereDate('booking_date', $data['booking_date'])->sum('guests');
                    }
                    abort_if($reservedQuantity + $quantity > $item->capacity, 422, 'This listing has no remaining capacity for the requested date or quantity.');
                }
                $amount = (int) round($item->price * (100 - min(100, (int) $item->discount)) / 100) * $quantity;
            }

            $booking = Booking::create(['user_id' => $request->user()->id, 'item_type' => $itemType, 'item_id' => $item->id,
                'host_id' => $hostId, 'listing_id' => $listingId, 'status' => 'pending', 'amount' => $amount,
                'booking_date' => $data['booking_date'] ?? null, 'guests' => (int) ($data['quantity'] ?? $data['guests'] ?? 1), 'notes' => $data['notes'] ?? null]);
            if ($itemType === 'competition') $item->increment('participants_count');
            Notification::create(['user_id' => $booking->user_id, 'type' => 'booking_status', 'title' => 'Booking confirmation',
                'body' => 'Your booking has been received.', 'link' => '/payment/'.$booking->id, 'read' => false]);
            return $booking;
            });
        } catch (QueryException $error) {
            if ($data['item_type'] === 'competition' && str_contains($error->getMessage(), 'bookings_unique_active_competition_user')) {
                return response()->json(['message' => 'You are already registered for this competition.'], 422);
            }
            throw $error;
        }
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
        $isAdmin = $request->user()->is_super_admin || $request->user()->userRoleByUserId()->where('role', 'admin')->exists();
        return DB::transaction(function () use ($booking, $data, $userId, $isAdmin) {
            $locked = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $isHost = $locked->host_id === $userId;
            $isOwner = $locked->user_id === $userId;
            abort_unless($isAdmin || $isHost || ($isOwner && $data['status'] === 'cancelled'), 403);
            abort_if(in_array($locked->status, ['completed', 'cancelled'], true), 422, 'This booking can no longer be changed.');
            $allowedTransitions = ['pending' => ['confirmed', 'cancelled'], 'confirmed' => ['completed', 'cancelled']];
            abort_unless(in_array($data['status'], $allowedTransitions[$locked->status] ?? [], true), 422, 'This booking status transition is not allowed.');
            if ($data['status'] === 'confirmed') {
                abort_unless($locked->amount === 0 || Payment::where('booking_id', $locked->id)->where('status', 'paid')->where('requires_review', false)->exists(), 422, 'A verified payment is required before confirming this booking.');
            }
            if ($data['status'] === 'completed') {
                abort_unless($locked->amount === 0 || Payment::where('booking_id', $locked->id)->where('status', 'paid')->where('requires_review', false)->exists(), 422, 'A verified payment is required before completing this booking.');
                abort_unless(!$locked->booking_date || $locked->booking_date->toDateString() <= today()->toDateString(), 422, 'A booking cannot be completed before its scheduled date.');
            }
            if ($data['status'] === 'cancelled') {
                abort_unless(!Payment::where('booking_id', $locked->id)->where('status', 'paid')->exists(), 422, 'Paid bookings cannot be cancelled until refunds are supported.');
                abort_unless(!Payment::where('booking_id', $locked->id)->where('status', 'pending')->where('expires_at', '>', now())->exists(), 422, 'Wait for the active payment attempt to finish before cancelling.');
            }
            $previousStatus = $locked->status;
            $locked->update($data);
            if ($previousStatus !== 'cancelled' && $locked->status === 'cancelled' && $locked->item_type === 'competition') {
                $competition = Competition::whereKey($locked->item_id)->lockForUpdate()->first();
                if ($competition) $competition->update(['participants_count' => max(0, (int) $competition->participants_count - 1)]);
            }
            if ($locked->status === 'cancelled') Payment::where('booking_id', $locked->id)->where('status', 'pending')->update(['status' => 'cancelled']);
            if ($locked->status !== 'pending' && $locked->user_id !== $userId) Notification::create(['user_id' => $locked->user_id, 'type' => 'booking_status', 'title' => 'Booking status updated', 'body' => 'Your booking is '.$locked->status.'.', 'link' => '/dashboard', 'read' => false]);
            return response()->json(['data' => $this->present($locked->refresh())]);
        });
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
        $revenue = (clone $bookings)->whereIn('status', ['confirmed', 'completed'])->sum('amount');
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
