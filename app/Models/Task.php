<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'member_id',
        'title',
        'description',
        'due_date',
        'priority',
        'status',
    ];

    /**
     * Get the event that owns the task.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the member assigned to the task.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
