<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Billable;

    /**
     * Get the plan limits based on subscription.
     */
    public function getPlanLimitsAttribute()
    {
        if ($this->subscribed('business')) {
            return ['events' => -1, 'members' => -1, 'premium' => true];
        }

        if ($this->subscribed('pro')) {
            return ['events' => 10, 'members' => -1, 'premium' => true];
        }

        return ['events' => 1, 'members' => 5, 'premium' => false];
    }

    /**
     * Get the events for the user.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the members created by the user.
     */
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
