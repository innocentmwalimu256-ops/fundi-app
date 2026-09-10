<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTechnicianSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isTechnician()) {
            // Check if subscription is active
            if (!SubscriptionService::isActive($user)) {
                // Routes that expired technicians can always access
                $allowedRoutes = [
                    'technician.dashboard',
                    'technician.subscription',
                    'technician.subscription.*',
                    'technician.profile',
                    'technician.profile.*',
                    'technician.availability',
                    'technician.availability.*',
                    'technician.portfolios.*',
                    'technician.reviews.*',
                    'notifications.*',
                    'logout',
                ];

                if (!$request->routeIs($allowedRoutes)) {
                    return redirect()->route('technician.subscription.expired')
                        ->with('warning', 'Your subscription has expired. Renew your plan to receive new service opportunities and unlock new client contacts.');
                }
            }
        }

        return $next($request);
    }
}
