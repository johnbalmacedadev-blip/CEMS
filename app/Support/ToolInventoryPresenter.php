<?php

namespace App\Support;

use App\Models\Tool;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ToolInventoryPresenter
{
    /**
     * Latest inventory snapshot rows (remaining qty per tool).
     *
     * @return array{as_of:?string, items:Collection<int, array{name:string, quantity:int, date:string}>}
     */
    public static function currentInventory(?string $search = null): array
    {
        $latestDate = Tool::query()
            ->where('entry_type', 'inventory')
            ->max('date_acquired');

        if (! $latestDate) {
            // Fallback: sum purchases if no snapshots yet
            $items = Tool::query()
                ->where('entry_type', 'purchase')
                ->when($search, fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
                ->get()
                ->groupBy(fn ($t) => mb_strtoupper(trim($t->name)))
                ->map(function ($group) {
                    $first = $group->first();

                    return [
                        'name' => $first->name,
                        'quantity' => (int) $group->sum('quantity'),
                        'date' => optional($group->max('date_acquired'))->format('Y-m-d'),
                    ];
                })
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            return [
                'as_of' => $items->max('date'),
                'items' => $items,
            ];
        }

        $asOf = Carbon::parse($latestDate)->toDateString();
        $items = Tool::query()
            ->where('entry_type', 'inventory')
            ->whereDate('date_acquired', $asOf)
            ->when($search, fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->orderBy('name')
            ->get()
            ->map(fn (Tool $t) => [
                'name' => $t->name,
                'quantity' => (int) $t->quantity,
                'date' => $asOf,
            ])
            ->values();

        return [
            'as_of' => $asOf,
            'items' => $items,
        ];
    }

    /**
     * Purchases + inventory quantity changes between snapshot dates.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function movements(?string $search = null, ?string $dateFrom = null, ?string $dateTo = null): Collection
    {
        $movements = collect();

        $purchases = Tool::query()
            ->where('entry_type', 'purchase')
            ->when($search, fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->when($dateFrom, fn ($q) => $q->whereDate('date_acquired', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('date_acquired', '<=', $dateTo))
            ->orderBy('date_acquired')
            ->orderBy('id')
            ->get();

        foreach ($purchases as $p) {
            $movements->push([
                'date' => $p->date_acquired->format('Y-m-d'),
                'type' => 'Purchase',
                'name' => $p->name,
                'quantity' => (int) $p->quantity,
                'amount' => (float) $p->amount,
                'notes' => 'Purchase recorded',
                'tool_id' => $p->id,
            ]);
        }

        $dates = Tool::query()
            ->where('entry_type', 'inventory')
            ->when($dateFrom, fn ($q) => $q->whereDate('date_acquired', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('date_acquired', '<=', $dateTo))
            ->select('date_acquired')
            ->distinct()
            ->orderBy('date_acquired')
            ->pluck('date_acquired')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->values()
            ->all();

        // Also include one snapshot before date_from as baseline for diffs
        if ($dateFrom && $dates !== []) {
            $baseline = Tool::query()
                ->where('entry_type', 'inventory')
                ->whereDate('date_acquired', '<', $dateFrom)
                ->max('date_acquired');
            if ($baseline) {
                array_unshift($dates, Carbon::parse($baseline)->toDateString());
                $dates = array_values(array_unique($dates));
                sort($dates);
            }
        }

        $prevMap = null;
        $prevDate = null;
        foreach ($dates as $date) {
            $rows = Tool::query()
                ->where('entry_type', 'inventory')
                ->whereDate('date_acquired', $date)
                ->get();

            $currMap = [];
            foreach ($rows as $row) {
                $key = mb_strtoupper(trim($row->name));
                $currMap[$key] = [
                    'name' => $row->name,
                    'quantity' => (int) $row->quantity,
                ];
            }

            if ($prevMap !== null) {
                $allKeys = array_unique(array_merge(array_keys($prevMap), array_keys($currMap)));
                foreach ($allKeys as $key) {
                    $prevQty = $prevMap[$key]['quantity'] ?? 0;
                    $currQty = $currMap[$key]['quantity'] ?? 0;
                    $name = $currMap[$key]['name'] ?? $prevMap[$key]['name'];

                    if ($search && stripos($name, $search) === false) {
                        continue;
                    }

                    if ($prevQty === $currQty) {
                        continue;
                    }

                    if ($prevQty === 0 && $currQty > 0) {
                        $type = 'Added to inventory';
                        $delta = $currQty;
                        $notes = "New on inventory as of {$date} (was not on {$prevDate})";
                    } elseif ($currQty === 0 && $prevQty > 0) {
                        $type = 'Removed from inventory';
                        $delta = -$prevQty;
                        $notes = "Removed between {$prevDate} and {$date}";
                    } elseif ($currQty > $prevQty) {
                        $type = 'Quantity increased';
                        $delta = $currQty - $prevQty;
                        $notes = "{$prevQty} → {$currQty} ({$prevDate} → {$date})";
                    } else {
                        $type = 'Quantity decreased';
                        $delta = $currQty - $prevQty;
                        $notes = "{$prevQty} → {$currQty} ({$prevDate} → {$date})";
                    }

                    // Respect date filter for the movement date (use current snapshot date)
                    if ($dateFrom && $date < $dateFrom) {
                        continue;
                    }
                    if ($dateTo && $date > $dateTo) {
                        continue;
                    }

                    $movements->push([
                        'date' => $date,
                        'type' => $type,
                        'name' => $name,
                        'quantity' => $delta,
                        'amount' => null,
                        'notes' => $notes,
                        'tool_id' => null,
                    ]);
                }
            }

            $prevMap = $currMap;
            $prevDate = $date;
        }

        return $movements
            ->sortByDesc(fn ($m) => $m['date'].'|'.$m['type'].'|'.$m['name'])
            ->values();
    }
}
