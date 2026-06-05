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

        // Hide edit/update/delete buttons when user lacks page permission
        Blade::if('canPage', function (string $page, string $action = 'view') {
            return auth()->check() && auth()->user()->canAccessPage($page, $action);
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
















