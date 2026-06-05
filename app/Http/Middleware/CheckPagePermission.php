<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPagePermission
{
    /**
     * Handle an incoming request. Admins bypass. Others must have permission for current route.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (! $user) {
            return $next($request);
        }
        if ($user->isAdmin()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if (! $routeName) {
            return $next($request);
        }

        $map = config('pages.route_to_permission', []);
        $info = $map[$routeName] ?? null;
        if (! $info) {
            return $next($request);
        }

        $page = $info['page'] ?? null;
        $action = $info['action'] ?? 'view';
        if (! $page) {
            return $next($request);
        }

        if (! $user->canAccessPage($page, $action)) {
            abort(403, 'You do not have permission to perform this action on this page.');
        }

        return $next($request);
    }
}
