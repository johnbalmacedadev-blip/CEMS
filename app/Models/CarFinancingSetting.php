<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarFinancingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'financing_scheme_id',
        'year_model_range',
        'chattel_fee',
        'chattel_fee_percent',
        'insurance_initial',
        'no_pdc_charge',
        'term_pct_12',
        'term_pct_24',
        'term_pct_36',
        'term_pct_48',
        'term_pct_60',
    ];

    protected $casts = [
        'chattel_fee' => 'decimal:2',
        'chattel_fee_percent' => 'decimal:2',
        'insurance_initial' => 'decimal:2',
        'no_pdc_charge' => 'decimal:2',
        'term_pct_12' => 'decimal:4',
        'term_pct_24' => 'decimal:4',
        'term_pct_36' => 'decimal:4',
        'term_pct_48' => 'decimal:4',
        'term_pct_60' => 'decimal:4',
    ];

    public function financingScheme()
    {
        return $this->belongsTo(FinancingScheme::class, 'financing_scheme_id');
    }

    /**
     * Parse year_model_range (e.g. "2014-2001" or "2026-2022") to [minYear, maxYear]. Returns [null, null] if unparseable.
     */
    public function getYearRange(): array
    {
        $parts = array_map('intval', array_filter(preg_split('/\s*-\s*/', trim((string) $this->year_model_range))));
        if (count($parts) < 2) {
            $single = (int) trim((string) $this->year_model_range);
            return $single > 0 ? [$single, $single] : [null, null];
        }
        return [min($parts[0], $parts[1]), max($parts[0], $parts[1])];
    }

    /**
     * Check if the given year falls within this setting's year_model_range (e.g. "2026-2022" or "2014-2001").
     */
    public function yearInRange(int $year): bool
    {
        [$min, $max] = $this->getYearRange();
        if ($min === null || $max === null) {
            return false;
        }
        return $year >= $min && $year <= $max;
    }

    /**
     * Find a setting for the given vehicle year. Optionally limit to a financing scheme.
     * 1) If a range contains the year (e.g. 2021 in 2024-2001), use it.
     * 2) Else use the range whose max year is the largest that is still <= vehicle year (e.g. 2021 → use 2014-2001).
     * 3) Else use the range with the smallest min year (oldest bracket).
     */
    public static function findForYear(int $year, ?int $schemeId = null): ?self
    {
        $query = self::query();
        if ($schemeId !== null) {
            $query->where('financing_scheme_id', $schemeId);
        }
        $settings = $query->orderBy('year_model_range')->get();
        if ($settings->isEmpty()) {
            return null;
        }

        foreach ($settings as $setting) {
            if ($setting->yearInRange($year)) {
                return $setting;
            }
        }

        $withMinMax = $settings->map(function ($s) {
            [$min, $max] = $s->getYearRange();
            return ['setting' => $s, 'min' => $min ?? 0, 'max' => $max ?? 0];
        })->filter(fn ($x) => $x['min'] > 0 || $x['max'] > 0);

        $below = $withMinMax->filter(fn ($x) => $x['max'] <= $year);
        if ($below->isNotEmpty()) {
            return $below->sortByDesc('max')->first()['setting'];
        }

        return $withMinMax->sortBy('min')->first()['setting'] ?? $settings->first();
    }

    /**
     * Amount Financed = Unit Price - Down Payment
     */
    public function amountFinanced(float $unitPrice, float $downPayment): float
    {
        return max(0, $unitPrice - $downPayment);
    }

    /**
     * CHMF (Chattel Fee) = Amount Financed * chattel_fee_percent / 100 when percent is set; else fixed chattel_fee.
     */
    public function chattelFeeFromAf(float $amountFinanced): float
    {
        if ($this->chattel_fee_percent !== null && $this->chattel_fee_percent > 0) {
            return round($amountFinanced * (float) $this->chattel_fee_percent / 100, 2);
        }
        return (float) $this->chattel_fee;
    }

    /**
     * All In DP = Down Payment + CHMF + Insurance + No PDC
     */
    public function allInDownPayment(float $downPayment, float $amountFinanced = 0): float
    {
        $chmf = $amountFinanced > 0 ? $this->chattelFeeFromAf($amountFinanced) : (float) $this->chattel_fee;
        return $downPayment + $chmf + (float) $this->insurance_initial + (float) $this->no_pdc_charge;
    }

    /**
     * Monthly payment for a given term: (Amount Financed * (1 + term %)) / months
     */
    public function monthlyPayment(float $amountFinanced, int $months): float
    {
        $pct = match ($months) {
            12 => (float) $this->term_pct_12,
            24 => (float) $this->term_pct_24,
            36 => (float) $this->term_pct_36,
            48 => (float) $this->term_pct_48,
            60 => (float) $this->term_pct_60,
            default => 0,
        };
        $totalRepayment = $amountFinanced * (1 + $pct);
        return $months > 0 ? round($totalRepayment / $months, 2) : 0;
    }

    /**
     * Term percentage as display value (e.g. 15.30 for 15.30%)
     */
    public function termPctDisplay(int $months): float
    {
        $pct = match ($months) {
            12 => (float) $this->term_pct_12,
            24 => (float) $this->term_pct_24,
            36 => (float) $this->term_pct_36,
            48 => (float) $this->term_pct_48,
            60 => (float) $this->term_pct_60,
            default => 0,
        };
        return round($pct * 100, 2);
    }
}
