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
        /* DARK COMMAND CENTER (BIONIC BENTO GRID - BILLION DOLLAR SAAS)         */
        /* ===================================================================== */
        .dark-command-section {
            background: #090d16;
            position: relative;
            padding: 105px 0;
            overflow: hidden;
            color: #f8fafc;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dark-mesh-glow-left {
            position: absolute;
            top: 5%;
            left: 5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(12, 166, 120, 0.18) 0%, transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .dark-mesh-glow-right {
            position: absolute;
            bottom: 5%;
            right: 5%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.14) 0%, transparent 70%);
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }

        .dark-command-header {
            position: relative;
            z-index: 1;
            max-width: 720px;
            margin: 0 auto 60px auto;
            text-align: center;
        }

        .dark-section-eyebrow {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #34d399;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            padding: 5px 14px;
            border-radius: 999px;
            display: inline-block;
            margin-bottom: 14px;
        }

        .dark-section-headline {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.035em;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 14px;
        }

        .dark-section-subhead {
            font-size: 1.08rem;
            color: #94a3b8;
            line-height: 1.65;
            margin: 0;
            font-weight: 400;
        }

        .dark-bento-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 34px;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 16px 36px -10px rgba(0, 0, 0, 0.6);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1;
        }

        .dark-bento-card:hover {
            transform: translateY(-5px);
            border-color: rgba(12, 166, 120, 0.45);
            box-shadow: 0 24px 50px -10px rgba(12, 166, 120, 0.22);
            background: rgba(15, 23, 42, 0.85);
        }

        .dark-card-badge {
            font-size: 0.74rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        .dark-badge-teal {
            background: rgba(12, 166, 120, 0.15);
            border-color: rgba(12, 166, 120, 0.3);
            color: #34d399;
        }
        .dark-badge-rose {
            background: rgba(244, 63, 94, 0.15);
            border-color: rgba(244, 63, 94, 0.3);
            color: #fb7185;
        }
        .dark-badge-blue {
            background: rgba(59, 130, 246, 0.15);
            border-color: rgba(59, 130, 246, 0.3);
            color: #60a5fa;
        }

        .dark-card-title {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin: 16px 0 8px 0;
        }

        .dark-card-desc {
            font-size: 0.94rem;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 24px;
            font-weight: 400;
        }

        /* Dark Visual Surface Components */
        .dark-visual-panel {
            background: rgba(2, 6, 23, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 16px 18px;
            margin-top: auto;
        }

        /* Scheduler Timeline Slot Strip */
        .dark-timeline-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 9px 12px;
            margin-bottom: 8px;
            font-size: 0.82rem;
            transition: all 0.2s ease;
        }
        .dark-timeline-row:last-child {
            margin-bottom: 0;
        }
        .dark-timeline-row:hover {
            background: rgba(30, 41, 59, 0.7);
            border-color: rgba(12, 166, 120, 0.3);
        }

        /* AI Voice Visualizer Bars */
        .ai-voice-equalizer {
            display: flex;
            align-items: center;
            gap: 4px;
            height: 20px;
        }
        .equalizer-bar {
            width: 3px;
            background: #10b981;
            border-radius: 2px;
            animation: eqDance 1.2s ease-in-out infinite alternate;
        }
        .equalizer-bar:nth-child(2) { animation-delay: 0.2s; height: 16px; }
        .equalizer-bar:nth-child(3) { animation-delay: 0.4s; height: 12px; }
        .equalizer-bar:nth-child(4) { animation-delay: 0.1s; height: 18px; }
        .equalizer-bar:nth-child(5) { animation-delay: 0.3s; height: 10px; }

        @keyframes eqDance {
            0% { transform: scaleY(0.4); }
            100% { transform: scaleY(1.2); }
        }

        .dark-pill-chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            padding: 4px 11px;
            font-size: 0.76rem;
            color: #cbd5e1;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .dark-pill-active {
            background: rgba(12, 166, 120, 0.18);
            border-color: rgba(12, 166, 120, 0.35);
            color: #34d399;
            font-weight: 600;
        }

        /* Interactive DM Trigger Flow Strip */
        .dm-flow-pipeline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            padding: 4px 0;
        }
        .dm-flow-box {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 0.75rem;
            color: #e2e8f0;
        }
        .dm-flow-arrow {
            color: #10b981;
            font-size: 1rem;
            animation: pulseArrow 1.5s infinite;
        }
        @keyframes pulseArrow {
            0%, 100% { transform: translateX(0); opacity: 0.6; }
            50% { transform: translateX(4px); opacity: 1; }
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
                Automate multi-platform publishing, 24/7 contextual AI comment replies, and instant private DM lead capture across Facebook, Instagram, and YouTube.
            </p>

            <!-- CTA Buttons -->
            <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                <a href="{{ route('login') }}" class="btn btn-teal btn-lg px-4 py-2.5">
                    <i class="ti ti-bolt me-1.5"></i>Start Free Trial
                </a>
                <a href="#features" class="btn btn-outline-secondary btn-lg px-4 py-2.5">
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
    <!-- STEP 2: CORE CAPABILITIES (BIONIC DARK SAAS BENTO GRID)                   -->
    <!-- ========================================================================= -->
    <section id="features" class="dark-command-section">
        <!-- Ambient Glowing Background Meshes -->
        <div class="dark-mesh-glow-left"></div>
        <div class="dark-mesh-glow-right"></div>

        <div class="container-xl position-relative" style="z-index: 1;">
            <!-- Section Header -->
            <div class="dark-command-header">
                <span class="dark-section-eyebrow">COMMAND CENTER CORE</span>
                <h2 class="dark-section-headline">Engineered for High-Stakes Social Operations</h2>
                <p class="dark-section-subhead">
                    Every mission-critical tool built to publish rich media, moderate thousands of follower inquiries, and protect brand reputation around the clock.
                </p>
            </div>

            <!-- Bento Grid -->
            <div class="row g-4">
                <!-- Bento Card 1: Content Publishing & Drag-Drop Calendar (7 Columns) -->
                <div class="col-12 col-lg-7">
                    <div class="dark-bento-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="avatar avatar-md rounded-3 bg-teal-lt text-teal">
                                <i class="ti ti-calendar-event fs-1"></i>
                            </span>
                            <span class="dark-card-badge dark-badge-teal">
                                <i class="ti ti-clock me-1"></i>1-Minute Background Daemon
                            </span>
                        </div>
                        <h3 class="dark-card-title">Unified Multi-Platform Content Scheduler</h3>
                        <p class="dark-card-desc">
                            Compose your message once, attach high-res images or 1080p video, and schedule across Facebook, Instagram, and YouTube. Our automated server daemon executes scheduled publishes every 60 seconds with zero manual intervention.
                        </p>

                        <!-- Visual Software Timeline Surface -->
                        <div class="dark-visual-panel">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fs-8 text-secondary fw-semibold">LIVE SCHEDULE QUEUE</span>
                                <span class="fs-8 text-teal d-inline-flex align-items-center gap-1">
                                    <span class="radar-dot-pulse"></span> Daemon Active
                                </span>
                            </div>

                            <!-- Timeline Item 1 -->
                            <div class="dark-timeline-row">
                                <div class="d-flex align-items-center gap-2 text-truncate">
                                    <span class="avatar avatar-xs rounded-2 bg-red-lt text-danger">
                                        <i class="ti ti-brand-youtube"></i>
                                    </span>
                                    <span class="text-truncate text-light fw-medium">Budget 2026 Analysis - Full Video</span>
                                </div>
                                <span class="badge bg-teal-lt text-teal fs-8">1080p Resumable</span>
                            </div>

                            <!-- Timeline Item 2 -->
                            <div class="dark-timeline-row">
                                <div class="d-flex align-items-center gap-2 text-truncate">
                                    <span class="avatar avatar-xs rounded-2 bg-pink-lt text-pink">
                                        <i class="ti ti-brand-instagram"></i>
                                    </span>
                                    <span class="text-truncate text-light fw-medium">Reel: 5 Important Welfare Schemes</span>
                                </div>
                                <span class="badge bg-purple-lt text-purple fs-8">9:16 Vertical</span>
                            </div>

                            <!-- Timeline Item 3 -->
                            <div class="dark-timeline-row">
                                <div class="d-flex align-items-center gap-2 text-truncate">
                                    <span class="avatar avatar-xs rounded-2 bg-blue-lt text-primary">
                                        <i class="ti ti-brand-facebook"></i>
                                    </span>
                                    <span class="text-truncate text-light fw-medium">Public Grievance Camp Announcement</span>
                                </div>
                                <span class="badge bg-blue-lt text-primary fs-8">Page Feed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Card 2: Adaptive AI Personas (5 Columns) -->
                <div class="col-12 col-lg-5">
                    <div class="dark-bento-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="avatar avatar-md rounded-3 bg-teal-lt text-teal">
                                <i class="ti ti-brain fs-1"></i>
                            </span>
                            <span class="dark-card-badge dark-badge-teal">
                                Gemini, OpenAI, Qwen
                            </span>
                        </div>
                        <h3 class="dark-card-title">Adaptive AI Personas</h3>
                        <p class="dark-card-desc">
                            Customize the response voice for each channel. AI analyzes sentiment, detects nuance, and drafts authentic replies in your selected voice.
                        </p>

                        <!-- Visual AI Tuner Surface -->
                        <div class="dark-visual-panel">
                            <!-- Equalizer Visualizer & Active Persona -->
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white border-opacity-10">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="ai-voice-equalizer">
                                        <div class="equalizer-bar" style="height: 14px;"></div>
                                        <div class="equalizer-bar"></div>
                                        <div class="equalizer-bar"></div>
                                        <div class="equalizer-bar"></div>
                                        <div class="equalizer-bar"></div>
                                    </div>
                                    <span class="fs-8 text-light fw-bold">Active Voice Persona</span>
                                </div>
                                <span class="dark-pill-chip dark-pill-active">
                                    <i class="ti ti-check"></i>Humble & Respectful
                                </span>
                            </div>

                            <!-- Configurable Tones -->
                            <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                                <span class="dark-pill-chip">Patriotic & Inspiring</span>
                                <span class="dark-pill-chip">Assertive & Firm</span>
                                <span class="dark-pill-chip">Formal & Official</span>
                            </div>

                            <!-- Languages Supported -->
                            <div class="d-flex align-items-center gap-2 text-muted fs-8">
                                <span>Languages:</span>
                                <span class="badge bg-teal-lt text-teal fs-8">Hindi + Hinglish</span>
                                <span class="badge bg-dark text-secondary fs-8">Pure Hindi</span>
                                <span class="badge bg-dark text-secondary fs-8">English</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Card 3: Instant Comment-to-DM (4 Columns) -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="dark-bento-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="avatar avatar-md rounded-3 bg-teal-lt text-teal">
                                <i class="ti ti-mail-forward fs-1"></i>
                            </span>
                            <span class="dark-card-badge dark-badge-blue">
                                Private Replies API
                            </span>
                        </div>
                        <h3 class="dark-card-title">Automated Comment-to-DM</h3>
                        <p class="dark-card-desc">
                            Convert public interactions into private inquiries. When a user comments specific keywords on Instagram or Facebook, send an instant DM with details or links.
                        </p>

                        <!-- Visual Interactive Pipeline Surface -->
                        <div class="dark-visual-panel">
                            <div class="dm-flow-pipeline mb-2">
                                <div class="dm-flow-box">
                                    <i class="ti ti-message text-teal me-1"></i>Comment: "link"
                                </div>
                                <i class="ti ti-arrow-right dm-flow-arrow"></i>
                                <div class="dm-flow-box" style="border-color: rgba(12, 166, 120, 0.4);">
                                    <i class="ti ti-send text-teal me-1"></i>Private DM (0.8s)
                                </div>
                            </div>
                            <div class="fs-8 text-muted d-flex justify-content-between">
                                <span>Rule: Contains Keyword</span>
                                <span class="text-teal fw-semibold">100% Meta Compliant</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Card 4: 24/7 Anti-Troll Shield (4 Columns) -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="dark-bento-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="avatar avatar-md rounded-3 bg-teal-lt text-teal">
                                <i class="ti ti-shield-lock fs-1"></i>
                            </span>
                            <span class="dark-card-badge dark-badge-rose">
                                Zero Human Delay
                            </span>
                        </div>
                        <h3 class="dark-card-title">24/7 Anti-Troll & Abuse Shield</h3>
                        <p class="dark-card-desc">
                            Our AI toxicity classification scans incoming comments for abusive language, hate speech, and troll attacks, automatically hiding them from public view in 0.3 seconds.
                        </p>

                        <!-- Visual Toxicity Shield HUD Surface -->
                        <div class="dark-visual-panel">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fs-8 text-danger fw-semibold"><i class="ti ti-alert-triangle me-1"></i>Threat Detected</span>
                                <span class="badge bg-danger-lt text-danger fs-8">Toxicity 96%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between text-muted fs-8">
                                <span class="text-truncate">Hate comment hidden</span>
                                <span class="text-teal fw-semibold"><i class="ti ti-check me-0.5"></i>Auto-Shielded</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Card 5: Real-Time Political Sentiment War Room (4 Columns) -->
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="dark-bento-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="avatar avatar-md rounded-3 bg-teal-lt text-teal">
                                <i class="ti ti-chart-dots-3 fs-1"></i>
                            </span>
                            <span class="dark-card-badge dark-badge-teal">
                                Sentiment War Room
                            </span>
                        </div>
                        <h3 class="dark-card-title">Live Sentiment Intelligence</h3>
                        <p class="dark-card-desc">
                            Know exactly what your constituents and customers think. Real-time categorization of comments into Positive, Grievances, and Complaints with interactive trend graphs.
                        </p>

                        <!-- Visual Sentiment Meter Surface -->
                        <div class="dark-visual-panel">
                            <div class="d-flex align-items-center justify-content-between small mb-1.5">
                                <span class="fw-semibold text-light">Constituent Sentiment</span>
                                <span class="text-teal fw-bold">84% Positive</span>
                            </div>
                            <div class="progress progress-sm mb-2" style="background: rgba(255, 255, 255, 0.08); height: 6px;">
                                <div class="progress-bar bg-teal" style="width: 84%"></div>
                                <div class="progress-bar bg-warning" style="width: 12%"></div>
                                <div class="progress-bar bg-danger" style="width: 4%"></div>
                            </div>
                            <div class="d-flex justify-content-between fs-8 text-muted">
                                <span><i class="ti ti-trending-up text-teal me-0.5"></i>+12.4% Trust Index</span>
                                <span>Meta & YouTube</span>
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
                    <button class="apple-faq-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                        <span>Can my Facebook, Instagram, or YouTube account get banned or restricted?</span>
                        <span class="apple-faq-icon"><i class="ti ti-plus"></i></span>
                    </button>
                    <div id="faqCollapse1" class="collapse show" data-bs-parent="#faqAccordion">
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
                        <li><a href="#faq"><i class="ti ti-lock text-teal"></i>Encrypted Token Vault</a></li>
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
                        <li><a href="#faq"><i class="ti ti-brand-azure text-teal"></i>Microsoft Azure Cloud</a></li>
                        <li><a href="#faq"><i class="ti ti-webhook text-teal"></i>HMAC SHA-256 Webhooks</a></li>
                    </ul>
                </div>

                <!-- Col 4: Trust & Compliance -->
                <div class="col-12 col-md-6 col-lg-3">
                    <h5 class="footer-heading">Trust & Compliance</h5>
                    <ul class="footer-link-list">
                        <li><a href="{{ url('/privacy') }}"><i class="ti ti-file-shield text-teal"></i>Privacy Policy</a></li>
                        <li><a href="{{ url('/terms') }}"><i class="ti ti-file-text text-teal"></i>Terms of Service</a></li>
                        <li><a href="{{ url('/data-deletion') }}"><i class="ti ti-trash text-teal"></i>Data Deletion Policy</a></li>
                        <li><a href="#faq"><i class="ti ti-shield-lock text-teal"></i>Security Architecture</a></li>
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
