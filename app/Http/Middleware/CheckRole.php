<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login.index');
        }

        foreach ($roles as $role) {
            if (strcasecmp((string) $role, 'Customer') === 0 && $user->isCustomer()) {
                return $next($request);
            }
            if ($user->role && strcasecmp((string) $user->role, (string) $role) === 0) {
                return $next($request);
            }
            if ($user->role_id && $user->userRole && strcasecmp((string) $user->userRole->name, (string) $role) === 0) {
                return $next($request);
            }
        }

        return redirect()->route('login.index')->with('failed', 'You are not authorized to access that page.');
    }
}
