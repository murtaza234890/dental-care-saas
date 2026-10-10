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

        // Whitelisted routes: allow viewing billing & subscription page, public booking, and logging out
        if ($request->routeIs('admin.subscription*') || $request->routeIs('logout') || $request->routeIs('clinic.*')) {
            return $next($request);
        }

        $subscription = $clinic->subscription;

        // If subscription is suspended or trial has expired (past 14 days)
        if ($subscription && ($subscription->isSuspended() || (! $subscription->isValid()) || $subscription->isTrialExpired())) {
            session()->flash('trial_expired', true);

            return redirect()->route('admin.subscription')
                ->withErrors(['subscription' => 'Your 14-day free trial has expired. Please select a plan and enter your billing card below to unlock full dashboard access.']);
        }

        return $next($request);
    }
}
