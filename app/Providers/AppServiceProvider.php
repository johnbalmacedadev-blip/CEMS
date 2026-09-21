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

        // Behind cPanel / Cloudflare: HTTPS when needed, and subdirectory root from APP_URL
        // (e.g. https://carempireph.com/cedbase). Also detect SCRIPT_NAME (/cedbase/index.php)
        // so redirects never jump to https://carempireph.com/login outside the app folder.
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

        if (! $this->app->runningInConsole()) {
            $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
            if (preg_match('#^(/.+)/index\.php$#', $script, $m) && $m[1] !== '') {
                $subdir = $m[1];
                $scheme = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                if (! empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                    $scheme = strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https' ? 'https' : $scheme;
                }
                $host = (string) ($_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? ''));
                if ($host !== '') {
                    \Illuminate\Support\Facades\URL::forceRootUrl($scheme.'://'.$host.$subdir);
                    if ($scheme === 'https') {
                        \Illuminate\Support\Facades\URL::forceScheme('https');
                    }
                }
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
















