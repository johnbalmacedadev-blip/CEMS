<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Car Empire Management System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        /* CSS Variables for Theme */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #ffffff;
            --text-primary: #212529;
            --text-secondary: #495057;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --border-hover: #adb5bd;
            --shadow: rgba(0, 0, 0, 0.06);
        }

        [data-theme="dark"] {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --bg-tertiary: #212121;
            --text-primary: #ffffff;
            --text-secondary: #e0e0e0;
            --text-muted: #b0b0b0;
            --border-color: #404040;
            --border-hover: #555555;
            --shadow: rgba(0, 0, 0, 0.3);
        }

        /* Global padding for all pages */
        html, body {
            overflow-x: hidden;
        }
        
        body {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            padding-bottom: 60px;
        }
        
        /* Apply 30px padding to all main content containers - consistent across all pages */
        .container-fluid {
            padding-left: 30px !important;
            padding-right: 30px !important;
        }
        
        /* Exclude navbar from padding requirement */
        .navbar .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        
        /* Ensure consistent padding for regular containers too */
        .container {
            padding-left: 30px !important;
            padding-right: 30px !important;
        }
        
        /* Apply padding to main content areas */
        main, .main-content, [class*="content"] {
            padding-left: 30px !important;
            padding-right: 30px !important;
        }
        
        /* Exclude footer from padding requirement */
        .app-footer .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        
        /* Responsive: reduce padding on smaller screens */
        @media (max-width: 768px) {
            .container-fluid:not(.navbar .container-fluid):not(.app-footer .container-fluid),
            .container,
            main, .main-content, [class*="content"] {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }
        }
        
        @media (max-width: 576px) {
            .container-fluid:not(.navbar .container-fluid):not(.app-footer .container-fluid),
            .container,
            main, .main-content, [class*="content"] {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }
        }
        .navbar-brand {
            font-weight: bold;
        }
        /*
         * Light, clean, outline-first theme overrides
         */
        /* Navbar: Dark theme with logo (overrides theme-aware) */
        .navbar.navbar-dark.bg-dark,
        .navbar.navbar-dark {
            background-color: rgba(26, 26, 26, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 2px solid #dc3545 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .navbar-dark .navbar-brand {
            color: #ffffff !important;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .navbar-dark .navbar-brand img {
            height: 40px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(220, 53, 69, 0.3));
        }
        
        .navbar-dark .navbar-brand span {
            color: #ffffff !important;
        }
        
        .navbar-dark .nav-link,
        .navbar-dark .dropdown-item {
            color: #ffffff !important;
            transition: color 0.3s ease;
        }
        
        .navbar-dark .btn-outline-light {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
        
        .navbar-dark .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .navbar-dark .btn-outline-secondary {
            color: #ffffff !important;
            border-color: rgba(220, 53, 69, 0.5) !important;
        }
        
        .navbar-dark .btn-outline-secondary:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
        }
        
        .navbar-dark .btn-outline-warning {
            color: #ffffff !important;
            border-color: rgba(255, 193, 7, 0.5) !important;
        }
        
        .navbar-dark .btn-outline-warning:hover {
            background-color: rgba(255, 193, 7, 0.2);
            border-color: rgba(255, 193, 7, 0.8);
        }
        
        .navbar-dark .nav-link.dropdown-toggle,
        .navbar-dark .dropdown-menu {
            background-color: rgba(26, 26, 26, 0.98);
            border-color: rgba(220, 53, 69, 0.3);
        }
        
        .navbar-dark .dropdown-item:hover {
            background-color: rgba(220, 53, 69, 0.2);
            color: #ffffff !important;
        }
        
        .navbar-dark .badge {
            color: #ffffff;
        }
        
        /* Global Navbar Full Width - Fixed at Top */
        .global-navbar,
        .navbar.navbar-expand-lg {
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1030 !important;
            display: block !important;
            order: 1 !important;
            flex-shrink: 0 !important;
        }
        
        /* Username White Color */
        .navbar .text-white,
        .navbar .text-white span,
        .navbar .nav-link {
            color: #ffffff !important;
        }
        
        .navbar .nav-link:hover {
            color: #ffffff !important;
        }
        
        /* Ensure body content starts below navbar */
        body {
            padding-top: 56px !important; /* Height of navbar */
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
        }
        
        /* Ensure content wrapper doesn't overlap navbar */
        .content-wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
            order: 2 !important;
            flex: 1 !important;
        }
        
        /* Ensure navbar is always first */
        .global-navbar {
            order: 1 !important;
        }
        
        /* Global Sidebar Styles */
        .global-sidebar {
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            max-height: calc(100vh - 56px - 60px); /* Account for navbar and footer */
        }
        
        .global-sidebar.show {
            transform: translateX(0) !important;
        }
        
        .global-sidebar:not(.show) {
            transform: translateX(-100%) !important;
        }
        
        /* Ensure sidebar content is scrollable */
        .global-sidebar .nav {
            padding-bottom: 1rem;
        }
        
        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
            position: fixed;
            top: 56px;
            left: 0;
            width: 100%;
            height: calc(100vh - 56px - 60px); /* Account for navbar and footer */
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1019;
            display: none;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        /* Adjust content when sidebar is open */
        body.sidebar-open .content-wrapper {
            margin-left: 250px;
        }
        
        @media (max-width: 768px) {
            body.sidebar-open .content-wrapper {
                margin-left: 0;
            }
        }
        
        /* Sidebar Navigation Links */
        .global-sidebar .nav-link {
            color: var(--text-secondary);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            transition: color 0.3s ease, background-color 0.3s ease;
            margin-bottom: 0.25rem;
        }
        
        .global-sidebar .nav-link:hover {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
        }
        
        .global-sidebar .nav-link.active {
            background-color: #0d6efd;
            color: #ffffff;
        }
        
        .global-sidebar .sidebar-heading {
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }
        
        /* Hide navbar on home page */
        body:has(.home-page) .global-navbar {
            display: none !important;
        }

        /* Sidebar: theme-aware */
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: var(--bg-primary);
            border-right: 1px solid var(--border-color);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .sidebar .nav-link {
            color: var(--text-secondary);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            transition: color 0.3s ease, background-color 0.3s ease;
        }
        .sidebar .nav-link:hover {
            color: var(--text-primary);
            background-color: var(--bg-secondary);
        }
        .sidebar .nav-link.active {
            color: #0d6efd;
            background-color: transparent;
            border: 1px solid #0d6efd;
        }

        .main-content {
            padding: 2rem;
        }
        /* Cards: theme-aware */
        .card {
            box-shadow: none;
            border: 1px solid var(--border-color);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        .card-header {
            background-color: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        /* If pages used bg-* on card headers, tone them down to outline accents */
        .card-header.bg-primary,
        .card-header.bg-success,
        .card-header.bg-info,
        .card-header.bg-warning,
        .card-header.bg-danger,
        .card-header.bg-secondary {
            background-color: var(--bg-primary) !important;
            color: var(--text-primary) !important;
            border-bottom: 1px solid var(--border-color) !important;
        }
        .card-header.bg-primary { border-left: 3px solid #0d6efd; }
        .card-header.bg-success { border-left: 3px solid #198754; }
        .card-header.bg-info { border-left: 3px solid #0dcaf0; }
        .card-header.bg-warning { border-left: 3px solid #ffc107; }
        .card-header.bg-danger { border-left: 3px solid #dc3545; }
        .card-header.bg-secondary { border-left: 3px solid #6c757d; }

        /* Buttons: convert filled variants to outline style for a clean look */
        .btn-primary {
            color: #0d6efd;
            background-color: transparent;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }
        .btn-warning {
            color: #f59f00;
            background-color: transparent;
            border-color: #ffc107;
        }
        .btn-warning:hover {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-danger {
            color: #dc3545;
            background-color: transparent;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }
        .btn-success {
            color: #198754;
            background-color: transparent;
            border-color: #198754;
        }
        .btn-success:hover {
            background-color: #198754;
            color: #fff;
        }
        .btn-secondary {
            color: #6c757d;
            background-color: transparent;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-outline-warning {
            color: #f59f00;
            border-color: #ffc107;
        }
    </style>
    
    @yield('styles')
    @stack('styles')
</head>
<body style="padding-top: 56px; margin: 0; display: flex; flex-direction: column; min-height: 100vh;">
    @include('partials.preloader')
    <!-- Global Navigation Bar - Hidden on home page -->
    @if(!request()->routeIs('home'))
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark global-navbar" style="width: 100%; margin: 0; padding: 0; position: fixed; top: 0; left: 0; right: 0; z-index: 1030; display: block; order: 1; flex-shrink: 0;">
        <div class="container-fluid" style="padding-left: 1rem; padding-right: 1rem;">
            <button class="btn btn-outline-light me-3" type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/CAREMPIRE_LOGO.png') }}" alt="CAR EMPIRE Logo" onerror="this.style.display='none';">
            </a>
            
            <div class="navbar-nav ms-auto d-flex align-items-center">
                <!-- Cache Clear Button -->
                <button class="btn btn-outline-warning btn-sm me-2" onclick="clearAllCache()" title="Clear Cache">
                    <i class="fas fa-sync-alt"></i>
                </button>

                <!-- User identity -->
                <span class="me-2 d-none d-md-inline text-white">
                    <i class="fas fa-user me-1"></i><span style="color: #ffffff !important;">{{ Auth::user()->name }}</span>
                    <span class="badge bg-{{ Auth::user()->isAdmin() ? 'danger' : 'primary' }} ms-1">{{ ucfirst(Auth::user()->role) }}</span>
                </span>

                <!-- Direct Logout Button -->
                <a href="{{ route('logout') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>
    @endif

    <!-- Global Sidebar - Hidden on home page -->
    @if(!request()->routeIs('home'))
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" style="display: none;"></div>
    
    <!-- Global Sidebar -->
    <nav class="global-sidebar collapse" id="globalSidebar" style="position: fixed; top: 56px; left: 0; height: calc(100vh - 56px - 60px); width: 250px; background-color: var(--bg-primary); border-right: 1px solid var(--border-color); z-index: 1020; overflow-y: auto; overflow-x: hidden; transition: transform 0.3s ease-in-out; transform: translateX(-100%);">
        <div class="pt-3 px-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">
                        <i class="fas fa-car me-2"></i>Unit Report
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sales-agents.*') ? 'active' : '' }}" href="{{ route('sales-agents.index') }}">
                        <i class="fas fa-user-tie me-2"></i>Sales Agents
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                        <i class="fas fa-users me-2"></i>Employees
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-users me-2"></i>Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-file-invoice me-2"></i>Sales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-tools me-2"></i>Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-bar me-2"></i>Reports
                    </a>
                </li>
                <!-- Expenses Menu -->
                <li class="nav-item mt-2">
                    <h6 class="sidebar-heading px-3 py-2 text-muted small text-uppercase" style="font-size: 0.75rem; font-weight: 600;">
                        <span>Expenses & Inventory</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('expenses-inventory') && request()->get('section') == 'expenses' ? 'active' : '' }}" href="{{ route('expenses-inventory', ['section' => 'expenses']) }}">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Expense Transactions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('expenses-inventory') && request()->get('section') == 'tools-purchase' ? 'active' : '' }}" href="{{ route('expenses-inventory', ['section' => 'tools-purchase']) }}">
                        <i class="fas fa-shopping-cart me-2"></i>Purchase Inventory
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('expenses-inventory') && request()->get('section') == 'tools-current' ? 'active' : '' }}" href="{{ route('expenses-inventory', ['section' => 'tools-current']) }}">
                        <i class="fas fa-boxes me-2"></i>Current Tools Inventory
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('expenses-inventory') && request()->get('section') == 'external-expenses' ? 'active' : '' }}" href="{{ route('expenses-inventory', ['section' => 'external-expenses']) }}">
                        <i class="fas fa-store me-2"></i>External Expenses
                    </a>
                </li>
                @if(Auth::user()->isAdmin())
                <li class="nav-item mt-2">
                    <h6 class="sidebar-heading px-3 py-2 text-muted small text-uppercase" style="font-size: 0.75rem; font-weight: 600;">
                        <span>Administration</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.users.*') ? 'active' : '' }}" href="{{ route('settings.users.index') }}">
                        <i class="fas fa-users-cog me-2"></i>Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings') && !request()->routeIs('settings.users.*') ? 'active' : '' }}" href="{{ route('settings') }}">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </nav>
    @endif

    <div class="content-wrapper" style="margin-top: 0; padding-top: 0; order: 2; flex: 1; transition: margin-left 0.3s ease-in-out;">
        @yield('content')
    </div>
    <a href="{{ route('home') }}" class="back-home-fab" title="Back to Main Menu" aria-label="Back to Main Menu">
        <i class="fas fa-home"></i>
    </a>
    <button class="theme-toggle" id="themeToggle" title="Toggle Theme" aria-label="Toggle Theme">
        <i class="fas fa-sun"></i>
        <i class="fas fa-moon"></i>
    </button>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .back-home-fab {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1050;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            background-color: #1a1a1a;
            border: 1px solid #dc3545;
            text-decoration: none;
            transition: transform .12s ease, box-shadow .12s ease, background-color .3s ease, color .12s ease;
        }
        .back-home-fab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 18px var(--shadow);
            background-color: #dc3545;
            color: #ffffff;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
            background-color: var(--bg-primary);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: transform .12s ease, box-shadow .12s ease, background-color .3s ease, border-color .3s ease;
        }
        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 18px var(--shadow);
            border-color: var(--border-hover);
        }
        .theme-toggle i.fa-sun {
            display: block;
        }
        .theme-toggle i.fa-moon {
            display: none;
        }
        [data-theme="dark"] .theme-toggle i.fa-sun {
            display: none;
        }
        [data-theme="dark"] .theme-toggle i.fa-moon {
            display: block;
        }

        /* Additional theme-aware elements */
        .text-muted {
            color: var(--text-muted) !important;
        }
        .table {
            color: var(--text-primary);
        }
        .table-dark {
            background-color: var(--bg-tertiary) !important;
            color: var(--text-primary) !important;
        }
        .table-striped > tbody > tr:nth-of-type(odd) > td {
            background-color: var(--bg-secondary);
        }
        input, select, textarea {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            border-color: var(--border-color);
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            border-color: #0d6efd;
        }
    </style>
    
    <!-- Theme Switcher Script -->
    <script>
        (function() {
            // Get theme preference from localStorage or default to light
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            
            // Apply saved theme immediately to prevent flash
            html.setAttribute('data-theme', savedTheme);
            
            // Theme toggle functionality
            document.addEventListener('DOMContentLoaded', function() {
                const themeToggle = document.getElementById('themeToggle');
                
                if (themeToggle) {
                    themeToggle.addEventListener('click', function() {
                        const currentTheme = html.getAttribute('data-theme');
                        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                        
                        html.setAttribute('data-theme', newTheme);
                        localStorage.setItem('theme', newTheme);
                    });
                }
            });
        })();
    </script>
    
    <!-- Cache Busting Script -->
    <script>
        // Only clear cache on manual refresh, not on automatic navigation
        if (performance.navigation.type === 1 && !sessionStorage.getItem('cacheCleared')) {
            // Page was manually refreshed, clear cache once
            sessionStorage.setItem('cacheCleared', 'true');
            
            // Clear browser cache
            if ('caches' in window) {
                caches.keys().then(function(names) {
                    names.forEach(function(name) {
                        caches.delete(name);
                    });
                });
            }
        }
        
        // Reset cache flag on successful navigation
        window.addEventListener('beforeunload', function() {
            sessionStorage.removeItem('cacheCleared');
        });
        
        // Add cache busting to forms (only for vehicle forms)
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                // Only add cache busting to vehicle forms, not login forms
                if (form.action.includes('/vehicles') || form.action.includes('/dashboard')) {
                    form.addEventListener('submit', function() {
                        // Clear browser cache before form submission
                        if ('caches' in window) {
                            caches.keys().then(function(names) {
                                names.forEach(function(name) {
                                    caches.delete(name);
                                });
                            });
                        }
                    });
                }
            });
        });
        
        // Cache clearing function
        function clearAllCache() {
            Swal.fire({
                title: 'Clear Cache?',
                text: 'This will clear all cached data and force a fresh reload.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Clear Cache!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Clear browser cache
                    if ('caches' in window) {
                        caches.keys().then(function(names) {
                            names.forEach(function(name) {
                                caches.delete(name);
                            });
                        });
                    }
                    
                    // Clear localStorage and sessionStorage (but preserve theme preference)
                    const currentTheme = localStorage.getItem('theme');
                    localStorage.clear();
                    sessionStorage.clear();
                    if (currentTheme) {
                        localStorage.setItem('theme', currentTheme);
                    }
                    
                    // Force hard refresh
                    window.location.reload(true);
                }
            });
        }
    </script>
    
    @stack('scripts')
    @yield('scripts')
    
    <!-- Global Sidebar Toggle Script -->
    <script>
    (function() {
        function initSidebar() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const globalSidebar = document.getElementById('globalSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const body = document.body;
            
            if (!sidebarToggle || !globalSidebar) {
                // Retry if elements not found yet
                setTimeout(initSidebar, 100);
                return;
            }
            
            // Check if sidebar state is stored in localStorage
            const sidebarState = localStorage.getItem('globalSidebarOpen');
            
            // Set initial state (closed by default)
            if (sidebarState === 'true') {
                // User previously opened sidebar - restore open state
                globalSidebar.classList.add('show');
                body.classList.add('sidebar-open');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.add('show');
                }
            } else {
                // Default: sidebar closed
                globalSidebar.classList.remove('show');
                body.classList.remove('sidebar-open');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('show');
                }
                localStorage.setItem('globalSidebarOpen', 'false');
            }
            
            // Remove any existing event listeners by cloning the button
            const newToggle = sidebarToggle.cloneNode(true);
            sidebarToggle.parentNode.replaceChild(newToggle, sidebarToggle);
            
            // Toggle sidebar
            newToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (globalSidebar.classList.contains('show')) {
                    // Close sidebar
                    globalSidebar.classList.remove('show');
                    body.classList.remove('sidebar-open');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.remove('show');
                    }
                    localStorage.setItem('globalSidebarOpen', 'false');
                } else {
                    // Open sidebar
                    globalSidebar.classList.add('show');
                    body.classList.add('sidebar-open');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.add('show');
                    }
                    localStorage.setItem('globalSidebarOpen', 'true');
                }
            });
            
            // Close sidebar when clicking overlay (mobile)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    globalSidebar.classList.remove('show');
                    body.classList.remove('sidebar-open');
                    sidebarOverlay.classList.remove('show');
                    localStorage.setItem('globalSidebarOpen', 'false');
                });
            }
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSidebar);
        } else {
            // DOM is already ready
            initSidebar();
        }
    })();
    </script>
    
    <!-- Footer with red border -->
    <footer class="app-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center py-2">
                    <small style="color: #ffffff;">© {{ date('Y') }} Car Empire Management System. All rights reserved.</small>
                </div>
            </div>
        </div>
    </footer>
    
    <style>
        .app-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background-color: #000000;
            border-top: 3px solid #dc3545;
            z-index: 1000;
            padding: 0;
            margin: 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .app-footer .container-fluid {
            padding: 0.5rem 1rem;
        }
    </style>

    @include('partials.flash-alert')

    @auth
        @include('partials.live-chat')
    @endauth
</body>
</html>
