<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'date' => $this->date,
            'location' => $this->location,
            'budget_estimated' => (float) $this->budget_estimated,
            'budget_real' => (float) $this->budget_real,
            'total_contributions' => (float) $this->total_contributions,
            'currency' => $this->currency,
            'status' => $this->status,
            'expenses' => ExpenseResource::collection($this->whenLoaded('expenses')),
            'contributions' => ContributionResource::collection($this->whenLoaded('contributions')),
            'media' => $this->whenLoaded('media'),
            'organizer' => $this->whenLoaded('organizer'),
            'created_at' => $this->created_at,
        ];

        // On ne génère le QR Code que pour l'organisateur connecté
        if ($request->user()) {
            $url = route('events.public', $this->slug);
            $qrCodeBase64 = base64_encode(QrCode::size(300)->margin(1)->generate($url));
            $data['qr_code'] = 'data:image/svg+xml;base64,' . $qrCodeBase64;
        }

        return $data;
    }
}
