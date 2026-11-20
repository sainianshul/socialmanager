<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'SocialManager Admin')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    {{-- Early Theme Initialization to Prevent Flash of Unstyled Content --}}
    <script>
        (function() {
            var theme = localStorage.getItem('data-bs-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
            if (localStorage.getItem('sidebar-folded') === 'true') {
                document.documentElement.setAttribute('data-bs-sidebar', 'folded');
            }
        })();
    </script>
    
    <!-- Tabler Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    
    <!-- Plus Jakarta Sans Font (Modern Geometric Enterprise UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tabler Official Typography (Inter fallback) -->
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}?v={{ file_exists(public_path('css/admin-custom.css')) ? filemtime(public_path('css/admin-custom.css')) : time() }}">
    
    @stack('datatables_css')
    @stack('styles')
</head>
