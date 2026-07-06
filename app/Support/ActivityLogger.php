<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Route name patterns that should not generate activity logs (autocomplete, chat, etc.).
     */
    protected static array $excludePatterns = [
        '*.search',
        'chat.*',
        'vehicles.search-archiveable',
        'soa.transactions',
        'soa.get-all-cash-additions',
        'soa.floated-funds',
        'expenses.vehicle-categories.index',
        'expenses.payment-methods.index',
        'tools.search',
    ];

    /**
     * Log a permitted request after the response succeeds.
     */
    public static function logPermittedRequest(Request $request): void
    {
        if (! Auth::check()) {
            return;
        }

        $routeName = $request->route()?->getName();
        if (! $routeName || self::shouldExcludeRoute($routeName)) {
            return;
        }

        $permission = self::resolveRoutePermission($routeName, $request);
        if ($permission === null) {
            return;
        }

        $user = Auth::user();
        $pageSlug = $permission['page'];
        $action = $permission['action'] ?? 'view';

        if (! self::userMayPerform($user, $pageSlug, $action)) {
            return;
        }

        if (in_array($action, ['create', 'update', 'delete'], true) && $request->attributes->get('activity_logged')) {
            return;
        }

        if ($action === 'view' && self::recentViewExists($user->id, $routeName)) {
            return;
        }

        $pageLabel = config('pages.list')[$pageSlug] ?? ucwords(str_replace(['.', '-', '_'], ' ', $pageSlug));

        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'model_type' => 'Page',
                'model_id' => null,
                'description' => self::descriptionFor($action, $pageLabel),
                'page' => $pageLabel,
                'section' => self::sectionForRoute($routeName),
                'changes' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable) {
            // Never break the app if logging fails.
        }
    }

    /**
     * Mark the current request as already logged (prevents duplicate mutation logs).
     */
    public static function markLogged(Request $request): void
    {
        $request->attributes->set('activity_logged', true);
    }

    protected static function shouldExcludeRoute(string $routeName): bool
    {
        foreach (self::$excludePatterns as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        return str_contains($routeName, '.search');
    }

    protected static function resolveRoutePermission(string $routeName, Request $request): ?array
    {
        $map = config('pages.route_to_permission', []);

        if (isset($map[$routeName])) {
            return $map[$routeName];
        }

        $pageSlug = self::resolvePageSlugFromRoutePrefix($routeName);
        if ($pageSlug === null) {
            return null;
        }

        return [
            'page' => $pageSlug,
            'action' => self::inferActionFromRouteName($routeName, $request->method()),
        ];
    }

    protected static function resolvePageSlugFromRoutePrefix(string $routeName): ?string
    {
        $prefixes = config('pages.route_prefix_to_page', []);
        $matchedPrefix = null;
        $matchedPage = null;

        foreach ($prefixes as $prefix => $pageSlug) {
            if (str_starts_with($routeName, $prefix) && ($matchedPrefix === null || strlen($prefix) > strlen($matchedPrefix))) {
                $matchedPrefix = $prefix;
                $matchedPage = $pageSlug;
            }
        }

        return $matchedPage;
    }

    protected static function inferActionFromRouteName(string $routeName, string $method = 'GET'): string
    {
        if (preg_match('/\.(store|create|upload|mark-completed|mark-new-completed)(\.|$|-)/', $routeName)) {
            return 'create';
        }

        if (preg_match('/\.(destroy|delete|remove)(\.|$|-)/', $routeName)) {
            return 'delete';
        }

        if (preg_match('/\.(update|edit|put|patch|primary|mark-incomplete|archive)(\.|$|-)/', $routeName)) {
            return 'update';
        }

        if (str_contains($routeName, 'add-cash') || str_contains($routeName, 'manual-entries')) {
            return strtoupper($method) === 'DELETE' ? 'delete' : (in_array(strtoupper($method), ['PUT', 'PATCH'], true) ? 'update' : 'create');
        }

        return match (strtoupper($method)) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'view',
        };
    }

    protected static function userMayPerform(User $user, string $pageSlug, string $action): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->canAccessPage($pageSlug, $action);
    }

    protected static function recentViewExists(int $userId, string $routeName): bool
    {
        return ActivityLog::query()
            ->where('user_id', $userId)
            ->where('action', 'view')
            ->where('section', $routeName)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->exists();
    }

    protected static function descriptionFor(string $action, string $pageLabel): string
    {
        return match ($action) {
            'view' => "Viewed {$pageLabel}",
            'create' => "Created on {$pageLabel}",
            'update' => "Updated on {$pageLabel}",
            'delete' => "Deleted on {$pageLabel}",
            default => ucfirst($action)." on {$pageLabel}",
        };
    }

    protected static function sectionForRoute(string $routeName): string
    {
        return $routeName;
    }
}
