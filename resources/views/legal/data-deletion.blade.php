<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>User Data Deletion Instructions - SocialManager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <style>
        body { background-color: #f8fafc; color: #1e293b; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .legal-card { max-width: 860px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); padding: 40px; border: 1px solid #e2e8f0; }
        h1 { font-size: 2rem; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        h2 { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-top: 28px; margin-bottom: 12px; }
        p, li { font-size: 0.95rem; line-height: 1.7; color: #475569; }
        .step-box { background: #f1f5f9; border-left: 4px solid #0054a6; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="legal-card">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h1 class="mb-1"><i class="ti ti-trash text-danger me-2"></i>User Data Deletion Instructions</h1>
                <span class="badge bg-secondary text-white">Meta Data Deletion Callback & Compliance</span>
            </div>
            <a href="/" class="btn btn-outline-secondary btn-sm"><i class="ti ti-arrow-left me-1"></i>Back to Home</a>
        </div>

        @if(!empty($code))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="ti ti-circle-check fs-2"></i>
                <div>
                    <strong>Data Deletion Request Acknowledged!</strong><br>
                    Confirmation Tracking Code: <code>{{ $code }}</code>. Your associated access tokens and synced profile data are queued for permanent deletion.
                </div>
            </div>
        @endif

        <p>In compliance with Meta Platform Terms and General Data Protection Regulations, <strong>SocialManager</strong> provides a transparent and automated process for users to delete their personal and social account data from our databases.</p>

        <h2>How to Delete Your Data</h2>
        <p>You can remove your data using any of the three methods below:</p>

        <div class="step-box">
            <h4 class="mb-2 text-dark"><i class="ti ti-circle-number-1 text-primary me-2"></i>Method 1: Instant Self-Service via Dashboard</h4>
            <p class="mb-0">Log in to your SocialManager account, go to <strong>Connected Accounts</strong>, locate the social account you wish to remove, click the three-dots menu (<i class="ti ti-dots-vertical"></i>) and select <strong>Delete Account</strong>. This immediately purges all stored OAuth access tokens, refresh tokens, and cached engagement data from our database.</p>
        </div>

        <div class="step-box">
            <h4 class="mb-2 text-dark"><i class="ti ti-circle-number-2 text-primary me-2"></i>Method 2: Remove App via Facebook Settings</h4>
            <p class="mb-0">Go to your Facebook Profile -> <strong>Settings & Privacy</strong> -> <strong>Settings</strong> -> <strong>Apps and Websites</strong>. Find <strong>DevApp / SocialManager</strong> and click <strong>Remove</strong>. This triggers our automated data deletion callback to instantly purge your account credentials.</p>
        </div>

        <div class="step-box">
            <h4 class="mb-2 text-dark"><i class="ti ti-circle-number-3 text-primary me-2"></i>Method 3: Direct Email Request</h4>
            <p class="mb-0">Send an email with the subject <em>"Data Deletion Request"</em> to <a href="mailto:support@rezel.online">support@rezel.online</a> with your account email address. Our engineering team will permanently erase your data and send a confirmation within 48 business hours.</p>
        </div>

        <h2>What Gets Deleted</h2>
        <ul>
            <li>User account credentials, email, and authentication records.</li>
            <li>Stored Facebook, Instagram, and YouTube OAuth access tokens and refresh tokens.</li>
            <li>Cached comment threads, auto-reply history, and scheduled post media binaries.</li>
        </ul>
    </div>
</div>
</body>
</html>
