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
            $('#canva-status-dot').removeClass('bg-secondary bg-warning').addClass('bg-success');
            $('#canva-status-text').html('<span class="text-success fw-bold">Canva Connected</span> &bull; Direct Sync Enabled');
            $('#btn-connect-canva').addClass('d-none');
            $('#btn-disconnect-canva').removeClass('d-none');
        } else {
            $('#canva-status-dot').removeClass('bg-success bg-warning').addClass('bg-secondary');
            $('#canva-status-text').text('Canva Connect Ready');
            $('#btn-connect-canva').removeClass('d-none');
            $('#btn-disconnect-canva').addClass('d-none');
        }
    }

    // Check status whenever modal opens
    $('#modal-canva-studio').on('show.bs.modal', function () {
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
            // If not connected, prompt user to connect or open direct template
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
                    // Pre-fill design ID
                    $('#canva-design-id-input').val(res.design_id);
                    $('#canva-import-feedback')
                        .removeClass('d-none text-danger')
                        .addClass('text-success')
                        .html('<i class="ti ti-check me-1"></i>Design opened in new tab! Finish editing on Canva, then click <b>Import to Post</b> below.');

                    // Open Canva Editor in new tab
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

        // Extract ID if full URL pasted (e.g. https://www.canva.com/design/DAFVztcvd9z/edit)
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
                format: 'png'
            },
            success: function(res) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');

                if (res.success && res.image_url) {
                    feedback.removeClass('text-primary text-danger').addClass('text-success').html('<i class="ti ti-check me-1"></i>Imported successfully! Attached to your post.');

                    // Attach to Media Preview in Composer Form
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

    // Helper: Attach Canva Image to Composer
    function attachCanvaImageToComposer(url, path, filename) {
        let container = $('#canva-imported-media-container');
        if (container.length === 0) {
            $('#meta-media-section').append('<div id="canva-imported-media-container" class="mt-3"></div>');
            container = $('#canva-imported-media-container');
        }

        let itemHtml = `
            <div class="card card-sm border bg-light-subtle p-2 mb-2 d-flex flex-row align-items-center justify-content-between canva-imported-item" style="border-radius: 10px;">
                <div class="d-flex align-items-center gap-2">
                    <img src="${url}" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;" alt="Canva graphic">
                    <div>
                        <div class="fw-bold small text-dark">${filename}</div>
                        <span class="badge bg-primary-subtle text-primary" style="font-size: 10px;">Imported from Canva</span>
                    </div>
                </div>
                <input type="hidden" name="canva_media_paths[]" value="${path}">
                <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle border-0 btn-remove-canva-media" title="Remove attachment">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        `;
        container.append(itemHtml);

        // Also if on YouTube tab, populate thumbnail preview
        if ($('#youtube-thumbnail-preview').length > 0) {
            $('#youtube-thumbnail-preview').attr('src', url).removeClass('d-none');
            $('#canva-imported-media-container').append(`<input type="hidden" name="canva_thumbnail" value="${path}">`);
        }
    }

    // Remove Canva Media
    $(document).on('click', '.btn-remove-canva-media', function() {
        $(this).closest('.canva-imported-item').remove();
    });
});
</script>
