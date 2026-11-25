@php
    $hasOld = session()->hasOldInput();
    $managerToggles = [
        [
            'name' => 'can_manage_content',
            'label' => 'Content Management',
            'hint' => 'Manage Political Parties, Leaders, Strips, Festivals, Templates & Readymade Posters',
            'default' => isset($user) ? ($user->can_manage_content ?? true) : true,
        ],
        [
            'name' => 'can_manage_users',
            'label' => 'Manage Users',
            'hint' => 'Create, edit, block and inspect client accounts & quotas',
            'default' => isset($user) ? ($user->can_manage_users ?? false) : false,
        ],
        [
            'name' => 'can_manage_tickets',
            'label' => 'Support Tickets',
            'hint' => 'View, reply and update status on all user support tickets',
            'default' => isset($user) ? ($user->can_manage_tickets ?? false) : false,
        ],
        [
            'name' => 'can_access_system',
            'label' => 'System Access',
            'hint' => 'Access System Activity logs, Queue Monitor, Webhook Logs & API Docs',
            'default' => isset($user) ? ($user->can_access_system ?? false) : false,
        ],
    ];
    $currentRole = (int) ($hasOld ? old('role', $user->role ?? \App\Models\User::ROLE_USER) : ($user->role ?? \App\Models\User::ROLE_USER));
    $isManagerSelected = $currentRole === \App\Models\User::ROLE_MANAGER;
@endphp

{{-- MANAGER PERMISSIONS --}}
<div class="col-12 mt-4 {{ $isManagerSelected ? '' : 'd-none' }}" id="manager-permissions-section">
    <div class="card bg-light-lt border shadow-none">
        <div class="card-header bg-transparent py-2.5">
            <h4 class="card-title text-dark mb-0 d-flex align-items-center">
                <i class="ti ti-shield-lock me-2 text-azure fs-3"></i>Manager Permissions
            </h4>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-center">
                @foreach($managerToggles as $perm)
                    @php
                        $isChecked = $hasOld ? (bool) old($perm['name']) : (bool) $perm['default'];
                    @endphp
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="d-flex align-items-center">
                            <label class="form-check form-switch cursor-pointer mb-0">
                                <input class="form-check-input" type="checkbox" name="{{ $perm['name'] }}" value="1" {{ $isChecked ? 'checked' : '' }}>
                                <span class="form-check-label fw-bold text-dark">{{ $perm['label'] }}</span>
                            </label>
                            <i class="ti ti-info-circle text-muted ms-1 cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $perm['hint'] }}"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
