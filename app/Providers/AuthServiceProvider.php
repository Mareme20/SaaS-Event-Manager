<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('create-event', function (User $user) {
            $limit = $user->plan_limits['events'];
            
            if ($limit === -1) return true;
            
            return $user->events()->count() < $limit;
        });

        Gate::define('add-member', function (User $user) {
            $limit = $user->plan_limits['members'];
            
            if ($limit === -1) return true;
            
            return $user->members()->count() < $limit;
        });

        Gate::define('access-premium', function (User $user) {
            return $user->plan_limits['premium'];
        });
    }
}
