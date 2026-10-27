@if(($assetMode ?? '') === 'styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<style>
    #vehicle_id + .ts-wrapper,
    .ts-wrapper.form-select {
        width: 100%;
    }
    .ts-dropdown {
        z-index: 1060;
    }
</style>
@elseif(($assetMode ?? '') === 'scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
(function () {
    var el = document.getElementById('vehicle_id');
    if (!el || typeof TomSelect === 'undefined') return;
    if (el.tomselect) return;

    new TomSelect(el, {
        create: false,
        allowEmptyOption: true,
        maxOptions: null,
        placeholder: 'Search plate, make, or model…',
        sortField: { field: 'text', direction: 'asc' },
        plugins: ['clear_button'],
        searchField: ['text'],
    });
})();
</script>
@endif
