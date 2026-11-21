@extends('admin.layouts.app')

@section('title', 'Leadership Strips')

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['url' => url('/admin'), 'label' => 'Dashboard'],
        ['url' => route('admin.branding.index'), 'label' => 'Branding Studio'],
        ['label' => 'Leadership Strips'],
    ]" />
@endsection

@push('styles')
<style>
/* Leadership Strips Styling matching Accounts Hub */
.strip-row-hover {
    transition: background-color 0.15s ease-in-out;
}
.strip-row-hover:hover {
    background-color: #f8fafc;
}
.bs-strip-preview-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    overflow-x: auto;
    padding: 10px 4px;
}
.bs-strip-leader-chip {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-width: 72px;
    position: relative;
    flex-shrink: 0;
}
.bs-strip-leader-chip img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid #0d9488;
    object-fit: cover;
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}
.bs-strip-leader-chip .leader-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: #1e293b;
    margin-top: 5px;
    white-space: nowrap;
}
.bs-strip-leader-chip .leader-order {
    font-size: 0.65rem;
    color: #64748b;
}
.bs-builder-leader-row {
    padding: 8px 10px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    margin-bottom: 6px;
    transition: all 0.15s ease;
    cursor: pointer;
}
.bs-builder-leader-row:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}
.bs-builder-leader-row.selected {
    background: #f0fdfa;
    border-color: #0d9488;
}
.bs-order-item {
    padding: 8px 12px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    margin-bottom: 6px;
    transition: all 0.15s ease;
}
.bs-order-badge {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #0d9488;
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Leadership Strips</h2>
                <p class="text-secondary small mb-0">Configure top leadership header banners for your festival and political social media posters.</p>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary" id="btn-create-strip-modal">
                    <i class="ti ti-plus me-1"></i>Create Strip
                </button>
            </div>
        </div>
    </div>

    {{-- Alert Container --}}
    <div id="strip-alert-container"></div>

    @if($userStrips->isEmpty() && $systemStrips->isEmpty())
        {{-- Formal Clean Empty State --}}
        <div class="card p-5 text-center my-3">
            <div class="card-body py-4">
                <div class="avatar avatar-xl rounded-circle bg-primary-lt mx-auto mb-3">
                    <i class="ti ti-layout-navbar fs-1 text-primary"></i>
                </div>
                <h3 class="fw-bold mb-2">No Leadership Strips Configured</h3>
                <p class="text-secondary small mb-4">Create your first custom leadership banner with your preferred party leaders hierarchy.</p>
                <div>
                    <button type="button" class="btn btn-primary px-4" id="btn-create-first-strip">
                        <i class="ti ti-plus me-1"></i>Create Strip
                    </button>
                </div>
            </div>
        </div>
    @else
        {{-- Custom Leadership Strips Card --}}
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="card-title fw-bold m-0 text-body">My Leadership Strips</h3>
                    <span class="badge badge-outline text-secondary fw-semibold">{{ $userStrips->count() }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.branding.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-palette me-1"></i>Branding Studio
                    </a>
                </div>
            </div>

            {{-- Strips List Group --}}
            <div class="list-group list-group-flush">
                @forelse($userStrips as $strip)
                    <div class="list-group-item p-3 p-md-4 strip-row-hover" style="border-bottom: 1px solid #ebedf2;" id="strip-card-{{ $strip->id }}">
                        <div class="row align-items-center g-3">
                            {{-- Strip Details & Leader Avatars --}}
                            <div class="col-12 col-md-5 col-xl-5">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative flex-shrink-0">
                                        <span class="avatar avatar-lg rounded-3 bg-teal-lt text-teal border shadow-xs" style="width: 48px; height: 48px;">
                                            <i class="ti ti-layout-navbar fs-2"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                            <span class="fw-bold fs-3 text-body text-truncate">{{ $strip->name }}</span>
                                            @if($strip->is_default)
                                                <span class="badge badge-outline text-success fw-semibold fs-7 py-0.5 px-2" id="badge-default-{{ $strip->id }}">
                                                    <i class="ti ti-check me-1"></i>Default
                                                </span>
                                            @else
                                                <span class="badge badge-outline text-secondary fw-semibold fs-7 py-0.5 px-2 d-none" id="badge-default-{{ $strip->id }}">
                                                    Default
                                                </span>
                                            @endif
                                            @if($strip->party)
                                                <span class="badge bg-light text-secondary border fs-7 py-0.5 px-2">
                                                    {{ $strip->party->short_code ?: $strip->party->name }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Leaders Mini Stack --}}
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap mt-1">
                                            @forelse($strip->leaders as $leader)
                                                <div class="d-inline-flex align-items-center gap-1 bg-light border rounded-pill px-2 py-0.5" title="{{ $leader->name }} (#{{ $leader->pivot->order ?? $loop->iteration }})">
                                                    <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
                                                    <span class="small fw-semibold text-secondary" style="font-size: 0.75rem;">{{ Str::limit($leader->name, 14) }}</span>
                                                </div>
                                            @empty
                                                <span class="text-secondary small">No leaders configured</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Leaders Count & Ordering --}}
                            <div class="col-6 col-md-3 col-xl-3">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <span class="h3 fw-bold mb-0 text-body">
                                            {{ $strip->leaders->count() }}
                                        </span>
                                        <span class="text-secondary small">
                                            Leaders
                                        </span>
                                    </div>
                                    <div class="text-muted small fs-7 mt-0.5">
                                        <i class="ti ti-arrows-sort me-1"></i>Order #1 to #{{ $strip->leaders->count() }}
                                    </div>
                                </div>
                            </div>

                            {{-- Status Badge --}}
                            <div class="col-6 col-md-2 col-xl-2">
                                <div class="d-flex flex-column align-items-start gap-1">
                                    @if($strip->is_default)
                                        <span class="badge badge-outline text-success d-inline-flex align-items-center py-1 px-2.5">
                                            <span class="status-dot bg-success me-1.5" style="width: 7px; height: 7px;"></span>Active Strip
                                        </span>
                                    @else
                                        <span class="badge badge-outline text-secondary d-inline-flex align-items-center py-1 px-2.5">
                                            <span class="status-dot bg-secondary me-1.5" style="width: 7px; height: 7px;"></span>Ready
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="col-12 col-md-2 col-xl-2 text-md-end">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    @if(!$strip->is_default)
                                        <button type="button" class="btn btn-sm btn-outline-success px-2.5 btn-make-default" data-id="{{ $strip->id }}" title="Set as default strip">
                                            <i class="ti ti-check me-1"></i>Set Default
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-primary px-2.5 btn-edit-strip" data-id="{{ $strip->id }}" data-name="{{ $strip->name }}" title="Edit leaders">
                                        <i class="ti ti-pencil me-1"></i>Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger px-2 btn-delete-strip" data-id="{{ $strip->id }}" title="Delete strip">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted" id="no-user-strips-notice">
                        <div class="mb-2">No custom leadership strips created yet.</div>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="btn-create-first-strip">
                            <i class="ti ti-plus me-1"></i>Create First Strip
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Official Party Strips Showcase --}}
        @if($systemStrips->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="card-title fw-bold m-0 text-body">Party Official Strips</h3>
                        <span class="badge badge-outline text-warning fw-semibold">{{ $systemStrips->count() }} Available</span>
                    </div>
                    <span class="text-secondary small">Pre-curated sequence based on official party leadership</span>
                </div>

                <div class="list-group list-group-flush">
                    @foreach($systemStrips as $sysStrip)
                        <div class="list-group-item p-3 p-md-4 strip-row-hover" style="border-bottom: 1px solid #ebedf2;">
                            <div class="row align-items-center g-3">
                                {{-- Strip Info & Avatars --}}
                                <div class="col-12 col-md-5 col-xl-5">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative flex-shrink-0">
                                            <span class="avatar avatar-lg rounded-3 bg-warning-lt text-warning border shadow-xs" style="width: 48px; height: 48px;">
                                                <i class="ti ti-flag fs-2"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                <span class="fw-bold fs-3 text-body text-truncate">{{ $sysStrip->name }}</span>
                                                <span class="badge bg-secondary-lt fw-semibold fs-7 py-0.5 px-2">Party Official</span>
                                                @if($sysStrip->party)
                                                    <span class="badge bg-light text-secondary border fs-7 py-0.5 px-2">
                                                        {{ $sysStrip->party->short_code ?: $sysStrip->party->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Mini Avatar Stack --}}
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap mt-1">
                                                @foreach($sysStrip->leaders as $leader)
                                                    <div class="d-inline-flex align-items-center gap-1 bg-light border rounded-pill px-2 py-0.5" title="{{ $leader->name }} (#{{ $leader->pivot->order ?? $loop->iteration }})">
                                                        <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
                                                        <span class="small fw-semibold text-secondary" style="font-size: 0.75rem;">{{ Str::limit($leader->name, 14) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Leaders Count --}}
                                <div class="col-6 col-md-3 col-xl-3">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-baseline gap-1">
                                            <span class="h3 fw-bold mb-0 text-body">
                                                {{ $sysStrip->leaders->count() }}
                                            </span>
                                            <span class="text-secondary small">Leaders</span>
                                        </div>
                                        <div class="text-muted small fs-7 mt-0.5">
                                            <i class="ti ti-shield-check me-1"></i>Official Sequence
                                        </div>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="col-6 col-md-2 col-xl-2">
                                    @if($activeStrip?->id === $sysStrip->id && !$userStrips->contains('is_default', true))
                                        <span class="badge badge-outline text-success d-inline-flex align-items-center py-1 px-2.5">
                                            <span class="status-dot bg-success me-1.5" style="width: 7px; height: 7px;"></span>Active Default
                                        </span>
                                    @else
                                        <span class="badge badge-outline text-secondary d-inline-flex align-items-center py-1 px-2.5">
                                            <span class="status-dot bg-secondary me-1.5" style="width: 7px; height: 7px;"></span>Party Standard
                                        </span>
                                    @endif
                                </div>

                                {{-- Action --}}
                                <div class="col-12 col-md-2 col-xl-2 text-md-end">
                                    @if($activeStrip?->id === $sysStrip->id && !$userStrips->contains('is_default', true))
                                        <span class="badge bg-success text-white py-1.5 px-3 rounded-pill">
                                            <i class="ti ti-check me-1"></i>Current Default
                                        </span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary px-3 btn-make-default" data-id="{{ $sysStrip->id }}">
                                            <i class="ti ti-copy me-1"></i>Activate as My Strip
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- ======================================================================
         INTERACTIVE STRIP BUILDER MODAL
         ====================================================================== --}}
    <div class="modal fade" id="stripBuilderModal" tabindex="-1" aria-labelledby="stripBuilderModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                
                {{-- Modal Header --}}
                <div class="modal-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="avatar avatar-md rounded-3 bg-teal-lt text-teal">
                            <i class="ti ti-layout-navbar fs-2"></i>
                        </div>
                        <div>
                            <h4 class="modal-title fw-bold text-dark mb-0" id="stripBuilderModalLabel">
                                Leadership Strip Builder
                            </h4>
                            <p class="text-secondary small mb-0">Select party leaders and arrange their sequence from left to right for poster headers.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4 bg-light-subtle">
                    
                    {{-- Row 1: Strip Name, Party Select & Default Toggle --}}
                    <div class="card border-0 shadow-xs rounded-3 p-3 mb-3 bg-white">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label fw-bold small text-dark mb-1">
                                    <i class="ti ti-tag text-primary me-1"></i>Strip Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="builder-strip-name" class="form-control" placeholder="e.g. My UP Leadership Strip" maxlength="150" required>
                                <input type="hidden" id="builder-strip-id" value="">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark mb-1">
                                    <i class="ti ti-flag text-warning me-1"></i>Political Party <span class="text-danger">*</span>
                                </label>
                                <select id="builder-party-id" class="form-select">
                                    <option value="">Select Party</option>
                                    @foreach($parties as $party)
                                        <option value="{{ $party->id }}" {{ ($currentPartyId == $party->id) ? 'selected' : '' }}>
                                            {{ $party->name }} ({{ $party->short_code ?: 'General' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark mb-1">
                                    <i class="ti ti-star text-warning me-1"></i>Default Status
                                </label>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" id="builder-is-default" checked>
                                    <label class="form-check-label fw-semibold text-dark small" for="builder-is-default">
                                        Set as active default
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Live Poster Header Strip Preview --}}
                    <div class="card border-0 shadow-xs rounded-3 p-3 mb-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-dark">
                                <i class="ti ti-eye text-primary me-1"></i>Live Header Strip Preview
                            </span>
                            <span class="badge bg-teal-lt rounded-pill px-2.5 py-0.5" id="builder-preview-count" style="font-size: 11px;">
                                0 Leaders Selected
                            </span>
                        </div>
                        <div class="bs-strip-preview-bar bg-light rounded-3 py-2.5 px-3" id="builder-preview-bar" style="min-height: 88px;">
                            <div class="text-secondary small text-center w-100 py-3" id="builder-preview-empty">
                                Select leaders from the list below to preview the leadership strip banner.
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: 2-Column Selection & Hierarchy Reordering --}}
                    <div class="row g-3">
                        
                        {{-- Left Column: Available Leaders with Search --}}
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-xs rounded-3 p-3 bg-white h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2.5">
                                    <h5 class="fw-bold text-dark mb-0 fs-4">
                                        <i class="ti ti-users me-1 text-primary"></i>Available Leaders
                                    </h5>
                                    <span class="text-secondary small" id="builder-available-count">0 Leaders</span>
                                </div>

                                {{-- Search Input --}}
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" id="builder-search-input" class="form-control border-start-0 ps-0" placeholder="Search by name or designation...">
                                </div>

                                {{-- Scrollable Leaders Checkbox List --}}
                                <div class="overflow-auto border rounded-3 p-2 bg-light-subtle" id="builder-leaders-list" style="max-height: 340px;">
                                    <div class="text-center py-4 text-muted small">Loading party leaders...</div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Selected Sequence & Reordering --}}
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-xs rounded-3 p-3 bg-white h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2.5">
                                    <h5 class="fw-bold text-dark mb-0 fs-4">
                                        <i class="ti ti-arrows-sort me-1 text-primary"></i>Hierarchy & Sequence
                                    </h5>
                                    <span class="badge bg-primary-lt" id="builder-selected-counter">0 / 12 Max</span>
                                </div>
                                <p class="text-secondary small mb-2.5" style="font-size: 0.78rem;">
                                    Leaders will appear on the poster in this order (Order #1 is leftmost).
                                </p>

                                {{-- Selected Items Orderable List --}}
                                <div class="overflow-auto border rounded-3 p-2 bg-light-subtle" id="builder-selected-list" style="max-height: 340px; min-height: 200px;">
                                    <div class="text-center py-5 text-muted small" id="builder-selected-empty">
                                        <i class="ti ti-arrow-left me-1"></i>Check leaders on the left to add them here.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Modal Alert Box --}}
                    <div id="builder-modal-alert" class="mt-3 d-none"></div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-white py-2.5 px-4 border-top d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" id="btn-save-strip">
                        <i class="ti ti-device-floppy me-1.5 fs-2"></i>Save Header Strip
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    var stripAlertContainer = document.getElementById('strip-alert-container');

    function showStripAlert(message, type) {
        if (!stripAlertContainer) return;
        stripAlertContainer.innerHTML =
            '<div class="alert alert-' + (type || 'success') + ' alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">' +
                '<div class="d-flex align-items-center gap-2">' +
                    '<i class="ti ti-' + (type === 'danger' ? 'alert-triangle' : 'circle-check') + ' fs-2"></i>' +
                    '<span>' + message + '</span>' +
                '</div>' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';
        stripAlertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Set Default Strip via AJAX
    document.addEventListener('click', async function (e) {
        var target = e.target.closest('.btn-make-default');
        if (!target) return;

        var stripId = target.getAttribute('data-id');
        if (!stripId) return;

        target.disabled = true;
        var origHtml = target.innerHTML;
        target.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Setting...';

        try {
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            var response = await fetch('/admin/leader-strips/' + stripId + '/default', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            var data = await response.json();

            if (response.ok && data.success) {
                showStripAlert(data.message || 'Default leadership strip updated.', 'success');
                setTimeout(function () {
                    window.location.reload();
                }, 400);
            } else {
                showStripAlert(data.message || 'Could not update default strip.', 'danger');
                target.disabled = false;
                target.innerHTML = origHtml;
            }
        } catch (err) {
            console.error('Error setting default strip:', err);
            showStripAlert('A network error occurred while updating default strip.', 'danger');
            target.disabled = false;
            target.innerHTML = origHtml;
        }
    });

    // Delete Strip via AJAX
    document.addEventListener('click', async function (e) {
        var target = e.target.closest('.btn-delete-strip');
        if (!target) return;

        var stripId = target.getAttribute('data-id');
        if (!stripId) return;

        if (!confirm('Are you sure you want to delete this leadership strip? This action cannot be undone.')) {
            return;
        }

        target.disabled = true;

        try {
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            var response = await fetch('/admin/leader-strips/' + stripId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            var data = await response.json();

            if (response.ok && data.success) {
                var card = document.getElementById('strip-card-' + stripId);
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    setTimeout(function () {
                        card.remove();
                    }, 300);
                }
                showStripAlert(data.message || 'Header strip deleted successfully.', 'success');
            } else {
                showStripAlert(data.message || 'Could not delete strip.', 'danger');
                target.disabled = false;
            }
        } catch (err) {
            console.error('Error deleting strip:', err);
            showStripAlert('A network error occurred while deleting the strip.', 'danger');
            target.disabled = false;
        }
    });

    // ======================================================================
    // INTERACTIVE STRIP BUILDER JAVASCRIPT LOGIC
    // ======================================================================
    var stripBuilderModalEl = document.getElementById('stripBuilderModal');
    var stripBuilderModal = stripBuilderModalEl ? bootstrap.Modal.getOrCreateInstance(stripBuilderModalEl) : null;

    var stripBuilderState = {
        stripId: null,
        partyId: null,
        availableLeaders: [],
        selectedLeaders: []
    };

    var inputStripName = document.getElementById('builder-strip-name');
    var inputStripId = document.getElementById('builder-strip-id');
    var selectParty = document.getElementById('builder-party-id');
    var checkIsDefault = document.getElementById('builder-is-default');
    var inputSearch = document.getElementById('builder-search-input');
    var elAvailableList = document.getElementById('builder-leaders-list');
    var elSelectedList = document.getElementById('builder-selected-list');
    var elPreviewBar = document.getElementById('builder-preview-bar');
    var elPreviewCount = document.getElementById('builder-preview-count');
    var elAvailableCount = document.getElementById('builder-available-count');
    var elSelectedCounter = document.getElementById('builder-selected-counter');
    var elModalAlert = document.getElementById('builder-modal-alert');
    var btnSaveStrip = document.getElementById('btn-save-strip');

    function showModalAlert(message, type) {
        if (!elModalAlert) return;
        elModalAlert.className = 'alert alert-' + (type || 'danger') + ' rounded-3 p-2.5 small mb-0';
        elModalAlert.innerHTML = '<i class="ti ti-alert-circle me-1"></i>' + message;
        elModalAlert.classList.remove('d-none');
    }

    function hideModalAlert() {
        if (elModalAlert) {
            elModalAlert.classList.add('d-none');
            elModalAlert.innerHTML = '';
        }
    }

    // Load Party Leaders
    async function fetchLeadersForParty(partyId) {
        if (elAvailableList) {
            elAvailableList.innerHTML = '<div class="text-center py-4 text-muted small"><span class="spinner-border spinner-border-sm me-1"></span>Loading leaders...</div>';
        }

        try {
            var url = '/admin/leader-strips/leaders' + (partyId ? '?party_id=' + partyId : '');
            var res = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            var data = await res.json();
            stripBuilderState.availableLeaders = data.leaders || [];
            renderAvailableLeaders(inputSearch ? inputSearch.value : '');
        } catch (err) {
            console.error('Error fetching leaders:', err);
            if (elAvailableList) {
                elAvailableList.innerHTML = '<div class="text-center py-4 text-danger small">Failed to load leaders.</div>';
            }
        }
    }

    // Party Dropdown Change Listener
    if (selectParty) {
        selectParty.addEventListener('change', function () {
            var newPartyId = selectParty.value;
            stripBuilderState.partyId = newPartyId;
            fetchLeadersForParty(newPartyId);
        });
    }

    // Open Builder for Create or Edit
    async function openStripBuilder(stripId) {
        hideModalAlert();

        var initialPartyId = (selectParty ? selectParty.value : '') || '{{ $currentPartyId ?? "" }}';

        stripBuilderState.stripId = stripId || null;
        stripBuilderState.partyId = initialPartyId;
        stripBuilderState.selectedLeaders = [];

        if (inputStripId) inputStripId.value = stripId || '';
        if (inputSearch) inputSearch.value = '';

        var modalTitle = document.getElementById('stripBuilderModalLabel');

        if (stripId) {
            if (modalTitle) modalTitle.textContent = 'Edit Leadership Strip';
        } else {
            if (modalTitle) modalTitle.textContent = 'Create New Leadership Strip';
            if (inputStripName) inputStripName.value = '';
            if (checkIsDefault) checkIsDefault.checked = true;
        }

        if (stripBuilderModal) {
            stripBuilderModal.show();
        }

        try {
            if (stripId) {
                // Fetch existing strip details
                var stripRes = await fetch('/admin/leader-strips/' + stripId, {
                    headers: { 'Accept': 'application/json' }
                });
                var stripData = await stripRes.json();
                if (stripData.success && stripData.strip) {
                    var s = stripData.strip;
                    if (inputStripName) inputStripName.value = s.name;
                    if (checkIsDefault) checkIsDefault.checked = !!s.is_default;
                    if (s.party_id && selectParty) {
                        selectParty.value = s.party_id;
                        stripBuilderState.partyId = s.party_id;
                    }
                    stripBuilderState.selectedLeaders = s.leaders.map(function (ldr) {
                        return {
                            id: ldr.id,
                            name: ldr.name,
                            designation: ldr.designation,
                            state: ldr.state,
                            image_url: ldr.image_url
                        };
                    });
                }
            }

            await fetchLeadersForParty(stripBuilderState.partyId);
            renderSelectedLeaders();
            renderLivePreview();
        } catch (err) {
            console.error('Error initializing strip builder:', err);
            showModalAlert('Failed to load strip details. Please try again.', 'danger');
        }
    }

    // Render Available Leaders List with Search
    function renderAvailableLeaders(filterQuery) {
        if (!elAvailableList) return;

        var leaders = stripBuilderState.availableLeaders;
        var q = (filterQuery || '').toLowerCase().trim();

        if (q) {
            leaders = leaders.filter(function (l) {
                return (l.name && l.name.toLowerCase().includes(q)) ||
                       (l.designation && l.designation.toLowerCase().includes(q)) ||
                       (l.state && l.state.toLowerCase().includes(q));
            });
        }

        if (elAvailableCount) {
            elAvailableCount.textContent = leaders.length + ' Available';
        }

        if (leaders.length === 0) {
            elAvailableList.innerHTML = '<div class="text-center py-4 text-muted small">No leaders found matching search.</div>';
            return;
        }

        var html = '';
        leaders.forEach(function (leader) {
            var isSelected = stripBuilderState.selectedLeaders.some(function (sl) { return sl.id === leader.id; });
            html += '<div class="bs-builder-leader-row ' + (isSelected ? 'selected' : '') + '" data-id="' + leader.id + '">' +
                '<input type="checkbox" class="form-check-input mt-0 leader-select-check" ' + (isSelected ? 'checked' : '') + ' data-id="' + leader.id + '">' +
                '<img src="' + leader.image_url + '" alt="' + leader.name + '" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1;">' +
                '<div class="flex-grow-1 min-w-0">' +
                    '<div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;">' + leader.name + '</div>' +
                    '<div class="text-secondary small text-truncate" style="font-size: 0.72rem;">' + (leader.designation || leader.state || '') + '</div>' +
                '</div>' +
                (leader.state ? '<span class="badge bg-light text-secondary border" style="font-size: 10px;">' + leader.state + '</span>' : '') +
            '</div>';
        });

        elAvailableList.innerHTML = html;
    }

    // Render Selected Leaders Hierarchy
    function renderSelectedLeaders() {
        if (!elSelectedList) return;

        var selected = stripBuilderState.selectedLeaders;

        if (elSelectedCounter) {
            elSelectedCounter.textContent = selected.length + ' / 12 Max';
        }

        if (selected.length === 0) {
            elSelectedList.innerHTML = '<div class="text-center py-5 text-muted small" id="builder-selected-empty">' +
                '<i class="ti ti-arrow-left me-1"></i>Check leaders on the left to add them here.' +
            '</div>';
            return;
        }

        var html = '';
        selected.forEach(function (leader, idx) {
            var isFirst = idx === 0;
            var isLast = idx === selected.length - 1;

            html += '<div class="bs-order-item" data-id="' + leader.id + '" data-index="' + idx + '">' +
                '<div class="bs-order-badge">#' + (idx + 1) + '</div>' +
                '<img src="' + leader.image_url + '" alt="' + leader.name + '" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1;">' +
                '<div class="flex-grow-1 min-w-0">' +
                    '<div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;">' + leader.name + '</div>' +
                '</div>' +
                '<div class="d-flex align-items-center gap-1">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle btn-move-leader" data-index="' + idx + '" data-dir="-1" ' + (isFirst ? 'disabled' : '') + ' title="Move Left / Higher">' +
                        '<i class="ti ti-arrow-up"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle btn-move-leader" data-index="' + idx + '" data-dir="1" ' + (isLast ? 'disabled' : '') + ' title="Move Right / Lower">' +
                        '<i class="ti ti-arrow-down"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-icon btn-ghost-danger rounded-circle btn-remove-leader ms-1" data-id="' + leader.id + '" title="Remove from strip">' +
                        '<i class="ti ti-x"></i>' +
                    '</button>' +
                '</div>' +
            '</div>';
        });

        elSelectedList.innerHTML = html;
    }

    // Render Real-Time Strip Visual Preview
    function renderLivePreview() {
        if (!elPreviewBar) return;

        var selected = stripBuilderState.selectedLeaders;

        if (elPreviewCount) {
            elPreviewCount.textContent = selected.length + ' Leaders Selected';
        }

        if (selected.length === 0) {
            elPreviewBar.innerHTML = '<div class="text-secondary small text-center w-100 py-3" id="builder-preview-empty">' +
                'Select leaders from the list below to preview the leadership strip banner.' +
            '</div>';
            return;
        }

        var html = '';
        selected.forEach(function (leader, idx) {
            html += '<div class="bs-strip-leader-chip">' +
                '<img src="' + leader.image_url + '" alt="' + leader.name + '">' +
                '<span class="leader-name">' + (leader.name.length > 14 ? leader.name.substring(0, 14) + '...' : leader.name) + '</span>' +
                '<span class="leader-order">#' + (idx + 1) + '</span>' +
            '</div>';
        });

        elPreviewBar.innerHTML = html;
    }

    // Toggle Leader Selection
    function toggleLeader(leaderId) {
        var id = parseInt(leaderId, 10);
        var existingIdx = stripBuilderState.selectedLeaders.findIndex(function (l) { return l.id === id; });

        if (existingIdx !== -1) {
            // Deselect
            stripBuilderState.selectedLeaders.splice(existingIdx, 1);
        } else {
            // Select (Max 12)
            if (stripBuilderState.selectedLeaders.length >= 12) {
                showModalAlert('Maximum 12 leaders can be added to a header strip.', 'warning');
                return;
            }
            var leader = stripBuilderState.availableLeaders.find(function (l) { return l.id === id; });
            if (leader) {
                stripBuilderState.selectedLeaders.push({
                    id: leader.id,
                    name: leader.name,
                    designation: leader.designation,
                    state: leader.state,
                    image_url: leader.image_url
                });
            }
        }

        hideModalAlert();
        renderAvailableLeaders(inputSearch ? inputSearch.value : '');
        renderSelectedLeaders();
        renderLivePreview();
    }

    // Search Input Listener
    if (inputSearch) {
        inputSearch.addEventListener('input', function () {
            renderAvailableLeaders(inputSearch.value);
        });
    }

    // Click handler for Available Leaders List
    if (elAvailableList) {
        elAvailableList.addEventListener('click', function (e) {
            var row = e.target.closest('.bs-builder-leader-row');
            if (!row) return;
            var leaderId = row.getAttribute('data-id');
            if (leaderId) {
                toggleLeader(leaderId);
            }
        });
    }

    // Click handler for Move Up, Move Down, and Remove in Selected Leaders List
    if (elSelectedList) {
        elSelectedList.addEventListener('click', function (e) {
            var btnMove = e.target.closest('.btn-move-leader');
            if (btnMove) {
                var idx = parseInt(btnMove.getAttribute('data-index'), 10);
                var dir = parseInt(btnMove.getAttribute('data-dir'), 10);
                var targetIdx = idx + dir;

                if (targetIdx >= 0 && targetIdx < stripBuilderState.selectedLeaders.length) {
                    var temp = stripBuilderState.selectedLeaders[idx];
                    stripBuilderState.selectedLeaders[idx] = stripBuilderState.selectedLeaders[targetIdx];
                    stripBuilderState.selectedLeaders[targetIdx] = temp;

                    renderSelectedLeaders();
                    renderLivePreview();
                }
                return;
            }

            var btnRemove = e.target.closest('.btn-remove-leader');
            if (btnRemove) {
                var leaderId = btnRemove.getAttribute('data-id');
                if (leaderId) {
                    toggleLeader(leaderId);
                }
                return;
            }
        });
    }

    // Trigger Open Builder on Create Strip Buttons
    var btnCreateStrip = document.getElementById('btn-create-strip-modal');
    if (btnCreateStrip) {
        btnCreateStrip.addEventListener('click', function () {
            openStripBuilder(null);
        });
    }

    var btnCreateFirst = document.getElementById('btn-create-first-strip');
    if (btnCreateFirst) {
        btnCreateFirst.addEventListener('click', function () {
            openStripBuilder(null);
        });
    }

    // Trigger Open Builder on Edit Strip Buttons
    document.addEventListener('click', function (e) {
        var btnEdit = e.target.closest('.btn-edit-strip');
        if (btnEdit) {
            var stripId = btnEdit.getAttribute('data-id');
            if (stripId) {
                openStripBuilder(stripId);
            }
        }
    });

    // Save Header Strip Action
    if (btnSaveStrip) {
        btnSaveStrip.addEventListener('click', async function () {
            hideModalAlert();

            var name = (inputStripName ? inputStripName.value : '').trim();
            if (!name) {
                showModalAlert('Please enter a strip name.', 'danger');
                if (inputStripName) inputStripName.focus();
                return;
            }

            var partyId = selectParty ? selectParty.value : stripBuilderState.partyId;
            if (!partyId) {
                showModalAlert('Please select a political party.', 'danger');
                if (selectParty) selectParty.focus();
                return;
            }

            if (stripBuilderState.selectedLeaders.length === 0) {
                showModalAlert('Please select at least 1 leader for the strip.', 'danger');
                return;
            }

            btnSaveStrip.disabled = true;
            var origText = btnSaveStrip.innerHTML;
            btnSaveStrip.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5"></span>Saving Strip...';

            var isDefault = checkIsDefault ? checkIsDefault.checked : true;
            var payload = {
                name: name,
                party_id: partyId,
                is_default: isDefault,
                items: stripBuilderState.selectedLeaders.map(function (leader, idx) {
                    return {
                        leader_id: leader.id,
                        order: idx + 1
                    };
                })
            };

            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            var isEdit = !!stripBuilderState.stripId;
            var url = isEdit ? '/admin/leader-strips/' + stripBuilderState.stripId : '/admin/leader-strips';
            var method = isEdit ? 'PUT' : 'POST';

            try {
                var response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                var result = await response.json();

                if (response.ok && result.success) {
                    if (stripBuilderModal) {
                        stripBuilderModal.hide();
                    }
                    showStripAlert(result.message || 'Header strip saved successfully.', 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 400);
                } else {
                    var errorMsg = result.message || 'Could not save strip.';
                    if (result.errors) {
                        var firstKey = Object.keys(result.errors)[0];
                        if (firstKey) errorMsg = result.errors[firstKey][0];
                    }
                    showModalAlert(errorMsg, 'danger');
                    btnSaveStrip.disabled = false;
                    btnSaveStrip.innerHTML = origText;
                }
            } catch (err) {
                console.error('Error saving strip:', err);
                showModalAlert('A network error occurred while saving the strip.', 'danger');
                btnSaveStrip.disabled = false;
                btnSaveStrip.innerHTML = origText;
            }
        });
    }
});
</script>
@endpush
