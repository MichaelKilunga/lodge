<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!$request->user() || !$request->user()->hasPermission($permission)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized. Missing permission: ' . $permission], 403);
            }
            return redirect()->back()->with('failed', 'You are not authorized to perform this action. Missing permission: ' . $permission);
        }

        return $next($request);
    }
}
