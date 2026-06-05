<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the analytics page with detailed reports.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vehicle Statistics
        $totalVehicles = \App\Models\Vehicle::count();
        $availableVehicles = \App\Models\Vehicle::where('status', 'Available')->count();
        $underMaintenanceVehicles = \App\Models\Vehicle::where('status', 'Under Maintenance')->count();
        $reservedVehicles = \App\Models\Vehicle::where('status', 'Reserved')->count();
        $releasedVehicles = \App\Models\Vehicle::where('status', 'Released')->count();
        $forfeitedVehicles = \App\Models\Vehicle::where('status', 'Forfeited')->count();
        
        // Monthly vehicle additions (last 12 months for more detail)
        $monthlyData = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[] = \App\Models\Vehicle::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        
        // Yearly vehicle additions (last 5 years)
        $yearlyData = [];
        $yearlyLabels = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;
            $yearlyLabels[] = $year;
            $yearlyData[] = \App\Models\Vehicle::whereYear('created_at', $year)->count();
        }
        
        // Vehicle status distribution
        $statusDistribution = [
            'Available' => $availableVehicles,
            'Reserved' => $reservedVehicles,
            'Released' => $releasedVehicles,
            'Under Maintenance' => $underMaintenanceVehicles,
            'Forfeited' => $forfeitedVehicles,
        ];
        
        // Top makes (top 10 for more detail)
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
            ->take(10)
            ->values();
        
        // Top models (top 10)
        $topModels = \App\Models\Vehicle::select('model_id')
            ->with('vehicleModel')
            ->whereNotNull('model_id')
            ->get()
            ->groupBy('model_id')
            ->map(function ($vehicles) {
                return [
                    'name' => $vehicles->first()->vehicleModel->name ?? 'Unknown',
                    'count' => $vehicles->count()
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values();
        
        // Monthly expenses (last 12 months)
        $monthlyExpenses = [];
        $expenseLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $expenseLabels[] = $date->format('M Y');
            $monthlyExpenses[] = \App\Models\ExpenseItem::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('cost');
        }
        
        // Expense breakdown by payment tag
        $expenseByTag = \App\Models\ExpenseItem::whereNotNull('payment_tag')
            ->get()
            ->groupBy('payment_tag')
            ->map(function ($items) {
                return [
                    'name' => $items->first()->payment_tag ?? 'Uncategorized',
                    'total' => $items->sum('cost'),
                    'count' => $items->count()
                ];
            })
            ->sortByDesc('total')
            ->values();
        
        // Total expenses
        $totalExpenses = \App\Models\ExpenseItem::sum('cost');
        
        // Total purchase value
        $totalPurchaseValue = \App\Models\Vehicle::sum('purchase_price');
        
        // Average purchase price
        $averagePurchasePrice = $totalVehicles > 0 ? ($totalPurchaseValue / $totalVehicles) : 0;
        
        // Vehicles by transmission type
        $transmissionData = \App\Models\Vehicle::whereNotNull('transmission')
            ->select('transmission')
            ->get()
            ->groupBy('transmission')
            ->map->count();
        
        // Vehicles by fuel type
        $fuelTypeData = \App\Models\Vehicle::whereNotNull('fuel_type')
            ->select('fuel_type')
            ->get()
            ->groupBy('fuel_type')
            ->map->count();
        
        // Employees statistics
        $totalEmployees = \App\Models\Employee::count();
        $activeEmployees = \App\Models\Employee::where('status', 'active')->count();
        $inactiveEmployees = \App\Models\Employee::where('status', 'inactive')->count();
        
        // Employees by contract type
        $employeeContractTypes = \App\Models\Employee::whereNotNull('contract_type')
            ->select('contract_type')
            ->get()
            ->groupBy('contract_type')
            ->map->count();
        
        // Sales agents statistics
        $totalSalesAgents = \App\Models\SalesAgent::count();
        $activeSalesAgents = \App\Models\SalesAgent::where('status', 'active')->count();
        $inactiveSalesAgents = \App\Models\SalesAgent::where('status', 'inactive')->count();
        
        // Monthly revenue (from released vehicles - sold price)
        $monthlyRevenue = [];
        $revenueLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueLabels[] = $date->format('M Y');
            $monthlyRevenue[] = \App\Models\Vehicle::where('status', 'Released')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->sum('sold_price');
        }
        
        // Total revenue
        $totalRevenue = \App\Models\Vehicle::where('status', 'Released')->sum('sold_price');
        
        // Profit calculation (Revenue - Purchase Value - Expenses)
        $totalProfit = $totalRevenue - $totalPurchaseValue - $totalExpenses;
        
        // Vehicles by year (manufacturing year distribution)
        $vehiclesByYear = \App\Models\Vehicle::whereNotNull('year')
            ->select('year')
            ->get()
            ->groupBy('year')
            ->map->count()
            ->sortKeys()
            ->take(15)
            ->toArray();
        
        return view('analytics.index', compact(
            'user',
            'totalVehicles',
            'availableVehicles',
            'underMaintenanceVehicles',
            'reservedVehicles',
            'releasedVehicles',
            'forfeitedVehicles',
            'monthlyLabels',
            'monthlyData',
            'yearlyLabels',
            'yearlyData',
            'statusDistribution',
            'topMakes',
            'topModels',
            'expenseLabels',
            'monthlyExpenses',
            'expenseByTag',
            'totalExpenses',
            'totalPurchaseValue',
            'averagePurchasePrice',
            'transmissionData',
            'fuelTypeData',
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'employeeContractTypes',
            'totalSalesAgents',
            'activeSalesAgents',
            'inactiveSalesAgents',
            'revenueLabels',
            'monthlyRevenue',
            'totalRevenue',
            'totalProfit',
            'vehiclesByYear'
        ));
    }
}
