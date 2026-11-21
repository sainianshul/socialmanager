@extends('admin.layouts.app')

@section('title', 'Branding Studio')

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['url' => url('/admin'), 'label' => 'Dashboard'],
        ['label' => 'Branding Studio'],
    ]" />
@endsection

@push('styles')
<style>
/* Modern Elegant Branding Studio Styling */
.bs-card {
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    background: #ffffff;
    overflow: hidden;
}

/* Nested Navigation Sidebar Pills */
.bs-sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.bs-nav-link {
    border: none;
    background: transparent;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    color: #475569;
    text-align: left;
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    transition: all 0.15s ease-in-out;
    text-decoration: none;
}
.bs-nav-link:hover {
    background-color: #f1f5f9;
    color: #0f172a;
}
.bs-nav-link.active {
    background-color: #f0fdfa;
    color: #0d9488;
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(13, 148, 136, 0.1);
}
.bs-nav-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
    font-size: 1.2rem;
    color: #64748b;
    transition: all 0.15s ease;
}
.bs-nav-link.active .bs-nav-icon {
    background: #0d9488;
    border-color: #0d9488;
    color: #ffffff;
}

/* Form Controls */
.bs-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 6px;
}
.bs-input {
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    padding: 0.65rem 0.95rem;
    font-size: 0.95rem;
    transition: all 0.15s ease-in-out;
}
.bs-input:focus {
    border-color: #0d9488;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
}

/* Custom Party Dropdown with Logo & Name */
.bs-party-trigger {
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    padding: 0.6rem 0.95rem;
    background: #ffffff;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-align: left;
    transition: all 0.15s ease-in-out;
}
.bs-party-trigger:hover, .bs-party-trigger:focus {
    border-color: #0d9488;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
}
.bs-party-menu {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08);
    max-height: 320px;
    overflow-y: auto;
    padding: 6px;
}
.bs-party-item {
    border-radius: 8px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #1e293b;
    font-size: 0.92rem;
    transition: all 0.15s ease;
    cursor: pointer;
}
.bs-party-item:hover {
    background-color: #f1f5f9;
    color: #0f172a;
}
.bs-party-item.active {
    background-color: #e6fffa;
    color: #0d9488;
    font-weight: 600;
}
.bs-party-thumb {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: contain;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 2px;
    flex-shrink: 0;
}
.bs-party-thumb-placeholder {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
}

