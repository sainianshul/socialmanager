{{-- Canva Design Studio Modal (Human-Crafted Minimal) --}}
<div class="modal fade" id="modal-canva-studio" tabindex="-1" aria-labelledby="modalCanvaStudioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 660px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            {{-- Modal Header: Spacious, clean, no overlapping badges or crosses --}}
            <div class="modal-header border-0 pb-1 pt-4 px-4 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="d-inline-flex align-items-center justify-content-center text-white fw-bold rounded-circle shadow-xs" style="width: 32px; height: 32px; background: linear-gradient(135deg, #00c4cc, #7d2ae8); font-family: -apple-system, BlinkMacSystemFont, sans-serif; font-style: italic; font-size: 18px; line-height: 1;">
                        C
                    </span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalCanvaStudioLabel" style="font-size: 1.15rem; letter-spacing: -0.2px;">Canva Design Studio</h5>
                </div>
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                    Close
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body px-4 pt-3 pb-3 bg-white">
                {{-- Format Presets Section --}}
                <div class="mb-4">
                    <div class="text-secondary small fw-medium mb-2.5" style="font-size: 12px; letter-spacing: 0.3px;">CHOOSE FORMAT</div>

                    <div class="row g-3">
                        {{-- 1. Square Post (1080x1080) --}}
                        <div class="col-sm-6">
                            <div class="card p-3 h-100 canva-format-card border" style="border-radius: 14px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 format-icon-box" style="width: 38px; height: 38px; background: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a;">
                                            <i class="ti ti-brand-instagram fs-2"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">Square Post</div>
                                            <div class="text-secondary" style="font-size: 11.5px;">1080 &times; 1080 &bull; 1:1</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-medium btn-canva-create-api shadow-xs format-action-btn" data-width="1080" data-height="1080" data-title="Social Square Post">
                                        Design
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Story / Reel (1080x1920) --}}
                        <div class="col-sm-6">
                            <div class="card p-3 h-100 canva-format-card border" style="border-radius: 14px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 format-icon-box" style="width: 38px; height: 38px; background: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a;">
                                            <i class="ti ti-device-mobile fs-2"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">Story / Reel</div>
                                            <div class="text-secondary" style="font-size: 11.5px;">1080 &times; 1920 &bull; 9:16</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-medium btn-canva-create-api shadow-xs format-action-btn" data-width="1080" data-height="1920" data-title="Social Story Post">
                                        Design
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Landscape Banner (1200x630) --}}
                        <div class="col-sm-6">
                            <div class="card p-3 h-100 canva-format-card border" style="border-radius: 14px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 format-icon-box" style="width: 38px; height: 38px; background: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a;">
                                            <i class="ti ti-brand-facebook fs-2"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">Landscape Banner</div>
                                            <div class="text-secondary" style="font-size: 11.5px;">1200 &times; 630 &bull; 1.91:1</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-medium btn-canva-create-api shadow-xs format-action-btn" data-width="1200" data-height="630" data-title="Facebook Landscape Banner">
                                        Design
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 4. YouTube Thumbnail (1280x720) --}}
                        <div class="col-sm-6">
                            <div class="card p-3 h-100 canva-format-card border" style="border-radius: 14px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 format-icon-box" style="width: 38px; height: 38px; background: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a;">
                                            <i class="ti ti-brand-youtube fs-2"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">YouTube Thumbnail</div>
                                            <div class="text-secondary" style="font-size: 11.5px;">1280 &times; 720 &bull; 16:9</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-medium btn-canva-create-api shadow-xs format-action-btn" data-width="1280" data-height="720" data-title="YouTube Video Thumbnail">
                                        Design
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Direct Import from Canva --}}
                <div class="pt-3 border-top mt-2" style="border-color: #f1f5f9 !important;">
                    <label class="form-label text-dark fw-semibold small mb-2" for="canva-design-id-input">Import Finished Design</label>
                    <div class="input-group">
                        <input type="text" id="canva-design-id-input" class="form-control" placeholder="Paste Canva link or Design ID..." style="border-radius: 10px 0 0 10px; border-color: #d1d5db; font-size: 0.9rem; height: 44px;">
                        <button type="button" id="btn-import-canva-design" class="btn btn-primary px-4 fw-medium" style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="canva-import-spinner"></span>
                            Import to Post
                        </button>
                    </div>
                    <div id="canva-import-feedback" class="mt-2 small d-none"></div>
                </div>
            </div>

            {{-- Modal Footer: Subtle status & dismiss --}}
            <div class="modal-footer border-0 pt-0 px-4 pb-4 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2" style="font-size: 12px;">
                    <span id="canva-status-dot" class="badge bg-secondary p-1 rounded-circle canva-status-dot" style="width: 7px; height: 7px;"></span>
                    <span id="canva-status-text" class="text-secondary fw-medium canva-status-text">Checking...</span>
                    <a href="{{ route('admin.canva.auth') }}" id="btn-connect-canva" class="btn btn-link btn-xs text-primary text-decoration-none p-0 ms-1 fw-semibold btn-connect-canva">Connect Account</a>
                    <button type="button" id="btn-disconnect-canva" class="btn btn-link btn-xs text-muted text-decoration-none p-0 ms-1 d-none btn-disconnect-canva" style="font-size: 11px;">Disconnect</button>
                </div>
                <button type="button" class="btn btn-light rounded-pill px-4 py-1.5 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Dedicated YouTube Thumbnail Studio Modal --}}
