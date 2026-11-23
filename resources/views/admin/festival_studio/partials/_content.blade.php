{{-- Festival & Gallery Control Bar --}}
@if($selectedFestival)
    @php
        $isToday = $selectedFestival->festival_date->isToday();
        $targetTime = $isToday ? now()->addMinutes(10)->format('H:i') : '08:00';
        $targetTimeFormatted = $isToday ? now()->addMinutes(10)->format('h:i A') : '08:00 AM';
        $captionText = ($selectedFestival->ready_caption ?? '') . ($selectedFestival->hashtags ? "\n\n" . $selectedFestival->hashtags : '');
    @endphp

    {{-- Top Bar: Festival Title, Badges & Caption Action --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h2 class="fw-bold m-0 text-dark" style="font-size: 1.45rem; letter-spacing: -0.02em;">
                        {{ $selectedFestival->title }}
                    </h2>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 font-monospace small">
                        {{ $selectedFestival->festival_date->format('d M Y') }}
                    </span>
                    @if($isToday)
                        <span class="badge bg-danger rounded-pill px-2.5 py-1 text-white small">Today</span>
                    @endif
                </div>
                <div class="text-secondary small mt-0.5">
                    Click any design below to customize in the studio panel and schedule
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Quick Copy Caption Button --}}
            @if(!empty($selectedFestival->ready_caption))
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 shadow-none" id="btn-copy-caption">
                    <i class="ti ti-copy me-1"></i><span id="copy-btn-text">Copy Caption</span>
                </button>
                <div id="festival-caption-text" class="d-none">{{ $captionText }}</div>
            @endif

            <span class="text-secondary small font-monospace">
                {{ $templates->count() }} Designs
            </span>
        </div>
    </div>

    {{-- Clean Normal-Size Templates Grid (2-3 Templates per Festival) --}}
    @if($templates->isNotEmpty())
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4 mb-5" id="fs-template-grid">
            @foreach($templates as $template)
                @php
                    $isScheduled = in_array($template->id, $scheduledTemplateIds ?? []);
                    $tplFooterStyle = $template->layout_config['footer']['style'] ?? ($template->preset_key === 'solid_white_banner' ? 'solid_white_banner' : 'dual_ribbon_badge');
                    $tplHeaderLayout = $template->layout_config['header']['layout'] ?? ($template->layout_config['header']['mode'] ?? 'full_width_strip');
                    $tplFooterFixed = !empty($template->layout_config['footer']['is_fixed']) ? '1' : '0';
                    $tplHeaderFixed = !empty($template->layout_config['header']['is_fixed']) ? '1' : '0';
                    $tplLeaderShape = $template->layout_config['header']['chip_shape'] ?? 'circle';
                    $tplLeaderBorder = $template->layout_config['header']['chip_border_color'] ?? '#FF9933';
                @endphp
                <div class="col" id="template-card-col-{{ $template->id }}">
                    <div class="fs-template-card h-100 d-flex flex-column shadow-xs rounded-4 overflow-hidden" id="template-card-{{ $template->id }}">
                        
                        {{-- 1:1 Aspect Ratio Large Poster Container --}}
                        <div class="fs-template-preview-wrapper cursor-pointer btn-open-slide-panel"
                             data-template-id="{{ $template->id }}"
                             data-template-title="{{ $template->title }}"
                             data-template-image="{{ $template->image_url }}"
                             data-festival-id="{{ $selectedFestival?->id }}"
                             data-festival-title="{{ $selectedFestival?->title }}"
                             data-festival-date="{{ $selectedFestival?->festival_date?->format('Y-m-d') }}"
                             data-festival-date-formatted="{{ $selectedFestival?->festival_date?->format('d M Y') }}"
                             data-caption="{{ $captionText }}"
                             data-footer-style="{{ $tplFooterStyle }}"
                             data-footer-fixed="{{ $tplFooterFixed }}"
                             data-header-layout="{{ $tplHeaderLayout }}"
                             data-header-fixed="{{ $tplHeaderFixed }}"
                             data-leader-shape="{{ $tplLeaderShape }}"
                             data-leader-border="{{ $tplLeaderBorder }}"
                             data-party-id="{{ $template->party_id ?? '' }}"
                             data-strip-id="{{ $template->layout_config['header']['strip_id'] ?? '' }}">
                            
                            <img src="{{ $template->image_url }}" alt="{{ $template->title }}" class="fs-template-preview-img w-100 h-100" loading="lazy">
                            
                            {{-- Top Party Badge --}}
                            @if($template->party)
                                <div class="position-absolute top-0 start-0 m-2.5 d-flex gap-1 z-1">
                                    <span class="badge bg-dark text-white rounded-pill shadow-xs" style="font-size: 11px; padding: 4px 10px;">
                                        {{ $template->party->short_code }}
                                    </span>
                                </div>
                            @endif

                            {{-- Hover Actions Overlay --}}
                            <div class="fs-template-overlay">
                                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 w-100 shadow-sm btn-open-slide-panel"
                                        data-template-id="{{ $template->id }}"
                                        data-template-title="{{ $template->title }}"
                                        data-template-image="{{ $template->image_url }}"
                                        data-footer-style="{{ $tplFooterStyle }}"
                                        data-footer-fixed="{{ $tplFooterFixed }}"
                                        data-header-layout="{{ $tplHeaderLayout }}"
                                        data-header-fixed="{{ $tplHeaderFixed }}"
                                        data-leader-shape="{{ $tplLeaderShape }}"
                                        data-leader-border="{{ $tplLeaderBorder }}"
                                        data-party-id="{{ $template->party_id ?? '' }}"
                                        data-strip-id="{{ $template->layout_config['header']['strip_id'] ?? '' }}">
                                    <i class="ti ti-palette me-1"></i>Customize in Studio
                                </button>
                            </div>
                        </div>

                        {{-- Card Footer with Title & Schedule Status --}}
                        <div class="p-3 d-flex align-items-center justify-content-between bg-white border-top">
                            <span class="fw-bold text-dark text-truncate me-2" title="{{ $template->title }}" id="card-title-{{ $template->id }}" style="font-size: 0.95rem;">
                                {{ $template->title }}
                            </span>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0" id="card-action-{{ $template->id }}">
                                @if($isScheduled)
                                    <span class="badge bg-success text-white rounded-pill px-3 py-1.5 small fw-semibold">
                                        <i class="ti ti-check me-1"></i>Scheduled
                                    </span>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold btn-open-slide-panel"
                                            data-template-id="{{ $template->id }}"
                                            data-template-title="{{ $template->title }}"
                                            data-template-image="{{ $template->image_url }}"
                                            data-footer-style="{{ $tplFooterStyle }}"
                                            data-footer-fixed="{{ $tplFooterFixed }}"
                                            data-header-layout="{{ $tplHeaderLayout }}"
                                            data-header-fixed="{{ $tplHeaderFixed }}"
                                            data-leader-shape="{{ $tplLeaderShape }}"
                                            data-leader-border="{{ $tplLeaderBorder }}"
                                            data-party-id="{{ $template->party_id ?? '' }}"
                                            data-strip-id="{{ $template->layout_config['header']['strip_id'] ?? '' }}">
                                        Customize
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="border border-dashed rounded-4 p-5 text-center my-4 bg-light-subtle">
            <div class="avatar avatar-md bg-secondary-lt rounded-circle mb-2">
                <i class="ti ti-photo-off text-secondary fs-3"></i>
            </div>
            <div class="fw-semibold text-dark mb-1">No templates found for this festival</div>
            <div class="text-secondary small">Select another upcoming festival date to explore designs.</div>
        </div>
    @endif

    {{-- Include Right Slide-Over Panel --}}
    @include('admin.festival_studio.partials._slide_panel')

@else
    <div class="border border-dashed rounded-4 p-5 text-center my-4 bg-light-subtle">
        <div class="avatar avatar-md bg-secondary-lt rounded-circle mb-2">
            <i class="ti ti-calendar-off text-secondary fs-3"></i>
        </div>
        <div class="fw-semibold text-dark mb-1">No upcoming festivals found</div>
        <div class="text-secondary small">Please add festival events to manage posters.</div>
    </div>
@endif
