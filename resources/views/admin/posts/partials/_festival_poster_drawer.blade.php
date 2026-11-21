{{-- Festival Poster Right-Side Drawer (Offcanvas) --}}
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="drawer-festival-posters" aria-labelledby="drawer-festival-posters-label" style="width: 450px; border-left: 1px solid rgba(0, 0, 0, 0.08);">
    {{-- Drawer Header --}}
    <div class="offcanvas-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2.5">
            <span class="avatar avatar-sm rounded-circle bg-warning-lt text-warning fw-bold">
                <i class="ti ti-sparkles fs-2"></i>
            </span>
            <div>
                <h4 class="offcanvas-title fw-bold mb-0 text-dark" id="drawer-festival-posters-label" style="font-size: 1.05rem; letter-spacing: -0.2px;">
                    Festival Posters
                </h4>
                <div class="text-secondary small" style="font-size: 11.5px;">Ready-to-use celebration & event templates</div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-secondary" data-bs-dismiss="offcanvas" style="font-size: 0.85rem;">
            Close
        </button>
    </div>

    {{-- Drawer Body --}}
    <div class="offcanvas-body p-4 bg-white" style="overflow-y: auto;">
        {{-- Search Input --}}
        <div class="mb-3">
            <div class="input-icon">
                <span class="input-icon-addon">
                    <i class="ti ti-search text-secondary"></i>
                </span>
                <input type="text" class="form-control rounded-2" id="festival-poster-search" placeholder="Search festival or event templates..." style="height: 40px; font-size: 0.9rem;">
            </div>
        </div>

        {{-- Category Filter Pills --}}
        <div class="d-flex align-items-center gap-1.5 flex-wrap mb-4" id="festival-category-pills">
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-semibold btn-dark">All</button>
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-medium btn-light border text-secondary">Upcoming</button>
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-medium btn-light border text-secondary">National</button>
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-medium btn-light border text-secondary">Religious</button>
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-medium btn-light border text-secondary">Branding</button>
        </div>

        {{-- Drawer Content Placeholder / Library Container --}}
        <div id="festival-posters-list-container">
            <div class="text-center py-5 px-3 border rounded-3 bg-light-subtle">
                <div class="avatar avatar-md rounded-circle bg-white shadow-xs text-warning mx-auto mb-3">
                    <i class="ti ti-sparkles fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Festival Templates Ready</h5>
                <p class="text-secondary small mb-3" style="max-width: 300px; margin: 0 auto; font-size: 12px; line-height: 1.5;">
                    Select pre-made festive posters with custom placeholders for your brand logo, name, and details.
                </p>
                <span class="badge bg-white border text-secondary rounded-pill px-3 py-1.5 fw-medium" style="font-size: 11px;">
                    Drawer Loaded & Ready
                </span>
            </div>
        </div>
    </div>

    {{-- Drawer Footer --}}
    <div class="offcanvas-footer border-top p-3 bg-white mt-auto d-flex align-items-center justify-content-between">
        <span class="text-secondary small" style="font-size: 11px;">
            <i class="ti ti-info-circle me-1"></i>Clicking a poster will attach it to your post.
        </span>
        <button type="button" class="btn btn-light rounded-pill px-3.5 py-1.5 text-secondary" data-bs-dismiss="offcanvas" style="font-size: 0.85rem;">
            Close
        </button>
    </div>
</div>
