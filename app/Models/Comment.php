<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'commentable_id',
        'commentable_type',
    ];

    /**
     * Get the parent commentable model (task, etc.).
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who authored the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
