<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    /**
     * Handle an incoming request and ensure the user has one of the allowed roles.
     * Usage in routes: ->middleware('role:admin,lab_manager')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized.');
    }
}
