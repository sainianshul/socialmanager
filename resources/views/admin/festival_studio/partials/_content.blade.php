{{-- Festival & Gallery Control Bar --}}
@if($selectedFestival)
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold m-0 text-dark">{{ $selectedFestival->title }}</h3>
            <span class="badge bg-primary-lt rounded-pill">{{ $selectedFestival->festival_date->format('d M Y') }}</span>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Quick Copy Caption Button --}}
            @if(!empty($selectedFestival->ready_caption))
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" id="btn-copy-caption">
                    <i class="ti ti-copy me-1"></i><span id="copy-btn-text">Copy Caption</span>
                </button>
                {{-- Hidden text storage for copy --}}
                <div id="festival-caption-text" class="d-none">{{ $selectedFestival->ready_caption }}{{ !empty($selectedFestival->hashtags) ? "\n\n" . $selectedFestival->hashtags : '' }}</div>
            @endif
        </div>
    </div>
@endif

{{-- Templates Grid with Live User Branding --}}
@if($templates->isNotEmpty())
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3 mb-4">
        @foreach($templates as $template)
            @php
                $captionText = ($selectedFestival?->ready_caption ?? '') . ($selectedFestival?->hashtags ? "\n\n" . $selectedFestival->hashtags : '');
                $composerUrl = route('admin.posts.create', [
                    'date' => $selectedFestival?->festival_date?->format('Y-m-d\T08:00'),
                    'caption' => $captionText,
                ]);
                $isToday = $selectedFestival?->festival_date?->isToday() ?? false;
                $targetTime = $isToday ? now()->addMinutes(10)->format('H:i') : '08:00';
                $targetTimeFormatted = $isToday ? now()->addMinutes(10)->format('h:i A') . ' (in 10 min)' : '08:00 AM';
                $isScheduled = in_array($template->id, $scheduledTemplateIds ?? []);
            @endphp
            <div class="col" id="template-card-col-{{ $template->id }}">
                <div class="fs-template-card h-100 d-flex flex-column" id="template-card-{{ $template->id }}">
                    
                    {{-- 1:1 Aspect Ratio Poster Container --}}
                    <div class="fs-template-preview-wrapper cursor-pointer btn-trigger-quick-schedule"
                         data-template-id="{{ $template->id }}"
                         data-template-title="{{ $template->title }}"
                         data-template-image="{{ $template->image_url }}"
                         data-festival-id="{{ $selectedFestival?->id }}"
                         data-festival-title="{{ $selectedFestival?->title }}"
                         data-festival-date="{{ $selectedFestival?->festival_date?->format('Y-m-d') }}"
                         data-festival-date-formatted="{{ $selectedFestival?->festival_date?->format('d M Y') }}"
                         data-is-today="{{ $isToday ? '1' : '0' }}"
                         data-target-time="{{ $targetTime }}"
                         data-target-time-formatted="{{ $targetTimeFormatted }}"
                         data-caption="{{ $captionText }}"
                         data-composer-url="{{ $composerUrl }}">
                        
                        <img src="{{ $template->image_url }}" alt="{{ $template->title }}" class="fs-template-preview-img" loading="lazy">
                        
                        {{-- Top Party Badge --}}
                        <div class="position-absolute top-0 start-0 m-2.5 d-flex gap-1 z-1">
                            @if($template->party)
                                <span class="badge bg-dark text-white rounded-pill shadow-xs" style="font-size: 10px; padding: 3px 8px;">
                                    {{ $template->party->short_code }}
                                </span>
                            @endif
                        </div>

                        {{-- Hover Actions Overlay --}}
                        <div class="fs-template-overlay">
                            <button type="button" class="btn btn-primary rounded-pill px-3 py-2 w-100 shadow-sm btn-trigger-quick-schedule"
                                    data-template-id="{{ $template->id }}"
                                    data-template-title="{{ $template->title }}"
                                    data-template-image="{{ $template->image_url }}"
                                    data-festival-id="{{ $selectedFestival?->id }}"
                                    data-festival-title="{{ $selectedFestival?->title }}"
                                    data-festival-date="{{ $selectedFestival?->festival_date?->format('Y-m-d') }}"
                                    data-festival-date-formatted="{{ $selectedFestival?->festival_date?->format('d M Y') }}"
                                    data-is-today="{{ $isToday ? '1' : '0' }}"
                                    data-target-time="{{ $targetTime }}"
                                    data-target-time-formatted="{{ $targetTimeFormatted }}"
                                    data-caption="{{ $captionText }}"
                                    data-composer-url="{{ $composerUrl }}">
                                <i class="ti ti-calendar-time me-1"></i>Quick Schedule
                            </button>
                            <span class="text-white-50 small mt-1" style="font-size: 11px;">1-Click Auto Post</span>
                        </div>
                    </div>

                    {{-- Minimal Card Footer --}}
                    <div class="p-2.5 d-flex align-items-center justify-content-between">
                        <span class="small fw-semibold text-dark text-truncate me-2" title="{{ $template->title }}" id="card-title-{{ $template->id }}">
                            {{ $template->title }}
                        </span>
                        <div class="d-flex align-items-center gap-1.5 flex-shrink-0" id="card-action-{{ $template->id }}">
                            @if($isScheduled)
                                <span class="badge bg-success-lt text-success rounded-pill px-2.5 py-1 small fw-semibold">
                                    <i class="ti ti-check me-1"></i>Scheduled
                                </span>
                            @else
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1 fw-semibold btn-trigger-quick-schedule"
                                        data-template-id="{{ $template->id }}"
                                        data-template-title="{{ $template->title }}"
                                        data-template-image="{{ $template->image_url }}"
                                        data-festival-id="{{ $selectedFestival?->id }}"
                                        data-festival-title="{{ $selectedFestival?->title }}"
                                        data-festival-date="{{ $selectedFestival?->festival_date?->format('Y-m-d') }}"
                                        data-festival-date-formatted="{{ $selectedFestival?->festival_date?->format('d M Y') }}"
                                        data-is-today="{{ $isToday ? '1' : '0' }}"
                                        data-target-time="{{ $targetTime }}"
                                        data-target-time-formatted="{{ $targetTimeFormatted }}"
                                        data-caption="{{ $captionText }}"
                                        data-composer-url="{{ $composerUrl }}"
                                        title="Quick Schedule Poster">
                                    Use
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
        <div class="fw-semibold text-dark mb-1">No templates found for this date</div>
        <div class="text-secondary small">Select another upcoming date to explore designs.</div>
    </div>
@endif
