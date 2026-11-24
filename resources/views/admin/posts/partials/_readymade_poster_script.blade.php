{{-- Readymade Posters AJAX Loader & Attachment Script --}}
<style>
.readymade-poster-card {
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    user-select: none;
}
.readymade-poster-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(13, 148, 136, 0.14) !important;
    border-color: #0d9488 !important;
}
.readymade-poster-card:hover .readymade-hover-overlay {
    opacity: 1 !important;
}
.readymade-cat-pill {
    transition: all 0.15s ease;
}
</style>

<script>
window.allReadymadePostersById = window.allReadymadePostersById || {};

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

                // Render category pills once (Show 4-5 first, then + More toggle)
                if (!categoriesLoaded && res.categories && res.categories.length > 0) {
                    let pillsContainer = $('#readymade-category-pills');
                    let visibleLimit = 5;
                    let totalExtra = 0;

                    res.categories.forEach(function(cat, index) {
                        let iconHtml = cat.icon ? `<i class="${cat.icon} me-1"></i>` : '';
                        let displayName = cat.name.split('(')[0].trim();
                        let isExtra = index >= visibleLimit;
                        if (isExtra) totalExtra++;

                        let extraClass = isExtra ? 'readymade-cat-extra d-none' : '';

                        pillsContainer.append(
                            `<button type="button" class="btn btn-xs rounded-pill px-3 py-1.5 fw-medium btn-light border text-secondary readymade-cat-pill ${extraClass}" data-category-id="${cat.id}" title="${cat.name}">
                                ${iconHtml}${displayName}
                            </button>`
                        );
                    });

                    if (totalExtra > 0) {
                        pillsContainer.append(
                            `<button type="button" class="btn btn-xs rounded-pill px-2.5 py-1.5 fw-semibold btn-outline-primary d-inline-flex align-items-center gap-1" id="btn-toggle-more-categories" data-expanded="0" title="Click to view more categories">
                                <i class="ti ti-dots fs-3"></i>
                                <span id="more-categories-label">+ More (${totalExtra})</span>
                            </button>`
                        );
                    }
                    categoriesLoaded = true;
                }

                readymadeCache = res.posters || [];

                if (readymadeCache.length === 0) {
                    $('#readymade-posters-empty').removeClass('d-none');
                    return;
                }

                let gridHtml = '';
                readymadeCache.forEach(function(p) {
                    // Cache poster by ID globally
                    window.allReadymadePostersById[p.id] = p;

                    gridHtml += `
                        <div class="col-6 col-sm-6">
                            <div class="card card-sm border shadow-xs h-100 readymade-poster-card cursor-pointer position-relative overflow-hidden" data-id="${p.id}" style="border-radius: 12px; cursor: pointer;">
                                <div class="position-relative overflow-hidden" style="aspect-ratio: 1 / 1; background: #f1f5f9;">
                                    <img src="${p.thumbnail_url || p.image_url}" class="w-100 h-100 object-fit-cover" alt="${p.name}" loading="lazy">
                                    <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1" style="z-index: 2;">
                                        ${p.category_name ? `<span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-0.5 shadow-xs" style="font-size: 10px;">${p.category_name}</span>` : ''}
                                        ${p.party_code ? `<span class="badge bg-primary text-white rounded-pill px-2 py-0.5 shadow-xs" style="font-size: 10px;">${p.party_code}</span>` : ''}
                                    </div>
                                    <div class="readymade-hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 text-white fw-bold gap-1" style="transition: opacity 0.2s ease; z-index: 3;">
                                        <i class="ti ti-circle-plus fs-1 text-white"></i>
                                        <span class="small">Click to Use</span>
                                    </div>
                                </div>
                                <div class="p-2.5 bg-white border-top">
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
        if (!poster) {
            console.error('attachReadymadePoster called without poster object');
            return;
        }

        console.log('Attaching readymade poster to composer:', poster);

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

        // 1. Force remove d-none from preview grid so it is immediately visible
        $('#composer-media-preview-grid').removeClass('d-none').append(cardHtml);
        $('#media-uploader-empty-state').addClass('d-none');
        $('#btn-clear-all-media').removeClass('d-none');

        // 2. Auto-fill caption
        if (poster.default_caption) {
            let contentBox = $('#composer-content');
            if (contentBox.length > 0) {
                contentBox.val(poster.default_caption);
                contentBox.trigger('input');
                contentBox.trigger('change');

                let counter = $('#caption-counter');
                if (counter.length > 0) {
                    counter.text(poster.default_caption.length + ' / 5000');
                }
            }
        }

        // 3. Switch post type pill to Photo if currently Text
        let activeType = $('#selected-post-type').val();
        if (activeType === 'text') {
            let photoPill = $('.post-type-pill[data-type="photo"]');
            if (photoPill.length > 0) {
                photoPill.trigger('click');
            } else {
                $('#selected-post-type').val('photo');
            }
        }

        // 4. Update YouTube thumbnail preview if active
        if ($('#youtube-thumbnail-preview').length > 0) {
            $('#youtube-thumbnail-preview').attr('src', poster.image_url);
            $('#youtube-thumbnail-preview-box').removeClass('d-none');
        }

        // 5. Update Attachment Counter Badge
        if (typeof window.updateAttachmentCount === 'function') {
            window.updateAttachmentCount();
        } else {
            let total = $('input[name="readymade_poster_paths[]"]').length + $('input[name="canva_media_paths[]"]').length;
            $('#media-attached-count').text(total + ' attached');
        }

        // 6. Close Drawer reliably
        let drawerEl = document.getElementById('drawer-festival-posters');
        if (drawerEl) {
            try {
                let bs = bootstrap.Offcanvas.getOrCreateInstance(drawerEl);
                bs.hide();
            } catch(e) {
                console.warn('Offcanvas hide fallback:', e);
            }
            $('#drawer-festival-posters [data-bs-dismiss="offcanvas"]').trigger('click');
        }

        // 7. Smooth scroll to media attachment area so user immediately sees attached poster
        if ($('#meta-media-section').length > 0) {
            $('html, body').animate({
                scrollTop: $('#meta-media-section').offset().top - 120
            }, 300);
        }
    }

    // Trigger loading when drawer is shown or button clicked
    let drawerEl = document.getElementById('drawer-festival-posters');
    if (drawerEl) {
        drawerEl.addEventListener('show.bs.offcanvas', function() {
            if (readymadeCache.length === 0) {
                fetchReadymadePosters(currentCategoryId, $('#readymade-poster-search').val());
            }
        });
    }

    $('#btn-festival-poster').on('click', function() {
        if (readymadeCache.length === 0) {
            fetchReadymadePosters(currentCategoryId, $('#readymade-poster-search').val());
        }
    });

    // Category pills filter
    $(document).on('click', '.readymade-cat-pill', function() {
        $('.readymade-cat-pill').removeClass('btn-dark active fw-semibold').addClass('btn-light border text-secondary fw-medium');
        $(this).removeClass('btn-light border text-secondary fw-medium').addClass('btn-dark active fw-semibold');
        currentCategoryId = $(this).attr('data-category-id') || '';
        fetchReadymadePosters(currentCategoryId, $('#readymade-poster-search').val());
    });

    // Toggle More / Less categories
    $(document).on('click', '#btn-toggle-more-categories', function(e) {
        e.preventDefault();
        let btn = $(this);
        let isExpanded = btn.attr('data-expanded') === '1';
        let extraCount = $('.readymade-cat-extra').length;

        if (isExpanded) {
            $('.readymade-cat-extra').not('.active').addClass('d-none');
            btn.attr('data-expanded', '0')
               .removeClass('btn-primary text-white')
               .addClass('btn-outline-primary')
               .html(`<i class="ti ti-dots fs-3"></i><span>+ More (${extraCount})</span>`);
        } else {
            $('.readymade-cat-extra').removeClass('d-none');
            btn.attr('data-expanded', '1')
               .removeClass('btn-outline-primary')
               .addClass('btn-primary text-white')
               .html(`<i class="ti ti-chevron-up me-1"></i><span>Less</span>`);
        }
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
    $(document).on('click', '.readymade-poster-card, .readymade-poster-card *', function(e) {
        e.preventDefault();
        e.stopPropagation();

        let card = $(this).closest('.readymade-poster-card');
        let posterId = card.attr('data-id');
        if (!posterId) return;

        let poster = window.allReadymadePostersById[posterId] || readymadeCache.find(function(p) { return p.id == posterId; });
        if (poster) {
            attachReadymadePoster(poster);
        } else {
            console.warn('Poster not found in cache for ID:', posterId);
        }
    });

    // Remove readymade poster from preview
    $(document).on('click', '.btn-remove-readymade-media', function(e) {
        e.preventDefault();
        $(this).closest('.readymade-imported-item').remove();
        if (typeof window.updateAttachmentCount === 'function') {
            window.updateAttachmentCount();
        } else {
            let total = $('input[name="readymade_poster_paths[]"]').length + $('input[name="canva_media_paths[]"]').length;
            $('#media-attached-count').text(total + ' attached');
            if (total === 0) {
                $('#composer-media-preview-grid').addClass('d-none');
                $('#btn-clear-all-media').addClass('d-none');
            }
        }
    });
});
</script>
