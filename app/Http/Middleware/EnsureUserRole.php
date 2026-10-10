<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request to verify user role.
     *
     * @param  string[]  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        // Platform owner has broad administrative override access
        if ($userRole === 'platform_owner') {
            return $next($request);
        }

        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Aapko is section tak rasai (access) hasil nahi hai. Role required: ' . implode(' ya ', $roles));
        }

        return $next($request);
    }
}
