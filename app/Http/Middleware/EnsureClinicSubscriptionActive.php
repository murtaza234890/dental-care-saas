<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicSubscriptionActive
{
    /**
     * Handle an incoming request and ensure tenant clinic has valid subscription.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Platform owner or unauthenticated requests are exempt
        if (! $user || $user->isPlatformOwner()) {
            return $next($request);
        }

        $clinic = $user->clinic;
        if (! $clinic) {
            return $next($request);
        }

        // Whitelisted routes: allow viewing billing & subscription page and logging out
        if ($request->routeIs('admin.subscription*') || $request->routeIs('logout')) {
            return $next($request);
        }

        $subscription = $clinic->subscription;

        // If subscription is suspended or past grace period
        if ($subscription && ($subscription->isSuspended() || (! $subscription->isValid()))) {
            // Block data mutation actions
            if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')) {
                return redirect()->route('admin.subscription')
                    ->withErrors(['subscription' => 'Your subscription has expired. Patient records remain safe and read-only. Please renew your plan to restore booking and billing actions.']);
            }
        }

        return $next($request);
    }
}
