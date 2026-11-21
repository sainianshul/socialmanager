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
/* Minimalist Branding Studio */
.bs-preview-wrapper {
    position: sticky;
    top: 85px;
}
.bs-poster-frame {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    background: #0f172a;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
    border: 1px solid rgba(0, 0, 0, 0.08);
}
.bs-poster-canvas {
    width: 100%;
    padding-top: 100%; /* Square 1:1 */
    position: relative;
    background-size: cover;
    background-position: center top;
    transition: background-image 0.2s ease-in-out;
}

/* Cutout Photo Rising on Right */
.bs-cutout-holder {
    position: absolute;
    right: 8px;
    bottom: 24px;
    width: 130px;
    height: 160px;
    z-index: 8;
    pointer-events: none;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
.bs-cutout-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.4));
}

/* Authentic Dual-Tone Indian Political Strip */
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
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.01em;
    line-height: 1.15;
}
.bs-leader-desig-text {
    font-size: 10px;
    font-weight: 700;
    color: #fef08a;
    line-height: 1.1;
    margin-top: 1px;
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
    border-top: 1px solid #e2e8f0;
    z-index: 6;
    overflow: hidden;
}
.bs-social-item {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
}
.bs-thumb-btn {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 2px solid transparent;
    cursor: pointer;
    overflow: hidden;
    padding: 0;
    transition: all 0.15s ease;
}
.bs-thumb-btn.active {
    border-color: #ff5e00;
    box-shadow: 0 0 0 2px rgba(255, 94, 0, 0.25);
}
.bs-thumb-btn img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>
@endpush

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title fw-bold text-dark mb-0" style="font-size: 1.5rem; letter-spacing: -0.02em;">
                    Branding Studio
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.festival-studio.index') }}" class="btn btn-sm btn-outline-primary px-3 py-1.5 rounded-pill shadow-xs">
                    <i class="ti ti-sparkles me-1"></i>Festival Studio
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
        
        {{-- Left Column: Minimal Branding Form --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h4 class="card-title fw-bold text-dark mb-0">Branding Settings</h4>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" id="branding-form">
                        @csrf

                        {{-- Political Party Selector --}}
                        <div class="mb-3.5">
                            <label class="form-label small fw-semibold text-dark mb-1">
                                <i class="ti ti-flag me-1 text-primary"></i>Political Party
                            </label>
                            <select name="party_id" id="input-party-id" class="form-select rounded-3">
                                <option value="" data-name="Independent / निर्दलीय" data-code="" data-color="#ff5e00" data-secondary="#0f172a">
                                    -- None / Independent --
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
                        </div>

                        {{-- Name & Designation --}}
                        <div class="row g-3 mb-3.5">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1">
                                    <i class="ti ti-user me-1 text-primary"></i>Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="display_name" id="input-display-name" class="form-control rounded-3" 
                                       value="{{ old('display_name', $branding->display_name ?? $user->name) }}" 
                                       placeholder="e.g. डॉ. धर्म सिंह सैनी" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1">
                                    <i class="ti ti-medal me-1 text-primary"></i>Designation
                                </label>
                                <input type="text" name="designation" id="input-designation" class="form-control rounded-3" 
                                       value="{{ old('designation', $branding->designation) }}" 
                                       placeholder="e.g. पूर्व मंत्री (उत्तर प्रदेश)">
                            </div>
                        </div>

                        {{-- Choose Image (Clean & Minimal) --}}
                        <div class="mb-3.5">
                            <label class="form-label small fw-semibold text-dark mb-1">
                                <i class="ti ti-photo me-1 text-primary"></i>Choose Image
                            </label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-md rounded-circle border shadow-xs bg-white overflow-hidden flex-shrink-0" style="width: 50px; height: 50px;">
                                    <img id="upload-preview-thumb" src="{{ $currentPhotoUrl }}" alt="Photo" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <input type="file" name="branding_image" id="input-branding-image" class="form-control rounded-3" accept="image/png,image/jpeg,image/webp">
                            </div>
                        </div>

                        {{-- Social Media Handles & Contacts (Optional) --}}
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-dark mb-2">
                                <i class="ti ti-share me-1 text-primary"></i>Social Media & Phone (Optional)
                            </label>
                            
                            <div class="row g-2.5">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-facebook">
                                            <i class="ti ti-brand-facebook fs-3"></i>
                                        </span>
                                        <input type="text" name="facebook_handle" id="input-facebook" class="form-control border-start-0 rounded-end-3 ps-0" 
                                               value="{{ old('facebook_handle', $branding->facebook_handle) }}" 
                                               placeholder="Facebook handle">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-danger">
                                            <i class="ti ti-brand-instagram fs-3"></i>
                                        </span>
                                        <input type="text" name="instagram_handle" id="input-instagram" class="form-control border-start-0 rounded-end-3 ps-0" 
                                               value="{{ old('instagram_handle', $branding->instagram_handle) }}" 
                                               placeholder="Instagram handle">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-dark">
                                            <i class="ti ti-brand-x fs-3"></i>
                                        </span>
                                        <input type="text" name="twitter_handle" id="input-twitter" class="form-control border-start-0 rounded-end-3 ps-0" 
                                               value="{{ old('twitter_handle', $branding->twitter_handle) }}" 
                                               placeholder="Twitter / X handle">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-success">
                                            <i class="ti ti-phone fs-3"></i>
                                        </span>
                                        <input type="text" name="phone_number" id="input-phone" class="form-control border-start-0 rounded-end-3 ps-0" 
                                               value="{{ old('phone_number', $branding->phone_number) }}" 
                                               placeholder="Phone number">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2 border-top text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                                <i class="ti ti-device-floppy me-1 fs-3"></i>Save Branding
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Minimal Live Poster Strip Preview --}}
        <div class="col-lg-5">
            <div class="bs-preview-wrapper">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                        <h4 class="card-title fw-bold text-dark mb-0">Live Preview</h4>
                        <span class="badge bg-primary-lt rounded-pill px-2.5 py-0.5" style="font-size: 11px;">Real-Time</span>
                    </div>

                    <div class="card-body p-3.5 bg-light-subtle">
                        
                        {{-- Clean Simulated Poster Canvas --}}
                        <div class="bs-poster-frame">
                            <div class="bs-poster-canvas" id="preview-poster-canvas" style="background-image: url('{{ asset('images/templates/tulsi-vivah.jpg') }}');">
                                
                                {{-- Cutout Photo Rising on the Right Side --}}
                                <div class="bs-cutout-holder">
                                    <img id="preview-avatar" src="{{ $currentPhotoUrl }}" alt="Cutout" class="bs-cutout-img">
                                </div>

                                {{-- Dual-Tone Political Banner --}}
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

    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inputs
    var nameInput = document.getElementById('input-display-name');
    var desigInput = document.getElementById('input-designation');
    var partySelect = document.getElementById('input-party-id');
    var imageInput = document.getElementById('input-branding-image');

    var fbInput = document.getElementById('input-facebook');
    var instaInput = document.getElementById('input-instagram');
    var twInput = document.getElementById('input-twitter');
    var phoneInput = document.getElementById('input-phone');

    // Previews
    var previewName = document.getElementById('preview-display-name');
    var previewDesig = document.getElementById('preview-designation');
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
    if (nameInput) {
        nameInput.addEventListener('input', function () {
            if (previewName) previewName.innerText = nameInput.value.trim() || 'डॉ. धर्म सिंह सैनी';
        });
    }

    // Live Designation Sync
    if (desigInput) {
        desigInput.addEventListener('input', function () {
            if (previewDesig) previewDesig.innerText = desigInput.value.trim() || 'पूर्व मंत्री (उत्तर प्रदेश)';
        });
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
            previewPartyName.innerText = shortCode ? partyName : 'निर्दलीय / स्वतंत्र';
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

    // Cutout Image Upload Live Preview
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
            previewFb.innerText = fbInput.value.trim() || 'Facebook';
        });
    }
    if (instaInput && previewInsta) {
        instaInput.addEventListener('input', function () {
            previewInsta.innerText = instaInput.value.trim() || 'Instagram';
        });
    }
    if (twInput && previewTw) {
        twInput.addEventListener('input', function () {
            previewTw.innerText = twInput.value.trim() || 'Twitter';
        });
    }
    if (phoneInput && previewPhone) {
        phoneInput.addEventListener('input', function () {
            previewPhone.innerText = phoneInput.value.trim() || 'Phone';
        });
    }

    // Template Switcher
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
