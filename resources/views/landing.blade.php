<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>SocialManager - AI-Powered Social Media Management & Auto Engagement</title>
    <meta name="description" content="Enterprise social media publishing, smart AI community moderation, and automated Comment-to-DM for Facebook, Instagram, and YouTube."/>
    
    <!-- Tabler Core & Icons -->
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <style>
        :root {
            --brand-teal: #0ca678;
            --brand-teal-hover: #099268;
            --brand-teal-light: rgba(12, 166, 120, 0.08);
            --brand-teal-border: rgba(12, 166, 120, 0.25);
            --navy-dark: #0f172a;
            --slate-border: #e2e8f0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #fafbfc;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Teal Buttons & Badges */
        .btn-teal {
            background-color: var(--brand-teal);
            border-color: var(--brand-teal);
            color: #ffffff;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }
        .btn-teal:hover {
            background-color: var(--brand-teal-hover);
            border-color: var(--brand-teal-hover);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(12, 166, 120, 0.25);
        }
        .badge-teal-soft {
            background-color: var(--brand-teal-light);
            color: var(--brand-teal);
            border: 1px solid var(--brand-teal-border);
            font-weight: 600;
        }

        /* Sticky Glass Navbar */
        .landing-navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }
        .nav-link-custom {
            color: #475569;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem;
            transition: color 0.15s ease;
        }
        .nav-link-custom:hover {
            color: var(--brand-teal);
        }

        /* Hero Styling */
        .hero-section {
            padding: 80px 0 60px 0;
            background: radial-gradient(circle at 50% 0%, rgba(12, 166, 120, 0.05) 0%, rgba(250, 251, 252, 0) 70%);
        }
        .hero-title {
            font-size: 3.25rem;
            font-weight: 800;
            line-height: 1.18;
            letter-spacing: -0.03em;
            color: var(--navy-dark);
        }
        .text-teal-gradient {
            background: linear-gradient(135deg, #0ca678 0%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-subtitle {
            font-size: 1.2rem;
            line-height: 1.65;
            color: #64748b;
            max-width: 720px;
            margin: 0 auto;
        }

        /* Dashboard UI Mockup Frame */
        .browser-mockup {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 20px 45px -12px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            margin-top: 40px;
        }
        .browser-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 16px;
            display: flex;
            align-items: center;
        }
        .browser-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .dot-red { background-color: #ef4444; }
        .dot-yellow { background-color: #f59e0b; }
        .dot-green { background-color: #10b981; }
        .browser-url-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 3px 14px;
            font-size: 0.78rem;
            color: #64748b;
            margin-left: 12px;
            width: 320px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Mockup Internal UI */
        .mockup-body {
            background: #f8fafc;
            padding: 24px;
        }
        .mock-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .mock-event-pill {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .mock-event-published {
            background-color: #ecfdf5;
            border: 1px solid #86efac;
            color: #064e3b;
        }
        .mock-event-scheduled {
            background-color: #eff6ff;
            border: 1px solid #93c5fd;
            color: #0f2d59;
        }
        .brand-icon-fb { color: #1877f2; }
        .brand-icon-ig { color: #d62976; }
        .brand-icon-yt { color: #e02424; }

        @media (max-width: 991px) {
            .hero-title { font-size: 2.3rem; }
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
            <div class="d-flex align-items-center justify-content-between py-2.5">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <span class="avatar avatar-sm rounded-2 bg-teal-lt text-teal">
                        <i class="ti ti-brand-campaignmonitor fs-2"></i>
                    </span>
                    <span class="fw-bold fs-3 text-dark">Social<span class="text-teal">Manager</span></span>
                    <span class="badge badge-teal-soft ms-1">v2.4</span>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="d-none d-lg-flex align-items-center gap-1">
                    <a href="#features" class="nav-link-custom">Features</a>
                    <a href="#platforms" class="nav-link-custom">Platforms</a>
                    <a href="#how-it-works" class="nav-link-custom">How It Works</a>
                    <a href="#pricing" class="nav-link-custom">Pricing</a>
                    <a href="#faq" class="nav-link-custom">FAQ</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-teal btn-sm px-3">
                            <i class="ti ti-layout-dashboard me-1"></i>Open Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost-secondary btn-sm px-3 d-none d-sm-inline-block">
                            Sign In
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-teal btn-sm px-3.5">
                            <i class="ti ti-arrow-right me-1"></i>Get Started Free
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- STEP 1: HIGH-IMPACT HERO SECTION                                         -->
    <!-- ========================================================================= -->
    <section class="hero-section text-center">
        <div class="container-xl">
            <!-- Official API Trust Badge -->
            <div class="d-inline-flex align-items-center gap-1.5 badge-teal-soft px-3 py-1.5 rounded-pill mb-3 fs-7">
                <i class="ti ti-shield-check fs-3"></i>
                <span>Official Meta Graph API & Google Cloud Partner Architecture</span>
            </div>

            <!-- Primary Headline -->
            <h1 class="hero-title mb-3">
                Scale Your Social Presence & Audience Inquiries <br class="d-none d-md-inline">
                <span class="text-teal-gradient">100% on Autopilot</span>
            </h1>

            <!-- Subtitle -->
            <p class="hero-subtitle mb-4">
                The centralized social management command center built for creators, public figures, and brands. 
                Schedule posts across Facebook, Instagram, and YouTube, reply to community comments 24/7 with customized contextual AI, and automatically dispatch private direct messages to interested leads.
            </p>

            <!-- CTA Buttons -->
            <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                <a href="{{ route('login') }}" class="btn btn-teal btn-lg px-4 py-2.5">
                    <i class="ti ti-bolt me-1.5"></i>Start Free Trial
                </a>
                <a href="#preview" class="btn btn-outline-secondary btn-lg px-4 py-2.5">
                    <i class="ti ti-player-play me-1.5"></i>Live Platform Preview
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

            <!-- ============================================================= -->
            <!-- LIVE PRODUCT DASHBOARD PREVIEW MOCKUP                         -->
            <!-- ============================================================= -->
            <div id="preview" class="browser-mockup text-start">
                <!-- Browser Bar -->
                <div class="browser-header">
                    <div>
                        <span class="browser-dot dot-red"></span>
                        <span class="browser-dot dot-yellow"></span>
                        <span class="browser-dot dot-green"></span>
                    </div>
                    <div class="browser-url-bar">
                        <i class="ti ti-lock text-teal"></i>
                        <span>https://socialmanager.rezel.online/admin/dashboard</span>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <span class="badge bg-teal-lt text-teal">
                            <span class="status-dot bg-teal me-1.5"></span>System Live
                        </span>
                    </div>
                </div>

                <!-- Mockup Content Body -->
                <div class="mockup-body">
                    <!-- Top Metric Cards -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <div class="mock-card p-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted small fw-semibold">Connected Channels</span>
                                    <span class="badge badge-teal-soft"><i class="ti ti-check me-1"></i>Active</span>
                                </div>
                                <div class="h3 fw-bold mb-1 text-dark">3 Channels</div>
                                <div class="d-flex align-items-center gap-2 small text-muted">
                                    <span><i class="ti ti-brand-facebook brand-icon-fb"></i> FB Page</span>
                                    <span>•</span>
                                    <span><i class="ti ti-brand-instagram brand-icon-ig"></i> IG Pro</span>
                                    <span>•</span>
                                    <span><i class="ti ti-brand-youtube brand-icon-yt"></i> YouTube</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="mock-card p-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted small fw-semibold">AI Comments Processed</span>
                                    <span class="badge bg-teal-lt text-teal">98.4% Accuracy</span>
                                </div>
                                <div class="h3 fw-bold mb-1 text-dark">1,420</div>
                                <div class="small text-muted">
                                    <span class="text-teal fw-semibold"><i class="ti ti-trending-up me-1"></i>+18.2%</span> vs last week
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="mock-card p-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted small fw-semibold">Auto DMs Dispatched</span>
                                    <span class="badge badge-outline text-teal">Instant (0.8s)</span>
                                </div>
                                <div class="h3 fw-bold mb-1 text-dark">312</div>
                                <div class="small text-muted">Private Comment-to-DM triggers</div>
                            </div>
                        </div>
                    </div>

                    <!-- Split View: Left Calendar + Right Live AI Auto-Reply & DM -->
                    <div class="row g-3">
                        <!-- Left: Publishing Calendar Preview -->
                        <div class="col-12 col-lg-6">
                            <div class="mock-card p-3.5 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ti ti-calendar text-teal fs-2"></i>
                                        <span class="fw-bold text-dark">Publishing Schedule Calendar</span>
                                    </div>
                                    <span class="badge bg-secondary-lt text-secondary">Today</span>
                                </div>

                                <!-- Scheduled Event 1 (Published) -->
                                <div class="mock-event-pill mock-event-published justify-content-between">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i class="ti ti-brand-youtube brand-icon-yt fs-3"></i>
                                        <span class="text-truncate">Budget 2026 Analysis - Full Video</span>
                                    </div>
                                    <span class="badge badge-outline text-success bg-white ms-2">
                                        <i class="ti ti-check me-1"></i>Published
                                    </span>
                                </div>

                                <!-- Scheduled Event 2 (Scheduled) -->
                                <div class="mock-event-pill mock-event-scheduled justify-content-between">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i class="ti ti-brand-instagram brand-icon-ig fs-3"></i>
                                        <span class="text-truncate">Reel: 5 Important Welfare Schemes</span>
                                    </div>
                                    <span class="badge badge-outline text-primary bg-white ms-2">
                                        <i class="ti ti-clock me-1"></i>04:30 PM
                                    </span>
                                </div>

                                <!-- Scheduled Event 3 (Scheduled) -->
                                <div class="mock-event-pill mock-event-scheduled justify-content-between mb-0">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i class="ti ti-brand-facebook brand-icon-fb fs-3"></i>
                                        <span class="text-truncate">Public Grievance Camp Announcement</span>
                                    </div>
                                    <span class="badge badge-outline text-primary bg-white ms-2">
                                        <i class="ti ti-clock me-1"></i>07:00 PM
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Live AI Moderation & Auto DM Stream -->
                        <div class="col-12 col-lg-6">
                            <div class="mock-card p-3.5 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ti ti-messages text-teal fs-2"></i>
                                        <span class="fw-bold text-dark">Live AI Comment Moderation & Auto DM</span>
                                    </div>
                                    <span class="badge badge-teal-soft">Live Stream</span>
                                </div>

                                <!-- User Comment Card -->
                                <div class="p-2.5 rounded-2 bg-light border mb-2">
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar avatar-xs rounded-circle bg-azure text-white">RS</span>
                                            <span class="fw-semibold small text-dark">Ramesh Sharma</span>
                                            <span class="text-muted fs-8"><i class="ti ti-brand-instagram brand-icon-ig"></i> Instagram</span>
                                        </div>
                                        <span class="text-muted fs-8">2m ago</span>
                                    </div>
                                    <div class="small text-secondary mb-2">
                                        "Sir please send me the application form link for the education scheme."
                                    </div>

                                    <!-- AI Public Reply -->
                                    <div class="p-2 rounded-2 bg-white border-start border-teal border-3 ms-2 mb-2">
                                        <div class="d-flex align-items-center gap-1.5 mb-1 text-teal fs-8 fw-semibold">
                                            <i class="ti ti-robot"></i>
                                            <span>AI Public Reply (Humble & Respectful Tone)</span>
                                        </div>
                                        <div class="fs-8 text-dark">
                                            "Ramesh ji, yojna ka official application form link aapke private direct message (DM) me bhej diya gaya hai. Kripya apna inbox check karein."
                                        </div>
                                    </div>

                                    <!-- Auto DM Dispatched Badge -->
                                    <div class="d-inline-flex align-items-center gap-1 badge badge-teal-soft py-1 px-2 fs-8">
                                        <i class="ti ti-mail-forward"></i>
                                        <span>Auto DM Dispatched to @ramesh_sharma via Instagram Direct</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Browser Mockup -->
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- PLACEHOLDER FOR STEP 2 (Platform Ribbon & Core Feature Bento Grid)        -->
    <!-- PLACEHOLDER FOR STEP 3 (How It Works & Live Workflows)                     -->
    <!-- PLACEHOLDER FOR STEP 4 (Pricing Plans & Security Guarantees)               -->
    <!-- PLACEHOLDER FOR STEP 5 (Client FAQs, CTA & Legal Compliant Footer)        -->
    <!-- ========================================================================= -->

    <!-- Basic Footer for Step 1 -->
    <footer class="py-4 border-top bg-white text-center text-muted small">
        <div class="container-xl">
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
                <span>&copy; {{ date('Y') }} <strong>SocialManager</strong>. Enterprise Social Automation.</span>
                <div class="d-flex gap-3">
                    <a href="{{ url('/privacy') }}" class="text-muted text-decoration-none">Privacy Policy</a>
                    <a href="{{ url('/terms') }}" class="text-muted text-decoration-none">Terms of Service</a>
                    <a href="{{ url('/data-deletion') }}" class="text-muted text-decoration-none">Data Deletion</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Tabler Core JS -->
    <script src="{{ asset('dist/js/tabler.min.js') }}"></script>
</body>
</html>
