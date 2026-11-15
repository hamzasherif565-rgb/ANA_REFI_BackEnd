<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompetitionResource;
use App\Http\Resources\ListingResource;
use App\Models\Competition;
use App\Models\Listing;
use App\Models\Notification;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CatalogController extends Controller
{
    public function listings(Request $request)
    {
        $filters = $request->validate([
            'type' => ['sometimes', Rule::in(['stay', 'experience', 'product'])],
            'status' => ['sometimes', Rule::in(['active', 'blocked', 'draft'])], 'category' => ['sometimes', 'string', 'max:191'],
            'search' => ['sometimes', 'string', 'max:150'], 'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'numeric', 'min:0'], 'sort' => ['sometimes', Rule::in(['newest', 'price_asc', 'price_desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
        $query = Listing::query()->with('userByHostId.profileById')->withAvg('reviewByListingId as average_rating', 'rating')->withCount('reviewByListingId as reviews_count');
        if ($request->user()) $query->where(function ($q) use ($request) { $q->where('status', 'active')->orWhere('host_id', $request->user()->id); });
        else $query->where('status', 'active');
        foreach (['type', 'category'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        if (isset($filters['min_price'])) $query->where('price', '>=', $filters['min_price']);
        if (isset($filters['max_price'])) $query->where('price', '<=', $filters['max_price']);
        if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('title', 'like', '%'.$filters['search'].'%')->orWhere('title_en', 'like', '%'.$filters['search'].'%')->orWhere('location', 'like', '%'.$filters['search'].'%'));
        match ($filters['sort'] ?? 'newest') { 'price_asc' => $query->orderBy('price'), 'price_desc' => $query->orderByDesc('price'), default => $query->latest() };
        return ListingResource::collection($query->paginate($filters['per_page'] ?? 20));
    }

    public function featured()
    {
        return ListingResource::collection(Listing::with('userByHostId.profileById')->where('type', 'product')->where('status', 'active')->where('is_featured', true)->latest()->limit(3)->get());
    }

    public function listing(Listing $listing)
    {
        abort_unless($listing->status === 'active' || auth()->id() === $listing->host_id || $this->isAdmin(), 404);
        $listing->load('userByHostId.profileById');
        $listing->loadAvg('reviewByListingId as average_rating', 'rating')->loadCount('reviewByListingId as reviews_count');
        return new ListingResource($listing);
    }

    public function hostListings(string $host)
    {
        return ListingResource::collection(Listing::where('host_id', $host)->where('status', 'active')->latest()->paginate(20));
    }

    public function storeListing(Request $request)
    {
        $data = $request->validate($this->listingRules());
        $data['host_id'] = $request->user()->id;
        return (new ListingResource(Listing::create($data)))->response()->setStatusCode(201);
    }

    public function updateListing(Request $request, Listing $listing)
    {
        $this->authorizeOwner($request, $listing->host_id);
        $listing->update($request->validate($this->listingRules(true)));
        return new ListingResource($listing->refresh());
    }

    public function deleteListing(Request $request, Listing $listing)
    {
        $this->authorizeOwner($request, $listing->host_id);
        $listing->delete();
        return response()->noContent();
    }

    public function competitions(Request $request)
    {
        $filters = $request->validate(['search' => ['sometimes', 'string', 'max:150'], 'category' => ['sometimes', 'string', 'max:191'], 'status' => ['sometimes', Rule::in(['upcoming', 'open', 'closed', 'completed', 'cancelled'])], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $query = Competition::query();
        if ($request->user()) $query->where(fn ($q) => $q->where('status', '!=', 'draft')->orWhere('host_id', $request->user()->id));
        else $query->where('status', '!=', 'draft');
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['category'])) $query->where('category', $filters['category']);
        if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('title', 'like', '%'.$filters['search'].'%')->orWhere('title_en', 'like', '%'.$filters['search'].'%')->orWhere('location', 'like', '%'.$filters['search'].'%'));
        return CompetitionResource::collection($query->orderByDesc('featured')->orderBy('starts_at')->paginate($filters['per_page'] ?? 30));
    }

    public function competition(Competition $competition)
    {
        abort_unless($competition->status !== 'draft' || auth()->id() === $competition->host_id || $this->isAdmin(), 404);
        return new CompetitionResource($competition);
    }

    public function storeCompetition(Request $request)
    {
        $data = $request->validate($this->competitionRules());
        $data['host_id'] = $request->user()->id;
        return (new CompetitionResource(Competition::create($data)))->response()->setStatusCode(201);
    }

    public function updateCompetition(Request $request, Competition $competition)
    {
        $this->authorizeOwner($request, $competition->host_id);
        $previousStatus = $competition->status;
        $competition->update($request->validate($this->competitionRules(true)));
        if ($previousStatus !== $competition->status) {
            Booking::where('item_type', 'competition')->where('item_id', $competition->id)->whereNotIn('status', ['cancelled'])->pluck('user_id')->unique()->each(function (string $userId) use ($competition): void {
                Notification::create(['user_id' => $userId, 'type' => 'competition_update', 'title' => 'Competition update',
                    'body' => $competition->title.' is now '.$competition->status.'.', 'link' => '/competitions', 'read' => false]);
            });
        }
        return new CompetitionResource($competition->refresh());
    }

    public function deleteCompetition(Request $request, Competition $competition)
    {
        $this->authorizeOwner($request, $competition->host_id);
        $competition->delete();
        return response()->noContent();
    }

    public function uploadListingImage(Request $request)
    {
        $data = $request->validate(['file' => ['required', 'image', 'max:10240']]);
        $path = $data['file']->store('listings/'.$request->user()->id, 'public');
        return response()->json(['data' => ['path' => $path, 'url' => Storage::disk('public')->url($path)]], 201);
    }

    public function uploadIdentity(Request $request)
    {
        $data = $request->validate(['file' => ['required', 'image', 'max:10240'], 'side' => ['required', Rule::in(['front', 'back'])]]);
        $path = $data['file']->store('identity-docs/'.$request->user()->id, 'local');
        return response()->json(['data' => ['path' => $path]], 201);
    }

    private function listingRules(bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';
        return ['type' => [$required, Rule::in(['stay', 'experience', 'product'])], 'title' => [$required, 'string', 'max:10000'],
            'title_en' => ['sometimes', 'nullable', 'string', 'max:10000'], 'description' => ['sometimes', 'nullable', 'string'],
            'description_en' => ['sometimes', 'nullable', 'string'], 'category' => ['sometimes', 'nullable', 'string', 'max:191'],
            'price' => [$required, 'integer', 'min:0'], 'discount' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'capacity' => ['sometimes', 'nullable', 'integer', 'min:1'], 'location' => ['sometimes', 'nullable', 'string'],
            'images' => ['sometimes', 'array', 'max:6'], 'images.*' => ['string', 'max:2048'],
            'starts_at' => ['sometimes', 'nullable', 'date'], 'ends_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['sometimes', Rule::in(['active', 'draft', 'blocked'])], 'is_featured' => ['prohibited']];
    }

    private function competitionRules(bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';
        return ['title' => [$required, 'string'], 'title_en' => ['sometimes', 'nullable', 'string'], 'description' => ['sometimes', 'nullable', 'string'],
            'description_en' => ['sometimes', 'nullable', 'string'], 'category' => [$required, 'string', 'max:191'], 'location' => [$required, 'string'],
            'starts_at' => [$required, 'date'], 'ends_at' => [$required, 'date', 'after:starts_at'], 'prize_amount' => ['sometimes', 'integer', 'min:0'],
            'entry_fee' => ['sometimes', 'integer', 'min:0'], 'capacity' => ['sometimes', 'integer', 'min:1'], 'image_url' => ['sometimes', 'nullable', 'url'],
            'status' => ['sometimes', Rule::in(['draft', 'upcoming', 'open', 'closed', 'completed', 'cancelled'])],
            'featured' => ['prohibited'], 'coming_soon' => ['sometimes', 'boolean']];
    }

    private function authorizeOwner(Request $request, ?string $ownerId): void
    {
        abort_unless($request->user()->id === $ownerId || $this->isAdmin(), 403);
    }

    private function isAdmin(): bool
    {
        return auth()->user()?->is_super_admin || auth()->user()?->userRoleByUserId()->where('role', 'admin')->exists();
    }
}
