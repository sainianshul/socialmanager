{{-- Readymade Poster Right-Side Drawer (Offcanvas) --}}
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="drawer-festival-posters" aria-labelledby="drawer-festival-posters-label" style="width: 680px; max-width: 96vw; border-left: 1px solid rgba(0, 0, 0, 0.08);">
    {{-- Drawer Header --}}
    <div class="offcanvas-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2.5">
            <span class="avatar avatar-sm rounded-circle bg-primary-lt text-primary fw-bold">
                <i class="ti ti-photo-star fs-2"></i>
            </span>
            <div>
                <h4 class="offcanvas-title fw-bold mb-0 text-dark" id="drawer-festival-posters-label" style="font-size: 1.05rem; letter-spacing: -0.2px;">
                    Readymade Posters
                </h4>
                <div class="text-secondary small" style="font-size: 11.5px;">Click any poster to attach directly to your post preview</div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-secondary" data-bs-dismiss="offcanvas" style="font-size: 0.85rem;">
            Close
        </button>
    </div>

    {{-- Drawer Body --}}
    <div class="offcanvas-body p-4 bg-light-subtle d-flex flex-column" style="overflow-y: auto;">
        {{-- Search Input --}}
        <div class="mb-3">
            <div class="input-icon">
                <span class="input-icon-addon">
                    <i class="ti ti-search text-secondary"></i>
                </span>
                <input type="text" class="form-control rounded-2 bg-white" id="readymade-poster-search" placeholder="Search readymade posters by title or caption..." style="height: 42px; font-size: 0.9rem;">
            </div>
        </div>

        {{-- Category Filter Pills --}}
        <div class="d-flex align-items-center gap-1.5 flex-wrap mb-3" id="readymade-category-pills">
            <button type="button" class="btn btn-xs rounded-pill px-3 py-1.5 fw-semibold btn-dark readymade-cat-pill active" data-category-id="">All</button>
            <span class="text-secondary small ms-1 d-none" id="readymade-cat-loading">Loading categories...</span>
        </div>

        {{-- Posters Grid / Loading / Empty State --}}
        <div id="readymade-posters-loading" class="text-center py-5">
            <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
            <div class="text-secondary small mt-2">Loading readymade posters...</div>
        </div>

        <div id="readymade-posters-empty" class="text-center py-5 d-none">
            <div class="avatar avatar-lg rounded-circle bg-light text-secondary mx-auto mb-2">
                <i class="ti ti-photo-off fs-1"></i>
            </div>
            <div class="fw-semibold text-dark">No posters found</div>
            <div class="text-secondary small mt-1">Try another category or search keyword.</div>
        </div>

        <div class="row g-3" id="readymade-posters-grid"></div>
    </div>

    {{-- Drawer Footer --}}
    <div class="offcanvas-footer border-top p-3 bg-white mt-auto d-flex align-items-center justify-content-between">
        <span class="text-secondary small" style="font-size: 11.5px;">
            <i class="ti ti-click me-1 text-primary"></i>Click any poster image to attach to composer.
        </span>
        <button type="button" class="btn btn-light rounded-pill px-3.5 py-1.5 text-secondary" data-bs-dismiss="offcanvas" style="font-size: 0.85rem;">
            Close
        </button>
    </div>
</div>
