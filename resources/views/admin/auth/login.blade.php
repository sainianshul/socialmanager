<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <title>Sign In — SocialManager Admin</title>
    
    <!-- Tabler Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    
    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif;
            background-color: #fafbfc !important;
            color: #1e293b;
        }
        .login-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.85) !important;
            border-radius: 20px !important;
            box-shadow: 0 16px 36px -8px rgba(29, 184, 171, 0.08), 0 4px 14px -2px rgba(15, 23, 42, 0.03) !important;
            transition: box-shadow 0.25s ease, transform 0.25s ease;
        }
        .login-card:hover {
            box-shadow: 0 22px 48px -8px rgba(29, 184, 171, 0.12), 0 6px 18px -2px rgba(15, 23, 42, 0.05) !important;
        }
        .brand-logo-circle {
            width: 44px;
            height: 44px;
            background-color: #1db8ab !important;
            color: #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(29, 184, 171, 0.32);
            transition: transform 0.2s ease;
        }
        .brand-logo-circle:hover {
            transform: scale(1.05);
        }
        .form-control:focus {
            border-color: #1db8ab !important;
            box-shadow: 0 0 0 3px rgba(29, 184, 171, 0.15) !important;
        }
        .btn-primary {
            background-color: #1db8ab !important;
            border-color: #1db8ab !important;
            box-shadow: 0 4px 14px rgba(29, 184, 171, 0.28) !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
        }
        .btn-primary:hover {
            background-color: #17a397 !important;
            border-color: #17a397 !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(29, 184, 171, 0.38) !important;
        }
    </style>
</head>
<body class="d-flex flex-column">
    <script>
        var mode = localStorage.getItem('data-bs-theme') || 'light';
        document.body.setAttribute('data-bs-theme', mode);
    </script>

    <div class="page page-center">
        <div class="container container-tight py-4">

            <div class="text-center mb-4">
                <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                    <div class="brand-logo-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v18" />
                            <path d="M3 12h18" />
                            <path d="M5.636 5.636l12.728 12.728" />
                            <path d="M18.364 5.636L5.636 18.364" />
                        </svg>
                    </div>
                    <span class="fw-bold text-dark fs-1 lh-1">SocialManager</span>
                </a>
            </div>

            <div class="card card-md login-card">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h2 text-center fw-bold text-dark mb-1">Login to your account</h2>
                    <p class="text-center text-secondary small mb-4">Enter your credentials to access the dashboard</p>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}" autocomplete="off" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Email address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="admin@vcancare.com" autocomplete="off" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Password</label>
                            <div class="input-group input-group-flat">
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="••••••••" autocomplete="off" required>
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" id="toggle-password" title="Show password"
                                       data-bs-toggle="tooltip">
                                        <i class="ti ti-eye" id="eye-icon"></i>
                                    </a>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="remember" value="1"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <span class="form-check-label text-secondary small">Remember me on this device</span>
                            </label>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" id="submit-btn">
                                Sign in
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center text-secondary small mt-4">
                &copy; {{ date('Y') }} SocialManager. All rights reserved.
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js" defer></script>
    <script>
        document.getElementById('toggle-password').addEventListener('click', function(e) {
            e.preventDefault();
            var input = document.getElementById('password');
            var icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ti ti-eye-off';
            } else {
                input.type = 'password';
                icon.className = 'ti ti-eye';
            }
        });
    </script>
</body>
</html>
