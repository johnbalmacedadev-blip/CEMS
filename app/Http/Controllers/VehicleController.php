<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleIncentive;
use App\Models\CarFinancingSetting;
use App\Models\FinancingScheme;
use App\Models\BranchLocation;
use App\Models\MiscellaneousTransaction;
use App\Support\ExcelUnitReconcile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Traits\LogsActivity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $reservationDateFrom = $request->get('reservation_date_from');
        $reservationDateTo = $request->get('reservation_date_to');
        // Backward compatibility for old single-date filter.
        $reservationDate = $request->get('reservation_date');
        $releaseDateFrom = $request->get('release_date_from');
        $releaseDateTo = $request->get('release_date_to');
        $branchLocationId = $request->get('branch_location_id');

        $with = ['make', 'vehicleModel', 'primaryImage', 'branchLocation'];
        if (Schema::hasTable('vehicle_forfeit_details')) {
            $with[] = 'forfeitDetails';
        }
        $query = Vehicle::with($with);
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

        if ($branchLocationId !== null && $branchLocationId !== '' && is_numeric($branchLocationId)) {
            $query->where('branch_location_id', (int) $branchLocationId);
        }

        $this->applyLocationVisibilityFilter($query, $request);

        $hasReservationFrom = is_string($reservationDateFrom) && trim($reservationDateFrom) !== '';
        $hasReservationTo = is_string($reservationDateTo) && trim($reservationDateTo) !== '';
        $hasLegacyReservation = is_string($reservationDate) && trim($reservationDate) !== '';
        if ($status === 'Reserved' && ($hasReservationFrom || $hasReservationTo || $hasLegacyReservation)) {
            $query->whereHas('statusDetail', function ($q) use ($hasReservationFrom, $hasReservationTo, $hasLegacyReservation, $reservationDateFrom, $reservationDateTo, $reservationDate) {
                if ($hasLegacyReservation && ! $hasReservationFrom && ! $hasReservationTo) {
                    $q->whereDate('sale_date', trim($reservationDate));

                    return;
                }
                if ($hasReservationFrom) {
                    $q->whereDate('sale_date', '>=', trim($reservationDateFrom));
                }
                if ($hasReservationTo) {
                    $q->whereDate('sale_date', '<=', trim($reservationDateTo));
                }
            });
        }

        $hasReleaseFrom = is_string($releaseDateFrom) && trim($releaseDateFrom) !== '';
        $hasReleaseTo = is_string($releaseDateTo) && trim($releaseDateTo) !== '';
        if ($status === 'Released' && ($hasReleaseFrom || $hasReleaseTo)) {
            $query->whereHas('statusDetail', function ($q) use ($hasReleaseFrom, $hasReleaseTo, $releaseDateFrom, $releaseDateTo) {
                if ($hasReleaseFrom) {
                    $q->whereDate('release_date', '>=', trim($releaseDateFrom));
                }
                if ($hasReleaseTo) {
                    $q->whereDate('release_date', '<=', trim($releaseDateTo));
                }
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Location-box checkboxes: when `locations` is present, only include those branch IDs
     * (and optionally unassigned via locations_none=1). Absent params = show all locations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $query
     */
    protected function applyLocationVisibilityFilter($query, Request $request): void
    {
        if (! $request->has('locations') && ! $request->has('locations_none')) {
            return;
        }

        $ids = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) $request->input('locations', [])
        ), fn ($id) => $id > 0)));
        $includeNone = $request->boolean('locations_none');

        $query->where(function ($q) use ($ids, $includeNone) {
            if ($ids !== []) {
                $q->whereIn('branch_location_id', $ids);
            }
            if ($includeNone) {
                if ($ids !== []) {
                    $q->orWhereNull('branch_location_id');
                } else {
                    $q->whereNull('branch_location_id');
                }
            } elseif ($ids === []) {
                $q->whereRaw('0 = 1');
            }
        });
    }

    /**
     * @return array{ids: array<int, int>|null, include_none: bool}
     * null ids = all locations enabled (default).
     */
    protected function locationVisibilityState(Request $request): array
    {
        if (! $request->has('locations') && ! $request->has('locations_none')) {
            return ['ids' => null, 'include_none' => true];
        }

        $ids = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) $request->input('locations', [])
        ), fn ($id) => $id > 0)));

        return [
            'ids' => $ids,
            'include_none' => $request->boolean('locations_none'),
        ];
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

        $vehicles = $this->vehiclesIndexCollection($request);
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
        $showPurchasePrice = auth()->user()?->canViewPurchasePrice() ?? false;

        return response()->streamDownload(function () use ($vehicles, $showPurchasePrice) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            $headers = [
                'Year',
                'Make',
                'Model',
                'Variant',
                'Body Type',
                'Plate',
                'Excel Tab',
                'Colour',
                'Transmission',
                'Fuel',
                'Kilometers',
            ];
            if ($showPurchasePrice) {
                $headers[] = 'Purchase Price';
            }
            $headers = array_merge($headers, [
                'Posted Price',
                'Sold Price',
                'Status',
                'Purchase Date',
                'Purchased From',
            ]);
            fputcsv($out, $headers);
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
                $row = [
                    $v->year,
                    $makeLabel,
                    $modelLabel,
                    $v->variant,
                    $v->body_type,
                    $v->plate_number,
                    $v->getAttribute('excel_source_tab'),
                    $v->colour,
                    $v->transmission,
                    $v->fuel_type,
                    $v->kilometers,
                ];
                if ($showPurchasePrice) {
                    $row[] = $v->purchase_price !== null ? number_format((float) $v->purchase_price, 2, '.', '') : '';
                }
                $row = array_merge($row, [
                    $v->posted_price !== null ? number_format((float) $v->posted_price, 2, '.', '') : '',
                    $v->sold_price !== null ? number_format((float) $v->sold_price, 2, '.', '') : '',
                    $displayStatus,
                    $v->purchase_date ? $v->purchase_date->format('Y-m-d') : '',
                    $v->purchased_from,
                ]);
                fputcsv($out, $row);
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

        // Release / reservation date filters only belong on their status tabs.
        // Drop them from the URL when the user switches away so results aren't emptied.
        $cleanQuery = $request->query();
        $needsRedirect = false;
        if ($status !== 'Released') {
            foreach (['release_date_from', 'release_date_to'] as $key) {
                if (array_key_exists($key, $cleanQuery) && $cleanQuery[$key] !== null && $cleanQuery[$key] !== '') {
                    unset($cleanQuery[$key]);
                    $needsRedirect = true;
                }
            }
        }
        if ($status !== 'Reserved') {
            foreach (['reservation_date_from', 'reservation_date_to', 'reservation_date'] as $key) {
                if (array_key_exists($key, $cleanQuery) && $cleanQuery[$key] !== null && $cleanQuery[$key] !== '') {
                    unset($cleanQuery[$key]);
                    $needsRedirect = true;
                }
            }
        }
        if ($needsRedirect) {
            unset($cleanQuery['page']);
            $cleanQuery['status'] = $status;

            return redirect()->route('vehicles.index', $cleanQuery);
        }

        $search = $request->get('search');
        $yearFrom = $request->get('year_from');
        $yearTo = $request->get('year_to');
        $transmission = $request->get('transmission');
        $fuelType = $request->get('fuel_type');
        $bodyType = $request->get('body_type');
        $purchasedFrom = $request->get('purchased_from');
        $reservationDateFrom = $request->get('reservation_date_from');
        $reservationDateTo = $request->get('reservation_date_to');
        $releaseDateFrom = $request->get('release_date_from');
        $releaseDateTo = $request->get('release_date_to');
        $branchLocationId = $request->get('branch_location_id');

        if ($status === 'Miscellaneous') {
            $miscQuery = MiscellaneousTransaction::query()
                ->orderByDesc('transaction_date')
                ->orderByDesc('id');

            if ($search) {
                $miscQuery->where(function ($q) use ($search) {
                    $q->where('description', 'like', '%'.$search.'%')
                        ->orWhere('location', 'like', '%'.$search.'%');
                });
            }

            $miscLocation = trim((string) $request->get('misc_location', ''));
            if ($miscLocation !== '' && in_array($miscLocation, MiscellaneousTransaction::locationOptions(), true)) {
                $miscQuery->where('location', $miscLocation);
            }

            $miscYear = (int) $request->get('misc_year', 0);
            $miscMonth = (int) $request->get('misc_month', 0);
            if ($miscYear >= 2000 && $miscYear <= 2100) {
                $miscQuery->whereYear('transaction_date', $miscYear);
            } else {
                $miscYear = 0;
            }
            if ($miscMonth >= 1 && $miscMonth <= 12) {
                $miscQuery->whereMonth('transaction_date', $miscMonth);
            } else {
                $miscMonth = 0;
            }

            $miscYearOptions = MiscellaneousTransaction::query()
                ->selectRaw('YEAR(transaction_date) as y')
                ->whereNotNull('transaction_date')
                ->distinct()
                ->orderByDesc('y')
                ->pluck('y')
                ->map(fn ($y) => (int) $y)
                ->filter()
                ->values();
            $currentYear = (int) now()->year;
            if (! $miscYearOptions->contains($currentYear)) {
                $miscYearOptions = $miscYearOptions->prepend($currentYear)->unique()->values();
            }
            if ($miscYear > 0 && ! $miscYearOptions->contains($miscYear)) {
                $miscYearOptions = $miscYearOptions->prepend($miscYear)->unique()->values();
            }

            $miscFilteredTotal = (float) (clone $miscQuery)->sum('amount');
            $miscellaneousTransactions = $miscQuery->paginate(20)->withQueryString();
            $vehicles = new LengthAwarePaginator([], 0, 10);
            $branches = BranchLocation::ordered()->get();
            $locationCounts = collect();
            $unassignedLocationCount = 0;
            $excelReconcileNotes = [];
            $excelReleaseWarning = null;
            $locationVisibility = $this->locationVisibilityState($request);
            $availableCount = Vehicle::where('status', 'Available')->count();
            $reservedCount = Vehicle::where('status', 'Reserved')->count();
            $releasedCount = Vehicle::where('status', 'Released')->count();
            $underMaintenanceCount = Vehicle::where('status', 'Under Maintenance')->count();
            $forfeitedCount = $this->forfeitedVehicleCount();

            return view('vehicles.index', compact(
                'vehicles',
                'miscellaneousTransactions',
                'miscLocation',
                'miscYear',
                'miscMonth',
                'miscYearOptions',
                'miscFilteredTotal',
                'status',
                'search',
                'yearFrom',
                'yearTo',
                'transmission',
                'fuelType',
                'bodyType',
                'purchasedFrom',
                'reservationDateFrom',
                'reservationDateTo',
                'releaseDateFrom',
                'releaseDateTo',
                'branchLocationId',
                'branches',
                'availableCount',
                'reservedCount',
                'releasedCount',
                'underMaintenanceCount',
                'forfeitedCount',
                'locationCounts',
                'unassignedLocationCount',
                'excelReconcileNotes',
                'excelReleaseWarning',
                'locationVisibility'
            ));
        }

        // Sync status to Forfeited for any vehicle that has forfeit details (skip archived)
        if (Schema::hasTable('vehicle_forfeit_details')) {
            Vehicle::whereHas('forfeitDetails')
                ->whereNotIn('status', ['Forfeited', 'Archived'])
                ->update(['status' => 'Forfeited']);
        }

        $excelReleaseContext = ExcelUnitReconcile::releasedDateFilterContext($request);
        $excelReleaseWarning = $excelReleaseContext['warning'] ?? null;
        $excelReconcileNotes = [];
        $locationVisibility = $this->locationVisibilityState($request);

        if ($excelReleaseContext) {
            $vehicles = $this->paginateExcelAlignedReleased($request, $excelReleaseContext);
            $branches = BranchLocation::ordered()->get();
            $locationCounts = collect($excelReleaseContext['location_counts'] ?? [])->map(function ($loc) use ($branches) {
                $branch = $branches->first(function ($b) use ($loc) {
                    return strcasecmp((string) $b->name, (string) ($loc['name'] ?? '')) === 0;
                });

                return [
                    'id' => $branch?->id ?? ($loc['id'] ?? null),
                    'name' => $loc['name'] ?? 'Unknown',
                    'count' => (int) ($loc['count'] ?? 0),
                ];
            })->values();
            $unassignedLocationCount = 0;
        } else {
            $vehicles = $this->vehiclesIndexBaseQuery($request)->paginate(10)->withQueryString();
            $branches = BranchLocation::ordered()->get();

            $summaryParams = $request->all();
            unset(
                $summaryParams['branch_location_id'],
                $summaryParams['page'],
                $summaryParams['locations'],
                $summaryParams['locations_none']
            );
            $locationSummaryBase = $this->vehiclesIndexBaseQuery(new Request($summaryParams));

            $locationCounts = $branches->map(function ($branch) use ($locationSummaryBase) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'count' => (clone $locationSummaryBase)->where('branch_location_id', $branch->id)->count(),
                ];
            })->values();

            $unassignedLocationCount = (clone $locationSummaryBase)->whereNull('branch_location_id')->count();
            $excelReconcileNotes = ExcelUnitReconcile::notesForRequest($request);
        }

        ExcelUnitReconcile::attachExcelSourceRows($vehicles);

        // Get counts for each status
        $availableCount = Vehicle::where('status', 'Available')->count();
        $reservedCount = Vehicle::where('status', 'Reserved')->count();
        $releasedCount = Vehicle::where('status', 'Released')->count();
        $underMaintenanceCount = Vehicle::where('status', 'Under Maintenance')->count();
        $forfeitedCount = $this->forfeitedVehicleCount();

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
            'reservationDateFrom',
            'reservationDateTo',
            'releaseDateFrom',
            'releaseDateTo',
            'branchLocationId',
            'branches',
            'availableCount',
            'reservedCount',
            'releasedCount',
            'underMaintenanceCount',
            'forfeitedCount',
            'locationCounts',
            'unassignedLocationCount',
            'excelReconcileNotes',
            'excelReleaseWarning',
            'locationVisibility'
        ));
    }

    protected function forfeitedVehicleCount(): int
    {
        $query = Vehicle::where('status', '!=', 'Archived');
        if (Schema::hasTable('vehicle_forfeit_details')) {
            $query->where(function ($q) {
                $q->where('status', 'Forfeited')->orWhereHas('forfeitDetails');
            });
        } else {
            $query->where('status', 'Forfeited');
        }

        return $query->count();
    }

    /**
     * List/count units from Excel release history for the selected date range.
     * Includes Excel-matched plates even when DB status is no longer Released (e.g. Forfeited).
     *
     * @param  array<string, mixed>  $context
     */
    protected function paginateExcelAlignedReleased(Request $request, array $context): LengthAwarePaginator
    {
        $platesMeta = $context['plates_meta'] ?? [];
        $vehicleIds = $context['vehicle_ids'] ?? [];
        $issuesByPlate = $context['issues_by_plate'] ?? [];

        $query = Vehicle::with(['make', 'vehicleModel', 'primaryImage', 'forfeitDetails', 'branchLocation', 'statusDetail'])
            ->whereIn('id', $vehicleIds ?: [0])
            ->where('status', '!=', 'Archived');

        ExcelUnitReconcile::applyNonReleaseFilters($query, $request);
        $this->applyLocationVisibilityFilter($query, $request);

        $vehicles = $query->get();
        $vehiclesByPlate = $vehicles->groupBy(
            fn (Vehicle $vehicle) => ExcelUnitReconcile::normalizePlate($vehicle->plate_number)
        );

        // Prefer Excel order; include Excel-only stubs (blank plate / missing in DB).
        $ordered = collect();
        $usedVehicleIds = [];
        foreach ($platesMeta as $plateKey => $meta) {
            $plate = ExcelUnitReconcile::metaPlate($plateKey, $meta);
            $candidates = $vehiclesByPlate->get($plate, collect())->all();
            $vehicle = ExcelUnitReconcile::pickVehicleForExcelRow($candidates, $meta, $usedVehicleIds);
            if (! $vehicle) {
                // Extra filters (search/year/etc.) should skip stubs that don't match.
                if (! $this->excelOnlyVehicleMatchesFilters($meta, $request)) {
                    continue;
                }
                if (! $this->excelOnlyVehicleMatchesLocationVisibility($meta, $request)) {
                    continue;
                }
                $vehicle = ExcelUnitReconcile::makeExcelOnlyVehicle($plate !== '' ? $plate : $plateKey, $meta);
            } else {
                $usedVehicleIds[$vehicle->id] = true;
            }

            if (isset($meta['purchase_price']) && $meta['purchase_price'] !== null && $meta['purchase_price'] !== '') {
                $vehicle->setAttribute('purchase_price', $meta['purchase_price']);
            }

            $rowIssues = array_values(array_filter(
                $issuesByPlate[$plate] ?? [],
                function ($issue) use ($meta) {
                    $issueRow = $issue['excel_row'] ?? null;
                    if ($issueRow === null || ($meta['excel_row'] ?? null) === null) {
                        return true;
                    }

                    return (int) $issueRow === (int) $meta['excel_row'];
                }
            ));
            $vehicle->setAttribute('excel_period_release_date', $meta['release_date'] ?? null);
            $vehicle->setAttribute('excel_period_row', $meta['excel_row'] ?? null);
            $vehicle->setAttribute('excel_source_tab', $meta['tab'] ?? null);
            $vehicle->setAttribute('excel_period_branch', $meta['branch'] ?? null);
            $vehicle->setAttribute('excel_release_issues', $rowIssues);
            $vehicle->setAttribute('excel_forced_section_add', ! empty($meta['forced_section_add']) || ! empty($meta['forced_rerelease_add']));
            $vehicle->setAttribute('has_excel_rerelease_issue', false);
            if ($meta && optional($vehicle->statusDetail)->release_date) {
                $dbDate = $vehicle->statusDetail->release_date->format('Y-m-d');
                if (! empty($meta['release_date']) && $dbDate !== $meta['release_date']) {
                    $vehicle->setAttribute('has_excel_rerelease_issue', true);
                }
            }
            foreach ($issuesByPlate[$plate] ?? [] as $issue) {
                if (($issue['excel_row'] ?? null) !== null && ($meta['excel_row'] ?? null) !== null
                    && (int) $issue['excel_row'] !== (int) $meta['excel_row']) {
                    continue;
                }
                if (($issue['type'] ?? '') === 'duplicate_rerelease') {
                    $vehicle->setAttribute('has_excel_rerelease_issue', true);
                    break;
                }
            }
            $ordered->push($vehicle);
        }

        $vehicles = $ordered->values();

        // Excel unique count is the source of truth for this filtered period.
        // If extra filters remove some rows, use the filtered matched count instead.
        $hasExtra = ExcelUnitReconcile::hasAppliedFilters($request)
            && (
                trim((string) $request->get('search', '')) !== ''
                || trim((string) $request->get('year_from', '')) !== ''
                || trim((string) $request->get('year_to', '')) !== ''
                || trim((string) $request->get('transmission', '')) !== ''
                || trim((string) $request->get('fuel_type', '')) !== ''
                || trim((string) $request->get('body_type', '')) !== ''
                || trim((string) $request->get('purchased_from', '')) !== ''
            );
        $hasLocationVisibility = $request->has('locations') || $request->has('locations_none');

        $excelTotal = (int) ($context['total'] ?? count($platesMeta));
        if ($hasExtra || $hasLocationVisibility) {
            $total = $vehicles->count();
        } else {
            $total = $excelTotal;
        }

        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $vehicles->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    protected function excelOnlyVehicleMatchesFilters(array $meta, Request $request): bool
    {
        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $hay = strtoupper(implode(' ', array_filter([
                $meta['plate_raw'] ?? '',
                $meta['plate'] ?? '',
                $meta['make'] ?? '',
                $meta['model'] ?? '',
                $meta['variant'] ?? '',
                (string) ($meta['year'] ?? ''),
            ])));
            if (! str_contains($hay, strtoupper($search))) {
                return false;
            }
        }

        $yearFrom = trim((string) $request->get('year_from', ''));
        $yearTo = trim((string) $request->get('year_to', ''));
        $year = isset($meta['year']) ? (int) $meta['year'] : null;
        if ($yearFrom !== '' && ($year === null || $year < (int) $yearFrom)) {
            return false;
        }
        if ($yearTo !== '' && ($year === null || $year > (int) $yearTo)) {
            return false;
        }

        $transmission = trim((string) $request->get('transmission', ''));
        if ($transmission !== '' && strcasecmp((string) ($meta['transmission'] ?? ''), $transmission) !== 0) {
            return false;
        }
        $fuelType = trim((string) $request->get('fuel_type', ''));
        if ($fuelType !== '' && strcasecmp((string) ($meta['fuel_type'] ?? ''), $fuelType) !== 0) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    protected function excelOnlyVehicleMatchesLocationVisibility(array $meta, Request $request): bool
    {
        if (! $request->has('locations') && ! $request->has('locations_none')) {
            return true;
        }

        $state = $this->locationVisibilityState($request);
        $ids = $state['ids'];
        $includeNone = $state['include_none'];
        $branchName = trim((string) ($meta['branch'] ?? ''));
        if ($branchName === '') {
            return $includeNone;
        }

        if ($ids === null) {
            return true;
        }

        $branch = BranchLocation::query()->where('name', $branchName)->first();
        if (! $branch) {
            return $includeNone;
        }

        return in_array((int) $branch->id, $ids, true);
    }

    /**
     * Vehicles collection for export using the same Excel-aligned release logic when applicable.
     */
    protected function vehiclesIndexCollection(Request $request)
    {
        $excelReleaseContext = ExcelUnitReconcile::releasedDateFilterContext($request);
        if ($excelReleaseContext) {
            // Reuse Excel-aligned builder for the full filtered set.
            $platesMeta = $excelReleaseContext['plates_meta'] ?? [];
            $vehicleIds = $excelReleaseContext['vehicle_ids'] ?? [];
            $issuesByPlate = $excelReleaseContext['issues_by_plate'] ?? [];
            $query = Vehicle::with(['make', 'vehicleModel', 'primaryImage', 'forfeitDetails', 'branchLocation', 'statusDetail'])
                ->whereIn('id', $vehicleIds ?: [0])
                ->where('status', '!=', 'Archived');
            ExcelUnitReconcile::applyNonReleaseFilters($query, $request);
            $this->applyLocationVisibilityFilter($query, $request);
            $vehiclesByPlate = $query->get()->groupBy(
                fn (Vehicle $vehicle) => ExcelUnitReconcile::normalizePlate($vehicle->plate_number)
            );

            $ordered = collect();
            $usedVehicleIds = [];
            foreach ($platesMeta as $plateKey => $meta) {
                $plate = ExcelUnitReconcile::metaPlate($plateKey, $meta);
                $candidates = $vehiclesByPlate->get($plate, collect())->all();
                $vehicle = ExcelUnitReconcile::pickVehicleForExcelRow($candidates, $meta, $usedVehicleIds);
                if (! $vehicle) {
                    if (! $this->excelOnlyVehicleMatchesFilters($meta, $request)) {
                        continue;
                    }
                    if (! $this->excelOnlyVehicleMatchesLocationVisibility($meta, $request)) {
                        continue;
                    }
                    $vehicle = ExcelUnitReconcile::makeExcelOnlyVehicle($plate !== '' ? $plate : $plateKey, $meta);
                } else {
                    $usedVehicleIds[$vehicle->id] = true;
                }
                if (isset($meta['purchase_price']) && $meta['purchase_price'] !== null && $meta['purchase_price'] !== '') {
                    $vehicle->setAttribute('purchase_price', $meta['purchase_price']);
                }
                $vehicle->setAttribute('excel_period_release_date', $meta['release_date'] ?? null);
                $vehicle->setAttribute('excel_period_row', $meta['excel_row'] ?? null);
                $vehicle->setAttribute('excel_source_tab', $meta['tab'] ?? null);
                $vehicle->setAttribute('excel_period_branch', $meta['branch'] ?? null);
                $vehicle->setAttribute('excel_period_total_revenue', $meta['total_revenue'] ?? null);
                $vehicle->setAttribute('excel_period_total_costs', $meta['total_costs'] ?? null);
                $vehicle->setAttribute('excel_period_total_profit', $meta['total_profit'] ?? null);
                $vehicle->setAttribute('excel_period_sales_price', $meta['sales_price'] ?? null);
                $vehicle->setAttribute('excel_forced_section_add', ! empty($meta['forced_section_add']) || ! empty($meta['forced_rerelease_add']));
                $vehicle->setAttribute('excel_release_issues', $issuesByPlate[$plate] ?? []);
                $ordered->push($vehicle);
            }

            ExcelUnitReconcile::attachExcelSourceRows($ordered);

            return $ordered->values();
        }

        $vehicles = $this->vehiclesIndexBaseQuery($request)->get();
        ExcelUnitReconcile::attachExcelSourceRows($vehicles);

        return $vehicles;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = BranchLocation::active()->ordered()->get();

        return view('vehicles.create', compact('branches'));
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
            'branch_location_id' => 'required|exists:branch_locations,id',
        ]);

        // Get the make and model names for backward compatibility
        $make = \App\Models\Make::find($request->make_id);
        $model = \App\Models\VehicleModel::find($request->model_id);
        
        $vehicleData = $request->all();
        $vehicleData['make'] = $make ? $make->name : '';
        $vehicleData['model'] = $model ? $model->name : '';
        $vehicleData['branch_location_id'] = $request->branch_location_id;
        
        $vehicle = Vehicle::create($vehicleData);

        // Log activity (never block vehicle create if logging fails)
        try {
            $this->logCreate($vehicle);
        } catch (\Throwable $e) {
            report($e);
        }

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
     * JSON payload for the Unit Report eye-icon quick view modal (Excel-style labels).
     */
    public function quickView(Vehicle $vehicle)
    {
        try {
            $relations = ['branchLocation'];
            if (Schema::hasTable('vehicle_expenses')) {
                $relations[] = 'expense';
            }
            if (Schema::hasTable('vehicle_status_details')) {
                $relations[] = 'statusDetail';
            }
            if (Schema::hasTable('vehicle_forfeit_details')) {
                $relations[] = 'forfeitDetails';
            }
            if (Schema::hasTable('vehicle_ads')) {
                $relations[] = 'ads';
            }
            if (Schema::hasTable('video_posting_records')) {
                $relations[] = 'videoPostingRecords';
            }
            $vehicle->load($relations);

            $attrs = $vehicle->getAttributes();
            $sd = $vehicle->statusDetail;
            $ex = $vehicle->expense;
            $forfeit = collect($vehicle->forfeitDetails ?? [])->sortByDesc(function ($row) {
                return optional($row->forfeit_date)->timestamp ?? 0;
            })->first();

        $salesPrice = (float) ($vehicle->sold_price ?? $sd?->sales_price ?? 0);
        $finance1 = (float) ($sd?->finance_revenue_1 ?? 0);
        $finance2 = (float) ($sd?->finance_revenue_2 ?? 0);
        $totalRevenue = $salesPrice + $finance1 + $finance2;

        $purchasePrice = (float) ($vehicle->purchase_price ?? 0);
        $repairCost = (float) ($ex?->total_repair_cost ?? 0);
        $postRepairCost = (float) ($ex?->post_reservation_repairs_cost ?? 0);
        $capital = (float) ($ex?->total_capital_repair_capital_posted ?? 0);
        if ($capital <= 0) {
            $capital = $purchasePrice + $repairCost + $postRepairCost;
        }
        $agentCost = (float) ($sd?->agent_cost ?? 0);
        $transferCost = (float) ($sd?->transfer_cost ?? 0);
        $totalCosts = $capital + $agentCost + $transferCost;
        $totalProfit = $totalRevenue - $totalCosts;

        $canViewPurchase = auth()->user()?->canViewPurchasePrice() ?? false;
        $videoMeta = $this->quickViewVideoMeta($vehicle);
        $adLinks = $vehicle->relationLoaded('ads')
            ? $vehicle->ads
                ->flatMap(fn ($ad) => $ad->video_links_list)
                ->filter()
                ->unique()
                ->values()
                ->implode("\n")
            : '';

        $cashFinancing = trim((string) ($sd?->cash_financing ?? ''));
        if (! empty($sd?->financing_company)) {
            $cashFinancing = trim($cashFinancing.' · '.$sd->financing_company);
        }

        $fields = [
            ['label' => 'YEAR', 'value' => $this->qvText($attrs['year'] ?? null)],
            ['label' => 'MAKE', 'value' => $this->qvText($attrs['make'] ?? null)],
            ['label' => 'MODEL', 'value' => $this->qvText($attrs['model'] ?? null)],
            ['label' => 'VARIANT', 'value' => $this->qvText($attrs['variant'] ?? null)],
            ['label' => 'TRANSMISSION', 'value' => $this->qvText($attrs['transmission'] ?? null)],
            ['label' => 'FUEL TYPE', 'value' => $this->qvText($attrs['fuel_type'] ?? null)],
            ['label' => 'KILOMETERS', 'value' => $this->qvNumber($attrs['kilometers'] ?? null, 0)],
            ['label' => 'PLATE NUMBER', 'value' => $this->qvText($attrs['plate_number'] ?? null)],
            ['label' => 'COLOR', 'value' => $this->qvText($attrs['colour'] ?? null)],
            ['label' => 'WITH TOOLS', 'value' => $this->qvYesNo($vehicle->with_tools)],
            ['label' => 'WITH MATTING', 'value' => $this->qvYesNo($vehicle->with_matting)],
            ['label' => 'WITH SPARE TIRE', 'value' => $this->qvYesNo($vehicle->with_spare_tire)],
            ['label' => 'PURCHASE PRICE', 'value' => $canViewPurchase ? $this->qvMoney($vehicle->purchase_price) : '—'],
            ['label' => 'PURCHASED FROM', 'value' => $this->qvText($attrs['purchased_from'] ?? null)],
            ['label' => 'FORMATTED PURCHASE DATE', 'value' => $this->qvDate($vehicle->purchase_date)],
            ['label' => 'SPARE KEY', 'value' => $this->qvYesNo($vehicle->spare_key)],
            ['label' => 'PAINT ITEMS', 'value' => $this->qvText($ex?->paint_items)],
            ['label' => 'PAINT COSTS', 'value' => $this->qvMoney($ex?->paint_costs)],
            ['label' => 'MECHANICAL/ELECTRICAL ITEMS', 'value' => $this->qvText($ex?->mechanical_electrical_items)],
            ['label' => 'MECHANICAL/ELECTRICAL COSTS', 'value' => $this->qvMoney($ex?->mechanical_electrical_costs)],
            ['label' => 'SCANNER ITEMS', 'value' => $this->qvText($ex?->cluster_items)],
            ['label' => 'SCANNER COSTS', 'value' => $this->qvMoney($ex?->cluster_costs)],
            ['label' => 'AIRCON ITEMS', 'value' => $this->qvText($ex?->aircon_items)],
            ['label' => 'AIRCON COST', 'value' => $this->qvMoney($ex?->aircon_cost)],
            ['label' => 'INTERIOR ITEMS', 'value' => $this->qvText($ex?->interior_items)],
            ['label' => 'INTERIOR COSTS', 'value' => $this->qvMoney($ex?->interior_costs)],
            ['label' => 'PAPERS ITEMS', 'value' => $this->qvText($ex?->papers_items)],
            ['label' => 'PAPERS COSTS', 'value' => $this->qvMoney($ex?->papers_costs)],
            ['label' => 'TYRES/BATTERY ITEMS', 'value' => $this->qvText($ex?->tyres_battery_items)],
            ['label' => 'TYRES/BATTERY COST', 'value' => $this->qvMoney($ex?->tyres_battery_cost)],
            ['label' => 'MISC ITEMS', 'value' => $this->qvText($ex?->misc_items)],
            ['label' => 'MISC COSTS', 'value' => $this->qvMoney($ex?->misc_costs)],
            ['label' => 'TOTAL REPAIR ITEMS', 'value' => $this->qvText($ex?->total_repair_items)],
            ['label' => 'TOTAL REPAIR COST', 'value' => $this->qvMoney($ex?->total_repair_cost)],
            ['label' => 'ADDITIONAL REPAIRS', 'value' => $this->qvText($ex?->post_reservation_repairs)],
            ['label' => 'ADDITIONAL REPAIRS COST', 'value' => $this->qvMoney($ex?->post_reservation_repairs_cost)],
            ['label' => 'TOTAL CAPITAL + REPAIR CAPITAL', 'value' => $this->qvMoney($capital > 0 ? $capital : $ex?->total_capital_repair_capital_posted)],
            ['label' => 'POSTED - PRICE', 'value' => $this->qvMoney($vehicle->posted_price ?? $ex?->price)],
            ['label' => 'SHOWROOM', 'value' => $this->qvText($sd?->showroom ?? $vehicle->branchLocation?->name)],
            ['label' => 'FORMATTED SALE DATE', 'value' => $this->qvDate($sd?->sale_date)],
            ['label' => 'SALES PRICE', 'value' => $this->qvMoney($sd?->sales_price ?? $vehicle->sold_price)],
            ['label' => 'SALE RESERVATION AMOUNT', 'value' => $this->qvMoney($sd?->sale_reservation_amount)],
            ['label' => 'SALES PERSON (RESERVED)', 'value' => $this->qvText($sd?->sales_person_reserved)],
            ['label' => 'SALES PERSON (RELEASE)', 'value' => $this->qvText($sd?->sales_person_release)],
            ['label' => 'GOOGLE REVIEW (YES OR NO)', 'value' => $this->qvYesNo($sd?->good_sales_review)],
            ['label' => 'VIDEO TESTIMONIAL (YES OR NO)', 'value' => ''],
            ['label' => 'FINANCING AMOUNT', 'value' => ''],
            ['label' => 'CASH/FINANCING', 'value' => $this->qvText($cashFinancing !== '' ? $cashFinancing : null)],
            ['label' => 'WITH/WITHOUT INSURANCE', 'value' => $this->qvInsurance($sd?->has_insurance)],
            ['label' => 'SALE ORIGIN', 'value' => $this->qvText($sd?->sale_origin)],
            ['label' => 'AGENT COST', 'value' => $this->qvMoney($sd?->agent_cost)],
            ['label' => 'FINANCE REVENUE 1', 'value' => $this->qvMoney($sd?->finance_revenue_1)],
            ['label' => 'FINANCE REVENUE 2', 'value' => $this->qvMoney($sd?->finance_revenue_2)],
            ['label' => 'SALE STATUS', 'value' => $this->qvText($vehicle->status ?? $sd?->sale_status)],
            ['label' => 'TRANSFER COST', 'value' => $this->qvMoney($sd?->transfer_cost)],
            ['label' => 'FORMATTED RELEASE DATE', 'value' => $this->qvDate($sd?->release_date)],
            ['label' => 'DAYS FROM RESERVATION TO RELEASE', 'value' => $this->qvText($sd?->days_from_reservation_to_release)],
            ['label' => 'CUSTOMER FIRST NAME', 'value' => $this->qvText($sd?->customer_first_name)],
            ['label' => 'CUSTOMER LAST NAME', 'value' => $this->qvText($sd?->customer_last_name)],
            ['label' => 'CUSTOMER DOB', 'value' => $this->qvDate($sd?->customer_date_of_birth)],
            ['label' => 'CUSTOMER GENDER', 'value' => $this->qvText($sd?->customer_gender)],
            ['label' => 'CUSTOMER LOCATION', 'value' => $this->qvText($sd?->customer_location)],
            ['label' => 'CUSTOMER PURPOSE', 'value' => $this->qvText($sd?->customer_purpose)],
            ['label' => 'TOTAL REVENUE', 'value' => $this->qvMoney($totalRevenue)],
            ['label' => 'TOTAL COSTS', 'value' => $this->qvMoney($totalCosts)],
            ['label' => 'TOTAL PROFIT', 'value' => $this->qvMoney($totalProfit)],
            ['label' => 'PREVIOUS FORFEIT', 'value' => $this->qvDate($forfeit?->previous_forfeit_date)],
            ['label' => 'FORFEIT AMOUNT', 'value' => $this->qvMoney($forfeit?->forfeit_amount)],
            ['label' => 'FORFEIT DATE', 'value' => $this->qvDate($forfeit?->forfeit_date)],
            ['label' => 'CASA RECORDS SENT TO GC', 'value' => ''],
            ['label' => 'VISIBLE ON WEBSITE', 'value' => ''],
            ['label' => 'VISIBLE ON PRICELIST', 'value' => ''],
            ['label' => 'LINK TO WEBSITE', 'value' => ''],
            ['label' => 'DATE OF LAST SOLO VIDEO', 'value' => $videoMeta['solo']],
            ['label' => 'DATE OF LAST DUAL VIDEO', 'value' => $videoMeta['dual']],
            ['label' => 'DATE OF LAST GROUP VIDEO', 'value' => $videoMeta['group']],
            ['label' => 'LINK TO VIDEO', 'value' => $this->qvText($adLinks !== '' ? $adLinks : ($videoMeta['link'] ?: null))],
            ['label' => 'DATE OF LAST SOLO VIDEO', 'value' => $videoMeta['solo_premium']],
            ['label' => 'DATE OF LAST DUAL VIDEO', 'value' => $videoMeta['dual_premium']],
            ['label' => 'DATE OF LAST GROUP VIDEO', 'value' => $videoMeta['group_premium']],
            ['label' => 'LINK TO VIDEO PREMIUM', 'value' => $videoMeta['link_premium']],
            ['label' => 'FINANCING DOCS', 'value' => ''],
            ['label' => 'FINANCING', 'value' => $this->qvText($sd?->financing_company)],
        ];

        return response()->json([
            'ok' => true,
            'id' => $vehicle->id,
            'title' => trim(($attrs['year'] ?? '').' '.($attrs['make'] ?? '').' '.($attrs['model'] ?? '').' · '.($attrs['plate_number'] ?? 'No plate')),
            'show_url' => route('vehicles.show', $vehicle, false),
            'fields' => $fields,
        ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => 'Could not load vehicle details.',
            ], 500);
        }
    }

    /**
     * @return array{solo:string,dual:string,group:string,link:string,solo_premium:string,dual_premium:string,group_premium:string,link_premium:string}
     */
    protected function quickViewVideoMeta(Vehicle $vehicle): array
    {
        $empty = [
            'solo' => '', 'dual' => '', 'group' => '', 'link' => '',
            'solo_premium' => '', 'dual_premium' => '', 'group_premium' => '', 'link_premium' => '',
        ];
        if (! $vehicle->relationLoaded('videoPostingRecords')) {
            return $empty;
        }
        $records = $vehicle->videoPostingRecords;
        if ($records->isEmpty()) {
            return $empty;
        }

        $pickLatest = function (string $needle) use ($records): string {
            $match = $records
                ->filter(function ($row) use ($needle) {
                    $hay = strtoupper(trim(($row->category ?? '').' '.($row->title ?? '').' '.($row->type ?? '').' '.($row->showroom ?? '')));

                    return str_contains($hay, strtoupper($needle));
                })
                ->sortByDesc(fn ($row) => optional($row->date_posted_social ?? $row->record_date)->timestamp ?? 0)
                ->first();
            if (! $match) {
                return '';
            }
            $date = $match->date_posted_social ?? $match->record_date;

            return $date ? $date->format('d-M-y') : '';
        };

        $latestLink = $records
            ->filter(fn ($row) => filled($row->link_url))
            ->sortByDesc(fn ($row) => optional($row->date_posted_social ?? $row->record_date)->timestamp ?? 0)
            ->pluck('link_url')
            ->unique()
            ->take(3)
            ->implode("\n");

        return [
            'solo' => $pickLatest('SOLO'),
            'dual' => $pickLatest('DUAL'),
            'group' => $pickLatest('GROUP'),
            'link' => $latestLink,
            'solo_premium' => $pickLatest('PREMIUM SOLO') ?: '',
            'dual_premium' => $pickLatest('PREMIUM DUAL') ?: '',
            'group_premium' => $pickLatest('PREMIUM GROUP') ?: '',
            'link_premium' => '',
        ];
    }

    protected function qvText(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        $text = trim((string) $value);

        return $text === '' ? '' : $text;
    }

    protected function qvMoney(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return '₱'.number_format((float) $value, 2);
    }

    protected function qvNumber(mixed $value, int $decimals = 0): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return number_format((float) $value, $decimals);
    }

    protected function qvDate(mixed $value): string
    {
        if (! $value) {
            return '';
        }
        try {
            return \Carbon\Carbon::parse($value)->format('d-M-y');
        } catch (\Throwable $e) {
            return $this->qvText($value);
        }
    }

    protected function qvYesNo(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'YES' : 'NO';
    }

    protected function qvInsurance(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'WITH INSURANCE' : 'WITHOUT INSURANCE';
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
            $showPurchasePrice = auth()->user()?->canViewPurchasePrice() ?? false;
            fputcsv($out, ['Vehicle Information']);
            $headers = ['Year', 'Make', 'Model', 'Plate', 'Variant', 'Status'];
            if ($showPurchasePrice) {
                $headers[] = 'Purchase Price';
            }
            $headers = array_merge($headers, ['Purchase Date', 'Posted Price', 'Sold Price']);
            fputcsv($out, $headers);
            $row = [
                $vehicle->year ?? '',
                $makeName,
                $modelName,
                $vehicle->plate_number ?? '',
                $vehicle->variant ?? '',
                $vehicle->status ?? '',
            ];
            if ($showPurchasePrice) {
                $row[] = $vehicle->purchase_price ?? '';
            }
            $row = array_merge($row, [
                $vehicle->purchase_date ? $vehicle->purchase_date->format('Y-m-d') : '',
                $vehicle->posted_price ?? '',
                $vehicle->sold_price ?? '',
            ]);
            fputcsv($out, $row);
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
        $vehicle->load(['make', 'vehicleModel', 'branchLocation']);
        $branches = BranchLocation::active()->ordered()->get();

        // Keep the vehicle's current branch in the list even if inactive
        if ($vehicle->branch_location_id && ! $branches->contains('id', $vehicle->branch_location_id)) {
            $current = BranchLocation::find($vehicle->branch_location_id);
            if ($current) {
                $branches = $branches->prepend($current)->unique('id')->values();
            }
        }

        return view('vehicles.edit', compact('vehicle', 'branches'));
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
            'branch_location_id' => 'required|exists:branch_locations,id',
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
        $vehicleData['branch_location_id'] = $request->branch_location_id;
        
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

        $vehicle->load(['primaryImage', 'forfeitDetails', 'branchLocation']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle moved to Archived successfully.',
                'swal_title' => 'Archived',
                'vehicle_id' => $vehicle->id,
                'vehicle' => [
                    'id' => $vehicle->id,
                    'show_url' => route('vehicles.show', $vehicle, false),
                    'quick_view_url' => route('vehicles.quick-view', $vehicle, false),
                    'full_name' => $vehicle->full_name,
                    'year' => $vehicle->year,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'plate_number' => $vehicle->plate_number,
                    'excel_row' => $vehicle->getAttribute('excel_period_row'),
                    'colour' => $vehicle->colour,
                    'purchase_price' => auth()->user()?->canViewPurchasePrice()
                        ? $vehicle->formatted_purchase_price
                        : null,
                    'transmission' => $vehicle->transmission,
                    'fuel_type' => $vehicle->fuel_type,
                    'location' => $vehicle->branchLocation?->name,
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