@extends('admin.layouts.app')

@section('title', 'Edit Scheduled Post #' . $scheduledPost->id)

@section('breadcrumbs')
    <ol class="breadcrumb m-0" aria-label="breadcrumbs">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-home fs-3"></i></a></li>
        <li class="breadcrumb-item">Content</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.posts.schedule') }}">Schedule</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Post #{{ $scheduledPost->id }}</li>
    </ol>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h2 class="page-title m-0">Edit Scheduled Post #{{ $scheduledPost->id }}</h2>
                    {!! $scheduledPost->status_badge !!}
                </div>
                <div class="text-secondary small">
                    Created on {{ $scheduledPost->created_at->format('d M Y, h:i A') }} by {{ $scheduledPost->user->name ?? 'User' }}
                </div>
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                {{-- Cancel / Delete Post Button --}}
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-delete-post">
                    <i class="ti ti-trash me-1"></i>Delete Post
                </button>
                <a href="{{ route('admin.posts.schedule') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Back to Schedule
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-2 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-circle fs-2 me-2"></i>
                <div>{{ session('warning') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i class="ti ti-alert-circle fs-2 me-2"></i>
                <strong>Please resolve the following issues:</strong>
            </div>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    {{-- Main Edit Card --}}
    <div class="card shadow-xs">
        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
            <h3 class="card-title fw-bold m-0 text-dark">
                <i class="ti ti-edit me-1.5 text-primary"></i>Update Post Details & Media
            </h3>
            <span class="badge badge-outline text-secondary">
                <i class="ti ti-clock me-1"></i>Target Date: {{ $scheduledPost->scheduled_at->format('d M Y, h:i A') }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.posts.schedule.update', $scheduledPost->id) }}" enctype="multipart/form-data" id="form-edit-post">
            @csrf
            @method('PUT')
            <input type="hidden" name="platform" id="selected-platform" value="{{ $platform }}">
            <input type="hidden" name="post_type" id="selected-post-type" value="{{ old('post_type', $scheduledPost->post_type) }}">
            <div id="canva-imported-media-container"></div>

            <div class="card-body p-4">

                {{-- 1. LOCKED PLATFORM & CHANNEL CARD (Readonly Display) --}}
                <div class="p-3 bg-light-subtle rounded border mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm rounded-circle bg-{{ $scheduledPost->account->platform_color }}-lt">
                                <i class="{{ $scheduledPost->account->platform_icon }} fs-2"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark fs-3">{{ $scheduledPost->account->name }}</div>
                                <div class="text-secondary small">{{ $scheduledPost->account->account_type_name }} &bull; {{ $scheduledPost->platform_name }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            {!! $scheduledPost->platform_badge !!}
                            <span class="badge bg-secondary-lt text-secondary" data-bs-toggle="tooltip" title="Platform & channel are locked to prevent API schema mismatches">
                                <i class="ti ti-lock me-1"></i>Platform Locked
                            </span>
                        </div>
                    </div>
                    <div class="text-secondary small fst-italic">
                        <i class="ti ti-info-circle me-1"></i>Platform cannot be changed for an existing scheduled post. You can update caption, media, thumbnail, privacy, and scheduled time.
                    </div>
                </div>

                {{-- 2. DYNAMIC PLATFORM FORM SECTIONS --}}

                @if($platform === \App\Models\Account::TYPE_YOUTUBE)
                    {{-- ================= YOUTUBE FIELDS ================= --}}

                    {{-- Title --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label required fw-bold text-dark m-0">Video Title</label>
                            <span class="text-secondary small" id="yt-title-counter">{{ strlen(old('title', $scheduledPost->title ?? '')) }} / 100</span>
                        </div>
                        <input type="text" name="title" id="youtube-title" class="form-control" placeholder="Video title" maxlength="100" required value="{{ old('title', $scheduledPost->title) }}">
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark m-0">Description</label>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-xs btn-outline-teal d-inline-flex align-items-center gap-1 py-0.5 px-2.5 rounded-2" id="btn-generate-ai-desc-edit" title="Generate SEO-optimized YouTube description from Title using AI">
                                    <i class="ti ti-sparkles text-teal"></i>
                                    <span id="btn-ai-desc-text-edit">Generate Description From AI</span>
                                </button>
                                <span class="text-secondary small" id="yt-desc-counter">{{ strlen(old('description', $scheduledPost->description ?: $scheduledPost->content)) }} / 5000</span>
                            </div>
                        </div>
                        <textarea name="description" id="youtube-description" class="form-control" rows="5" placeholder="Video description (optional)">{{ old('description', $scheduledPost->description ?: $scheduledPost->content) }}</textarea>
                        <small class="form-hint mt-1">
                            <i class="ti ti-bulb me-1 text-teal"></i>Tip: Enter a Video Title above and click <strong>Generate Description From AI</strong> for an SEO-friendly summary and hashtags.
                        </small>
                    </div>

                    {{-- Video File (Current preview + Replacement option) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Video File</label>

                            @if(!empty($scheduledPost->media_urls) && count($scheduledPost->media_urls) > 0)
                                @php
                                    $currentVideo = $scheduledPost->media_urls[0];
                                    $videoUrl = \Illuminate\Support\Facades\Storage::url($currentVideo);
                                @endphp
                                <div class="p-2.5 bg-light rounded border mb-2 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center text-truncate me-2">
                                        <i class="ti ti-movie fs-2 text-danger me-2 flex-shrink-0"></i>
                                        <div class="text-truncate">
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 250px;">{{ basename($currentVideo) }}</div>
                                            <small class="text-success"><i class="ti ti-check me-0.5"></i>Currently Attached</small>
                                        </div>
                                    </div>
                                    @if(\Illuminate\Support\Str::endsWith(strtolower($currentVideo), ['.mp4', '.mov', '.webm']))
                                        <a href="{{ $videoUrl }}" target="_blank" class="btn btn-sm btn-ghost-primary flex-shrink-0">
                                            <i class="ti ti-player-play me-1"></i>Preview
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <input type="file" name="video_file" id="youtube-video-file" class="form-control" accept="video/mp4,video/quicktime,video/webm">
                            <div class="form-text small text-secondary">
                                <i class="ti ti-help-circle me-1"></i>Upload a new video only if you wish to replace the current one.
                            </div>
                        </div>

                        {{-- Thumbnail (Current preview + Replacement option) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1.5">Custom Thumbnail</label>

                            @if($scheduledPost->thumbnail_path)
                                @php
                                    $thumbUrl = \Illuminate\Support\Facades\Storage::url($scheduledPost->thumbnail_path);
                                @endphp
                                <div class="p-2.5 bg-light rounded border mb-2 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $thumbUrl }}" class="rounded me-2 border shadow-xs" style="width: 56px; height: 36px; object-fit: cover;" alt="Thumbnail">
                                        <div>
                                            <div class="fw-semibold text-dark">{{ basename($scheduledPost->thumbnail_path) }}</div>
                                            <small class="text-success"><i class="ti ti-check me-0.5"></i>Custom Thumbnail Set</small>
                                        </div>
                                    </div>
                                    <a href="{{ $thumbUrl }}" target="_blank" class="btn btn-sm btn-ghost-primary">
                                        <i class="ti ti-eye me-1"></i>View
                                    </a>
                                </div>
                            @endif

                            <div class="d-flex align-items-center gap-2">
                                <input type="file" name="thumbnail" id="youtube-thumbnail" class="form-control" accept="image/jpeg,image/png,image/webp">
                                <button type="button" class="btn btn-canva-brand flex-shrink-0 d-inline-flex align-items-center gap-1.5 px-3 py-2" data-bs-toggle="modal" data-bs-target="#modal-canva-thumbnail" title="Design YouTube Thumbnail on Canva">
                                    <i class="ti ti-brand-canva"></i>
                                    <span>Design on Canva</span>
                                </button>
                            </div>
                            <div class="text-secondary small mt-1.5" style="font-size: 12px;">1280 &times; 720 px recommended &bull; Upload a new image or design to replace current</div>
                            <div id="youtube-thumbnail-preview-box" class="mt-2.5 d-none">
                                <div class="position-relative d-inline-block">
                                    <img id="youtube-thumbnail-preview" src="" class="rounded border shadow-xs" style="max-height: 90px; width: auto; object-fit: cover;" alt="Thumbnail preview">
                                    <button type="button" class="btn btn-sm btn-icon btn-dark bg-opacity-75 position-absolute top-0 end-0 m-1.5 rounded-circle border-0 text-white" id="btn-remove-youtube-thumbnail" title="Remove thumbnail" style="width: 24px; height: 24px; line-height: 1;">
                                        <i class="ti ti-x" style="font-size: 11px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tags & Privacy Status --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Tags (comma-separated)</label>
                            @php
                                $tagsValue = old('tags', is_array($scheduledPost->tags) ? implode(', ', $scheduledPost->tags) : '');
                            @endphp
                            <input type="text" name="tags" id="youtube-tags" class="form-control" placeholder="politics, development, ward update" value="{{ $tagsValue }}">
                            <div class="form-text small text-secondary">Separate keywords with commas.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required fw-bold text-dark">Privacy Status</label>
                            <select name="privacy_status" class="form-select" id="youtube-privacy-status" required>
                                <option value="public" {{ old('privacy_status', $scheduledPost->privacy_status) == 'public' ? 'selected' : '' }}>Public (Visible to everyone)</option>
                                <option value="unlisted" {{ old('privacy_status', $scheduledPost->privacy_status) == 'unlisted' ? 'selected' : '' }}>Unlisted (Anyone with link can watch)</option>
                                <option value="private" {{ old('privacy_status', $scheduledPost->privacy_status) == 'private' ? 'selected' : '' }}>Private (Only you can watch)</option>
                            </select>
                        </div>
                    </div>

                @else
                    {{-- ================= META (FACEBOOK / INSTAGRAM) FIELDS ================= --}}

                    {{-- Post Types Pills --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">Post Format / Type</label>
                        <div class="d-flex flex-wrap gap-2" id="post-type-pills-container">
                            @if($platform === \App\Models\Account::TYPE_FACEBOOK)
                                <span class="post-type-pill {{ old('post_type', $scheduledPost->post_type) == 'text' ? 'active' : '' }}" data-type="text"><i class="ti ti-typography"></i> Text</span>
                            @endif
                            <span class="post-type-pill {{ old('post_type', $scheduledPost->post_type) == 'photo' ? 'active' : '' }}" data-type="photo"><i class="ti ti-photo"></i> Photo</span>
                            <span class="post-type-pill {{ old('post_type', $scheduledPost->post_type) == 'carousel' ? 'active' : '' }}" data-type="carousel"><i class="ti ti-photo-circle"></i> Carousel</span>
                            <span class="post-type-pill {{ old('post_type', $scheduledPost->post_type) == 'video' ? 'active' : '' }}" data-type="video"><i class="ti ti-video"></i> Video</span>
                            @if($platform === \App\Models\Account::TYPE_INSTAGRAM)
                                <span class="post-type-pill {{ old('post_type', $scheduledPost->post_type) == 'reel' ? 'active' : '' }}" data-type="reel"><i class="ti ti-player-play"></i> Reel</span>
                            @endif
                        </div>
                    </div>

                    {{-- Caption / Content --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                            <label class="form-label fw-bold text-dark m-0">Caption</label>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1.5 py-1 px-2.5 rounded-2 shadow-xs" id="btn-toggle-ai-caption" data-bs-toggle="modal" data-bs-target="#modal-ai-caption" title="Generate AI Captions & Viral Hashtags">
                                    <i class="ti ti-sparkles text-primary"></i>
                                    <span class="fw-semibold">AI Caption & Hashtags</span>
                                </button>
                                <span class="text-secondary small" id="caption-counter">{{ strlen(old('content', $scheduledPost->content ?? '')) }} / {{ $platform === 2 ? '2200' : '5000' }}</span>
                            </div>
                        </div>

                        <textarea name="content" id="composer-content" class="form-control" rows="5" placeholder="Write caption or message...">{{ old('content', $scheduledPost->content) }}</textarea>
                    </div>

                    {{-- Current Media Gallery Preview + New Media Upload --}}
                    <div class="mb-4" id="meta-media-container">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                <i class="ti ti-photo text-primary"></i> Media Attachment
                            </label>
                        </div>

                        @if(!empty($scheduledPost->media_urls) && count($scheduledPost->media_urls) > 0)
                            <div class="p-3 bg-light rounded border mb-3">
                                <div class="text-secondary small fw-semibold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                                    Currently Attached Media ({{ count($scheduledPost->media_urls) }})
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($scheduledPost->media_urls as $idx => $mediaPath)
                                        @php
                                            $mediaUrl = \Illuminate\Support\Facades\Storage::url($mediaPath);
                                            $isVid = \Illuminate\Support\Str::endsWith(strtolower($mediaPath), ['.mp4', '.mov', '.webm']);
                                        @endphp
                                        <div class="position-relative border rounded overflow-hidden shadow-xs" style="width: 88px; height: 88px; background: #000;">
                                            @if($isVid)
                                                <video src="{{ $mediaUrl }}" class="w-100 h-100" style="object-fit: cover;"></video>
                                                <span class="position-absolute top-50 start-50 translate-middle badge bg-dark text-white p-1 rounded-circle">
                                                    <i class="ti ti-player-play fs-4"></i>
                                                </span>
                                            @else
                                                <img src="{{ $mediaUrl }}" class="w-100 h-100" style="object-fit: cover;" alt="Attached media">
                                            @endif
                                            <a href="{{ $mediaUrl }}" target="_blank" class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white text-center py-0.5 small text-decoration-none" style="font-size: 0.65rem;">
                                                <i class="ti ti-eye"></i> View #{{ $idx + 1 }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center flex-wrap mb-2" style="gap: 12px;">
                            <button type="button" class="btn btn-outline-secondary rounded-2 px-3.5 py-2 fw-medium d-inline-flex align-items-center gap-2 shadow-xs" onclick="$('#composer-media-files').click()">
                                <i class="ti ti-device-laptop fs-2 text-secondary"></i>
                                <span>Upload From Device</span>
                            </button>
                            <button type="button" class="btn btn-canva-brand rounded-2 px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2 ms-1" data-bs-toggle="modal" data-bs-target="#modal-canva-studio">
                                <i class="ti ti-brand-canva fs-2 text-white"></i>
                                <span class="text-white">Design on Canva</span>
                            </button>
                            <span id="composer-edit-selected-filename" class="small text-secondary ms-1"></span>
                        </div>
                        <input type="file" name="media_files[]" class="d-none" id="composer-media-files" accept="image/*,video/*" multiple onchange="let n = this.files.length; $('#composer-edit-selected-filename').text(n > 0 ? (n + ' file(s) selected') : '');">
                        <div class="form-text small text-secondary">
                            <i class="ti ti-help-circle me-1"></i>Select new media file(s) only if you want to replace current attachments. Leave blank to keep existing files.
                        </div>
                    </div>

                    {{-- First Comment Option (Instagram & Facebook Auto-Drop) --}}
                    <div id="first-comment-container" class="card card-sm border bg-light-subtle p-3 mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <label class="form-check form-switch m-0 d-flex align-items-center cursor-pointer">
                                <input class="form-check-input me-2" type="checkbox" id="check-enable-first-comment" {{ old('first_comment', $scheduledPost->first_comment) ? 'checked' : '' }}>
                                <span class="form-check-label fw-bold text-dark d-flex align-items-center gap-1.5">
                                    <i class="ti ti-message-circle text-primary fs-2"></i> Add to First Comment
                                </span>
                            </label>
                            <span class="text-secondary small" style="font-size: 11.5px;">Auto-drop hashtags or links in 1st comment</span>
                        </div>

                        {{-- First Comment Input Box (expands when switch is checked) --}}
                        <div id="first-comment-input-box" class="mt-3 ps-4 {{ old('first_comment', $scheduledPost->first_comment) ? '' : 'd-none' }}">
                            <div class="position-relative">
                                <textarea name="first_comment" id="composer-first-comment" class="form-control" rows="2" placeholder="Write first comment (e.g. #hashtags, website link, or extra details)...">{{ old('first_comment', $scheduledPost->first_comment) }}</textarea>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-1.5">
                                <small class="text-secondary" style="font-size: 11px;">Automatically published as the very first comment once live.</small>
                                <button type="button" class="btn btn-link btn-xs text-primary text-decoration-none p-0 fw-medium" id="btn-move-hashtags-to-comment" title="Cut hashtags from caption and paste into first comment">
                                    <i class="ti ti-arrows-transfer-down me-0.5"></i>Move Hashtags from Caption
                                </button>
                            </div>
                        </div>
                    </div>

                @endif

                {{-- 3. PUBLISHING MODE & SCHEDULE TIME --}}
                <div class="card p-3 bg-light-subtle mb-0 border">
                    <label class="form-label fw-bold text-dark mb-2">Publishing Mode & Timing</label>
                    <div class="d-flex align-items-center gap-4 mb-3 flex-wrap">
                        <label class="form-check m-0">
                            <input class="form-check-input" type="radio" name="publish_mode" value="schedule"
                                   {{ old('publish_mode', $scheduledPost->status === \App\Models\ScheduledPost::STATUS_DRAFT ? 'draft' : 'schedule') == 'schedule' ? 'checked' : '' }} id="radio-mode-schedule">
                            <span class="form-check-label fw-medium"><i class="ti ti-clock me-1 text-primary"></i>Schedule for Later</span>
                        </label>
                        <label class="form-check m-0">
                            <input class="form-check-input" type="radio" name="publish_mode" value="now"
                                   {{ old('publish_mode') == 'now' ? 'checked' : '' }} id="radio-mode-now">
                            <span class="form-check-label fw-medium"><i class="ti ti-send me-1 text-success"></i>Publish Immediately Now</span>
                        </label>
                        <label class="form-check m-0">
                            <input class="form-check-input" type="radio" name="publish_mode" value="draft"
                                   {{ old('publish_mode', $scheduledPost->status === \App\Models\ScheduledPost::STATUS_DRAFT ? 'draft' : 'schedule') == 'draft' ? 'checked' : '' }} id="radio-mode-draft">
                            <span class="form-check-label fw-medium"><i class="ti ti-file-pencil me-1 text-secondary"></i>Save as Draft</span>
                        </label>
                    </div>

                    <div id="schedule-datetime-container">
                        <label class="form-label fw-bold text-dark">Scheduled Date & Time</label>
                        @php
                            $dateVal = old('scheduled_at', $scheduledPost->scheduled_at ? $scheduledPost->scheduled_at->format('Y-m-d\TH:i') : now()->addHours(2)->format('Y-m-d\TH:i'));
                        @endphp
                        <input type="datetime-local" name="scheduled_at" id="composer-scheduled-at" class="form-control" style="max-width: 320px;"
                               value="{{ $dateVal }}">
                    </div>
                </div>

            </div>

            {{-- Card Footer --}}
            <div class="card-footer d-flex justify-content-between align-items-center py-3 px-4">
                <a href="{{ route('admin.posts.schedule') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-x me-1"></i>Cancel
                </a>

                <button type="submit" class="btn btn-primary px-4" id="btn-submit-post">
                    <i class="ti ti-check me-1.5"></i>Update Scheduled Post
                </button>
            </div>
        </form>
    </div>

    {{-- MODAL: Delete Post Confirmation --}}
    <div class="modal modal-blur fade" id="modal-delete-post" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="ti ti-alert-triangle fs-1 text-danger mb-2"></i>
                    <h3>Are you sure?</h3>
                    <div class="text-secondary">
                        Do you really want to cancel and delete this scheduled post? This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div class="col">
                                <form method="POST" action="{{ route('admin.posts.schedule.destroy', $scheduledPost->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">Delete Post</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- AI Caption & Hashtag Assistant Modal --}}
    @include('admin.posts.partials._ai_caption_studio')

    {{-- Canva Design Studio Modal --}}
    @include('admin.posts.partials._canva_studio_modal')

@endsection

@push('styles')
<style>
    .post-type-pill {
        cursor: pointer;
        border: 1px solid rgba(4, 32, 69, 0.12);
        padding: 0.45rem 0.85rem;
        border-radius: 6px;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8125rem;
        font-weight: 500;
        background: #ffffff;
        color: #334155;
    }
    .post-type-pill:hover {
        background: #f8fafc;
    }
    .post-type-pill.active {
        background: #1db8ab;
        color: #ffffff;
        border-color: #1db8ab;
    }
    [data-bs-theme="dark"] .post-type-pill {
        background: #111a2d !important;
        color: #cbd5e1 !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }
    [data-bs-theme="dark"] .post-type-pill:hover {
        background: #162035 !important;
        color: #f8fafc !important;
    }
    .media-source-btn {
        background: #ffffff;
        border: 1px solid #d1d5db;
        color: #334155;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.15s ease;
    }
    .media-source-btn:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }
    [data-bs-theme="dark"] .media-source-btn {
        background: #111a2d !important;
        border-color: rgba(255, 255, 255, 0.15) !important;
        color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .media-source-btn:hover {
        background: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
        color: #f8fafc !important;
    }
    .btn-canva-brand {
        background: linear-gradient(135deg, #00c4cc 0%, #7d2ae8 100%) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 2px 8px rgba(125, 42, 232, 0.25) !important;
        transition: all 0.2s ease !important;
    }
    .btn-canva-brand:hover {
        background: linear-gradient(135deg, #00b3ba 0%, #6e24cf 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(125, 42, 232, 0.35) !important;
        transform: translateY(-1px);
    }
    .btn-canva-brand:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(125, 42, 232, 0.2) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Post type pills click handler
    $('.post-type-pill').on('click', function () {
        $('.post-type-pill').removeClass('active');
        $(this).addClass('active');
        $('#selected-post-type').val($(this).data('type'));
    });

    // Publish Mode Handler (Schedule, Now, Draft)
    $('input[name="publish_mode"]').on('change', function () {
        let mode = $(this).val();
        if (mode === 'schedule') {
            $('#schedule-datetime-container').show();
            $('#composer-scheduled-at').prop('required', true);
            $('#btn-submit-post').html('<i class="ti ti-calendar-check me-1.5"></i>Update Scheduled Post');
        } else if (mode === 'now') {
            $('#schedule-datetime-container').hide();
            $('#composer-scheduled-at').prop('required', false);
            $('#btn-submit-post').html('<i class="ti ti-send me-1.5"></i>Update & Publish Now');
        } else {
            $('#schedule-datetime-container').hide();
            $('#composer-scheduled-at').prop('required', false);
            $('#btn-submit-post').html('<i class="ti ti-file-pencil me-1.5"></i>Update as Draft');
        }
    });

    // Character counters
    $('#composer-content').on('input', function () {
        let max = {{ $platform === 2 ? 2200 : 5000 }};
        $('#caption-counter').text($(this).val().length + ' / ' + max);
    });

    // First Comment toggle handler
    $('#check-enable-first-comment').on('change', function () {
        if ($(this).is(':checked')) {
            $('#first-comment-input-box').removeClass('d-none');
            $('#composer-first-comment').focus();
        } else {
            $('#first-comment-input-box').addClass('d-none');
        }
    });

    // Move Hashtags from Caption to First Comment
    $('#btn-move-hashtags-to-comment').on('click', function (e) {
        e.preventDefault();
        let caption = $('#composer-content').val() || '';
        let hashtagRegex = /(#[a-zA-Z0-9_\u0900-\u097F]+)/g;
        let matches = caption.match(hashtagRegex);

        if (!matches || matches.length === 0) {
            alert('No hashtags found in caption to move.');
            return;
        }

        // Remove hashtags from caption
        let cleanCaption = caption.replace(hashtagRegex, '').replace(/[ \t]+/g, ' ').replace(/\n\s*\n/g, '\n').trim();
        $('#composer-content').val(cleanCaption).trigger('input');

        // Append to first comment
        let existingComment = $('#composer-first-comment').val().trim();
        let newTags = matches.join(' ');
        let updatedComment = existingComment ? existingComment + '\n' + newTags : newTags;
        $('#composer-first-comment').val(updatedComment);

        // Ensure switch is on and visible
        $('#check-enable-first-comment').prop('checked', true);
        $('#first-comment-input-box').removeClass('d-none');
    });

    $('#youtube-title').on('input', function () {
        $('#yt-title-counter').text($(this).val().length + ' / 100');
    });

    $('#youtube-description').on('input', function () {
        $('#yt-desc-counter').text($(this).val().length + ' / 5000');
    });

    // AI Description Generator for Edit Post Form
    $('#btn-generate-ai-desc-edit').on('click', function () {
        let title = $.trim($('#youtube-title').val());
        if (!title) {
            alert('Please enter a Video Title first so AI can read it and generate an SEO-friendly description.');
            $('#youtube-title').focus();
            return;
        }

        let $btn = $(this);
        let $btnText = $('#btn-ai-desc-text-edit');
        let origText = $btnText.text();

        $btn.prop('disabled', true);
        $btnText.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Generating...');

        $.ajax({
            url: "{{ route('admin.posts.generate-ai-description') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                title: title
            },
            success: function (res) {
                if (res.success && res.description) {
                    $('#youtube-description').val(res.description).trigger('input');
                } else {
                    alert(res.message || 'Could not generate description. Please try again.');
                }
            },
            error: function (xhr) {
                let msg = 'Failed to generate AI description. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            },
            complete: function () {
                $btn.prop('disabled', false);
                $btnText.text(origText);
            }
        });
    });

    $('input[name="publish_mode"]:checked').trigger('change');
});
</script>
@include('admin.posts.partials._ai_caption_script')
@include('admin.posts.partials._canva_studio_script')
@endpush

