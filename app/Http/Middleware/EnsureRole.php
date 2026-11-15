<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        $allowed = $user && ($user->is_super_admin || $user->userRoleByUserId()->where('role', $role)->exists()
            || ($role !== 'admin' && $user->userRoleByUserId()->where('role', 'admin')->exists()));

        abort_unless($allowed, 403, 'You are not authorized to perform this action.');

        return $next($request);
    }
}
