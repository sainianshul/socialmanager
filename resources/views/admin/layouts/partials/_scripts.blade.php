<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Tabler Core JS -->
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js" defer></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toastify JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
function showToast(message, type = 'success') {
    let bg = "#206bc4";
    if (type === 'success') bg = "#2fb344";
    else if (type === 'error') bg = "#d63939";
    else if (type === 'warning') bg = "#f76707";

    Toastify({
        text: message,
        duration: 5000,
        close: true,
        gravity: "top",
        position: "right",
        style: {
            background: bg,
        }
    }).showToast();
}

@if(session('success'))
    showToast("{{ session('success') }}", 'success');
@endif

@if(session('warning'))
    showToast("{{ session('warning') }}", 'warning');
@endif

@if(session('error'))
    showToast("{{ session('error') }}", 'error');
@endif

// Sidebar Fold / Expand toggle handler
document.addEventListener('DOMContentLoaded', function () {
    function toggleSidebar(e) {
        if (e && e.preventDefault) {
            e.preventDefault();
        }
        var isFolded = document.body.classList.toggle('sidebar-folded');
        if (isFolded) {
            document.documentElement.setAttribute('data-bs-sidebar', 'folded');
            localStorage.setItem('sidebar-folded', 'true');
        } else {
            document.documentElement.removeAttribute('data-bs-sidebar');
            localStorage.setItem('sidebar-folded', 'false');
        }
        setTimeout(function() {
            window.dispatchEvent(new Event('resize'));
        }, 260);
    }

    // Direct listener and event delegation for toggle buttons
    document.addEventListener('click', function (e) {
        var toggleTrigger = e.target.closest('#btn-toggle-sidebar, #btn-toggle-sidebar-navbar');
        if (toggleTrigger) {
            toggleSidebar(e);
        }
    });

    // Auto-close mobile sidebar collapse when clicking a link
    var sidebarMenu = document.getElementById('sidebar-menu');
    if (sidebarMenu) {
        sidebarMenu.addEventListener('click', function (e) {
            var navLink = e.target.closest('.nav-link:not([data-bs-toggle])');
            if (navLink && window.innerWidth < 992 && sidebarMenu.classList.contains('show')) {
                var toggler = document.querySelector('.navbar-vertical .navbar-toggler');
                if (toggler) {
                    toggler.click();
                }
            }
        });
    }

    // Expand / Collapse long comments and replies
    document.addEventListener('click', function (e) {
        var moreBtn = e.target.closest('.btn-comment-more');
        if (moreBtn) {
            e.preventDefault();
            var parent = moreBtn.closest('.comment-text-expandable');
            if (parent) {
                var shortEl = parent.querySelector('.comment-text-short');
                var fullEl = parent.querySelector('.comment-text-full');
                if (shortEl) shortEl.classList.add('d-none');
                if (fullEl) fullEl.classList.remove('d-none');
            }
            return;
        }

        var lessBtn = e.target.closest('.btn-comment-less');
        if (lessBtn) {
            e.preventDefault();
            var parent = lessBtn.closest('.comment-text-expandable');
            if (parent) {
                var shortEl = parent.querySelector('.comment-text-short');
                var fullEl = parent.querySelector('.comment-text-full');
                if (fullEl) fullEl.classList.add('d-none');
                if (shortEl) shortEl.classList.remove('d-none');
            }
            return;
        }
    });

    // Theme Toggle Handler (Smooth & Instant without page reload)
    document.addEventListener('click', function (e) {
        var themeTrigger = e.target.closest('.btn-theme-toggle');
        if (themeTrigger) {
            e.preventDefault();
            var targetTheme = themeTrigger.getAttribute('data-theme') || 'light';
            localStorage.setItem('data-bs-theme', targetTheme);
            document.documentElement.setAttribute('data-bs-theme', targetTheme);
            document.body.setAttribute('data-bs-theme', targetTheme);
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: targetTheme } }));
        }
    });

    // Initialize tooltips
    if (window.bootstrap && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], .btn-toggle-stats[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>

<style>
.btn-toggle-stats.active,
.btn-toggle-stats[aria-expanded="true"] {
    background-color: var(--tblr-primary, #206bc4) !important;
    border-color: var(--tblr-primary, #206bc4) !important;
    color: #ffffff !important;
}
.btn-toggle-stats.active i,
.btn-toggle-stats[aria-expanded="true"] i {
    color: #ffffff !important;
}
</style>

@stack('datatables_js')
@stack('scripts')
