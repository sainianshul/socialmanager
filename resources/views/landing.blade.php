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
    <!-- STEP 2: CERTIFIED PLATFORMS RIBBON                                        -->
    <!-- ========================================================================= -->
    <section id="platforms" class="py-5 bg-white border-top border-bottom">
        <div class="container-xl">
            <div class="text-center mb-4">
                <span class="badge badge-teal-soft mb-2"><i class="ti ti-api-app me-1"></i>Official Integrations</span>
                <h3 class="fw-bold text-dark mb-1">Directly Connected to Official Platform APIs</h3>
                <p class="text-muted small">No web scrapers, no unofficial bots. 100% compliant with platform developer terms.</p>
            </div>

            <div class="row g-3 justify-content-center">
                <!-- Facebook Platform Card -->
                <div class="col-12 col-md-4">
                    <div class="card h-100 border p-3.5 shadow-none" style="border-radius: 10px;">
                        <div class="d-flex align-items-center gap-3 mb-2.5">
                            <span class="avatar avatar-md rounded-2" style="background: rgba(24, 119, 242, 0.08); color: #1877f2;">
                                <i class="ti ti-brand-facebook fs-1"></i>
                            </span>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">Facebook Pages</h4>
                                <span class="badge badge-outline text-primary fs-8">Graph API v20.0</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Permanent Page tokens with real-time Webhook feed subscription. Publish posts, sync comments, and dispatch Messenger replies.</p>
                        <div class="d-flex flex-wrap gap-1.5 mt-auto">
                            <span class="badge bg-light text-secondary border fs-8"><i class="ti ti-check text-teal me-1"></i>Photos & Videos</span>
                            <span class="badge bg-light text-secondary border fs-8"><i class="ti ti-check text-teal me-1"></i>Auto Moderation</span>
                            <span class="badge bg-light text-secondary border fs-8"><i class="ti ti-check text-teal me-1"></i>Instant DMs</span>
                        </div>
                    </div>
                </div>

                <!-- Instagram Platform Card -->
                <div class="col-12 col-md-4">
                    <div class="card h-100 border p-3.5 shadow-none" style="border-radius: 10px;">
                        <div class="d-flex align-items-center gap-3 mb-2.5">
                            <span class="avatar avatar-md rounded-2" style="background: rgba(214, 41, 118, 0.08); color: #d62976;">
                                <i class="ti ti-brand-instagram fs-1"></i>
                            </span>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">Instagram Professional</h4>
                                <span class="badge badge-outline text-pink fs-8">Business & Creator</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Linked Instagram account automation. Schedule Carousels & Reels, track public interactions, and send private Direct Messages.</p>
                        <div class="d-flex flex-wrap gap-1.5 mt-auto">
                            <span class="badge bg-light text-secondary border fs-8"><i class="ti ti-check text-teal me-1"></i>Reels Publishing</span>
                            <span class="badge bg-light text-secondary border fs-8"><i class="ti ti-check text-teal me-1"></i>Comment-to-DM</span>
                            <span class="badge bg-light text-secondary border fs-8"><i class="ti ti-check text-teal me-1"></i>Direct Messaging</span>
                        </div>
                    </div>
                </div>

                <!-- YouTube Platform Card -->
                <div class="col-12 col-md-4">
                    <div class="card h-100 border p-3.5 shadow-none" style="border-radius: 10px;">
                        <div class="d-flex align-items-center gap-3 mb-2.5">
                            <span class="avatar avatar-md rounded-2" style="background: rgba(224, 36, 36, 0.08); color: #e02424;">
                                <i class="ti ti-brand-youtube fs-1"></i>
                            </span>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">YouTube Channels</h4>
                                <span class="badge badge-outline text-danger fs-8">Data API v3</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Resumable video & Shorts publishing with automated 24/7 background token renewal. Public comment threading and auto-hiding.</p>
                        <div class="d-flex flex-wrap gap-1.5 mt-auto">
                            <span class="badge bg-light text-secondary border fs-8"><i class="ti ti-check text-teal me-1"></i>Shorts & Videos</span>
                            <span class="badge bg-light text-secondary border fs-8"><i class="ti ti-check text-teal me-1"></i>Auto-Renew Token</span>
                            <span class="badge bg-light text-secondary border fs-8"><i class="ti ti-check text-teal me-1"></i>Troll Shield</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- STEP 2: CORE FEATURES BENTO GRID                                          -->
    <!-- ========================================================================= -->
    <section id="features" class="py-6" style="background-color: #fafbfc;">
        <div class="container-xl">
            <div class="text-center mb-5">
                <span class="badge badge-teal-soft mb-2"><i class="ti ti-layers-linked me-1"></i>Core Capabilities</span>
                <h2 class="fw-bold text-dark mb-2 display-6">Engineered for High-Stakes Social Operations</h2>
                <p class="text-muted" style="max-width: 640px; margin: 0 auto; font-size: 1.05rem;">
                    Every tool required to publish rich media, engage thousands of citizens and followers, and protect brand reputation around the clock.
                </p>
            </div>

            <!-- Bento Box Grid -->
            <div class="row g-3">
                <!-- Bento Card 1: Content Publishing & Drag-Drop Calendar (7 Columns) -->
                <div class="col-12 col-lg-7">
                    <div class="card h-100 border p-4 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal">
                                <i class="ti ti-calendar-event fs-1"></i>
                            </span>
                            <span class="badge badge-outline text-teal">1-Minute Background Daemon</span>
                        </div>
                        <h3 class="fw-bold text-dark mb-2">Unified Multi-Platform Content Scheduler</h3>
                        <p class="text-muted mb-4">
                            Compose your message once, upload your image or video, and schedule it across Facebook, Instagram, and YouTube. Our automated server daemon executes scheduled publishes every 60 seconds with zero manual intervention.
                        </p>

                        <!-- Inner Feature Showcase Mockup -->
                        <div class="p-3 rounded-2 bg-light border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="small fw-semibold text-secondary">Active Calendar Slots</span>
                                <span class="small text-teal"><i class="ti ti-clock me-1"></i>Queue Running</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 rounded-2 bg-white border d-flex align-items-center gap-2">
                                        <i class="ti ti-video text-danger"></i>
                                        <div class="text-truncate">
                                            <div class="fw-semibold small text-truncate">Weekly Address Video</div>
                                            <div class="text-muted fs-8">YouTube • 1080p Resumable</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-2 bg-white border d-flex align-items-center gap-2">
                                        <i class="ti ti-device-mobile text-pink"></i>
                                        <div class="text-truncate">
                                            <div class="fw-semibold small text-truncate">Highlights Reel</div>
                                            <div class="text-muted fs-8">Instagram • 9:16 Vertical</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Card 2: Contextual Multi-AI Personas (5 Columns) -->
                <div class="col-12 col-lg-5">
                    <div class="card h-100 border p-4 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal">
                                <i class="ti ti-brain fs-1"></i>
                            </span>
                            <span class="badge badge-teal-soft">Gemini, OpenAI, Qwen</span>
                        </div>
                        <h3 class="fw-bold text-dark mb-2">Adaptive AI Personas</h3>
                        <p class="text-muted mb-3">
                            Customize the response voice for each channel. AI analyzes sentiment and drafts authentic replies in your selected voice.
                        </p>

                        <!-- Tone Selector Chips Preview -->
                        <div class="p-3 rounded-2 bg-light border">
                            <span class="small text-secondary fw-semibold d-block mb-2">Configurable Tones:</span>
                            <div class="d-flex flex-wrap gap-1 mb-2.5">
                                <span class="badge bg-white text-teal border border-teal fs-8"><i class="ti ti-check me-1"></i>Humble & Respectful</span>
                                <span class="badge bg-white text-secondary border fs-8">Patriotic & Inspiring</span>
                                <span class="badge bg-white text-secondary border fs-8">Assertive & Firm</span>
                                <span class="badge bg-white text-secondary border fs-8">Formal & Official</span>
                            </div>
                            <span class="small text-secondary fw-semibold d-block mb-1.5">Languages Supported:</span>
                            <div class="d-flex gap-1">
                                <span class="badge bg-teal-lt text-teal fs-8">Hindi + Hinglish</span>
                                <span class="badge bg-secondary-lt text-secondary fs-8">Pure Hindi</span>
                                <span class="badge bg-secondary-lt text-secondary fs-8">English</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Card 3: Instant Comment-to-DM Engine (4 Columns) -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border p-4 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal">
                                <i class="ti ti-mail-forward fs-1"></i>
                            </span>
                            <span class="badge badge-outline text-primary">Private Replies API</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Automated Comment-to-DM</h4>
                        <p class="text-muted small mb-3">
                            Convert public interactions into private inquiries. When a user comments specific keywords on Instagram or Facebook, send an instant DM with the link, details, or registration form.
                        </p>
                        <div class="p-2.5 rounded-2 bg-light border mt-auto">
                            <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-1">
                                <span>Rule Trigger Mode</span>
                                <span class="text-teal fw-semibold">Contains Keyword</span>
                            </div>
                            <div class="small fw-semibold text-dark">
                                Keywords: <code>link</code>, <code>apply</code>, <code>scheme</code>, <code>help</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Card 4: 24/7 Anti-Troll Shield (4 Columns) -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border p-4 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal">
                                <i class="ti ti-shield-lock fs-1"></i>
                            </span>
                            <span class="badge badge-outline text-danger">Zero Human Delay</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">24/7 Anti-Troll & Abuse Shield</h4>
                        <p class="text-muted small mb-3">
                            Our AI toxicity classification scans incoming comments for abusive language, hate speech, and troll attacks, automatically hiding them from public view before they cause brand harm.
                        </p>
                        <div class="p-2.5 rounded-2 bg-light border mt-auto">
                            <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-1">
                                <span>Action Taken</span>
                                <span class="badge bg-danger-lt text-danger fs-8">Auto-Hidden</span>
                            </div>
                            <div class="small text-muted text-truncate">
                                Abusive comment hidden on Facebook & YouTube
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Card 5: Real-Time Political Sentiment War Room (4 Columns) -->
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card h-100 border p-4 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal">
                                <i class="ti ti-chart-dots-3 fs-1"></i>
                            </span>
                            <span class="badge badge-teal-soft">Sentiment War Room</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Live Sentiment Intelligence</h4>
                        <p class="text-muted small mb-3">
                            Know exactly what your constituents and customers think. Real-time categorization of comments into Positive, Grievances, and Complaints with interactive trend graphs.
                        </p>
                        <div class="p-2.5 rounded-2 bg-light border mt-auto">
                            <div class="d-flex align-items-center justify-content-between small mb-1.5">
                                <span class="fw-semibold text-dark">Constituent Sentiment</span>
                                <span class="text-teal fw-bold">84% Positive</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-teal" style="width: 84%"></div>
                                <div class="progress-bar bg-warning" style="width: 12%"></div>
                                <div class="progress-bar bg-danger" style="width: 4%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
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
