<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'host_id' => $this->host_id, 'type' => $this->type, 'title' => $this->title,
            'title_en' => $this->title_en, 'description' => $this->description, 'description_en' => $this->description_en,
            'category' => $this->category, 'price' => $this->price, 'discount' => $this->discount,
            'capacity' => $this->capacity, 'location' => $this->location, 'images' => $this->images ?? [],
            'starts_at' => $this->starts_at, 'ends_at' => $this->ends_at, 'is_featured' => (bool) $this->is_featured,
            'status' => $this->status, 'created_at' => $this->created_at,
            'host' => $this->whenLoaded('userByHostId', fn () => [
                'id' => $this->userByHostId?->id,
                'display_name' => $this->userByHostId?->profileById?->display_name,
                'avatar_url' => $this->userByHostId?->profileById?->avatar_url,
            ]),
            'average_rating' => $this->when(isset($this->average_rating), $this->average_rating),
            'reviews_count' => $this->when(isset($this->reviews_count), $this->reviews_count),
        ];
    }
}
