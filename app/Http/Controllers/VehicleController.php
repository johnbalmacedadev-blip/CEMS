<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleIncentive;
use App\Models\CarFinancingSetting;
use App\Models\FinancingScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Traits\LogsActivity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;

class VehicleController extends Controller
{
    use LogsActivity;
    /**
     * Display the pricelist (vehicles with purchase, posted, and sold prices).
     */
    public function pricelist(Request $request)
    {
        // Normalize default filters out of the URL (e.g. status=all, page=1)
        $originalQuery = $request->query();
        $cleanQuery = $originalQuery;
        if (($cleanQuery['status'] ?? null) === 'all') unset($cleanQuery['status']);
        if (isset($cleanQuery['page']) && (int) $cleanQuery['page'] === 1) unset($cleanQuery['page']);
        if (isset($cleanQuery['search']) && trim((string) $cleanQuery['search']) === '') unset($cleanQuery['search']);
        if (isset($cleanQuery['year_from']) && trim((string) $cleanQuery['year_from']) === '') unset($cleanQuery['year_from']);
        if (isset($cleanQuery['year_to']) && trim((string) $cleanQuery['year_to']) === '') unset($cleanQuery['year_to']);
        if ($cleanQuery !== $originalQuery) {
            return redirect()->route('pricelist', $cleanQuery);
        }

        $status = $request->get('status', 'all');
        $search = $request->get('search');
        $yearFrom = $request->get('year_from');
        $yearTo = $request->get('year_to');

        $query = Vehicle::with(['make', 'vehicleModel', 'forfeitDetails']);
        $query->forUnitReportStatus($status);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('make', 'LIKE', "%{$search}%")
                    ->orWhere('model', 'LIKE', "%{$search}%")
                    ->orWhere('plate_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('make', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('vehicleModel', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"));
            });
        }

        if ($yearFrom !== null && $yearFrom !== '') {
            $query->where('year', '>=', (int) $yearFrom);
        }
        if ($yearTo !== null && $yearTo !== '') {
            $query->where('year', '<=', (int) $yearTo);
        }

        $vehicles = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $financingSettings = CarFinancingSetting::with('financingScheme')->orderBy('year_model_range')->get()->sortBy(function ($s) {
            return ($s->financingScheme->sort_order ?? 99) . ' ' . ($s->financingScheme->name ?? '') . ' ' . $s->year_model_range;
        })->values();

        return view('pricelist.index', compact('vehicles', 'status', 'search', 'yearFrom', 'yearTo', 'financingSettings'));
    }

    public function exportPricelistPdf(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        $yearFrom = $request->get('year_from');
        $yearTo = $request->get('year_to');

        $query = Vehicle::with(['make', 'vehicleModel', 'forfeitDetails']);
        $query->forUnitReportStatus($status);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('make', 'LIKE', "%{$search}%")
                    ->orWhere('model', 'LIKE', "%{$search}%")
                    ->orWhere('plate_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('make', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('vehicleModel', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"));
            });
        }

        if ($yearFrom !== null && $yearFrom !== '') {
            $query->where('year', '>=', (int) $yearFrom);
        }
        if ($yearTo !== null && $yearTo !== '') {
            $query->where('year', '<=', (int) $yearTo);
        }

        $vehicles = $query->orderBy('created_at', 'desc')->get();

        $filters = [
            'status' => $status,
            'search' => $search,
            'year_from' => $yearFrom,
            'year_to' => $yearTo,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pricelist.export-pdf', compact('vehicles', 'filters'))
            ->setPaper('a4', 'landscape');

        $filename = 'pricelist-' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Store pricelist financing details (Option 1 & Option 2) using selected year model variables.
     */
    public function storePricelistFinancing(Request $request, Vehicle $vehicle)
    {
        $request->validate(['year_model_setting_id' => 'required|exists:car_financing_settings,id']);

        $setting = CarFinancingSetting::findOrFail($request->year_model_setting_id);
        $price = (float) ($vehicle->posted_price ?? 0);
        if ($price <= 0) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Vehicle has no posted price.'], 422);
            }
            return back()->with('error', 'Vehicle has no posted price.');
        }

        // Option 1: Low Down Payment (20% DP)
        $dp1 = round($price * 0.20, 2);
        $af1 = $setting->amountFinanced($price, $dp1);
        $allIn1 = $setting->allInDownPayment($dp1, $af1);
        $vehicle->option1_cash_out = $allIn1;
        $vehicle->option1_12mos = $setting->monthlyPayment($af1, 12);
        $vehicle->option1_24mos = $setting->monthlyPayment($af1, 24);
        $vehicle->option1_36mos = $setting->monthlyPayment($af1, 36);
        $vehicle->option1_48mos = $setting->monthlyPayment($af1, 48);

        // Option 2: Low Monthly Payment (40% DP)
        $dp2 = round($price * 0.40, 2);
        $af2 = $setting->amountFinanced($price, $dp2);
        $allIn2 = $setting->allInDownPayment($dp2, $af2);
        $vehicle->option2_cash_out = $allIn2;
        $vehicle->option2_12mos = $setting->monthlyPayment($af2, 12);
        $vehicle->option2_24mos = $setting->monthlyPayment($af2, 24);
        $vehicle->option2_36mos = $setting->monthlyPayment($af2, 36);
        $vehicle->option2_48mos = $setting->monthlyPayment($af2, 48);

        $vehicle->save();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Financing details saved.', 'vehicle_id' => $vehicle->id]);
        }
        return back()->with('success', 'Financing details saved.');
    }

    /**
     * Store pricelist financing details for multiple vehicles (bulk).
     */
    public function storePricelistFinancingBulk(Request $request)
    {
        $request->validate([
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'year_model_setting_id' => 'required|exists:car_financing_settings,id',
        ]);

        $setting = CarFinancingSetting::findOrFail($request->year_model_setting_id);
        $vehicleIds = array_unique($request->vehicle_ids);
        $updated = 0;
        $skipped = 0;

        foreach ($vehicleIds as $id) {
            $vehicle = Vehicle::find($id);
            if (!$vehicle) continue;
            $price = (float) ($vehicle->posted_price ?? 0);
            if ($price <= 0) {
                $skipped++;
                continue;
            }
            $dp1 = round($price * 0.20, 2);
            $af1 = $setting->amountFinanced($price, $dp1);
            $allIn1 = $setting->allInDownPayment($dp1, $af1);
            $vehicle->option1_cash_out = $allIn1;
            $vehicle->option1_12mos = $setting->monthlyPayment($af1, 12);
            $vehicle->option1_24mos = $setting->monthlyPayment($af1, 24);
            $vehicle->option1_36mos = $setting->monthlyPayment($af1, 36);
            $vehicle->option1_48mos = $setting->monthlyPayment($af1, 48);
            $dp2 = round($price * 0.40, 2);
            $af2 = $setting->amountFinanced($price, $dp2);
            $allIn2 = $setting->allInDownPayment($dp2, $af2);
            $vehicle->option2_cash_out = $allIn2;
            $vehicle->option2_12mos = $setting->monthlyPayment($af2, 12);
            $vehicle->option2_24mos = $setting->monthlyPayment($af2, 24);
            $vehicle->option2_36mos = $setting->monthlyPayment($af2, 36);
            $vehicle->option2_48mos = $setting->monthlyPayment($af2, 48);
            $vehicle->save();
            $updated++;
        }

        $message = $updated > 0
            ? "Financing details saved for {$updated} vehicle(s)." . ($skipped > 0 ? " {$skipped} skipped (no posted price)." : '')
            : 'No vehicles updated.';

        if ($request->wantsJson()) {
            return response()->json(['message' => $message, 'updated' => $updated, 'skipped' => $skipped]);
        }
        return back()->with('success', $message);
    }

    /**
     * Update OPTION 1 and OPTION 2 for all vehicles with a posted price, using each vehicle's YEAR to match a financing year range.
     */
    public function updateAllPricelistFinancing(Request $request)
    {
        $defaultSchemeId = FinancingScheme::orderBy('sort_order')->orderBy('name')->value('id');
        $vehicles = Vehicle::whereNotNull('posted_price')->where('posted_price', '>', 0)->get();
        $updated = 0;
        $skipped = 0;
        $noMatch = 0;

        foreach ($vehicles as $vehicle) {
            $price = (float) $vehicle->posted_price;
            $year = (int) ($vehicle->year ?? 0);
            if ($year <= 0) {
                $skipped++;
                continue;
            }
            $setting = CarFinancingSetting::findForYear($year, $defaultSchemeId);
            if (!$setting) {
                $noMatch++;
                continue;
            }
            $dp1 = round($price * 0.20, 2);
            $af1 = $setting->amountFinanced($price, $dp1);
            $allIn1 = $setting->allInDownPayment($dp1, $af1);
            $vehicle->option1_cash_out = $allIn1;
            $vehicle->option1_12mos = $setting->monthlyPayment($af1, 12);
            $vehicle->option1_24mos = $setting->monthlyPayment($af1, 24);
            $vehicle->option1_36mos = $setting->monthlyPayment($af1, 36);
            $vehicle->option1_48mos = $setting->monthlyPayment($af1, 48);
            $dp2 = round($price * 0.40, 2);
            $af2 = $setting->amountFinanced($price, $dp2);
            $allIn2 = $setting->allInDownPayment($dp2, $af2);
            $vehicle->option2_cash_out = $allIn2;
            $vehicle->option2_12mos = $setting->monthlyPayment($af2, 12);
            $vehicle->option2_24mos = $setting->monthlyPayment($af2, 24);
            $vehicle->option2_36mos = $setting->monthlyPayment($af2, 36);
            $vehicle->option2_48mos = $setting->monthlyPayment($af2, 48);
            $vehicle->save();
            $updated++;
        }

        $parts = ["{$updated} vehicle(s) updated."];
        if ($noMatch > 0) $parts[] = "{$noMatch} had no matching year range.";
        if ($skipped > 0) $parts[] = "{$skipped} skipped (no year).";
        $message = implode(' ', $parts);

        return redirect()->route('pricelist')->with('success', $message);
    }

    /**
     * Set posted price for all vehicles to 10% above purchase price.
     */
    public function setPostedPrice10Percent(Request $request)
    {
        $vehicles = Vehicle::whereNotNull('purchase_price')->where('purchase_price', '>', 0)->get();
        $updated = 0;
        foreach ($vehicles as $vehicle) {
            $purchase = (float) $vehicle->purchase_price;
            $vehicle->posted_price = round($purchase * 1.10, 2);
            $vehicle->save();
            $updated++;
        }
        $message = $updated > 0
            ? "Posted price set to purchase + 10% for {$updated} vehicle(s)."
            : 'No vehicles with a purchase price found.';
        return redirect()->route('pricelist')->with('success', $message);
    }

    /**
     * Filters for Unit Report (vehicles index) — same logic for list and export.
     */
    protected function vehiclesIndexBaseQuery(Request $request): Builder
    {
        $status = $request->get('status', 'Available');
        $search = $request->get('search');
        $yearFrom = $request->get('year_from');
        $yearTo = $request->get('year_to');
        $transmission = $request->get('transmission');
        $fuelType = $request->get('fuel_type');
        $bodyType = $request->get('body_type');
        $purchasedFrom = $request->get('purchased_from');
        $reservationDate = $request->get('reservation_date');

        $query = Vehicle::with(['make', 'vehicleModel', 'primaryImage', 'forfeitDetails']);
        $query->forUnitReportStatus($status);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('make', 'LIKE', "%{$search}%")
                    ->orWhere('model', 'LIKE', "%{$search}%")
                    ->orWhere('variant', 'LIKE', "%{$search}%")
                    ->orWhere('plate_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('make', function ($makeQuery) use ($search) {
                        $makeQuery->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('vehicleModel', function ($modelQuery) use ($search) {
                        $modelQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($yearFrom !== null && $yearFrom !== '') {
            $query->where('year', '>=', (int) $yearFrom);
        }
        if ($yearTo !== null && $yearTo !== '') {
            $query->where('year', '<=', (int) $yearTo);
        }

        if (is_string($transmission) && $transmission !== '' && in_array($transmission, ['Manual', 'Automatic'], true)) {
            $query->where('transmission', $transmission);
        }

        if (is_string($fuelType) && $fuelType !== '' && in_array($fuelType, ['Diesel', 'Gasoline', 'Hybrid', 'Electric'], true)) {
            $query->where('fuel_type', $fuelType);
        }

        if (is_string($bodyType) && trim($bodyType) !== '') {
            $query->where('body_type', 'like', '%' . trim($bodyType) . '%');
        }

        if (is_string($purchasedFrom) && trim($purchasedFrom) !== '') {
            $query->where('purchased_from', 'like', '%' . trim($purchasedFrom) . '%');
        }

        if (is_string($reservationDate) && trim($reservationDate) !== '') {
            $query->whereHas('statusDetail', function ($q) use ($reservationDate) {
                $q->whereDate('sale_date', $reservationDate);
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Export Unit Report (CSV for Excel, or PDF) using current filters.
     */
    public function exportIndex(Request $request)
    {
        $format = strtolower((string) $request->get('format', 'csv'));
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(422, 'Invalid export format.');
        }

        $vehicles = $this->vehiclesIndexBaseQuery($request)->get();
        $baseName = 'unit-report-vehicles-' . date('Y-m-d');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('vehicles.export-index-pdf', compact('vehicles'))
                ->setPaper('a4', 'landscape');

            return $pdf->download($baseName . '.pdf');
        }

        return $this->streamVehiclesIndexCsv($vehicles, $baseName);
    }

    protected function streamVehiclesIndexCsv($vehicles, string $baseName)
    {
        $filename = $baseName . '.csv';

        return response()->streamDownload(function () use ($vehicles) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'Year',
                'Make',
                'Model',
                'Variant',
                'Body Type',
                'Plate',
                'Colour',
                'Transmission',
                'Fuel',
                'Kilometers',
                'Purchase Price',
                'Posted Price',
                'Sold Price',
                'Status',
                'Purchase Date',
                'Purchased From',
            ]);
            foreach ($vehicles as $v) {
                $displayStatus = ($v->status === 'Forfeited' || $v->forfeitDetails->count() > 0)
                    ? 'Forfeited'
                    : (string) $v->status;
                $makeLabel = is_object($v->make) && isset($v->make->name)
                    ? $v->make->name
                    : (is_string($v->make) ? $v->make : (string) ($v->getAttributes()['make'] ?? ''));
                $modelLabel = is_object($v->vehicleModel) && isset($v->vehicleModel->name)
                    ? $v->vehicleModel->name
                    : (is_string($v->model) ? $v->model : (string) ($v->getAttributes()['model'] ?? ''));
                fputcsv($out, [
                    $v->year,
                    $makeLabel,
                    $modelLabel,
                    $v->variant,
                    $v->body_type,
                    $v->plate_number,
                    $v->colour,
                    $v->transmission,
                    $v->fuel_type,
                    $v->kilometers,
                    $v->purchase_price !== null ? number_format((float) $v->purchase_price, 2, '.', '') : '',
                    $v->posted_price !== null ? number_format((float) $v->posted_price, 2, '.', '') : '',
                    $v->sold_price !== null ? number_format((float) $v->sold_price, 2, '.', '') : '',
                    $displayStatus,
                    $v->purchase_date ? $v->purchase_date->format('Y-m-d') : '',
                    $v->purchased_from,
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'Available');
        $search = $request->get('search');
        $yearFrom = $request->get('year_from');
        $yearTo = $request->get('year_to');
        $transmission = $request->get('transmission');
        $fuelType = $request->get('fuel_type');
        $bodyType = $request->get('body_type');
        $purchasedFrom = $request->get('purchased_from');
        $reservationDate = $request->get('reservation_date');

        // Sync status to Forfeited for any vehicle that has forfeit details (skip archived)
        Vehicle::whereHas('forfeitDetails')
            ->whereNotIn('status', ['Forfeited', 'Archived'])
            ->update(['status' => 'Forfeited']);

        $vehicles = $this->vehiclesIndexBaseQuery($request)->paginate(10)->withQueryString();

        // Get counts for each status
        $availableCount = Vehicle::where('status', 'Available')->count();
        $reservedCount = Vehicle::where('status', 'Reserved')->count();
        $releasedCount = Vehicle::where('status', 'Released')->count();
        $underMaintenanceCount = Vehicle::where('status', 'Under Maintenance')->count();
        $forfeitedCount = Vehicle::where('status', '!=', 'Archived')
            ->where(function ($q) {
                $q->where('status', 'Forfeited')->orWhereHas('forfeitDetails');
            })
            ->count();

        return view('vehicles.index', compact(
            'vehicles',
            'status',
            'search',
            'yearFrom',
            'yearTo',
            'transmission',
            'fuelType',
            'bodyType',
            'purchasedFrom',
            'reservationDate',
            'availableCount',
            'reservedCount',
            'releasedCount',
            'underMaintenanceCount',
            'forfeitedCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'make_id' => 'required|exists:makes,id',
            'model_id' => 'required|exists:models,id',
            'variant' => 'nullable|string|max:255',
            'body_type' => 'nullable|string|max:255',
            'transmission' => 'required|in:Manual,Automatic',
            'fuel_type' => 'required|in:Diesel,Gasoline,Hybrid,Electric',
            'kilometers' => 'required|integer|min:0',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'colour' => 'required|string|max:255',
            'with_tools' => 'boolean',
            'with_matting' => 'boolean',
            'with_spare_tire' => 'boolean',
            'purchase_price' => 'required|numeric|min:0',
            'purchased_from' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'spare_key' => 'boolean',
            'notes' => 'nullable|string',
                'status' => 'required|in:Available,Under Maintenance,Reserved,Released,Forfeited',
        ]);

        // Get the make and model names for backward compatibility
        $make = \App\Models\Make::find($request->make_id);
        $model = \App\Models\VehicleModel::find($request->model_id);
        
        $vehicleData = $request->all();
        $vehicleData['make'] = $make ? $make->name : '';
        $vehicleData['model'] = $model ? $model->name : '';
        
        $vehicle = Vehicle::create($vehicleData);

        // Log activity
        $this->logCreate($vehicle);

        // Clear only application cache after creating vehicle
        Cache::flush();

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehicle added successfully! You can now continue adding other details.');
    }

    /**
     * Display the specified resource.
     */
        public function show(Vehicle $vehicle)
        {
            $vehicle->load([
                'make', 
                'vehicleModel', 
                'images', 
                'expense', 
                'statusDetail', 
                'incentive',
                'gasExpenses', 
                'customSections.fields', 
                'customFields',
                'expenseItems.expenseTransaction',
                'expenseItems.receipts',
                'acquisitionDocuments.files',
                'reservationDocuments.files', // Added for reservation documents with files
                'releaseDocuments.files', // Added for release documents with files
                'ads', // Added for vehicle ads
                'forfeitDetails',
                'followUpDocuments',
                'salesAgentCommissions',
                'transferOrcrs',
                'videoPostingRecords',
                'buffingRecords.employee'
            ]);
            
            // Load all expense items for this vehicle, including from transactions with mixed items
            // Get all transaction IDs that have at least one item for this vehicle
            $transactionIds = \App\Models\ExpenseItem::where('vehicle_id', $vehicle->id)
                ->where('payment_tag', 'Vehicle')
                ->pluck('expense_transaction_id')
                ->unique();
            
            // Get all items from those transactions that are for this vehicle
            $allExpenseItems = \App\Models\ExpenseItem::whereIn('expense_transaction_id', $transactionIds)
                ->where('vehicle_id', $vehicle->id)
                ->where('payment_tag', 'Vehicle')
                ->with(['expenseTransaction', 'receipts'])
                ->orderBy('expense_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Override the expenseItems collection with all items
            $vehicle->setRelation('expenseItems', $allExpenseItems);
            
            // Sync vehicle status with sale_status in statusDetail
            if ($vehicle->statusDetail) {
                // If sale_status doesn't match vehicle status, update sale_status to match vehicle status
                if ($vehicle->statusDetail->sale_status !== $vehicle->status) {
                    $vehicle->statusDetail->update(['sale_status' => $vehicle->status]);
                    $vehicle->load('statusDetail'); // Reload to get updated data
                }
            } else {
                // If statusDetail doesn't exist but vehicle has a status, create it
                if ($vehicle->status) {
                    \App\Models\VehicleStatusDetail::create([
                        'plate_number' => $vehicle->plate_number,
                        'sale_status' => $vehicle->status
                    ]);
                    $vehicle->load('statusDetail'); // Reload to get the new statusDetail
                }
            }
            
            $showrooms = \App\Models\Showroom::active()->orderBy('name')->get();

            $executiveAgents = \App\Models\ExecutiveAgent::query()
                ->orderByRaw("CASE WHEN LOWER(status) = 'active' THEN 0 ELSE 1 END")
                ->orderBy('name')
                ->get(['id', 'name', 'executive_code', 'status']);

            $salesAgentsList = \App\Models\SalesAgent::query()
                ->orderByRaw("CASE WHEN LOWER(status) = 'active' THEN 0 ELSE 1 END")
                ->orderBy('name')
                ->get(['id', 'name', 'sales_agent_id', 'status']);

            return view('vehicles.show', compact('vehicle', 'showrooms', 'executiveAgents', 'salesAgentsList'));
        }

    /**
     * Export vehicle details as PDF or CSV (Excel).
     */
    public function export(Request $request, Vehicle $vehicle)
    {
        $vehicle->load([
            'make', 'vehicleModel', 'statusDetail', 'gasExpenses',
            'expenseItems.expenseTransaction', 'expenseItems.receipts',
            'transferOrcrs', 'videoPostingRecords', 'buffingRecords.employee',
            'salesAgentCommissions', 'followUpDocuments', 'ads', 'forfeitDetails',
            'acquisitionDocuments', 'reservationDocuments', 'releaseDocuments',
        ]);
        $transactionIds = \App\Models\ExpenseItem::where('vehicle_id', $vehicle->id)
            ->where('payment_tag', 'Vehicle')
            ->pluck('expense_transaction_id')->unique();
        $allExpenseItems = \App\Models\ExpenseItem::whereIn('expense_transaction_id', $transactionIds)
            ->where('vehicle_id', $vehicle->id)
            ->where('payment_tag', 'Vehicle')
            ->with(['expenseTransaction', 'receipts'])
            ->orderBy('expense_date', 'desc')->orderBy('created_at', 'desc')
            ->get();
        $vehicle->setRelation('expenseItems', $allExpenseItems);

        $format = strtolower($request->get('format', 'pdf'));

        if ($format === 'csv' || $format === 'excel') {
            return $this->exportVehicleCsv($vehicle);
        }

        return $this->exportVehiclePdf($vehicle);
    }

    protected function exportVehicleCsv(Vehicle $vehicle): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'vehicle-' . ($vehicle->plate_number ?? $vehicle->id) . '-' . date('Y-m-d') . '.csv';
        $makeName = is_object($vehicle->make) ? $vehicle->make->name : ($vehicle->make ?? '');
        $modelName = is_object($vehicle->vehicleModel) ? $vehicle->vehicleModel->name : ($vehicle->model ?? '');

        return response()->streamDownload(function () use ($vehicle, $makeName, $modelName) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Vehicle Information']);
            fputcsv($out, ['Year', 'Make', 'Model', 'Plate', 'Variant', 'Status', 'Purchase Price', 'Purchase Date', 'Posted Price', 'Sold Price']);
            fputcsv($out, [
                $vehicle->year ?? '',
                $makeName,
                $modelName,
                $vehicle->plate_number ?? '',
                $vehicle->variant ?? '',
                $vehicle->status ?? '',
                $vehicle->purchase_price ?? '',
                $vehicle->purchase_date ? $vehicle->purchase_date->format('Y-m-d') : '',
                $vehicle->posted_price ?? '',
                $vehicle->sold_price ?? '',
            ]);
            fputcsv($out, []);
            fputcsv($out, ['Expense Items']);
            fputcsv($out, ['Date', 'Description', 'Category', 'Cost']);
            foreach ($vehicle->expenseItems as $item) {
                fputcsv($out, [
                    $item->expense_date ? $item->expense_date->format('Y-m-d') : '',
                    $item->description ?? '',
                    $item->expense_category ?? '',
                    $item->cost ?? '',
                ]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Transfer OR/CR']);
            fputcsv($out, ['Date', 'Transaction Type', 'Release Date', 'LTO File', 'Status']);
            foreach ($vehicle->transferOrcrs as $r) {
                fputcsv($out, [
                    $r->date ? $r->date->format('Y-m-d') : '',
                    $r->transaction_type ?? '',
                    $r->release_date ? $r->release_date->format('Y-m-d') : '',
                    $r->lto_file_no ?? '',
                    $r->status ?? '',
                ]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Sales Agent Commissions']);
            fputcsv($out, ['Agent', 'Client', 'Type', 'Amount', 'Date Sent']);
            foreach ($vehicle->salesAgentCommissions as $c) {
                fputcsv($out, [
                    $c->agent_name ?? '',
                    $c->client_name ?? '',
                    $c->transaction_type ?? '',
                    $c->amount ?? '',
                    $c->date_sent ? $c->date_sent->format('Y-m-d') : '',
                ]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Buffing Records']);
            fputcsv($out, ['Date', 'Staff', 'Status', 'Notes']);
            foreach ($vehicle->buffingRecords as $b) {
                fputcsv($out, [
                    $b->buffing_date ? $b->buffing_date->format('Y-m-d') : '',
                    $b->employee ? $b->employee->full_name : '',
                    $b->status ?? '',
                    Str::limit($b->notes ?? '', 100),
                ]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Video/Posting Records']);
            fputcsv($out, ['Date', 'Title', 'Type', 'Platform', 'Status']);
            foreach ($vehicle->videoPostingRecords as $v) {
                fputcsv($out, [
                    $v->record_date ? $v->record_date->format('Y-m-d') : '',
                    $v->title ?? '',
                    $v->type ?? '',
                    $v->platform ?? '',
                    $v->status ?? '',
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function exportVehiclePdf(Vehicle $vehicle)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('vehicles.export-pdf', compact('vehicle'))
            ->setPaper('a4', 'portrait');
        $filename = 'vehicle-' . ($vehicle->plate_number ?? $vehicle->id) . '-' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $vehicle->load(['make', 'vehicleModel']);
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'make_id' => 'required|exists:makes,id',
            'model_id' => 'required|exists:models,id',
            'variant' => 'nullable|string|max:255',
            'body_type' => 'nullable|string|max:255',
            'transmission' => 'required|in:Manual,Automatic',
            'fuel_type' => 'required|in:Diesel,Gasoline,Hybrid,Electric',
            'kilometers' => 'required|integer|min:0',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'colour' => 'required|string|max:255',
            'with_tools' => 'boolean',
            'with_matting' => 'boolean',
            'with_spare_tire' => 'boolean',
            'purchase_price' => 'required|numeric|min:0',
            'purchased_from' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'spare_key' => 'boolean',
            'notes' => 'nullable|string',
            'status' => 'required|in:Available,Under Maintenance,Reserved,Released,Forfeited',
            'option1_cash_out' => 'nullable|numeric|min:0',
            'option1_12mos' => 'nullable|numeric|min:0',
            'option1_24mos' => 'nullable|numeric|min:0',
            'option1_36mos' => 'nullable|numeric|min:0',
            'option1_48mos' => 'nullable|numeric|min:0',
            'option2_cash_out' => 'nullable|numeric|min:0',
            'option2_12mos' => 'nullable|numeric|min:0',
            'option2_24mos' => 'nullable|numeric|min:0',
            'option2_36mos' => 'nullable|numeric|min:0',
            'option2_48mos' => 'nullable|numeric|min:0',
        ]);

        // Get the make and model names for backward compatibility
        $make = \App\Models\Make::find($request->make_id);
        $model = \App\Models\VehicleModel::find($request->model_id);
        
        // Get original values for logging
        $original = $vehicle->getOriginal();
        
        $vehicleData = $request->all();
        $vehicleData['make'] = $make ? $make->name : '';
        $vehicleData['model'] = $model ? $model->name : '';
        
        $vehicle->update($vehicleData);
        
        // Track changes for logging
        $changes = [];
        foreach ($vehicleData as $key => $value) {
            if (isset($original[$key]) && $original[$key] != $value) {
                $changes[$key] = [
                    'old' => $original[$key],
                    'new' => $value
                ];
            }
        }
        
        // Log activity
        $this->logUpdate($vehicle, !empty($changes) ? $changes : null);
        
        // Sync statusDetail sale_status with vehicle status
        if (isset($vehicleData['status'])) {
            \App\Models\VehicleStatusDetail::updateOrCreate(
                ['plate_number' => $vehicle->plate_number],
                ['sale_status' => $vehicleData['status']]
            );
        }

        // Clear only application cache after updating vehicle
        Cache::flush();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle updated successfully!');
    }

    /**
     * Search vehicles that can be moved to Archived (for the Archived tab modal).
     *
     * Returns units in Available, Released, or Forfeited status. Excludes already-archived vehicles.
     *
     * @group Unit Report
     * @authenticated
     *
     * @queryParam q string optional Search by plate, make, model, or variant. Example: Toyota
     *
     * @response 200 [{"id":1,"plate_number":"ABC 1234","label":"2020 Toyota Vios (ABC 1234)","status":"Available","archive_url":"http://localhost/vehicles/1/archive"}]
     */
    public function searchArchiveable(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        $vehicles = Vehicle::with('forfeitDetails')
            ->archiveable()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('plate_number', 'LIKE', "%{$search}%")
                        ->orWhere('make', 'LIKE', "%{$search}%")
                        ->orWhere('model', 'LIKE', "%{$search}%")
                        ->orWhere('variant', 'LIKE', "%{$search}%")
                        ->orWhereHas('make', fn ($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                        ->orWhereHas('vehicleModel', fn ($mq) => $mq->where('name', 'LIKE', "%{$search}%"));
                });
            })
            ->orderByDesc('year')
            ->orderBy('make')
            ->limit(25)
            ->get(['id', 'plate_number', 'make', 'model', 'year', 'status']);

        return response()->json($vehicles->map(function (Vehicle $vehicle) {
            $displayStatus = ($vehicle->status === 'Forfeited' || $vehicle->forfeitDetails->isNotEmpty())
                ? 'Forfeited'
                : $vehicle->status;

            return [
                'id' => $vehicle->id,
                'plate_number' => $vehicle->plate_number,
                'label' => trim("{$vehicle->year} {$vehicle->make} {$vehicle->model}") . " ({$vehicle->plate_number})",
                'status' => $displayStatus,
                'archive_url' => route('vehicles.archive', $vehicle),
            ];
        }));
    }

    /**
     * Move a unit to Archived (Available, Released, or Forfeited only).
     */
    /**
     * Archive a vehicle
     *
     * Moves an Available, Released, or Forfeited unit to Archived status.
     *
     * @group Unit Report
     * @authenticated
     *
     * @urlParam vehicle integer required The vehicle ID. Example: 1
     *
     * @response 200 scenario="JSON request" {"success":true,"message":"Vehicle moved to Archived successfully.","swal_title":"Archived","vehicle_id":1}
     * @response 422 scenario="Not archiveable" {"success":false,"message":"This vehicle cannot be archived.","swal_title":"Cannot Archive"}
     */
    public function archive(Request $request, Vehicle $vehicle)
    {
        if (! $vehicle->isArchiveable()) {
            $message = 'This vehicle cannot be archived. Only Available, Released, or Forfeited units can be archived.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'swal_title' => 'Cannot Archive',
                ], 422);
            }

            return redirect()->back()
                ->with('error', $message)
                ->with('swal_title', 'Cannot Archive');
        }

        $previousStatus = $vehicle->status;

        $vehicle->update([
            'status_before_archive' => $previousStatus,
            'archived_at' => now(),
            'status' => 'Archived',
        ]);

        if ($vehicle->statusDetail) {
            $vehicle->statusDetail->update(['sale_status' => 'Archived']);
        }

        $this->logUpdate($vehicle, [
            'status' => ['from' => $previousStatus, 'to' => 'Archived'],
        ]);

        Cache::flush();

        $vehicle->load(['primaryImage', 'forfeitDetails']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle moved to Archived successfully.',
                'swal_title' => 'Archived',
                'vehicle_id' => $vehicle->id,
                'vehicle' => [
                    'id' => $vehicle->id,
                    'show_url' => route('vehicles.show', $vehicle),
                    'full_name' => $vehicle->full_name,
                    'year' => $vehicle->year,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'plate_number' => $vehicle->plate_number,
                    'colour' => $vehicle->colour,
                    'purchase_price' => $vehicle->formatted_purchase_price,
                    'transmission' => $vehicle->transmission,
                    'fuel_type' => $vehicle->fuel_type,
                    'archived_at' => $vehicle->archived_at?->format('M d, Y'),
                    'thumbnail_url' => $vehicle->primaryImage?->thumbnail_url,
                ],
            ]);
        }

        return redirect()->route('vehicles.index', ['status' => 'Archived'])
            ->with('success', 'Vehicle moved to Archived successfully.')
            ->with('swal_title', 'Archived');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Log activity before deleting
        $this->logDelete($vehicle);
        
        $vehicle->delete();

        // Clear only application cache after deleting vehicle
        Cache::flush();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle deleted successfully!');
    }

    /**
     * Update incentive details for a vehicle.
     */
    public function updateIncentive(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'sa_origin' => 'nullable|string|max:255',
            'sa_origin_link' => 'nullable|string|max:500',
            'sa_origin_file' => 'nullable|file|image|max:5120',
            'reserved_by' => 'nullable|string|max:255',
            'no_look' => 'nullable|boolean',
            'no_look_link' => 'nullable|string|max:500',
            'no_look_file' => 'nullable|file|image|max:5120',
            'insurance' => 'nullable|boolean',
            'insurance_link' => 'nullable|string|max:500',
            'insurance_file' => 'nullable|file|image|max:5120',
            'testimonial' => 'nullable|boolean',
            'testimonial_link' => 'nullable|string|max:500',
            'testimonial_file' => 'nullable|file|image|max:5120',
            'review' => 'nullable|boolean',
            'review_link' => 'nullable|string|max:500',
            'review_file' => 'nullable|file|image|max:5120',
        ]);

        $data = [
            'sa_origin' => $request->sa_origin,
            'sa_origin_link' => $request->sa_origin_link,
            'reserved_by' => $request->reserved_by,
            'no_look' => (bool) $request->boolean('no_look'),
            'no_look_link' => $request->no_look_link,
            'insurance' => (bool) $request->boolean('insurance'),
            'insurance_link' => $request->insurance_link,
            'testimonial' => (bool) $request->boolean('testimonial'),
            'testimonial_link' => $request->testimonial_link,
            'review' => (bool) $request->boolean('review'),
            'review_link' => $request->review_link,
        ];

        $disk = 'public';
        $basePath = 'vehicle-incentives/' . $vehicle->id;

        $fileFields = [
            'sa_origin_file' => 'sa_origin_file_path',
            'no_look_file' => 'no_look_file_path',
            'insurance_file' => 'insurance_file_path',
            'testimonial_file' => 'testimonial_file_path',
            'review_file' => 'review_file_path',
        ];

        $incentive = $vehicle->incentive;

        foreach ($fileFields as $inputKey => $dbKey) {
            if ($request->hasFile($inputKey)) {
                $file = $request->file($inputKey);
                $path = $file->store($basePath, $disk);
                $data[$dbKey] = $path;
            }
        }

        if ($incentive) {
            $incentive->update($data);
        } else {
            $data['vehicle_id'] = $vehicle->id;
            VehicleIncentive::create($data);
        }

        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Incentive details saved successfully!',
        ]);
    }

    /**
     * Update posted price for a vehicle.
     */
    public function updatePostedPrice(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'posted_price' => 'nullable|numeric|min:0',
        ]);

        // Get original values for logging
        $original = $vehicle->getOriginal();

        $vehicle->update([
            'posted_price' => $request->posted_price ?: null,
        ]);

        // Track changes for logging
        $changes = [];
        if (isset($original['posted_price']) && $original['posted_price'] != $vehicle->posted_price) {
            $changes['posted_price'] = [
                'old' => $original['posted_price'],
                'new' => $vehicle->posted_price
            ];
        }

        // Log activity
        $this->logUpdate($vehicle, !empty($changes) ? $changes : null);

        return response()->json([
            'success' => true,
            'message' => 'Posted price updated successfully!',
            'vehicle' => $vehicle->fresh()
        ]);
    }

    /**
     * Delete/clear posted price for a vehicle.
     */
    public function deletePostedPrice(Vehicle $vehicle)
    {
        // Get original values for logging
        $original = $vehicle->getOriginal();

        $vehicle->update([
            'posted_price' => null,
        ]);

        // Track changes for logging
        $changes = [];
        if (isset($original['posted_price']) && $original['posted_price'] !== null) {
            $changes['posted_price'] = [
                'old' => $original['posted_price'],
                'new' => null
            ];
        }

        // Log activity
        $this->logUpdate($vehicle, !empty($changes) ? $changes : null);

        return response()->json([
            'success' => true,
            'message' => 'Posted price deleted successfully!',
            'vehicle' => $vehicle->fresh()
        ]);
    }

    /**
     * Update sold price for a vehicle.
     */
    public function updateSoldPrice(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'sold_price' => 'nullable|numeric|min:0',
        ]);

        // Get original values for logging
        $original = $vehicle->getOriginal();

        $vehicle->update([
            'sold_price' => $request->sold_price ?: null,
        ]);

        // Track changes for logging
        $changes = [];
        if (isset($original['sold_price']) && $original['sold_price'] != $vehicle->sold_price) {
            $changes['sold_price'] = [
                'old' => $original['sold_price'],
                'new' => $vehicle->sold_price
            ];
        }

        // Log activity
        $this->logUpdate($vehicle, !empty($changes) ? $changes : null);

        return response()->json([
            'success' => true,
            'message' => 'Sold price updated successfully!',
            'vehicle' => $vehicle->fresh()
        ]);
    }

    /**
     * Delete/clear sold price for a vehicle.
     */
    public function deleteSoldPrice(Vehicle $vehicle)
    {
        // Get original values for logging
        $original = $vehicle->getOriginal();

        $vehicle->update([
            'sold_price' => null,
        ]);

        // Track changes for logging
        $changes = [];
        if (isset($original['sold_price']) && $original['sold_price'] !== null) {
            $changes['sold_price'] = [
                'old' => $original['sold_price'],
                'new' => null
            ];
        }

        // Log activity
        $this->logUpdate($vehicle, !empty($changes) ? $changes : null);

        return response()->json([
            'success' => true,
            'message' => 'Sold price deleted successfully!',
            'vehicle' => $vehicle->fresh()
        ]);
    }
}