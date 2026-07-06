<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GasExpenseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::redirect('/', '/login');

// Test route for debugging (temporary)
Route::get('/test-models', function() {
    $camry = \App\Models\VehicleModel::where('name', 'LIKE', '%Camry%')->first();
    $toyotaModels = \App\Models\VehicleModel::where('make_id', 1)->get(['id', 'name', 'is_active']);
    
    return response()->json([
        'camry' => $camry,
        'toyota_models' => $toyotaModels,
        'total_models' => \App\Models\VehicleModel::count()
    ]);
});

// Test route for debugging uploads (outside auth)
Route::get('/test-upload', function() {
    return response()->json([
        'storage_path' => storage_path('app/public/vehicles'),
        'storage_exists' => file_exists(storage_path('app/public/vehicles')),
        'storage_writable' => is_writable(storage_path('app/public/vehicles')),
        'public_link_exists' => file_exists(public_path('storage')),
    ]);
});

// API routes for autocomplete search (moved outside auth for testing)
Route::get('/api/makes/search', function(\Illuminate\Http\Request $request) {
    try {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([], 200);
        }
        
        $makes = \App\Models\Make::where('name', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);
        
        return response()->json($makes, 200, [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in makes search: ' . $e->getMessage());
        return response()->json(['error' => 'An error occurred while searching makes'], 500);
    }
});

Route::get('/api/models/search', function(\Illuminate\Http\Request $request) {
    try {
        $query = $request->get('q', '');
        $makeId = $request->get('make_id');
        
        // Debug: Log the request parameters
        \Log::info('Model search request', [
            'query' => $query,
            'make_id' => $makeId
        ]);
        
        if (empty($query)) {
            return response()->json([], 200);
        }
        
        $models = \App\Models\VehicleModel::where('name', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->limit(10);
            
        if ($makeId) {
            $models->where('make_id', $makeId);
        }
        
        $results = $models->get(['id', 'name']);
        
        // Debug: Log the results
        \Log::info('Model search results', [
            'count' => $results->count(),
            'results' => $results->toArray()
        ]);
        
        return response()->json($results, 200, [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in models search: ' . $e->getMessage());
        return response()->json(['error' => 'An error occurred while searching models'], 500);
    }
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', function(\Illuminate\Http\Request $request) {
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user) {
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'model_type' => 'Auth',
            'model_id' => null,
            'description' => 'Logged out',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->with('success', 'You have been logged out successfully.');
})->middleware('auth');

// Live team chat (all authenticated users)
Route::middleware('auth')->prefix('api/chat')->group(function () {
    Route::get('/sync', [App\Http\Controllers\ChatController::class, 'sync'])->name('chat.sync');
    Route::post('/messages', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');
    Route::post('/heartbeat', [App\Http\Controllers\ChatController::class, 'heartbeat'])->name('chat.heartbeat');
});

// Protected routes: admin-only user management (no page permission check)
Route::middleware(['auth', 'admin', 'log.activity'])->group(function () {
    Route::get('/settings/users', [UserManagementController::class, 'index'])->name('settings.users.index');
    Route::get('/settings/users/create', [UserManagementController::class, 'create'])->name('settings.users.create');
    Route::post('/settings/users', [UserManagementController::class, 'store'])->name('settings.users.store');
    Route::get('/settings/users/{user}/edit', [UserManagementController::class, 'edit'])->name('settings.users.edit');
    Route::put('/settings/users/{user}', [UserManagementController::class, 'update'])->name('settings.users.update');
    Route::delete('/settings/users/{user}', [UserManagementController::class, 'destroy'])->name('settings.users.destroy');
    Route::get('/settings/users/{user}/permissions', [UserManagementController::class, 'permissions'])->name('settings.users.permissions');
    Route::put('/settings/users/{user}/permissions', [UserManagementController::class, 'updatePermissions'])->name('settings.users.permissions.update');
});

// Protected routes: page-level permissions applied for non-admins
Route::middleware(['auth', 'page.permission', 'log.activity'])->group(function () {
    // Main home page after login
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
    // Placeholder section routes (to be implemented)
    Route::view('/cars-boost-videos', 'placeholders.cars-boost-videos')->name('cars-boost-videos');
    Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics-report/financial', [App\Http\Controllers\AnalyticsReportController::class, 'financial'])->name('analytics-report.financial');
    Route::get('/analytics-report/financial/export', [App\Http\Controllers\AnalyticsReportController::class, 'exportFinancial'])->name('analytics-report.financial.export');
    Route::get('/analytics-report/sales', [App\Http\Controllers\AnalyticsReportController::class, 'sales'])->name('analytics-report.sales');
    Route::get('/analytics-report/sales-executive', [App\Http\Controllers\AnalyticsReportController::class, 'salesExecutive'])->name('analytics-report.sales-executive');
    Route::get('/settings', function() { return view('settings.index'); })->name('settings');
    Route::get('/settings/financing', [App\Http\Controllers\CarFinancingSettingController::class, 'index'])->name('settings.financing.index');
    Route::post('/settings/financing', [App\Http\Controllers\CarFinancingSettingController::class, 'store'])->name('settings.financing.store');
    Route::put('/settings/financing/{car_financing_setting}', [App\Http\Controllers\CarFinancingSettingController::class, 'update'])->name('settings.financing.update');
    Route::delete('/settings/financing/{car_financing_setting}', [App\Http\Controllers\CarFinancingSettingController::class, 'destroy'])->name('settings.financing.destroy');
    Route::post('/settings/financing/schemes', [App\Http\Controllers\CarFinancingSettingController::class, 'storeScheme'])->name('settings.financing.schemes.store');
    Route::put('/settings/financing/schemes/{financing_scheme}', [App\Http\Controllers\CarFinancingSettingController::class, 'updateScheme'])->name('settings.financing.schemes.update');
    Route::delete('/settings/financing/schemes/{financing_scheme}', [App\Http\Controllers\CarFinancingSettingController::class, 'destroyScheme'])->name('settings.financing.schemes.destroy');
    Route::get('/settings/branch-locations', [App\Http\Controllers\BranchLocationController::class, 'index'])->name('settings.branch-locations.index');
    Route::post('/settings/branch-locations', [App\Http\Controllers\BranchLocationController::class, 'store'])->name('settings.branch-locations.store');
    Route::put('/settings/branch-locations/{branch_location}', [App\Http\Controllers\BranchLocationController::class, 'update'])->name('settings.branch-locations.update');
    Route::delete('/settings/branch-locations/{branch_location}', [App\Http\Controllers\BranchLocationController::class, 'destroy'])->name('settings.branch-locations.destroy');
    Route::get('/payroll', [App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
    Route::resource('source-screenshots', App\Http\Controllers\SourceScreenshotController::class)->names('source-screenshots');
    Route::get('/car-video-boost-report', [App\Http\Controllers\CarVideoBoostReportController::class, 'index'])->name('car-video-boost-report.index');
    Route::post('/car-video-boost-report/ads', [App\Http\Controllers\CarVideoBoostReportController::class, 'storeAd'])->name('car-video-boost-report.store-ad');
    Route::put('/car-video-boost-report/ads/{vehicleAd}', [App\Http\Controllers\CarVideoBoostReportController::class, 'updateAd'])->name('car-video-boost-report.update-ad');
    Route::delete('/car-video-boost-report/ads/{vehicleAd}', [App\Http\Controllers\CarVideoBoostReportController::class, 'destroyAd'])->name('car-video-boost-report.destroy-ad');
    Route::resource('video-posting-tracker', App\Http\Controllers\VideoPostingTrackerController::class)->names('video-posting-tracker');
    Route::resource('follow-up-documents', App\Http\Controllers\FollowUpDocumentsController::class)->names('follow-up-documents');
    Route::resource('client-follow-up-list', App\Http\Controllers\ClientFollowUpListController::class)->names('client-follow-up-list');
    Route::resource('appointment-list', App\Http\Controllers\AppointmentListController::class)->parameters(['appointment-list' => 'appointment_list'])->names('appointment-list');
    Route::resource('trail-form-list', App\Http\Controllers\TrailFormListController::class)->parameters(['trail-form-list' => 'trail_form_list'])->names('trail-form-list');
    Route::get('/api/contracts/vehicles/search', [App\Http\Controllers\ContractController::class, 'searchVehicles'])->name('contracts.vehicles.search');
    Route::get('/api/sales-agent-commissions/agents/search', [App\Http\Controllers\SalesAgentCommissionController::class, 'searchAgents'])->name('sales-agent-commissions.agents.search');
    Route::resource('contracts', App\Http\Controllers\ContractController::class)->names('contracts');
    Route::get('/transfer-orcr/export/pdf', [App\Http\Controllers\TransferOrcrController::class, 'exportPdf'])->name('transfer-orcr.export-pdf');
    Route::get('/transfer-orcr/summary-report', [App\Http\Controllers\TransferOrcrController::class, 'summaryReport'])->name('transfer-orcr.summary-report');
    Route::resource('transfer-orcr', App\Http\Controllers\TransferOrcrController::class)->names('transfer-orcr');
    Route::resource('vehicle-registration', App\Http\Controllers\VehicleRegistrationController::class)->names('vehicle-registration');
    Route::resource('sales-agent-commissions', App\Http\Controllers\SalesAgentCommissionController::class)->names('sales-agent-commissions');
    
    // Expenses & Inventory routes
    Route::get('/expenses-inventory', [App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses-inventory');
    Route::get('/expenses-inventory/export', [App\Http\Controllers\ExpenseController::class, 'exportInventory'])->name('expenses-inventory.export');
    Route::get('/expenses/create', [App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expenseTransaction}', [App\Http\Controllers\ExpenseController::class, 'show'])->name('expenses.show');
    Route::put('/expenses/{expenseTransaction}', [App\Http\Controllers\ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expenseTransaction}', [App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::post('/expenses/{expenseTransaction}/items', [App\Http\Controllers\ExpenseController::class, 'addExpenseItem'])->name('expenses.items.store');
    Route::put('/expenses/{expenseTransaction}/items/{expenseItem}', [App\Http\Controllers\ExpenseController::class, 'updateExpenseItem'])->name('expenses.items.update');
    Route::delete('/expenses/{expenseTransaction}/items/{expenseItem}', [App\Http\Controllers\ExpenseController::class, 'deleteExpenseItem'])->name('expenses.items.destroy');
    Route::post('/expenses/{expenseTransaction}/items/{expenseItem}/receipts', [App\Http\Controllers\ExpenseController::class, 'uploadReceipts'])->name('expenses.items.receipts.store');
    Route::delete('/expenses/{expenseTransaction}/items/{expenseItem}/receipts/{expenseItemReceipt}', [App\Http\Controllers\ExpenseController::class, 'deleteReceipt'])->name('expenses.items.receipts.destroy');
    Route::get('/api/expenses/vehicles/search', [App\Http\Controllers\ExpenseController::class, 'searchVehicles'])->name('expenses.vehicles.search');
    Route::get('/api/expenses/vehicle-categories', [App\Http\Controllers\ExpenseController::class, 'getVehicleCategories'])->name('expenses.vehicle-categories.index');
    Route::post('/api/expenses/vehicle-categories', [App\Http\Controllers\ExpenseController::class, 'addVehicleCategory'])->name('expenses.vehicle-categories.store');
    Route::get('/api/expenses/payment-methods', [App\Http\Controllers\ExpenseController::class, 'getPaymentMethods'])->name('expenses.payment-methods.index');
    
    // Tools routes (moved to expenses-inventory)
    // IMPORTANT: More specific routes (search, history) must come BEFORE parameterized routes ({tool})
    Route::get('/api/tools/search', [App\Http\Controllers\ToolsController::class, 'search'])->name('tools.search');
    Route::get('/api/tools/history', [App\Http\Controllers\ToolsController::class, 'history'])->name('tools.history');
    Route::post('/api/tools', [App\Http\Controllers\ToolsController::class, 'store'])->name('tools.store');
    Route::get('/api/tools/{tool}', [App\Http\Controllers\ToolsController::class, 'show'])->name('tools.show');
    Route::put('/api/tools/{tool}', [App\Http\Controllers\ToolsController::class, 'update'])->name('tools.update');
    Route::delete('/api/tools/{tool}', [App\Http\Controllers\ToolsController::class, 'destroy'])->name('tools.destroy');
    
    // Redirect old operations-tracker to expenses-inventory
    Route::get('/operations-tracker', function() {
        return redirect()->route('expenses-inventory', ['section' => 'tools-purchase']);
    });

    // Mechanic Tools/Expenses — dedicated page (shows expenses-inventory tools-purchase section)
    Route::get('/mechanic-tools-expenses', function() {
        return redirect()->route('expenses-inventory', ['section' => 'tools-purchase']);
    })->name('mechanic-tools-expenses');
    Route::get('/admin-docs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('admin-docs');
    
    // Car Photos Folder (Car Reports)
    Route::get('/car-photos-folder', [App\Http\Controllers\VehicleImageController::class, 'carPhotosFolder'])->name('car-photos-folder');
    
    // Pricelist (Car Reports)
    Route::get('/pricelist', [VehicleController::class, 'pricelist'])->name('pricelist');
    Route::get('/pricelist/export/pdf', [VehicleController::class, 'exportPricelistPdf'])->name('pricelist.exportPdf');
    Route::post('/pricelist/vehicles/{vehicle}/financing-details', [VehicleController::class, 'storePricelistFinancing'])->name('pricelist.financing.store');
    Route::post('/pricelist/financing-details-bulk', [VehicleController::class, 'storePricelistFinancingBulk'])->name('pricelist.financing.storeBulk');
    Route::post('/pricelist/update-all-financing', [VehicleController::class, 'updateAllPricelistFinancing'])->name('pricelist.financing.updateAll');
    Route::post('/pricelist/set-posted-price-10-percent', [VehicleController::class, 'setPostedPrice10Percent'])->name('pricelist.setPostedPrice10Percent');
    
    // Buffing Tracker (Staff Reports)
    Route::resource('buffing-tracker', App\Http\Controllers\BuffingTrackerController::class)->names('buffing-tracker');
    Route::resource('insurance-tracker', App\Http\Controllers\InsuranceTrackerController::class)->parameters(['insurance-tracker' => 'insurance_tracker'])->names('insurance-tracker');
    Route::resource('recommendation-tracker', App\Http\Controllers\RecommendationTrackerController::class)->parameters(['recommendation-tracker' => 'recommendation_tracker'])->names('recommendation-tracker');
    Route::delete('recommendation-tracker/{recommendation_tracker}/images/{image}', [App\Http\Controllers\RecommendationTrackerController::class, 'destroyImage'])->name('recommendation-tracker.images.destroy');
    
    // Gas Expenses / P.O. Tracker (Payments/Expenses Reports)
    Route::get('/gas-expense-po-tracker', [App\Http\Controllers\GasExpensePoTrackerController::class, 'index'])->name('gas-expense-po-tracker.index');
    Route::get('/gas-expense-po-tracker/export/pdf', [App\Http\Controllers\GasExpensePoTrackerController::class, 'exportPdf'])->name('gas-expense-po-tracker.export-pdf');
    Route::post('/gas-expense-po-tracker/po', [App\Http\Controllers\GasExpensePoTrackerController::class, 'storePo'])->name('gas-expense-po-tracker.store-po');
    Route::put('/gas-expense-po-tracker/{purchase_order}/po', [App\Http\Controllers\GasExpensePoTrackerController::class, 'updatePo'])->name('gas-expense-po-tracker.update-po');
    Route::delete('/gas-expense-po-tracker/{purchase_order}/po', [App\Http\Controllers\GasExpensePoTrackerController::class, 'destroyPo'])->name('gas-expense-po-tracker.destroy-po');
    Route::post('/gas-expense-po-tracker/gas-expenses', [App\Http\Controllers\GasExpensePoTrackerController::class, 'storeGasExpense'])->name('gas-expense-po-tracker.store-gas');
    Route::put('/gas-expense-po-tracker/gas-expenses/{gasExpense}', [App\Http\Controllers\GasExpensePoTrackerController::class, 'updateGasExpense'])->name('gas-expense-po-tracker.update-gas');
    Route::delete('/gas-expense-po-tracker/gas-expenses/{gasExpense}', [App\Http\Controllers\GasExpensePoTrackerController::class, 'destroyGasExpense'])->name('gas-expense-po-tracker.destroy-gas');
    
    // Vehicle routes (export-list must be registered before vehicles/{vehicle})
    Route::get('vehicles/export-list', [VehicleController::class, 'exportIndex'])->name('vehicles.export-list');
    Route::get('vehicles/search-archiveable', [VehicleController::class, 'searchArchiveable'])->name('vehicles.search-archiveable');
    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/archive', [VehicleController::class, 'archive'])->name('vehicles.archive');
    Route::get('vehicles/{vehicle}/export', [VehicleController::class, 'export'])->name('vehicles.export');
    
    // Vehicle posted price routes
    Route::put('vehicles/{vehicle}/posted-price', [VehicleController::class, 'updatePostedPrice'])->name('vehicles.posted-price.update');
    Route::delete('vehicles/{vehicle}/posted-price', [VehicleController::class, 'deletePostedPrice'])->name('vehicles.posted-price.delete');
    
    // Vehicle sold price routes
    Route::put('vehicles/{vehicle}/sold-price', [VehicleController::class, 'updateSoldPrice'])->name('vehicles.sold-price.update');
    Route::delete('vehicles/{vehicle}/sold-price', [VehicleController::class, 'deleteSoldPrice'])->name('vehicles.sold-price.delete');
    
    // Vehicle incentive (S.A origin, reserved by, checkboxes + link/attach)
    Route::put('vehicles/{vehicle}/incentive', [VehicleController::class, 'updateIncentive'])->name('vehicles.incentive.update');
    
    // Vehicle image routes
    Route::get('vehicles/{vehicle}/images', [App\Http\Controllers\VehicleImageController::class, 'index'])->name('vehicles.images.index');
    Route::post('vehicles/{vehicle}/images', [App\Http\Controllers\VehicleImageController::class, 'store'])->name('vehicles.images.store');
    Route::put('vehicles/{vehicle}/images/{image}/primary', [App\Http\Controllers\VehicleImageController::class, 'setPrimary'])->name('vehicles.images.primary');
    Route::delete('vehicles/{vehicle}/images/{image}', [App\Http\Controllers\VehicleImageController::class, 'destroy'])->name('vehicles.images.destroy');
    
        // Vehicle expense routes
        Route::get('vehicles/{vehicle}/expenses/create', [App\Http\Controllers\VehicleExpenseController::class, 'create'])->name('vehicles.expenses.create');
        Route::post('vehicles/{vehicle}/expenses', [App\Http\Controllers\VehicleExpenseController::class, 'store'])->name('vehicles.expenses.store');
        Route::post('vehicles/{vehicle}/post-reservation-expenses', [App\Http\Controllers\VehicleExpenseController::class, 'storePostReservationExpense'])->name('vehicles.post-reservation-expenses.store');
        Route::post('vehicles/{vehicle}/post-release-expenses', [App\Http\Controllers\VehicleExpenseController::class, 'storePostReleaseExpense'])->name('vehicles.post-release-expenses.store');
        Route::put('vehicles/{vehicle}/expenses/{expense}', [App\Http\Controllers\VehicleExpenseController::class, 'update'])->name('vehicles.expenses.update');
        Route::delete('vehicles/{vehicle}/expenses/{expense}', [App\Http\Controllers\VehicleExpenseController::class, 'destroy'])->name('vehicles.expenses.destroy');
        
        // Vehicle Ads routes
        Route::post('vehicles/{vehicle}/ads', [App\Http\Controllers\VehicleAdController::class, 'store'])->name('vehicles.ads.store');
        Route::post('vehicles/{vehicle}/forfeit-details', [App\Http\Controllers\VehicleForfeitDetailController::class, 'store'])->name('vehicles.forfeit-details.store');
        Route::put('vehicles/{vehicle}/ads/{vehicleAd}', [App\Http\Controllers\VehicleAdController::class, 'update'])->name('vehicles.ads.update');
        Route::delete('vehicles/{vehicle}/ads/{vehicleAd}', [App\Http\Controllers\VehicleAdController::class, 'destroy'])->name('vehicles.ads.destroy');
        
        // Vehicle status routes
        Route::put('vehicles/{vehicle}/status', [App\Http\Controllers\VehicleStatusController::class, 'updateStatus'])->name('vehicles.status.update');
        Route::post('vehicles/{vehicle}/status-details', [App\Http\Controllers\VehicleStatusController::class, 'storeStatusDetails'])->name('vehicles.status-details.store');
        Route::get('vehicles/{vehicle}/status-details', [App\Http\Controllers\VehicleStatusController::class, 'getStatusDetails'])->name('vehicles.status-details.get');
        Route::get('vehicles/{vehicle}/reservation-details', [App\Http\Controllers\VehicleStatusController::class, 'showReservationDetails'])->name('vehicles.reservation-details.show');
        Route::delete('vehicles/{vehicle}/status-details', [App\Http\Controllers\VehicleStatusController::class, 'deleteStatusDetails'])->name('vehicles.status-details.delete');
        
        // Gas expense routes
        Route::get('vehicles/{vehicle}/gas-expenses', [GasExpenseController::class, 'index'])->name('vehicles.gas-expenses.index');
        Route::post('vehicles/{vehicle}/gas-expenses', [GasExpenseController::class, 'store'])->name('vehicles.gas-expenses.store');
        Route::put('vehicles/{vehicle}/gas-expenses/{gasExpense}', [GasExpenseController::class, 'update'])->name('vehicles.gas-expenses.update');
        Route::delete('vehicles/{vehicle}/gas-expenses/{gasExpense}', [GasExpenseController::class, 'destroy'])->name('vehicles.gas-expenses.destroy');
        
        // Custom section routes
        Route::get('vehicles/{vehicle}/custom-sections', [App\Http\Controllers\CustomSectionController::class, 'index'])->name('vehicles.custom-sections.index');
        Route::post('vehicles/{vehicle}/custom-sections', [App\Http\Controllers\CustomSectionController::class, 'store'])->name('vehicles.custom-sections.store');
        Route::get('vehicles/{vehicle}/custom-sections/{customSection}/edit', [App\Http\Controllers\CustomSectionController::class, 'edit'])->name('vehicles.custom-sections.edit');
        Route::put('vehicles/{vehicle}/custom-sections/{customSection}', [App\Http\Controllers\CustomSectionController::class, 'update'])->name('vehicles.custom-sections.update');
        Route::delete('vehicles/{vehicle}/custom-sections/{customSection}', [App\Http\Controllers\CustomSectionController::class, 'destroy'])->name('vehicles.custom-sections.destroy');
        
        // Vehicle custom field routes
        Route::post('vehicles/{vehicle}/custom-fields', [App\Http\Controllers\VehicleCustomFieldController::class, 'store'])->name('vehicles.custom-fields.store');
        Route::get('vehicles/{vehicle}/custom-fields/{customField}', [App\Http\Controllers\VehicleCustomFieldController::class, 'show'])->name('vehicles.custom-fields.show');
        Route::put('vehicles/{vehicle}/custom-fields/{customField}', [App\Http\Controllers\VehicleCustomFieldController::class, 'update'])->name('vehicles.custom-fields.update');
        Route::delete('vehicles/{vehicle}/custom-fields/{customField}', [App\Http\Controllers\VehicleCustomFieldController::class, 'destroy'])->name('vehicles.custom-fields.destroy');
        Route::get('vehicles/{vehicle}/custom-fields/section/{sectionName}', [App\Http\Controllers\VehicleCustomFieldController::class, 'getFieldsForSection'])->name('vehicles.custom-fields.section');
    
        // API routes for dropdowns
        Route::get('/api/models/{make}', function($makeId) {
            return \App\Models\VehicleModel::where('make_id', $makeId)->active()->orderBy('name')->get();
        });
        
        // Vehicle document routes
        // IMPORTANT: More specific routes (mark-completed) must come BEFORE parameterized routes ({document})
        Route::get('vehicles/{vehicle}/documents', [App\Http\Controllers\VehicleDocumentController::class, 'index'])->name('vehicles.documents.index');
        Route::post('vehicles/{vehicle}/documents/mark-completed', [App\Http\Controllers\VehicleDocumentController::class, 'markNewCompleted'])->name('vehicles.documents.mark-new-completed');
        Route::get('vehicles/{vehicle}/documents/{documentType}/add-details', [App\Http\Controllers\VehicleDocumentController::class, 'addDetails'])->name('vehicles.documents.add-details');
        Route::get('vehicles/{vehicle}/documents/{documentType}/create', [App\Http\Controllers\VehicleDocumentController::class, 'create'])->name('vehicles.documents.create');
        Route::post('vehicles/{vehicle}/documents/{documentType}', [App\Http\Controllers\VehicleDocumentController::class, 'store'])->name('vehicles.documents.store');
        Route::get('vehicles/{vehicle}/documents/{document}/edit', [App\Http\Controllers\VehicleDocumentController::class, 'edit'])->name('vehicles.documents.edit');
        Route::put('vehicles/{vehicle}/documents/{document}', [App\Http\Controllers\VehicleDocumentController::class, 'update'])->name('vehicles.documents.update');
        Route::get('vehicles/{vehicle}/documents/{document}', [App\Http\Controllers\VehicleDocumentController::class, 'show'])->name('vehicles.documents.show');
        Route::delete('vehicles/{vehicle}/documents/{document}', [App\Http\Controllers\VehicleDocumentController::class, 'destroy'])->name('vehicles.documents.destroy');
        Route::get('vehicles/{vehicle}/documents/{document}/download', [App\Http\Controllers\VehicleDocumentController::class, 'download'])->name('vehicles.documents.download');
        Route::get('vehicles/{vehicle}/documents/files/{file}/download', [App\Http\Controllers\VehicleDocumentController::class, 'downloadFile'])->name('vehicles.documents.files.download');
        Route::post('vehicles/{vehicle}/documents/{document}/mark-completed', [App\Http\Controllers\VehicleDocumentController::class, 'markCompleted'])->name('vehicles.documents.mark-completed');
        Route::post('vehicles/{vehicle}/documents/{document}/mark-incomplete', [App\Http\Controllers\VehicleDocumentController::class, 'markIncomplete'])->name('vehicles.documents.mark-incomplete');
        
        // Document Form Templates routes
        Route::get('document-templates', [App\Http\Controllers\DocumentFormTemplateController::class, 'index'])->name('document-templates.index');
        Route::get('document-templates/create', [App\Http\Controllers\DocumentFormTemplateController::class, 'create'])->name('document-templates.create');
        Route::post('document-templates', [App\Http\Controllers\DocumentFormTemplateController::class, 'store'])->name('document-templates.store');
        Route::get('document-templates/{template}/edit', [App\Http\Controllers\DocumentFormTemplateController::class, 'edit'])->name('document-templates.edit');
        Route::put('document-templates/{template}', [App\Http\Controllers\DocumentFormTemplateController::class, 'update'])->name('document-templates.update');
        Route::delete('document-templates/{template}', [App\Http\Controllers\DocumentFormTemplateController::class, 'destroy'])->name('document-templates.destroy');
        Route::get('document-templates/{template}', [App\Http\Controllers\VehicleDocumentController::class, 'getTemplate'])->name('document-templates.get');

        // Company documents: Online AR BOLO, AR Template (upload file or link)
        foreach (['online_ar_bolo' => 'online-ar-bolo', 'ar_template' => 'ar-template'] as $docType => $prefix) {
            Route::prefix($prefix)->name($prefix . '.')->group(function () use ($docType, $prefix) {
                Route::get('/', [App\Http\Controllers\CompanyDocumentController::class, 'index'])->defaults('document_type', $docType)->name('index');
                Route::get('create', [App\Http\Controllers\CompanyDocumentController::class, 'create'])->defaults('document_type', $docType)->name('create');
                Route::post('/', [App\Http\Controllers\CompanyDocumentController::class, 'store'])->defaults('document_type', $docType)->name('store');
                Route::get('{document}/edit', [App\Http\Controllers\CompanyDocumentController::class, 'edit'])->defaults('document_type', $docType)->name('edit');
                Route::put('{document}', [App\Http\Controllers\CompanyDocumentController::class, 'update'])->defaults('document_type', $docType)->name('update');
                Route::delete('{document}', [App\Http\Controllers\CompanyDocumentController::class, 'destroy'])->defaults('document_type', $docType)->name('destroy');
            });
        }

        // Agent BOLO: agents with basic info + files/links per agent
        Route::get('agent-bolo', [App\Http\Controllers\AgentBoloController::class, 'index'])->name('agent-bolo.index');
        Route::get('agent-bolo/create', [App\Http\Controllers\AgentBoloController::class, 'create'])->name('agent-bolo.create');
        Route::post('agent-bolo', [App\Http\Controllers\AgentBoloController::class, 'store'])->name('agent-bolo.store');
        Route::get('agent-bolo/{agent}', [App\Http\Controllers\AgentBoloController::class, 'show'])->name('agent-bolo.show');
        Route::get('agent-bolo/{agent}/edit', [App\Http\Controllers\AgentBoloController::class, 'edit'])->name('agent-bolo.edit');
        Route::put('agent-bolo/{agent}', [App\Http\Controllers\AgentBoloController::class, 'update'])->name('agent-bolo.update');
        Route::delete('agent-bolo/{agent}', [App\Http\Controllers\AgentBoloController::class, 'destroy'])->name('agent-bolo.destroy');
        Route::post('agent-bolo/{agent}/documents', [App\Http\Controllers\AgentBoloController::class, 'storeDocument'])->name('agent-bolo.documents.store');
        Route::delete('agent-bolo/{agent}/documents/{document}', [App\Http\Controllers\AgentBoloController::class, 'destroyDocument'])->name('agent-bolo.documents.destroy');

        Route::bind('memo', fn ($value) => \App\Models\CompanyDocument::where('type', \App\Models\CompanyDocument::TYPE_MEMO)->findOrFail($value));
        Route::resource('memos', App\Http\Controllers\MemoController::class)->except(['show']);

        // Sales Agents routes
        Route::get('/staff-reports/sales-agents', [App\Http\Controllers\SalesAgentController::class, 'staffReport'])->name('staff-reports.sales-agents');
        Route::get('/staff-reports/executive-agents', [App\Http\Controllers\ExecutiveAgentController::class, 'index'])->name('staff-reports.executive-agents');
        Route::post('/staff-reports/executive-agents', [App\Http\Controllers\ExecutiveAgentController::class, 'store'])->name('staff-reports.executive-agents.store');
        Route::resource('sales-agents', App\Http\Controllers\SalesAgentController::class);

        // Employees routes
        Route::resource('employees', App\Http\Controllers\EmployeeController::class);

        // SOA routes
        Route::get('/soa/create', [App\Http\Controllers\SOAController::class, 'create'])->name('soa.create');
        Route::post('/soa', [App\Http\Controllers\SOAController::class, 'store'])->name('soa.store');
        Route::get('/api/soa/transactions', [App\Http\Controllers\SOAController::class, 'getTransactions'])->name('soa.transactions');
        Route::post('/api/soa/daily-budget', [App\Http\Controllers\SOAController::class, 'storeDailyBudget'])->name('soa.daily-budget.store');
        Route::post('/api/soa/add-cash', [App\Http\Controllers\SOAController::class, 'addCash'])->name('soa.add-cash');
        Route::get('/api/soa/cash-additions', [App\Http\Controllers\SOAController::class, 'getAllCashAdditions'])->name('soa.get-all-cash-additions');
        Route::put('/api/soa/cash/{id}', [App\Http\Controllers\SOAController::class, 'updateCash'])->name('soa.update-cash');
        Route::delete('/api/soa/cash/{id}', [App\Http\Controllers\SOAController::class, 'deleteCash'])->name('soa.delete-cash');
        Route::put('/api/soa/update-starting-cash', [App\Http\Controllers\SOAController::class, 'updateStartingCash'])->name('soa.update-starting-cash');
        Route::post('/api/soa/manual-entries', [App\Http\Controllers\SOAController::class, 'storeSoaManualEntry'])->name('soa.manual-entries.store');
        Route::put('/api/soa/manual-entries/{soa_manual_entry}', [App\Http\Controllers\SOAController::class, 'updateSoaManualEntry'])->name('soa.manual-entries.update');
        Route::delete('/api/soa/manual-entries/{soa_manual_entry}', [App\Http\Controllers\SOAController::class, 'destroySoaManualEntry'])->name('soa.manual-entries.destroy');
        Route::delete('/api/soa/daily-record', [App\Http\Controllers\SOAController::class, 'destroySoaForDate'])->name('soa.daily-record.destroy');
        Route::get('/api/soa/floated-funds', [App\Http\Controllers\SOAController::class, 'getFloatedFunds'])->name('soa.floated-funds');
});