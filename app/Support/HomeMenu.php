<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class HomeMenu
{
    /**
     * Categories and items the user is allowed to view on the home page.
     */
    public static function categoriesForUser(?User $user): array
    {
        $categories = config('home_menu.categories', []);
        $visible = [];

        foreach ($categories as $category) {
            $items = [];
            foreach ($category['items'] ?? [] as $item) {
                if (self::userCanViewItem($user, $item)) {
                    $items[] = self::hydrateItemUrl($item);
                }
            }

            if ($items !== []) {
                $category['items'] = $items;
                $visible[] = $category;
            }
        }

        return $visible;
    }

    public static function userCanViewItem(?User $user, array $item): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $page = self::resolvePageSlug($item);

        if ($page === null) {
            return false;
        }

        return $user->canAccessPage($page, 'view');
    }

    public static function resolvePageSlug(array $item): ?string
    {
        if (! empty($item['page'])) {
            return $item['page'];
        }

        $routeName = $item['route'] ?? null;
        if (! $routeName) {
            return null;
        }

        $info = config('pages.route_to_permission')[$routeName] ?? null;

        return $info['page'] ?? null;
    }

    protected static function hydrateItemUrl(array $item): array
    {
        $routeName = $item['route'] ?? null;
        if ($routeName && Route::has($routeName)) {
            $item['url'] = route($routeName, $item['route_params'] ?? []);
        } else {
            $item['url'] = '#';
        }

        return $item;
    }
}
