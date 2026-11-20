{{-- AI Caption Assistant Modal --}}
<div class="modal fade" id="modal-ai-caption" tabindex="-1" aria-labelledby="modalAiCaptionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 680px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-1 pt-4 px-4 bg-white">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3" style="width: 36px; height: 36px;">
                        <i class="ti ti-sparkles fs-2"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalAiCaptionLabel" style="font-size: 1.05rem;">Caption Assistant</h5>
                        <p class="text-secondary small mb-0">Describe your topic to generate captions and hashtags</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-white">
                {{-- Form Field 1: Topic --}}
                <div class="mb-3">
                    <label class="form-label text-dark fw-medium small mb-1.5">Topic or summary</label>
                    <textarea id="ai-studio-topic" class="form-control" rows="2" placeholder="e.g. Product launch, weekend sale, or daily tips..." style="font-size: 0.9rem; border-radius: 8px;"></textarea>
                    
                    {{-- Minimal Quick Idea Tags --}}
                    <div class="d-flex align-items-center gap-1.5 overflow-x-auto pt-2 pb-0.5 flex-nowrap" id="ai-preset-pills" style="scrollbar-width: none;">
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2.5 rounded-pill ai-preset-btn fw-normal" data-preset="Product launch announcement">Launch</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2.5 rounded-pill ai-preset-btn fw-normal" data-preset="Helpful tips and advice">Tips</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2.5 rounded-pill ai-preset-btn fw-normal" data-preset="Behind the scenes look">Behind The Scenes</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2.5 rounded-pill ai-preset-btn fw-normal" data-preset="Community greeting and best wishes">Greeting</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2.5 rounded-pill ai-preset-btn fw-normal" data-preset="Special offer and discount">Offer</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2.5 rounded-pill ai-preset-btn fw-normal" data-preset="Question for the community">Question</button>
                    </div>
                </div>

                {{-- Form Controls Row: 3 Clean Selects --}}
                <div class="row g-2.5 mb-3">
                    <div class="col-md-4">
                        <label class="form-label text-secondary small fw-medium mb-1">Tone</label>
                        <select class="form-select form-select-sm" id="ai-studio-tone" style="border-radius: 8px;">
                            <option value="engaging" selected>Engaging</option>
                            <option value="professional">Professional</option>
                            <option value="casual">Casual</option>
                            <option value="promotional">Promotional</option>
                            <option value="inspirational">Inspirational</option>
                            <option value="humorous">Humorous</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-secondary small fw-medium mb-1">Platform</label>
                        <select class="form-select form-select-sm" id="ai-studio-platform" style="border-radius: 8px;">
                            <option value="all" selected>All Platforms</option>
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-secondary small fw-medium mb-1">Language</label>
                        <select class="form-select form-select-sm" id="ai-studio-language" style="border-radius: 8px;">
                            <option value="English" selected>English</option>
                            <option value="Hinglish">Hinglish</option>
                            <option value="Hindi">Hindi</option>
                        </select>
                    </div>
                </div>

                {{-- Trigger Button --}}
                <div class="mb-2">
                    <button type="button" class="btn btn-primary w-100 rounded-pill py-2 fw-medium shadow-xs d-flex align-items-center justify-content-center gap-2" id="btn-run-ai-caption" style="font-size: 0.9rem;">
                        <i class="ti ti-sparkles"></i>
                        <span id="btn-run-ai-text">Generate Caption</span>
                    </button>
                </div>

                {{-- Results Area (Appears after generation) --}}
                <div id="ai-studio-results" class="pt-3 border-top mt-3 d-none" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-medium" style="font-size: 11px;">Select variation</span>
                        <button type="button" class="btn btn-xs btn-ghost-secondary rounded-pill d-inline-flex align-items-center gap-1 text-decoration-none" id="btn-rerun-ai-studio">
                            <i class="ti ti-refresh"></i>Regenerate
                        </button>
                    </div>

                    {{-- Variation Selector Tabs --}}
                    <div class="d-flex align-items-center gap-1.5 overflow-x-auto flex-nowrap pb-2 mb-2" id="ai-variation-pills" style="scrollbar-width: none;">
                        <button type="button" class="btn btn-xs btn-outline-primary active flex-shrink-0 rounded-pill ai-var-btn px-2.5 py-1" data-index="0" id="ai-var-btn-0">Option 1</button>
                        <button type="button" class="btn btn-xs btn-outline-primary flex-shrink-0 rounded-pill ai-var-btn px-2.5 py-1" data-index="1" id="ai-var-btn-1">Option 2</button>
                        <button type="button" class="btn btn-xs btn-outline-primary flex-shrink-0 rounded-pill ai-var-btn px-2.5 py-1" data-index="2" id="ai-var-btn-2">Option 3</button>
                    </div>

                    {{-- Caption Preview Box --}}
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-medium mb-1">Generated text</label>
                        <textarea id="ai-caption-preview" class="form-control" rows="4" style="background: #ffffff; line-height: 1.5; font-size: 0.875rem; border-radius: 8px;"></textarea>
                    </div>

                    {{-- Hashtags Cloud --}}
                    <div class="mb-3 p-2.5 rounded-3 bg-light-subtle" style="border: 1px solid #f1f5f9;">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="text-secondary small fw-medium" style="font-size: 11px;">Hashtags</span>
                            <div class="d-flex align-items-center gap-2" style="font-size: 11px;">
                                <button type="button" class="btn btn-link btn-xs text-decoration-none p-0 text-primary" id="btn-toggle-all-hashtags">Select All</button>
                                <span class="text-muted">&bull;</span>
                                <button type="button" class="btn btn-link btn-xs text-decoration-none p-0 text-secondary" id="btn-copy-only-hashtags">Copy Tags</button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-1.5" id="ai-hashtags-cloud">
                            {{-- Hashtag chips injected dynamically --}}
                        </div>
                    </div>

                    {{-- Bottom Insertion Actions --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-1">
                        <button type="button" class="btn btn-primary rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-xs" id="btn-apply-ai-caption" style="font-size: 0.875rem;">
                            <i class="ti ti-check"></i>
                            <span class="fw-medium">Apply to Post</span>
                        </button>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1" id="btn-copy-full-ai-text" style="font-size: 0.875rem;">
                                <i class="ti ti-copy"></i>
                                <span>Copy</span>
                            </button>
                            <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 text-secondary" data-bs-dismiss="modal" style="font-size: 0.875rem;">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
