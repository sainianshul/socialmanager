<aside class="navbar navbar-vertical navbar-expand-lg">
    <div class="container-fluid">
        <!-- Mobile Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Brand Logo (Desktop) -->
        <h1 class="navbar-brand navbar-brand-autodark d-none d-lg-flex align-items-center w-100 mb-0 py-3 px-3">
            <a href="{{ url('/admin') }}" class="d-flex align-items-center gap-2 text-decoration-none text-reset" title="SocialManager">
                <div class="navbar-brand-image text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background-color: #1db8ab !important;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3v18" />
                        <path d="M3 12h18" />
                        <path d="M5.636 5.636l12.728 12.728" />
                        <path d="M18.364 5.636L5.636 18.364" />
                    </svg>
                </div>
                <span class="navbar-brand-title fw-bold text-body fs-3 mb-0">SocialManager</span>
            </a>
        </h1>

        <!-- Brand Logo (Mobile) -->
        <h1 class="navbar-brand navbar-brand-autodark d-lg-none mb-0 py-1 px-1">
            <a href="{{ url('/admin') }}" class="d-flex align-items-center gap-2 text-decoration-none text-reset" title="SocialManager">
                <div class="navbar-brand-image text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background-color: #1db8ab !important;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3v18" />
                        <path d="M3 12h18" />
                        <path d="M5.636 5.636l12.728 12.728" />
                        <path d="M18.364 5.636L5.636 18.364" />
                    </svg>
                </div>
                <span class="navbar-brand-title fw-bold text-body fs-4 mb-0">SocialManager</span>
            </a>
        </h1>

        <!-- Mobile Quick Actions -->
        <div class="navbar-nav flex-row d-lg-none align-items-center">
            <div class="nav-item d-flex me-3">
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
                    <span class="avatar avatar-sm rounded-circle" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=1db8ab&color=fff&rounded=true')"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('admin.profile.index') }}" class="dropdown-item">Profile</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Collapse Menu -->
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-2">
                {{-- MAIN MENU --}}
                <li class="nav-section-title">Main Menu</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ url('/admin') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-smart-home fs-2"></i>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>
                @if(auth()->check() && !auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}" href="{{ route('admin.analytics.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Sentiment Analytics">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-chart-dots fs-2"></i>
                            </span>
                            <span class="nav-link-title">Sentiment Analytics</span>
                        </a>
                    </li>
                @endif

                {{-- USER MANAGEMENT (Admin / Manager only) --}}
                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isManager()))
                    <li class="nav-section-title">User Management</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="All Users">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-users fs-2"></i>
                            </span>
                            <span class="nav-link-title">All Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Add User">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-success">
                                <i class="ti ti-user-plus fs-2"></i>
                            </span>
                            <span class="nav-link-title">Add User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.blocked*') ? 'active' : '' }}" href="{{ route('admin.users.blocked') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Blocked Users">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-warning">
                                <i class="ti ti-user-off fs-2"></i>
                            </span>
                            <span class="nav-link-title">Blocked Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.deleted*') ? 'active' : '' }}" href="{{ route('admin.users.deleted') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Deleted Users">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-danger">
                                <i class="ti ti-trash fs-2"></i>
                            </span>
                            <span class="nav-link-title">Deleted Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.help.*') ? 'active' : '' }}" href="{{ route('admin.help.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Support Tickets">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-teal">
                                <i class="ti ti-ticket fs-2"></i>
                            </span>
                            <span class="nav-link-title">Support Tickets</span>
                        </a>
                    </li>
                @endif

                {{-- CLIENT SOCIAL ACCOUNTS, CONTENT & ENGAGEMENT (Only for Regular Users / Non-Admin) --}}
                {{-- SuperAdmin manages users directly and accesses user accounts via Ghost/Shadow Mode --}}
                @if(auth()->check() && !auth()->user()->isAdmin())
                    {{-- ACCOUNTS HUB --}}
                    <li class="nav-section-title">Accounts</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}" href="{{ route('admin.accounts.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Connected Accounts">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-plug-connected fs-2"></i>
                            </span>
                            <span class="nav-link-title">Connected Accounts</span>
                        </a>
                    </li>

                    {{-- CONTENT --}}
                    <li class="nav-section-title">Content</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.posts.create') ? 'active' : '' }}" href="{{ route('admin.posts.create') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="New Post">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-square-plus fs-2"></i>
                            </span>
                            <span class="nav-link-title">New Post</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.festival-studio.*') ? 'active' : '' }}" href="{{ route('admin.festival-studio.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Festival Studio">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-warning">
                                <i class="ti ti-sparkles fs-2"></i>
                            </span>
                            <span class="nav-link-title">Festival Studio</span>
                            <span class="badge bg-warning-lt ms-auto">Auto</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.posts.instagram') ? 'active' : '' }}" href="{{ route('admin.posts.instagram') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Instagram Posts">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-pink">
                                <i class="ti ti-brand-instagram fs-2"></i>
                            </span>
                            <span class="nav-link-title">Instagram Posts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.posts.facebook') ? 'active' : '' }}" href="{{ route('admin.posts.facebook') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Facebook Posts">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-facebook">
                                <i class="ti ti-brand-facebook fs-2"></i>
                            </span>
                            <span class="nav-link-title">Facebook Posts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.posts.youtube') ? 'active' : '' }}" href="{{ route('admin.posts.youtube') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="YouTube Videos">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-danger">
                                <i class="ti ti-brand-youtube fs-2"></i>
                            </span>
                            <span class="nav-link-title">YouTube Videos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.posts.schedule*') ? 'active' : '' }}" href="{{ route('admin.posts.schedule') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Schedule & Publish">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-azure">
                                <i class="ti ti-calendar-event fs-2"></i>
                            </span>
                            <span class="nav-link-title">Schedule & Publish</span>
                        </a>
                    </li>


                    {{-- ENGAGEMENT & AI MODERATION --}}
                    <li class="nav-section-title">Engagement</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.comments.instagram*') ? 'active' : '' }}" href="{{ route('admin.comments.instagram') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Insta Comment">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-pink">
                                <i class="ti ti-messages fs-2"></i>
                            </span>
                            <span class="nav-link-title">Insta Comment</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.comments.facebook*') ? 'active' : '' }}" href="{{ route('admin.comments.facebook') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="FB Comment">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-facebook">
                                <i class="ti ti-message-circle fs-2"></i>
                            </span>
                            <span class="nav-link-title">FB Comment</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.comments.youtube*') ? 'active' : '' }}" href="{{ route('admin.comments.youtube') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="YouTube Comments">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-danger">
                                <i class="ti ti-message-chatbot fs-2"></i>
                            </span>
                            <span class="nav-link-title">YouTube Comments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.auto-dm.*') ? 'active' : '' }}" href="{{ route('admin.auto-dm.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Auto DM">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-mail-forward fs-2"></i>
                            </span>
                            <span class="nav-link-title">Auto DM</span>
                        </a>
                    </li>

                    {{-- HELP & SUPPORT --}}
                    <li class="nav-section-title">Help & Support</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.help.*') ? 'active' : '' }}" href="{{ route('admin.help.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Help Center">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-azure">
                                <i class="ti ti-help-circle fs-2"></i>
                            </span>
                            <span class="nav-link-title">Help</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="tooltip" data-bs-placement="right" title="Documentation">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-azure">
                                <i class="ti ti-book fs-2"></i>
                            </span>
                            <span class="nav-link-title">Documentation</span>
                        </a>
                    </li>
                @endif

                {{-- SYSTEM & API (Admin only) --}}
                @if(auth()->check() && auth()->user()->isAdmin())
                    <li class="nav-section-title">System & API</li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="tooltip" data-bs-placement="right" title="Settings">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-settings fs-2"></i>
                            </span>
                            <span class="nav-link-title">Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/api/documentation') }}" target="_blank" data-bs-toggle="tooltip" data-bs-placement="right" title="API Docs (Swagger)">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-teal">
                                <i class="ti ti-code fs-2"></i>
                            </span>
                            <span class="nav-link-title">API Docs (Swagger)</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</aside>