/* Upload Avatar Frame */
.bs-upload-thumb {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    object-fit: cover;
    background: #ffffff;
    flex-shrink: 0;
}
.bs-upload-thumb.transparent-bg {
    background-image: repeating-conic-gradient(#cbd5e1 0% 25%, #ffffff 0% 50%) !important;
    background-size: 12px 12px !important;
}

/* Header Strip Preview Bar */
.bs-strip-preview-bar {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 16px;
    overflow-x: auto;
}
.bs-strip-leader-chip {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-width: 80px;
}
.bs-strip-leader-chip img {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    border: 2px solid #0d9488;
    object-fit: cover;
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}
.bs-strip-leader-chip .leader-name {
    font-size: 0.78rem;
    font-weight: 600;
    color: #1e293b;
    margin-top: 6px;
    white-space: nowrap;
}
.bs-strip-leader-chip .leader-order {
    font-size: 0.68rem;
    color: #64748b;
}

/* Custom Strip Cards & Stacks */
.bs-strip-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    background: #ffffff;
    transition: all 0.2s ease;
}
.bs-strip-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
}
.bs-strip-card.active-default {
    border: 1.5px solid #0d9488;
    background: #f0fdfa;
}
.bs-mini-avatar-stack {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    padding: 4px 0;
}
.bs-strip-badge-default {
    background-color: #0d9488;
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
}
</style>
@endpush

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title fw-bold text-dark mb-0" style="font-size: 1.55rem; letter-spacing: -0.02em;">
                    Branding Studio
                </h1>
                <p class="text-secondary small mb-0 mt-1">Configure your profile details, political party affiliation, photo cutout, and header strips.</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.festival-studio.index') }}" class="btn btn-outline-primary px-3 py-1.5 rounded-pill shadow-xs">
                    <i class="ti ti-sparkles me-1"></i>Festival Studio
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-circle-check fs-2"></i>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Nested Layout Card --}}
    <div class="card bs-card border-0 mb-5">
        <div class="row g-0">

            {{-- Nested Sidebar (Left Column) --}}
            <div class="col-12 col-lg-3 border-end bg-light-subtle">
                <div class="p-3.5 p-md-4">
                    <div class="text-uppercase text-secondary fw-bold small mb-3 ps-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                        Navigation
                    </div>

                    <div class="nav bs-sidebar-nav" id="branding-tab-list" role="tablist">
                        {{-- Tab 1: General Profile --}}
                        <button class="bs-nav-link active" id="tab-general-btn" data-bs-toggle="pill" data-bs-target="#pane-general" type="button" role="tab" aria-controls="pane-general" aria-selected="true">
                            <div class="bs-nav-icon">
                                <i class="ti ti-user"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">General Profile</div>
                                <div class="text-secondary small" style="font-size: 0.75rem;">Name, post & contact</div>
                            </div>
                        </button>

                        {{-- Tab 2: Political Party --}}
                        <button class="bs-nav-link" id="tab-party-btn" data-bs-toggle="pill" data-bs-target="#pane-party" type="button" role="tab" aria-controls="pane-party" aria-selected="false">
                            <div class="bs-nav-icon">
                                <i class="ti ti-flag"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Political Party</div>
                                <div class="text-secondary small" style="font-size: 0.75rem;">Party symbol & identity</div>
                            </div>
                        </button>

                        {{-- Tab 3: Photo Cutout --}}
                        <button class="bs-nav-link" id="tab-cutout-btn" data-bs-toggle="pill" data-bs-target="#pane-cutout" type="button" role="tab" aria-controls="pane-cutout" aria-selected="false">
                            <div class="bs-nav-icon">
                                <i class="ti ti-photo"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Photo Cutout</div>
                                <div class="text-secondary small" style="font-size: 0.75rem;">Cutout & photo upload</div>
                            </div>
                        </button>

                        {{-- Tab 4: Header Strips --}}
                        <button class="bs-nav-link" id="tab-strips-btn" data-bs-toggle="pill" data-bs-target="#pane-strips" type="button" role="tab" aria-controls="pane-strips" aria-selected="false">
                            <div class="bs-nav-icon">
                                <i class="ti ti-layout-navbar"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-bold">Header Strips</span>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5" style="font-size: 10px;">Next</span>
                                </div>
                                <div class="text-secondary small" style="font-size: 0.75rem;">Leadership strip preview</div>
                            </div>
                        </button>
                    </div>

                    {{-- Quick Info Card --}}
                    <div class="mt-4 p-3 rounded-3 bg-white border border-light-subtle shadow-xs">
                        <div class="small fw-bold text-dark mb-1">
                            <i class="ti ti-info-circle text-primary me-1"></i>Quick Note
                        </div>
                        <p class="text-secondary small mb-0" style="font-size: 0.78rem; line-height: 1.45;">
                            All profile changes are automatically applied when generating festival and political posters.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Nested Tab Content (Right Column) --}}
            <div class="col-12 col-lg-9">
                <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" id="branding-form">
                    @csrf

                    <div class="tab-content" id="branding-tab-content">

                        {{-- ======================================================================
                             TAB PANE 1: GENERAL PROFILE
                             ====================================================================== --}}
                        <div class="tab-pane fade show active p-4 p-md-5" id="pane-general" role="tabpanel" aria-labelledby="tab-general-btn">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                                <div>
                                    <h3 class="fw-bold text-dark mb-1" style="font-size: 1.25rem;">General Profile Information</h3>
                                    <p class="text-secondary small mb-0">Your name, official designation, contact number, and social media handles.</p>
                                </div>
                                <span class="badge bg-light text-secondary border px-2.5 py-1">General</span>
                            </div>

                            {{-- Name & Designation --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="bs-label">
                                        <i class="ti ti-user text-primary"></i>Display Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="display_name" id="input-display-name" class="form-control bs-input" 
                                           value="{{ old('display_name', $branding->display_name ?? $user->name) }}" 
                                           placeholder="e.g. Dr. Dharam Singh Saini" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="bs-label">
                                        <i class="ti ti-medal text-primary"></i>Designation
                                    </label>
                                    <input type="text" name="designation" id="input-designation" class="form-control bs-input" 
                                           value="{{ old('designation', $branding->designation) }}" 
                                           placeholder="e.g. Former Minister (Uttar Pradesh)">
                                </div>
                            </div>

                            {{-- Client Type & Phone --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="bs-label">
                                        <i class="ti ti-briefcase text-primary"></i>Profile Category
                                    </label>
                                    <select name="client_type" id="input-client-type" class="form-select bs-input">
                                        <option value="politician" {{ old('client_type', $branding->client_type ?? $user->client_type) === 'politician' ? 'selected' : '' }}>
                                            Political Leader / Representative
                                        </option>
                                        <option value="business" {{ old('client_type', $branding->client_type ?? $user->client_type) === 'business' ? 'selected' : '' }}>
                                            Business / Organization
                                        </option>
                                        <option value="personal" {{ old('client_type', $branding->client_type ?? $user->client_type) === 'personal' ? 'selected' : '' }}>
                                            Personal Brand / Creator
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="bs-label">
                                        <i class="ti ti-phone text-primary"></i>Phone Number
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-success">
                                            <i class="ti ti-phone fs-3"></i>
                                        </span>
                                        <input type="text" name="phone_number" id="input-phone" class="form-control bs-input border-start-0 ps-0" 
                                               value="{{ old('phone_number', $branding->phone_number) }}" 
                                               placeholder="+91 98765 43210">
                                    </div>
                                </div>
                            </div>

                            {{-- Social Media Handles --}}
                            <div class="mb-4">
                                <label class="bs-label mb-2.5">
                                    <i class="ti ti-share text-primary"></i>Social Media Handles
                                </label>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0 text-primary">
                                                <i class="ti ti-brand-facebook fs-3"></i>
                                            </span>
                                            <input type="text" name="facebook_handle" id="input-facebook" class="form-control bs-input border-start-0 ps-0" 
                                                   value="{{ old('facebook_handle', $branding->facebook_handle) }}" 
                                                   placeholder="Facebook username">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0 text-danger">
                                                <i class="ti ti-brand-instagram fs-3"></i>
                                            </span>
                                            <input type="text" name="instagram_handle" id="input-instagram" class="form-control bs-input border-start-0 ps-0" 
                                                   value="{{ old('instagram_handle', $branding->instagram_handle) }}" 
                                                   placeholder="Instagram handle">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0 text-dark">
                                                <i class="ti ti-brand-x fs-3"></i>
                                            </span>
                                            <input type="text" name="twitter_handle" id="input-twitter" class="form-control bs-input border-start-0 ps-0" 
                                                   value="{{ old('twitter_handle', $branding->twitter_handle) }}" 
                                                   placeholder="Twitter / X handle">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Save Button for General Tab --}}
                            <div class="pt-4 border-top text-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="ti ti-device-floppy me-1.5 fs-2"></i>Save General Profile
                                </button>
                            </div>
                        </div>

                        {{-- ======================================================================
                             TAB PANE 2: POLITICAL PARTY
                             ====================================================================== --}}
                        <div class="tab-pane fade p-4 p-md-5" id="pane-party" role="tabpanel" aria-labelledby="tab-party-btn">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                                <div>
                                    <h3 class="fw-bold text-dark mb-1" style="font-size: 1.25rem;">Political Party Affiliation</h3>
                                    <p class="text-secondary small mb-0">Choose your party to display official logo symbols and color schemes on banners.</p>
                                </div>
                                <span class="badge bg-light text-secondary border px-2.5 py-1">Party</span>
                            </div>

                            @php
                                $selectedPartyId = old('party_id', $branding->party_id);
                                $selectedParty = $parties->firstWhere('id', $selectedPartyId);
                            @endphp

                            <div class="mb-4">
                                <label class="bs-label">
                                    <i class="ti ti-flag text-primary"></i>Selected Political Party
                                </label>

                                <div class="dropdown" id="partyDropdownContainer">
                                    <button type="button" class="bs-party-trigger" id="partyDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-2.5" id="partySelectedDisplay">
                                            @if($selectedParty)
                                                @if($selectedParty->symbol_logo && file_exists(public_path($selectedParty->symbol_logo)))
                                                    <img src="{{ asset($selectedParty->symbol_logo) }}" alt="{{ $selectedParty->name }}" class="bs-party-thumb">
                                                @else
                                                    <span class="bs-party-thumb-placeholder">{{ $selectedParty->short_code }}</span>
                                                @endif
                                                <div>
                                                    <span class="fw-bold text-dark">{{ $selectedParty->name }}</span>
                                                    <span class="text-secondary small ms-1">({{ $selectedParty->short_code }})</span>
                                                </div>
                                            @else
                                                <span class="bs-party-thumb-placeholder">--</span>
                                                <span class="text-muted fw-normal">-- None / Independent --</span>
                                            @endif
                                        </div>
                                        <i class="ti ti-chevron-down text-muted fs-3"></i>
                                    </button>

                                    <ul class="dropdown-menu w-100 bs-party-menu" aria-labelledby="partyDropdownBtn">
                                        <li>
                                            <div class="bs-party-item {{ empty($selectedPartyId) ? 'active' : '' }}" 
                                                 data-id="" 
                                                 data-name="-- None / Independent --" 
                                                 data-logo="" 
                                                 data-code="">
                                                <span class="bs-party-thumb-placeholder">--</span>
                                                <span class="text-secondary">None / Independent</span>
                                            </div>
                                        </li>
                                        @foreach($parties as $party)
                                            @php
                                                $hasLogo = $party->symbol_logo && file_exists(public_path($party->symbol_logo));
                                                $logoUrl = $hasLogo ? asset($party->symbol_logo) : '';
                                            @endphp
                                            <li>
                                                <div class="bs-party-item {{ $selectedPartyId == $party->id ? 'active' : '' }}" 
                                                     data-id="{{ $party->id }}" 
                                                     data-name="{{ $party->name }}"
                                                     data-code="{{ $party->short_code }}"
                                                     data-logo="{{ $logoUrl }}">
                                                    @if($hasLogo)
                                                        <img src="{{ $logoUrl }}" alt="{{ $party->name }}" class="bs-party-thumb">
                                                    @else
                                                        <span class="bs-party-thumb-placeholder">{{ $party->short_code }}</span>
                                                    @endif
                                                    <div class="d-flex align-items-center gap-1.5 flex-grow-1">
                                                        <span class="fw-bold text-dark">{{ $party->name }}</span>
                                                        <span class="text-secondary small">({{ $party->short_code }})</span>
                                                    </div>
                                                    @if($selectedPartyId == $party->id)
                                                        <i class="ti ti-check text-success ms-auto"></i>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <input type="hidden" name="party_id" id="input-party-id" value="{{ $selectedPartyId }}">
                                </div>
                            </div>

                            {{-- Party Identity Preview Card --}}
                            @if($selectedParty)
                                <div class="p-3.5 rounded-3 border bg-light-subtle d-flex align-items-center gap-3 mb-4">
                                    @if($selectedParty->symbol_logo && file_exists(public_path($selectedParty->symbol_logo)))
                                        <img src="{{ asset($selectedParty->symbol_logo) }}" alt="{{ $selectedParty->name }}" style="width: 50px; height: 50px; object-fit: contain; background: #fff; padding: 4px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $selectedParty->name }} ({{ $selectedParty->short_code }})</div>
                                        <div class="text-secondary small">Official party symbols and branding badges will be placed on generated posters automatically.</div>
                                    </div>
                                </div>
                            @endif

                            {{-- Save Button for Party Tab --}}
                            <div class="pt-4 border-top text-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="ti ti-device-floppy me-1.5 fs-2"></i>Save Political Party
                                </button>
                            </div>
                        </div>

                        {{-- ======================================================================
                             TAB PANE 3: PHOTO CUTOUT
                             ====================================================================== --}}
                        <div class="tab-pane fade p-4 p-md-5" id="pane-cutout" role="tabpanel" aria-labelledby="tab-cutout-btn">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                                <div>
                                    <h3 class="fw-bold text-dark mb-1" style="font-size: 1.25rem;">Profile Photo & Background Removal</h3>
                                    <p class="text-secondary small mb-0">Upload your portrait photo and remove the background for high quality poster placement.</p>
                                </div>
                                <span class="badge bg-light text-secondary border px-2.5 py-1">Photo</span>
                            </div>

                            <div class="p-4 rounded-3 border bg-light-subtle mb-4">
                                <div class="d-flex align-items-center gap-4 flex-wrap flex-sm-nowrap">
                                    <div>
                                        <img id="upload-preview-thumb" src="{{ $currentPhotoUrl }}" alt="Photo" class="bs-upload-thumb">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="bs-label mb-2">
                                            <i class="ti ti-upload text-primary"></i>Upload New Photo
                                        </label>
                                        <input type="file" name="branding_image" id="input-branding-image" class="form-control bs-input" accept="image/png,image/jpeg,image/webp">
                                        <div class="form-text small text-secondary mt-1">Recommended: Clear portrait or bust photo with good lighting (JPG, PNG, WebP up to 10MB).</div>
                                    </div>
                                </div>

                                {{-- Background Removal Action (Appears after photo selection) --}}
                                <div id="bg-remove-box" class="mt-3.5 p-3 rounded-3 bg-white border d-none">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <span class="small text-dark fw-semibold">Remove background from this photo?</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" id="btn-remove-bg">
                                            <i class="ti ti-cut me-1"></i>Remove Background
                                        </button>
                                    </div>
                                    <div id="bg-remove-status" class="mt-2 d-none"></div>
                                </div>
                            </div>

                            {{-- Save Button for Cutout Tab --}}
                            <div class="pt-4 border-top text-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="ti ti-device-floppy me-1.5 fs-2"></i>Save Photo
                                </button>
                            </div>
                        </div>

                        {{-- ======================================================================
                             TAB PANE 4: HEADER STRIPS (MANAGEMENT & PREVIEW)
                             ====================================================================== --}}
                        <div class="tab-pane fade p-4 p-md-5" id="pane-strips" role="tabpanel" aria-labelledby="tab-strips-btn">
                            {{-- Header with Create Strip Button --}}
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom flex-wrap gap-2">
                                <div>
                                    <h3 class="fw-bold text-dark mb-1" style="font-size: 1.25rem;">Leadership Header Strips</h3>
                                    <p class="text-secondary small mb-0">Manage the top leadership banners displayed at the head of your political and festival posters.</p>
                                </div>
                                <button type="button" class="btn btn-primary rounded-pill px-3.5 py-1.5 shadow-sm" id="btn-create-strip-modal">
                                    <i class="ti ti-plus me-1.5"></i>Create New Strip
                                </button>
                            </div>

                            {{-- Dynamic AJAX Status Alerts --}}
                            <div id="strip-alert-container"></div>

                            {{-- Section 1: Active Default Strip --}}
                            @if($activeStrip)
                                <div class="card border border-teal-subtle rounded-3 p-3.5 mb-4 shadow-xs" style="background: linear-gradient(to right, #f0fdfa, #ffffff);" id="active-strip-banner">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-teal text-white rounded-pill px-2.5 py-1">
                                                <i class="ti ti-check me-1"></i>Active Poster Strip
                                            </span>
                                            <span class="fw-bold text-dark fs-4" id="active-strip-title">{{ $activeStrip->name }}</span>
                                            @if($activeStrip->is_system)
                                                <span class="badge bg-secondary-lt rounded-pill">Party Official</span>
                                            @else
                                                <span class="badge bg-primary-lt rounded-pill">Custom Strip</span>
                                            @endif
                                        </div>
                                        <div class="text-secondary small">
                                            <i class="ti ti-users me-1 text-primary"></i><span id="active-strip-leader-count">{{ $activeStrip->leaders->count() }}</span> Leaders Included
                                        </div>
                                    </div>

                                    {{-- Strip Visual Preview Chips --}}
                                    <div class="bs-strip-preview-bar">
                                        @forelse($activeStrip->leaders as $leader)
                                            <div class="bs-strip-leader-chip">
                                                <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}">
                                                <span class="leader-name">{{ Str::limit($leader->name, 14) }}</span>
                                                <span class="leader-order">#{{ $leader->pivot->order ?? $loop->iteration }}</span>
                                            </div>
                                        @empty
                                            <span class="text-secondary small">No leaders in this strip.</span>
                                        @endforelse
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info rounded-3 mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ti ti-info-circle fs-2"></i>
                                        <span>No leadership strip selected. Please choose your political party in the Political Party tab to link default leadership strips.</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Section 2: My Custom Strips --}}
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h4 class="fw-bold text-dark mb-0" style="font-size: 1rem;">
                                        <i class="ti ti-bookmarks me-1.5 text-primary"></i>My Custom Strips
                                    </h4>
                                    <span class="text-secondary small" id="user-strips-counter">{{ $userStrips->count() }} Saved</span>
                                </div>

                                @if($userStrips->isNotEmpty())
                                    <div class="row g-3" id="user-strips-list">
                                        @foreach($userStrips as $strip)
                                            <div class="col-12" id="strip-card-{{ $strip->id }}">
                                                <div class="bs-strip-card {{ $strip->is_default ? 'active-default' : '' }}">
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="fw-bold text-dark fs-4">{{ $strip->name }}</span>
                                                            @if($strip->is_default)
                                                                <span class="bs-strip-badge-default" id="badge-default-{{ $strip->id }}">
                                                                    <i class="ti ti-check me-1"></i>Default
                                                                </span>
                                                            @else
                                                                <span class="badge bg-light text-secondary border d-none" id="badge-default-{{ $strip->id }}">
                                                                    Default
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex align-items-center gap-1.5">
                                                            @if(!$strip->is_default)
                                                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 btn-make-default" data-id="{{ $strip->id }}">
                                                                    <i class="ti ti-check me-1"></i>Set as Default
                                                                </button>
                                                            @endif
                                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-edit-strip" data-id="{{ $strip->id }}" data-name="{{ $strip->name }}">
                                                                <i class="ti ti-pencil me-1"></i>Edit
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 btn-delete-strip" data-id="{{ $strip->id }}" title="Delete strip">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    {{-- Mini Leaders Avatars Stack --}}
                                                    <div class="bs-mini-avatar-stack">
                                                        @foreach($strip->leaders as $leader)
                                                            <div class="d-flex align-items-center gap-1 bg-white border rounded-pill px-2 py-0.5" title="{{ $leader->name }} (#{{ $leader->pivot->order ?? $loop->iteration }})">
                                                                <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}" style="width: 24px; height: 24px; border-radius: 50%;">
                                                                <span class="small fw-semibold text-dark" style="font-size: 0.78rem;">{{ Str::limit($leader->name, 12) }}</span>
                                                                <span class="text-secondary small" style="font-size: 0.7rem;">#{{ $leader->pivot->order ?? $loop->iteration }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 rounded-3 border border-dashed text-center bg-light-subtle mb-3" id="no-user-strips-notice">
                                        <i class="ti ti-layout-navbar fs-1 text-secondary mb-2 d-block"></i>
                                        <div class="fw-bold text-dark">No custom leadership strips yet</div>
                                        <p class="text-secondary small mb-3">You can create custom strips with your preferred leaders hierarchy, or use the official party default strip below.</p>
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="btn-create-first-strip">
                                            <i class="ti ti-plus me-1"></i>Create Your First Strip
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Section 3: Official Ready-Made Party Strip --}}
                            @if($systemStrip)
                                <div class="mb-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h4 class="fw-bold text-dark mb-0" style="font-size: 1rem;">
                                            <i class="ti ti-flag me-1.5 text-warning"></i>Official {{ $systemStrip->party?->name ?? 'Party' }} Strip
                                        </h4>
                                        <span class="badge bg-secondary-lt rounded-pill">System Default</span>
                                    </div>

                                    <div class="bs-strip-card {{ (!$userStrips->contains('is_default', true) && $activeStrip?->id === $systemStrip->id) ? 'active-default' : '' }}">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                            <div>
                                                <span class="fw-bold text-dark fs-4">{{ $systemStrip->name }}</span>
                                                <span class="text-secondary small ms-2">({{ $systemStrip->leaders->count() }} Top Leaders)</span>
                                            </div>
                                            <div>
                                                @if($activeStrip?->id === $systemStrip->id && !$userStrips->contains('is_default', true))
                                                    <span class="bs-strip-badge-default">
                                                        <i class="ti ti-check me-1"></i>Active Default
                                                    </span>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-make-default" data-id="{{ $systemStrip->id }}">
                                                        <i class="ti ti-copy me-1"></i>Activate as My Strip
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Mini Leaders Stack --}}
                                        <div class="bs-mini-avatar-stack">
                                            @foreach($systemStrip->leaders as $leader)
                                                <div class="d-flex align-items-center gap-1 bg-white border rounded-pill px-2 py-0.5" title="{{ $leader->name }} (#{{ $leader->pivot->order ?? $loop->iteration }})">
                                                    <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}" style="width: 24px; height: 24px; border-radius: 50%;">
                                                    <span class="small fw-semibold text-dark" style="font-size: 0.78rem;">{{ Str::limit($leader->name, 12) }}</span>
                                                    <span class="text-secondary small" style="font-size: 0.7rem;">#{{ $leader->pivot->order ?? $loop->iteration }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Section 4: Leadership Hierarchy Table --}}
                            @if($activeStrip && $activeStrip->leaders->isNotEmpty())
                                <div class="mb-2">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Active Strip Hierarchy Details</h5>
                                        <span class="badge bg-light text-secondary border">Order 1 to {{ $activeStrip->leaders->count() }}</span>
                                    </div>
                                    <div class="table-responsive rounded-3 border">
                                        <table class="table table-vcenter table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width: 80px;">Order</th>
                                                    <th style="width: 70px;">Photo</th>
                                                    <th>Leader Name</th>
                                                    <th>Designation</th>
                                                    <th>Scope</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($activeStrip->leaders as $leader)
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-light text-dark border">
                                                                #{{ $leader->pivot->order ?? $loop->iteration }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1;">
                                                        </td>
                                                        <td class="fw-bold text-dark">{{ $leader->name }}</td>
                                                        <td class="text-secondary">{{ $leader->designation ?: '--' }}</td>
                                                        <td>
                                                            <span class="badge bg-primary-lt">{{ $leader->state ?: 'National' }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Preserve Active Tab via URL Hash (#pane-general, #pane-party, #pane-cutout, #pane-strips)
    var hash = window.location.hash;
    if (hash) {
        var targetBtn = document.querySelector('[data-bs-target="' + hash + '"]');
        if (targetBtn) {
            var tabTrigger = new bootstrap.Tab(targetBtn);
            tabTrigger.show();
        }
    }

    var tabButtons = document.querySelectorAll('#branding-tab-list button[data-bs-toggle="pill"]');
    tabButtons.forEach(function (btn) {
        btn.addEventListener('shown.bs.tab', function (e) {
            var target = e.target.getAttribute('data-bs-target');
            if (target && history.replaceState) {
                history.replaceState(null, null, target);
            }
        });
    });

    // Custom Party Dropdown Interaction (Image + Name)
    var partyItems = document.querySelectorAll('.bs-party-item');
    var partyHiddenInput = document.getElementById('input-party-id');
    var partyDisplay = document.getElementById('partySelectedDisplay');

    partyItems.forEach(function (item) {
        item.addEventListener('click', function () {
            partyItems.forEach(function (i) { i.classList.remove('active'); });
            item.classList.add('active');

            var partyId = item.getAttribute('data-id');
            var partyName = item.getAttribute('data-name');
            var partyCode = item.getAttribute('data-code');
            var partyLogo = item.getAttribute('data-logo');

            if (partyHiddenInput) {
                partyHiddenInput.value = partyId;
            }

            if (partyDisplay) {
                if (partyId) {
                    var thumbHtml = partyLogo 
                        ? '<img src="' + partyLogo + '" alt="' + partyName + '" class="bs-party-thumb">' 
                        : '<span class="bs-party-thumb-placeholder">' + partyCode + '</span>';

                    partyDisplay.innerHTML = thumbHtml + '<div><span class="fw-bold text-dark">' + partyName + '</span><span class="text-secondary small ms-1">(' + partyCode + ')</span></div>';
                } else {
                    partyDisplay.innerHTML = '<span class="bs-party-thumb-placeholder">--</span><span class="text-muted fw-normal">-- None / Independent --</span>';
                }
            }
        });
    });

    // Image Upload & Background Removal
    var imageInput = document.getElementById('input-branding-image');
    var previewThumb = document.getElementById('upload-preview-thumb');
    var bgRemoveBox = document.getElementById('bg-remove-box');
    var btnRemoveBg = document.getElementById('btn-remove-bg');
    var bgStatus = document.getElementById('bg-remove-status');

    var originalFile = null;
    var originalThumbUrl = null;

    if (imageInput && previewThumb) {
        imageInput.addEventListener('change', function () {
            if (imageInput.files && imageInput.files[0]) {
                originalFile = imageInput.files[0];
                var reader = new FileReader();
                reader.onload = function (e) {
                    originalThumbUrl = e.target.result;
                    previewThumb.src = e.target.result;
                    previewThumb.classList.remove('transparent-bg');
                };
                reader.readAsDataURL(originalFile);

                // Reveal background removal option box
                if (bgRemoveBox) {
                    bgRemoveBox.classList.remove('d-none');
                }
                if (btnRemoveBg) {
                    btnRemoveBg.classList.remove('d-none');
                    btnRemoveBg.disabled = false;
                }
                if (bgStatus) {
                    bgStatus.classList.add('d-none');
                    bgStatus.innerHTML = '';
                }
            }
        });
    }

    // Background Removal Trigger
    if (btnRemoveBg) {
        btnRemoveBg.addEventListener('click', async function () {
            if (!originalFile) return;

            btnRemoveBg.disabled = true;
            if (bgStatus) {
                bgStatus.classList.remove('d-none');
                bgStatus.innerHTML = '<div class="d-flex align-items-center gap-2 text-primary small py-1">' +
                    '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>' +
                    '<span>Removing background...</span>' +
                '</div>';
            }

            try {
                // Dynamic import of self-contained browser bundle
                var imgly = await import('https://cdn.jsdelivr.net/npm/@imgly/background-removal/+esm');
                var removeBackground = imgly.removeBackground;

                var blob = await removeBackground(originalFile, {
                    model: 'small',
                    device: 'cpu'
                });

                var transparentUrl = URL.createObjectURL(blob);

                // Update thumbnail with transparent background pattern
                if (previewThumb) {
                    previewThumb.src = transparentUrl;
                    previewThumb.classList.add('transparent-bg');
                }

                // Place transparent PNG file into the file input
                var cutoutFile = new File([blob], 'cutout_' + Date.now() + '.png', { type: 'image/png' });
                var dt = new DataTransfer();
                dt.items.add(cutoutFile);
                imageInput.files = dt.files;

                btnRemoveBg.classList.add('d-none');

                if (bgStatus) {
                    bgStatus.innerHTML = '<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-success small py-1">' +
                        '<span class="d-flex align-items-center gap-1.5 fw-semibold">' +
                            '<i class="ti ti-circle-check fs-2 text-success"></i>Background removed.' +
                        '</span>' +
                        '<button type="button" class="btn btn-sm btn-link text-secondary p-0" id="btn-revert-bg">Revert to original</button>' +
                    '</div>';

                    var btnRevert = document.getElementById('btn-revert-bg');
                    if (btnRevert) {
                        btnRevert.addEventListener('click', function () {
                            if (originalFile && imageInput) {
                                var dtOrig = new DataTransfer();
                                dtOrig.items.add(originalFile);
                                imageInput.files = dtOrig.files;
                            }
                            if (previewThumb && originalThumbUrl) {
                                previewThumb.src = originalThumbUrl;
                                previewThumb.classList.remove('transparent-bg');
                            }
                            btnRemoveBg.classList.remove('d-none');
                            btnRemoveBg.disabled = false;
                            bgStatus.innerHTML = '<span class="text-secondary small">Reverted to original photo.</span>';
                        });
                    }
                }
            } catch (err) {
                console.error('Background removal error:', err);
                if (bgStatus) {
                    bgStatus.innerHTML = '<div class="text-secondary small py-1">' +
                        'Could not remove background. Your original photo will be used.' +
                    '</div>';
                }
                btnRemoveBg.classList.remove('d-none');
                btnRemoveBg.disabled = false;
            }
        });
    }

    // Header Strips: AJAX Status Alert Helper
    function showStripAlert(message, type) {
        var alertContainer = document.getElementById('strip-alert-container');
        if (!alertContainer) return;
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var iconClass = type === 'success' ? 'ti-circle-check' : 'ti-alert-circle';
        alertContainer.innerHTML = '<div class="alert ' + alertClass + ' alert-dismissible fade show rounded-3 shadow-xs mb-3" role="alert">' +
            '<div class="d-flex align-items-center gap-2">' +
                '<i class="ti ' + iconClass + ' fs-2"></i>' +
                '<span class="fw-semibold">' + message + '</span>' +
            '</div>' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
        '</div>';
    }

    // Header Strips: Set as Default Action
    document.addEventListener('click', async function (e) {
        var target = e.target.closest('.btn-make-default');
        if (!target) return;

        var stripId = target.getAttribute('data-id');
        if (!stripId) return;

        target.disabled = true;
        var originalHtml = target.innerHTML;
        target.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Setting...';

        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            var response = await fetch('/admin/leader-strips/' + stripId + '/default', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            var result = await response.json();
            if (result.success) {
                showStripAlert(result.message || 'Default leadership strip updated.', 'success');
                setTimeout(function () {
                    window.location.hash = '#pane-strips';
                    window.location.reload();
                }, 500);
            } else {
                showStripAlert(result.message || 'Could not set default strip.', 'danger');
                target.disabled = false;
                target.innerHTML = originalHtml;
            }
        } catch (err) {
            console.error('Make default strip error:', err);
            showStripAlert('An error occurred while updating default strip.', 'danger');
            target.disabled = false;
            target.innerHTML = originalHtml;
        }
    });

    // Header Strips: Delete Action
    document.addEventListener('click', async function (e) {
        var target = e.target.closest('.btn-delete-strip');
        if (!target) return;

        var stripId = target.getAttribute('data-id');
        if (!stripId) return;

        if (!confirm('Are you sure you want to delete this custom leadership strip?')) {
            return;
        }

        target.disabled = true;
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            var response = await fetch('/admin/leader-strips/' + stripId, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            var result = await response.json();
            if (result.success) {
                var stripCard = document.getElementById('strip-card-' + stripId);
                if (stripCard) {
                    stripCard.style.transition = 'opacity 0.3s ease';
                    stripCard.style.opacity = '0';
                    setTimeout(function () {
                        stripCard.remove();
                    }, 300);
                }
                showStripAlert(result.message || 'Strip deleted successfully.', 'success');
            } else {
                showStripAlert(result.message || 'Could not delete strip.', 'danger');
                target.disabled = false;
            }
        } catch (err) {
            console.error('Delete strip error:', err);
            showStripAlert('An error occurred while deleting strip.', 'danger');
            target.disabled = false;
        }
    });
});
</script>
@endpush