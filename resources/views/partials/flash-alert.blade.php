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



    function showFlashAlert(attempt) {

        attempt = attempt || 0;



        if (shown || !window.__ceFlashAlert) {

            return;

        }



        if (typeof Swal === 'undefined') {

            if (attempt < 40) {

                window.setTimeout(function () { showFlashAlert(attempt + 1); }, 50);

            }

            return;

        }



        shown = true;

        var data = window.__ceFlashAlert;

        window.__ceFlashAlert = null;



        if (window.CarEmpirePreloader && typeof window.CarEmpirePreloader.hide === 'function') {

            window.CarEmpirePreloader.hide();

        }



        Swal.fire({

            icon: data.icon || 'success',

            title: data.title || 'Success',

            text: data.text || '',

            confirmButtonText: 'OK',

            confirmButtonColor: data.icon === 'error' ? '#dc3545' : '#198754',

        });

    }



    function scheduleFlashAlert() {

        window.setTimeout(function () { showFlashAlert(0); }, 80);

    }



    if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', scheduleFlashAlert, { once: true });

    } else {

        scheduleFlashAlert();

    }



    window.addEventListener('load', scheduleFlashAlert, { once: true });



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

