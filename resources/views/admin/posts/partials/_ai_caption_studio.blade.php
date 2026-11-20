{{-- AI Caption Assistant Modal --}}
<div class="modal fade" id="modal-ai-caption" tabindex="-1" aria-labelledby="modalAiCaptionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 780px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3" style="width: 38px; height: 38px;">
                        <i class="ti ti-sparkles fs-2"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalAiCaptionLabel" style="font-size: 1.15rem;">Caption Assistant</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-white">
                {{-- Unified Prompt Card: Textarea + Embedded Ideas Toolbar --}}
                <div class="mb-3.5">
                    <label class="form-label text-dark fw-semibold small mb-1.5">Topic</label>
                    <div class="card p-3 shadow-none unified-topic-card" style="border: 1px solid #d1d5db !important; border-radius: 12px; background: #ffffff;">
                        <textarea id="ai-studio-topic" class="form-control border-0 p-0 shadow-none" rows="3" placeholder="What is this post about?" style="resize: none; background: transparent; font-size: 0.95rem; line-height: 1.5; color: #1e293b;"></textarea>
                        
                        {{-- Embedded Ideas Toolbar --}}
                        <div class="d-flex align-items-center justify-content-between pt-2.5 mt-2 border-top" style="border-color: #f1f5f9 !important;">
                            <div class="d-flex align-items-center gap-1.5 overflow-x-auto flex-nowrap" id="ai-preset-pills" style="scrollbar-width: none;">
                                <span class="text-secondary small fw-medium flex-shrink-0" style="font-size: 11px; color: #94a3b8;">Ideas:</span>
                                <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 ai-preset-btn" data-preset="Product launch announcement">
                                    <i class="ti ti-rocket me-1 text-primary"></i>Launch
                                </button>
                                <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 ai-preset-btn" data-preset="Helpful tips and advice">
                                    <i class="ti ti-bulb me-1 text-warning"></i>Tips
                                </button>
                                <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 ai-preset-btn" data-preset="Behind the scenes look">
                                    <i class="ti ti-camera me-1 text-info"></i>Behind The Scenes
                                </button>
                                <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 ai-preset-btn" data-preset="Community greeting and best wishes">
                                    <i class="ti ti-heart me-1 text-danger"></i>Greeting
                                </button>
                                <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 ai-preset-btn" data-preset="Special offer and discount">
                                    <i class="ti ti-percentage me-1 text-success"></i>Offer
                                </button>
                                <button type="button" class="btn btn-xs rounded-pill px-2.5 py-1 ai-preset-btn" data-preset="Question for the community">
                                    <i class="ti ti-help me-1 text-purple"></i>Question
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Controls Row: Normal Standard Selects with Increased Size --}}
                <div class="row g-3 mb-3.5">
                    <div class="col-md-4">
                        <label class="form-label text-secondary small fw-medium mb-1.5">Tone</label>
                        <select class="form-select form-control" id="ai-studio-tone" style="height: 44px; font-size: 0.95rem; border-radius: 10px; border-color: #d1d5db;">
                            <option value="engaging" selected>Engaging</option>
                            <option value="professional">Professional</option>
                            <option value="casual">Casual</option>
                            <option value="promotional">Promotional</option>
                            <option value="inspirational">Inspirational</option>
                            <option value="humorous">Humorous</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-secondary small fw-medium mb-1.5">Platform</label>
                        <select class="form-select form-control" id="ai-studio-platform" style="height: 44px; font-size: 0.95rem; border-radius: 10px; border-color: #d1d5db;">
                            <option value="all" selected>All Platforms</option>
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-secondary small fw-medium mb-1.5">Language</label>
                        <select class="form-select form-control" id="ai-studio-language" style="height: 44px; font-size: 0.95rem; border-radius: 10px; border-color: #d1d5db;">
                            <option value="English" selected>English</option>
                            <option value="Hinglish">Hinglish</option>
                            <option value="Hindi">Hindi</option>
                        </select>
                    </div>
                </div>

                {{-- Generate Button: Compact, right-aligned, clean margins --}}
                <div class="d-flex justify-content-end mt-3 mb-2">
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-medium shadow-xs d-inline-flex align-items-center gap-2" id="btn-run-ai-caption" style="font-size: 0.9rem;">
                        <i class="ti ti-sparkles"></i>
                        <span id="btn-run-ai-text">Generate Caption</span>
                    </button>
                </div>

                {{-- Results Area (Appears after generation) --}}
                <div id="ai-studio-results" class="pt-4 border-top mt-4 d-none" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2.5">
                        <span class="text-secondary small fw-medium" style="font-size: 12px;">Select variation</span>
                        <button type="button" class="btn btn-xs btn-ghost-secondary rounded-pill d-inline-flex align-items-center gap-1 text-decoration-none" id="btn-rerun-ai-studio">
                            <i class="ti ti-refresh"></i>Regenerate
                        </button>
                    </div>

                    {{-- Variation Selector Tabs --}}
                    <div class="d-flex align-items-center gap-2 overflow-x-auto flex-nowrap pb-2 mb-3" id="ai-variation-pills" style="scrollbar-width: none;">
                        <button type="button" class="btn btn-sm btn-outline-primary active flex-shrink-0 rounded-pill ai-var-btn px-3 py-1.5" data-index="0" id="ai-var-btn-0">Option 1</button>
                        <button type="button" class="btn btn-sm btn-outline-primary flex-shrink-0 rounded-pill ai-var-btn px-3 py-1.5" data-index="1" id="ai-var-btn-1">Option 2</button>
                        <button type="button" class="btn btn-sm btn-outline-primary flex-shrink-0 rounded-pill ai-var-btn px-3 py-1.5" data-index="2" id="ai-var-btn-2">Option 3</button>
                    </div>

                    {{-- Caption Preview Box --}}
                    <div class="mb-3.5">
                        <label class="form-label text-secondary small fw-medium mb-1.5">Generated text</label>
                        <textarea id="ai-caption-preview" class="form-control" rows="5" style="background: #ffffff; line-height: 1.6; font-size: 0.95rem; border-radius: 10px; border-color: #d1d5db;"></textarea>
                    </div>

                    {{-- Hashtags Cloud --}}
                    <div class="mb-3.5 p-3 rounded-3 bg-light-subtle" style="border: 1px solid #f1f5f9;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary small fw-medium" style="font-size: 12px;">Hashtags</span>
                            <div class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                <button type="button" class="btn btn-link btn-xs text-decoration-none p-0 text-primary fw-medium" id="btn-toggle-all-hashtags">Select All</button>
                                <span class="text-muted">&bull;</span>
                                <button type="button" class="btn btn-link btn-xs text-decoration-none p-0 text-secondary" id="btn-copy-only-hashtags">Copy Tags</button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-1.5" id="ai-hashtags-cloud">
                            {{-- Hashtag chips injected dynamically --}}
                        </div>
                    </div>

                    {{-- Bottom Insertion Actions --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 d-inline-flex align-items-center gap-1.5 shadow-xs" id="btn-apply-ai-caption" style="font-size: 0.9rem;">
                            <i class="ti ti-check"></i>
                            <span class="fw-medium">Apply to Post</span>
                        </button>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-1" id="btn-copy-full-ai-text" style="font-size: 0.9rem;">
                                <i class="ti ti-copy"></i>
                                <span>Copy</span>
                            </button>
                            <button type="button" class="btn btn-light rounded-pill px-3.5 py-2 text-secondary" data-bs-dismiss="modal" style="font-size: 0.9rem;">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.unified-topic-card:focus-within {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
}
.ai-preset-btn {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-size: 11.5px;
    font-weight: 500;
    transition: all 0.15s ease;
}
.ai-preset-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #0f172a;
}
.ai-preset-btn.active {
    background: #eff6ff !important;
    border-color: #bfdbfe !important;
    color: #1d4ed8 !important;
    box-shadow: 0 1px 2px rgba(29, 78, 216, 0.05);
}
</style>
