<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (is_array($role)) {
            if (!in_array($request->user()->role, $role)) {
                return response(['message' => 'You are not allowed to acces this service' ], 403);
            }
        } else {
            if ($request->user()->role != $role) {
                return response(['message' => 'You are not allowed to acces this service' ], 403);
            }
        }

        return $next($request);
    }
}
