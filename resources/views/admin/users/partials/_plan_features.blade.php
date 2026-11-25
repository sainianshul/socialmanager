@php
    $hasOld = session()->hasOldInput();
    $featureToggles = [
        [
            'name' => 'can_schedule_posts',
            'label' => 'Post Scheduling',
            'hint' => 'Multi-account scheduling & publishing',
            'default' => $user->can_schedule_posts ?? true,
        ],
        [
            'name' => 'can_use_festival_studio',
            'label' => 'Festival Studio',
            'hint' => 'Branded festival posters & templates',
            'default' => $user->can_use_festival_studio ?? true,
        ],
        [
            'name' => 'can_use_auto_dm',
            'label' => 'Auto DM',
            'hint' => 'Comment-to-DM automated reply',
            'default' => $user->can_use_auto_dm ?? true,
        ],
        [
            'name' => 'can_use_ai',
            'label' => 'AI Features',
            'hint' => 'Sentiment & caption generators',
            'default' => $user->can_use_ai ?? true,
        ],
    ];
    $quotaValue = $hasOld ? old('ai_token_quota') : (isset($user) ? $user->ai_token_quota : 100000);
@endphp

{{-- PLAN FEATURES & AI TOKEN QUOTA --}}
<div class="col-12 mt-4">
    <div class="card bg-light-lt border shadow-none">
        <div class="card-header bg-transparent py-2.5">
            <h4 class="card-title text-dark mb-0 d-flex align-items-center">
                <i class="ti ti-toggle-right me-2 text-primary fs-3"></i>Plan Features & AI Quota
            </h4>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-center">
                @foreach($featureToggles as $toggle)
                    @php
                        $isChecked = $hasOld ? (bool) old($toggle['name']) : (bool) $toggle['default'];
                    @endphp
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="d-flex align-items-center">
                            <label class="form-check form-switch cursor-pointer mb-0">
                                <input class="form-check-input" type="checkbox" name="{{ $toggle['name'] }}" value="1" {{ $isChecked ? 'checked' : '' }}>
                                <span class="form-check-label fw-bold text-dark">{{ $toggle['label'] }}</span>
                            </label>
                            <i class="ti ti-info-circle text-muted ms-1 cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $toggle['hint'] }}"></i>
                        </div>
                    </div>
                @endforeach

                <div class="col-12 col-md-6 mt-3">
                    <label class="form-label fw-bold text-dark d-flex align-items-center">
                        <span>Monthly AI Token Quota</span>
                        <i class="ti ti-info-circle text-muted ms-1 cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="100,000 tokens ≈ 300 comments/captions. Leave empty for unlimited."></i>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-cpu"></i></span>
                        <input type="number" name="ai_token_quota" class="form-control" value="{{ $quotaValue }}" placeholder="e.g. 100000 (leave blank for unlimited)" min="0" step="5000">
                        <span class="input-group-text">Tokens / mo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
