@extends('admin.layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Super Admin System Overview' : (auth()->user()->isManager() ? 'Manager Console' : 'Social Dashboard'))

@section('content')
    @if(auth()->user()->isAdmin())
        @include('admin.partials._dashboard_superadmin')
    @elseif(auth()->user()->isManager())
        @include('admin.partials._dashboard_manager')
    @else
        @include('admin.partials._dashboard_client')
    @endif
@endsection

@push('scripts')
    @if(auth()->user()->isAdmin())
        @include('admin.partials._dashboard_superadmin_scripts')
    @elseif(!auth()->user()->isManager())
        @include('admin.partials._dashboard_client_scripts')
    @endif
@endpush
