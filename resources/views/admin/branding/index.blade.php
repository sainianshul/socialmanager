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
/* Branding Studio Master Styles */
.bs-preview-wrapper {
    position: sticky;
    top: 85px;
}
.bs-poster-frame {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    background: #0f172a;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.22);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.bs-poster-canvas {
    width: 100%;
    padding-top: 100%; /* Square 1:1 Aspect Ratio */
    position: relative;
    background-size: cover;
    background-position: center top;
    transition: background-image 0.25s ease-in-out;
}

/* Top Leaders Strip */
.bs-leaders-header {
    position: absolute;
    top: 6px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
    padding: 3px 6px;
    z-index: 4;
}
.bs-leader-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid #ff7700;
    background: radial-gradient(circle, #ff9933 0%, #ea580c 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
    overflow: hidden;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
}

/* Cutout Photo Rising on Right */
.bs-cutout-holder {
    position: absolute;
    right: 6px;
    bottom: 24px;
    width: 135px;
    height: 165px;
    z-index: 9;
    pointer-events: none;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
.bs-cutout-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.45));
}

/* Authentic Indian Political Dual-Tone Banner */
.bs-political-banner {
    position: absolute;
    bottom: 24px;
    left: 0;
    right: 0;
    height: 44px;
    display: flex;
    align-items: stretch;
    z-index: 6;
}
.bs-party-green-bar {
    background: #0d6832;
    color: #ffffff;
    padding: 2px 14px 2px 10px;
    display: flex;
    align-items: center;
    gap: 6px;
    clip-path: polygon(0 0, 93% 0, 100% 100%, 0% 100%);
    width: 44%;
    flex-shrink: 0;
    box-shadow: 2px 0 6px rgba(0, 0, 0, 0.2);
}
.bs-party-symbol {
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ff9933;
    font-size: 15px;
    flex-shrink: 0;
}
.bs-party-name-text {
    font-size: 11.5px;
    font-weight: 800;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.1;
}
.bs-leader-orange-bar {
    background: #ff5e00;
    color: #ffffff;
    padding: 3px 12px 3px 14px;
    margin-left: -12px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    z-index: 7;
}
.bs-leader-name-text {
    font-size: 14.5px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.01em;
    line-height: 1.15;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
}
.bs-leader-desig-text {
    font-size: 10px;
    font-weight: 700;
    color: #fef08a; /* Bright yellow Hindi designation */
    line-height: 1.1;
    margin-top: 1px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

/* Business / Personal Banner Variant */
.bs-corporate-banner {
    position: absolute;
    bottom: 24px;
    left: 0;
    right: 0;
    height: 44px;
    background: linear-gradient(90deg, #0f172a 0%, #1e293b 70%, #334155 100%);
    padding: 3px 14px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    z-index: 6;
    border-top: 2px solid #1db8ab;
}

/* White Social Strip at Bottom */
.bs-social-bottom-strip {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 24px;
    background: #ffffff;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 10px;
    font-size: 9px;
    font-weight: 700;
    color: #1e293b;
    border-top: 1px solid #cbd5e1;
    z-index: 6;
    overflow: hidden;
}
.bs-social-item {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
}
.bs-social-item i {
    font-size: 11px;
}
.bs-thumb-btn {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    border: 2px solid transparent;
    cursor: pointer;
    overflow: hidden;
    padding: 0;
    transition: all 0.15s ease;
}
.bs-thumb-btn.active {
    border-color: #ff5e00;
    box-shadow: 0 0 0 2px rgba(255, 94, 0, 0.3);
}
.bs-thumb-btn img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>
@endpush

@section('content')

    {{-- Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title fw-bold text-dark mb-0" style="font-size: 1.55rem; letter-spacing: -0.02em;">
                    Branding Studio
                </h1>
                <div class="text-secondary small mt-0.5">
                    Customize your photo cutout, designation, political party, and social media strip for festival posters.
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.festival-studio.index') }}" class="btn btn-sm btn-outline-primary px-3 py-1.5 rounded-pill shadow-xs">
                    <i class="ti ti-sparkles me-1"></i>Open Festival Studio
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-circle-check fs-2"></i>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-xs mb-3" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        
        {{-- Left Column: Branding Customization Form --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Branding Identity & Strip</h4>
                        <div class="text-secondary small">Automatically embedded on every festival poster generated.</div>
                    </div>
                    <span class="badge bg-teal-lt rounded-pill px-3 py-1">Auto-Applied</span>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" id="branding-form">
                        @csrf

                        {{-- Section 1: Client / Profile Type --}}
                        <div class="mb-3.5">
                            <label class="form-label small fw-semibold text-dark mb-1">
                                <i class="ti ti-category me-1 text-primary"></i>Profile Category
                            </label>
                            <select name="client_type" id="input-client-type" class="form-select rounded-3">
                                <option value="politician" {{ old('client_type', $branding->client_type ?? 'politician') === 'politician' ? 'selected' : '' }}>
                                    Politician / Leader (राजनीतिक दल / जननेता)
                                </option>
                                <option value="business" {{ old('client_type', $branding->client_type) === 'business' ? 'selected' : '' }}>
                                    Business / Enterprise (व्यावसायिक प्रतिष्ठान)
                                </option>
                                <option value="personal" {{ old('client_type', $branding->client_type) === 'personal' ? 'selected' : '' }}>
                                    Creator / Personal Brand (व्यक्तिगत)
                                </option>
                            </select>
                        </div>

                        {{-- Section 2: Political Party Selector (Condition: Politician) --}}
                        <div class="mb-3.5" id="party-select-group">
                            <label class="form-label small fw-semibold text-dark mb-1">
                                <i class="ti ti-flag me-1 text-primary"></i>Political Party (राजनीतिक दल)
                            </label>
                            <select name="party_id" id="input-party-id" class="form-select rounded-3">
                                <option value="" data-name="Independent / निर्दलीय" data-code="" data-color="#ff5e00" data-secondary="#0f172a">
                                    -- None / Independent (निर्दलीय) --
                                </option>
                                @foreach($parties as $party)
                                    <option value="{{ $party->id }}" 
                                            data-name="{{ $party->name }}"
                                            data-code="{{ $party->short_code }}"
                                            data-color="{{ $party->primary_color ?? '#ff5e00' }}"
                                            data-secondary="{{ $party->secondary_color ?? '#0d6832' }}"
                                            {{ old('party_id', $branding->party_id) == $party->id ? 'selected' : '' }}>
                                        {{ $party->name }} ({{ $party->short_code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint small text-muted">Select party to activate party logo, official colors, and Hindi header banner.</div>
                        </div>

                        {{-- Section 3: Name & Designation --}}
                        <div class="row g-3 mb-3.5">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1">
                                    <i class="ti ti-user me-1 text-primary"></i>Leader / Display Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="display_name" id="input-display-name" class="form-control rounded-3" 
                                       value="{{ old('display_name', $branding->display_name ?? $user->name) }}" 
                                       placeholder="e.g. डॉ. धर्म सिंह सैनी" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1">
                                    <i class="ti ti-medal me-1 text-primary"></i>Designation / Title (पद)
                                </label>
                                <input type="text" name="designation" id="input-designation" class="form-control rounded-3" 
                                       value="{{ old('designation', $branding->designation) }}" 
                                       placeholder="e.g. पूर्व मंत्री (उत्तर प्रदेश)">
                            </div>
                        </div>

                        {{-- Section 4: Branding Photo Cutout (Choose File) --}}
                        <div class="mb-4 p-3.5 rounded-3 border bg-light-subtle">
                            <label class="form-label small fw-semibold text-dark mb-1">
                                <i class="ti ti-photo me-1 text-primary"></i>Branding Photo (Photo Cutout / Profile Image)
                            </label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-lg rounded-circle border shadow-xs bg-white overflow-hidden flex-shrink-0" style="width: 72px; height: 72px;">
                                    <img id="upload-preview-thumb" src="{{ $currentPhotoUrl }}" alt="Current Photo" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="branding_image" id="input-branding-image" class="form-control rounded-3" accept="image/png,image/jpeg,image/webp">
                                    <div class="form-hint small text-muted mt-1">
                                        Choose File: Transparent PNG cutout recommended (Max 10 MB). A clear portrait with pagdi/turban or formal attire creates highest impact.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 5: Social Media Handles & Contacts (Footer Strip - Optional) --}}
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-dark mb-2">
                                <i class="ti ti-share me-1 text-primary"></i>Social Media Handles & Contacts (Optional Footer Strip)
                            </label>
                            
                            <div class="row g-2.5">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-facebook">
                                            <i class="ti ti-brand-facebook fs-3"></i>
                                        </span>
                                        <input type="text" name="facebook_handle" id="input-facebook" class="form-control border-start-0 rounded-end-3 ps-0" 
                                               value="{{ old('facebook_handle', $branding->facebook_handle) }}" 
                                               placeholder="Facebook: Dharam.singhsaini">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-danger">
                                            <i class="ti ti-brand-instagram fs-3"></i>
                                        </span>
                                        <input type="text" name="instagram_handle" id="input-instagram" class="form-control border-start-0 rounded-end-3 ps-0" 
                                               value="{{ old('instagram_handle', $branding->instagram_handle) }}" 
                                               placeholder="Instagram: Drdharamsinghsaini">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-dark">
                                            <i class="ti ti-brand-x fs-3"></i>
                                        </span>
                                        <input type="text" name="twitter_handle" id="input-twitter" class="form-control border-start-0 rounded-end-3 ps-0" 
                                               value="{{ old('twitter_handle', $branding->twitter_handle) }}" 
                                               placeholder="Twitter / X: Drdharamsaini">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-success">
                                            <i class="ti ti-phone fs-3"></i>
                                        </span>
                                        <input type="text" name="phone_number" id="input-phone" class="form-control border-start-0 rounded-end-3 ps-0" 
                                               value="{{ old('phone_number', $branding->phone_number) }}" 
                                               placeholder="Mobile: +91 98765 43210">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="text-secondary small">
                                <i class="ti ti-check text-success me-1"></i>Saved profile updates across all future posters.
                            </span>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                                <i class="ti ti-device-floppy me-1 fs-3"></i>Save Branding Settings
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Live Poster Strip Preview --}}
        <div class="col-lg-5">
            <div class="bs-preview-wrapper">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                        <h4 class="card-title fw-bold text-dark mb-0">Live Poster Strip Preview</h4>
                        <span class="badge bg-primary-lt rounded-pill px-2.5 py-0.5" style="font-size: 11px;">Real-Time</span>
                    </div>

                    <div class="card-body p-3.5 bg-light-subtle">
                        
                        {{-- Simulated Poster with Authentic Netaji Bottom Strip --}}
                        <div class="bs-poster-frame">
                            <div class="bs-poster-canvas" id="preview-poster-canvas" style="background-image: url('{{ asset('images/templates/tulsi-vivah.jpg') }}');">
                                
                                {{-- Top Senior Leaders Strip (Visible when Politician is selected) --}}
                                <div class="bs-leaders-header" id="preview-leaders-header">
                                    <div class="bs-leader-circle" title="Leader 1">PM</div>
                                    <div class="bs-leader-circle" title="Leader 2">HM</div>
                                    <div class="bs-leader-circle" title="Leader 3">JP</div>
                                    <div class="bs-leader-circle" title="Leader 4">CM</div>
                                    <div class="bs-leader-circle" title="Leader 5">DY</div>
                                    <div class="bs-leader-circle" title="Leader 6">MP</div>
                                    <div class="bs-leader-circle" title="Leader 7">MLA</div>
                                </div>

                                {{-- Cutout Photo Rising on the Right Side --}}
                                <div class="bs-cutout-holder">
                                    <img id="preview-avatar" src="{{ $currentPhotoUrl }}" alt="Cutout" class="bs-cutout-img">
                                </div>

                                {{-- Politician Dual-Tone Banner (Party Green Bar + Leader Saffron Bar) --}}
                                <div class="bs-political-banner" id="preview-political-banner">
                                    <div class="bs-party-green-bar" id="preview-party-bar">
                                        <div class="bs-party-symbol">
                                            <i class="ti ti-flower"></i>
                                        </div>
                                        <div class="bs-party-name-text" id="preview-party-name">
                                            {{ $branding->party ? $branding->party->name : 'भारतीय जनता पार्टी' }}
                                        </div>
                                    </div>
                                    <div class="bs-leader-orange-bar" id="preview-leader-bar">
                                        <div class="bs-leader-name-text" id="preview-display-name">
                                            {{ $branding->display_name ?: 'डॉ. धर्म सिंह सैनी' }}
                                        </div>
                                        <div class="bs-leader-desig-text" id="preview-designation">
                                            {{ $branding->designation ?: 'पूर्व मंत्री (उत्तर प्रदेश)' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Corporate / Personal Banner (Hidden when Politician) --}}
                                <div class="bs-corporate-banner d-none" id="preview-corporate-banner">
                                    <div class="bs-leader-name-text text-white" id="preview-corp-name">
                                        {{ $branding->display_name ?: 'Company / Leader Name' }}
                                    </div>
                                    <div class="bs-leader-desig-text text-info" id="preview-corp-desig">
                                        {{ $branding->designation ?: 'Founder & Director' }}
                                    </div>
                                </div>

                                {{-- White Social Strip at Bottom --}}
                                <div class="bs-social-bottom-strip" id="preview-socials-strip">
                                    <span class="bs-social-item" id="preview-fb-item">
                                        <i class="ti ti-brand-facebook text-primary"></i>
                                        <span id="preview-fb-text">{{ $branding->facebook_handle ?: 'Dharam.singhsaini' }}</span>
                                    </span>
                                    <span class="bs-social-item" id="preview-insta-item">
                                        <i class="ti ti-brand-instagram text-danger"></i>
                                        <span id="preview-insta-text">{{ $branding->instagram_handle ?: 'Drdharamsinghsaini' }}</span>
                                    </span>
                                    <span class="bs-social-item" id="preview-tw-item">
                                        <i class="ti ti-brand-x text-dark"></i>
                                        <span id="preview-tw-text">{{ $branding->twitter_handle ?: 'Drdharamsaini' }}</span>
                                    </span>
                                    <span class="bs-social-item" id="preview-phone-item">
                                        <i class="ti ti-phone text-success"></i>
                                        <span id="preview-phone-text">{{ $branding->phone_number ?: '+91 98765 43210' }}</span>
                                    </span>
                                </div>

                            </div>
                        </div>

                        {{-- Template Switcher Thumbnails --}}
                        <div class="mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <span class="small fw-semibold text-dark">Preview on festival template:</span>
                                <span class="text-secondary small" style="font-size: 11px;">Click to switch background</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="bs-thumb-btn active" data-bg="{{ asset('images/templates/tulsi-vivah.jpg') }}" title="Tulsi Vivah">
                                    <img src="{{ asset('images/templates/tulsi-vivah.jpg') }}" alt="Tulsi Vivah">
                                </button>
                                <button type="button" class="bs-thumb-btn" data-bg="{{ asset('images/templates/janjatiya-gaurav-diwas-birsa-munda.jpg') }}" title="Birsa Munda">
                                    <img src="{{ asset('images/templates/janjatiya-gaurav-diwas-birsa-munda.jpg') }}" alt="Birsa Munda">
                                </button>
                                <button type="button" class="bs-thumb-btn" data-bg="{{ asset('images/templates/milkha-singh-jayanti.jpg') }}" title="Milkha Singh">
                                    <img src="{{ asset('images/templates/milkha-singh-jayanti.jpg') }}" alt="Milkha Singh">
                                </button>
                                <button type="button" class="bs-thumb-btn" data-bg="{{ asset('images/templates/anandiben-patel-jayanti.jpg') }}" title="Anandiben Patel">
                                    <img src="{{ asset('images/templates/anandiben-patel-jayanti.jpg') }}" alt="Anandiben Patel">
                                </button>
                            </div>
                        </div>

                        <div class="text-secondary small mt-2.5 text-center" style="font-size: 11.5px;">
                            <i class="ti ti-info-circle me-1 text-primary"></i>This footer strip will automatically be embedded on every festival poster.
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Input elements
    var nameInput = document.getElementById('input-display-name');
    var desigInput = document.getElementById('input-designation');
    var clientTypeSelect = document.getElementById('input-client-type');
    var partySelect = document.getElementById('input-party-id');
    var partyGroup = document.getElementById('party-select-group');
    var imageInput = document.getElementById('input-branding-image');

    var fbInput = document.getElementById('input-facebook');
    var instaInput = document.getElementById('input-instagram');
    var twInput = document.getElementById('input-twitter');
    var phoneInput = document.getElementById('input-phone');

    // Preview elements
    var previewName = document.getElementById('preview-display-name');
    var previewDesig = document.getElementById('preview-designation');
    var previewCorpName = document.getElementById('preview-corp-name');
    var previewCorpDesig = document.getElementById('preview-corp-desig');
    
    var previewPoliticalBanner = document.getElementById('preview-political-banner');
    var previewCorporateBanner = document.getElementById('preview-corporate-banner');
    var previewLeadersHeader = document.getElementById('preview-leaders-header');
    var previewPartyBar = document.getElementById('preview-party-bar');
    var previewLeaderBar = document.getElementById('preview-leader-bar');
    var previewPartyName = document.getElementById('preview-party-name');

    var previewAvatar = document.getElementById('preview-avatar');
    var previewThumb = document.getElementById('upload-preview-thumb');
    var posterCanvas = document.getElementById('preview-poster-canvas');

    var previewFb = document.getElementById('preview-fb-text');
    var previewInsta = document.getElementById('preview-insta-text');
    var previewTw = document.getElementById('preview-tw-text');
    var previewPhone = document.getElementById('preview-phone-text');

    // Live Name Sync
    function syncName() {
        var val = (nameInput && nameInput.value.trim()) || 'डॉ. धर्म सिंह सैनी';
        if (previewName) previewName.innerText = val;
        if (previewCorpName) previewCorpName.innerText = val;
    }
    if (nameInput) nameInput.addEventListener('input', syncName);

    // Live Designation Sync
    function syncDesig() {
        var val = (desigInput && desigInput.value.trim()) || 'पूर्व मंत्री (उत्तर प्रदेश)';
        if (previewDesig) previewDesig.innerText = val;
        if (previewCorpDesig) previewCorpDesig.innerText = val;
    }
    if (desigInput) desigInput.addEventListener('input', syncDesig);

    // Live Category (Client Type) Toggle
    function checkClientType() {
        if (!clientTypeSelect) return;
        var type = clientTypeSelect.value;
        if (type === 'politician') {
            if (partyGroup) partyGroup.classList.remove('d-none');
            if (previewPoliticalBanner) previewPoliticalBanner.classList.remove('d-none');
            if (previewCorporateBanner) previewCorporateBanner.classList.add('d-none');
            if (previewLeadersHeader) previewLeadersHeader.classList.remove('d-none');
            updatePartyTheme();
        } else {
            if (partyGroup) partyGroup.classList.add('d-none');
            if (previewPoliticalBanner) previewPoliticalBanner.classList.add('d-none');
            if (previewCorporateBanner) previewCorporateBanner.classList.remove('d-none');
            if (previewLeadersHeader) previewLeadersHeader.classList.add('d-none');
        }
    }
    if (clientTypeSelect) {
        clientTypeSelect.addEventListener('change', checkClientType);
        checkClientType();
    }

    // Live Party Theme Colors
    function updatePartyTheme() {
        if (!partySelect) return;
        var opt = partySelect.options[partySelect.selectedIndex];
        if (!opt) return;

        var partyName = opt.getAttribute('data-name') || 'भारतीय जनता पार्टी';
        var primaryColor = opt.getAttribute('data-color') || '#ff5e00';
        var secondaryColor = opt.getAttribute('data-secondary') || '#0d6832';
        var shortCode = opt.getAttribute('data-code') || '';

        if (previewPartyName) {
            previewPartyName.innerText = shortCode ? (partyName.indexOf('(') !== -1 ? partyName : partyName) : 'निर्दलीय / स्वतंत्र';
        }
        if (previewLeaderBar) {
            previewLeaderBar.style.backgroundColor = primaryColor;
        }
        if (previewPartyBar) {
            previewPartyBar.style.backgroundColor = secondaryColor;
        }
    }
    if (partySelect) {
        partySelect.addEventListener('change', updatePartyTheme);
        updatePartyTheme();
    }

    // Cutout / Profile Image Upload Live Preview
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            if (imageInput.files && imageInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    if (previewAvatar) previewAvatar.src = e.target.result;
                    if (previewThumb) previewThumb.src = e.target.result;
                };
                reader.readAsDataURL(imageInput.files[0]);
            }
        });
    }

    // Live Social Handles Sync
    if (fbInput && previewFb) {
        fbInput.addEventListener('input', function () {
            previewFb.innerText = fbInput.value.trim() || 'Dharam.singhsaini';
        });
    }
    if (instaInput && previewInsta) {
        instaInput.addEventListener('input', function () {
            previewInsta.innerText = instaInput.value.trim() || 'Drdharamsinghsaini';
        });
    }
    if (twInput && previewTw) {
        twInput.addEventListener('input', function () {
            previewTw.innerText = twInput.value.trim() || 'Drdharamsaini';
        });
    }
    if (phoneInput && previewPhone) {
        phoneInput.addEventListener('input', function () {
            previewPhone.innerText = phoneInput.value.trim() || '+91 98765 43210';
        });
    }

    // Festival Template Switcher
    var thumbBtns = document.querySelectorAll('.bs-thumb-btn');
    thumbBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            thumbBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            var bgUrl = btn.getAttribute('data-bg');
            if (posterCanvas && bgUrl) {
                posterCanvas.style.backgroundImage = "url('" + bgUrl + "')";
            }
        });
    });
});
</script>
@endpush
