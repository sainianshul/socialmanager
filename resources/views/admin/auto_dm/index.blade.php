@extends('admin.layouts.app')

@section('title', 'Auto DM')

@section('content')

    {{-- Clean Page Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Auto DM (Comment-to-Inbox)</h2>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary" onclick="alert('Auto DM rules configuration is in development and will be available soon.')">
                    <i class="ti ti-plus me-1"></i>+ Add Auto DM Rule
                </button>
            </div>
        </div>
    </div>

    {{-- Feature Banner --}}
    <div class="card mb-3">
        <div class="card-status-top bg-purple"></div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="avatar avatar-md rounded bg-purple-lt">
                        <i class="ti ti-message-chatbot fs-2"></i>
                    </span>
                </div>
                <div class="col">
                    <h3 class="card-title mb-1">Instant Private Messaging via Meta API</h3>
                    <p class="text-secondary mb-0">
                        Convert public commenters into private conversations. Automatically send personalized direct messages on Facebook Messenger or Instagram Direct whenever constituents comment on your posts or trigger specific keywords (e.g. "Camp", "Price", "Help").
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Rules Card --}}
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                <h3 class="card-title">Active Automation Rules</h3>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select" style="width: 180px;">
                        <option value="">All Channels ({{ $accounts->count() }})</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            @include('admin.layouts.partials._table-empty', [
                'id' => 'autodm-empty',
                'title' => 'No Auto DM Rules Configured',
                'subtitle' => 'You have not set up any keyword triggers yet. Click "+ Add Auto DM Rule" above to create automated direct responses.'
            ])
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let emptyEl = document.getElementById('autodm-empty');
        if (emptyEl) {
            emptyEl.classList.remove('d-none');
        }
    });
</script>
@endpush

