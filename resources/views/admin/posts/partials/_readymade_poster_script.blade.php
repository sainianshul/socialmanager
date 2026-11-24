{{-- Readymade Posters AJAX Loader & Attachment Script --}}
<style>
.readymade-poster-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
}
.readymade-poster-card:hover .readymade-hover-overlay {
    opacity: 1 !important;
}
.readymade-cat-pill {
    transition: all 0.15s ease;
}
</style>

<script>
$(document).ready(function() {
    let readymadeCache = [];
    let currentCategoryId = '';
    let readymadeDebounceTimer = null;
    let categoriesLoaded = false;

    // Load readymade posters from API
    function fetchReadymadePosters(categoryId = '', search = '') {
        $('#readymade-posters-loading').removeClass('d-none');
        $('#readymade-posters-empty').addClass('d-none');
        $('#readymade-posters-grid').addClass('d-none').empty();

        let url = '{{ route('admin.readymade-posters.list') }}';
        let params = [];
        if (categoryId) params.push('category_id=' + encodeURIComponent(categoryId));
        if (search) params.push('search=' + encodeURIComponent(search));
        if (params.length > 0) url += '?' + params.join('&');

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#readymade-posters-loading').addClass('d-none');

                if (!res.success) {
                    $('#readymade-posters-empty').removeClass('d-none');
                    return;
                }

                // Render category pills once
                if (!categoriesLoaded && res.categories && res.categories.length > 0) {
                    let pillsContainer = $('#readymade-category-pills');
                    res.categories.forEach(function(cat) {
                        let iconHtml = cat.icon ? `<i class="${cat.icon} me-1"></i>` : '';
                        pillsContainer.append(
                            `<button type="button" class="btn btn-xs rounded-pill px-3 py-1.5 fw-medium btn-light border text-secondary readymade-cat-pill" data-category-id="${cat.id}">
                                ${iconHtml}${cat.name}
                            </button>`
                        );
                    });
                    categoriesLoaded = true;
                }

                readymadeCache = res.posters || [];

                if (readymadeCache.length === 0) {
                    $('#readymade-posters-empty').removeClass('d-none');
                    return;
                }

                let gridHtml = '';
                readymadeCache.forEach(function(p) {
                    gridHtml += `
                        <div class="col-6 col-sm-6">
                            <div class="card card-sm border shadow-xs h-100 readymade-poster-card cursor-pointer position-relative overflow-hidden" data-id="${p.id}" style="border-radius: 12px; transition: transform 0.15s ease, box-shadow 0.15s ease;">
                                <div class="position-relative overflow-hidden" style="aspect-ratio: 1 / 1; background: #f8fafc;">
                                    <img src="${p.thumbnail_url || p.image_url}" class="w-100 h-100 object-fit-cover" alt="${p.name}" loading="lazy">
                                    <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1">
                                        ${p.category_name ? `<span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-0.5" style="font-size: 10px;">${p.category_name}</span>` : ''}
                                        ${p.party_code ? `<span class="badge bg-primary text-white rounded-pill px-2 py-0.5" style="font-size: 10px;">${p.party_code}</span>` : ''}
                                    </div>
                                    <div class="readymade-hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 text-white fw-bold gap-1" style="transition: opacity 0.2s ease;">
                                        <i class="ti ti-circle-plus fs-2"></i>
                                        <span>Click to Use</span>
                                    </div>
                                </div>
                                <div class="p-2.5 bg-white">
                                    <div class="fw-semibold text-dark text-truncate small" title="${p.name}">${p.name}</div>
                                    ${p.default_caption ? `<div class="text-secondary small text-truncate mt-0.5" style="font-size: 11px;">${p.default_caption}</div>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#readymade-posters-grid').html(gridHtml).removeClass('d-none');
            },
            error: function(err) {
                $('#readymade-posters-loading').addClass('d-none');
                $('#readymade-posters-empty').removeClass('d-none');
                console.error('Failed to load readymade posters:', err);
            }
        });
    }

    // Attach chosen readymade poster to the post composer preview grid
    function attachReadymadePoster(poster) {
        if (!poster) return;

        let cardHtml = `
            <div class="col-6 col-sm-4 col-md-3 readymade-imported-item">
                <div class="card card-sm border position-relative overflow-hidden shadow-xs h-100" style="border-radius: 10px; border-color: #0d9488 !important;">
                    <img src="${poster.image_url}" class="w-100" style="height: 110px; object-fit: cover;" alt="${poster.name}">
                    <button type="button" class="btn btn-sm btn-icon btn-dark bg-opacity-75 position-absolute top-0 end-0 m-1.5 rounded-circle border-0 text-white btn-remove-readymade-media" title="Remove poster" style="width: 26px; height: 26px;">
                        <i class="ti ti-x" style="font-size: 12px;"></i>
                    </button>
                    <div class="p-2 bg-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge text-white" style="background: linear-gradient(135deg, #0d9488, #0284c7); font-size: 10px;">Readymade</span>
                            <span class="text-success small fw-semibold" style="font-size: 10px;">Ready</span>
                        </div>
                        <div class="small fw-semibold text-truncate text-dark mt-1" title="${poster.name}">${poster.name}</div>
                    </div>
                    <input type="hidden" name="readymade_poster_paths[]" value="${poster.image_path}">
                    <input type="hidden" name="readymade_poster_id" value="${poster.id}">
                    <input type="hidden" name="canva_media_paths[]" value="${poster.image_path}">
                </div>
            </div>
        `;

        $('#composer-media-preview-grid').append(cardHtml);

        // Auto-fill caption if composer is empty
        let contentBox = $('#composer-content');
        if (contentBox.length > 0 && !contentBox.val().trim() && poster.default_caption) {
            contentBox.val(poster.default_caption).trigger('input');
        }

        // Auto-switch post_type to photo if currently text
        let activeType = $('#selected-post-type').val();
        if (activeType === 'text') {
            $('.post-type-pill[data-type="photo"]').trigger('click');
        }

        // Also if on YouTube tab, populate thumbnail preview
        if ($('#youtube-thumbnail-preview').length > 0) {
            $('#youtube-thumbnail-preview').attr('src', poster.image_url);
            $('#youtube-thumbnail-preview-box').removeClass('d-none');
        }

        if (typeof updateAttachmentCount === 'function') {
            updateAttachmentCount();
        }

        // Close drawer
        let drawerEl = document.getElementById('drawer-festival-posters');
        if (drawerEl) {
            let bsOffcanvas = bootstrap.Offcanvas.getInstance(drawerEl);
            if (bsOffcanvas) bsOffcanvas.hide();
        }
    }

    // Trigger loading when drawer is shown
    let drawerEl = document.getElementById('drawer-festival-posters');
    if (drawerEl) {
        drawerEl.addEventListener('show.bs.offcanvas', function() {
            if (readymadeCache.length === 0) {
                fetchReadymadePosters(currentCategoryId, $('#readymade-poster-search').val());
            }
        });
    }

    // Category pills filter
    $(document).on('click', '.readymade-cat-pill', function() {
        $('.readymade-cat-pill').removeClass('btn-dark active fw-semibold').addClass('btn-light border text-secondary fw-medium');
        $(this).removeClass('btn-light border text-secondary fw-medium').addClass('btn-dark active fw-semibold');
        currentCategoryId = $(this).attr('data-category-id') || '';
        fetchReadymadePosters(currentCategoryId, $('#readymade-poster-search').val());
    });

    // Search filter
    $('#readymade-poster-search').on('input', function() {
        clearTimeout(readymadeDebounceTimer);
        let q = $(this).val();
        readymadeDebounceTimer = setTimeout(function() {
            fetchReadymadePosters(currentCategoryId, q);
        }, 300);
    });

    // Poster card click -> attach to composer
    $(document).on('click', '.readymade-poster-card', function() {
        let posterId = parseInt($(this).attr('data-id'));
        let poster = readymadeCache.find(function(p) { return p.id === posterId; });
        if (poster) {
            attachReadymadePoster(poster);
        }
    });

    // Remove readymade poster from preview
    $(document).on('click', '.btn-remove-readymade-media', function() {
        $(this).closest('.readymade-imported-item').remove();
        if (typeof updateAttachmentCount === 'function') {
            updateAttachmentCount();
        }
    });
});
</script>
