<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'title' => $this->title,
            'amount' => (float) $this->amount,
            'category' => $this->category,
            'description' => $this->description,
            'date' => $this->date,
            'created_at' => $this->created_at,
        ];
    }
}
