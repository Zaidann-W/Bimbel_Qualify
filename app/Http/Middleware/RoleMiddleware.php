<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->canAccessRoleArea($role)) {
            // Jika user mencoba akses area role lain, redirect ke dashboard miliknya
            return redirect()->route($user->role.'.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke area tersebut.');
        }

        return $next($request);
    }
}
