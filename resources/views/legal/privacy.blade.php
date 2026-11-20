<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Privacy Policy - SocialManager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <style>
        body { background-color: #f8fafc; color: #1e293b; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .legal-card { max-width: 860px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); padding: 40px; border: 1px solid #e2e8f0; }
        h1 { font-size: 2rem; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        h2 { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-top: 28px; margin-bottom: 12px; }
        p, li { font-size: 0.95rem; line-height: 1.7; color: #475569; }
        .badge-date { font-size: 0.8rem; background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 999px; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="legal-card">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h1 class="mb-1"><i class="ti ti-shield-lock text-primary me-2"></i>Privacy Policy</h1>
                <span class="badge-date">Last Updated: November 20, 2026</span>
            </div>
            <a href="/" class="btn btn-outline-secondary btn-sm"><i class="ti ti-arrow-left me-1"></i>Back to Home</a>
        </div>

        <p>Welcome to <strong>SocialManager</strong> ("we", "our", or "us"). We provide an AI-powered social media management and engagement platform helping organizations, creators, and public figures manage content and community interactions across Facebook, Instagram, and YouTube.</p>

        <h2>1. Information We Collect</h2>
        <p>When you authenticate your social media accounts via OAuth 2.0 (Meta Facebook Login and Google YouTube OAuth), we receive authorization to access certain profile and page assets as granted by you:</p>
        <ul>
            <li><strong>Profile & Channel Information:</strong> Page name, ID, handle, profile picture, subscriber and follower counts.</li>
            <li><strong>Engagement Data:</strong> Comments, public replies, and mentions on your connected posts and videos.</li>
            <li><strong>OAuth Access Credentials:</strong> Short-lived and long-lived OAuth tokens and refresh tokens strictly used to perform scheduled publishing and engagement synchronization on your behalf.</li>
        </ul>

        <h2>2. How We Use Your Information</h2>
        <p>We use your information solely to provide, operate, and enhance our social management services:</p>
        <ul>
            <li>Publishing scheduled posts, photos, and videos to your connected Facebook, Instagram, and YouTube accounts.</li>
            <li>Synchronizing public comments and drafting or dispatching authorized AI replies and moderation actions.</li>
            <li>Displaying performance and analytics metrics on your secure dashboard.</li>
        </ul>
        <p><strong>We do not sell, rent, or trade your personal or social media data to any third parties or data brokers.</strong></p>

        <h2>3. Data Storage & Security</h2>
        <p>All access tokens and refresh tokens are securely encrypted at rest and in transit using industry-standard TLS encryption. Our databases are secured behind enterprise cloud firewalls with restricted access controls.</p>

        <h2>4. Third-Party Platform Compliance</h2>
        <p>Our platform strictly adheres to the following platform policies:</p>
        <ul>
            <li><strong>Meta Platform Terms:</strong> Compliant with Meta Developer Terms and Data Policy for Facebook & Instagram Graph APIs.</li>
            <li><strong>YouTube API Services:</strong> By using our YouTube integration, you agree to be bound by the <a href="https://www.youtube.com/t/terms" target="_blank" rel="noopener">YouTube Terms of Service</a> and <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google Privacy Policy</a>.</li>
        </ul>

        <h2>5. Data Retention & Deletion</h2>
        <p>You can revoke access to your accounts at any time via the Connected Accounts dashboard or directly through your Facebook or Google Security Settings. For automated or manual data deletion instructions, visit our <a href="{{ url('/data-deletion') }}">User Data Deletion page</a>.</p>

        <h2>6. Contact Us</h2>
        <p>If you have any questions or data deletion requests, please contact our Data Protection Officer at: <a href="mailto:support@rezel.online">support@rezel.online</a>.</p>
    </div>
</div>
</body>
</html>
