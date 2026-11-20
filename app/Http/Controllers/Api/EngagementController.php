<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\Competition;
use App\Models\ContactMessage;
use App\Models\Favorite;
use App\Models\Listing;
use App\Models\Notification;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EngagementController extends Controller
{
    public function favorites(Request $request)
    {
        $rows = Favorite::where('user_id', $request->user()->id)->latest('created_at')->get();
        return response()->json(['data' => $rows->map(fn ($row) => [...$row->toArray(), 'item' => $this->favoriteItem($row->item_type, $row->item_id)])]);
    }

    public function addFavorite(Request $request)
    {
        $data = $request->validate(['item_id' => ['required', 'uuid'], 'item_type' => ['required', Rule::in(['stay', 'experience', 'product', 'competition'])]]);
        $item = $data['item_type'] === 'competition' ? Competition::whereKey($data['item_id'])->where('status', '!=', 'draft')->firstOrFail() : Listing::whereKey($data['item_id'])->where('type', $data['item_type'])->where('status', 'active')->firstOrFail();
        $favorite = Favorite::firstOrCreate(['user_id' => $request->user()->id, 'item_type' => $data['item_type'], 'item_id' => $item->id]);
        return response()->json(['data' => [...$favorite->toArray(), 'item' => $this->favoriteItem($favorite->item_type, $favorite->item_id)]], 201);
    }

    public function removeFavorite(Request $request, string $itemType, string $itemId)
    {
        Favorite::where('user_id', $request->user()->id)->where('item_type', $itemType)->where('item_id', $itemId)->delete();
        return response()->noContent();
    }

    public function removeFavoriteById(Request $request, Favorite $favorite)
    {
        abort_unless($favorite->user_id === $request->user()->id, 403);
        $favorite->delete();
        return response()->noContent();
    }

    public function reviews(Request $request, Listing $listing)
    {
        return response()->json(['data' => Review::where('listing_id', $listing->id)->with('userByUserId.profileById')->latest('created_at')->paginate(min((int) $request->integer('per_page', 20), 100))]);
    }

    public function addReview(Request $request, Listing $listing)
    {
        abort_unless($listing->status === 'active', 404);
        abort_unless(\App\Models\Booking::where('user_id', $request->user()->id)->where('listing_id', $listing->id)->where('status', 'completed')->whereHas('payments', fn ($query) => $query->where('status', 'paid')->where('requires_review', false))->exists(), 403, 'A completed, verified booking is required to review this listing.');
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'comment' => ['nullable', 'string', 'max:500'], 'image_url' => ['nullable', 'url', 'max:2048']]);
        abort_if(Review::where('listing_id', $listing->id)->where('user_id', $request->user()->id)->exists(), 409, 'You have already reviewed this listing.');
        $review = Review::create([...$data, 'listing_id' => $listing->id, 'user_id' => $request->user()->id]);
        return response()->json(['data' => $review->load('userByUserId.profileById')], 201);
    }

    public function updateReview(Request $request, Review $review)
    {
        $this->authorizeReview($request, $review);
        $review->update($request->validate(['rating' => ['sometimes', 'integer', 'between:1,5'], 'comment' => ['sometimes', 'nullable', 'string', 'max:500'], 'image_url' => ['sometimes', 'nullable', 'url', 'max:2048']]));
        return response()->json(['data' => $review->refresh()]);
    }

    public function deleteReview(Request $request, Review $review)
    {
        $this->authorizeReview($request, $review); $review->delete(); return response()->noContent();
    }

    public function notifications(Request $request)
    {
        $query = Notification::where('user_id', $request->user()->id)->latest('created_at');
        $unread = (clone $query)->where('read', false)->count();
        $rows = $query->paginate(min((int) $request->integer('per_page', 30), 100));
        return response()->json(['data' => $rows->items(), 'meta' => ['current_page' => $rows->currentPage(), 'last_page' => $rows->lastPage(), 'per_page' => $rows->perPage(), 'total' => $rows->total(), 'unread_count' => $unread], 'links' => ['first' => $rows->url(1), 'last' => $rows->url($rows->lastPage()), 'prev' => $rows->previousPageUrl(), 'next' => $rows->nextPageUrl()]]);
    }

    public function readAll(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)->where('read', false)->update(['read' => true]);
        return response()->json(['data' => ['updated' => $count]]);
    }

    public function deleteNotification(Request $request, Notification $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 403); $notification->delete(); return response()->noContent();
    }

    public function posts(Request $request)
    {
        $perPage = min((int) $request->integer('per_page', 60), 100);
        return response()->json(['data' => CommunityPost::with('userByUserId.profileById')->latest('created_at')->paginate($perPage)]);
    }

    public function addPost(Request $request)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'body' => ['required', 'string', 'max:10000'], 'image_url' => ['sometimes', 'nullable', 'url'], 'listing_id' => ['sometimes', 'nullable', 'uuid', 'exists:public_listings,id']]);
        if (!empty($data['listing_id'])) {
            $listing = Listing::findOrFail($data['listing_id']);
            abort_unless($listing->status === 'active' || $listing->host_id === $request->user()->id, 403);
        }
        $post = CommunityPost::create([...$data, 'user_id' => $request->user()->id]);
        return response()->json(['data' => $post->load('userByUserId.profileById')], 201);
    }

    public function deletePost(Request $request, CommunityPost $post)
    {
        abort_unless($post->user_id === $request->user()->id || $this->isAdmin($request), 403); $post->delete(); return response()->noContent();
    }

    public function contact(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:150'], 'email' => ['required', 'email', 'max:255'], 'message' => ['required', 'string', 'min:5', 'max:5000']]);
        return response()->json(['data' => ContactMessage::create($data)], 201);
    }

    private function favoriteItem(string $type, string $id): ?array
    {
        $item = $type === 'competition' ? Competition::find($id) : Listing::find($id);
        return $item ? ['id' => $item->id, 'type' => $type, 'title' => $item->title, 'title_en' => $item->title_en ?? null, 'images' => $item->images ?? [], 'image_url' => $item->image_url ?? null, 'location' => $item->location, 'price' => $item->price ?? $item->entry_fee ?? 0, 'status' => $item->status] : null;
    }

    private function authorizeReview(Request $request, Review $review): void
    {
        abort_unless($review->user_id === $request->user()->id || $this->isAdmin($request), 403);
    }

    private function isAdmin(Request $request): bool
    {
        return $request->user()->is_super_admin || $request->user()->userRoleByUserId()->where('role', 'admin')->exists();
    }
}
