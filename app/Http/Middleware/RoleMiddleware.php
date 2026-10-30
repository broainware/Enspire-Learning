<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        abort_unless($user && $user->role === $role && $user->is_active, 403, 'Anda tidak memiliki akses ke halaman ini.');

        return $next($request);
    }
}
