<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeatureRequest;
use App\Models\Listing;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserContact;
use App\Models\Competition;
use App\Models\VolunteerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function listings(Request $request)
    {
        return response()->json(['data' => Listing::latest()->paginate(min((int) $request->integer('per_page', 100), 100))]);
    }

    public function publicProfiles(Request $request)
    {
        $ids = array_filter(explode(',', (string) $request->query('ids', '')));
        $query = Profile::query()->select(['id', 'display_name', 'avatar_url', 'bio', 'account_type', 'created_at']);
        if ($ids) $query->whereIn('id', $ids); elseif ($request->user()) $query->whereKey($request->user()->id); else return response()->json(['data' => []]);
        return response()->json(['data' => $query->get()]);
    }

    public function hostListings(Request $request)
    {
        return response()->json(['data' => Listing::where('host_id', $request->user()->id)->latest()->paginate(100)]);
    }

    public function hostCompetitions(Request $request)
    {
        return response()->json(['data' => Competition::where('host_id', $request->user()->id)->latest()->paginate(100)]);
    }

    public function saveHostVerification(Request $request)
    {
        $data = $request->validate(['first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'], 'national_id' => ['required', 'digits:14'], 'id_front_path' => ['required', 'string', 'max:1024'], 'id_back_path' => ['required', 'string', 'max:1024']]);
        return response()->json(['data' => \App\Models\HostVerification::updateOrCreate(['user_id' => $request->user()->id], [...$data, 'status' => 'pending'])], 201);
    }

    public function users(Request $request)
    {
        $query = Profile::query()->with('userById');
        if ($request->filled('search')) $query->where('display_name', 'like', '%'.$request->string('search')->toString().'%');
        $profiles = $query->latest()->paginate(min((int) $request->integer('per_page', 30), 100));
        $phones = UserContact::whereIn('user_id', collect($profiles->items())->pluck('id'))->pluck('phone', 'user_id');
        $rows = collect($profiles->items())->map(fn ($p) => [...$p->toArray(), 'email' => $p->userById?->email, 'phone' => $phones[$p->id] ?? null,
            'roles' => $p->userById?->userRoleByUserId()->pluck('role')->all() ?? []]);
        return response()->json(['data' => $rows, 'meta' => ['current_page' => $profiles->currentPage(), 'last_page' => $profiles->lastPage(), 'total' => $profiles->total()]]);
    }

    public function block(Request $request, string $user)
    {
        $data = $request->validate(['is_blocked' => ['required', 'boolean']]);
        $profile = Profile::findOrFail($user);
        abort_if($profile->id === $request->user()->id, 422, 'You cannot block your own account.');
        $profile->update(['is_blocked' => $data['is_blocked']]);
        User::whereKey($user)->update(['banned_until' => $data['is_blocked'] ? now()->addYears(20) : null]);
        return response()->json(['data' => $profile->refresh()]);
    }

    public function featureRequests(Request $request)
    {
        $query = FeatureRequest::with('listingByListingId')->latest();
        if ($request->user()->userRoleByUserId()->where('role', 'host')->exists() && !$request->user()->userRoleByUserId()->where('role', 'admin')->exists()) $query->where('host_id', $request->user()->id);
        return response()->json(['data' => $query->paginate(min((int) $request->integer('per_page', 50), 100))]);
    }

    public function createFeatureRequest(Request $request)
    {
        $data = $request->validate(['listing_id' => ['required', 'uuid', 'exists:public_listings,id'], 'amount' => ['required', 'integer', 'min:0'], 'payment_method' => ['required', 'string', 'max:40']]);
        $listing = Listing::findOrFail($data['listing_id']);
        abort_unless($listing->host_id === $request->user()->id && $listing->type === 'product', 403);
        return response()->json(['data' => FeatureRequest::create([...$data, 'host_id' => $request->user()->id, 'payment_status' => 'pending', 'status' => 'pending'])], 201);
    }

    public function updateFeatureRequest(Request $request, FeatureRequest $featureRequest)
    {
        $data = $request->validate(['status' => ['required', 'in:approved,rejected,pending'], 'admin_note' => ['sometimes', 'nullable', 'string', 'max:2000']]);
        $featureRequest->update($data);
        if ($data['status'] === 'approved') Listing::whereKey($featureRequest->listing_id)->update(['is_featured' => true]);
        return response()->json(['data' => $featureRequest->refresh()]);
    }

    public function toggleFeatured(Request $request, Listing $listing)
    {
        $data = $request->validate(['is_featured' => ['required', 'boolean']]);
        abort_unless($listing->type === 'product', 422, 'Only products may be featured.');
        abort_unless($listing->status === 'active', 422, 'Only active products may be featured.');
        if ($data['is_featured']) {
            DB::transaction(function () use ($listing) {
                $count = Listing::where('type', 'product')->where('status', 'active')->where('is_featured', true)->lockForUpdate()->count();
                abort_if(!$listing->is_featured && $count >= 3, 422, 'A maximum of three active products can be featured.');
                $listing->update(['is_featured' => true]);
            });
        } else $listing->update(['is_featured' => false]);
        return response()->json(['data' => $listing->refresh()]);
    }

    public function volunteers(Request $request)
    {
        $query = VolunteerRequest::query()->latest();
        if ($request->user()->userRoleByUserId()->where('role', 'admin')->doesntExist()) $query->where('status', 'approved');
        return response()->json(['data' => $query->paginate(min((int) $request->integer('per_page', 30), 100))]);
    }

    public function applyVolunteer(Request $request)
    {
        $data = $request->validate(['full_name' => ['required', 'string', 'min:2', 'max:100'], 'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:20'], 'interest_area' => ['required', 'string', 'min:2', 'max:100'],
            'availability' => ['required', 'string', 'min:2', 'max:200'], 'motivation' => ['required', 'string', 'min:10', 'max:1500'],
            'age' => ['sometimes', 'nullable', 'integer', 'min:13', 'max:100'], 'avatar_url' => ['sometimes', 'nullable', 'url'],
            'bio' => ['sometimes', 'nullable', 'string'], 'location' => ['sometimes', 'nullable', 'string'], 'skills' => ['sometimes', 'array'],
            'languages' => ['sometimes', 'array'], 'experience' => ['sometimes', 'nullable', 'string'], 'accessibility' => ['sometimes', 'nullable', 'string']]);
        return response()->json(['data' => VolunteerRequest::updateOrCreate(['user_id' => $request->user()->id], [...$data, 'status' => 'pending'])], 201);
    }

    public function updateVolunteer(Request $request, VolunteerRequest $volunteer)
    {
        $volunteer->update($request->validate(['status' => ['required', 'in:pending,approved,rejected']]));
        return response()->json(['data' => $volunteer->refresh()]);
    }

    public function deleteVolunteer(VolunteerRequest $volunteer)
    {
        $volunteer->delete(); return response()->noContent();
    }

    public function serviceRequests(Request $request)
    {
        return response()->json(['data' => \App\Models\VolunteerServiceRequest::where('volunteer_user_id', $request->user()->id)->latest()->paginate(30)]);
    }

    public function createServiceRequest(Request $request)
    {
        $data = $request->validate(['volunteer_id' => ['required', 'uuid', 'exists:public_volunteer_requests,id'], 'requested_date' => ['required', 'date', 'after_or_equal:today'],
            'requested_time' => ['sometimes', 'nullable', 'string', 'max:100'], 'location' => ['required', 'string', 'max:1000'], 'reason' => ['required', 'string', 'max:5000'],
            'people_count' => ['sometimes', 'integer', 'min:1', 'max:100'], 'notes' => ['sometimes', 'nullable', 'string', 'max:5000']]);
        $volunteer = VolunteerRequest::where('status', 'approved')->findOrFail($data['volunteer_id']);
        $row = \App\Models\VolunteerServiceRequest::create([...$data, 'requester_id' => $request->user()->id, 'volunteer_id' => $volunteer->id, 'volunteer_user_id' => $volunteer->user_id, 'status' => 'pending']);
        return response()->json(['data' => $row], 201);
    }

    public function updateServiceRequest(Request $request, \App\Models\VolunteerServiceRequest $serviceRequest)
    {
        abort_unless($serviceRequest->volunteer_user_id === $request->user()->id || $request->user()->userRoleByUserId()->where('role', 'admin')->exists(), 403);
        $serviceRequest->update($request->validate(['status' => ['required', 'in:pending,approved,rejected,completed,cancelled']]));
        return response()->json(['data' => $serviceRequest->refresh()]);
    }

    public function stats()
    {
        return response()->json(['data' => ['travelers' => Profile::where('account_type', 'traveler')->count(), 'hosts' => Profile::where('account_type', 'host')->count(),
            'bookings' => \App\Models\Booking::count(), 'volunteers' => VolunteerRequest::where('status', 'approved')->count(),
            'listings' => Listing::where('status', 'active')->count(), 'competitions' => \App\Models\Competition::count()]]);
    }
}
