<?php

namespace App\Http\Controllers;

use App\Models\CarFinancingSetting;
use App\Models\FinancingScheme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarFinancingSettingController extends Controller
{
    /**
     * Show the car financing rules (schemes) and calculator per scheme.
     */
    public function index(Request $request)
    {
        $schemes = FinancingScheme::orderBy('sort_order')->orderBy('name')->get();
        $currentSchemeId = $request->get('scheme', $schemes->first()?->id);
        $currentScheme = $schemes->firstWhere('id', (int) $currentSchemeId) ?? $schemes->first();
        $settings = $currentScheme
            ? CarFinancingSetting::where('financing_scheme_id', $currentScheme->id)->orderBy('year_model_range')->get()
            : collect();

        return view('settings.financing', compact('schemes', 'currentScheme', 'settings'));
    }

    /**
     * Store a new year model range setting for a scheme.
     */
    public function store(Request $request)
    {
        $schemeId = $request->validate(['financing_scheme_id' => 'required|exists:financing_schemes,id'])['financing_scheme_id'];

        $request->validate([
            'year_model_range' => ['required', 'string', 'max:50', Rule::unique('car_financing_settings', 'year_model_range')->where('financing_scheme_id', $schemeId)],
            'chattel_fee_percent' => 'nullable|numeric|min:0|max:100',
            'chattel_fee' => 'nullable|numeric|min:0',
            'insurance_initial' => 'required|numeric|min:0',
            'no_pdc_charge' => 'required|numeric|min:0',
            'term_pct_12' => 'required|numeric|min:0|max:100',
            'term_pct_24' => 'required|numeric|min:0|max:100',
            'term_pct_36' => 'required|numeric|min:0|max:100',
            'term_pct_48' => 'required|numeric|min:0|max:100',
            'term_pct_60' => 'required|numeric|min:0|max:100',
        ]);

        CarFinancingSetting::create([
            'financing_scheme_id' => $schemeId,
            'year_model_range' => $request->year_model_range,
            'chattel_fee_percent' => $request->chattel_fee_percent !== null && $request->chattel_fee_percent !== '' ? $request->chattel_fee_percent : null,
            'chattel_fee' => $request->chattel_fee ?? 0,
            'insurance_initial' => $request->insurance_initial,
            'no_pdc_charge' => $request->no_pdc_charge,
            'term_pct_12' => $request->term_pct_12 / 100,
            'term_pct_24' => $request->term_pct_24 / 100,
            'term_pct_36' => $request->term_pct_36 / 100,
            'term_pct_48' => $request->term_pct_48 / 100,
            'term_pct_60' => $request->term_pct_60 / 100,
        ]);

        return redirect()->route('settings.financing.index', ['scheme' => $schemeId])
            ->with('success', 'Financing setting added successfully.');
    }

    /**
     * Update an existing year model range setting.
     */
    public function update(Request $request, CarFinancingSetting $carFinancingSetting)
    {
        $request->validate([
            'year_model_range' => ['required', 'string', 'max:50', Rule::unique('car_financing_settings', 'year_model_range')->ignore($carFinancingSetting->id)->where('financing_scheme_id', $carFinancingSetting->financing_scheme_id)],
            'chattel_fee_percent' => 'nullable|numeric|min:0|max:100',
            'chattel_fee' => 'nullable|numeric|min:0',
            'insurance_initial' => 'required|numeric|min:0',
            'no_pdc_charge' => 'required|numeric|min:0',
            'term_pct_12' => 'required|numeric|min:0|max:100',
            'term_pct_24' => 'required|numeric|min:0|max:100',
            'term_pct_36' => 'required|numeric|min:0|max:100',
            'term_pct_48' => 'required|numeric|min:0|max:100',
            'term_pct_60' => 'required|numeric|min:0|max:100',
        ]);

        $carFinancingSetting->update([
            'year_model_range' => $request->year_model_range,
            'chattel_fee_percent' => $request->chattel_fee_percent !== null && $request->chattel_fee_percent !== '' ? $request->chattel_fee_percent : null,
            'chattel_fee' => $request->chattel_fee ?? 0,
            'insurance_initial' => $request->insurance_initial,
            'no_pdc_charge' => $request->no_pdc_charge,
            'term_pct_12' => $request->term_pct_12 / 100,
            'term_pct_24' => $request->term_pct_24 / 100,
            'term_pct_36' => $request->term_pct_36 / 100,
            'term_pct_48' => $request->term_pct_48 / 100,
            'term_pct_60' => $request->term_pct_60 / 100,
        ]);

        return redirect()->route('settings.financing.index', ['scheme' => $carFinancingSetting->financing_scheme_id])
            ->with('success', 'Financing setting updated successfully.');
    }

    /**
     * Remove a year model range setting.
     */
    public function destroy(CarFinancingSetting $carFinancingSetting)
    {
        $schemeId = $carFinancingSetting->financing_scheme_id;
        $carFinancingSetting->delete();
        return redirect()->route('settings.financing.index', ['scheme' => $schemeId])
            ->with('success', 'Financing setting removed.');
    }

    /**
     * Store a new financing scheme (rule).
     */
    public function storeScheme(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);

        $scheme = FinancingScheme::create([
            'name' => $request->name,
            'sort_order' => (FinancingScheme::max('sort_order') ?? 0) + 1,
        ]);

        return redirect()->route('settings.financing.index', ['scheme' => $scheme->id])
            ->with('success', 'Financing rule added.');
    }

    /**
     * Update a financing scheme.
     */
    public function updateScheme(Request $request, FinancingScheme $financing_scheme)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $financing_scheme->update(['name' => $request->name]);
        return redirect()->route('settings.financing.index', ['scheme' => $financing_scheme->id])
            ->with('success', 'Financing rule updated.');
    }

    /**
     * Remove a financing scheme and its year ranges.
     */
    public function destroyScheme(FinancingScheme $financing_scheme)
    {
        $financing_scheme->delete();
        return redirect()->route('settings.financing.index')
            ->with('success', 'Financing rule removed.');
    }
}
