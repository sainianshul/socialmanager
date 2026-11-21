{{-- Festival Poster Right-Side Drawer (Offcanvas) --}}
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="drawer-festival-posters" aria-labelledby="drawer-festival-posters-label" style="width: 460px; max-width: 90vw; border-left: 1px solid rgba(0, 0, 0, 0.08);">
    {{-- Drawer Header --}}
    <div class="offcanvas-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2.5">
            <span class="avatar avatar-sm rounded-circle bg-warning-lt text-warning fw-bold">
                <i class="ti ti-sparkles fs-2"></i>
            </span>
            <div>
                <h4 class="offcanvas-title fw-bold mb-0 text-dark" id="drawer-festival-posters-label" style="font-size: 1.05rem; letter-spacing: -0.2px;">
                    Festival & Campaign Posters
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
        <div class="d-flex align-items-center gap-1.5 flex-wrap mb-3" id="festival-category-pills">
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-semibold btn-dark">All</button>
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-medium btn-light border text-secondary">Upcoming Festivals</button>
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-medium btn-light border text-secondary">Elections & Political</button>
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-medium btn-light border text-secondary">National Days</button>
            <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 fw-medium btn-light border text-secondary">Branding</button>
        </div>

        {{-- Sample Template Image Concept Card (Image 2) --}}
        <div class="card border rounded-3 overflow-hidden shadow-xs mb-3">
            <div class="card-header bg-light-subtle py-2.5 px-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-1.5">
                    <i class="ti ti-template text-primary"></i>
                    <span class="fw-semibold text-dark small">Pre-Made Poster Templates</span>
                </div>
                <span class="badge bg-warning-lt text-warning rounded-pill px-2 py-0.5" style="font-size: 10.5px;">
                    Sample Preview
                </span>
            </div>

            <div class="position-relative bg-light p-2">
                <img src="{{ asset('images/festival-templates-sample.jpg') }}" class="img-fluid rounded-2 w-100 border shadow-xs" alt="Campaign and Festival Poster Templates Sample" style="display: block;">
            </div>

            <div class="card-body p-3 bg-white">
                <div class="small fw-semibold text-dark mb-1">Customizable Template Layouts:</div>
                <ul class="list-unstyled small text-secondary mb-0" style="font-size: 12px; line-height: 1.6;">
                    <li class="d-flex align-items-center gap-1.5 mb-1">
                        <i class="ti ti-check text-success"></i>
                        <span><strong>Empty Face / Photo Slots:</strong> Instant replace with user photo (ADD IMAGE)</span>
                    </li>
                    <li class="d-flex align-items-center gap-1.5 mb-1">
                        <i class="ti ti-check text-success"></i>
                        <span><strong>Branding & Logo Slots:</strong> Party, business or campaign logo placement</span>
                    </li>
                    <li class="d-flex align-items-center gap-1.5">
                        <i class="ti ti-check text-success"></i>
                        <span><strong>Direct Composer Sync:</strong> Click to attach poster directly to post</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Drawer Footer --}}
    <div class="offcanvas-footer border-top p-3 bg-white mt-auto d-flex align-items-center justify-content-between">
        <span class="text-secondary small" style="font-size: 11px;">
            <i class="ti ti-info-circle me-1"></i>Select any festival template to attach to post.
        </span>
        <button type="button" class="btn btn-light rounded-pill px-3.5 py-1.5 text-secondary" data-bs-dismiss="offcanvas" style="font-size: 0.85rem;">
            Close
        </button>
    </div>
</div>
