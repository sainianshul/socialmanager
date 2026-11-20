<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>SocialManager - AI-Powered Social Media Management & Auto Engagement</title>
    <meta name="description" content="Enterprise social media publishing, smart AI community moderation, and automated Comment-to-DM for Facebook, Instagram, and YouTube."/>
    
    <!-- Tabler Core CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <!-- Tabler Icons Webfont -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
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

        /* Center Nav Pill Island */
        .nav-capsule-island {
            background: rgba(241, 245, 249, 0.75);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 50px;
            padding: 4px 6px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        .nav-capsule-link {
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
            padding: 6px 16px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-block;
        }
        .nav-capsule-link:hover {
            color: #0ca678;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
            transform: translateY(-1px);
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
            font-size: 1.22rem;
            line-height: 1.7;
            color: #475569;
            max-width: 740px;
            margin: 0 auto;
            font-weight: 450;
        }

        /* ===================================================================== */
        /* HIGH-TECH SAAS ANIMATIONS & GLOW                                      */
        /* ===================================================================== */
        @keyframes pulseGlowAura {
            0%, 100% {
                transform: scale(1) translateY(0);
                opacity: 0.75;
            }
            50% {
                transform: scale(1.08) translateY(-8px);
                opacity: 1;
            }
        }

        @keyframes mockupFloat {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes radarPing {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        @keyframes shimmerSweep {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }

        /* Ambient Glow & Floating Container */
        .hero-showcase-container {
            position: relative;
            max-width: 1080px;
            margin: 48px auto 0;
            text-align: left;
        }

        .hero-glow-aura {
            position: absolute;
            top: -30px;
            left: 8%;
            right: 8%;
            height: 400px;
            background: radial-gradient(ellipse at 50% 50%, rgba(12, 166, 120, 0.28) 0%, rgba(32, 201, 151, 0.15) 35%, rgba(99, 102, 241, 0.08) 60%, transparent 75%);
            filter: blur(75px);
            z-index: 0;
            pointer-events: none;
            animation: pulseGlowAura 9s ease-in-out infinite alternate;
        }

        /* Main SaaS Window Frame */
        .saas-mockup-frame {
            position: relative;
            z-index: 1;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 
                0 0 0 1px rgba(15, 23, 42, 0.03),
                0 25px 60px -15px rgba(15, 23, 42, 0.14),
                0 45px 95px -20px rgba(12, 166, 120, 0.18);
            overflow: hidden;
            animation: mockupFloat 8s ease-in-out infinite;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .saas-mockup-frame:hover {
            box-shadow: 
                0 0 0 1px rgba(15, 23, 42, 0.05),
                0 30px 70px -15px rgba(15, 23, 42, 0.18),
                0 55px 110px -20px rgba(12, 166, 120, 0.25);
        }

        /* macOS Dark Chrome Bar */
        .mockup-header-chrome {
            background: #0f172a;
            padding: 13px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .traffic-dots-group {
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .traffic-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            display: inline-block;
        }
        .dot-apple-red { background: #ff5f56; box-shadow: 0 0 8px rgba(255, 95, 86, 0.6); }
        .dot-apple-yellow { background: #ffbd2e; box-shadow: 0 0 8px rgba(255, 189, 46, 0.6); }
        .dot-apple-green { background: #27c93f; box-shadow: 0 0 8px rgba(39, 201, 63, 0.6); }

        .mockup-url-pill {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 5px 16px;
            font-size: 0.78rem;
            color: #94a3b8;
            margin-left: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .mockup-live-status {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.14);
            border: 1px solid rgba(16, 185, 129, 0.28);
            border-radius: 999px;
            padding: 4px 14px;
            font-size: 0.76rem;
            font-weight: 700;
            color: #34d399;
            letter-spacing: 0.02em;
        }
        .radar-dot-pulse {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            animation: radarPing 2s infinite cubic-bezier(0, 0, 0.2, 1);
        }

        /* Canvas & Modern Components */
        .mockup-canvas {
            background: #f8fafc;
            padding: 24px;
        }

        /* 3 Borderless Floating Stat Cards */
        .saas-stat-card {
            background: #ffffff;
            border: none;
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }
        .saas-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
        }
        .stat-card-title {
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .stat-card-num {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin: 4px 0;
        }
        .stat-pill {
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .stat-pill-teal {
            background: rgba(12, 166, 120, 0.12);
            color: #0ca678;
        }
        .stat-pill-emerald {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
        }
        .stat-pill-indigo {
            background: rgba(99, 102, 241, 0.12);
            color: #4f46e5;
        }

        /* Two Showcase Panels (Calendar & AI Moderation) */
        .saas-showcase-panel {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .saas-panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .panel-icon-squircle {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(12, 166, 120, 0.1);
            color: #0ca678;
            font-size: 1.15rem;
        }
        .panel-title-text {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
        }

        /* Publishing Schedule Queue Strips (NO UGLY RECTANGLES!) */
        .saas-queue-strip {
            background: #f8fafc;
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 9px;
            transition: transform 0.2s ease, background 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .saas-queue-strip:last-child {
            margin-bottom: 0;
        }
        .saas-queue-strip::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }
        .queue-yt::before { background: #e02424; }
        .queue-ig::before { background: #d62976; }
        .queue-fb::before { background: #1877f2; }

        .saas-queue-strip:hover {
            background: #f1f5f9;
            transform: translateX(4px);
        }
        .queue-title {
            font-size: 0.84rem;
            font-weight: 600;
            color: #1e293b;
            margin-left: 8px;
        }

        /* Live AI Moderation Stream - Clean Chat Bubble Layout */
        .moderation-chat-stream {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .chat-bubble-inbound {
            background: #f1f5f9;
            border-radius: 4px 14px 14px 14px;
            padding: 11px 14px;
        }
        .chat-inbound-user {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .user-avatar-mark {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .user-name-text {
            font-size: 0.8rem;
            font-weight: 700;
            color: #0f172a;
        }
        .user-platform-tag {
            font-size: 0.72rem;
            color: #d62976;
            font-weight: 600;
        }
        .chat-comment-body {
            font-size: 0.82rem;
            color: #334155;
            line-height: 1.45;
        }

        .chat-bubble-reply {
            background: linear-gradient(135deg, rgba(12, 166, 120, 0.08) 0%, rgba(32, 201, 151, 0.04) 100%);
            border-left: 3px solid #0ca678;
            border-radius: 14px 4px 14px 14px;
            padding: 11px 14px;
            margin-left: 12px;
        }
        .chat-reply-header {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #0ca678;
            font-size: 0.74rem;
            font-weight: 700;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .chat-reply-body {
            font-size: 0.81rem;
            color: #0f172a;
            line-height: 1.45;
        }

        /* Shimmering Auto-DM Dispatch Banner */
        .auto-dm-dispatch-shimmer {
            background: linear-gradient(90deg, #ecfdf5 0%, #d1fae5 35%, #a7f3d0 50%, #d1fae5 65%, #ecfdf5 100%);
            background-size: 200% 100%;
            animation: shimmerSweep 3.5s infinite linear;
            border-radius: 10px;
            padding: 9px 13px;
            display: flex;
            align-items: center;
            gap: 9px;
            color: #065f46;
            font-size: 0.78rem;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.14);
        }
        .dispatch-icon-box {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: #0ca678;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .brand-icon-fb { color: #1877f2; }
        .brand-icon-ig { color: #d62976; }
        .brand-icon-yt { color: #e02424; }

        /* General Badge Outline Refinement */
        .badge-outline {
            background: rgba(12, 166, 120, 0.08) !important;
            border: 1px solid rgba(12, 166, 120, 0.22) !important;
            border-radius: 999px;
            font-weight: 700;
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

        .bento-micro-surface {
            background: #f8fafc;
            border-radius: 12px;
            padding: 14px 16px;
            margin-top: auto;
            border: none;
        }
        .bento-slot-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 10px 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
        }

        /* Bento Grid Cards */
        .bento-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 18px;
            padding: 1.85rem;
            height: 100%;
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
        }
        .bento-card:hover {
            transform: translateY(-5px);
            border-color: rgba(12, 166, 120, 0.35);
            box-shadow: 0 20px 40px -8px rgba(12, 166, 120, 0.14), 0 0 0 1px rgba(12, 166, 120, 0.2);
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

        /* Security Cards */
        .security-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 18px;
            padding: 1.85rem;
            height: 100%;
            transition: all 0.28s ease;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
        }
        .security-card:hover {
            transform: translateY(-5px);
            border-color: rgba(12, 166, 120, 0.35);
            box-shadow: 0 18px 36px -8px rgba(12, 166, 120, 0.12);
        }

        /* Pricing Cards */
        .pricing-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 20px;
            padding: 2.25rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            height: 100%;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
        }
        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.1);
        }
        .pricing-card-featured {
            border: 2px solid #0ca678 !important;
            box-shadow: 0 20px 45px -8px rgba(12, 166, 120, 0.24) !important;
            background: linear-gradient(180deg, rgba(12, 166, 120, 0.03) 0%, #ffffff 100%);
        }
        .pricing-badge-popular {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: #0ca678;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 4px 14px;
            border-radius: 999px;
            box-shadow: 0 4px 12px rgba(12, 166, 120, 0.35);
        }

        /* Accordion Custom Styling */
        .accordion-custom .accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 12px !important;
            margin-bottom: 14px;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }
        .accordion-custom .accordion-item:hover {
            border-color: #cbd5e1;
        }
        .accordion-custom .accordion-button {
            font-weight: 650;
            font-size: 1.05rem;
            color: #0f172a;
            background: #ffffff;
            padding: 1.25rem 1.45rem;
            box-shadow: none !important;
        }
        .accordion-custom .accordion-button:not(.collapsed) {
            color: #0d8a80;
            background-color: rgba(29, 184, 171, 0.04);
            border-bottom: 1px solid #e2e8f0;
        }
        .accordion-custom .accordion-body {
            padding: 1.35rem 1.45rem;
            color: #475569;
            font-size: 0.96rem;
            line-height: 1.75;
        }

        /* Pre-Footer CTA Card */
        .cta-banner-card {
            background: linear-gradient(135deg, #0b1320 0%, #111a2d 65%, #064e3b 100%);
            border: 1px solid rgba(29, 184, 171, 0.35);
            border-radius: 18px;
            box-shadow: 0 25px 50px -15px rgba(11, 19, 32, 0.45);
            position: relative;
            overflow: hidden;
        }
        .cta-banner-card::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -15%;
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(29, 184, 171, 0.25) 0%, rgba(29, 184, 171, 0) 70%);
            pointer-events: none;
        }

        /* Modern 4-Column Footer */
        .landing-footer {
            background: #0b1320;
            color: #94a3b8;
            border-top: 1px solid #1e293b;
        }
        .landing-footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .landing-footer a:hover {
            color: #2dd4bf;
        }
        .footer-heading {
            color: #f8fafc;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 1.25rem;
        }
        .footer-link-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        .footer-link-list li {
            margin-bottom: 0.8rem;
            font-size: 0.92rem;
        }
        .footer-link-list li a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
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
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="brand-logo d-flex align-items-center gap-2.5 text-decoration-none">
                    <span class="brand-logo-mark">
                        <i class="ti ti-brand-campaignmonitor fs-2 text-white"></i>
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="brand-logo-title">Social<span class="text-teal-gradient">Manager</span></span>
                        <span class="badge badge-teal-soft">v2.4</span>
                    </div>
                </a>

                <!-- Desktop Navigation Capsule Island -->
                <nav class="d-none d-lg-flex align-items-center">
                    <div class="nav-capsule-island">
                        <a href="#features" class="nav-capsule-link">Features</a>
                        <a href="#platforms" class="nav-capsule-link">Platforms</a>
                        <a href="#how-it-works" class="nav-capsule-link">How It Works</a>
                        <a href="#pricing" class="nav-capsule-link">Pricing</a>
                        <a href="#faq" class="nav-capsule-link">FAQ</a>
                    </div>
                </nav>

                <!-- Auth / Action Buttons -->
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn-nav-cta">
                            <i class="ti ti-layout-dashboard"></i>Open Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-nav-signin d-none d-sm-inline-flex">
                            Sign In
                        </a>
                        <a href="{{ route('login') }}" class="btn-nav-cta">
                            <span>Get Started Free</span>
                            <i class="ti ti-arrow-right"></i>
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
                    <a href="#features" class="nav-link py-2 px-3 rounded-2 fw-semibold text-dark">Features</a>
                    <a href="#platforms" class="nav-link py-2 px-3 rounded-2 fw-semibold text-dark">Platforms</a>
                    <a href="#how-it-works" class="nav-link py-2 px-3 rounded-2 fw-semibold text-dark">How It Works</a>
                    <a href="#pricing" class="nav-link py-2 px-3 rounded-2 fw-semibold text-dark">Pricing</a>
                    <a href="#faq" class="nav-link py-2 px-3 rounded-2 fw-semibold text-dark">FAQ</a>
                    <hr class="my-2 border-secondary border-opacity-25">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 py-2 fw-semibold mb-2">Sign In</a>
                        <a href="{{ route('login') }}" class="btn btn-teal w-100 py-2 fw-semibold">Get Started Free</a>
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
            <div class="hero-showcase-container" id="preview">
                <!-- Ambient Pulsing Glow Aura -->
                <div class="hero-glow-aura"></div>

                <!-- Main SaaS Window Frame -->
                <div class="saas-mockup-frame">
                    <!-- macOS Dark Chrome Bar -->
                    <div class="mockup-header-chrome">
                        <div class="traffic-dots-group">
                            <span class="traffic-dot dot-apple-red"></span>
                            <span class="traffic-dot dot-apple-yellow"></span>
                            <span class="traffic-dot dot-apple-green"></span>
                        </div>
                        <div class="mockup-url-pill d-none d-sm-flex">
                            <i class="ti ti-lock text-teal"></i>
                            <span>https://socialmanager.rezel.online/admin/dashboard</span>
                        </div>
                        <div class="mockup-live-status">
                            <span class="radar-dot-pulse"></span>
                            <span>Autopilot Active • System Live</span>
                        </div>
                    </div>

                    <!-- Canvas Body -->
                    <div class="mockup-canvas">
                        <!-- Top 3 Floating Stat Cards (Borderless) -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <div class="saas-stat-card">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="stat-card-title">Connected Channels</span>
                                        <span class="stat-pill stat-pill-teal"><i class="ti ti-circle-check fs-4"></i>Active</span>
                                    </div>
                                    <div class="stat-card-num">3 Channels</div>
                                    <div class="d-flex align-items-center gap-2 small text-muted">
                                        <span class="d-inline-flex align-items-center gap-1"><i class="ti ti-brand-facebook brand-icon-fb"></i> FB Page</span>
                                        <span class="text-secondary">•</span>
                                        <span class="d-inline-flex align-items-center gap-1"><i class="ti ti-brand-instagram brand-icon-ig"></i> IG Pro</span>
                                        <span class="text-secondary">•</span>
                                        <span class="d-inline-flex align-items-center gap-1"><i class="ti ti-brand-youtube brand-icon-yt"></i> YouTube</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="saas-stat-card">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="stat-card-title">AI Comments Processed</span>
                                        <span class="stat-pill stat-pill-emerald">98.4% Accuracy</span>
                                    </div>
                                    <div class="stat-card-num">1,420</div>
                                    <div class="small text-muted">
                                        <span class="text-teal fw-semibold"><i class="ti ti-trending-up me-1"></i>+18.2%</span> vs last week
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="saas-stat-card">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="stat-card-title">Auto DMs Dispatched</span>
                                        <span class="stat-pill stat-pill-indigo"><i class="ti ti-bolt fs-4"></i>Instant (0.8s)</span>
                                    </div>
                                    <div class="stat-card-num">312</div>
                                    <div class="small text-muted">Private Comment-to-DM triggers</div>
                                </div>
                            </div>
                        </div>

                        <!-- Split View: Left Publishing Calendar + Right Live AI Moderation -->
                        <div class="row g-3">
                            <!-- Left: Publishing Schedule Calendar -->
                            <div class="col-12 col-lg-6">
                                <div class="saas-showcase-panel">
                                    <div class="saas-panel-heading">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="panel-icon-squircle">
                                                <i class="ti ti-calendar-event"></i>
                                            </div>
                                            <span class="panel-title-text">Publishing Schedule Calendar</span>
                                        </div>
                                        <span class="stat-pill stat-pill-teal">Today</span>
                                    </div>

                                    <!-- Queue Strip 1 (Published - YouTube) -->
                                    <div class="saas-queue-strip queue-yt">
                                        <div class="d-flex align-items-center gap-2 text-truncate">
                                            <span class="avatar avatar-xs rounded-2 bg-red-lt text-danger">
                                                <i class="ti ti-brand-youtube fs-3"></i>
                                            </span>
                                            <span class="queue-title text-truncate">Budget 2026 Analysis - Full Video</span>
                                        </div>
                                        <span class="stat-pill stat-pill-emerald ms-2">
                                            <i class="ti ti-check"></i>Published
                                        </span>
                                    </div>

                                    <!-- Queue Strip 2 (Scheduled - Instagram) -->
                                    <div class="saas-queue-strip queue-ig">
                                        <div class="d-flex align-items-center gap-2 text-truncate">
                                            <span class="avatar avatar-xs rounded-2 bg-pink-lt text-pink">
                                                <i class="ti ti-brand-instagram fs-3"></i>
                                            </span>
                                            <span class="queue-title text-truncate">Reel: 5 Important Welfare Schemes</span>
                                        </div>
                                        <span class="stat-pill stat-pill-indigo ms-2">
                                            <i class="ti ti-clock"></i>04:30 PM
                                        </span>
                                    </div>

                                    <!-- Queue Strip 3 (Scheduled - Facebook) -->
                                    <div class="saas-queue-strip queue-fb">
                                        <div class="d-flex align-items-center gap-2 text-truncate">
                                            <span class="avatar avatar-xs rounded-2 bg-blue-lt text-primary">
                                                <i class="ti ti-brand-facebook fs-3"></i>
                                            </span>
                                            <span class="queue-title text-truncate">Public Grievance Camp Announcement</span>
                                        </div>
                                        <span class="stat-pill stat-pill-indigo ms-2">
                                            <i class="ti ti-clock"></i>07:00 PM
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Live AI Moderation & Auto DM Stream -->
                            <div class="col-12 col-lg-6">
                                <div class="saas-showcase-panel">
                                    <div class="saas-panel-heading">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="panel-icon-squircle">
                                                <i class="ti ti-messages"></i>
                                            </div>
                                            <span class="panel-title-text">Live AI Comment Moderation & Auto DM</span>
                                        </div>
                                        <span class="stat-pill stat-pill-teal">
                                            <span class="radar-dot-pulse me-1"></span>Live Stream
                                        </span>
                                    </div>

                                    <div class="moderation-chat-stream">
                                        <!-- User Inbound Comment Bubble -->
                                        <div class="chat-bubble-inbound">
                                            <div class="chat-inbound-user">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="user-avatar-mark">RS</div>
                                                    <span class="user-name-text">Ramesh Sharma</span>
                                                    <span class="user-platform-tag"><i class="ti ti-brand-instagram me-0.5"></i>Instagram</span>
                                                </div>
                                                <span class="text-muted fs-8">2m ago</span>
                                            </div>
                                            <div class="chat-comment-body">
                                                "Sir please send me the application form link for the education scheme."
                                            </div>
                                        </div>

                                        <!-- AI Public Reply Bubble -->
                                        <div class="chat-bubble-reply">
                                            <div class="chat-reply-header">
                                                <i class="ti ti-robot fs-3"></i>
                                                <span>AI Public Reply (Humble & Respectful Tone)</span>
                                            </div>
                                            <div class="chat-reply-body">
                                                "Ramesh ji, yojna ka official application form link aapke private direct message (DM) me bhej diya gaya hai. Kripya apna inbox check karein."
                                            </div>
                                        </div>

                                        <!-- Auto-DM Dispatch Shimmer Notification Bar -->
                                        <div class="auto-dm-dispatch-shimmer">
                                            <div class="dispatch-icon-box">
                                                <i class="ti ti-mail-forward"></i>
                                            </div>
                                            <div class="text-truncate">
                                                Auto DM Dispatched to @ramesh_sharma via Instagram Direct
                                            </div>
                                        </div>
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
    <!-- STEP 2: CERTIFIED PLATFORMS RIBBON (APPLE-STYLE MINIMAL)                  -->
    <!-- ========================================================================= -->
    <section id="platforms" class="apple-platforms-section">
        <div class="container-xl">
            <div class="text-center mb-5" style="max-width: 680px; margin-left: auto; margin-right: auto;">
                <span class="apple-section-eyebrow mb-2 d-inline-block">OFFICIAL ECOSYSTEM</span>
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
                    <div class="bento-card d-flex flex-column">
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
                        <div class="bento-micro-surface">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="small fw-semibold text-secondary">Active Calendar Slots</span>
                                <span class="small text-teal"><i class="ti ti-clock me-1"></i>Queue Running</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="bento-slot-card">
                                        <i class="ti ti-video text-danger fs-3"></i>
                                        <div class="text-truncate">
                                            <div class="fw-semibold small text-truncate">Weekly Address Video</div>
                                            <div class="text-muted fs-8">YouTube • 1080p Resumable</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bento-slot-card">
                                        <i class="ti ti-device-mobile text-pink fs-3"></i>
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
                    <div class="bento-card d-flex flex-column">
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
                        <div class="bento-micro-surface">
                            <span class="small text-secondary fw-semibold d-block mb-2">Configurable Tones:</span>
                            <div class="d-flex flex-wrap gap-1 mb-2.5">
                                <span class="badge bg-teal text-white rounded-pill fs-8"><i class="ti ti-check me-1"></i>Humble & Respectful</span>
                                <span class="badge bg-white text-secondary rounded-pill shadow-xs fs-8">Patriotic & Inspiring</span>
                                <span class="badge bg-white text-secondary rounded-pill shadow-xs fs-8">Assertive & Firm</span>
                                <span class="badge bg-white text-secondary rounded-pill shadow-xs fs-8">Formal & Official</span>
                            </div>
                            <span class="small text-secondary fw-semibold d-block mb-1.5">Languages Supported:</span>
                            <div class="d-flex gap-1">
                                <span class="badge bg-teal-lt text-teal rounded-pill fs-8">Hindi + Hinglish</span>
                                <span class="badge bg-secondary-lt text-secondary rounded-pill fs-8">Pure Hindi</span>
                                <span class="badge bg-secondary-lt text-secondary rounded-pill fs-8">English</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Card 3: Instant Comment-to-DM Engine (4 Columns) -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bento-card d-flex flex-column">
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
                        <div class="bento-micro-surface">
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
                    <div class="bento-card d-flex flex-column">
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
                        <div class="bento-micro-surface">
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
                    <div class="bento-card d-flex flex-column">
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
                        <div class="bento-micro-surface">
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

            <!-- ============================================================= -->
            <!-- INTERACTIVE WORKFLOW PREVIEWS (TABS)                          -->
            <!-- ============================================================= -->
            <div class="card border mt-5 p-4 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                <div class="text-center mb-4">
                    <span class="badge badge-teal-soft mb-1.5"><i class="ti ti-eye me-1"></i>Interactive Simulation</span>
                    <h3 class="fw-bold text-dark mb-1">See SocialManager in Action</h3>
                    <p class="text-muted small">Explore how our background daemons handle real community interactions across different scenarios.</p>
                </div>

                <!-- Tabler Navigation Tabs -->
                <ul class="nav nav-tabs nav-fill mb-4" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#tab-ai-reply" class="nav-link active fw-semibold" data-bs-toggle="tab" aria-selected="true" role="tab">
                            <i class="ti ti-messages text-teal me-1.5 fs-2"></i>
                            <span>AI Comment Threading</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab-auto-dm" class="nav-link fw-semibold" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                            <i class="ti ti-mail-forward text-primary me-1.5 fs-2"></i>
                            <span>Comment-to-DM Flow</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab-troll-shield" class="nav-link fw-semibold" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                            <i class="ti ti-shield-lock text-danger me-1.5 fs-2"></i>
                            <span>Anti-Troll Shield</span>
                        </a>
                    </li>
                </ul>

                <!-- Tab Contents -->
                <div class="tab-content">
                    <!-- Tab 1: AI Comment Threading -->
                    <div class="tab-pane active show" id="tab-ai-reply" role="tabpanel">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-lg-5">
                                <span class="badge bg-teal-lt text-teal mb-2"><i class="ti ti-brain me-1"></i>Contextual Intelligence</span>
                                <h4 class="fw-bold text-dark mb-2">Natural Multilingual Replies Without Human Fatigue</h4>
                                <p class="text-muted small mb-3">
                                    When constituents or fans comment on YouTube or Facebook, the Gemini AI engine analyzes the tone, detects grievances or compliments, and drafts a tailored, human-like response in under 5 seconds.
                                </p>
                                <div class="d-flex align-items-center gap-2 small text-muted">
                                    <span class="badge bg-light text-secondary border"><i class="ti ti-clock me-1"></i>Response Time: ~3.8s</span>
                                    <span class="badge bg-light text-secondary border"><i class="ti ti-world me-1"></i>Hindi + Hinglish</span>
                                </div>
                            </div>

                            <div class="col-12 col-lg-7">
                                <div class="p-3.5 rounded-2 bg-light border">
                                    <!-- Incoming Comment -->
                                    <div class="p-3 rounded-2 bg-white border mb-2.5">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs rounded-circle bg-azure text-white">MK</span>
                                                <span class="fw-semibold small text-dark">Manoj Kumar</span>
                                                <span class="badge bg-danger-lt text-danger fs-8"><i class="ti ti-brand-youtube me-1"></i>YouTube</span>
                                            </div>
                                            <span class="text-muted fs-8">Incoming Comment</span>
                                        </div>
                                        <div class="small text-dark">
                                            "Sir hamare ward me road reconstruction ka kaam kab tak start hoga? Traffic bohot badh gaya hai."
                                        </div>
                                    </div>

                                    <!-- AI Detection Pipeline -->
                                    <div class="d-flex align-items-center gap-2 mb-2.5 px-2">
                                        <i class="ti ti-arrow-down text-teal"></i>
                                        <span class="badge badge-teal-soft fs-8">Sentiment: Public Inquiry / Civic Grievance (Score: 0.94)</span>
                                        <span class="badge badge-outline text-secondary fs-8">Tone: Humble & Respectful</span>
                                    </div>

                                    <!-- Live Published Thread Reply -->
                                    <div class="p-3 rounded-2 bg-white border-start border-teal border-3 ms-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs rounded-circle bg-teal text-white"><i class="ti ti-check"></i></span>
                                                <span class="fw-semibold small text-dark">Official Channel Reply</span>
                                                <span class="badge bg-teal-lt text-teal fs-8">Auto-Approved Live</span>
                                            </div>
                                            <span class="text-muted fs-8">Published in 3.8s</span>
                                        </div>
                                        <div class="small text-dark">
                                            "Manoj ji, namaskar. Ward me road reconstruction ka tender pass ho chuka hai aur next week se physical road work shuru ho raha hai. Aapki suvidha hamari prathmikta hai."
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Comment-to-DM Lead Capture -->
                    <div class="tab-pane" id="tab-auto-dm" role="tabpanel">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-lg-5">
                                <span class="badge bg-primary-lt text-primary mb-2"><i class="ti ti-mail-forward me-1"></i>Lead Generation</span>
                                <h4 class="fw-bold text-dark mb-2">Automated Conversion from Public Comments to Private DMs</h4>
                                <p class="text-muted small mb-3">
                                    Set keyword rules for specific campaign posts or channel-wide. When a follower asks for a link, brochure, or support, our Meta integration sends a private message to their Instagram Direct or Messenger inbox immediately.
                                </p>
                                <div class="d-flex align-items-center gap-2 small text-muted">
                                    <span class="badge bg-light text-secondary border"><i class="ti ti-bolt text-teal me-1"></i>Webhook Latency: &lt; 800ms</span>
                                    <span class="badge bg-light text-secondary border"><i class="ti ti-brand-instagram text-pink me-1"></i>Instagram Direct</span>
                                </div>
                            </div>

                            <div class="col-12 col-lg-7">
                                <div class="p-3.5 rounded-2 bg-light border">
                                    <!-- Public Post Interaction -->
                                    <div class="p-3 rounded-2 bg-white border mb-2.5">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs rounded-circle bg-pink text-white">PK</span>
                                                <span class="fw-semibold small text-dark">Priya Kapoor</span>
                                                <span class="badge bg-pink-lt text-pink fs-8"><i class="ti ti-brand-instagram me-1"></i>Instagram Post</span>
                                            </div>
                                            <span class="badge bg-light text-secondary border fs-8">Public Comment</span>
                                        </div>
                                        <div class="small text-dark">
                                            "Interested in joining the youth volunteer campaign. How do I register?"
                                        </div>
                                    </div>

                                    <!-- Automated Rule Trigger -->
                                    <div class="d-flex align-items-center gap-2 mb-2.5 px-2">
                                        <i class="ti ti-arrow-down text-primary"></i>
                                        <span class="badge badge-outline text-primary fs-8">Keyword Matched: "register"</span>
                                        <span class="badge bg-teal-lt text-teal fs-8">Auto DM Dispatched</span>
                                    </div>

                                    <!-- Private Instagram DM Window -->
                                    <div class="p-3 rounded-2 bg-white border border-primary border-opacity-25 ms-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-brand-instagram text-pink fs-3"></i>
                                                <span class="fw-semibold small text-dark">Private Instagram Direct Message</span>
                                            </div>
                                            <span class="badge bg-teal-lt text-teal fs-8">Delivered to Inbox</span>
                                        </div>
                                        <div class="small text-dark mb-1">
                                            "Hi Priya! Thank you for your enthusiasm to join our team. Here is your direct volunteer registration form link: <code>https://campaign.portal/register</code>. We look forward to working with you!"
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Silent Anti-Troll Shield -->
                    <div class="tab-pane" id="tab-troll-shield" role="tabpanel">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-lg-5">
                                <span class="badge bg-danger-lt text-danger mb-2"><i class="ti ti-shield-x me-1"></i>Brand Safety</span>
                                <h4 class="fw-bold text-dark mb-2">Zero-Latency Brand & Political Protection</h4>
                                <p class="text-muted small mb-3">
                                    Coordinated troll attacks and abusive comments can damage public reputation. Our toxicity filter silently auto-hides abusive comments using official platform APIs before other citizens or followers ever notice them.
                                </p>
                                <div class="d-flex align-items-center gap-2 small text-muted">
                                    <span class="badge bg-light text-secondary border"><i class="ti ti-eye-off text-danger me-1"></i>Silently Hidden</span>
                                    <span class="badge bg-light text-secondary border"><i class="ti ti-check text-teal me-1"></i>Community Safe</span>
                                </div>
                            </div>

                            <div class="col-12 col-lg-7">
                                <div class="p-3.5 rounded-2 bg-light border">
                                    <!-- Incoming Troll Comment -->
                                    <div class="p-3 rounded-2 bg-white border mb-2.5">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs rounded-circle bg-secondary text-white">TR</span>
                                                <span class="fw-semibold small text-dark">Anonymous Troll User</span>
                                                <span class="badge bg-primary-lt text-primary fs-8"><i class="ti ti-brand-facebook me-1"></i>Facebook Page</span>
                                            </div>
                                            <span class="text-muted fs-8">Incoming Comment</span>
                                        </div>
                                        <div class="small text-muted fst-italic">
                                            "[Abusive defamatory statement and unparliamentary language attacking campaign]"
                                        </div>
                                    </div>

                                    <!-- AI Detection Pipeline -->
                                    <div class="d-flex align-items-center gap-2 mb-2.5 px-2">
                                        <i class="ti ti-shield-alert text-danger"></i>
                                        <span class="badge bg-danger-lt text-danger fs-8">Toxicity Score: 0.98 (Severe Abuse)</span>
                                        <span class="badge badge-teal-soft fs-8">Auto-Hide Executed</span>
                                    </div>

                                    <!-- Action Result Card -->
                                    <div class="p-3 rounded-2 bg-white border-start border-danger border-3 ms-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-eye-off text-danger fs-3"></i>
                                                <span class="fw-semibold small text-dark">Hidden on Facebook & YouTube</span>
                                            </div>
                                            <span class="badge bg-secondary-lt text-secondary fs-8">Public Thread Clean</span>
                                        </div>
                                        <div class="small text-muted">
                                            Comment marked as <code>rejected</code> on YouTube and <code>is_hidden = true</code> on Meta Graph API. The troll is unaware their comment has been hidden, completely defusing engagement bait.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- STEP 4: TRANSPARENT PRICING PLANS                                         -->
    <!-- ========================================================================= -->
    <section id="pricing" class="py-6" style="background-color: #fafbfc;">
        <div class="container-xl">
            <div class="text-center mb-5">
                <span class="badge badge-teal-soft mb-2"><i class="ti ti-tag me-1"></i>Predictable Pricing</span>
                <h2 class="fw-bold text-dark mb-2 display-6">Simple Plans for Every Stage of Growth</h2>
                <p class="text-muted" style="max-width: 640px; margin: 0 auto; font-size: 1.05rem;">
                    From independent creators to full-scale political campaign war rooms. All plans include 24/7 background automation and official API compliance.
                </p>
            </div>

            <div class="row g-4 align-items-stretch justify-content-center">
                <!-- Starter Plan -->
                <div class="col-12 col-lg-4">
                    <div class="pricing-card d-flex flex-column">
                        <div class="mb-3">
                            <span class="badge bg-secondary-lt text-secondary fw-semibold mb-2">Starter Creator</span>
                            <h3 class="fw-bold text-dark mb-1">Single Channel</h3>
                            <p class="text-muted small">Ideal for solo creators and local constituency representatives.</p>
                        </div>

                        <div class="d-flex align-items-baseline gap-1 my-3 pb-3 border-bottom">
                            <span class="fs-4 text-muted fw-bold">&#8377;</span>
                            <span class="display-5 fw-extrabold text-dark">2,499</span>
                            <span class="text-muted small">/ month</span>
                        </div>

                        <ul class="list-unstyled small text-secondary mb-4 space-y-2">
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span><strong>1 Social Channel</strong> (Facebook, IG, or YouTube)</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Multi-Platform Content Scheduler</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Up to <strong>500 AI Comment Replies</strong> / mo</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Basic Anti-Troll Auto-Hide Shield</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>AES-256 Token Encryption at Rest</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 text-muted">
                                <i class="ti ti-x text-secondary"></i>
                                <span>Comment-to-DM Private Messaging</span>
                            </li>
                        </ul>

                        <div class="mt-auto">
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 py-2.5 fw-semibold">
                                Get Started
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pro Campaign Plan (Featured with Teal Glow) -->
                <div class="col-12 col-lg-4">
                    <div class="pricing-card pricing-card-featured d-flex flex-column position-relative">
                        <!-- Most Popular Tag -->
                        <div class="position-absolute top-0 end-0 translate-middle-y me-3">
                            <span class="badge bg-teal text-white py-1 px-2.5 fs-8">
                                <i class="ti ti-star me-1"></i>Most Popular
                            </span>
                        </div>

                        <div class="mb-3">
                            <span class="badge badge-teal-soft fw-semibold mb-2">Professional</span>
                            <h3 class="fw-bold text-dark mb-1">Campaign War Room</h3>
                            <p class="text-muted small">Built for growing public figures, election war rooms, and brands.</p>
                        </div>

                        <div class="d-flex align-items-baseline gap-1 my-3 pb-3 border-bottom">
                            <span class="fs-4 text-muted fw-bold">&#8377;</span>
                            <span class="display-5 fw-extrabold text-teal">6,999</span>
                            <span class="text-muted small">/ month</span>
                        </div>

                        <ul class="list-unstyled small text-secondary mb-4 space-y-2">
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span><strong>Up to 5 Social Accounts</strong> (FB, IG &amp; YouTube)</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span><strong>Unlimited Scheduled Posts</strong>, Reels &amp; Shorts</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span><strong>Unlimited AI Comment Replies</strong> (Gemini AI)</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span><strong>Comment-to-DM Engine</strong> (Messenger &amp; IG Direct)</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Adaptive Tone Selector (Humble, Patriotic, Firm)</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Real-Time Sentiment Intelligence &amp; Analytics</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Priority Background Queue Processing</span>
                            </li>
                        </ul>

                        <div class="mt-auto">
                            <a href="{{ route('login') }}" class="btn btn-teal w-100 py-2.5 shadow-sm fw-semibold">
                                <i class="ti ti-bolt me-1"></i>Start 14-Day Free Trial
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="col-12 col-lg-4">
                    <div class="pricing-card d-flex flex-column">
                        <div class="mb-3">
                            <span class="badge bg-secondary-lt text-secondary fw-semibold mb-2">Enterprise</span>
                            <h3 class="fw-bold text-dark mb-1">State / National Network</h3>
                            <p class="text-muted small">For major political organizations, agencies, and enterprise brands.</p>
                        </div>

                        <div class="d-flex align-items-baseline gap-1 my-3 pb-3 border-bottom">
                            <span class="fs-4 text-muted fw-bold">&#8377;</span>
                            <span class="display-5 fw-extrabold text-dark">19,999</span>
                            <span class="text-muted small">/ month</span>
                        </div>

                        <ul class="list-unstyled small text-secondary mb-4 space-y-2">
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span><strong>Unlimited Channels &amp; Team Seats</strong></span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Multi-Tenant Role Access (Admin / Manager / User)</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Custom Fine-Tuned AI Persona &amp; Knowledge Base</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Dedicated Cloud VM &amp; High-Throughput Queue</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>Custom Webhook Ingestion &amp; HMAC Verification</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="ti ti-check text-teal"></i>
                                <span>24/7 Dedicated Account Manager &amp; Phone SLA</span>
                            </li>
                        </ul>

                        <div class="mt-auto">
                            <a href="mailto:support@rezel.online?subject=Enterprise%20War%20Room%20Inquiry" class="btn btn-outline-secondary w-100 py-2.5 fw-semibold">
                                Contact Enterprise Team
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- STEP 4: ENTERPRISE SECURITY & RELIABILITY GUARANTEE                       -->
    <!-- ========================================================================= -->
    <section id="security" class="py-6 bg-white border-top border-bottom">
        <div class="container-xl">
            <div class="text-center mb-5">
                <span class="badge badge-teal-soft mb-2"><i class="ti ti-shield-check me-1"></i>Security Architecture</span>
                <h2 class="fw-bold text-dark mb-2 display-6">Enterprise-Grade Security by Design</h2>
                <p class="text-muted" style="max-width: 640px; margin: 0 auto; font-size: 1.05rem;">
                    Because managing influential public profiles requires uncompromising safety, our infrastructure adheres to strict cryptographic standards.
                </p>
            </div>

            <div class="row g-4">
                <!-- Security Pillar 1 -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="security-card d-flex flex-column">
                        <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal mb-3">
                            <i class="ti ti-key fs-1"></i>
                        </span>
                        <h4 class="fw-bold text-dark mb-2">AES-256 Token Encryption</h4>
                        <p class="text-muted small mb-0">
                            All OAuth access tokens and refresh tokens are encrypted at rest using AES-256-CBC cipher with HMAC authentication. Plaintext tokens are never stored.
                        </p>
                    </div>
                </div>

                <!-- Security Pillar 2 -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="security-card d-flex flex-column">
                        <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal mb-3">
                            <i class="ti ti-certificate fs-1"></i>
                        </span>
                        <h4 class="fw-bold text-dark mb-2">HMAC SHA-256 Webhook Check</h4>
                        <p class="text-muted small mb-0">
                            Every incoming Meta webhook is verified against Meta's cryptographic signature header (<code>X-Hub-Signature-256</code>), blocking 100% of unauthorized or forged requests.
                        </p>
                    </div>
                </div>

                <!-- Security Pillar 3 -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="security-card d-flex flex-column">
                        <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal mb-3">
                            <i class="ti ti-fingerprint fs-1"></i>
                        </span>
                        <h4 class="fw-bold text-dark mb-2">Zero Password Storage</h4>
                        <p class="text-muted small mb-0">
                            Authentication occurs strictly via official Meta Facebook Login and Google OAuth 2.0 prompts. Your social account passwords are never accessed or stored.
                        </p>
                    </div>
                </div>

                <!-- Security Pillar 4 -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="security-card d-flex flex-column">
                        <span class="avatar avatar-md rounded-2 bg-teal-lt text-teal mb-3">
                            <i class="ti ti-server-2 fs-1"></i>
                        </span>
                        <h4 class="fw-bold text-dark mb-2">Azure Cloud Infrastructure</h4>
                        <p class="text-muted small mb-0">
                            Hosted on Microsoft Azure Virtual Machines with Docker containerization, Redis in-memory queue management, and 99.9% automated uptime monitoring.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- STEP 5: FREQUENTLY ASKED QUESTIONS (ACCORDION)                            -->
    <!-- ========================================================================= -->
    <section id="faq" class="py-6 border-bottom bg-white">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="text-center mb-5">
                <span class="badge badge-teal-soft px-3 py-1.5 rounded-pill mb-2">
                    <i class="ti ti-help-circle me-1"></i>Frequently Asked Questions
                </span>
                <h2 class="display-6 fw-bold text-dark mb-2">
                    Everything You Need to Know
                </h2>
                <p class="text-muted fs-5 mx-auto" style="max-width: 680px;">
                    Direct technical clarity regarding Meta & Google API safety, rate limits, comment triggers, and data encryption.
                </p>
            </div>

            <!-- Accordion Wrapper -->
            <div class="row justify-content-center">
                <div class="col-12 col-lg-9">
                    <div class="accordion accordion-custom" id="faqAccordion">
                        
                        <!-- FAQ 1 -->
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="ti ti-shield-check text-teal fs-2"></i>
                                        Can my Facebook, Instagram, or YouTube account get banned or restricted?
                                    </span>
                                </button>
                            </h3>
                            <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    No. Unlike unofficial scraping bots or browser automation scripts that risk account bans, SocialManager interacts exclusively through officially certified Meta Graph API (v22.0) and Google Cloud YouTube Data API (v3) protocols. Every comment response and scheduled post adheres strictly to platform rate-limiting and messaging policies. Your accounts remain 100% compliant and secure within enterprise developer boundaries.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="ti ti-message-share text-teal fs-2"></i>
                                        How does the Comment-to-DM lead capture trigger work on Instagram and Facebook?
                                    </span>
                                </button>
                            </h3>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    When a follower leaves a comment on your posts or reels, Meta's Realtime Webhooks immediately push the payload to our secure server in under 300 milliseconds. SocialManager evaluates the comment against your configured trigger keywords (such as PRICE, LINK, or DEMO) or natural language intent. The engine instantly posts a polite public reply confirming receipt while simultaneously dispatching a personalized private Direct Message (DM) to their inbox containing the relevant link, brochure, or checkout page.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="ti ti-brand-youtube text-teal fs-2"></i>
                                        How does YouTube comment automation work without exceeding Google API quotas?
                                    </span>
                                </button>
                            </h3>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    YouTube's Data API provides a default allocation of 10,000 units per day per Google Cloud project. Since YouTube comments operate on an optimized 10-minute cron check rather than streaming webhooks, polling your channel consumes only 1 unit per cycle (144 units per 24 hours). Posting an AI reply uses 50 units. The built-in rate-limiter and duplicate reply detector ensure your project never approaches or exceeds Google's quota limits.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="ti ti-user-check text-teal fs-2"></i>
                                        Can I customize the AI's personality, tone, and brand safety guidelines?
                                    </span>
                                </button>
                            </h3>
                            <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes. SocialManager features a dedicated AI Personas manager where you define exact brand voice guidelines, industry vocabulary, predefined knowledge base answers, and negative-sentiment escalation rules. You can also specify banned keywords and competitor filters so the AI never generates off-brand or risky statements.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeading5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="ti ti-lock text-teal fs-2"></i>
                                        How are social tokens and sensitive client credentials protected?
                                    </span>
                                </button>
                            </h3>
                            <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    All platform OAuth tokens are encrypted at rest using AES-256-CBC cipher with application-level key derivation via Laravel's native Crypt layer. Decrypted tokens are strictly excluded from JSON serializations, front-end payloads, and log files. Webhooks are validated using HMAC SHA-256 signatures, and we never access, request, or store your personal social media passwords.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeading6">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse6">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="ti ti-toggle-right text-teal fs-2"></i>
                                        Can I review AI-generated responses before they get published?
                                    </span>
                                </button>
                            </h3>
                            <div id="faqCollapse6" class="accordion-collapse collapse" aria-labelledby="faqHeading6" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes. SocialManager offers two operational modes. You can set it to Full Autopilot for immediate, hands-free 24/7 engagement, or switch to Review Queue mode where AI drafts replies and stages them for one-click human approval by your social media team.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- STEP 5: HIGH-CONVERSION PRE-FOOTER CTA CARD                               -->
    <!-- ========================================================================= -->
    <section class="py-6 bg-light">
        <div class="container-xl">
            <div class="cta-banner-card p-4 p-md-5 text-white">
                <div class="row align-items-center gy-4">
                    <div class="col-12 col-lg-8">
                        <span class="badge badge-teal-soft text-teal bg-teal-lt px-3 py-1.5 rounded-pill mb-3">
                            <i class="ti ti-rocket me-1.5"></i>Supercharge Your Social Operations
                        </span>
                        <h2 class="display-6 fw-bold mb-3 text-white">
                            Ready to Turn Comments into Conversations and Conversions?
                        </h2>
                        <p class="text-white-50 fs-5 mb-4" style="max-width: 650px;">
                            Eliminate manual comment tracking, respond to customers in under 5 seconds, and drive qualified leads straight into your direct messages across Facebook, Instagram, and YouTube.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="btn btn-teal btn-lg px-4 py-2.5 fw-semibold shadow-sm">
                                <i class="ti ti-bolt me-1.5"></i>Start 14-Day Free Trial
                            </a>
                            <a href="#platforms" class="btn btn-outline-light btn-lg px-4 py-2.5">
                                <i class="ti ti-device-desktop me-1.5"></i>Explore Platform Integrations
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card bg-white bg-opacity-10 border border-white border-opacity-10 p-3.5 rounded-3">
                            <div class="d-flex flex-column gap-2.5">
                                <div class="d-flex align-items-center text-white small">
                                    <i class="ti ti-circle-check text-teal fs-2 me-2"></i>
                                    <span>14-day full-featured free trial</span>
                                </div>
                                <div class="d-flex align-items-center text-white small">
                                    <i class="ti ti-circle-check text-teal fs-2 me-2"></i>
                                    <span>No credit card required to start</span>
                                </div>
                                <div class="d-flex align-items-center text-white small">
                                    <i class="ti ti-circle-check text-teal fs-2 me-2"></i>
                                    <span>AES-256 token encryption at rest</span>
                                </div>
                                <div class="d-flex align-items-center text-white small">
                                    <i class="ti ti-circle-check text-teal fs-2 me-2"></i>
                                    <span>Official Meta & Google APIs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- STEP 5: MODERN 4-COLUMN FOOTER WITH COMPLIANCE & LEGAL LINKS              -->
    <!-- ========================================================================= -->
    <footer class="landing-footer pt-6 pb-4">
        <div class="container-xl">
            <div class="row g-4 mb-5">
                <!-- Col 1: Brand Info -->
                <div class="col-12 col-lg-4">
                    <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
                        <span class="avatar avatar-sm rounded-2 bg-teal-lt text-teal">
                            <i class="ti ti-brand-campaignmonitor fs-2"></i>
                        </span>
                        <span class="fw-bold fs-3 text-white">Social<span class="text-teal">Manager</span></span>
                        <span class="badge badge-teal-soft ms-1">v2.4</span>
                    </a>
                    <p class="text-muted small mb-3 pe-lg-4">
                        Enterprise-grade social media publishing, AI comment auto-reply, and automated direct messaging built for creators, businesses, and digital agencies.
                    </p>
                    <div class="d-inline-flex align-items-center gap-2 px-2.5 py-1.5 rounded-2 bg-dark border border-secondary border-opacity-25 small">
                        <span class="status-indicator-live"></span>
                        <span class="text-white-50">Systems Status: <strong class="text-teal">All APIs Operational</strong></span>
                    </div>
                </div>

                <!-- Col 2: Platform Capabilities -->
                <div class="col-6 col-md-3 col-lg-2">
                    <h5 class="footer-heading">Capabilities</h5>
                    <ul class="footer-link-list">
                        <li><a href="#features"><i class="ti ti-calendar-event text-teal"></i>Content Scheduler</a></li>
                        <li><a href="#features"><i class="ti ti-robot text-teal"></i>AI Auto-Responder</a></li>
                        <li><a href="#features"><i class="ti ti-message-share text-teal"></i>Comment-to-DM Engine</a></li>
                        <li><a href="#features"><i class="ti ti-shield-half text-teal"></i>Anti-Troll Shield</a></li>
                        <li><a href="#features"><i class="ti ti-chart-dots text-teal"></i>Sentiment Analytics</a></li>
                        <li><a href="#security"><i class="ti ti-lock text-teal"></i>Encrypted Token Vault</a></li>
                    </ul>
                </div>

                <!-- Col 3: Supported Ecosystem -->
                <div class="col-6 col-md-3 col-lg-3">
                    <h5 class="footer-heading">Supported Ecosystem</h5>
                    <ul class="footer-link-list">
                        <li><a href="#platforms"><i class="ti ti-brand-facebook text-teal"></i>Facebook Pages API</a></li>
                        <li><a href="#platforms"><i class="ti ti-brand-instagram text-teal"></i>Instagram Professional API</a></li>
                        <li><a href="#platforms"><i class="ti ti-brand-youtube text-teal"></i>YouTube Data API v3</a></li>
                        <li><a href="#platforms"><i class="ti ti-brand-google text-teal"></i>Google Cloud OAuth 2.0</a></li>
                        <li><a href="#security"><i class="ti ti-brand-azure text-teal"></i>Microsoft Azure Cloud</a></li>
                        <li><a href="#security"><i class="ti ti-webhook text-teal"></i>HMAC SHA-256 Webhooks</a></li>
                    </ul>
                </div>

                <!-- Col 4: Trust & Compliance -->
                <div class="col-12 col-md-6 col-lg-3">
                    <h5 class="footer-heading">Trust & Compliance</h5>
                    <ul class="footer-link-list">
                        <li><a href="{{ url('/privacy') }}"><i class="ti ti-file-shield text-teal"></i>Privacy Policy</a></li>
                        <li><a href="{{ url('/terms') }}"><i class="ti ti-file-text text-teal"></i>Terms of Service</a></li>
                        <li><a href="{{ url('/data-deletion') }}"><i class="ti ti-trash text-teal"></i>Data Deletion Policy</a></li>
                        <li><a href="#security"><i class="ti ti-shield-lock text-teal"></i>Security Architecture</a></li>
                        <li><a href="{{ route('login') }}"><i class="ti ti-login text-teal"></i>Client Portal Login</a></li>
                    </ul>
                </div>
            </div>

            <!-- Divider -->
            <hr class="border-secondary border-opacity-25 my-4">

            <!-- Bottom Row: Copyright & Disclaimer -->
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-muted small">
                <span>&copy; {{ date('Y') }} <strong>SocialManager</strong>. Enterprise Social Media Infrastructure. All rights reserved.</span>
                <a href="#" class="text-white-50 text-decoration-none">
                    <i class="ti ti-arrow-up me-1"></i>Back to Top
                </a>
            </div>

            <div class="mt-3 text-muted" style="font-size: 0.76rem; color: #64748b !important;">
                Disclaimer: SocialManager is an independent SaaS automation platform. Meta, Facebook, and Instagram are registered trademarks of Meta Platforms, Inc. YouTube and Google are registered trademarks of Google LLC. SocialManager integrates exclusively through officially sanctioned, authenticated public API endpoints.
            </div>
        </div>
    </footer>

    <!-- Tabler Core JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
</body>
</html>
