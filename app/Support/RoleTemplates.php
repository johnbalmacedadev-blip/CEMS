<?php

namespace App\Support;

use App\Models\User;
use App\Models\UserPagePermission;

class RoleTemplates
{
    /**
     * @return array<string, array>
     */
    public static function all(): array
    {
        return config('role_templates.templates', []);
    }

    public static function get(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    /**
     * Templates that apply a page-permission matrix (not Super Admin).
     *
     * @return array<string, array>
     */
    public static function permissionPresets(): array
    {
        return array_filter(self::all(), fn (array $t) => ($t['sets_role'] ?? 'user') !== 'admin');
    }

    /**
     * Build permission map for every page slug.
     *
     * @return array<string, array{can_view: bool, can_create: bool, can_update: bool, can_delete: bool}>
     */
    public static function buildPermissions(string $templateKey): array
    {
        $template = self::get($templateKey);
        if (! $template) {
            return [];
        }

        $pages = array_keys(config('pages.list', []));
        $defaults = $template['defaults'] ?? [
            'can_view' => false,
            'can_create' => false,
            'can_update' => false,
            'can_delete' => false,
        ];
        $overrides = $template['page_overrides'] ?? [];
        $excluded = array_flip($template['exclude_pages'] ?? []);

        // Super Admin has no matrix — treat as full grant for UI preview
        if (($template['sets_role'] ?? null) === 'admin' || $defaults === null) {
            $full = [
                'can_view' => true,
                'can_create' => true,
                'can_update' => true,
                'can_delete' => true,
            ];
            $result = [];
            foreach ($pages as $slug) {
                $result[$slug] = $full;
            }

            return $result;
        }

        $result = [];
        foreach ($pages as $slug) {
            if (isset($excluded[$slug])) {
                $result[$slug] = [
                    'can_view' => false,
                    'can_create' => false,
                    'can_update' => false,
                    'can_delete' => false,
                ];
                continue;
            }

            $row = $overrides[$slug] ?? $defaults;
            $result[$slug] = [
                'can_view' => (bool) ($row['can_view'] ?? false),
                'can_create' => (bool) ($row['can_create'] ?? false),
                'can_update' => (bool) ($row['can_update'] ?? false),
                'can_delete' => (bool) ($row['can_delete'] ?? false),
            ];
        }

        return $result;
    }

    /**
     * Persist template permissions onto a user. Sets role when template defines sets_role.
     */
    public static function applyToUser(User $user, string $templateKey): void
    {
        $template = self::get($templateKey);
        if (! $template) {
            return;
        }

        if (! empty($template['sets_role'])) {
            $user->role = $template['sets_role'];
            $user->save();
        }

        if (($template['sets_role'] ?? null) === 'admin') {
            // Admin bypasses matrix; clear stale rows so UI stays clean
            $user->pagePermissions()->delete();

            return;
        }

        $map = self::buildPermissions($templateKey);
        foreach ($map as $slug => $flags) {
            UserPagePermission::updateOrCreate(
                ['user_id' => $user->id, 'page_slug' => $slug],
                $flags
            );
        }
    }

    public static function labelForRole(?string $role): string
    {
        return match ($role) {
            'admin' => 'Super Admin',
            'user' => 'User',
            default => ucfirst((string) $role),
        };
    }
}
