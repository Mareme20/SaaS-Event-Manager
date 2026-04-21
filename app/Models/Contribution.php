<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="Contribution",
 *     description="Contribution model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="event_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="member_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="amount", type="number", format="float", example=5000.00),
 *     @OA\Property(property="description", type="string", example="Monthly contribution"),
 *     @OA\Property(property="date", type="string", format="date", example="2026-05-10"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-21T13:58:24Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-04-21T13:58:24Z")
 * )
 */
class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'member_id',
        'amount',
        'description',
        'date',
    ];

    /**
     * Get the event associated with the contribution.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the member who made the contribution.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
