@extends('layouts.app')

@section('title', 'Home - Car Empire Management System')

@section('content')
<script>
// Set default theme immediately before styles load
(function() {
    const savedTheme = localStorage.getItem('homeTheme') || 'dark';
    document.documentElement.setAttribute('data-home-theme', savedTheme);
    if (document.body) {
        document.body.setAttribute('data-home-theme', savedTheme);
    }
    // Also set on window load as backup
    window.addEventListener('load', function() {
        document.body.setAttribute('data-home-theme', savedTheme);
    });
})();
</script>
<style>
    /* Background image with overlay */
    body {
        position: relative;
        min-height: 100vh;
    }
    
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://carempireph.com/wp-content/uploads/2021/05/evgeny-tchebotarev-aiwuLjLPFnU-unsplash-scaled-1.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        z-index: -2;
    }
    
    body::after {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.75);
        z-index: -1;
        transition: background-color 0.3s ease;
    }
    
    /* Light theme overlay */
    [data-home-theme="light"] body::after,
    body[data-home-theme="light"]::after {
        background-color: rgba(0, 0, 0, 0.3);
    }
    
    /* Dark navbar with red accents - keep this */
    .navbar {
        background-color: rgba(26, 26, 26, 0.95) !important;
        backdrop-filter: blur(10px);
        border-bottom: 2px solid #dc3545;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    
    .navbar-brand {
        color: #ffffff !important;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .navbar-brand img {
        height: 40px;
        width: auto;
        filter: drop-shadow(0 2px 4px rgba(220, 53, 69, 0.3));
    }
    
    .navbar .btn-outline-secondary {
        color: #ffffff;
        border-color: rgba(220, 53, 69, 0.5);
    }
    
    .navbar .btn-outline-secondary:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #ffffff;
    }
    
    .navbar .btn-outline-light {
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    .navbar .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
    }
    
    /* Home container — avoid z-index here (traps modals under Bootstrap’s backdrop) */
    .home-container {
        padding-top: 2rem;
        padding-bottom: 2rem;
        position: relative;
    }
    
    .welcome-section {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .welcome-section h1 {
        color: #ffffff;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        transition: color 0.3s ease, text-shadow 0.3s ease;
    }
    
    [data-home-theme="light"] .welcome-section h1,
    body[data-home-theme="light"] .welcome-section h1 {
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .welcome-section p {
        color: #e0e0e0;
        font-size: 1.1rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        transition: color 0.3s ease, text-shadow 0.3s ease;
    }
    
    [data-home-theme="light"] .welcome-section p,
    body[data-home-theme="light"] .welcome-section p {
        color: #ffffff;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
    
    /* Cards with dark semi-transparent background */
    .home-card {
        border-width: 1px;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
        height: 100%;
        background-color: rgba(26, 26, 26, 0.75);
        backdrop-filter: blur(10px);
        cursor: pointer;
    }
    
    [data-home-theme="light"] .home-card,
    body[data-home-theme="light"] .home-card {
        background-color: rgba(255, 255, 255, 0.9);
    }
    
    .home-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        background-color: rgba(26, 26, 26, 0.85);
    }
    
    [data-home-theme="light"] .home-card:hover,
    body[data-home-theme="light"] .home-card:hover {
        background-color: rgba(255, 255, 255, 1);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .home-card .card-title {
        color: #ffffff;
        font-weight: 600;
        font-size: 1.15rem;
        transition: color 0.3s ease;
    }
    
    [data-home-theme="light"] .home-card .card-title,
    body[data-home-theme="light"] .home-card .card-title {
        color: #212529;
    }
    
    .home-card .card-text {
        color: #ffffff;
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }
    
    [data-home-theme="light"] .home-card .card-text,
    body[data-home-theme="light"] .home-card .card-text {
        color: #6c757d;
    }
    
    .icon-circle {
        width: 52px;
        height: 52px;
        min-width: 52px;
        min-height: 52px;
        max-width: 52px;
        max-height: 52px;
        border: 1px solid currentColor;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: all 0.3s ease;
        flex-shrink: 0;
        aspect-ratio: 1 / 1;
    }
    
    .home-card:hover .icon-circle {
        transform: scale(1.1);
    }
    
    .home-card a {
        text-decoration: none;
        color: inherit;
    }
    
    /* Scroll-up animation for category buttons */
    @keyframes scrollUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .category-card {
        animation: scrollUp 0.6s ease-out forwards;
        opacity: 0;
    }
    
    /* Staggered animation delays for each card */
    .category-card:nth-child(1) {
        animation-delay: 0.1s;
    }
    
    .category-card:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .category-card:nth-child(3) {
        animation-delay: 0.3s;
    }
    
    .category-card:nth-child(4) {
        animation-delay: 0.4s;
    }
    
    .category-card:nth-child(5) {
        animation-delay: 0.5s;
    }
    
    .category-card:nth-child(6) {
        animation-delay: 0.6s;
    }
    
    .category-card:nth-child(7) {
        animation-delay: 0.7s;
    }
    
    .category-card:nth-child(8) {
        animation-delay: 0.8s;
    }
    
    .category-card:nth-child(9) {
        animation-delay: 0.9s;
    }

    .category-card:nth-child(10) {
        animation-delay: 1s;
    }
    
    /* Sub-links live in the card DOM for Blade routes but open in a modal — hidden on the card */
    .home-card .category-sublinks {
        display: none !important;
    }
    
    /* Modal sub-link list (cloned from card) */
    #homeCategoryModal .home-modal-sublinks {
        list-style: none;
        padding-left: 0;
        margin: 0;
        font-size: 0.9rem;
    }
    
    #homeCategoryModal .home-modal-sublinks li {
        margin-bottom: 0.5rem;
    }
    
    #homeCategoryModal .home-modal-sublinks li:last-child {
        margin-bottom: 0;
    }
    
    #homeCategoryModal .home-modal-sublinks a {
        display: block;
        text-decoration: none;
        padding: 0.55rem 0.85rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        text-align: left;
    }
    
    #homeCategoryModal[data-bs-theme="dark"] .home-modal-sublinks a {
        color: rgba(255, 255, 255, 0.95);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.22);
    }
    
    #homeCategoryModal[data-bs-theme="dark"] .home-modal-sublinks a:hover {
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.35);
        color: #fff;
        transform: translateX(4px);
    }
    
    #homeCategoryModal[data-bs-theme="light"] .home-modal-sublinks a {
        color: #212529;
        background: rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.12);
    }
    
    #homeCategoryModal[data-bs-theme="light"] .home-modal-sublinks a:hover {
        background: rgba(0, 0, 0, 0.1);
        border-color: rgba(0, 0, 0, 0.2);
        color: #212529;
        transform: translateX(4px);
    }

    #homeCategoryModal .home-sublink-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    #homeCategoryModal .home-sublink-icon {
        flex-shrink: 0;
        width: 1.35rem;
        text-align: center;
        opacity: 0.95;
        transition: color 0.2s ease, transform 0.2s ease, opacity 0.2s ease;
    }

    #homeCategoryModal[data-bs-theme="dark"] .home-sublink-icon {
        color: #6ea8fe;
    }

    #homeCategoryModal[data-bs-theme="light"] .home-sublink-icon {
        color: #0d6efd;
    }

    #homeCategoryModal[data-bs-theme="dark"] .home-modal-sublinks a.home-sublink-item:hover {
        background: rgba(220, 53, 69, 0.22);
        border-color: rgba(220, 53, 69, 0.55);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
        transform: translateX(4px);
    }

    #homeCategoryModal[data-bs-theme="dark"] .home-modal-sublinks a.home-sublink-item:hover .home-sublink-icon {
        color: #fff;
        transform: scale(1.08);
    }

    #homeCategoryModal[data-bs-theme="light"] .home-modal-sublinks a.home-sublink-item:hover {
        background: rgba(13, 110, 253, 0.12);
        border-color: rgba(13, 110, 253, 0.45);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
        transform: translateX(4px);
    }

    #homeCategoryModal[data-bs-theme="light"] .home-modal-sublinks a.home-sublink-item:hover .home-sublink-icon {
        color: #0a58ca;
        transform: scale(1.08);
    }

    /* Modal title + header readable in dark mode (data-bs-theme is on #homeCategoryModal itself) */
    #homeCategoryModal[data-bs-theme="dark"] {
        --bs-modal-color: #f8f9fa;
        --bs-modal-title-color: #ffffff;
        --bs-heading-color: #ffffff;
    }

    #homeCategoryModal[data-bs-theme="dark"] .modal-content {
        background-color: #212529;
        border-color: #495057;
        color: #f8f9fa;
    }

    #homeCategoryModal[data-bs-theme="dark"] .modal-header {
        border-bottom-color: rgba(255, 255, 255, 0.12);
    }

    #homeCategoryModal[data-bs-theme="dark"] .modal-title,
    #homeCategoryModal[data-bs-theme="dark"] #homeCategoryModalTitle {
        color: #ffffff !important;
    }

    #homeCategoryModal[data-bs-theme="dark"] .btn-close {
        filter: invert(1) grayscale(100%);
        opacity: 0.85;
    }

    #homeCategoryModal[data-bs-theme="dark"] .btn-close:hover {
        opacity: 1;
    }

    #homeCategoryModal[data-bs-theme="light"] {
        --bs-modal-title-color: #212529;
        --bs-heading-color: #212529;
    }

    #homeCategoryModal[data-bs-theme="light"] .modal-title,
    #homeCategoryModal[data-bs-theme="light"] #homeCategoryModalTitle {
        color: #212529 !important;
    }
    
    .home-card .card-header-link {
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
    }
    
    .home-card .card-header-link:hover {
        color: inherit;
    }
    
    /* Logo styling */
    .home-logo {
        text-align: center;
        margin-bottom: 2rem;
        padding-top: 2rem;
    }
    
    .home-logo img {
        height: 80px;
        width: auto;
        filter: drop-shadow(0 2px 4px rgba(220, 53, 69, 0.3));
    }
    
    /* Logout button styling */
    .home-logout {
        text-align: center;
        margin-top: 3rem;
        padding-bottom: 2rem;
    }
    
    .home-logout .btn {
        padding: 0.5rem 1.5rem;
    }
    
    /* Keep logout button white in light mode */
    [data-home-theme="light"] .home-logout .btn,
    body[data-home-theme="light"] .home-logout .btn {
        color: #ffffff !important;
        border-color: #ffffff !important;
    }
    
    [data-home-theme="light"] .home-logout .btn:hover,
    body[data-home-theme="light"] .home-logout .btn:hover {
        color: #ffffff !important;
        border-color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    /* Theme toggle button for home page */
    .home-theme-toggle {
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
        color: #ffffff;
        background-color: rgba(26, 26, 26, 0.9);
        border: 1px solid rgba(220, 53, 69, 0.5);
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
    }
    
    [data-home-theme="light"] .home-theme-toggle,
    body[data-home-theme="light"] .home-theme-toggle {
        color: #212529;
        background-color: rgba(255, 255, 255, 0.9);
        border-color: rgba(0, 0, 0, 0.2);
    }
    
    .home-theme-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 18px rgba(220, 53, 69, 0.4);
        background-color: rgba(220, 53, 69, 0.9);
        border-color: #dc3545;
    }
    
    [data-home-theme="light"] .home-theme-toggle:hover,
    body[data-home-theme="light"] .home-theme-toggle:hover {
        background-color: rgba(255, 255, 255, 1);
        border-color: rgba(0, 0, 0, 0.3);
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.2);
    }
    
    /* Default: dark theme - show moon icon */
    .home-theme-toggle i.fa-sun {
        display: none;
    }
    
    .home-theme-toggle i.fa-moon {
        display: block;
    }
    
    /* Light theme - show sun icon */
    [data-home-theme="light"] .home-theme-toggle i.fa-sun,
    body[data-home-theme="light"] .home-theme-toggle i.fa-sun {
        display: block;
    }
    
    [data-home-theme="light"] .home-theme-toggle i.fa-moon,
    body[data-home-theme="light"] .home-theme-toggle i.fa-moon {
        display: none;
    }
    
    /* Logo styling for light theme */
    [data-home-theme="light"] .home-logo img,
    body[data-home-theme="light"] .home-logo img {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }
    
    /* Hide the layout's theme toggle button on home page */
    .theme-toggle {
        display: none !important;
    }
    
    /* Ensure home theme toggle is visible and styled correctly */
    .home-theme-toggle {
        display: inline-flex !important;
    }
    
    .home-theme-toggle i {
        font-size: 1.2rem;
    }

    #homeCategoryModal {
        z-index: 1060;
    }
