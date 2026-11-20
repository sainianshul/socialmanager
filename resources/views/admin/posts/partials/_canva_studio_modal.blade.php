{{-- Canva Design Studio Modal --}}
<div class="modal fade" id="modal-canva-studio" tabindex="-1" aria-labelledby="modalCanvaStudioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3" style="width: 44px; height: 44px;">
                        <i class="ti ti-brand-canva fs-1"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalCanvaStudioLabel">Canva Design Studio</h5>
                        <p class="text-secondary small mb-0">Create stunning social graphics with official Canva presets and import them directly to your post.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-white">
                {{-- Account Connection Status Banner --}}
                <div id="canva-auth-banner" class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2.5">
                        <span id="canva-status-dot" class="badge bg-secondary p-1 rounded-circle" style="width: 10px; height: 10px;"></span>
                        <div>
                            <span id="canva-status-text" class="fw-semibold text-dark small">Checking Canva Connection...</span>
                            <div class="text-muted" style="font-size: 11px;">Client ID: OC-AaH45pF87cNr</div>
                        </div>
                    </div>
                    <div id="canva-auth-action">
                        <a href="{{ route('admin.canva.auth') }}" id="btn-connect-canva" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-medium">
                            <i class="ti ti-plug me-1"></i>Connect Canva Account
                        </a>
                        <button type="button" id="btn-disconnect-canva" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-medium d-none">
                            <i class="ti ti-plug-off me-1"></i>Disconnect
                        </button>
                    </div>
                </div>

                {{-- Section: Presets Grid --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-secondary" style="font-size: 11px; letter-spacing: 0.5px;">Standard Social Presets</span>
                        <span class="text-muted small">Click any preset to launch Canva</span>
                    </div>

                    <div class="row g-3">
                        {{-- 1. Square Post (1080x1080) --}}
                        <div class="col-sm-6">
                            <div class="card h-100 border p-3 canva-preset-card" style="border-radius: 12px; transition: all 0.2s ease;">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 p-1.5 bg-danger-subtle text-danger">
                                            <i class="ti ti-brand-instagram fs-3"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">Square Post</div>
                                            <div class="text-muted small">1080 &times; 1080 px &bull; 1:1</div>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-secondary border">Feed Post</span>
                                </div>
                                <p class="text-secondary small mb-3">Best for Instagram and Facebook feed updates, announcements, and multi-image posts.</p>
                                <div class="mt-auto d-flex gap-2">
                                    <a href="https://www.canva.com/create/instagram-posts/" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">
                                        <i class="ti ti-external-link me-1"></i>Open Template
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btn-canva-create-api" data-width="1080" data-height="1080" data-title="Social Square Post">
                                        <i class="ti ti-sparkles me-1"></i>Create
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Story / Reel (1080x1920) --}}
                        <div class="col-sm-6">
                            <div class="card h-100 border p-3 canva-preset-card" style="border-radius: 12px; transition: all 0.2s ease;">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 p-1.5 bg-purple-subtle text-purple">
                                            <i class="ti ti-device-mobile fs-3"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">Story / Reel</div>
                                            <div class="text-muted small">1080 &times; 1920 px &bull; 9:16</div>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-secondary border">Vertical</span>
                                </div>
                                <p class="text-secondary small mb-3">Full-screen vertical format for Instagram Stories, Reels covers, and Shorts.</p>
                                <div class="mt-auto d-flex gap-2">
                                    <a href="https://www.canva.com/create/instagram-stories/" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">
                                        <i class="ti ti-external-link me-1"></i>Open Template
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btn-canva-create-api" data-width="1080" data-height="1080" data-title="Social Story Post">
                                        <i class="ti ti-sparkles me-1"></i>Create
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Landscape Banner (1200x630) --}}
                        <div class="col-sm-6">
                            <div class="card h-100 border p-3 canva-preset-card" style="border-radius: 12px; transition: all 0.2s ease;">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 p-1.5 bg-primary-subtle text-primary">
                                            <i class="ti ti-brand-facebook fs-3"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">Landscape Banner</div>
                                            <div class="text-muted small">1200 &times; 630 px &bull; 1.91:1</div>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-secondary border">Landscape</span>
                                </div>
                                <p class="text-secondary small mb-3">Optimized for Facebook timeline updates, press statements, and news infographics.</p>
                                <div class="mt-auto d-flex gap-2">
                                    <a href="https://www.canva.com/create/facebook-posts/" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">
                                        <i class="ti ti-external-link me-1"></i>Open Template
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btn-canva-create-api" data-width="1200" data-height="630" data-title="Facebook Landscape Banner">
                                        <i class="ti ti-sparkles me-1"></i>Create
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 4. YouTube Thumbnail (1280x720) --}}
                        <div class="col-sm-6">
                            <div class="card h-100 border p-3 canva-preset-card" style="border-radius: 12px; transition: all 0.2s ease;">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 p-1.5 bg-danger-subtle text-danger">
                                            <i class="ti ti-brand-youtube fs-3"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">YouTube Thumbnail</div>
                                            <div class="text-muted small">1280 &times; 720 px &bull; 16:9</div>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-secondary border">High CTR</span>
                                </div>
                                <p class="text-secondary small mb-3">High-resolution 16:9 thumbnail for video broadcasts and YouTube updates.</p>
                                <div class="mt-auto d-flex gap-2">
                                    <a href="https://www.canva.com/create/youtube-thumbnails/" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">
                                        <i class="ti ti-external-link me-1"></i>Open Template
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btn-canva-create-api" data-width="1280" data-height="720" data-title="YouTube Video Thumbnail">
                                        <i class="ti ti-sparkles me-1"></i>Create
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section: Direct Import from Canva --}}
                <div class="p-3 rounded-3" style="background: #fafbfc; border: 1px solid #f1f5f9;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark small d-flex align-items-center gap-1.5">
                            <i class="ti ti-download text-primary"></i> Import Design by ID
                        </span>
                        <span class="text-muted" style="font-size: 11px;">Exports high-res PNG directly to your post</span>
                    </div>
                    <div class="input-group">
                        <input type="text" id="canva-design-id-input" class="form-control form-control-sm" placeholder="Paste Canva Design ID or URL (e.g. DAFVztcvd9z)">
                        <button type="button" id="btn-import-canva-design" class="btn btn-sm btn-primary px-3">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="canva-import-spinner"></span>
                            Import to Post
                        </button>
                    </div>
                    <div id="canva-import-feedback" class="mt-2 small d-none"></div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-0 pt-0 px-4 pb-4 bg-white">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.canva-preset-card:hover {
    border-color: #6366f1 !important;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.08);
}
</style>
