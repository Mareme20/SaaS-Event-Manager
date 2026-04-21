<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="Event",
 *     description="Event model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="title", type="string", example="Wedding of Alice & Bob"),
 *     @OA\Property(property="slug", type="string", example="wedding-alice-bob-a1b2c3"),
 *     @OA\Property(property="description", type="string", example="Grand wedding celebration"),
 *     @OA\Property(property="date", type="string", format="date", example="2026-06-20"),
 *     @OA\Property(property="location", type="string", example="Cotonou, Benin"),
 *     @OA\Property(property="budget_estimated", type="number", format="float", example=5000000.00),
 *     @OA\Property(property="budget_real", type="number", format="float", example=4500000.00),
 *     @OA\Property(property="total_contributions", type="number", format="float", example=3000000.00),
 *     @OA\Property(property="currency", type="string", example="FCFA"),
 *     @OA\Property(property="status", type="string", example="planned"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-17T20:03:36Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-04-17T20:03:36Z")
 * )
 */
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'date',
        'location',
        'budget_estimated',
        'currency',
        'status',
    ];

    /**
     * Get the contributions for the event.
     */
    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * Get the tasks for the event.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the expenses for the event.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the media for the event.
     */
    public function media()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Get the organizer (User) of the event.
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Calculate the real budget (sum of expenses).
     */
    public function getBudgetRealAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    /**
     * Calculate the total contributions collected.
     */
    public function getTotalContributionsAttribute()
    {
        return $this->contributions()->sum('amount');
    }

    /**
     * Define the append for serialization.
     */
    protected $appends = ['budget_real', 'total_contributions'];
}
