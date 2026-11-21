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
/* Clean Minimal Single-Layer Strip Styling */
.clean-strip-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 14px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.clean-strip-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
}
.clean-strip-card.active-strip {
    border: 1.5px solid #0d9488 !important;
}

/* Horizontal Leaders Rail */
.leaders-strip-rail {
    display: flex;
    align-items: center;
    gap: 16px;
    overflow-x: auto;
    padding: 6px 0 2px 0;
}
.leaders-strip-rail::-webkit-scrollbar {
    height: 5px;
}
.leaders-strip-rail::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 6px;
}
.leader-chip-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-width: 76px;
    flex-shrink: 0;
}
.leader-avatar-box {
    position: relative;
    width: 48px;
    height: 48px;
    margin-bottom: 6px;
}
.leader-avatar-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #0d9488;
    background: #f8fafc;
}
.leader-order-num {
    position: absolute;
    top: -3px;
    right: -3px;
    background: #0d9488;
    color: #ffffff;
    font-size: 9px;
    font-weight: 700;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #ffffff;
}
.leader-name-text {
    font-size: 0.78rem;
    font-weight: 600;
    color: #1e293b;
    max-width: 86px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1.25;
}
.leader-role-text {
    font-size: 0.68rem;
    color: #64748b;
    max-width: 86px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-top: 2px;
}
.leader-sep-arrow {
    color: #cbd5e1;
    font-size: 0.85rem;
    flex-shrink: 0;
    opacity: 0.5;
}

