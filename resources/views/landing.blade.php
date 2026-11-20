<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>SocialManager - AI-Powered Social Media Management & Auto Engagement</title>
    <meta name="description" content="Enterprise social media publishing, smart AI community moderation, and automated Comment-to-DM for Facebook, Instagram, and YouTube."/>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Tabler Core CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <!-- Tabler Icons Webfont -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        html {
            scroll-behavior: smooth;
        }

        :root {
            --brand-teal: #1db8ab;
            --brand-teal-hover: #17a397;
            --brand-teal-dark: #10857b;
            --brand-teal-light: rgba(29, 184, 171, 0.08);
            --brand-teal-border: rgba(29, 184, 171, 0.25);
            --navy-dark: #0f172a;
            --navy-surface: #1e293b;
            --slate-body: #334155;
            --slate-muted: #64748b;
            --slate-border: #e2e8f0;
            --slate-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #fafbfc;
            color: #1e293b;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Modern Gradient Accents */
        .text-teal-gradient {
            background: linear-gradient(135deg, #1db8ab 0%, #0ca678 50%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Buttons & Badges */
        .btn-teal {
            background-color: #1db8ab;
            border-color: #1db8ab;
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(29, 184, 171, 0.28);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-teal:hover, .btn-teal:focus {
            background-color: #17a397;
            border-color: #17a397;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(29, 184, 171, 0.38);
        }
        .btn-outline-teal {
            color: #1db8ab;
            border-color: #1db8ab;
            background: transparent;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-outline-teal:hover {
            background-color: rgba(29, 184, 171, 0.08);
            color: #10857b;
            border-color: #10857b;
        }
        .btn-outline-hero {
            color: #334155;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-outline-hero:hover {
            color: #0f172a;
            background-color: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        }
        .badge-teal-soft {
            background-color: rgba(29, 184, 171, 0.08);
            color: #0d8a80;
            border: 1px solid rgba(29, 184, 171, 0.25);
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .bg-teal-lt {
            background-color: rgba(29, 184, 171, 0.08) !important;
            color: #0d8a80 !important;
        }
        .text-teal {
            color: #0d8a80 !important;
        }

        /* =========================================================
           HIGH-END FLOATING GLASS NAVBAR
        ========================================================= */
        .landing-navbar {
            position: sticky;
            top: 0;
            z-index: 1040;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 0.85rem 0;
        }

        /* Brand Logo Mark */
        .brand-logo-mark {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #0ca678 0%, #12b886 50%, #20c997 100%);
            color: #ffffff;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(12, 166, 120, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .brand-logo:hover .brand-logo-mark {
            transform: scale(1.05) rotate(-2deg);
            box-shadow: 0 8px 22px rgba(12, 166, 120, 0.45);
        }
        .brand-logo-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.35rem;
            color: #0f172a;
            letter-spacing: -0.03em;
            line-height: 1;
        }

        /* Desktop Clean Navigation Links (Minimal, No Grey Background) */
        .landing-nav-links {
            display: inline-flex;
            align-items: center;
            gap: 2.25rem;
            margin: 0;
            padding: 0;
        }
        .landing-nav-link {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            text-decoration: none;
            padding: 4px 0;
            transition: color 0.18s ease;
            letter-spacing: -0.01em;
        }
        .landing-nav-link:hover {
            color: #1db8ab;
        }

        /* Nav Action Buttons */
        .btn-nav-signin {
            color: #334155;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 8px 18px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-nav-signin:hover {
            color: #0ca678;
            background: rgba(12, 166, 120, 0.06);
        }
        .btn-nav-cta {
            background: linear-gradient(135deg, #0ca678 0%, #099268 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.92rem;
            padding: 9px 22px;
            border-radius: 50px;
            border: none;
            box-shadow: 0 4px 16px rgba(12, 166, 120, 0.35);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-nav-cta:hover {
            background: linear-gradient(135deg, #099268 0%, #087f5b 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(12, 166, 120, 0.45);
        }

        /* Hero Styling */
        .hero-section {
            padding: 90px 0 70px 0;
            background: radial-gradient(circle at 50% 0%, rgba(29, 184, 171, 0.09) 0%, rgba(250, 251, 252, 0) 70%),
                        linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
            position: relative;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.035em;
            color: #0f172a;
        }
        .hero-subtitle {
            font-size: 1.18rem;
            line-height: 1.6;
            color: #475569;
            max-width: 680px;
            margin: 0 auto;
            font-weight: 400;
        }

        /* ===================================================================== */
        /* APPLE-STYLE MINIMALIST PLATFORMS SECTION                              */
        /* ===================================================================== */
        .apple-platforms-section {
            padding: 95px 0;
            background: #ffffff;
            border-top: 1px solid rgba(15, 23, 42, 0.05);
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        }
        .apple-section-eyebrow {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #0ca678;
        }
        .apple-section-headline {
            font-size: 2.35rem;
            font-weight: 800;
            letter-spacing: -0.035em;
            color: #0f172a;
            line-height: 1.2;
        }
        .apple-section-subhead {
            font-size: 1.08rem;
            color: #64748b;
            line-height: 1.6;
            margin: 0;
            font-weight: 400;
        }
        .apple-platform-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.07);
            border-radius: 24px;
            padding: 40px 32px;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.03);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .apple-platform-card:hover {
            transform: translateY(-5px);
            border-color: rgba(15, 23, 42, 0.14);
            box-shadow: 0 24px 48px -12px rgba(15, 23, 42, 0.08);
        }
        .apple-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 24px;
            transition: transform 0.3s ease;
        }
        .apple-platform-card:hover .apple-icon-wrapper {
            transform: scale(1.08);
        }
        .apple-icon-fb {
            background: #edf5ff;
            color: #1877f2;
        }
        .apple-icon-ig {
            background: #fdf2f8;
            color: #d62976;
        }
        .apple-icon-yt {
            background: #fef2f2;
            color: #e02424;
        }
        .apple-card-title {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            color: #0f172a;
            margin: 0 0 12px 0;
        }
        .apple-card-desc {
            font-size: 0.96rem;
            line-height: 1.65;
            color: #64748b;
            margin: 0 0 32px 0;
            font-weight: 400;
        }
        .apple-card-meta {
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
        }
        .apple-api-chip {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .apple-api-chip::before {
            content: '';
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
        }
        .apple-official-badge {
            font-size: 0.78rem;
            font-weight: 600;
            color: #0ca678;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* ===================================================================== */
        /* APPLE-STYLE BENTO GRID: WHY SOCIALMANAGER                             */
        /* ===================================================================== */
        .apple-features-section {
            padding: 95px 0;
            background: #fcfdfe;
            border-top: 1px solid rgba(15, 23, 42, 0.05);
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        }
        .apple-bento-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 20px;
            padding: 30px 26px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03), 0 1px 2px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        .apple-bento-card:hover {
            transform: translateY(-4px);
            border-color: rgba(29, 184, 171, 0.45);
            box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.08), 0 2px 6px rgba(0, 0, 0, 0.03);
        }
        .apple-bento-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        .apple-bento-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: rgba(29, 184, 171, 0.08);
            color: #10857b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            border: 1px solid rgba(29, 184, 171, 0.22);
            transition: transform 0.2s ease, background-color 0.2s ease;
        }
        .apple-bento-card:hover .apple-bento-icon {
            transform: scale(1.05);
            background: rgba(29, 184, 171, 0.14);
        }
        .apple-bento-badge {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 20px;
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .apple-bento-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
            line-height: 1.35;
        }
        .apple-bento-desc {
            font-size: 0.92rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .apple-bento-widget {
            background: #f8fafc;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 12px;
            padding: 11px 14px;
            font-size: 0.825rem;
            font-weight: 500;
        }

        /* ===================================================================== */
        /* APPLE-STYLE HOW IT WORKS PROCESS SECTION                              */
        /* ===================================================================== */
        .apple-process-section {
            padding: 95px 0;
            background: #fafafc;
            border-top: 1px solid rgba(15, 23, 42, 0.05);
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        }
        .apple-step-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.07);
            border-radius: 24px;
            padding: 40px 32px;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.03);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .apple-step-card:hover {
            transform: translateY(-5px);
            border-color: rgba(15, 23, 42, 0.14);
            box-shadow: 0 24px 48px -12px rgba(15, 23, 42, 0.08);
        }
        .apple-step-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .apple-step-number {
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #0ca678;
            background: rgba(12, 166, 120, 0.1);
            padding: 4px 12px;
            border-radius: 999px;
            display: inline-block;
        }
        .apple-step-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            background: #f8fafc;
            color: #0f172a;
            transition: all 0.3s ease;
        }
        .apple-step-card:hover .apple-step-icon {
            transform: scale(1.08);
            background: rgba(12, 166, 120, 0.1);
            color: #0ca678;
        }
        .apple-step-title {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            color: #0f172a;
            margin: 0 0 12px 0;
        }
        .apple-step-desc {
            font-size: 0.96rem;
            line-height: 1.65;
            color: #64748b;
            margin: 0 0 32px 0;
            font-weight: 400;
        }
        .apple-step-meta {
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
        }
        .apple-step-pill {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .apple-step-pill::before {
            content: '';
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
        }
        .apple-step-time {
            font-size: 0.78rem;
            font-weight: 600;
            color: #0ca678;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* ===================================================================== */
        /* APPLE-STYLE MINIMALIST FAQ ACCORDION                                  */
        /* ===================================================================== */
        .apple-faq-section {
            padding: 95px 0;
            background: #ffffff;
            border-top: 1px solid rgba(15, 23, 42, 0.05);
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        }
        .apple-faq-container {
            max-width: 820px;
            margin: 0 auto;
        }
        .apple-faq-item {
            border-bottom: 1px solid #eef2f6;
            padding: 0;
            background: transparent;
            transition: border-color 0.2s ease;
        }
        .apple-faq-item:first-child {
            border-top: 1px solid #eef2f6;
        }
        .apple-faq-button {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            background: transparent;
            border: none;
            padding: 26px 0;
            font-size: 1.15rem;
            font-weight: 600;
            letter-spacing: -0.015em;
            color: #0f172a;
            cursor: pointer;
            transition: color 0.2s ease;
            box-shadow: none !important;
        }
        .apple-faq-button:hover {
            color: #0ca678;
        }
        .apple-faq-button:focus {
            outline: none;
            box-shadow: none;
        }
        .apple-faq-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-left: 20px;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), background 0.3s ease, color 0.3s ease;
        }
        .apple-faq-button:not(.collapsed) .apple-faq-icon {
            transform: rotate(45deg);
            background: rgba(12, 166, 120, 0.1);
            color: #0ca678;
        }
        .apple-faq-body {
            padding: 0 0 28px 0;
            font-size: 1rem;
            line-height: 1.75;
            color: #64748b;
            max-width: 760px;
            font-weight: 400;
        }

        /* Minimal Apple-Style Footer */
        .landing-footer {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-top: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 -4px 20px -2px rgba(15, 23, 42, 0.03), 0 -1px 2px rgba(0, 0, 0, 0.01);
            color: #64748b;
            padding: 48px 0 36px;
        }
        .landing-footer a.footer-link {
            color: #64748b;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.15s ease;
        }
        .landing-footer a.footer-link:hover {
            color: #0ca678;
        }
        .status-indicator-live {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: livePulse 2s infinite;
        }
        @keyframes livePulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @media (max-width: 991px) {
            .hero-title { font-size: 2.35rem; }
            .hero-subtitle { font-size: 1.05rem; }
            .browser-url-bar { display: none; }
        }
    </style>
</head>
<body>

    <!-- ========================================================================= -->
    <!-- STEP 1: STICKY NAVBAR                                                     -->
    <!-- ========================================================================= -->
    <header class="landing-navbar">
        <div class="container-xl">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Brand Logo (Official SocialManager Logo) -->
                <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <div class="navbar-brand-image text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background-color: #1db8ab !important;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v18" />
                            <path d="M3 12h18" />
                            <path d="M5.636 5.636l12.728 12.728" />
                            <path d="M18.364 5.636L5.636 18.364" />
                        </svg>
                    </div>
                    <span class="fw-bold text-dark fs-3 mb-0">SocialManager</span>
                </a>

                <!-- Desktop Clean Navigation Links (No Grey Background) -->
                <nav class="d-none d-lg-flex align-items-center">
                    <div class="landing-nav-links">
                        <a href="#features" class="landing-nav-link">Why SocialManager</a>
                        <a href="#platforms" class="landing-nav-link">Platforms</a>
                        <a href="#how-it-works" class="landing-nav-link">How It Works</a>
                        <a href="#faq" class="landing-nav-link">FAQ</a>
                    </div>
                </nav>

                <!-- Auth / Action Buttons -->
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn-nav-cta">
                            <i class="ti ti-layout-dashboard"></i>Open Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-nav-signin">
                            Sign In
                        </a>
                    @endauth

                    <!-- Mobile Menu Toggle Button -->
                    <button class="btn btn-icon btn-ghost-secondary rounded-circle d-lg-none ms-1" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti ti-menu-2 fs-2"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Dropdown -->
            <div class="collapse d-lg-none mt-3 pt-3 border-top" id="mobileNavCollapse">
                <div class="d-flex flex-column gap-2 pb-2">
                    <a href="#features" class="nav-link py-2 px-3 rounded-2 fw-semibold text-dark">Why SocialManager</a>
                    <a href="#platforms" class="nav-link py-2 px-3 rounded-2 fw-semibold text-dark">Platforms</a>
                    <a href="#how-it-works" class="nav-link py-2 px-3 rounded-2 fw-semibold text-dark">How It Works</a>
                    <a href="#faq" class="nav-link py-2 px-3 rounded-2 fw-semibold text-dark">FAQ</a>
                    <hr class="my-2 border-secondary border-opacity-25">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 py-2 fw-semibold">Sign In</a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-teal w-100 py-2 fw-semibold">Open Dashboard</a>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- STEP 1: HIGH-IMPACT HERO SECTION                                         -->
    <!-- ========================================================================= -->
    <section class="hero-section text-center">
        <div class="container-xl">
            <!-- Primary Headline -->
            <h1 class="hero-title mb-3">
                Scale Your Social Presence & Audience Inquiries <br class="d-none d-md-inline">
                <span class="text-teal-gradient">100% on Autopilot</span>
            </h1>

            <!-- Subtitle -->
            <p class="hero-subtitle mb-4">
                Automate multi-platform publishing, 24/7 contextual AI comment replies, and instant private DM lead capture across Facebook, Instagram, and YouTube.
            </p>

            <!-- CTA Button -->
            <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 mb-4">
                <a href="{{ route('login') }}" class="btn btn-teal btn-lg px-4 py-2.5">
                    <i class="ti ti-bolt me-1.5"></i>Start Free
                </a>
                <a href="#features" class="btn btn-outline-hero btn-lg px-4 py-2.5">
                    <span>Why SocialManager</span>
                    <i class="ti ti-arrow-down ms-1.5"></i>
                </a>
            </div>

            <!-- Micro Trust Indicators (Zero Emojis, Pure SVG Icons) -->
            <div class="d-flex flex-wrap align-items-center justify-content-center gap-4 text-muted small mb-5">
                <div class="d-flex align-items-center gap-1.5">
                    <i class="ti ti-check text-teal fs-3"></i>
                    <span>No credit card required</span>
                </div>
                <div class="d-flex align-items-center gap-1.5">
                    <i class="ti ti-check text-teal fs-3"></i>
                    <span>Zero password storage (OAuth 2.0)</span>
                </div>
                <div class="d-flex align-items-center gap-1.5">
                    <i class="ti ti-check text-teal fs-3"></i>
                    <span>24/7 Auto-Renew Token Protection</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- STEP 2: CERTIFIED PLATFORMS RIBBON (APPLE-STYLE MINIMAL)                  -->
    <!-- ========================================================================= -->
    <section id="platforms" class="apple-platforms-section">
        <div class="container-xl">
            <div class="text-center mb-5" style="max-width: 680px; margin-left: auto; margin-right: auto;">
                <h2 class="apple-section-headline mb-3">Directly connected to official platform APIs.</h2>
                <p class="apple-section-subhead">Zero scrapers. Full enterprise compliance across Meta and Google Cloud.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Facebook Platform Card -->
                <div class="col-12 col-md-4">
                    <div class="apple-platform-card">
                        <div class="apple-icon-wrapper apple-icon-fb">
                            <i class="ti ti-brand-facebook"></i>
                        </div>
                        <h3 class="apple-card-title">Facebook Pages</h3>
                        <p class="apple-card-desc">Publish content, sync community interactions, and dispatch real-time private messages.</p>
                        <div class="apple-card-meta">
                            <span class="apple-api-chip">Meta Graph API</span>
                            <span class="apple-official-badge"><i class="ti ti-shield-check"></i>Official</span>
                        </div>
                    </div>
                </div>

                <!-- Instagram Platform Card -->
                <div class="col-12 col-md-4">
                    <div class="apple-platform-card">
                        <div class="apple-icon-wrapper apple-icon-ig">
                            <i class="ti ti-brand-instagram"></i>
                        </div>
                        <h3 class="apple-card-title">Instagram Professional</h3>
                        <p class="apple-card-desc">Schedule Reels and Carousels, track post engagement, and automate private Comment-to-DM triggers.</p>
                        <div class="apple-card-meta">
                            <span class="apple-api-chip">Instagram Graph API</span>
                            <span class="apple-official-badge"><i class="ti ti-shield-check"></i>Official</span>
                        </div>
                    </div>
                </div>

                <!-- YouTube Platform Card -->
                <div class="col-12 col-md-4">
                    <div class="apple-platform-card">
                        <div class="apple-icon-wrapper apple-icon-yt">
                            <i class="ti ti-brand-youtube"></i>
                        </div>
                        <h3 class="apple-card-title">YouTube Channels</h3>
                        <p class="apple-card-desc">Upload videos and Shorts with 24/7 background token renewal and automated comment filtering.</p>
                        <div class="apple-card-meta">
                            <span class="apple-api-chip">YouTube Data API v3</span>
                            <span class="apple-official-badge"><i class="ti ti-shield-check"></i>Official</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- ========================================================================= -->
    <!-- STEP 2: WHY SOCIALMANAGER (APPLE-STYLE MINIMAL BENTO GRID SHOWCASE)      -->
    <!-- ========================================================================= -->
    <section id="features" class="apple-features-section">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="text-center mb-5" style="max-width: 760px; margin-left: auto; margin-right: auto;">
                <span class="apple-section-eyebrow mb-2 d-inline-block">ENGINEERED FOR SCALE</span>
                <h2 class="apple-section-headline mb-3">Why SocialManager?</h2>
                <p class="apple-section-subhead">
                    Everything you need to broadcast across channels, moderate thousands of follower inquiries, and safeguard brand integrity around the clock.
                </p>
            </div>

            <!-- Apple Bento Grid (6 Refined Capability Cards) -->
            <div class="row g-4">
                <!-- Card 1: Multi-Platform 1-Click Publishing -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="apple-bento-card">
                        <div>
                            <div class="apple-bento-header">
                                <div class="apple-bento-icon">
                                    <i class="ti ti-layers-intersect"></i>
                                </div>
                                <span class="apple-bento-badge">1-Click Broadcast</span>
                            </div>
                            <h3 class="apple-bento-title">Multi-Platform &amp; Multi-Account</h3>
                            <p class="apple-bento-desc">
                                Broadcast rich media, reels, and video content across Facebook Pages, Instagram Professional, and YouTube simultaneously in a single click.
                            </p>
                        </div>
                        <div class="apple-bento-widget d-flex align-items-center justify-content-between text-muted">
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="badge bg-blue-lt border border-blue-subtle px-2 py-0.5 small"><i class="ti ti-brand-facebook me-1"></i>FB</span>
                                <span class="badge bg-pink-lt border border-pink-subtle px-2 py-0.5 small"><i class="ti ti-brand-instagram me-1"></i>IG</span>
                                <span class="badge bg-red-lt border border-red-subtle px-2 py-0.5 small"><i class="ti ti-brand-youtube me-1"></i>YT</span>
                            </div>
                            <span class="text-teal fw-semibold small d-flex align-items-center gap-1">
                                <i class="ti ti-check"></i>Synced
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Unified Content Scheduler -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="apple-bento-card">
                        <div>
                            <div class="apple-bento-header">
                                <div class="apple-bento-icon">
                                    <i class="ti ti-calendar-event"></i>
                                </div>
                                <span class="apple-bento-badge">Auto-Queue</span>
                            </div>
                            <h3 class="apple-bento-title">Unified Multi-Platform Content Scheduler</h3>
                            <p class="apple-bento-desc">
                                Automated queue management for posts, reels, and videos. Delivers content at peak engagement hours with zero human intervention.
                            </p>
                        </div>
                        <div class="apple-bento-widget d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 text-dark small fw-medium">
                                <i class="ti ti-clock-play text-teal"></i>
                                <span>Next: Today, 04:30 PM</span>
                            </div>
                            <span class="badge bg-teal-lt border border-teal-subtle">Queued</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: 24/7 Anti-Troll & Abuse Shield -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="apple-bento-card">
                        <div>
                            <div class="apple-bento-header">
                                <div class="apple-bento-icon">
                                    <i class="ti ti-shield-lock"></i>
                                </div>
                                <span class="apple-bento-badge">Real-Time</span>
                            </div>
                            <h3 class="apple-bento-title">24/7 Anti-Troll &amp; Toxicity Shield</h3>
                            <p class="apple-bento-desc">
                                Autonomous AI toxicity guardrails silently detect and auto-hide abusive comments, political slurs, and spam bots before they go public.
                            </p>
                        </div>
                        <div class="apple-bento-widget d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 small">
                                <i class="ti ti-shield-check text-teal"></i>
                                <span class="text-dark fw-medium">Troll Auto-Hidden</span>
                            </div>
                            <span class="badge bg-success-lt border border-success-subtle">Protected</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Automated Comment-to-DM Engine -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="apple-bento-card">
                        <div>
                            <div class="apple-bento-header">
                                <div class="apple-bento-icon">
                                    <i class="ti ti-mail-forward"></i>
                                </div>
                                <span class="apple-bento-badge">&lt; 0.8s Latency</span>
                            </div>
                            <h3 class="apple-bento-title">Automated Comment-to-DM Engine</h3>
                            <p class="apple-bento-desc">
                                Convert audience comments into qualified leads. Dispatches verified private DMs with 24-hour frequency capping to prevent spam flags.
                            </p>
                        </div>
                        <div class="apple-bento-widget d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 small">
                                <span class="text-muted">Trigger: "Link"</span>
                                <i class="ti ti-arrow-right text-teal"></i>
                                <span class="text-dark fw-semibold">Instant DM Sent</span>
                            </div>
                            <span class="badge bg-teal-lt">0.8s</span>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Live Sentiment Intelligence & Analytics -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="apple-bento-card">
                        <div>
                            <div class="apple-bento-header">
                                <div class="apple-bento-icon">
                                    <i class="ti ti-chart-dots"></i>
                                </div>
                                <span class="apple-bento-badge">Sentiment Pulse</span>
                            </div>
                            <h3 class="apple-bento-title">Live Sentiment Intelligence &amp; Analytics</h3>
                            <p class="apple-bento-desc">
                                Real-time sentiment classification sorts thousands of constituent comments into appreciation, inquiries, and grievances with transparent ratio tracking.
                            </p>
                        </div>
                        <div class="apple-bento-widget">
                            <div class="d-flex justify-content-between align-items-center small mb-1.5">
                                <span class="fw-semibold text-dark">Constituent Pulse</span>
                                <span class="text-teal fw-bold">71.4% Positive</span>
                            </div>
                            <div class="progress progress-sm" style="height: 5px;">
                                <div class="progress-bar bg-teal" style="width: 71.4%"></div>
                                <div class="progress-bar bg-blue" style="width: 14.3%"></div>
                                <div class="progress-bar bg-warning" style="width: 14.3%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 6: Top User Tracking & Adaptive AI Personas -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="apple-bento-card">
                        <div>
                            <div class="apple-bento-header">
                                <div class="apple-bento-icon">
                                    <i class="ti ti-users"></i>
                                </div>
                                <span class="apple-bento-badge">Adaptive Voice</span>
                            </div>
                            <h3 class="apple-bento-title">Top User Tracking &amp; Adaptive AI Personas</h3>
                            <p class="apple-bento-desc">
                                Automatically recognize loyal constituents and VIP followers while tailoring AI responses in your designated tone across Hindi &amp; Hinglish.
                            </p>
                        </div>
                        <div class="apple-bento-widget d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 small">
                                <i class="ti ti-sparkles text-teal"></i>
                                <span class="text-dark fw-medium">Humble &amp; Respectful</span>
                            </div>
                            <span class="badge bg-secondary-lt">Hindi + Hinglish</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STEP 3: HOW IT WORKS (APPLE-STYLE MINIMAL 3 STEPS)                       -->
    <!-- ========================================================================= -->
    <section id="how-it-works" class="apple-process-section">
        <div class="container-xl">
            <div class="text-center mb-5" style="max-width: 680px; margin-left: auto; margin-right: auto;">
                <span class="apple-section-eyebrow mb-2 d-inline-block">SIMPLE ONBOARDING</span>
                <h2 class="apple-section-headline mb-3">Up and Running in Under 5 Minutes</h2>
                <p class="apple-section-subhead">No complex server setups or API keys required. Authenticate your accounts and let our background automation handle engagement.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Step 01 -->
                <div class="col-12 col-md-4">
                    <div class="apple-step-card">
                        <div class="apple-step-header">
                            <span class="apple-step-number">Step 01</span>
                            <div class="apple-step-icon">
                                <i class="ti ti-plug-connected"></i>
                            </div>
                        </div>
                        <h3 class="apple-step-title">Connect Your Accounts</h3>
                        <p class="apple-step-desc">Link your Facebook Page, Instagram Professional account, and YouTube channel in 1 click using official OAuth 2.0 single sign-on.</p>
                        <div class="apple-step-meta">
                            <span class="apple-step-pill">1-Click OAuth 2.0</span>
                            <span class="apple-step-time"><i class="ti ti-clock"></i>&lt; 60 sec</span>
                        </div>
                    </div>
                </div>

                <!-- Step 02 -->
                <div class="col-12 col-md-4">
                    <div class="apple-step-card">
                        <div class="apple-step-header">
                            <span class="apple-step-number">Step 02</span>
                            <div class="apple-step-icon">
                                <i class="ti ti-adjustments-horizontal"></i>
                            </div>
                        </div>
                        <h3 class="apple-step-title">Configure AI Tone & Rules</h3>
                        <p class="apple-step-desc">Select your preferred AI communication voice, define Comment-to-DM keyword triggers, and activate 24/7 Anti-Troll Shield.</p>
                        <div class="apple-step-meta">
                            <span class="apple-step-pill">Custom Persona</span>
                            <span class="apple-step-time"><i class="ti ti-clock"></i>2 mins</span>
                        </div>
                    </div>
                </div>

                <!-- Step 03 -->
                <div class="col-12 col-md-4">
                    <div class="apple-step-card">
                        <div class="apple-step-header">
                            <span class="apple-step-number">Step 03</span>
                            <div class="apple-step-icon">
                                <i class="ti ti-rocket"></i>
                            </div>
                        </div>
                        <h3 class="apple-step-title">Operate on Full Autopilot</h3>
                        <p class="apple-step-desc">Scheduled posts publish automatically on time. Incoming comments are answered in seconds, and private DMs convert leads 24/7.</p>
                        <div class="apple-step-meta">
                            <span class="apple-step-pill">24/7 Automation</span>
                            <span class="apple-step-time"><i class="ti ti-circle-check"></i>Always Live</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- STEP 5: FREQUENTLY ASKED QUESTIONS (APPLE-STYLE MINIMAL)                  -->
    <!-- ========================================================================= -->
    <section id="faq" class="apple-faq-section">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="text-center mb-5" style="max-width: 680px; margin-left: auto; margin-right: auto;">
                <span class="apple-section-eyebrow mb-2 d-inline-block">Frequently Asked Questions</span>
                <h2 class="apple-section-headline mb-3">Everything you need to know.</h2>
                <p class="apple-section-subhead">Clear answers regarding official platform safety, comment-to-DM triggers, and encryption.</p>
            </div>

            <!-- Apple-Style Accordion Wrapper -->
            <div class="apple-faq-container" id="faqAccordion">
                
                <!-- FAQ 1 -->
                <div class="apple-faq-item">
                    <button class="apple-faq-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                        <span>Can my Facebook, Instagram, or YouTube account get banned or restricted?</span>
                        <span class="apple-faq-icon"><i class="ti ti-plus"></i></span>
                    </button>
                    <div id="faqCollapse1" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="apple-faq-body">
                            No. Unlike unofficial scraping bots or browser automation extensions that risk account flags, SocialManager communicates exclusively through certified Meta Graph API and Google Cloud YouTube Data API protocols. Every automated comment response and scheduled post strictly complies with platform rate-limiting and developer terms.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="apple-faq-item">
                    <button class="apple-faq-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                        <span>How does the Comment-to-DM lead capture trigger work on Instagram and Facebook?</span>
                        <span class="apple-faq-icon"><i class="ti ti-plus"></i></span>
                    </button>
                    <div id="faqCollapse2" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="apple-faq-body">
                            When a follower comments on your posts or Reels with your configured trigger keywords (like "link", "pricing", or "details"), Meta sends an instant webhook event. SocialManager immediately publishes an authentic public reply confirming delivery while simultaneously dispatching a personalized private Direct Message (DM) to their inbox.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="apple-faq-item">
                    <button class="apple-faq-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                        <span>How does YouTube comment automation work without exceeding Google API quotas?</span>
                        <span class="apple-faq-icon"><i class="ti ti-plus"></i></span>
                    </button>
                    <div id="faqCollapse3" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="apple-faq-body">
                            YouTube's Data API provides a standard allocation of 10,000 units per day. Our intelligent polling daemon operates on an optimized schedule consuming only 144 units every 24 hours. Built-in rate limiting and deduplication ensure your account stays safely within official Google Cloud quota limits at all times.
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="apple-faq-item">
                    <button class="apple-faq-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                        <span>Can I customize the AI's personality, tone, and brand safety guidelines?</span>
                        <span class="apple-faq-icon"><i class="ti ti-plus"></i></span>
                    </button>
                    <div id="faqCollapse4" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="apple-faq-body">
                            Yes. You can select pre-built voices such as Humble & Respectful, Patriotic & Inspiring, or Formal & Official, or define your own tailored brand voice prompt. You can also specify negative-sentiment escalation rules, competitor name filters, and prohibited vocabulary.
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="apple-faq-item">
                    <button class="apple-faq-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                        <span>How are social tokens and sensitive client credentials protected?</span>
                        <span class="apple-faq-icon"><i class="ti ti-plus"></i></span>
                    </button>
                    <div id="faqCollapse5" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="apple-faq-body">
                            All platform OAuth access tokens are encrypted at rest using AES-256 encryption. We never request, store, or have access to your personal social media passwords. Platform authentication is executed strictly through official single sign-on dialogs hosted by Meta and Google.
                        </div>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="apple-faq-item">
                    <button class="apple-faq-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse6">
                        <span>Can I review AI-generated responses before they get published?</span>
                        <span class="apple-faq-icon"><i class="ti ti-plus"></i></span>
                    </button>
                    <div id="faqCollapse6" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="apple-faq-body">
                            Yes. SocialManager supports two engagement modes: Full Autopilot for automatic hands-free 24/7 engagement, or Human Approval Mode where AI responses are held in a review queue for one-click approval before publication.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
        <!-- ========================================================================= -->
    <!-- STEP 5: MINIMAL APPLE-STYLE FOOTER                                        -->
    <!-- ========================================================================= -->
    <footer class="landing-footer">
        <div class="container-xl">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-4 pb-4 border-bottom border-light">
                <!-- Brand Logo (Official SocialManager Logo) -->
                <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <div class="navbar-brand-image text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background-color: #1db8ab !important;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v18" />
                            <path d="M3 12h18" />
                            <path d="M5.636 5.636l12.728 12.728" />
                            <path d="M18.364 5.636L5.636 18.364" />
                        </svg>
                    </div>
                    <span class="fw-bold text-dark fs-3 mb-0">SocialManager</span>
                </a>

                <!-- Navigation & Legal Links -->
                <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4">
                    <a href="#features" class="footer-link">Why SocialManager</a>
                    <a href="#platforms" class="footer-link">Platforms</a>
                    <a href="#how-it-works" class="footer-link">How It Works</a>
                    <a href="#faq" class="footer-link">FAQ</a>
                    <a href="{{ url('/privacy') }}" class="footer-link">Privacy Policy</a>
                    <a href="{{ url('/terms') }}" class="footer-link">Terms of Service</a>
                    <a href="{{ url('/data-deletion') }}" class="footer-link">Data Deletion Policy</a>
                    <a href="{{ route('login') }}" class="footer-link fw-semibold text-teal">Sign In</a>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-3 text-muted small">
                &copy; {{ date('Y') }} SocialManager.
            </div>
        </div>
    </footer>

    <!-- Tabler Core JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
</body>
</html>
