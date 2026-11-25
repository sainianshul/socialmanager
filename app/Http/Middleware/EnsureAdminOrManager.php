<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrManager
{
    /**
     * Handle an incoming request, optionally enforcing a granular Manager permission.
     */
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && !$user->isManager())) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->isAdmin() || $permission === null) {
            return $next($request);
        }

        $allowed = match ($permission) {
            'users'   => $user->canManageUsers(),
            'content' => $user->canManageContent(),
            'tickets' => $user->canManageTickets(),
            'system'  => $user->canAccessSystem(),
            default   => false,
        };

        if (!$allowed) {
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
