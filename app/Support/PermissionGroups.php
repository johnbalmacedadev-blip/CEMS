<?php

namespace App\Support;

class PermissionGroups
{
    /**
     * Pages grouped for the user permissions matrix (matches home menu categories).
     *
     * @return array<int, array{id: string, title: string, description?: string, icon?: string, pages: array<string, string>}>
     */
    public static function forPermissionsMatrix(): array
    {
        $allPages = config('pages.list', []);
        $groups = config('pages.permission_groups', []);
        $assigned = [];
        $result = [];

        foreach ($groups as $group) {
            $pages = [];
            foreach ($group['pages'] ?? [] as $slug) {
                if (! isset($allPages[$slug]) || isset($assigned[$slug])) {
                    continue;
                }
                $pages[$slug] = $allPages[$slug];
                $assigned[$slug] = true;
            }

            if ($pages !== []) {
                $result[] = [
                    'id' => $group['id'] ?? 'group-' . count($result),
                    'title' => $group['title'] ?? 'Group',
                    'description' => $group['description'] ?? '',
                    'icon' => $group['icon'] ?? 'fa-folder',
                    'pages' => $pages,
                ];
            }
        }

        $ungrouped = array_diff_key($allPages, $assigned);
        if ($ungrouped !== []) {
            $result[] = [
                'id' => 'other',
                'title' => 'Other',
                'description' => 'Additional features not yet categorized',
                'icon' => 'fa-ellipsis-h',
                'pages' => $ungrouped,
            ];
        }

        return $result;
    }
}
