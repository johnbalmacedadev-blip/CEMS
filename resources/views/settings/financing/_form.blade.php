<div class="row g-2">
    <div class="col-md-12">
        <label class="form-label fw-bold">Year Model Range</label>
        <input type="text" class="form-control" name="year_model_range" value="{{ $setting ? $setting->year_model_range : '' }}" placeholder="e.g. 2026-2022" required>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold">CHMF % (of AF)</label>
        <div class="input-group">
            <input type="number" class="form-control" name="chattel_fee_percent" step="0.01" min="0" max="100" value="{{ $setting && $setting->chattel_fee_percent !== null ? $setting->chattel_fee_percent : '8' }}" placeholder="8">
            <span class="input-group-text">%</span>
        </div>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold">CHMF override</label>
        <input type="number" class="form-control" name="chattel_fee" step="0.01" min="0" value="{{ $setting ? $setting->chattel_fee : '0' }}" placeholder="0 = use %">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold">Insurance</label>
        <input type="number" class="form-control" name="insurance_initial" step="0.01" min="0" value="{{ $setting ? $setting->insurance_initial : '10000' }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold">NO PDC</label>
        <input type="number" class="form-control" name="no_pdc_charge" step="0.01" min="0" value="{{ $setting ? $setting->no_pdc_charge : '1500' }}" required>
    </div>
    <div class="col-12"><hr class="my-2"><span class="small fw-bold">Term % (12–60 mo)</span></div>
    <div class="col-md-4">
        <label class="form-label">12 Months</label>
        <input type="number" class="form-control" name="term_pct_12" step="0.01" min="0" max="100" value="{{ $setting ? round($setting->term_pct_12 * 100, 2) : '15.30' }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">24 Months</label>
        <input type="number" class="form-control" name="term_pct_24" step="0.01" min="0" max="100" value="{{ $setting ? round($setting->term_pct_24 * 100, 2) : '30.60' }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">36 Months</label>
        <input type="number" class="form-control" name="term_pct_36" step="0.01" min="0" max="100" value="{{ $setting ? round($setting->term_pct_36 * 100, 2) : '45.90' }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">48 Months</label>
        <input type="number" class="form-control" name="term_pct_48" step="0.01" min="0" max="100" value="{{ $setting ? round($setting->term_pct_48 * 100, 2) : '61.20' }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">60 Months</label>
        <input type="number" class="form-control" name="term_pct_60" step="0.01" min="0" max="100" value="{{ $setting ? round($setting->term_pct_60 * 100, 2) : '72.00' }}" required>
    </div>
</div>
