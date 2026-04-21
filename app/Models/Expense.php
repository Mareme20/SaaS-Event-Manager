<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="Expense",
 *     description="Expense model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="event_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="title", type="string", example="Catering service"),
 *     @OA\Property(property="amount", type="number", format="float", example=150000.00),
 *     @OA\Property(property="category", type="string", example="Food"),
 *     @OA\Property(property="description", type="string", example="Dinner for 100 people"),
 *     @OA\Property(property="date", type="string", format="date", example="2026-06-21"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-17T20:03:36Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-04-17T20:03:36Z")
 * )
 */
class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'amount',
        'category',
        'description',
        'date',
    ];

    /**
     * Get the event that owns the expense.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
