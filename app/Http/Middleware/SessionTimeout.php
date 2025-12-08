<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $lastActivity = session('last_activity');

            // Check if session has expired (30 minutes of inactivity)
            $maxInactiveTime = config('session.lifetime', 120) * 60; // Convert minutes to seconds
            if ($lastActivity && (time() - $lastActivity > $maxInactiveTime)) {
                auth()->logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect('/login')->with('error', 'Your session has expired due to inactivity. Please log in again.');
            }

            // Update the last activity time
            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
