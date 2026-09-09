@extends('layouts.app')

@section('title', 'Import Data - Settings')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a href="{{ route('settings') }}" class="btn btn-outline-light me-3">
            <i class="fas fa-chevron-left"></i>
        </a>
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/CAREMPIRE_LOGO.png') }}" alt="CAR EMPIRE Logo" onerror="this.style.display='none';">
        </a>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-file-import me-2"></i>Import Data</h2>
            <p class="text-muted mb-0">Upload an Excel workbook, select which tabs to import, review affected tables and pages, then confirm.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="importStepUpload">
                        <label class="form-label fw-semibold">Excel file (.xlsx)</label>
                        <input type="file" class="form-control" id="settingsImportFile" accept=".xlsx,.xls">
                        <div class="form-text">Max ~50MB. Large workbooks (Released units, Gas PO, Transfer) may take a minute to analyze.</div>
                    </div>

                    <div id="importDetected" class="d-none mt-3">
                        <div class="alert alert-info py-2 mb-0">
                            Detected: <strong id="importWorkbookLabel"></strong>
                            <span class="text-muted" id="importFileName"></span>
                            <div class="small mt-1" id="importDestination"></div>
                        </div>
                    </div>

                    <div id="importStepSheets" class="d-none mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">Select tabs to import</div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="importSelectAllSupported">Select all supported</button>
                        </div>
                        <div id="importSheetList" class="border rounded p-2" style="max-height: 320px; overflow:auto;"></div>
                    </div>

                    <div id="importStepSummary" class="d-none mt-3">
                        <div class="fw-semibold mb-2">Import impact summary</div>
                        <div class="row g-2 mb-3" id="importTotals"></div>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Excel tab</th>
                                        <th>Type</th>
                                        <th>Rows</th>
                                        <th>Create</th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody id="importTabsBody"></tbody>
                            </table>
                        </div>
                        <div class="fw-semibold mb-2">Affected database tables</div>
                        <div class="table-responsive mb-2">
                            <table class="table table-sm table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Table</th>
                                        <th>Action</th>
                                        <th>~Rows touched</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody id="importTablesBody"></tbody>
                            </table>
                        </div>
                        <ul class="small text-muted mb-0" id="importNotes"></ul>
                    </div>

                    <div id="importStepResult" class="d-none mt-3"></div>
                    <div id="importError" class="alert alert-danger d-none mt-3 mb-0"></div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="button" class="btn btn-primary" id="importAnalyzeBtn" disabled>
                            <i class="fas fa-search me-1"></i>Analyze selected tabs
                        </button>
                        <button type="button" class="btn btn-success d-none" id="importConfirmBtn">
                            <i class="fas fa-check me-1"></i>Confirm &amp; Import
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">Supported workbooks</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush small">
                        @foreach($catalog as $item)
                            <div class="list-group-item">
                                <div class="fw-semibold">{{ $item['label'] }}</div>
                                <div class="text-muted">Page: {{ $item['page'] }}</div>
                                <div class="text-muted"><code>{{ implode(', ', $item['tables']) }}</code></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function initSettingsDataImport() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const fileInput = document.getElementById('settingsImportFile');
    const sheetStep = document.getElementById('importStepSheets');
    const detectedEl = document.getElementById('importDetected');
    const summaryStep = document.getElementById('importStepSummary');
    const resultStep = document.getElementById('importStepResult');
    const sheetList = document.getElementById('importSheetList');
    const fileNameEl = document.getElementById('importFileName');
    const workbookLabelEl = document.getElementById('importWorkbookLabel');
    const destinationEl = document.getElementById('importDestination');
    const analyzeBtn = document.getElementById('importAnalyzeBtn');
    const confirmBtn = document.getElementById('importConfirmBtn');
    const errorEl = document.getElementById('importError');
    const totalsEl = document.getElementById('importTotals');
    const tabsBody = document.getElementById('importTabsBody');
    const tablesBody = document.getElementById('importTablesBody');
    const notesEl = document.getElementById('importNotes');
    const selectAllBtn = document.getElementById('importSelectAllSupported');

    let importToken = null;
    let workbookType = null;
    let selectedSheets = [];

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, function (m) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m];
        });
    }
    function showError(msg) {
        errorEl.textContent = msg || 'Something went wrong.';
        errorEl.classList.remove('d-none');
    }
    function clearError() {
        errorEl.classList.add('d-none');
        errorEl.textContent = '';
    }

    fileInput.addEventListener('change', async function () {
        clearError();
        summaryStep.classList.add('d-none');
        resultStep.classList.add('d-none');
        confirmBtn.classList.add('d-none');
        sheetStep.classList.add('d-none');
        detectedEl.classList.add('d-none');
        const file = fileInput.files && fileInput.files[0];
        if (!file) return;

        const form = new FormData();
        form.append('file', file);
        analyzeBtn.disabled = true;
        analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Reading Excel…';

        try {
            const res = await fetch(@json(route('settings.import-data.upload')), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: form
            });
            const data = await res.json();
            if (!res.ok || !data.ok) {
                throw new Error(data.message || 'Upload failed');
            }
            importToken = data.token;
            workbookType = data.workbook_type || null;
            fileNameEl.textContent = ' — ' + (data.original_name || file.name);
            workbookLabelEl.textContent = data.workbook_label || 'Unknown workbook';
            if (data.page) {
                destinationEl.innerHTML = 'Displays on: <strong>' + escapeHtml(data.page.label || '') + '</strong>' +
                    (data.tables && data.tables.length ? ' · Tables: <code>' + escapeHtml(data.tables.join(', ')) + '</code>' : '');
            } else {
                destinationEl.textContent = data.message || '';
            }
            detectedEl.classList.remove('d-none');

            sheetList.innerHTML = '';
            (data.sheets || []).forEach(function (sheet) {
                const id = 'sheet_' + btoa(unescape(encodeURIComponent(sheet.name))).replace(/=+/g, '');
                const disabled = !sheet.supported;
                const wrap = document.createElement('div');
                wrap.className = 'form-check py-1 border-bottom';
                wrap.innerHTML =
                    '<input class="form-check-input import-sheet-check" type="checkbox" value="' + escapeHtml(sheet.name) + '" id="' + id + '" ' + (disabled ? 'disabled' : 'checked') + '>' +
                    '<label class="form-check-label w-100" for="' + id + '">' +
                    '<div class="d-flex justify-content-between gap-2">' +
                    '<span><strong>' + escapeHtml(sheet.name) + '</strong>' +
                    (sheet.supported
                        ? ' <span class="badge bg-success ms-1">Supported</span>'
                        : ' <span class="badge bg-secondary ms-1">Unsupported</span>') +
                    '</span>' +
                    '<span class="text-muted small">' + Number(sheet.excel_rows || 0).toLocaleString() + ' rows</span>' +
                    '</div>' +
                    (sheet.note ? '<div class="small text-muted">' + escapeHtml(sheet.note) + '</div>' : '') +
                    (sheet.supported && sheet.tables ? '<div class="small text-muted">Tables: ' + escapeHtml((sheet.tables || []).join(', ')) + '</div>' : '') +
                    '</label>';
                sheetList.appendChild(wrap);
            });
            sheetStep.classList.remove('d-none');
            analyzeBtn.disabled = !workbookType;
            if (!workbookType) {
                showError(data.message || 'Workbook type not recognized.');
            }
        } catch (err) {
            showError(err.message || String(err));
        } finally {
            analyzeBtn.innerHTML = '<i class="fas fa-search me-1"></i>Analyze selected tabs';
        }
    });

    selectAllBtn.addEventListener('click', function () {
        sheetList.querySelectorAll('.import-sheet-check:not(:disabled)').forEach(function (el) {
            el.checked = true;
        });
    });

    analyzeBtn.addEventListener('click', async function () {
        clearError();
        selectedSheets = Array.from(sheetList.querySelectorAll('.import-sheet-check:checked')).map(function (el) {
            return el.value;
        });
        if (!importToken || !workbookType || selectedSheets.length === 0) {
            showError('Select at least one supported Excel tab.');
            return;
        }
        analyzeBtn.disabled = true;
        analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Analyzing…';
        try {
            const res = await fetch(@json(route('settings.import-data.analyze')), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    token: importToken,
                    workbook_type: workbookType,
                    sheets: selectedSheets
                })
            });
            const data = await res.json();
            if (!res.ok || !data.ok) {
                throw new Error(data.message || 'Analyze failed');
            }
            const summary = data.summary || {};
            const totals = summary.totals || {};
            const rowsProcessed = Number(totals.rows_to_process || totals.excel_rows || 0);
            totalsEl.innerHTML =
                '<div class="col-md-4"><div class="border rounded p-2"><div class="small text-muted">Rows to process</div><div class="fs-5 fw-bold">' + rowsProcessed.toLocaleString() + '</div></div></div>' +
                '<div class="col-md-4"><div class="border rounded p-2"><div class="small text-muted">Will create (units)</div><div class="fs-5 fw-bold text-success">' + Number(totals.will_create_vehicles || 0).toLocaleString() + '</div></div></div>' +
                '<div class="col-md-4"><div class="border rounded p-2"><div class="small text-muted">Will update (units)</div><div class="fs-5 fw-bold text-primary">' + Number(totals.will_update_vehicles || 0).toLocaleString() + '</div></div></div>';

            if (summary.page) {
                destinationEl.innerHTML = 'Displays on: <strong>' + escapeHtml(summary.page.label || '') + '</strong>';
            }

            tabsBody.innerHTML = (summary.tabs || []).map(function (t) {
                return '<tr>' +
                    '<td>' + escapeHtml(t.name) + '</td>' +
                    '<td>' + escapeHtml(t.status || '') + (t.branch ? ' / ' + escapeHtml(t.branch) : '') + '</td>' +
                    '<td>' + Number(t.excel_rows || 0).toLocaleString() + '</td>' +
                    '<td>' + (t.will_create_vehicles == null ? '—' : Number(t.will_create_vehicles || 0).toLocaleString()) + '</td>' +
                    '<td>' + (t.will_update_vehicles == null ? '—' : Number(t.will_update_vehicles || 0).toLocaleString()) + '</td>' +
                    '</tr>';
            }).join('');

            tablesBody.innerHTML = (summary.tables || []).map(function (t) {
                return '<tr>' +
                    '<td><code>' + escapeHtml(t.table) + '</code></td>' +
                    '<td>' + escapeHtml(t.action) + '</td>' +
                    '<td>' + Number(t.approx_rows_touched || 0).toLocaleString() + '</td>' +
                    '<td class="small">' + escapeHtml(t.description || '') + '</td>' +
                    '</tr>';
            }).join('');

            notesEl.innerHTML = (summary.notes || []).map(function (n) {
                return '<li>' + escapeHtml(n) + '</li>';
            }).join('');

            summaryStep.classList.remove('d-none');
            confirmBtn.classList.remove('d-none');
        } catch (err) {
            showError(err.message || String(err));
        } finally {
            analyzeBtn.disabled = false;
            analyzeBtn.innerHTML = '<i class="fas fa-search me-1"></i>Analyze selected tabs';
        }
    });

    confirmBtn.addEventListener('click', async function () {
        clearError();
        if (!importToken || !workbookType || selectedSheets.length === 0) {
            showError('Nothing to import.');
            return;
        }
        if (!window.confirm('Proceed with importing the selected Excel tabs? Existing matching records may be updated.')) {
            return;
        }

        confirmBtn.disabled = true;
        analyzeBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Importing…';
        try {
            const res = await fetch(@json(route('settings.import-data.confirm')), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    token: importToken,
                    workbook_type: workbookType,
                    sheets: selectedSheets,
                    confirm: 1
                })
            });
            const data = await res.json();
            resultStep.classList.remove('d-none');
            if (!res.ok || !data.ok) {
                resultStep.innerHTML = '<div class="alert alert-warning mb-0"><strong>' + escapeHtml(data.message || 'Import finished with errors') + '</strong></div>';
            } else {
                resultStep.innerHTML = '<div class="alert alert-success mb-0"><strong>' + escapeHtml(data.message || 'Import completed') + '</strong></div>';
            }
            const results = data.results || [];
            if (results.length) {
                resultStep.innerHTML += '<div class="table-responsive mt-2"><table class="table table-sm"><thead><tr><th>Sheet</th><th>Command</th><th>Rows</th><th>Exit</th></tr></thead><tbody>' +
                    results.map(function (r) {
                        return '<tr><td>' + escapeHtml(r.sheet) + '</td><td><code>' + escapeHtml(r.command) + '</code></td><td>' + Number(r.rows || 0).toLocaleString() + '</td><td>' + Number(r.exit_code) + '</td></tr>';
                    }).join('') + '</tbody></table></div>';
            }
        } catch (err) {
            showError(err.message || String(err));
        } finally {
            confirmBtn.disabled = false;
            analyzeBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check me-1"></i>Confirm &amp; Import';
        }
    });
})();
</script>
@endsection
