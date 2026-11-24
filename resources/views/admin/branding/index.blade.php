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

/* Photo Gallery & Checkerboard Background */
.checkerboard-bg {
    background-color: #f8fafc;
    background-image: repeating-conic-gradient(#e2e8f0 0% 25%, #ffffff 0% 50%) !important;
    background-size: 14px 14px !important;
}
.photo-gallery-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.photo-gallery-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
}
.photo-gallery-card.is-active-default {
    border-color: #0d9488 !important;
    box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.25) !important;
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

                        {{-- Link 4: Leadership Header Strips (Opens Dedicated Page) --}}
                        <a href="{{ route('admin.leader-strips.index') }}" class="bs-nav-link text-decoration-none" id="nav-strips-link">
                            <div class="bs-nav-icon">
                                <i class="ti ti-layout-navbar"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-bold">Leadership Header Strips</span>
                                    <i class="ti ti-chevron-right text-secondary small"></i>
                                </div>
                                <div class="text-secondary small" style="font-size: 0.75rem;">Manage poster leader banners</div>
                            </div>
                        </a>
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
                                        <i class="ti ti-user text-primary"></i>Display Name (English) <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="display_name" id="input-display-name" class="form-control bs-input" 
                                           value="{{ old('display_name', $branding->display_name ?? $user->name) }}" 
                                           placeholder="e.g. Dr. Dharam Singh Saini" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="bs-label">
                                        <i class="ti ti-language text-primary"></i>Hindi Name (हिंदी में नाम)
                                    </label>
                                    <input type="text" name="hindi_name" id="input-hindi-name" class="form-control bs-input" 
                                           value="{{ old('hindi_name', $branding->hindi_name) }}" 
                                           placeholder="उदा. डॉ. धर्म सिंह सैनी">
                                    <span class="text-secondary small" style="font-size: 0.75rem;">If provided, this Hindi name will appear on poster branding strips.</span>
                                </div>
                                <div class="col-md-12">
                                    <label class="bs-label">
                                        <i class="ti ti-medal text-primary"></i>Designation / Role
                                    </label>
                                    <input type="text" name="designation" id="input-designation" class="form-control bs-input" 
                                           value="{{ old('designation', $branding->designation) }}" 
                                           placeholder="e.g. पूर्व मंत्री (उत्तर प्रदेश सरकार)">
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
                             TAB PANE 3: PHOTO CUTOUT & MULTI-PHOTO GALLERY
                             ====================================================================== --}}
                        <div class="tab-pane fade p-4 p-md-5" id="pane-cutout" role="tabpanel" aria-labelledby="tab-cutout-btn">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom flex-wrap gap-2">
                                <div>
                                    <h3 class="fw-bold text-dark mb-1" style="font-size: 1.25rem;">Branding Photos & Cutout Gallery</h3>
                                    <p class="text-secondary small mb-0">Upload multiple photos and select a default one for your festival and political posters.</p>
                                </div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill">
                                    <i class="ti ti-photo me-1"></i>Multi-Photo Studio
                                </span>
                            </div>

                            {{-- Dynamic Alert Box for Gallery Actions --}}
                            <div id="gallery-alert-box" class="d-none mb-4"></div>

                            {{-- Top Card: Upload New Photo --}}
                            <div class="p-4 rounded-3 border bg-light-subtle mb-4">
                                <div class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <i class="ti ti-cloud-upload text-primary fs-3"></i>
                                    <span>Upload New Photo (नई तस्वीर जोड़ें)</span>
                                </div>

                                <div class="d-flex align-items-center gap-4 flex-wrap flex-sm-nowrap">
                                    <div>
                                        <img id="upload-preview-thumb" src="{{ $currentPhotoUrl }}" alt="Photo" class="bs-upload-thumb">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="bs-label mb-2">
                                            <i class="ti ti-upload text-primary"></i>Choose Image File
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

                                {{-- Quick Upload Options --}}
                                <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="check-make-default" {{ $brandingImages->isEmpty() ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold text-dark" for="check-make-default">
                                            Set as active default photo (मुख्य फोटो बनाएं)
                                        </label>
                                    </div>

                                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm" id="btn-upload-direct">
                                        <i class="ti ti-cloud-upload me-1.5 fs-2"></i>Upload to Gallery
                                    </button>
                                </div>
                            </div>

                            {{-- Bottom Section: Photo Collection Grid --}}
                            <div class="mt-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">My Photo Collection (आपकी फोटो गैलरी)</h4>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-0.5 small" id="gallery-counter">
                                            {{ $brandingImages->count() }} {{ Str::plural('photo', $brandingImages->count()) }}
                                        </span>
                                    </div>
                                    <span class="text-secondary small">Click "Set Default" to choose active poster photo</span>
                                </div>

                                {{-- Photos Grid --}}
                                <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4" id="photo-gallery-grid">
                                    @foreach($brandingImages as $img)
                                        <div class="col" id="photo-col-{{ $img->id }}">
                                            <div class="card h-100 border rounded-3 position-relative photo-gallery-card {{ $img->is_default ? 'is-active-default border-primary' : 'border-light-subtle' }}" style="background: #ffffff;">
                                                {{-- Delete Button --}}
                                                <button type="button" class="btn btn-sm btn-light text-danger rounded-circle p-0 position-absolute top-0 end-0 m-2 shadow-xs border btn-delete-photo" 
                                                        data-id="{{ $img->id }}" 
                                                        data-url="{{ route('admin.branding.images.destroy', ['image' => $img->id]) }}"
                                                        title="Delete this photo"
                                                        style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; z-index: 2;">
                                                    <i class="ti ti-trash fs-2"></i>
                                                </button>

                                                {{-- Default Badge --}}
                                                <div class="position-absolute top-0 start-0 m-2 z-1">
                                                    <span class="badge bg-success rounded-pill px-2 py-0.5 small shadow-xs default-badge {{ $img->is_default ? '' : 'd-none' }}">
                                                        <i class="ti ti-check me-0.5"></i>Default
                                                    </span>
                                                </div>

                                                {{-- Image Viewport with Checkerboard Pattern --}}
                                                <div class="checkerboard-bg p-2 rounded-top-3 d-flex align-items-center justify-content-center" style="height: 150px; overflow: hidden;">
                                                    <img src="{{ $img->cutout_url }}" alt="Branding Photo" class="img-fluid gallery-photo-img" style="max-height: 135px; object-fit: contain;">
                                                </div>

                                                {{-- Card Footer Action --}}
                                                <div class="p-2.5 text-center bg-white rounded-bottom-3 border-top">
                                                    <button type="button" 
                                                            class="btn btn-xs rounded-pill w-100 py-1 fw-semibold btn-make-default {{ $img->is_default ? 'btn-success disabled' : 'btn-outline-primary' }}"
                                                            data-id="{{ $img->id }}"
                                                            data-url="{{ route('admin.branding.images.default', ['image' => $img->id]) }}">
                                                        @if($img->is_default)
                                                            <i class="ti ti-circle-check me-1"></i>Active Default
                                                        @else
                                                            <i class="ti ti-star me-1"></i>Set Default
                                                        @endif
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Empty State when no photos exist --}}
                                <div id="gallery-empty-state" class="text-center py-5 border border-dashed rounded-3 bg-light-subtle {{ $brandingImages->isEmpty() ? '' : 'd-none' }}">
                                    <div class="mb-2 text-secondary">
                                        <i class="ti ti-photo-off" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                    </div>
                                    <div class="fw-bold text-dark mb-1">No Branding Photos Yet</div>
                                    <div class="text-secondary small mb-3">Upload your portrait cutout photo above to start creating branded festival posters.</div>
                                </div>
                            </div>

                            {{-- Save Button for Cutout Tab --}}
                            <div class="pt-4 border-top text-end mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="ti ti-device-floppy me-1.5 fs-2"></i>Save All Changes
                                </button>
                            </div>
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

    // Support URL param ?tab=cutout
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'cutout') {
        var cutoutBtn = document.getElementById('tab-cutout-btn');
        if (cutoutBtn) {
            new bootstrap.Tab(cutoutBtn).show();
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

    // =========================================================================
    // MULTI-PHOTO GALLERY: UPLOAD, SET DEFAULT & DELETE
    // =========================================================================
    var btnUploadDirect = document.getElementById('btn-upload-direct');
    var checkMakeDefault = document.getElementById('check-make-default');
    var galleryGrid = document.getElementById('photo-gallery-grid');
    var galleryCounter = document.getElementById('gallery-counter');
    var galleryEmpty = document.getElementById('gallery-empty-state');
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function showGalleryAlert(message, type) {
        var alertBox = document.getElementById('gallery-alert-box');
        if (!alertBox) return;
        var icon = type === 'success' ? 'ti-circle-check' : 'ti-alert-circle';
        alertBox.className = 'alert alert-' + type + ' alert-dismissible fade show rounded-3 shadow-xs mb-4';
        alertBox.innerHTML = '<div class="d-flex align-items-center gap-2">' +
            '<i class="ti ' + icon + ' fs-2"></i>' +
            '<span class="fw-semibold">' + message + '</span>' +
        '</div>' +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    }

    function updateGalleryCounter() {
        if (!galleryGrid || !galleryCounter) return;
        var count = galleryGrid.querySelectorAll('.photo-gallery-card').length;
        galleryCounter.textContent = count + ' ' + (count === 1 ? 'photo' : 'photos');
        if (count === 0 && galleryEmpty) {
            galleryEmpty.classList.remove('d-none');
        } else if (galleryEmpty) {
            galleryEmpty.classList.add('d-none');
        }
    }

    function setCardAsActiveDefault(targetCard) {
        if (!galleryGrid) return;
        var cards = galleryGrid.querySelectorAll('.photo-gallery-card');
        cards.forEach(function (card) {
            card.classList.remove('is-active-default', 'border-primary');
            card.classList.add('border-light-subtle');
            var badge = card.querySelector('.default-badge');
            if (badge) badge.classList.add('d-none');
            var btn = card.querySelector('.btn-make-default');
            if (btn) {
                btn.className = 'btn btn-xs rounded-pill w-100 py-1 fw-semibold btn-make-default btn-outline-primary';
                btn.removeAttribute('disabled');
                btn.innerHTML = '<i class="ti ti-star me-1"></i>Set Default';
            }
        });

        if (targetCard) {
            targetCard.classList.add('is-active-default', 'border-primary');
            targetCard.classList.remove('border-light-subtle');
            var targetBadge = targetCard.querySelector('.default-badge');
            if (targetBadge) targetBadge.classList.remove('d-none');
            var targetBtn = targetCard.querySelector('.btn-make-default');
            if (targetBtn) {
                targetBtn.className = 'btn btn-xs rounded-pill w-100 py-1 fw-semibold btn-make-default btn-success disabled';
                targetBtn.setAttribute('disabled', 'disabled');
                targetBtn.innerHTML = '<i class="ti ti-circle-check me-1"></i>Active Default';
            }
        }
    }

    if (btnUploadDirect && imageInput) {
        btnUploadDirect.addEventListener('click', async function () {
            if (!imageInput.files || !imageInput.files[0]) {
                showGalleryAlert('Please select a photo to upload.', 'danger');
                imageInput.focus();
                return;
            }

            var originalBtnHtml = btnUploadDirect.innerHTML;
            btnUploadDirect.disabled = true;
            btnUploadDirect.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" role="status"></span>Uploading...';

            var formData = new FormData();
            formData.append('branding_image', imageInput.files[0]);
            formData.append('is_default', checkMakeDefault && checkMakeDefault.checked ? '1' : '0');

            try {
                var response = await fetch('{{ route('admin.branding.images.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                var data = await response.json();

                if (response.ok && data.success) {
                    showGalleryAlert(data.message || 'Branding photo uploaded successfully.', 'success');

                    // If set as default, update previous cards
                    if (data.image.is_default) {
                        setCardAsActiveDefault(null);
                    }

                    // Prepend new card
                    var defaultRouteTemplate = '{{ route('admin.branding.images.default', ['image' => ':id']) }}';
                    var destroyRouteTemplate = '{{ route('admin.branding.images.destroy', ['image' => ':id']) }}';
                    var defaultUrl = defaultRouteTemplate.replace(':id', data.image.id);
                    var destroyUrl = destroyRouteTemplate.replace(':id', data.image.id);

                    var newCol = document.createElement('div');
                    newCol.className = 'col';
                    newCol.id = 'photo-col-' + data.image.id;
                    newCol.innerHTML = '<div class="card h-100 border rounded-3 position-relative photo-gallery-card ' + (data.image.is_default ? 'is-active-default border-primary' : 'border-light-subtle') + '" style="background: #ffffff;">' +
                        '<button type="button" class="btn btn-sm btn-light text-danger rounded-circle p-0 position-absolute top-0 end-0 m-2 shadow-xs border btn-delete-photo" ' +
                                'data-id="' + data.image.id + '" ' +
                                'data-url="' + destroyUrl + '" ' +
                                'title="Delete this photo" ' +
                                'style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; z-index: 2;">' +
                            '<i class="ti ti-trash fs-2"></i>' +
                        '</button>' +
                        '<div class="position-absolute top-0 start-0 m-2 z-1">' +
                            '<span class="badge bg-success rounded-pill px-2 py-0.5 small shadow-xs default-badge ' + (data.image.is_default ? '' : 'd-none') + '">' +
                                '<i class="ti ti-check me-0.5"></i>Default' +
                            '</span>' +
                        '</div>' +
                        '<div class="checkerboard-bg p-2 rounded-top-3 d-flex align-items-center justify-content-center" style="height: 150px; overflow: hidden;">' +
                            '<img src="' + data.image.url + '" alt="Branding Photo" class="img-fluid gallery-photo-img" style="max-height: 135px; object-fit: contain;">' +
                        '</div>' +
                        '<div class="p-2.5 text-center bg-white rounded-bottom-3 border-top">' +
                            '<button type="button" class="btn btn-xs rounded-pill w-100 py-1 fw-semibold btn-make-default ' + (data.image.is_default ? 'btn-success disabled' : 'btn-outline-primary') + '" ' +
                                    'data-id="' + data.image.id + '" ' +
                                    'data-url="' + defaultUrl + '">' +
                                (data.image.is_default ? '<i class="ti ti-circle-check me-1"></i>Active Default' : '<i class="ti ti-star me-1"></i>Set Default') +
                            '</button>' +
                        '</div>' +
                    '</div>';

                    if (galleryGrid) {
                        galleryGrid.prepend(newCol);
                    }

                    if (previewThumb) {
                        previewThumb.src = data.image.url;
                        previewThumb.classList.remove('transparent-bg');
                    }

                    // Reset input
                    imageInput.value = '';
                    if (bgRemoveBox) bgRemoveBox.classList.add('d-none');
                    if (bgStatus) { bgStatus.classList.add('d-none'); bgStatus.innerHTML = ''; }
                    updateGalleryCounter();
                } else {
                    showGalleryAlert(data.message || 'Failed to upload photo. Please check image format.', 'danger');
                }
            } catch (err) {
                console.error('Upload error:', err);
                showGalleryAlert('Network error while uploading photo. Please try again.', 'danger');
            } finally {
                btnUploadDirect.disabled = false;
                btnUploadDirect.innerHTML = originalBtnHtml;
            }
        });
    }

    // Event Delegation for "Set Default" and "Delete Photo"
    if (galleryGrid) {
        galleryGrid.addEventListener('click', async function (e) {
            // Set Default Action
            var makeDefaultBtn = e.target.closest('.btn-make-default');
            if (makeDefaultBtn && !makeDefaultBtn.disabled && !makeDefaultBtn.classList.contains('disabled')) {
                var url = makeDefaultBtn.getAttribute('data-url');
                var card = makeDefaultBtn.closest('.photo-gallery-card');
                var originalHtml = makeDefaultBtn.innerHTML;

                makeDefaultBtn.disabled = true;
                makeDefaultBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Setting...';

                try {
                    var response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    var data = await response.json();

                    if (response.ok && data.success) {
                        setCardAsActiveDefault(card);
                        showGalleryAlert(data.message || 'Default branding photo updated.', 'success');

                        // Update thumbnail
                        var cardImg = card.querySelector('.gallery-photo-img');
                        if (cardImg && previewThumb) {
                            previewThumb.src = cardImg.src;
                            previewThumb.classList.remove('transparent-bg');
                        }
                    } else {
                        makeDefaultBtn.disabled = false;
                        makeDefaultBtn.innerHTML = originalHtml;
                        showGalleryAlert(data.message || 'Could not set default photo.', 'danger');
                    }
                } catch (err) {
                    console.error('Set default error:', err);
                    makeDefaultBtn.disabled = false;
                    makeDefaultBtn.innerHTML = originalHtml;
                    showGalleryAlert('Network error while setting default photo.', 'danger');
                }
                return;
            }

            // Delete Action
            var deleteBtn = e.target.closest('.btn-delete-photo');
            if (deleteBtn) {
                if (!confirm('Are you sure you want to delete this photo?')) {
                    return;
                }

                var deleteUrl = deleteBtn.getAttribute('data-url');
                var col = deleteBtn.closest('.col');

                deleteBtn.disabled = true;

                try {
                    var delResponse = await fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    var delData = await delResponse.json();

                    if (delResponse.ok && delData.success) {
                        col.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                        col.style.opacity = '0';
                        col.style.transform = 'scale(0.9)';
                        setTimeout(function () {
                            col.remove();
                            updateGalleryCounter();

                            if (delData.new_default_id) {
                                var nextCard = document.querySelector('#photo-col-' + delData.new_default_id + ' .photo-gallery-card');
                                if (nextCard) {
                                    setCardAsActiveDefault(nextCard);
                                }
                                if (delData.new_default_url && previewThumb) {
                                    previewThumb.src = delData.new_default_url;
                                }
                            }
                        }, 250);

                        showGalleryAlert(delData.message || 'Photo removed successfully.', 'success');
                    } else {
                        deleteBtn.disabled = false;
                        showGalleryAlert(delData.message || 'Could not delete photo.', 'danger');
                    }
                } catch (delErr) {
                    console.error('Delete photo error:', delErr);
                    deleteBtn.disabled = false;
                    showGalleryAlert('Network error while deleting photo.', 'danger');
                }
            }
        });
    }
});
</script>
@endpush