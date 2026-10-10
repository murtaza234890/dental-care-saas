<?php

namespace App\Http\Middleware;

use App\Models\Clinic;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request and bind tenant context.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Deactivated user check
            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Your account has been deactivated. Please contact your clinic administrator.',
                ]);
            }

            // Platform owner doesn't belong to a single tenant unless impersonating
            if ($user->clinic_id) {
                app()->instance('current_clinic_id', $user->clinic_id);
                session(['current_clinic_id' => $user->clinic_id]);

                if ($user->relationLoaded('clinic') || $user->clinic) {
                    app()->instance('current_clinic', $user->clinic);
                }
            }
        }

        return $next($request);
    }
}
