<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forcer le HTTPS uniquement en production et si on n'est pas sur localhost
        if (config('app.env') === 'production' && !app()->runningInConsole()) {
            if (!in_array(request()->getHost(), ['127.0.0.1', 'localhost'])) {
                URL::forceScheme('https');
            }
        }

        // Support pour les proxys comme Railway
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }
    }
}
