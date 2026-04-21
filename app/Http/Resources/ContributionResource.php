<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContributionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'event_title' => $this->event->title ?? null,
            'member' => new MemberResource($this->whenLoaded('member')),
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'date' => $this->date,
            'created_at' => $this->created_at,
        ];
    }
}
