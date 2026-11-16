@extends('admin.layouts.app')

@section('title', 'Social Accounts')

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Connected Accounts</h2>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#connect-account-modal">
                    <i class="ti ti-plus me-1"></i>Connect Account
                </button>
            </div>
        </div>
    </div>

    @if($totalAccounts === 0)
        {{-- Formal Clean Empty State --}}
        <div class="card p-5 text-center my-3">
            <div class="card-body py-4">
                <div class="avatar avatar-xl rounded-circle bg-primary-lt mx-auto mb-3">
                    <i class="ti ti-brand-meta fs-1 text-primary"></i>
                </div>
                <h3 class="fw-bold mb-4">No Accounts Connected</h3>
                <div>
                    <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#connect-account-modal">
                        <i class="ti ti-plus me-1"></i>Connect Account
                    </button>
                </div>
            </div>
        </div>
    @else
        {{-- Professional Full-Width Channel Hub --}}
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="card-title fw-bold m-0 text-dark">Connected Channels</h3>
                    <span class="badge badge-outline text-secondary fw-semibold">{{ $accounts->total() }}</span>
                </div>
                
                {{-- Platform Filter Select (Right Side) --}}
                <div class="d-flex align-items-center">
                    <select id="filter-platform" class="form-select" style="min-width: 185px;">
                        <option value="" {{ !request('platform') ? 'selected' : '' }}>
                            All Channels ({{ $totalAccounts }})
                        </option>
                        <option value="{{ \App\Models\Account::TYPE_FACEBOOK }}" {{ request('platform') == \App\Models\Account::TYPE_FACEBOOK ? 'selected' : '' }}>
                            Facebook ({{ $fbCount }})
                        </option>
                        <option value="{{ \App\Models\Account::TYPE_INSTAGRAM }}" {{ request('platform') == \App\Models\Account::TYPE_INSTAGRAM ? 'selected' : '' }}>
                            Instagram ({{ $igCount }})
                        </option>
                        <option value="{{ \App\Models\Account::TYPE_YOUTUBE }}" {{ request('platform') == \App\Models\Account::TYPE_YOUTUBE ? 'selected' : '' }}>
                            YouTube ({{ $ytCount }})
                        </option>
                    </select>
                </div>
            </div>

            {{-- Full-Width Modern Row Cards --}}
            <div class="list-group list-group-flush">
                @forelse($accounts as $index => $account)
                    <div class="list-group-item p-3 p-md-4 transition-all" style="border-bottom: 1px solid #ebedf2;">
                        <div class="row align-items-center g-3">
                            {{-- Profile with Corner Platform Badge + Channel Name (Clean Handle, No Raw ID) --}}
                            <div class="col-12 col-md-5 col-xl-5">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative flex-shrink-0">
                                        @if($account->profile_pic_url)
                                            <span class="avatar avatar-lg rounded-circle border shadow-xs" style="background-image: url('{{ $account->profile_pic_url }}'); width: 48px; height: 48px;"></span>
                                        @else
                                            <span class="avatar avatar-lg rounded-circle bg-light text-dark fw-bold border shadow-xs" style="width: 48px; height: 48px; font-size: 1.15rem;">
                                                {{ strtoupper(substr($account->name, 0, 1)) }}
                                            </span>
                                        @endif
                                        <span class="position-absolute bottom-0 end-0 badge rounded-circle bg-white shadow-xs p-1 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; border: 1.5px solid #ffffff;">
                                            <i class="{{ $account->platform_icon }} text-{{ $account->platform_color }}" style="font-size: 0.75rem;"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                            <span class="fw-bold fs-3 text-dark text-truncate">{{ $account->name }}</span>
                                            <span class="badge badge-outline text-{{ $account->platform_color }} fw-semibold fs-7 py-0.5 px-2">
                                                {{ $account->account_type_name }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 text-secondary fs-7">
                                            @if($account->username)
                                                <span>{{ '@' . $account->username }}</span>
                                            @else
                                                <span class="text-muted">{{ $account->category ?: 'Connected Channel' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Audience (Followers / Subscribers) & Last Synced --}}
                            <div class="col-6 col-md-3 col-xl-3">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <span class="h3 fw-bold mb-0 text-dark">
                                            {{ number_format($account->isYouTube() ? $account->subscriber_count : $account->followers_count) }}
                                        </span>
                                        <span class="text-secondary small">
                                            {{ $account->isYouTube() ? 'Subscribers' : 'Followers' }}
                                        </span>
                                    </div>
                                    <div class="text-muted small fs-7 mt-0.5">
                                        <i class="ti ti-clock-hour-4 me-1"></i>Synced {{ $account->last_synced_at ? $account->last_synced_at->diffForHumans() : 'recently' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Status Badge (Clean, No Blink) --}}
                            <div class="col-6 col-md-2 col-xl-2">
                                <div class="d-flex flex-column align-items-start gap-1">
                                    @if($account->is_active)
                                        <span class="badge badge-outline text-success d-inline-flex align-items-center py-1 px-2.5">
                                            <span class="status-dot bg-success me-1.5" style="width: 7px; height: 7px;"></span>Active
                                        </span>
                                    @else
                                        <span class="badge badge-outline text-secondary d-inline-flex align-items-center py-1 px-2.5">
                                            <span class="status-dot bg-secondary me-1.5" style="width: 7px; height: 7px;"></span>Paused
                                        </span>
                                    @endif

                                    @if($account->isTokenExpired())
                                        <span class="badge badge-outline text-danger d-inline-flex align-items-center gap-1 py-0.5 px-2 fs-7" title="Connection token expired. Re-authenticate to resume syncing.">
                                            <i class="ti ti-alert-circle"></i>Token Expired
                                        </span>
                                    @elseif($account->isTokenExpiringSoon())
                                        <span class="badge badge-outline text-warning d-inline-flex align-items-center gap-1 py-0.5 px-2 fs-7" title="Token expires soon. Please re-authenticate.">
                                            <i class="ti ti-clock-pause"></i>Expires in {{ max(1, (int)$account->token_expires_at->diffInDays(now())) }}d
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions: Configure Button + Three-Dots (⋮) Menu --}}
                            <div class="col-12 col-md-2 col-xl-2 text-md-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    {{-- Primary Action: Configure --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-settings px-3 d-inline-flex align-items-center gap-1"
                                        data-id="{{ $account->id }}"
                                        data-name="{{ $account->name }}"
                                        data-tone="{{ $account->ai_tone }}"
                                        data-language="{{ $account->ai_language }}"
                                        data-autoreply="{{ $account->auto_reply_mode }}"
                                        data-autohide="{{ $account->auto_hide_trolls ? 1 : 0 }}"
                                        data-commenttodm="{{ $account->comment_to_dm ? 1 : 0 }}"
                                        style="height: 34px;">
                                        <i class="ti ti-adjustments fs-3"></i>Configure
                                    </button>

                                    {{-- Secondary Actions: Three Dots Menu --}}
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon btn-ghost-secondary border" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 34px; height: 34px;" title="Channel Options">
                                            <i class="ti ti-dots-vertical fs-3"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow-sm border py-1" style="min-width: 195px;">
                                            {{-- Re-sync --}}
                                            <form method="POST" action="{{ route('admin.accounts.sync', $account->id) }}" class="m-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2">
                                                    <i class="ti ti-refresh fs-3"></i>
                                                    <span>Re-sync Channel</span>
                                                </button>
                                            </form>

                                            {{-- External Link --}}
                                            @php
                                                $externalUrl = $account->isYouTube()
                                                    ? 'https://youtube.com/channel/' . ($account->youtube_channel_id ?: $account->meta_account_id)
                                                    : ($account->isFacebook()
                                                        ? 'https://facebook.com/' . $account->meta_account_id
                                                        : ($account->username ? 'https://instagram.com/' . $account->username : null));
                                            @endphp
                                            @if($externalUrl)
                                                <a href="{{ $externalUrl }}" target="_blank" class="dropdown-item d-flex align-items-center gap-2 py-2">
                                                    <i class="ti ti-external-link fs-3"></i>
                                                    <span>Open on {{ $account->account_type_name }}</span>
                                                </a>
                                            @endif

                                            {{-- Pause / Activate --}}
                                            <form method="POST" action="{{ route('admin.accounts.toggle-status', $account->id) }}" class="m-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2">
                                                    <i class="ti ti-power fs-3"></i>
                                                    <span>{{ $account->is_active ? 'Pause Channel' : 'Activate Channel' }}</span>
                                                </button>
                                            </form>

                                            <div class="dropdown-divider my-1"></div>

                                            {{-- Disconnect --}}
                                            <form method="POST" action="{{ route('admin.accounts.destroy', $account->id) }}" class="m-0 disconnect-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger btn-disconnect" data-name="{{ $account->name }}">
                                                    <i class="ti ti-trash fs-3"></i>
                                                    <span>Disconnect Channel</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-muted">
                        <div class="avatar avatar-lg rounded-circle bg-light text-secondary mx-auto mb-3">
                            <i class="ti ti-plug-off fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-1 text-dark">No Channels Found</h4>
                        <p class="text-secondary small mb-3">No connected accounts match the selected platform filter.</p>
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-refresh me-1"></i>View All Channels
                        </a>
                    </div>
                @endforelse
            </div>

            @if($accounts->hasPages())
                <div class="card-footer d-flex align-items-center py-3 px-4">
                    {{ $accounts->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- Connect Account Modal --}}
    <div class="modal modal-blur fade" id="connect-account-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="ti ti-plug-connected text-primary"></i>
                        Connect Social Media Channel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group list-group-flush border rounded-3 mb-0">
                        {{-- Facebook Option --}}
                        <div class="list-group-item d-flex align-items-center justify-content-between p-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="avatar avatar-md bg-blue text-white rounded-3">
                                    <i class="ti ti-brand-facebook fs-1"></i>
                                </span>
                                <div>
                                    <div class="fw-bold fs-3 text-dark">Facebook Page</div>
                                    <div class="text-secondary small">Manage posts, engagement & auto-replies</div>
                                </div>
                            </div>
                            <a href="{{ route('facebook.redirect') }}" class="btn btn-outline-primary px-3">
                                Connect
                            </a>
                        </div>

                        {{-- Instagram Option --}}
                        <div class="list-group-item d-flex align-items-center justify-content-between p-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="avatar avatar-md bg-pink text-white rounded-3">
                                    <i class="ti ti-brand-instagram fs-1"></i>
                                </span>
                                <div>
                                    <div class="fw-bold fs-3 text-dark">Instagram Business</div>
                                    <div class="text-secondary small">Manage comments, direct messages & engagement</div>
                                </div>
                            </div>
                            <a href="{{ route('facebook.redirect', ['connect' => 'instagram']) }}" class="btn btn-outline-pink px-3">
                                Connect
                            </a>
                        </div>

                        {{-- YouTube Option --}}
                        <div class="list-group-item d-flex align-items-center justify-content-between p-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="avatar avatar-md bg-red text-white rounded-3">
                                    <i class="ti ti-brand-youtube fs-1"></i>
                                </span>
                                <div>
                                    <div class="fw-bold fs-3 text-dark">YouTube Channel</div>
                                    <div class="text-secondary small">Manage videos, community comments & video analytics</div>
                                </div>
                            </div>
                            <a href="{{ Route::has('youtube.redirect') ? route('youtube.redirect') : url('/auth/youtube/redirect') }}" class="btn btn-outline-danger px-3">
                                Connect
                            </a>
                        </div>
                    </div>

                    {{-- Clean Learn More Footer --}}
                    <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top px-1">
                        <span class="text-secondary small">Need help connecting your accounts?</span>
                        <a href="#" class="small fw-semibold text-primary text-decoration-none d-inline-flex align-items-center gap-1">
                            Learn more <i class="ti ti-arrow-up-right fs-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modern Right-Side Settings Drawer (Offcanvas) --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="settings-offcanvas" aria-labelledby="settings-offcanvas-label" style="width: 440px;">
        <form id="settings-form" method="POST" action="" class="d-flex flex-column h-100 mb-0">
            @csrf
            <div class="offcanvas-header border-bottom py-3 px-4">
                <div>
                    <h4 class="offcanvas-title fw-bold mb-0 text-dark" id="settings-offcanvas-label">Channel Automation</h4>
                    <div class="text-secondary small" id="offcanvas-account-name"></div>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body p-4" style="overflow-y: auto;">
                {{-- AI Personality Section --}}
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-3">AI Communication Style</h5>
                    
                    {{-- Tone Selection --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-1">
                            Reply Tone
                            <i class="ti ti-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Tone used by AI when formulating comment replies"></i>
                        </label>
                        <select name="ai_tone" id="offcanvas-ai-tone" class="form-select" required>
                            @foreach($aiTones as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Language Selection --}}
                    <div>
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-1">
                            Language Preference
                            <i class="ti ti-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Preferred language dialect for citizen responses"></i>
                        </label>
                        <select name="ai_language" id="offcanvas-ai-language" class="form-select" required>
                            @foreach($aiLanguages as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="hr-text text-uppercase text-muted my-4">Automation Rules</div>

                {{-- Auto Comment Mode (Select Dropdown) --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted text-uppercase mb-1">
                        Auto Comment Mode
                        <i class="ti ti-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Off: No AI replies | With Approval: AI creates draft for review | Auto Approved: AI instantly replies live to public"></i>
                    </label>
                    <select name="auto_reply_mode" id="offcanvas-auto-reply" class="form-select" required>
                        <option value="0">Auto comment off</option>
                        <option value="1">Auto Comment With approved</option>
                        <option value="2">Auto comment and Auto approved</option>
                    </select>
                </div>

                {{-- Auto-Hide Abusive Comments --}}
                <div class="card card-sm border bg-light-subtle mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <label for="offcanvas-auto-hide" class="form-check-label fw-semibold text-dark d-block mb-0 cursor-pointer">
                                    Auto-Hide Abusive Comments
                                    <i class="ti ti-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Automatically detects hate speech or abusive remarks and hides them immediately from public view"></i>
                                </label>
                                <span class="text-secondary small">Hides hate speech & abusive comments immediately</span>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="auto_hide_trolls" id="offcanvas-auto-hide" value="1">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Comment to DM --}}
                <div class="card card-sm border bg-light-subtle">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <label for="offcanvas-comment-to-dm" class="form-check-label fw-semibold text-dark d-block mb-0 cursor-pointer">
                                    Comment to Direct Message (DM)
                                    <i class="ti ti-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Sends an automated private direct message to constituents who comment on your posts"></i>
                                </label>
                                <span class="text-secondary small">Sends private DM to commenters</span>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="comment_to_dm" id="offcanvas-comment-to-dm" value="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="offcanvas-footer border-top p-3 bg-white mt-auto d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-ghost-secondary px-3" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                    <i class="ti ti-check"></i>Save Settings
                </button>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    $(function () {
        // Clean Facebook OAuth redirect hash from URL bar
        if (window.location.hash === '#_=_') {
            if (history.replaceState) {
                history.replaceState(null, null, window.location.href.split('#')[0]);
            } else {
                window.location.hash = '';
            }
        }

        // Platform Filter change
        $('#filter-platform').on('change', function () {
            let platform = $(this).val();
            let url = new URL(window.location.href);
            if (platform) {
                url.searchParams.set('platform', platform);
            } else {
                url.searchParams.delete('platform');
            }
            url.searchParams.delete('page');
            window.location.href = url.pathname + url.search;
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover' });

        // Open Settings Drawer (Offcanvas) with existing account config
        $('.btn-settings').on('click', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let tone = $(this).data('tone');
            let language = $(this).data('language');
            let autoReply = $(this).data('autoreply');
            let autoHide = $(this).data('autohide') == 1;
            let commentToDm = $(this).data('commenttodm') == 1;

            $('#offcanvas-account-name').text(name);
            $('#offcanvas-ai-tone').val(tone);
            $('#offcanvas-ai-language').val(language);
            $('#offcanvas-auto-reply').val(autoReply !== undefined ? autoReply : 1);
            $('#offcanvas-auto-hide').prop('checked', autoHide);
            $('#offcanvas-comment-to-dm').prop('checked', commentToDm);

            $('#settings-form').attr('action', '/admin/accounts/' + id + '/settings');

            const offcanvasEl = document.getElementById('settings-offcanvas');
            if (offcanvasEl) {
                const offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                offcanvasInstance.show();
            }
        });

        // Disconnect confirmation
        $('.btn-disconnect').on('click', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Disconnect ' + name + '?',
                text: 'This will stop AI auto-replies and webhook event monitoring for this account.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d63939',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Disconnect',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
