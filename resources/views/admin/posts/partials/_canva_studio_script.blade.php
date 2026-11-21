{{-- Canva Studio Script --}}
<script>
$(document).ready(function() {
    let canvaConnected = false;
    let csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

    // 1. Fetch Canva Connection Status
    function checkCanvaStatus() {
        $.ajax({
            url: "{{ route('admin.canva.status') }}",
            type: "GET",
            dataType: "json",
            success: function(res) {
                canvaConnected = res.is_connected;
                updateCanvaUI(res);
            },
            error: function(err) {
                console.warn('Canva status check failed:', err);
                $('#canva-status-dot').removeClass('bg-success bg-secondary').addClass('bg-warning');
                $('#canva-status-text').text('Canva API Configured');
            }
        });
    }

    function updateCanvaUI(data) {
        if (data.is_connected) {
            $('#canva-status-dot, .canva-status-dot').removeClass('bg-secondary bg-warning').addClass('bg-success');
            $('#canva-status-text, .canva-status-text').html('<span class="text-success fw-medium">Connected to Canva</span>');
            $('#btn-connect-canva, .btn-connect-canva').addClass('d-none');
            $('#btn-disconnect-canva, .btn-disconnect-canva').removeClass('d-none');
        } else {
            $('#canva-status-dot, .canva-status-dot').removeClass('bg-success bg-warning').addClass('bg-secondary');
            $('#canva-status-text, .canva-status-text').html('<span class="text-muted">Not connected</span>');
            $('#btn-connect-canva, .btn-connect-canva').removeClass('d-none');
            $('#btn-disconnect-canva, .btn-disconnect-canva').addClass('d-none');
        }
    }

    // Check status whenever modal opens
    $('#modal-canva-studio, #modal-canva-thumbnail').on('show.bs.modal', function () {
        checkCanvaStatus();
    });

    // 2. Handle Create Design via Canva API
    $('.btn-canva-create-api').on('click', function(e) {
        e.preventDefault();
        let btn = $(this);
        let width = btn.data('width') || 1080;
        let height = btn.data('height') || 1080;
        let title = btn.data('title') || 'Social Post';

        if (!canvaConnected) {
            if (confirm('Canva account is not connected yet for direct sync. Would you like to connect now? (Click Cancel to open Canva template directly)')) {
                window.location.href = "{{ route('admin.canva.auth') }}";
            } else {
                window.open('https://www.canva.com/design/play?width=' + width + '&height=' + height + '&units=px', '_blank');
            }
            return;
        }

        let originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Creating...');

        $.ajax({
            url: "{{ route('admin.canva.create-design') }}",
            type: "POST",
            data: {
                _token: csrfToken,
                title: title,
                width: width,
                height: height
            },
            success: function(res) {
                btn.prop('disabled', false).html(originalHtml);
                if (res.success && res.edit_url) {
                    $('#canva-design-id-input').val(res.design_id);
                    $('#canva-import-feedback')
                        .removeClass('d-none text-danger')
                        .addClass('text-success')
                        .html('<i class="ti ti-check me-1"></i>Design opened in new tab! Finish editing on Canva, then click <b>Import to Post</b> below.');

                    window.open(res.edit_url, '_blank');
                } else {
                    alert(res.message || 'Could not launch Canva editor.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Canva API request failed.';
                alert(msg);
            }
        });
    });

    // 3. Handle Import Design by ID
    $('#btn-import-canva-design').on('click', function() {
        let inputVal = $('#canva-design-id-input').val().trim();
        if (!inputVal) {
            alert('Please enter a valid Canva Design ID or URL.');
            return;
        }

        let designId = inputVal;
        let urlMatch = inputVal.match(/design\/([a-zA-Z0-9_-]+)/);
        if (urlMatch && urlMatch[1]) {
            designId = urlMatch[1];
        }

        let btn = $(this);
        let spinner = $('#canva-import-spinner');
        let feedback = $('#canva-import-feedback');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        feedback.removeClass('d-none text-danger text-success').addClass('text-primary').html('<i class="ti ti-loader me-1"></i>Exporting high-resolution graphic from Canva...');

        $.ajax({
            url: "{{ route('admin.canva.export-design') }}",
            type: "POST",
            data: {
                _token: csrfToken,
                design_id: designId,
                format: 'jpg'
            },
            success: function(res) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');

                if (res.success && res.image_url) {
                    feedback.removeClass('text-primary text-danger').addClass('text-success').html('<i class="ti ti-check me-1"></i>Imported successfully! Attached to your post.');

                    attachCanvaImageToComposer(res.image_url, res.relative_path, res.filename);

                    setTimeout(function() {
                        let modal = bootstrap.Modal.getInstance(document.getElementById('modal-canva-studio'));
                        if (modal) modal.hide();
                    }, 1200);
                } else {
                    feedback.removeClass('text-primary text-success').addClass('text-danger').text(res.message || 'Export failed.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to export design from Canva.';
                feedback.removeClass('text-primary text-success').addClass('text-danger').text(msg);
            }
        });
    });

    // 4. Disconnect Canva
    $('#btn-disconnect-canva').on('click', function() {
        if (!confirm('Are you sure you want to disconnect your Canva account?')) return;

        $.ajax({
            url: "{{ route('admin.canva.disconnect') }}",
            type: "POST",
            data: { _token: csrfToken },
            success: function() {
                checkCanvaStatus();
                alert('Canva disconnected successfully.');
            }
        });
    });

    // 5. Media Uploader & Attachment Hub Handling
    function updateAttachmentCount() {
        let localCount = 0;
        let fileInput = document.getElementById('composer-media-files');
        if (fileInput && fileInput.files) {
            localCount = fileInput.files.length;
        }
        let canvaCount = $('input[name="canva_media_paths[]"]').length;
        let total = localCount + canvaCount;

        $('#media-attached-count').text(total + ' attached');

        if (total > 0) {
            $('#media-uploader-empty-state').addClass('d-none');
            $('#composer-media-preview-grid').removeClass('d-none');
            $('#media-uploader-active-bar').removeClass('d-none');
            $('#btn-clear-all-media').removeClass('d-none');
        } else {
            $('#media-uploader-empty-state').removeClass('d-none');
            $('#composer-media-preview-grid').addClass('d-none');
            $('#media-uploader-active-bar').addClass('d-none');
            $('#btn-clear-all-media').addClass('d-none');
        }
    }

    // Clear all attached media
    $(document).on('click', '#btn-clear-all-media', function(e) {
        e.preventDefault();
        let fileInput = document.getElementById('composer-media-files');
        if (fileInput) fileInput.value = '';
        $('.local-media-preview-item').remove();
        $('.canva-imported-item').remove();
        $('#canva-imported-media-container').empty();
        updateAttachmentCount();
    });

    // Trigger file browse from buttons
    $(document).on('click', '#btn-trigger-file-browser, #btn-add-more-media', function(e) {
        e.preventDefault();
        $('#composer-media-files').click();
    });

    // Render local device uploads in preview grid
    $('#composer-media-files').on('change', function() {
        // Clear previous local previews
        $('.local-media-preview-item').remove();

        if (this.files && this.files.length > 0) {
            Array.from(this.files).forEach((file, idx) => {
                let isImage = file.type.startsWith('image/');
                let isVideo = file.type.startsWith('video/');
                let sizeStr = (file.size / 1024 / 1024).toFixed(1) + ' MB';
                if (file.size < 1024 * 1024) {
                    sizeStr = (file.size / 1024).toFixed(0) + ' KB';
                }

                let previewHtml = '';
                if (isImage) {
                    let objectUrl = URL.createObjectURL(file);
                    previewHtml = `
                        <div class="col-6 col-sm-4 col-md-3 local-media-preview-item">
                            <div class="card card-sm border position-relative overflow-hidden shadow-xs h-100" style="border-radius: 10px;">
                                <img src="${objectUrl}" class="w-100" style="height: 110px; object-fit: cover;" alt="Upload preview">
                                <div class="p-2 bg-white">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-light text-secondary border" style="font-size: 10px;">Device</span>
                                        <span class="text-muted" style="font-size: 11px;">${sizeStr}</span>
                                    </div>
                                    <div class="small fw-semibold text-truncate text-dark mt-1" title="${file.name}">${file.name}</div>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (isVideo) {
                    previewHtml = `
                        <div class="col-6 col-sm-4 col-md-3 local-media-preview-item">
                            <div class="card card-sm border position-relative overflow-hidden shadow-xs h-100 bg-dark text-white d-flex align-items-center justify-content-center" style="height: 145px; border-radius: 10px;">
                                <div class="text-center p-2">
                                    <i class="ti ti-video fs-1 text-primary mb-1"></i>
                                    <div class="small fw-semibold text-truncate text-white" style="max-width: 120px;">${file.name}</div>
                                    <span class="badge bg-secondary" style="font-size: 10px;">${sizeStr}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
                $('#composer-media-preview-grid').append(previewHtml);
            });
        }
        updateAttachmentCount();
    });

    // Drag & Drop Handling on Composer Media Card
    let dropCard = $('#composer-media-card');
    if (dropCard.length > 0) {
        dropCard.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropCard.css({ 'border-color': '#6366f1', 'background': '#eef2ff' });
        });

        dropCard.on('dragleave dragend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropCard.css({ 'border-color': '#cbd5e1', 'background': '#fafbfc' });
        });

        dropCard.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropCard.css({ 'border-color': '#cbd5e1', 'background': '#fafbfc' });

            let dt = e.originalEvent.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                let fileInput = document.getElementById('composer-media-files');
                if (fileInput) {
                    fileInput.files = dt.files;
                    $(fileInput).trigger('change');
                }
            }
        });
    }

    // Helper: Attach Canva Image to Composer Grid
    function attachCanvaImageToComposer(url, path, filename) {
        let cardHtml = `
            <div class="col-6 col-sm-4 col-md-3 canva-imported-item">
                <div class="card card-sm border position-relative overflow-hidden shadow-xs h-100" style="border-radius: 10px; border-color: #8b5cf6 !important;">
                    <img src="${url}" class="w-100" style="height: 110px; object-fit: cover;" alt="Canva graphic">
                    <button type="button" class="btn btn-sm btn-icon btn-dark bg-opacity-75 position-absolute top-0 end-0 m-1.5 rounded-circle border-0 text-white btn-remove-canva-media" title="Remove attachment" style="width: 26px; height: 26px;">
                        <i class="ti ti-x" style="font-size: 12px;"></i>
                    </button>
                    <div class="p-2 bg-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge text-white" style="background: linear-gradient(135deg, #00c4cc, #7d2ae8); font-size: 10px;">Canva</span>
                            <span class="text-success small fw-semibold" style="font-size: 10px;">Ready</span>
                        </div>
                        <div class="small fw-semibold text-truncate text-dark mt-1" title="${filename}">${filename}</div>
                    </div>
                    <input type="hidden" name="canva_media_paths[]" value="${path}">
                </div>
            </div>
        `;

        $('#composer-media-preview-grid').append(cardHtml);
        updateAttachmentCount();

        // Also if on YouTube tab, populate thumbnail preview
        if ($('#youtube-thumbnail-preview').length > 0) {
            $('#youtube-thumbnail-preview').attr('src', url);
            $('#youtube-thumbnail-preview-box').removeClass('d-none');
            $('#canva-imported-media-container').append(`<input type="hidden" name="canva_thumbnail" value="${path}">`);
        }
    }

    // Remove Canva Media
    $(document).on('click', '.btn-remove-canva-media', function() {
        $(this).closest('.canva-imported-item').remove();
        updateAttachmentCount();
    });

    // YouTube Thumbnail live preview
    $('#youtube-thumbnail').on('change', function() {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#youtube-thumbnail-preview').attr('src', e.target.result);
                $('#youtube-thumbnail-preview-box').removeClass('d-none');
                $('input[name="canva_thumbnail"]').remove();
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Remove YouTube Thumbnail
    $(document).on('click', '#btn-remove-youtube-thumbnail', function(e) {
        e.preventDefault();
        $('#youtube-thumbnail-preview').attr('src', '');
        $('#youtube-thumbnail-preview-box').addClass('d-none');
        $('input[name="canva_thumbnail"]').remove();
        let thumbFileInput = document.getElementById('youtube-thumbnail');
        if (thumbFileInput) thumbFileInput.value = '';
    });

    // Dedicated YouTube Thumbnail Canva Launcher
    $('#btn-create-yt-thumbnail-api').on('click', function(e) {
        e.preventDefault();
        let btn = $(this);
        let width = 1280;
        let height = 720;
        let videoTitle = $('#youtube-title').val() ? $('#youtube-title').val().trim() : '';
        let title = (videoTitle ? videoTitle.substring(0, 40) + ' - ' : '') + 'YouTube Thumbnail';

        if (!canvaConnected) {
            window.open('https://www.canva.com/create/youtube-thumbnails/', '_blank');
            return;
        }

        let originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Opening...');

        $.ajax({
            url: "{{ route('admin.canva.create-design') }}",
            type: "POST",
            data: {
                _token: csrfToken,
                title: title,
                width: width,
                height: height,
                preset: 'youtube_thumbnail'
            },
            success: function(res) {
                btn.prop('disabled', false).html(originalHtml);
                if (res.success && res.edit_url) {
                    $('#canva-thumbnail-id-input').val(res.design_id);
                    $('#canva-thumbnail-feedback')
                        .removeClass('d-none text-danger')
                        .addClass('text-success')
                        .html('<i class="ti ti-check me-1"></i>Thumbnail editor opened in Canva! Finish editing, then click <b>Import to Thumbnail</b> below.');
                    window.open(res.edit_url, '_blank');
                } else {
                    alert(res.message || 'Could not launch Canva editor.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Canva API request failed.';
                alert(msg);
            }
        });
    });

    // Dedicated YouTube Thumbnail Importer
    $('#btn-import-canva-thumbnail').on('click', function() {
        let inputVal = $('#canva-thumbnail-id-input').val().trim();
        if (!inputVal) {
            alert('Please enter a valid Canva Design ID or URL.');
            return;
        }

        let designId = inputVal;
        let urlMatch = inputVal.match(/design\/([a-zA-Z0-9_-]+)/);
        if (urlMatch && urlMatch[1]) {
            designId = urlMatch[1];
        }

        let btn = $(this);
        let spinner = $('#canva-thumb-import-spinner');
        let feedback = $('#canva-thumbnail-feedback');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        feedback.removeClass('d-none text-danger text-success').addClass('text-primary').html('<i class="ti ti-loader me-1"></i>Exporting thumbnail from Canva...');

        $.ajax({
            url: "{{ route('admin.canva.export-design') }}",
            type: "POST",
            data: {
                _token: csrfToken,
                design_id: designId,
                format: 'jpg'
            },
            success: function(res) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');

                if (res.success && res.image_url) {
                    feedback.removeClass('text-primary text-danger').addClass('text-success').html('<i class="ti ti-check me-1"></i>Thumbnail attached successfully!');

                    // Populate thumbnail preview
                    $('#youtube-thumbnail-preview').attr('src', res.image_url);
                    $('#youtube-thumbnail-preview-box').removeClass('d-none');

                    // Reset raw file input so Canva thumbnail takes precedence
                    let thumbFileInput = document.getElementById('youtube-thumbnail');
                    if (thumbFileInput) thumbFileInput.value = '';

                    // Ensure hidden input is set for form submission
                    $('input[name="canva_thumbnail"]').remove();
                    if ($('#canva-imported-media-container').length === 0) {
                        $('#form-create-post, #form-edit-post').append('<div id="canva-imported-media-container"></div>');
                    }
                    $('#canva-imported-media-container').append(`<input type="hidden" name="canva_thumbnail" value="${res.relative_path}">`);

                    setTimeout(function() {
                        let modal = bootstrap.Modal.getInstance(document.getElementById('modal-canva-thumbnail'));
                        if (modal) modal.hide();
                    }, 1000);
                } else {
                    feedback.removeClass('text-primary text-success').addClass('text-danger').text(res.message || 'Export failed.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to export design from Canva.';
                feedback.removeClass('text-primary text-success').addClass('text-danger').text(msg);
            }
        });
    });

    // Initial check
    updateAttachmentCount();
});
</script>
