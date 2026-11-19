@extends('admin.layouts.app')

@section('title', 'Webhook Activity Logs')

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.auto-dm.index') }}" class="btn btn-outline-secondary btn-icon btn-sm" title="Back to Auto DM">
                        <i class="ti ti-arrow-left"></i>
                    </a>
                    <h2 class="page-title text-body m-0">Webhook Activity Logs</h2>
                    <span class="badge badge-outline text-success"><i class="ti ti-circle-check me-1"></i>Endpoint Active</span>
                </div>
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                <a href="{{ route('admin.webhook-logs.index') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="ti ti-refresh me-1.5"></i>Refresh Logs
                </a>
                <form action="{{ route('admin.webhook-logs.clear') }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to clear the webhook log file?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center">
                        <i class="ti ti-trash me-1.5"></i>Clear Logs
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Connection Info Cards --}}
    <div class="row g-3 mb-3">
        {{-- Callback URL --}}
        <div class="col-12 col-md-5">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-medium">Webhook Callback URL</span>
                        <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm" onclick="copyToClipboard('https://socialmanager.rezel.online/webhook/meta', 'Callback URL copied!')" title="Copy URL">
                            <i class="ti ti-copy"></i>
                        </button>
                    </div>
                    <div class="font-monospace text-primary fw-semibold small text-truncate">
                        https://socialmanager.rezel.online/webhook/meta
                    </div>
                </div>
            </div>
        </div>

        {{-- Verify Token --}}
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-medium">Verify Token</span>
                        <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm" onclick="copyToClipboard('warroom_secret_token_123', 'Verify Token copied!')" title="Copy Token">
                            <i class="ti ti-copy"></i>
                        </button>
                    </div>
                    <div class="font-monospace text-dark fw-bold small">
                        warroom_secret_token_123
                    </div>
                </div>
            </div>
        </div>

        {{-- File Status --}}
        <div class="col-12 col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-3">
                    <span class="text-secondary small fw-medium d-block mb-1">Log File Details</span>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted font-monospace">webhook.log</span>
                        <span class="badge badge-outline text-secondary">{{ $fileSize }}</span>
                    </div>
                    <div class="text-muted small mt-1">Updated: {{ $lastModified }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Logs Viewer Console --}}
    <div class="card shadow-sm">
        <div class="card-header py-2.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-terminal-2 text-dark fs-2"></i>
                <h3 class="card-title m-0">Live Webhook Log Stream (Last 250 Lines)</h3>
            </div>
            <div class="d-flex align-items-center gap-2 small text-secondary">
                <span class="status-dot status-dot-animated bg-success"></span>
                <span>Auto-monitoring enabled</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if(empty(trim($rawLogs)))
                <div class="p-5 text-center text-secondary">
                    <div class="avatar avatar-lg rounded-circle bg-blue-lt mb-3">
                        <i class="ti ti-activity text-primary fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-1">No Webhook Logs Recorded Yet</h4>
                    <p class="small text-muted mb-3" style="max-width: 480px; margin: 0 auto;">
                        Logs will automatically appear here as soon as Meta sends a verification request (handshake) or an incoming comment event.
                    </p>
                    <a href="{{ route('admin.webhook-logs.index') }}" class="btn btn-outline-primary btn-sm px-3">
                        <i class="ti ti-refresh me-1"></i>Check Again
                    </a>
                </div>
            @else
                <pre class="m-0 p-3 bg-dark text-light font-monospace small"
                     id="webhook-log-pre"
                     style="max-height: 600px; overflow-y: auto; border-radius: 0 0 4px 4px; line-height: 1.5; white-space: pre-wrap; word-break: break-all;">{{ $rawLogs }}</pre>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function copyToClipboard(text, successMsg) {
        navigator.clipboard.writeText(text).then(function() {
            if (typeof showToast === 'function') {
                showToast(successMsg, 'success');
            } else {
                alert(successMsg);
            }
        });
    }

    // Scroll to bottom of log stream automatically
    document.addEventListener('DOMContentLoaded', function () {
        const logPre = document.getElementById('webhook-log-pre');
        if (logPre) {
            logPre.scrollTop = logPre.scrollHeight;
        }
    });
</script>
@endpush
