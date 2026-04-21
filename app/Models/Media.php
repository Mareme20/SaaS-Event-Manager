<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'file_path',
        'file_type',
        'title',
        'is_public',
    ];

    /**
     * Get the event that owns the media.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who uploaded the media.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full URL for the media file.
     */
    public function getFileUrlAttribute()
    {
        return asset(Storage::url($this->file_path));
    }

    /**
     * Define the append for serialization.
     */
    protected $appends = ['file_url'];
}
