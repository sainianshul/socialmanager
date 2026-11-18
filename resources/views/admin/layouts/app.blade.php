@include('admin.layouts.partials._header')
<body>
    <script>
        var mode = localStorage.getItem('data-bs-theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', mode);
        document.body.setAttribute('data-bs-theme', mode);
        if (localStorage.getItem('sidebar-folded') === 'true') {
            document.documentElement.setAttribute('data-bs-sidebar', 'folded');
            document.body.classList.add('sidebar-folded');
        }
    </script>
    <div class="page">
        <!-- Sidebar -->
        @include('admin.layouts.partials._sidebar')
        
        <div class="page-wrapper">
            <!-- Navbar -->
            @include('admin.layouts.partials._navbar')
            
            <!-- Page body -->
            <div class="page-body">
                <div class="container-fluid px-3 px-lg-4">
                    @yield('content')
                </div>
            </div>
            
            <!-- Footer -->
            @include('admin.layouts.partials._footer')
        </div>
    </div>
    
    @include('admin.layouts.partials._scripts')
</body>
</html>