<div class="modal fade" id="modal-canva-thumbnail" tabindex="-1" aria-labelledby="modalCanvaThumbnailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-1 pt-4 px-4 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="d-inline-flex align-items-center justify-content-center text-white fw-bold rounded-circle shadow-xs" style="width: 32px; height: 32px; background: linear-gradient(135deg, #00c4cc, #7d2ae8); font-family: -apple-system, BlinkMacSystemFont, sans-serif; font-style: italic; font-size: 18px; line-height: 1;">
                        C
                    </span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalCanvaThumbnailLabel" style="font-size: 1.15rem; letter-spacing: -0.2px;">YouTube Thumbnail Studio</h5>
                </div>
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                    Close
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body px-4 pt-3 pb-3 bg-white">
                {{-- Format Hero Card --}}
                <div class="card p-3 border mb-3" style="border-radius: 14px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: #fef2f2; border: 1px solid #fee2e2; color: #dc2626;">
                                <i class="ti ti-brand-youtube fs-1"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 1rem;">Video Thumbnail Canvas</div>
                                <div class="text-secondary" style="font-size: 12px;">1280 &times; 720 px &bull; High Resolution</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-canva-brand rounded-pill px-3.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs" id="btn-create-yt-thumbnail-api" style="font-size: 0.85rem;">
                                <i class="ti ti-brand-canva"></i>
                                <span>Open in Canva</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: #f1f5f9 !important;">
                        <span class="text-secondary small" style="font-size: 11.5px;">Need ready-made thumbnail inspiration?</span>
                        <a href="https://www.canva.com/create/youtube-thumbnails/" target="_blank" class="text-decoration-none small fw-semibold text-primary d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                            <i class="ti ti-template"></i>
                            <span>Browse Canva Thumbnail Templates</span>
                            <i class="ti ti-external-link" style="font-size: 11px;"></i>
                        </a>
                    </div>
                </div>

                {{-- Direct Import from Canva --}}
                <div class="pt-2 border-top mt-3" style="border-color: #f1f5f9 !important;">
                    <label class="form-label text-dark fw-semibold small mb-2" for="canva-thumbnail-id-input">Import Finished Thumbnail</label>
                    <div class="input-group">
                        <input type="text" id="canva-thumbnail-id-input" class="form-control" placeholder="Paste Canva link or Design ID (e.g. DAFVztcvd9z)..." style="border-radius: 10px 0 0 10px; border-color: #d1d5db; font-size: 0.9rem; height: 44px;">
                        <button type="button" id="btn-import-canva-thumbnail" class="btn btn-primary px-4 fw-medium" style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="canva-thumb-import-spinner"></span>
                            Import to Thumbnail
                        </button>
                    </div>
                    <div id="canva-thumbnail-feedback" class="mt-2 small d-none"></div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-0 pt-0 px-4 pb-4 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2" style="font-size: 12px;">
                    <span class="badge bg-secondary p-1 rounded-circle canva-status-dot" style="width: 7px; height: 7px;"></span>
                    <span class="text-secondary fw-medium canva-status-text">Checking...</span>
                    <a href="{{ route('admin.canva.auth') }}" class="btn btn-link btn-xs text-primary text-decoration-none p-0 ms-1 fw-semibold btn-connect-canva">Connect Account</a>
                    <button type="button" class="btn btn-link btn-xs text-muted text-decoration-none p-0 ms-1 d-none btn-disconnect-canva" style="font-size: 11px;">Disconnect</button>
                </div>
                <button type="button" class="btn btn-light rounded-pill px-4 py-1.5 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.canva-format-card {
    border-color: #e2e8f0 !important;
    background: #ffffff;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.canva-format-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    transform: translateY(-1px);
}
.format-action-btn {
    transition: all 0.15s ease;
}
.format-action-btn:hover {
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
}
</style>
