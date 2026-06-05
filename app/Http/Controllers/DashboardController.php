<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Recent vehicles
        $recentVehicles = \App\Models\Vehicle::with(['make', 'vehicleModel', 'primaryImage'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Vehicle counts by status
        $totalVehicles = \App\Models\Vehicle::count();
        $availableVehicles = \App\Models\Vehicle::where('status', 'Available')->count();
        $underMaintenanceVehicles = \App\Models\Vehicle::where('status', 'Under Maintenance')->count();
        $reservedVehicles = \App\Models\Vehicle::where('status', 'Reserved')->count();
        $releasedVehicles = \App\Models\Vehicle::where('status', 'Released')->count();
        $forfeitedVehicles = \App\Models\Vehicle::where('status', 'Forfeited')->count();
        
        // For backward compatibility
        $soldVehicles = $releasedVehicles;
        $maintenanceVehicles = $underMaintenanceVehicles;
        
        // Monthly vehicle additions (last 6 months)
        $monthlyData = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[] = \App\Models\Vehicle::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        
        // Vehicle status distribution for pie chart
        $statusDistribution = [
            'Available' => $availableVehicles,
            'Reserved' => $reservedVehicles,
            'Released' => $releasedVehicles,
            'Under Maintenance' => $underMaintenanceVehicles,
            'Forfeited' => $forfeitedVehicles,
        ];
        
        // Top makes (by vehicle count)
        $topMakes = \App\Models\Vehicle::select('make_id')
            ->with('make')
            ->whereNotNull('make_id')
            ->get()
            ->groupBy('make_id')
            ->map(function ($vehicles) {
                return [
                    'name' => $vehicles->first()->make->name ?? 'Unknown',
                    'count' => $vehicles->count()
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();
        
        // Monthly expenses (last 6 months)
        $monthlyExpenses = [];
        $expenseLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $expenseLabels[] = $date->format('M Y');
            $monthlyExpenses[] = \App\Models\ExpenseItem::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('cost');
        }
        
        // Total expenses
        $totalExpenses = \App\Models\ExpenseItem::sum('cost');
        
        // Total purchase value
        $totalPurchaseValue = \App\Models\Vehicle::sum('purchase_price');
        
        // Employees count
        $totalEmployees = \App\Models\Employee::count();
        $activeEmployees = \App\Models\Employee::where('status', 'active')->count();
        
        // Sales agents count
        $totalSalesAgents = \App\Models\SalesAgent::count();
        $activeSalesAgents = \App\Models\SalesAgent::where('status', 'active')->count();
        
        return view('dashboard.index', compact(
            'user', 
            'recentVehicles', 
            'totalVehicles', 
            'availableVehicles', 
            'underMaintenanceVehicles', 
            'reservedVehicles', 
            'releasedVehicles', 
            'soldVehicles', 
            'maintenanceVehicles',
            'forfeitedVehicles',
            'monthlyLabels',
            'monthlyData',
            'statusDistribution',
            'topMakes',
            'expenseLabels',
            'monthlyExpenses',
            'totalExpenses',
            'totalPurchaseValue',
            'totalEmployees',
            'activeEmployees',
            'totalSalesAgents',
            'activeSalesAgents'
        ));
    }
}