</style>

<div class="container py-5 home-container">
    <!-- Logo above welcome text -->
    <div class="home-logo">
        <img src="{{ asset('images/CAREMPIRE_LOGO.png') }}" alt="CAR EMPIRE Logo" onerror="this.style.display='none';">
    </div>
    
    <div class="welcome-section">
        <h1 class="h2 mb-2">Welcome, {{ Auth::user()->name }}</h1>
        <p class="mb-0">Select a category to get started or <a href="{{ route('dashboard') }}" class="text-decoration-none" style="color: #dc3545; font-weight: 600;">go to dashboard</a></p>
    </div>

    <div class="row g-4">
        <!-- 1. CAR REPORTS -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card border-primary">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle text-primary me-3">
                            <i class="fas fa-car"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">CAR REPORTS</h5>
                            <p class="card-text small mb-0">Unit reports, photos, pricelist</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('car-photos-folder') }}" class="home-sublink-item"><i class="fas fa-images fa-fw home-sublink-icon" aria-hidden="true"></i><span>CAR PHOTOS FOLDER</span></a></li>
                        <li><a href="{{ route('vehicles.index') }}" class="home-sublink-item"><i class="fas fa-car fa-fw home-sublink-icon" aria-hidden="true"></i><span>UNIT REPORT</span></a></li>
                        <li><a href="{{ route('pricelist') }}" class="home-sublink-item"><i class="fas fa-tags fa-fw home-sublink-icon" aria-hidden="true"></i><span>PRICELIST</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 2. STAFF REPORTS -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card border-success">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle text-success me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">STAFF REPORTS</h5>
                            <p class="card-text small mb-0">Trackers and recommendations</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('buffing-tracker.index') }}" class="home-sublink-item"><i class="fas fa-spray-can fa-fw home-sublink-icon" aria-hidden="true"></i><span>BUFFING TRACKER</span></a></li>
                        <li><a href="{{ route('insurance-tracker.index') }}" class="home-sublink-item"><i class="fas fa-shield-alt fa-fw home-sublink-icon" aria-hidden="true"></i><span>INSURANCE TRACKER</span></a></li>
                        <li><a href="{{ route('expenses-inventory', ['section' => 'tools-purchase']) }}" class="home-sublink-item"><i class="fas fa-wrench fa-fw home-sublink-icon" aria-hidden="true"></i><span>MECHANIC TRACKER</span></a></li>
                        <li><a href="{{ route('expenses-inventory', ['section' => 'tools-purchase']) }}" class="home-sublink-item"><i class="fas fa-id-card fa-fw home-sublink-icon" aria-hidden="true"></i><span>DRIVER ACTIVITY TRACKER</span></a></li>
                        <li><a href="{{ route('recommendation-tracker.index') }}" class="home-sublink-item"><i class="fas fa-clipboard-check fa-fw home-sublink-icon" aria-hidden="true"></i><span>RECOMMENDATION TRACKER</span></a></li>
                        <li><a href="{{ route('staff-reports.sales-agents') }}" class="home-sublink-item"><i class="fas fa-user-tie fa-fw home-sublink-icon" aria-hidden="true"></i><span>SALES AGENTS</span></a></li>
                        <li><a href="{{ route('staff-reports.executive-agents') }}" class="home-sublink-item"><i class="fas fa-user-shield fa-fw home-sublink-icon" aria-hidden="true"></i><span>EXECUTIVE AGENTS</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 3. PAYMENTS/EXPENSES REPORTS -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card border-warning">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle text-warning me-3">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">PAYMENTS/EXPENSES REPORTS</h5>
                            <p class="card-text small mb-0">Expenses, SOA, payroll, commission</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('expenses-inventory') }}" class="home-sublink-item"><i class="fas fa-receipt fa-fw home-sublink-icon" aria-hidden="true"></i><span>EXPENSES REPORT</span></a></li>
                        <li><a href="{{ route('soa.create') }}" class="home-sublink-item"><i class="fas fa-piggy-bank fa-fw home-sublink-icon" aria-hidden="true"></i><span>SOA CASH VAULT </span></a></li>
                        <li><a href="{{ route('sales-agent-commissions.index') }}" class="home-sublink-item"><i class="fas fa-percent fa-fw home-sublink-icon" aria-hidden="true"></i><span>SALES AGENT COMMISSION</span></a></li>
                        <li><a href="{{ route('gas-expense-po-tracker.index') }}" class="home-sublink-item"><i class="fas fa-gas-pump fa-fw home-sublink-icon" aria-hidden="true"></i><span>GAS EXPENSES/P.O. TRACKER</span></a></li>
                        <li><a href="{{ route('payroll.index') }}" class="home-sublink-item"><i class="fas fa-money-check fa-fw home-sublink-icon" aria-hidden="true"></i><span>PAYROLL</span></a></li>
                        <li><a href="{{ route('sales-agent-commissions.index') }}" class="home-sublink-item"><i class="fas fa-hand-holding-usd fa-fw home-sublink-icon" aria-hidden="true"></i><span>COMMISSION</span></a></li>
                        <li><a href="{{ route('source-screenshots.index') }}" class="home-sublink-item"><i class="fas fa-camera fa-fw home-sublink-icon" aria-hidden="true"></i><span>SOURCE SCREENSHOTS</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 4. VLOGS AND POSTS REPORTS -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card border-info">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle text-info me-3">
                            <i class="fas fa-video"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">VLOGS AND POSTS REPORTS</h5>
                            <p class="card-text small mb-0">Video and posting trackers</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('car-video-boost-report.index') }}" class="home-sublink-item"><i class="fas fa-video fa-fw home-sublink-icon" aria-hidden="true"></i><span>CAR VIDEO BOOST REPORT</span></a></li>
                        <li><a href="{{ route('video-posting-tracker.index') }}" class="home-sublink-item"><i class="fas fa-photo-video fa-fw home-sublink-icon" aria-hidden="true"></i><span>VIDEO AND POSTING TRACKER</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 5. TRANSFERS/PAPERS REPORTS -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card border-secondary">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle text-secondary me-3">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">TRANSFERS/PAPERS REPORTS</h5>
                            <p class="card-text small mb-0">Documents and transfer OR/CR</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('follow-up-documents.index') }}" class="home-sublink-item"><i class="fas fa-file-alt fa-fw home-sublink-icon" aria-hidden="true"></i><span>FOLLOW UP DOCUMENTS</span></a></li>
                        <li><a href="{{ route('transfer-orcr.index') }}" class="home-sublink-item"><i class="fas fa-right-left fa-fw home-sublink-icon" aria-hidden="true"></i><span>TRANSFER ORCR</span></a></li>
                        <li><a href="{{ route('vehicle-registration.index') }}" class="home-sublink-item"><i class="fas fa-id-card fa-fw home-sublink-icon" aria-hidden="true"></i><span>VEHICLE REGISTRATION</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 6. CUSTOMER LISTS -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card" style="border-color: #0d6efd;">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle me-3" style="color: #0d6efd; border-color: #0d6efd;">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">CUSTOMER LISTS</h5>
                            <p class="card-text small mb-0">Client and trail form lists</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('client-follow-up-list.index') }}" class="home-sublink-item"><i class="fas fa-user-friends fa-fw home-sublink-icon" aria-hidden="true"></i><span>CLIENT FOLLOW UP LIST</span></a></li>
                        <li><a href="{{ route('appointment-list.index') }}" class="home-sublink-item"><i class="fas fa-calendar-check fa-fw home-sublink-icon" aria-hidden="true"></i><span>APPOINTMENT LIST</span></a></li>
                        <li><a href="{{ route('admin-docs') }}" class="home-sublink-item"><i class="fas fa-clipboard-list fa-fw home-sublink-icon" aria-hidden="true"></i><span>TRAIL FORM LIST</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 7. EQUIPMENT LISTS -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card" style="border-color: #fd7e14;">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle me-3" style="color: #fd7e14; border-color: #fd7e14;">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">EQUIPMENT LISTS</h5>
                            <p class="card-text small mb-0">Mechanic tools and expenses</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('mechanic-tools-expenses') }}" class="home-sublink-item"><i class="fas fa-toolbox fa-fw home-sublink-icon" aria-hidden="true"></i><span>MECHANIC TOOLS/EXPENSES</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 8. COMPANY DOCUMENTS (with SETTINGS as last link) -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card" style="border-color: #e91e63;">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle me-3" style="color: #e91e63; border-color: #e91e63;">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">COMPANY DOCUMENTS</h5>
                            <p class="card-text small mb-0">Employee list, contracts, memos, BOLO, settings</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('employees.index') }}" class="home-sublink-item"><i class="fas fa-users fa-fw home-sublink-icon" aria-hidden="true"></i><span>EMPLOYEE LIST</span></a></li>
                        <li><a href="{{ route('contracts.index') }}" class="home-sublink-item"><i class="fas fa-file-contract fa-fw home-sublink-icon" aria-hidden="true"></i><span>CONTRACTS</span></a></li>
                        <li><a href="{{ route('admin-docs') }}" class="home-sublink-item"><i class="fas fa-file-signature fa-fw home-sublink-icon" aria-hidden="true"></i><span>MEMORANDUMS</span></a></li>
                        <li><a href="{{ route('admin-docs') }}" class="home-sublink-item"><i class="fas fa-sticky-note fa-fw home-sublink-icon" aria-hidden="true"></i><span>MEMOS</span></a></li>
                        <li><a href="{{ route('document-templates.index') }}" class="home-sublink-item"><i class="fas fa-file-invoice fa-fw home-sublink-icon" aria-hidden="true"></i><span>AR FORM TEMPLATES</span></a></li>
                        <li><a href="{{ route('ar-template.index') }}" class="home-sublink-item"><i class="fas fa-file-code fa-fw home-sublink-icon" aria-hidden="true"></i><span>AR TEMPLATE</span></a></li>
                        <li><a href="{{ route('online-ar-bolo.index') }}" class="home-sublink-item"><i class="fas fa-globe fa-fw home-sublink-icon" aria-hidden="true"></i><span>ONLINE AR BOLO</span></a></li>
                        <li><a href="{{ route('agent-bolo.index') }}" class="home-sublink-item"><i class="fas fa-user-secret fa-fw home-sublink-icon" aria-hidden="true"></i><span>AGENT BOLO</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 9. ANALYTICS REPORT -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card" style="border-color: #20c997;">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle me-3" style="color: #20c997; border-color: #20c997;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">ANALYTICS REPORT</h5>
                            <p class="card-text small mb-0">Financial and sales analytics pages</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('analytics-report.financial') }}" class="home-sublink-item"><i class="fas fa-chart-line fa-fw home-sublink-icon" aria-hidden="true"></i><span>FINANCIAL REPORT</span></a></li>
                        <li><a href="{{ route('analytics-report.sales') }}" class="home-sublink-item"><i class="fas fa-chart-bar fa-fw home-sublink-icon" aria-hidden="true"></i><span>SALES REPORT</span></a></li>
                        <li><a href="{{ route('analytics-report.sales-executive') }}" class="home-sublink-item"><i class="fas fa-user-tie fa-fw home-sublink-icon" aria-hidden="true"></i><span>SALES EXECUTIVE REPORT</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Settings (after Company Documents) -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card" style="border-color: #6c757d;">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle me-3" style="color: #6c757d; border-color: #6c757d;">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">SETTINGS</h5>
                            <p class="card-text small mb-0">System and application settings</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('settings') }}" class="home-sublink-item"><i class="fas fa-cog fa-fw home-sublink-icon" aria-hidden="true"></i><span>APPLICATION SETTINGS</span></a></li>
                        <li><a href="{{ route('admin-docs') }}" class="home-sublink-item"><i class="fas fa-history fa-fw home-sublink-icon" aria-hidden="true"></i><span>USER ACTIVITY LOGS</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Compare -->
        <div class="col-12 col-sm-6 col-lg-4 category-card">
            <div class="card h-100 home-card" style="border-color: #6610f2;">
                <div class="card-body">
                    <div class="card-header-link" role="button" tabindex="0" aria-haspopup="dialog">
                        <div class="icon-circle me-3" style="color: #6610f2; border-color: #6610f2;">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">COMPARE</h5>
                            <p class="card-text small mb-0">Compare listings across competitor websites</p>
                        </div>
                    </div>
                    <ul class="category-sublinks">
                        <li><a href="{{ route('compare.index') }}" class="home-sublink-item"><i class="fas fa-balance-scale fa-fw home-sublink-icon" aria-hidden="true"></i><span>COMPARE CARS</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Category sub-menu modal -->
    <div class="modal fade" id="homeCategoryModal" tabindex="-1" aria-labelledby="homeCategoryModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="homeCategoryModalTitle">Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="homeCategoryModalBody"></div>
            </div>
        </div>
    </div>
    
    <!-- Logout button below buttons -->
    <div class="home-logout">
        <a href="{{ route('logout') }}" class="btn btn-outline-secondary">
            <i class="fas fa-sign-out-alt me-1"></i>Logout
        </a>
    </div>
