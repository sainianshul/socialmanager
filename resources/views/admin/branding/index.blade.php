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
}
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
    padding: 0.55rem 0.95rem;
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
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    object-fit: cover;
    background: #ffffff;
    flex-shrink: 0;
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

    {{-- Main Container with Proper Margin & Centered Proportions --}}
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-9 col-md-11">
            
            <div class="card bs-card border-0 mb-5">
                <div class="card-header bg-white py-3.5 px-4 px-md-5 border-bottom">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <h4 class="card-title fw-bold text-dark mb-0" style="font-size: 1.2rem;">
                            <i class="ti ti-id me-2 text-primary"></i>Branding Settings
                        </h4>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" id="branding-form">
                        @csrf

                        {{-- Section 1: Political Party with Image & Name --}}
                        <div class="mb-4">
                            <label class="bs-label">
                                <i class="ti ti-flag text-primary"></i>Political Party
                            </label>

                            @php
                                $selectedPartyId = old('party_id', $branding->party_id);
                                $selectedParty = $parties->firstWhere('id', $selectedPartyId);
                            @endphp

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

                                {{-- Hidden input submitted with the form --}}
                                <input type="hidden" name="party_id" id="input-party-id" value="{{ $selectedPartyId }}">
                            </div>
                        </div>

                        {{-- Section 2: Name & Designation --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="bs-label">
                                    <i class="ti ti-user text-primary"></i>Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="display_name" id="input-display-name" class="form-control bs-input" 
                                       value="{{ old('display_name', $branding->display_name ?? $user->name) }}" 
                                       placeholder="e.g. डॉ. धर्म सिंह सैनी" required>
                            </div>
                            <div class="col-md-6">
                                <label class="bs-label">
                                    <i class="ti ti-medal text-primary"></i>Designation
                                </label>
                                <input type="text" name="designation" id="input-designation" class="form-control bs-input" 
                                       value="{{ old('designation', $branding->designation) }}" 
                                       placeholder="e.g. पूर्व मंत्री (उत्तर प्रदेश)">
                            </div>
                        </div>

                        {{-- Section 3: Choose Image --}}
                        <div class="mb-4 p-3.5 rounded-3 border bg-light-subtle">
                            <label class="bs-label mb-2">
                                <i class="ti ti-photo text-primary"></i>Choose Image
                            </label>
                            <div class="d-flex align-items-center gap-3">
                                <img id="upload-preview-thumb" src="{{ $currentPhotoUrl }}" alt="Photo" class="bs-upload-thumb">
                                <input type="file" name="branding_image" id="input-branding-image" class="form-control bs-input" accept="image/png,image/jpeg,image/webp">
                            </div>
                        </div>

                        {{-- Section 4: Social Media & Contacts (Optional) --}}
                        <div class="mb-4.5">
                            <label class="bs-label mb-2.5">
                                <i class="ti ti-share text-primary"></i>Social Media & Phone (Optional)
                            </label>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-facebook">
                                            <i class="ti ti-brand-facebook fs-3"></i>
                                        </span>
                                        <input type="text" name="facebook_handle" id="input-facebook" class="form-control bs-input border-start-0 ps-0" 
                                               value="{{ old('facebook_handle', $branding->facebook_handle) }}" 
                                               placeholder="Facebook handle">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-danger">
                                            <i class="ti ti-brand-instagram fs-3"></i>
                                        </span>
                                        <input type="text" name="instagram_handle" id="input-instagram" class="form-control bs-input border-start-0 ps-0" 
                                               value="{{ old('instagram_handle', $branding->instagram_handle) }}" 
                                               placeholder="Instagram handle">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-dark">
                                            <i class="ti ti-brand-x fs-3"></i>
                                        </span>
                                        <input type="text" name="twitter_handle" id="input-twitter" class="form-control bs-input border-start-0 ps-0" 
                                               value="{{ old('twitter_handle', $branding->twitter_handle) }}" 
                                               placeholder="Twitter / X handle">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-success">
                                            <i class="ti ti-phone fs-3"></i>
                                        </span>
                                        <input type="text" name="phone_number" id="input-phone" class="form-control bs-input border-start-0 ps-0" 
                                               value="{{ old('phone_number', $branding->phone_number) }}" 
                                               placeholder="Phone number">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 5: Save Button with Proper Margin --}}
                        <div class="pt-4 border-top text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm">
                                <i class="ti ti-device-floppy me-1.5 fs-2"></i>Save Branding
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    {{--
    ======================================================================
    LIVE PREVIEW POSTER (COMMENTED OUT AS PER USER REQUEST)
    ======================================================================
    <div class="col-lg-5">
        <div class="bs-preview-wrapper">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h4 class="card-title fw-bold text-dark mb-0">Live Preview</h4>
                    <span class="badge bg-primary-lt rounded-pill px-2.5 py-0.5" style="font-size: 11px;">Real-Time</span>
                </div>

                <div class="card-body p-3.5 bg-light-subtle">
                    
                    <!-- Clean Simulated Poster Canvas -->
                    <div class="bs-poster-frame">
                        <div class="bs-poster-canvas" id="preview-poster-canvas" style="background-image: url('{{ asset('images/templates/tulsi-vivah.jpg') }}');">
                            
                            <!-- Cutout Photo Rising on the Right Side -->
                            <div class="bs-cutout-holder">
                                <img id="preview-avatar" src="{{ $currentPhotoUrl }}" alt="Cutout" class="bs-cutout-img">
                            </div>

                            <!-- Dual-Tone Political Banner -->
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

                            <!-- White Social Strip at Bottom -->
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

                    <!-- Template Switcher Thumbnails -->
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <span class="small fw-semibold text-secondary">Templates:</span>
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

                </div>
            </div>
        </div>
    </div>
    ======================================================================
    --}}

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    // Image Upload Preview
    var imageInput = document.getElementById('input-branding-image');
    var previewThumb = document.getElementById('upload-preview-thumb');

    if (imageInput && previewThumb) {
        imageInput.addEventListener('change', function () {
            if (imageInput.files && imageInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    previewThumb.src = e.target.result;
                };
                reader.readAsDataURL(imageInput.files[0]);
            }
        });
    }
});
</script>
@endpush
