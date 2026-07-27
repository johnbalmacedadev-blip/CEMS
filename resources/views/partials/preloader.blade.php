<div id="cePreloader" class="ce-preloader" aria-hidden="true" aria-live="polite" aria-busy="false">
    <div class="ce-preloader-panel">
        <div class="ce-preloader-scene">
            <div class="ce-preloader-icon-wrap">
                <div class="ce-preloader-ring" aria-hidden="true"></div>
                <i class="fas fa-car ce-preloader-car" aria-hidden="true"></i>
            </div>
            <div class="ce-preloader-track">
                <span class="ce-preloader-dash"></span>
                <span class="ce-preloader-dash"></span>
                <span class="ce-preloader-dash"></span>
                <span class="ce-preloader-dash"></span>
            </div>
        </div>
        <p class="ce-preloader-label">Loading...</p>
    </div>
</div>

<style>
    .ce-preloader {
        position: fixed;
        inset: 0;
        z-index: 10050;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(15, 15, 15, 0.72);
        backdrop-filter: blur(3px);
        opacity: 0;
        transition: opacity 0.2s ease;
        pointer-events: none;
    }

    .ce-preloader.active {
        display: flex;
        opacity: 1;
        pointer-events: all;
    }

    .ce-preloader-panel {
        text-align: center;
        color: #fff;
        user-select: none;
    }

    .ce-preloader-scene {
        position: relative;
        width: 220px;
        height: 96px;
        margin: 0 auto 0.75rem;
    }

    .ce-preloader-icon-wrap {
        position: relative;
        width: 88px;
        height: 88px;
        margin: 0 auto;
    }

    .ce-preloader-ring {
        position: absolute;
        inset: 0;
        border: 3px solid rgba(255, 255, 255, 0.15);
        border-top-color: #dc3545;
        border-right-color: #dc3545;
        border-radius: 50%;
        animation: ce-ring-spin 0.85s linear infinite;
    }

    .ce-preloader-car {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        font-size: 2.25rem;
        color: #dc3545;
        filter: drop-shadow(0 4px 8px rgba(220, 53, 69, 0.45));
        animation: ce-car-bounce 0.55s ease-in-out infinite alternate;
        z-index: 2;
    }

    .ce-preloader-track {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 6px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
    }

    .ce-preloader-dash {
        position: absolute;
        top: 1px;
        width: 28px;
        height: 4px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.85);
        animation: ce-road-scroll 0.65s linear infinite;
    }

    .ce-preloader-dash:nth-child(1) { left: -28px; animation-delay: 0s; }
    .ce-preloader-dash:nth-child(2) { left: 38px; animation-delay: -0.16s; }
    .ce-preloader-dash:nth-child(3) { left: 104px; animation-delay: -0.32s; }
    .ce-preloader-dash:nth-child(4) { left: 170px; animation-delay: -0.48s; }

    .ce-preloader-label {
        margin: 0;
        font-size: 0.95rem;
        letter-spacing: 0.04em;
        font-weight: 600;
        animation: ce-label-pulse 1.2s ease-in-out infinite;
    }

    @keyframes ce-car-bounce {
        from { transform: translate(-50%, -50%) scale(1); }
        to { transform: translate(-50%, calc(-50% - 4px)) scale(1.04); }
    }

    @keyframes ce-ring-spin {
        to { transform: rotate(360deg); }
    }

    @keyframes ce-road-scroll {
        from { transform: translateX(0); opacity: 0.35; }
        50% { opacity: 1; }
        to { transform: translateX(-220px); opacity: 0.35; }
    }

    @keyframes ce-label-pulse {
        0%, 100% { opacity: 0.65; }
        50% { opacity: 1; }
    }
</style>

<script>
(function () {
    const preloader = document.getElementById('cePreloader');
    if (!preloader) return;

    let hideTimer = null;
    let shownAt = 0;

    function showPreloader() {
        clearTimeout(hideTimer);
        shownAt = Date.now();
        preloader.classList.add('active');
        preloader.setAttribute('aria-hidden', 'false');
        preloader.setAttribute('aria-busy', 'true');
        hideTimer = setTimeout(hidePreloader, 45000);
    }

    function hidePreloader() {
        const elapsed = Date.now() - shownAt;
        const finish = function () {
            preloader.classList.remove('active');
            preloader.setAttribute('aria-hidden', 'true');
            preloader.setAttribute('aria-busy', 'false');
            clearTimeout(hideTimer);
        };

        if (shownAt && elapsed < 250) {
            setTimeout(finish, 250 - elapsed);
            return;
        }

        finish();
    }

    function shouldShowForLink(link) {
        if (!link || !link.href) return false;
        if (link.hasAttribute('data-no-preloader')) return false;
        if (link.target === '_blank') return false;
        if (link.hasAttribute('download')) return false;
        if (link.dataset.bsToggle || link.dataset.toggle) return false;
        if (link.getAttribute('role') === 'tab') return false;

        const href = link.getAttribute('href') || '';
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:')) return false;

        try {
            const url = new URL(link.href, window.location.href);
            if (url.protocol === 'mailto:' || url.protocol === 'tel:') return false;
            if (url.origin !== window.location.origin) return false;
            if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return false;
        } catch (error) {
            return false;
        }

        return true;
    }

    function shouldShowForForm(form) {
        if (!form || form.tagName !== 'FORM') return false;
        if (form.hasAttribute('data-no-preloader')) return false;
        if (form.hasAttribute('data-ajax')) return false;
        if ((form.getAttribute('method') || 'get').toLowerCase() === 'dialog') return false;
        return true;
    }

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
            return;
        }

        const link = event.target.closest('a[href]');
        if (link && shouldShowForLink(link)) {
            showPreloader();
            return;
        }

        const button = event.target.closest('button[type="submit"], input[type="submit"]');
        if (button) {
            const form = button.closest('form');
            if (form && shouldShowForForm(form)) {
                showPreloader();
            }
        }
    }, true);

    document.addEventListener('submit', function (event) {
        if (!shouldShowForForm(event.target)) return;
        showPreloader();
        setTimeout(function () {
            if (event.defaultPrevented) {
                hidePreloader();
            }
        }, 0);
    }, false);

    window.addEventListener('beforeunload', function () {
        showPreloader();
    });

    window.addEventListener('pageshow', function () {
        hidePreloader();
    });

    window.addEventListener('load', hidePreloader);
    document.addEventListener('DOMContentLoaded', hidePreloader);

    window.CarEmpirePreloader = {
        show: showPreloader,
        hide: hidePreloader
    };
})();
</script>
