<?php

namespace App\Http\Controllers;

use App\Models\ChemicalMovement;
use App\Models\ChemicalStock;
use Illuminate\Http\Request;

class ChemicalInventoryController extends Controller
{
    public function index(Request $request)
    {
        $location = $request->get('location', '');
        $tab = in_array($request->get('tab'), ['stock', 'movements'], true)
            ? $request->get('tab')
            : 'stock';
        $search = $request->filled('q') ? trim((string) $request->get('q')) : null;
        $period = $request->get('period');

        $stocks = null;
        $movements = null;
        $stockTotalQty = 0;
        $stockCount = 0;
        $movementCount = 0;
        $periods = ChemicalMovement::query()
            ->when($location, fn ($q) => $q->where('location', $location))
            ->whereNotNull('period_label')
            ->where('period_label', '!=', '')
            ->distinct()
            ->orderBy('period_label')
            ->pluck('period_label');

        if ($tab === 'stock') {
            // Prefer latest snapshot date(s) so Current Stock matches Excel "current" view
            $latestDates = ChemicalStock::query()
                ->when($location, fn ($q) => $q->where('location', $location))
                ->whereNotNull('count_date')
                ->selectRaw('location, MAX(count_date) as max_date')
                ->groupBy('location')
                ->pluck('max_date', 'location');

            $query = ChemicalStock::query()
                ->when($location, fn ($q) => $q->where('location', $location))
                ->when($latestDates->isNotEmpty(), function ($q) use ($latestDates) {
                    $q->where(function ($outer) use ($latestDates) {
                        foreach ($latestDates as $loc => $maxDate) {
                            $outer->orWhere(function ($inner) use ($loc, $maxDate) {
                                $inner->where('location', $loc)->whereDate('count_date', $maxDate);
                            });
                        }
                    });
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($inner) use ($search) {
                        $inner->where('item', 'like', '%'.$search.'%')
                            ->orWhere('counted_by', 'like', '%'.$search.'%')
                            ->orWhere('location_stored', 'like', '%'.$search.'%');
                    });
                })
                ->orderBy('location')
                ->orderBy('item');

            $stockTotalQty = (int) (clone $query)->sum('existing_count');
            $stockCount = (clone $query)->count();
            $stocks = $query->paginate(50)->withQueryString();
        } else {
            $query = ChemicalMovement::query()
                ->when($location, fn ($q) => $q->where('location', $location))
                ->when($period, fn ($q) => $q->where('period_label', $period))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($inner) use ($search) {
                        $inner->where('item', 'like', '%'.$search.'%')
                            ->orWhere('brand', 'like', '%'.$search.'%')
                            ->orWhere('moved_by', 'like', '%'.$search.'%')
                            ->orWhere('quantity_text', 'like', '%'.$search.'%');
                    });
                })
                ->orderByRaw('COALESCE(movement_date, created_at) DESC')
                ->orderBy('id', 'desc');

            $movementCount = (clone $query)->count();
            $movements = $query->paginate(50)->withQueryString();
        }

        $locations = ChemicalStock::locationOptions();

        return view('chemical-inventory.index', compact(
            'tab',
            'location',
            'locations',
            'stocks',
            'movements',
            'stockTotalQty',
            'stockCount',
            'movementCount',
            'periods',
            'period',
            'search'
        ));
    }

    public function storeStock(Request $request)
    {
        $data = $request->validate([
            'location' => 'required|in:Premium,Annex',
            'item' => 'required|string|max:255',
            'existing_count' => 'required|integer|min:0',
            'count_date' => 'nullable|date',
            'counted_by' => 'nullable|string|max:255',
            'location_stored' => 'nullable|string|max:255',
            'verified_by_photo' => 'nullable|string|max:50',
        ]);

        ChemicalStock::create($data);

        return redirect()
            ->route('chemical-inventory.index', ['tab' => 'stock', 'location' => $data['location']])
            ->with('success', 'Stock item added.');
    }

    public function updateStock(Request $request, ChemicalStock $chemicalStock)
    {
        $data = $request->validate([
            'location' => 'required|in:Premium,Annex',
            'item' => 'required|string|max:255',
            'existing_count' => 'required|integer|min:0',
            'count_date' => 'nullable|date',
            'counted_by' => 'nullable|string|max:255',
            'location_stored' => 'nullable|string|max:255',
            'verified_by_photo' => 'nullable|string|max:50',
        ]);

        $chemicalStock->update($data);

        return redirect()
            ->route('chemical-inventory.index', [
                'tab' => 'stock',
                'location' => $request->get('filter_location', $data['location']),
                'q' => $request->get('q'),
            ])
            ->with('success', 'Stock item updated.');
    }

    public function destroyStock(ChemicalStock $chemicalStock)
    {
        $location = $chemicalStock->location;
        $chemicalStock->delete();

        return redirect()
            ->route('chemical-inventory.index', ['tab' => 'stock', 'location' => $location])
            ->with('success', 'Stock item deleted.');
    }

    public function storeMovement(Request $request)
    {
        $data = $this->validatedMovement($request);
        ChemicalMovement::create($data);

        return redirect()
            ->route('chemical-inventory.index', ['tab' => 'movements', 'location' => $data['location']])
            ->with('success', 'Movement added.');
    }

    public function updateMovement(Request $request, ChemicalMovement $chemicalMovement)
    {
        $data = $this->validatedMovement($request);
        $chemicalMovement->update($data);

        return redirect()
            ->route('chemical-inventory.index', [
                'tab' => 'movements',
                'location' => $request->get('filter_location', $data['location']),
                'period' => $request->get('period'),
                'q' => $request->get('q'),
            ])
            ->with('success', 'Movement updated.');
    }

    public function destroyMovement(ChemicalMovement $chemicalMovement)
    {
        $location = $chemicalMovement->location;
        $chemicalMovement->delete();

        return redirect()
            ->route('chemical-inventory.index', ['tab' => 'movements', 'location' => $location])
            ->with('success', 'Movement deleted.');
    }

    private function validatedMovement(Request $request): array
    {
        $data = $request->validate([
            'location' => 'required|in:Premium,Annex',
            'period_label' => 'nullable|string|max:50',
            'item' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'size_or_pieces' => 'nullable|string|max:255',
            'movement_date' => 'nullable|date',
            'movement_type' => 'nullable|string|max:30',
            'quantity_text' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer',
            'moved_by' => 'nullable|string|max:255',
            'remaining_count' => 'nullable|integer',
        ]);

        if (($data['quantity'] ?? null) === null && ! empty($data['quantity_text'])) {
            if (preg_match('/(-?\d+)/', $data['quantity_text'], $m)) {
                $data['quantity'] = (int) $m[1];
            }
        }

        if (! empty($data['movement_type'])) {
            $data['movement_type'] = strtoupper(trim($data['movement_type']));
        }

        return $data;
    }
}
