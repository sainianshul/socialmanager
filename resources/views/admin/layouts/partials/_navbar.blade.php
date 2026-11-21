<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none border-bottom">
    <div class="container-fluid px-3 px-lg-4">
        {{-- Left: Sidebar Toggle & Breadcrumbs (in place of search) --}}
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-icon btn-ghost-secondary border-0 text-body p-0 d-inline-flex align-items-center justify-content-center" id="btn-toggle-sidebar-navbar" title="Toggle Sidebar" aria-label="Toggle Sidebar" style="width: 36px; height: 36px; min-width: 36px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-toggle-icon">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                    <path d="M9 3v18" />
                </svg>
            </button>
            <div class="vr my-1 mx-2 text-secondary opacity-25 d-none d-lg-block" style="height: 18px;"></div>
            
            {{-- Breadcrumbs in place of Search --}}
            <nav aria-label="breadcrumb" class="d-inline-flex align-items-center">
                @if (View::hasSection('breadcrumbs'))
                    @yield('breadcrumbs')
                @else
                    <x-breadcrumb />
                @endif
            </nav>
        </div>

        {{-- Right: Theme Toggle & User Info --}}
        <div class="navbar-nav flex-row order-md-last ms-auto align-items-center">
            <div class="nav-item d-none d-md-flex me-3">
                <a href="javascript:void(0)" class="nav-link px-0 hide-theme-dark btn-theme-toggle" title="Enable dark mode" data-bs-toggle="tooltip"
		   data-bs-placement="bottom" data-theme="dark">
                    <i class="ti ti-moon fs-2"></i>
                </a>
                <a href="javascript:void(0)" class="nav-link px-0 hide-theme-light btn-theme-toggle" title="Enable light mode" data-bs-toggle="tooltip"
		   data-bs-placement="bottom" data-theme="light">
                    <i class="ti ti-sun fs-2"></i>
                </a>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm rounded-circle" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=1db8ab&color=fff&bold=true')"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div class="fw-semibold text-body">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="mt-1 small text-secondary">{{ auth()->user()->isManager() ? 'Manager' : (auth()->user()->isAdmin() ? 'Administrator' : 'User') }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
                        <i class="ti ti-user me-2"></i> Profile
                    </a>
                    <a href="{{ route('admin.branding.index') }}" class="dropdown-item">
                        <i class="ti ti-id me-2"></i> Branding Studio
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="ti ti-logout me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
