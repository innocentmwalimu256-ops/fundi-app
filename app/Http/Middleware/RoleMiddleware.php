<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $user = auth()->user();

        if ($user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended or deactivated. Please contact support.');
        }

        if (!in_array($user->role, $roles)) {
            // Redirect to appropriate dashboard based on their real role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard')->with('error', 'Unauthorized access restricted.'),
                'technician' => redirect()->route('technician.dashboard')->with('error', 'Unauthorized access restricted.'),
                default => redirect()->route('client.dashboard')->with('error', 'Unauthorized access restricted.'),
            };
        }

        return $next($request);
    }
}