/* Compact Strip Builder Specifics */
.bs-builder-leader-row {
    padding: 7px 10px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    margin-bottom: 5px;
    transition: all 0.15s ease;
    cursor: pointer;
    user-select: none;
}
.bs-builder-leader-row:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}
.bs-builder-leader-row.selected {
    background: #f0fdfa;
    border-color: #0d9488;
}
.bs-builder-order-badge {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #0d9488;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bs-builder-unselected-circle {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1.5px dashed #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bs-mini-chip {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #0d9488;
    border-radius: 20px;
    padding: 3px 8px 3px 4px;
    flex-shrink: 0;
}
.bs-mini-chip img {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
}
.bs-mini-chip .chip-name {
    font-size: 11px;
    font-weight: 600;
    color: #1e293b;
    max-width: 80px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.bs-mini-chip .chip-order {
    font-size: 10px;
    font-weight: 700;
    color: #0d9488;
}
.bs-mini-chip .chip-remove {
    cursor: pointer;
    color: #94a3b8;
    font-size: 13px;
    line-height: 1;
    transition: color 0.15s ease;
}
.bs-mini-chip .chip-remove:hover {
    color: #ef4444;
}
</style>
@endpush

@section('content')

    {{-- Page Title (Clean & Minimal, No Extra Buttons) --}}
    <div class="mb-4">
        <h2 class="fw-bold text-dark fs-2 mb-0">Leadership Strips</h2>
    </div>

    {{-- Alert Container --}}
    <div id="strip-alert-container"></div>

    {{-- ======================================================================
         SECTION 1: OFFICIAL STRIP
         ====================================================================== --}}
    @if($systemStrips->isNotEmpty())
        <div class="mb-4">
            <div class="mb-2">
                <h3 class="fw-bold text-dark fs-3 mb-0">Official Strip</h3>
            </div>

            @foreach($systemStrips as $sysStrip)
                <div class="clean-strip-card {{ ($activeStrip?->id === $sysStrip->id && !$userStrips->contains('is_default', true)) ? 'active-strip' : '' }}" id="strip-card-{{ $sysStrip->id }}">
                    
                    {{-- Header Line: Title, Badges & Action --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark fs-4">{{ $sysStrip->name }}</span>
                            @if($sysStrip->party)
                                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 11px;">
                                    {{ $sysStrip->party->short_code ?: $sysStrip->party->name }}
                                </span>
                            @endif
                            @if($activeStrip?->id === $sysStrip->id && !$userStrips->contains('is_default', true))
                                <span class="badge bg-teal-lt text-teal px-2 py-0.5 fw-semibold" style="font-size: 11px;">
                                    <i class="ti ti-check me-1"></i>Active Default
                                </span>
                            @endif
                        </div>

                        <div>
                            @if($activeStrip?->id === $sysStrip->id && !$userStrips->contains('is_default', true))
                                <span class="badge bg-success text-white py-1.5 px-3 rounded-pill" style="font-size: 12px;">
                                    <i class="ti ti-check me-1"></i>Current Active
                                </span>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-make-default" data-id="{{ $sysStrip->id }}" style="font-size: 12px;">
                                    <i class="ti ti-copy me-1"></i>Activate as My Strip
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Horizontal Leaders Rail (No Nested Box!) --}}
                    <div class="leaders-strip-rail">
                        @forelse($sysStrip->leaders as $leader)
                            <div class="leader-chip-item">
                                <div class="leader-avatar-box">
                                    <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}" class="leader-avatar-img">
                                    <span class="leader-order-num">#{{ $leader->pivot->order ?? $loop->iteration }}</span>
                                </div>
                                <span class="leader-name-text" title="{{ $leader->name }}">{{ Str::limit($leader->name, 14) }}</span>
                                @if($leader->designation)
                                    <span class="leader-role-text" title="{{ $leader->designation }}">{{ Str::limit($leader->designation, 15) }}</span>
                                @endif
                            </div>
                            @if(!$loop->last)
                                <i class="ti ti-chevron-right leader-sep-arrow"></i>
                            @endif
                        @empty
                            <span class="text-secondary small">No leaders configured.</span>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ======================================================================
         SECTION 2: PERSONAL STRIP (WITH CREATE STRIP BUTTON IN FRONT)
         ====================================================================== --}}
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h3 class="fw-bold text-dark fs-3 mb-0">Personal Strip</h3>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs" id="btn-create-strip-modal">
                <i class="ti ti-plus me-1"></i>Create Strip
            </button>
        </div>

        @if($userStrips->isNotEmpty())
            <div id="user-strips-list">
                @foreach($userStrips as $strip)
                    <div class="clean-strip-card {{ $strip->is_default ? 'active-strip' : '' }}" id="strip-card-{{ $strip->id }}">
                        
                        {{-- Header Line: Title, Badges & Actions --}}
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark fs-4">{{ $strip->name }}</span>
                                @if($strip->party)
                                    <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 11px;">
                                        {{ $strip->party->short_code ?: $strip->party->name }}
                                    </span>
                                @endif
                                @if($strip->is_default)
                                    <span class="badge bg-teal-lt text-teal px-2 py-0.5 fw-semibold" style="font-size: 11px;">
                                        <i class="ti ti-check me-1"></i>Active Default
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-1.5">
                                @if(!$strip->is_default)
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-0.5 btn-make-default" data-id="{{ $strip->id }}" style="font-size: 11px;">
                                        <i class="ti ti-check me-1"></i>Set Default
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5 btn-edit-strip" data-id="{{ $strip->id }}" data-name="{{ $strip->name }}" style="font-size: 11px;">
                                    <i class="ti ti-pencil me-1"></i>Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0.5 btn-delete-strip" data-id="{{ $strip->id }}" title="Delete strip" style="font-size: 11px;">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Horizontal Leaders Rail (No Nested Box!) --}}
                        <div class="leaders-strip-rail">
                            @forelse($strip->leaders as $leader)
                                <div class="leader-chip-item">
                                    <div class="leader-avatar-box">
                                        <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}" class="leader-avatar-img">
                                        <span class="leader-order-num">#{{ $leader->pivot->order ?? $loop->iteration }}</span>
                                    </div>
                                    <span class="leader-name-text" title="{{ $leader->name }}">{{ Str::limit($leader->name, 14) }}</span>
                                    @if($leader->designation)
                                        <span class="leader-role-text" title="{{ $leader->designation }}">{{ Str::limit($leader->designation, 15) }}</span>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                    <i class="ti ti-chevron-right leader-sep-arrow"></i>
                                @endif
                            @empty
                                <span class="text-secondary small">No leaders configured.</span>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Simple Clean Notice If No Personal Strip --}}
            <div class="clean-strip-card text-center py-4" id="no-user-strips-notice">
                <p class="text-secondary small mb-2">No personal strip created yet.</p>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btn-create-first-strip">
                    <i class="ti ti-plus me-1"></i>Create Personal Strip
                </button>
            </div>
        @endif
    </div>

    {{-- ======================================================================
         INTERACTIVE STRIP BUILDER MODAL (COMPACT & SIMPLE)
         ====================================================================== --}}
    <div class="modal fade" id="stripBuilderModal" tabindex="-1" aria-labelledby="stripBuilderModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                
                {{-- Modal Header --}}
                <div class="modal-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="stripBuilderModalLabel">
                        Create Strip
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-3 p-md-4">
                    
                    {{-- Form Fields: Name, Party, Default Status --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark mb-1" for="builder-strip-name">
                            Strip Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="builder-strip-name" class="form-control rounded-2" placeholder="e.g. My Campaign Strip" maxlength="150" required>
                        <input type="hidden" id="builder-strip-id" value="">
                    </div>

                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-7">
                            <label class="form-label fw-semibold small text-dark mb-1" for="builder-party-id">
                                Political Party <span class="text-danger">*</span>
                            </label>
                            <select id="builder-party-id" class="form-select rounded-2">
                                <option value="">Select Party</option>
                                @foreach($parties as $party)
                                    <option value="{{ $party->id }}" {{ ($currentPartyId == $party->id) ? 'selected' : '' }}>
                                        {{ $party->name }} ({{ $party->short_code ?: 'General' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5 pt-3">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="builder-is-default" checked>
                                <label class="form-check-label small fw-medium text-dark" for="builder-is-default">
                                    Set as default
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Selected Preview Strip (Compact Horizontal Rail) --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="small fw-semibold text-dark">Selected Sequence</span>
                            <span class="badge bg-teal-lt rounded-pill px-2 py-0.5" id="builder-preview-count" style="font-size: 11px;">
                                0 / 6 Selected
                            </span>
                        </div>
                        <div class="border rounded-3 p-2 bg-light-subtle d-flex align-items-center gap-1.5 overflow-x-auto" id="builder-preview-bar" style="min-height: 48px;">
                            <div class="text-secondary small text-center w-100 py-1" id="builder-preview-empty">
                                Click leaders below to add them to your strip.
                            </div>
                        </div>
                    </div>

                    {{-- Search Input --}}
                    <div class="mb-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" id="builder-search-input" class="form-control border-start-0 ps-0" placeholder="Search leader by name or designation...">
                        </div>
                    </div>

                    {{-- Single Scrollable Leaders List (Click to select/unselect in order) --}}
                    <div class="overflow-auto border rounded-3 p-1.5 bg-white" id="builder-leaders-list" style="max-height: 250px;">
                        <div class="text-center py-4 text-muted small">Loading party leaders...</div>
                    </div>

                    {{-- Modal Alert Box --}}
                    <div id="builder-modal-alert" class="mt-3 d-none"></div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-white py-2.5 px-4 border-top d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold" id="btn-save-strip">
                        <i class="ti ti-device-floppy me-1"></i>Save Strip
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
            '<div class="alert alert-' + (type || 'success') + ' alert-dismissible fade show rounded-3 shadow-xs mb-3" role="alert">' +
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
    // INTERACTIVE STRIP BUILDER JAVASCRIPT LOGIC (SIMPLE & COMPACT)
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
    var elPreviewBar = document.getElementById('builder-preview-bar');
    var elPreviewCount = document.getElementById('builder-preview-count');
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
            if (modalTitle) modalTitle.textContent = 'Edit Strip';
        } else {
            if (modalTitle) modalTitle.textContent = 'Create Strip';
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
            renderPreviewBar();
        } catch (err) {
            console.error('Error initializing strip builder:', err);
            showModalAlert('Failed to load strip details. Please try again.', 'danger');
        }
    }

    // Render Available Leaders List
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

        if (leaders.length === 0) {
            elAvailableList.innerHTML = '<div class="text-center py-4 text-muted small">No leaders found matching search.</div>';
            return;
        }

        var html = '';
        leaders.forEach(function (leader) {
            var selectedIdx = stripBuilderState.selectedLeaders.findIndex(function (sl) { return sl.id === leader.id; });
            var isSelected = (selectedIdx !== -1);

            html += '<div class="bs-builder-leader-row ' + (isSelected ? 'selected' : '') + '" data-id="' + leader.id + '">' +
                (isSelected
                    ? '<div class="bs-builder-order-badge">#' + (selectedIdx + 1) + '</div>'
                    : '<div class="bs-builder-unselected-circle"><i class="ti ti-plus text-muted" style="font-size: 11px;"></i></div>') +
                '<img src="' + leader.image_url + '" alt="' + leader.name + '" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 1.5px solid ' + (isSelected ? '#0d9488' : '#e2e8f0') + ';">' +
                '<div class="flex-grow-1 min-w-0">' +
                    '<div class="fw-bold text-dark text-truncate" style="font-size: 0.84rem;">' + leader.name + '</div>' +
                    '<div class="text-secondary text-truncate" style="font-size: 0.72rem;">' + (leader.designation || leader.state || '') + '</div>' +
                '</div>' +
                (isSelected
                    ? '<span class="badge bg-teal-lt text-teal px-2 py-0.5 rounded-pill" style="font-size: 10px;">Selected</span>'
                    : '<span class="text-muted small" style="font-size: 11px;">Add</span>') +
            '</div>';
        });

        elAvailableList.innerHTML = html;
    }

    // Render Mini Preview Bar of Selected Sequence
    function renderPreviewBar() {
        if (!elPreviewBar) return;

        var selected = stripBuilderState.selectedLeaders;

        if (elPreviewCount) {
            elPreviewCount.textContent = selected.length + ' / 6 Selected';
        }

        if (selected.length === 0) {
            elPreviewBar.innerHTML = '<div class="text-secondary small text-center w-100 py-1" id="builder-preview-empty">' +
                'Click leaders below to add them to your strip.' +
            '</div>';
            return;
        }

        var html = '';
        selected.forEach(function (leader, idx) {
            html += '<div class="bs-mini-chip" title="Order #' + (idx + 1) + ' - Click X to remove">' +
                '<span class="chip-order">#' + (idx + 1) + '</span>' +
                '<img src="' + leader.image_url + '" alt="' + leader.name + '">' +
                '<span class="chip-name">' + leader.name + '</span>' +
                '<i class="ti ti-x chip-remove" data-id="' + leader.id + '"></i>' +
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
            // Select (Max 6)
            if (stripBuilderState.selectedLeaders.length >= 6) {
                showModalAlert('Maximum 6 leaders can be added to a strip.', 'warning');
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
        renderPreviewBar();
    }

    // Search Input Listener
    if (inputSearch) {
        inputSearch.addEventListener('input', function () {
            renderAvailableLeaders(inputSearch.value);
        });
    }

    // Click handler for Leaders List
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

    // Click handler for removing leader from preview bar
    if (elPreviewBar) {
        elPreviewBar.addEventListener('click', function (e) {
            var btnRemove = e.target.closest('.chip-remove');
            if (!btnRemove) return;
            var leaderId = btnRemove.getAttribute('data-id');
            if (leaderId) {
                toggleLeader(leaderId);
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
