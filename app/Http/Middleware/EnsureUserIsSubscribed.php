<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Gate::denies('access-premium')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Cette fonctionnalité requiert un abonnement Pro ou Business.'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Cette fonctionnalité requiert un abonnement Pro ou Business.');
        }

        return $next($request);
    }
}
