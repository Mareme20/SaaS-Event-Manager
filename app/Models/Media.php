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
        // Si c'est déjà une URL complète (Cloudinary ou externe)
        if (is_string($this->file_path) && preg_match('/^https?:\/\//i', $this->file_path)) {
            // Si c'est Cloudinary, on peut ajouter des optimisations automatiques
            if (str_contains($this->file_path, 'cloudinary.com')) {
                return str_replace('/upload/', '/upload/f_auto,q_auto/', $this->file_path);
            }
            return $this->file_path;
        }

        // Sinon on utilise le disque public
        return Storage::disk('public')->url($this->file_path);
    }


    /**
     * Define the append for serialization.
     */
    protected $appends = ['file_url'];
}
