<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {

        if (!auth()->check()) {

            return redirect('/login');
        }

        if (!auth()->user()->role) {

            abort(403, 'Role not found');
        }

        $userRole =
            auth()->user()->role->role_name;

        if (!in_array($userRole, $roles)) {

            abort(403);
        }

        return $next($request);
    }
}