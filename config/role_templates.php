<?php

/**
 * Permission templates for User Management.
 *
 * Super Admin uses the built-in admin role (full access).
 * Contributor / Spectator remain role=user and get preset page permissions.
 *
 * Defaults apply to every page in config('pages.list') unless overridden
 * in `page_overrides` or excluded via `exclude_pages`.
 */
return [
    'templates' => [
        'super_admin' => [
            'label' => 'Super Admin',
            'description' => 'Full system access. Can manage users, settings, and all pages.',
            'icon' => 'fa-user-shield',
            'badge' => 'danger',
            'sets_role' => 'admin',
            // No page matrix needed — admin bypasses permission checks
            'defaults' => null,
            'exclude_pages' => [],
            'page_overrides' => [],
        ],

        'contributor' => [
            'label' => 'Contributor',
            'description' => 'Can view, add, and edit records across most pages. Delete is limited; user management stays admin-only.',
            'icon' => 'fa-user-edit',
            'badge' => 'primary',
            'sets_role' => 'user',
            'defaults' => [
                'can_view' => true,
                'can_create' => true,
                'can_update' => true,
                'can_delete' => false,
            ],
            // Sensitive / admin-only style pages: view only (or blocked below)
            'exclude_pages' => [
                // User management & activity logs stay with Super Admin
            ],
            'page_overrides' => [
                'settings' => [
                    'can_view' => true,
                    'can_create' => false,
                    'can_update' => false,
                    'can_delete' => false,
                ],
                'admin-docs' => [
                    'can_view' => false,
                    'can_create' => false,
                    'can_update' => false,
                    'can_delete' => false,
                ],
                'settings.financing' => [
                    'can_view' => true,
                    'can_create' => true,
                    'can_update' => true,
                    'can_delete' => false,
                ],
                'settings.branch-locations' => [
                    'can_view' => true,
                    'can_create' => true,
                    'can_update' => true,
                    'can_delete' => false,
                ],
            ],
        ],

        'spectator' => [
            'label' => 'Spectator',
            'description' => 'View-only access to available pages. Cannot create, edit, or delete records.',
            'icon' => 'fa-eye',
            'badge' => 'secondary',
            'sets_role' => 'user',
            'defaults' => [
                'can_view' => true,
                'can_create' => false,
                'can_update' => false,
                'can_delete' => false,
            ],
            'exclude_pages' => [],
            'page_overrides' => [
                'settings' => [
                    'can_view' => false,
                    'can_create' => false,
                    'can_update' => false,
                    'can_delete' => false,
                ],
                'admin-docs' => [
                    'can_view' => false,
                    'can_create' => false,
                    'can_update' => false,
                    'can_delete' => false,
                ],
                'settings.financing' => [
                    'can_view' => true,
                    'can_create' => false,
                    'can_update' => false,
                    'can_delete' => false,
                ],
                'settings.branch-locations' => [
                    'can_view' => true,
                    'can_create' => false,
                    'can_update' => false,
                    'can_delete' => false,
                ],
            ],
        ],
    ],
];
