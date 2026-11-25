@php
    $mgr = auth()->user();
@endphp

<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manager Workspace</div>
            <h2 class="page-title text-body">Welcome, {{ $mgr->name ?? 'Manager' }}</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="d-flex flex-wrap gap-2">
                @if($mgr->canManageContent())
                    <a href="{{ route('admin.festival-templates.index') }}" class="btn btn-primary">
                        <i class="ti ti-sparkles me-1"></i>Festival Templates
                    </a>
                    <a href="{{ route('admin.posters.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-template me-1"></i>Readymade Posters
                    </a>
                @endif
                @if($mgr->canManageUsers())
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-users me-1"></i>Users ({{ $managerStats['users'] ?? 0 }})
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row row-deck row-cards mb-4">
    @if($mgr->canManageContent())
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.political-parties.index') }}" class="card card-sm card-link text-decoration-none">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-primary-lt text-primary"><i class="ti ti-flag fs-2"></i></span></div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7">Political Parties</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ number_format($managerStats['parties'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.political-leaders.index') }}" class="card card-sm card-link text-decoration-none">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-warning-lt text-warning"><i class="ti ti-users-group fs-2"></i></span></div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7">Political Leaders</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ number_format($managerStats['leaders'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.festival-templates.index') }}" class="card card-sm card-link text-decoration-none">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-yellow-lt text-yellow"><i class="ti ti-sparkles fs-2"></i></span></div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7">Festival & Card Templates</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ number_format($managerStats['templates'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.posters.index') }}" class="card card-sm card-link text-decoration-none">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-indigo-lt text-indigo"><i class="ti ti-template fs-2"></i></span></div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7">Readymade Posters</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ number_format($managerStats['posters'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif

    @if($mgr->canManageUsers())
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.users.index') }}" class="card card-sm card-link text-decoration-none">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-teal-lt text-teal"><i class="ti ti-users fs-2"></i></span></div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7">Managed Users</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ number_format($managerStats['users'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif

    @if($mgr->canManageTickets())
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.help.index') }}" class="card card-sm card-link text-decoration-none">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-azure-lt text-azure"><i class="ti ti-ticket fs-2"></i></span></div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7">Open Support Tickets</div>
                            <div class="h2 mb-0 fw-bold text-body">{{ number_format($managerStats['openTickets'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif

    @if($mgr->canAccessSystem())
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('admin.system-activity.index') }}" class="card card-sm card-link text-decoration-none">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-purple-lt text-purple"><i class="ti ti-activity fs-2"></i></span></div>
                        <div class="col">
                            <div class="text-uppercase text-secondary fw-semibold fs-7">System Activity & Logs</div>
                            <div class="h4 mb-0 fw-bold text-body">Open Monitor</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif
</div>

@if(!$mgr->hasAnyManagerPermission())
    <div class="alert alert-warning">
        <i class="ti ti-alert-triangle me-2"></i>No administrative permissions are currently enabled for your manager account. Please contact your Super Admin.
    </div>
@endif
