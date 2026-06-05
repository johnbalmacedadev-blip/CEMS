@php
    $ceFlashAlert = null;
    if (session('success')) {
        $ceFlashAlert = [
            'icon' => 'success',
            'title' => session('swal_title', 'Success'),
            'text' => session('success'),
        ];
    } elseif (session('error')) {
        $ceFlashAlert = [
            'icon' => 'error',
            'title' => session('swal_title', 'Error'),
            'text' => session('error'),
        ];
    }
@endphp
@if($ceFlashAlert)
<style>
    .swal2-container { z-index: 10060 !important; }
</style>
<script>
window.__ceFlashAlert = @json($ceFlashAlert);
</script>
<script>
(function () {
    var shown = false;

    function showFlashAlert() {
        if (shown || !window.__ceFlashAlert || typeof Swal === 'undefined') {
            return;
        }
        shown = true;
        var data = window.__ceFlashAlert;
        window.__ceFlashAlert = null;

        Swal.fire({
            icon: data.icon || 'success',
            title: data.title || 'Success',
            text: data.text || '',
            confirmButtonText: 'OK',
            confirmButtonColor: data.icon === 'error' ? '#dc3545' : '#198754',
        });
    }

    function scheduleFlashAlert() {
        if (window.CarEmpirePreloader && typeof window.CarEmpirePreloader.hide === 'function') {
            window.CarEmpirePreloader.hide();
        }
        window.setTimeout(showFlashAlert, 120);
    }

    if (document.readyState === 'complete') {
        scheduleFlashAlert();
    } else {
        window.addEventListener('load', scheduleFlashAlert, { once: true });
    }

    window.addEventListener('pageshow', function (event) {
        if (!window.__ceFlashAlert) {
            return;
        }
        if (event.persisted) {
            scheduleFlashAlert();
        }
    });
})();
</script>
@endif
