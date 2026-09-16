<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 4 pagination
        \Illuminate\Pagination\Paginator::useBootstrapFour();

        // Behind cPanel / Cloudflare: HTTPS when needed, and subdirectory root only when APP_URL has a path
        // (e.g. https://carempireph.com/db-system). Do not forceRootUrl for plain local APP_URL —
        // that breaks php artisan serve on 127.0.0.1:8000 when APP_URL is http://localhost.
        $appUrl = (string) config('app.url');
        if ($appUrl !== '') {
            if (str_starts_with($appUrl, 'https://')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
            $parts = parse_url($appUrl);
            $path = isset($parts['path']) ? rtrim($parts['path'], '/') : '';
            if (! empty($parts['host']) && $path !== '' && $path !== '/') {
                $root = ($parts['scheme'] ?? 'https').'://'.$parts['host'];
                if (! empty($parts['port'])) {
                    $root .= ':'.$parts['port'];
                }
                $root .= $path;
                \Illuminate\Support\Facades\URL::forceRootUrl($root);
            }
        }

        // Hide edit/update/delete buttons when user lacks page permission
        Blade::if('canPage', function (string $page, string $action = 'view') {
            return auth()->check() && auth()->user()->canAccessPage($page, $action);
        });

        // Unit Report Purchase Price: Super Admin, or granted via user permissions
        Blade::if('canViewPurchasePrice', function () {
            return auth()->check() && auth()->user()->canViewPurchasePrice();
        });

        // Register activity observers for automatic logging
        // Note: Models that are manually logged in controllers are NOT observed to prevent duplicates.
        // Only observe models that might be updated outside of controllers or don't have manual logging.
        
        // These models are manually logged in controllers, so we don't observe them:
        // - ExpenseItem (logged in ExpenseController)
        // - CashAddition (logged in SOAController)
        // - DailyBudget (logged in SOAController)
        // - ExpenseItemReceipt (logged in ExpenseController)
        // - ExpenseTransaction (logged in ExpenseController)
        // - Vehicle (logged in VehicleController)
        // - Tool (logged in ToolsController)
        // - SalesAgent (logged in SalesAgentController)
        // - Employee (logged in EmployeeController)
        
        // If you add new models that don't have manual logging, add them here:
        // NewModel::observe(ActivityObserver::class);
    }
}
















