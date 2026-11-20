{{-- AI Caption & Hashtag Assistant Modal --}}
<div class="modal fade" id="modal-ai-caption" tabindex="-1" aria-labelledby="modalAiCaptionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3" style="width: 44px; height: 44px;">
                        <i class="ti ti-sparkles fs-1"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalAiCaptionLabel">AI Caption & Hashtag Assistant</h5>
                        <p class="text-secondary small mb-0">Craft high-converting hooks, engaging captions, and viral hashtags tailored for your audience.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-white">
                {{-- Form Field 1: Topic / What is this post about --}}
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small mb-1">
                        What is this post about? <span class="text-danger">*</span>
                    </label>
                    <textarea id="ai-studio-topic" class="form-control" rows="3" placeholder="e.g. Launching our new summer collection with 30% discount this weekend, or sharing 5 actionable daily productivity habits..."></textarea>
                    
                    {{-- Quick Preset Badges --}}
                    <div class="d-flex align-items-center gap-1.5 overflow-x-auto pt-2 pb-1 flex-nowrap" id="ai-preset-pills" style="scrollbar-width: none;">
                        <span class="text-secondary small fw-medium flex-shrink-0" style="font-size: 11px;">Presets:</span>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2 ai-preset-btn" data-preset="Product Launch announcement with early bird special discount">Launch</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2 ai-preset-btn" data-preset="5 actionable tips and tricks that help you save time">Tips & Advice</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2 ai-preset-btn" data-preset="Behind the scenes look at our team workspace and daily hustle">Behind The Scenes</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2 ai-preset-btn" data-preset="Festival greetings and best wishes to our wonderful community">Festival Greeting</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2 ai-preset-btn" data-preset="Limited-time promotional flash sale 30% off this weekend">Special Offer</button>
                        <button type="button" class="badge bg-light text-secondary border cursor-pointer flex-shrink-0 py-1 px-2 ai-preset-btn" data-preset="Ask the community: What is your #1 goal this month?">Ask Community</button>
                    </div>
                </div>

                {{-- Form Controls Row: Normal Form Selects --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary mb-1">Tone of Voice</label>
                        <select class="form-select" id="ai-studio-tone">
                            <option value="engaging" selected>Engaging & Viral (Hook-Driven)</option>
                            <option value="professional">Professional & Authoritative</option>
                            <option value="casual">Casual & Friendly</option>
                            <option value="promotional">Promotional & High Conversion</option>
                            <option value="inspirational">Inspirational & Motivational</option>
                            <option value="humorous">Humorous & Entertaining</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary mb-1">Target Format</label>
                        <select class="form-select" id="ai-studio-platform">
                            <option value="all" selected>Multi-Platform (Optimized)</option>
                            <option value="instagram">Instagram (Aesthetic + Spacing)</option>
                            <option value="facebook">Facebook (Discussion + Stories)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary mb-1">Language</label>
                        <select class="form-select" id="ai-studio-language">
                            <option value="English" selected>English</option>
                            <option value="Hinglish">Hinglish (Hindi + English)</option>
                            <option value="Hindi">Hindi (हिंदी)</option>
                        </select>
                    </div>
                </div>

                {{-- Trigger Button --}}
                <div class="mb-3">
                    <button type="button" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-2" id="btn-run-ai-caption">
                        <i class="ti ti-sparkles fs-2"></i>
                        <span id="btn-run-ai-text">Generate Captions with AI</span>
                    </button>
                </div>

                {{-- Results Area (Appears after generation) --}}
                <div id="ai-studio-results" class="pt-3 border-top d-none" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <span class="text-uppercase fw-bold text-secondary" style="font-size: 11px; letter-spacing: 0.5px;">Select Variation</span>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill d-inline-flex align-items-center gap-1" id="btn-rerun-ai-studio">
                            <i class="ti ti-refresh"></i>Regenerate
                        </button>
                    </div>

                    {{-- Variation Selector Tabs --}}
                    <div class="d-flex align-items-center gap-1.5 overflow-x-auto flex-nowrap pb-2 mb-2" id="ai-variation-pills" style="scrollbar-width: none;">
                        <button type="button" class="btn btn-sm btn-outline-primary active flex-shrink-0 ai-var-btn" data-index="0" id="ai-var-btn-0">Option 1: Hook & Story</button>
                        <button type="button" class="btn btn-sm btn-outline-primary flex-shrink-0 ai-var-btn" data-index="1" id="ai-var-btn-1">Option 2: Short & Punchy</button>
                        <button type="button" class="btn btn-sm btn-outline-primary flex-shrink-0 ai-var-btn" data-index="2" id="ai-var-btn-2">Option 3: Community Question</button>
                    </div>

                    {{-- Caption Preview Box --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-1">Generated Caption (Editable)</label>
                        <textarea id="ai-caption-preview" class="form-control" rows="5" style="background: #ffffff; line-height: 1.5; font-size: 0.875rem;"></textarea>
                    </div>

                    {{-- Hashtags Cloud --}}
                    <div class="mb-3 p-3 rounded-3" style="background: #fafbfc; border: 1px solid #f1f5f9;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary fw-bold small">
                                <i class="ti ti-hash me-1 text-primary"></i>Trending Hashtags (Click to toggle)
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-link btn-xs text-decoration-none p-0" id="btn-toggle-all-hashtags">Select All</button>
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
                        <button type="button" class="btn btn-success rounded-pill px-4 py-2 d-inline-flex align-items-center gap-1.5 shadow-xs" id="btn-apply-ai-caption">
                            <i class="ti ti-check fs-2"></i>
                            <span class="fw-semibold">Apply to Post</span>
                        </button>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1" id="btn-copy-full-ai-text">
                                <i class="ti ti-copy"></i>
                                <span>Copy Text</span>
                            </button>
                            <button type="button" class="btn btn-light rounded-pill px-3 py-2" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
