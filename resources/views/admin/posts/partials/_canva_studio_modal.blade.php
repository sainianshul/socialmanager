{{-- Canva Design Studio Modal (Apple Minimalist) --}}
<div class="modal fade" id="modal-canva-studio" tabindex="-1" aria-labelledby="modalCanvaStudioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 660px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="d-inline-flex align-items-center justify-content-center text-white fw-bold rounded-circle shadow-xs" style="width: 32px; height: 32px; background: linear-gradient(135deg, #00c4cc, #7d2ae8); font-family: -apple-system, BlinkMacSystemFont, sans-serif; font-style: italic; font-size: 18px; line-height: 1;">
                        C
                    </span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalCanvaStudioLabel" style="font-size: 1.15rem;">Canva Design Studio</h5>
                </div>

                <div class="d-flex align-items-center gap-2.5">
                    {{-- Minimal Connection Status Pill --}}
                    <div id="canva-auth-pill" class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 12px;">
                        <span id="canva-status-dot" class="badge bg-secondary p-1 rounded-circle" style="width: 7px; height: 7px;"></span>
                        <span id="canva-status-text" class="fw-medium text-secondary">Checking...</span>
                        <a href="{{ route('admin.canva.auth') }}" id="btn-connect-canva" class="btn btn-xs btn-link text-decoration-none p-0 ms-1 fw-semibold text-primary">Connect</a>
                        <button type="button" id="btn-disconnect-canva" class="btn btn-xs btn-link text-decoration-none p-0 ms-1 text-muted d-none" title="Disconnect Canva" style="font-size: 11px;">Disconnect</button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-white">
                {{-- Format Presets Grid --}}
                <div class="mb-3.5">
                    <div class="text-secondary small fw-medium mb-2.5" style="font-size: 12px;">Choose format to design</div>

                    <div class="row g-2.5">
                        {{-- 1. Square Post (1080x1080) --}}
                        <div class="col-sm-6">
                            <div class="card p-3 h-100 canva-format-card border" style="border-radius: 12px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-2 bg-danger-subtle text-danger" style="width: 36px; height: 36px;">
                                            <i class="ti ti-brand-instagram fs-2"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">Square Post</div>
                                            <div class="text-secondary" style="font-size: 11.5px;">1080 &times; 1080 &bull; 1:1</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-medium btn-canva-create-api shadow-xs" data-width="1080" data-height="1080" data-title="Social Square Post">
                                        Design
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Story / Reel (1080x1920) --}}
                        <div class="col-sm-6">
                            <div class="card p-3 h-100 canva-format-card border" style="border-radius: 12px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-2 bg-purple-subtle text-purple" style="width: 36px; height: 36px;">
                                            <i class="ti ti-device-mobile fs-2"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">Story / Reel</div>
                                            <div class="text-secondary" style="font-size: 11.5px;">1080 &times; 1920 &bull; 9:16</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-medium btn-canva-create-api shadow-xs" data-width="1080" data-height="1920" data-title="Social Story Post">
                                        Design
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Landscape Banner (1200x630) --}}
                        <div class="col-sm-6">
                            <div class="card p-3 h-100 canva-format-card border" style="border-radius: 12px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-2 bg-primary-subtle text-primary" style="width: 36px; height: 36px;">
                                            <i class="ti ti-brand-facebook fs-2"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">Landscape Banner</div>
                                            <div class="text-secondary" style="font-size: 11.5px;">1200 &times; 630 &bull; 1.91:1</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-medium btn-canva-create-api shadow-xs" data-width="1200" data-height="630" data-title="Facebook Landscape Banner">
                                        Design
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 4. YouTube Thumbnail (1280x720) --}}
                        <div class="col-sm-6">
                            <div class="card p-3 h-100 canva-format-card border" style="border-radius: 12px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-2 bg-danger-subtle text-danger" style="width: 36px; height: 36px;">
                                            <i class="ti ti-brand-youtube fs-2"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">YouTube Thumbnail</div>
                                            <div class="text-secondary" style="font-size: 11.5px;">1280 &times; 720 &bull; 16:9</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-medium btn-canva-create-api shadow-xs" data-width="1280" data-height="720" data-title="YouTube Video Thumbnail">
                                        Design
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Direct Import from Canva --}}
                <div class="pt-3 border-top mt-3" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-medium" style="font-size: 12px;">Import finished design</span>
                    </div>
                    <div class="input-group">
                        <input type="text" id="canva-design-id-input" class="form-control" placeholder="Paste Canva link or Design ID..." style="border-radius: 10px 0 0 10px; border-color: #d1d5db; font-size: 0.9rem; height: 42px;">
                        <button type="button" id="btn-import-canva-design" class="btn btn-primary px-4 fw-medium" style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="canva-import-spinner"></span>
                            Import to Post
                        </button>
                    </div>
                    <div id="canva-import-feedback" class="mt-2 small d-none"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.canva-format-card {
    border-color: #e2e8f0 !important;
    background: #ffffff;
    transition: all 0.15s ease;
}
.canva-format-card:hover {
    border-color: #cbd5e1 !important;
    background: #fafbfc;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
</style>
