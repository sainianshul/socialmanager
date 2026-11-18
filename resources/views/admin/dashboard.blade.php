@extends('admin.layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Super Admin System Overview' : 'Social Dashboard')

@section('content')
    @if(auth()->user()->isAdmin())
        @include('admin.partials._dashboard_superadmin')
    @else
        @include('admin.partials._dashboard_client')
    @endif
@endsection

@push('scripts')
    @if(auth()->user()->isAdmin())
        @include('admin.partials._dashboard_superadmin_scripts')
    @else
        @include('admin.partials._dashboard_client_scripts')
    @endif
@endpush
