<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && is_null($user->email_verified_at) && $user->role !== 'admin') {
            if (!$request->is('verify-email*') && !$request->is('logout') && !$request->is('language*')) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'Your email address is not verified.', 'redirect' => route('verification.notice')], 403)
                    : redirect()->route('verification.notice');
            }
        }

        return $next($request);
    }
}
