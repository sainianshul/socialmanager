{{-- AI Caption & Hashtag Studio (Interactive Drawer / Panel) --}}
<div id="ai-caption-studio-box" class="card border border-primary-subtle shadow-sm mb-4 d-none" style="border-radius: 12px; background: linear-gradient(180deg, #f0fdfa 0%, #ffffff 100%);">
    <div class="card-header py-2.5 px-3.5 border-bottom border-primary-subtle bg-white rounded-top d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <span class="avatar avatar-xs rounded bg-primary-lt text-primary">
                <i class="ti ti-sparkles fs-3"></i>
            </span>
            <div>
                <span class="fw-bold text-dark small d-block">AI Caption & Hashtag Assistant</span>
                <span class="text-secondary fs-8">Craft high-converting hooks, engaging captions, and viral hashtags</span>
            </div>
        </div>
        <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm" id="btn-close-ai-studio" title="Close AI Assistant">
            <i class="ti ti-x"></i>
        </button>
    </div>

    <div class="card-body p-3.5">
        {{-- Prompt Input --}}
        <div class="mb-3">
            <label class="form-label fw-bold text-dark small mb-1">What is this post about?</label>
            <textarea id="ai-studio-topic" class="form-control form-control-sm" rows="2" placeholder="e.g. Launching our new summer collection with 30% discount this weekend, or 5 quick daily habits for peak productivity..."></textarea>
            
            {{-- Quick Idea Preset Pills (Horizontally Scrollable on Mobile) --}}
            <div class="d-flex align-items-center gap-1.5 overflow-x-auto pb-1 mt-2 flex-nowrap" id="ai-preset-pills" style="scrollbar-width: none;">
                <span class="text-secondary fs-8 me-1 fw-medium flex-shrink-0">Presets:</span>
                <button type="button" class="badge bg-white text-secondary border cursor-pointer hover-shadow flex-shrink-0 ai-preset-btn" data-preset="Product Launch announcement with early bird special discount">Launch</button>
                <button type="button" class="badge bg-white text-secondary border cursor-pointer hover-shadow flex-shrink-0 ai-preset-btn" data-preset="5 actionable tips and tricks that help you save time">Tips & Advice</button>
                <button type="button" class="badge bg-white text-secondary border cursor-pointer hover-shadow flex-shrink-0 ai-preset-btn" data-preset="Behind the scenes look at our team workspace and daily hustle">Behind The Scenes</button>
                <button type="button" class="badge bg-white text-secondary border cursor-pointer hover-shadow flex-shrink-0 ai-preset-btn" data-preset="Festival greetings and best wishes to our wonderful community">Festival Greeting</button>
                <button type="button" class="badge bg-white text-secondary border cursor-pointer hover-shadow flex-shrink-0 ai-preset-btn" data-preset="Limited-time weekend promotional flash sale 40% off">Special Offer</button>
                <button type="button" class="badge bg-white text-secondary border cursor-pointer hover-shadow flex-shrink-0 ai-preset-btn" data-preset="Ask the community: What is your #1 goal this month?">Ask Community</button>
            </div>
        </div>

        {{-- Controls Row: Tone, Platform, Language --}}
        <div class="row g-2 mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-secondary mb-1">Tone of Voice</label>
                <select class="form-select form-select-sm" id="ai-studio-tone">
                    <option value="engaging" selected>Engaging & Viral (Hook-Driven)</option>
                    <option value="professional">Professional & Authoritative</option>
                    <option value="casual">Casual & Friendly</option>
                    <option value="promotional">Promotional & High Conversion</option>
                    <option value="inspirational">Inspirational & Motivational</option>
                    <option value="humorous">Humorous & Entertaining</option>
                </select>
            </div>

            <div class="col-6 col-md-4">
                <label class="form-label small fw-semibold text-secondary mb-1">Target Format</label>
                <select class="form-select form-select-sm" id="ai-studio-platform">
                    <option value="instagram">Instagram (Aesthetic + Spacing)</option>
                    <option value="facebook">Facebook (Discussion + Stories)</option>
                    <option value="all" selected>Multi-Platform (Optimized)</option>
                </select>
            </div>

            <div class="col-6 col-md-4">
                <label class="form-label small fw-semibold text-secondary mb-1">Language</label>
                <select class="form-select form-select-sm" id="ai-studio-language">
                    <option value="English" selected>English</option>
                    <option value="Hinglish">Hinglish (Hindi + English)</option>
                    <option value="Hindi">Hindi (हिंदी)</option>
                </select>
            </div>
        </div>

        {{-- Trigger Button --}}
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-1.5 shadow-xs" id="btn-run-ai-caption">
                <i class="ti ti-wand fs-2"></i>
                <span id="btn-run-ai-text">Generate with AI</span>
            </button>
            <span class="text-secondary fs-8 d-none d-sm-inline">
                <i class="ti ti-bolt text-warning me-0.5"></i>Generates 3 hook variations and curated viral hashtags
            </span>
        </div>

        {{-- Results Area (Appears after generation) --}}
        <div id="ai-studio-results" class="mt-3.5 pt-3 border-top border-primary-subtle d-none">
            {{-- Variation Selector Tabs (Scrollable on Mobile) --}}
            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-1.5 overflow-x-auto flex-nowrap pb-1" id="ai-variation-pills" style="scrollbar-width: none; max-width: 100%;">
                    <span class="text-secondary fs-8 fw-semibold me-1 flex-shrink-0">Options:</span>
                    <button type="button" class="btn btn-xs btn-outline-primary active flex-shrink-0 ai-var-btn" data-index="0" id="ai-var-btn-0">Option 1: Hook & Story</button>
                    <button type="button" class="btn btn-xs btn-outline-primary flex-shrink-0 ai-var-btn" data-index="1" id="ai-var-btn-1">Option 2: Short & Punchy</button>
                    <button type="button" class="btn btn-xs btn-outline-primary flex-shrink-0 ai-var-btn" data-index="2" id="ai-var-btn-2">Option 3: Community Question</button>
                </div>
                <button type="button" class="btn btn-xs btn-ghost-secondary flex-shrink-0 d-inline-flex align-items-center gap-1" id="btn-rerun-ai-studio" title="Re-generate fresh variations">
                    <i class="ti ti-refresh"></i>Regenerate
                </button>
            </div>

            {{-- Caption Preview Box --}}
            <div class="mb-3">
                <textarea id="ai-caption-preview" class="form-control form-control-sm" rows="5" style="background: #ffffff; line-height: 1.5; font-size: 0.875rem;"></textarea>
            </div>

            {{-- Hashtags Cloud --}}
            <div class="mb-3 p-2.5 rounded-2 bg-white border">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-secondary fs-8 fw-bold text-uppercase" style="letter-spacing: 0.04em;">
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
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <button type="button" class="btn btn-success btn-sm px-3.5 d-inline-flex align-items-center gap-1.5 shadow-xs" id="btn-apply-ai-caption">
                    <i class="ti ti-check fs-2"></i>
                    <span>Apply to Post</span>
                </button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" id="btn-copy-full-ai-text">
                        <i class="ti ti-copy"></i>
                        <span>Copy Full Text</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
