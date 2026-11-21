{{-- Post Live Preview Script --}}
<script>
$(document).ready(function() {
    // Default placeholder avatar SVG (Data URI)
    const defaultAvatarSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 24 24' fill='%2394a3b8'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z'/%3E%3C/svg%3E";
    const defaultThumbSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1280' height='720' viewBox='0 0 1280 720' fill='%231e293b'%3E%3Crect width='1280' height='720' fill='%230f172a'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='sans-serif' font-size='32' fill='%2394a3b8'%3EVideo Thumbnail Preview%3C/text%3E%3C/svg%3E";

    // 1. Switch preview platform card inside modal
    function switchPreviewTab(platform) {
        $('.preview-platform-tab').removeClass('active text-dark').addClass('text-secondary');
        $('.preview-platform-card').addClass('d-none');

        if (platform === 'facebook' || platform === 1 || platform === '1') {
            $('#preview-tab-fb').addClass('active text-dark').removeClass('text-secondary');
            $('#preview-card-facebook').removeClass('d-none');
        } else if (platform === 'youtube' || platform === 3 || platform === '3') {
            $('#preview-tab-yt').addClass('active text-dark').removeClass('text-secondary');
            $('#preview-card-youtube').removeClass('d-none');
        } else {
            // Default: Instagram
            $('#preview-tab-ig').addClass('active text-dark').removeClass('text-secondary');
            $('#preview-card-instagram').removeClass('d-none');
        }
    }

    $(document).on('click', '.preview-platform-tab', function() {
        let target = $(this).data('platform');
        switchPreviewTab(target);
    });

    // 2. Open Preview Modal & Populate Live Form Data
    $(document).on('click', '#btn-open-post-preview', function(e) {
        e.preventDefault();

        // Detect current platform
        let currentPlat = 1;
        if (typeof currentPlatform !== 'undefined') {
            currentPlat = currentPlatform;
        } else if ($('#selected-platform').length > 0) {
            currentPlat = parseInt($('#selected-platform').val()) || 1;
        }

        // Determine Account Name & Avatar
        let accountName = 'Your Brand';
        let accountAvatar = defaultAvatarSvg;

        // Try getting checked Facebook account
        let fbChecked = $('.fb-primary-checkbox:checked').first();
        let igChecked = $('.ig-primary-checkbox:checked').first();
        let ytOption = $('#composer-yt-account-select option:selected');

        if (currentPlat === 3 && ytOption.length > 0 && ytOption.val()) {
            accountName = ytOption.text().replace(/^\[.*?\]\s*/, '').trim();
            accountAvatar = ytOption.data('avatar') || defaultAvatarSvg;
        } else if (currentPlat === 2 && igChecked.length > 0) {
            accountName = igChecked.data('name') || 'Instagram Account';
            accountAvatar = igChecked.data('avatar') || defaultAvatarSvg;
        } else if (fbChecked.length > 0) {
            accountName = fbChecked.data('name') || 'Facebook Page';
            accountAvatar = fbChecked.data('avatar') || defaultAvatarSvg;
        } else {
            // Fallback to first available account label
            let anyName = $('.fb-primary-checkbox, .ig-primary-checkbox').first().data('name');
            if (anyName) accountName = anyName;
        }

        // Populate Account Info Across All Cards
        $('.preview-account-name').text(accountName);
        $('.preview-account-avatar').attr('src', accountAvatar);

        // Extract Caption / Content & Format Hashtags
        let captionRaw = $('#composer-content').val() || '';
        let formattedCaption = captionRaw
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/(#[a-zA-Z0-9_\u0900-\u097F]+)/g, '<span class="text-primary fw-semibold">$1</span>')
            .replace(/\n/g, '<br>');

        if (!captionRaw.trim()) {
            formattedCaption = '<span class="text-muted fst-italic">[No caption text entered yet]</span>';
        }
        $('.preview-caption-text').html(formattedCaption);

        // Extract First Comment (if filled)
        let firstComment = ($('#composer-first-comment').val() || '').trim();
        if (firstComment) {
            let formattedComment = firstComment
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/(#[a-zA-Z0-9_\u0900-\u097F]+)/g, '<span class="text-primary fw-semibold">$1</span>')
                .replace(/\n/g, '<br>');
            $('.preview-first-comment-text').html(formattedComment);
            $('.preview-first-comment-wrap').removeClass('d-none');
        } else {
            $('.preview-first-comment-wrap').addClass('d-none');
        }

        // Extract Media (Files from device, Canva imports, or existing previews)
        let mediaSrc = null;
        let isVideo = false;

        // Check local device uploads
        let fileInput = document.getElementById('composer-media-files');
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            let file = fileInput.files[0];
            if (file.type.startsWith('video/')) {
                isVideo = true;
                mediaSrc = URL.createObjectURL(file);
            } else {
                mediaSrc = URL.createObjectURL(file);
            }
            if (fileInput.files.length > 1) {
                $('.preview-carousel-badge').removeClass('d-none');
                $('.preview-carousel-count').text('1/' + fileInput.files.length);
            } else {
                $('.preview-carousel-badge').addClass('d-none');
            }
        }

        // Check Canva imported graphics
        if (!mediaSrc) {
            let canvaImg = $('.canva-imported-item img').first();
            if (canvaImg.length > 0 && canvaImg.attr('src')) {
                mediaSrc = canvaImg.attr('src');
            }
        }

        // Check edit mode existing media
        if (!mediaSrc) {
            let existingImg = $('#meta-media-container img').first();
            if (existingImg.length > 0 && existingImg.attr('src')) {
                mediaSrc = existingImg.attr('src');
            }
        }

        // Bind Media into Meta Previews
        if (mediaSrc) {
            if (isVideo) {
                $('.preview-media-main').addClass('d-none');
                $('.preview-media-video').attr('src', mediaSrc).removeClass('d-none');
            } else {
                $('.preview-media-video').addClass('d-none');
                $('.preview-media-main').attr('src', mediaSrc).removeClass('d-none');
            }
            $('.preview-media-placeholder').addClass('d-none');
        } else {
            $('.preview-media-main').addClass('d-none');
            $('.preview-media-video').addClass('d-none');
            $('.preview-media-placeholder').removeClass('d-none');
        }

        // YouTube Specific Video Details
        let ytTitle = ($('#youtube-title').val() || '').trim() || 'Untitled YouTube Video';
        let ytDesc = ($('#youtube-description').val() || '').trim() || 'No video description added yet.';
        let ytThumb = null;

        // Check YouTube custom thumbnail
        if ($('#youtube-thumbnail-preview').length > 0 && $('#youtube-thumbnail-preview').attr('src')) {
            ytThumb = $('#youtube-thumbnail-preview').attr('src');
        }

        // Check thumbnail file input
        let ytThumbInput = document.getElementById('youtube-thumbnail');
        if (!ytThumb && ytThumbInput && ytThumbInput.files && ytThumbInput.files[0]) {
            ytThumb = URL.createObjectURL(ytThumbInput.files[0]);
        }

        // Check edit mode thumbnail
        if (!ytThumb && $('#meta-media-container img').length > 0) {
            ytThumb = $('#meta-media-container img').first().attr('src');
        }

        if (!ytThumb) {
            ytThumb = defaultThumbSvg;
        }

        $('#preview-yt-title-text').text(ytTitle);
        $('#preview-yt-desc-text').text(ytDesc);
        $('.preview-yt-thumb').attr('src', ytThumb);

        // Switch to the matching tab & Show Modal
        switchPreviewTab(currentPlat);
        let previewModal = new bootstrap.Modal(document.getElementById('modal-post-preview'));
        previewModal.show();
    });
});
</script>
