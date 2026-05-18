<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Update last_seen_at if it's null or has been more than 1 minute since last update
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 1) {
                $user->updateQuietly([
                    'last_seen_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