</div>

<!-- Theme Toggle Button -->
<button class="home-theme-toggle" id="homeThemeToggle" title="Toggle Theme" aria-label="Toggle Theme" style="display: inline-flex !important;">
    <i class="fas fa-sun" style="display: none;"></i>
    <i class="fas fa-moon" style="display: block;"></i>
</button>
@endsection

@section('scripts')
<script>
// Apply theme immediately before DOM loads to prevent flash
(function() {
    const savedTheme = localStorage.getItem('homeTheme') || 'dark';
    if (document.body) {
        document.body.setAttribute('data-home-theme', savedTheme);
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            document.body.setAttribute('data-home-theme', savedTheme);
        });
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    // Get theme preference from localStorage or default to dark
    const savedTheme = localStorage.getItem('homeTheme') || 'dark';
    const body = document.body;
    const html = document.documentElement;
    
    // Ensure theme is applied to both body and html
    body.setAttribute('data-home-theme', savedTheme);
    html.setAttribute('data-home-theme', savedTheme);
    
    // Theme toggle functionality
    const themeToggle = document.getElementById('homeThemeToggle');
    
    if (themeToggle) {
        // Update icon visibility based on current theme
        function updateIcon(theme) {
            const sunIcon = themeToggle.querySelector('.fa-sun');
            const moonIcon = themeToggle.querySelector('.fa-moon');
            if (sunIcon && moonIcon) {
                if (theme === 'light') {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                }
            }
        }
        
        // Set initial icon
        updateIcon(savedTheme);
        
        themeToggle.addEventListener('click', function() {
            const currentTheme = body.getAttribute('data-home-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Update theme on both body and html
            body.setAttribute('data-home-theme', newTheme);
            html.setAttribute('data-home-theme', newTheme);
            localStorage.setItem('homeTheme', newTheme);
            
            // Update icon
            updateIcon(newTheme);
        });
    }

    // Category cards: open modal with sub-links (no hover menus)
    const homeCategoryModalEl = document.getElementById('homeCategoryModal');
    const homeCategoryModalTitleEl = document.getElementById('homeCategoryModalTitle');
    const homeCategoryModalBodyEl = document.getElementById('homeCategoryModalBody');

    // Modal must be under body or backdrop (z-index 1050) stacks above and blocks clicks
    if (homeCategoryModalEl && homeCategoryModalEl.parentElement !== document.body) {
        document.body.appendChild(homeCategoryModalEl);
    }

    function syncHomeModalTheme() {
        if (!homeCategoryModalEl) return;
        const t = body.getAttribute('data-home-theme') === 'light' ? 'light' : 'dark';
        homeCategoryModalEl.setAttribute('data-bs-theme', t);
    }

    function openHomeCategoryModal(card) {
        if (!homeCategoryModalEl || !homeCategoryModalTitleEl || !homeCategoryModalBodyEl) return;
        syncHomeModalTheme();
        const title = card.querySelector('.card-title');
        homeCategoryModalTitleEl.textContent = title ? title.textContent.trim() : 'Category';
        homeCategoryModalBodyEl.innerHTML = '';
        const sub = card.querySelector('.category-sublinks');
        if (sub && sub.querySelector('a')) {
            const clone = sub.cloneNode(true);
            clone.classList.remove('category-sublinks');
            clone.classList.add('home-modal-sublinks');
            homeCategoryModalBodyEl.appendChild(clone);
        } else {
            homeCategoryModalBodyEl.innerHTML = '<p class="text-body-secondary mb-0">No links in this category.</p>';
        }
        const modal = bootstrap.Modal.getOrCreateInstance(homeCategoryModalEl);
        modal.show();
    }

    document.querySelectorAll('.home-card').forEach(function(card) {
        card.addEventListener('click', function() {
            openHomeCategoryModal(card);
        });
        const header = card.querySelector('.card-header-link');
        if (header) {
            header.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openHomeCategoryModal(card);
                }
            });
        }
    });

    if (homeCategoryModalEl) {
        homeCategoryModalEl.addEventListener('show.bs.modal', syncHomeModalTheme);
    }
});
</script>
@endsection
