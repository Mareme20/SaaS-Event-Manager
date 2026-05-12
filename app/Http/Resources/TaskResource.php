<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'member' => new MemberResource($this->whenLoaded('member')),
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'priority' => $this->priority,
            'status' => $this->status,
            'comments_count' => $this->comments()->count(),
            'comments' => $this->comments()->with('user')->latest()->get()->map(fn($c) => [
                'id' => $c->id,
                'content' => $c->content,
                'user_name' => $c->user->name,
                'user_id' => $c->user_id,
                'created_at' => $c->created_at->diffForHumans(),
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
