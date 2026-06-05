@php
    $prefix = $prefix ?? 'ad_';
    $inputSize = $inputSize ?? '';
    $sizeClass = $inputSize ? "form-control-{$inputSize}" : '';
    $selectSizeClass = $inputSize ? "form-select-{$inputSize}" : '';
@endphp

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label mb-0">Video Links</label>
        <button type="button" class="btn btn-sm btn-outline-secondary va-add-video-link-btn" data-prefix="{{ $prefix }}">
            <i class="fas fa-plus me-1"></i>Add Video Link
        </button>
    </div>
    <div id="{{ $prefix }}video_links_container" class="va-video-links-container" data-prefix="{{ $prefix }}" data-size-class="{{ $sizeClass }}" data-select-size-class="{{ $selectSizeClass }}"></div>
</div>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label mb-0">Social Media Posts</label>
        <button type="button" class="btn btn-sm btn-outline-secondary va-add-social-link-btn" data-prefix="{{ $prefix }}">
            <i class="fas fa-plus me-1"></i>Add Social Post
        </button>
    </div>
    <div id="{{ $prefix }}social_links_container" class="va-social-links-container" data-prefix="{{ $prefix }}" data-size-class="{{ $sizeClass }}" data-select-size-class="{{ $selectSizeClass }}"></div>
</div>

<script>
window.VehicleAdLinkFields = window.VehicleAdLinkFields || {};
(function() {
    const socialChannels = @json(\App\Models\VehicleAd::socialChannelOptions());

    function escHtml(str) {
        return String(str ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function channelOptions(selected) {
        return socialChannels.map(ch =>
            `<option value="${escHtml(ch)}"${ch === selected ? ' selected' : ''}>${escHtml(ch)}</option>`
        ).join('');
    }

    function addVideoRow(prefix, url = '') {
        const container = document.getElementById(prefix + 'video_links_container');
        if (!container) return;
        const row = document.createElement('div');
        row.className = 'input-group input-group-sm mb-2 va-video-link-row';
        row.innerHTML = `
            <input type="url" class="form-control ${container.dataset.sizeClass || ''} va-video-link-input" placeholder="https://..." value="${escHtml(url)}">
            <button type="button" class="btn btn-outline-danger va-remove-row-btn" title="Remove"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
    }

    function addSocialRow(prefix, channel = 'Facebook', link = '') {
        const container = document.getElementById(prefix + 'social_links_container');
        if (!container) return;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-center va-social-link-row';
        row.innerHTML = `
            <div class="col-md-4">
                <select class="form-select ${container.dataset.selectSizeClass || ''} va-social-channel-select">${channelOptions(channel)}</select>
            </div>
            <div class="col-md-7">
                <input type="url" class="form-control ${container.dataset.sizeClass || ''} va-social-link-input" placeholder="https://..." value="${escHtml(link)}">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger va-remove-row-btn" title="Remove"><i class="fas fa-times"></i></button>
            </div>
        `;
        container.appendChild(row);
    }

    function resetFields(prefix, videoLinks = [], socialLinks = []) {
        const videoContainer = document.getElementById(prefix + 'video_links_container');
        const socialContainer = document.getElementById(prefix + 'social_links_container');
        if (videoContainer) videoContainer.innerHTML = '';
        if (socialContainer) socialContainer.innerHTML = '';

        const videos = Array.isArray(videoLinks) && videoLinks.length ? videoLinks : [''];
        videos.forEach(url => addVideoRow(prefix, url));

        const socials = Array.isArray(socialLinks) && socialLinks.length ? socialLinks : [{ channel: 'Facebook', link: '' }];
        socials.forEach(item => addSocialRow(prefix, item.channel || 'Facebook', item.link || ''));
    }

    function collectPayload(prefix) {
        const videoLinks = [];
        document.querySelectorAll('#' + prefix + 'video_links_container .va-video-link-input').forEach(input => {
            const val = input.value.trim();
            if (val) videoLinks.push(val);
        });

        const socialMediaLinks = [];
        document.querySelectorAll('#' + prefix + 'social_links_container .va-social-link-row').forEach(row => {
            const channel = row.querySelector('.va-social-channel-select')?.value || 'Other';
            const link = row.querySelector('.va-social-link-input')?.value.trim() || '';
            if (link) socialMediaLinks.push({ channel, link });
        });

        return { video_links: videoLinks, social_media_links: socialMediaLinks };
    }

    document.addEventListener('click', function(e) {
        const addVideoBtn = e.target.closest('.va-add-video-link-btn');
        if (addVideoBtn) {
            addVideoRow(addVideoBtn.dataset.prefix, '');
            return;
        }
        const addSocialBtn = e.target.closest('.va-add-social-link-btn');
        if (addSocialBtn) {
            addSocialRow(addSocialBtn.dataset.prefix, 'Facebook', '');
            return;
        }
        const removeBtn = e.target.closest('.va-remove-row-btn');
        if (removeBtn) {
            const row = removeBtn.closest('.va-video-link-row, .va-social-link-row');
            const container = row?.parentElement;
            if (row && container) {
                const minRows = container.classList.contains('va-video-links-container') ? 1 : 1;
                if (container.children.length > minRows) {
                    row.remove();
                } else {
                    row.querySelector('input[type="url"]')?.value && (row.querySelector('input[type="url"]').value = '');
                    row.querySelector('.va-social-channel-select') && (row.querySelector('.va-social-channel-select').value = 'Facebook');
                }
            }
        }
    });

    window.VehicleAdLinkFields.reset = resetFields;
    window.VehicleAdLinkFields.collect = collectPayload;
})();
</script>
