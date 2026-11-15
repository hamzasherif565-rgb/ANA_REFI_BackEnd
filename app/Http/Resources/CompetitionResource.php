<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'host_id' => $this->host_id, 'title' => $this->title, 'title_en' => $this->title_en,
            'description' => $this->description, 'description_en' => $this->description_en, 'category' => $this->category,
            'location' => $this->location, 'starts_at' => $this->starts_at, 'ends_at' => $this->ends_at,
            'prize_amount' => $this->prize_amount, 'entry_fee' => $this->entry_fee, 'capacity' => $this->capacity,
            'participants_count' => $this->participants_count, 'image_url' => $this->image_url, 'status' => $this->status,
            'featured' => (bool) $this->featured, 'coming_soon' => (bool) $this->coming_soon,
            'created_at' => $this->created_at,
        ];
    }
}
