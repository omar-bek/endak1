<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Endak - ' . __('messages.welcome_title'))</title>

    <!-- Bootstrap CSS -->
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #685C84;
            --primary-dark: #675B83;
            --secondary-color: #6A5E86;
            --accent-color: #766A90;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #675B83;
            --darker-color: #685C84;
            --light-color: #F8F6F7;
            --lighter-color: #F6F4F5;
            --text-color: #675B83;
            --text-muted: #9E96AE;
            --border-color: #B1A9BE;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #685C84 0%, #6A5E86 25%, #766A90 50%, #B1A9BE 75%, #F8F6F7 100%);
            background-attachment: fixed;
            color: var(--text-color);
            line-height: 1.7;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* RTL Support */
        [dir="rtl"] {
            text-align: right;
        }

        [dir="ltr"] {
            text-align: left;
        }

        /* Glass Morphism Effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-lg);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--shadow-xl);
        }

        /* Navigation */
        .navbar {
            background: rgba(104, 92, 132, 0.95) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-lg);
            padding: 1rem 0;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1000;
        }

        .navbar.scrolled {
            background: rgba(103, 91, 131, 0.98) !important;
            padding: 0.5rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #B1A9BE, #F8F6F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.02);
        }

        .nav-link {
            font-weight: 500;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.9) !important;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            margin: 0 0.15rem;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white !important;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #685C84 0%, #6A5E86 25%, #766A90 50%, #B1A9BE 75%, #F8F6F7 100%);
            color: white;
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
            min-height: 60vh;
            display: flex;
            align-items: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="0,100 1000,0 1000,100"/></svg>');
            background-size: cover;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff, #f0f9ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1rem;
            font-weight: 400;
            margin-bottom: 1.5rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        /* Flash Messages */
        .alert {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            font-weight: 500;
            animation: slideInDown 0.5s ease-out;
        }

        .alert-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-left: 4px solid #047857;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-left: 4px solid #b91c1c;
        }

        .alert-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border-left: 4px solid #b45309;
        }

        .alert-info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border-left: 4px solid #1d4ed8;
        }

        .alert .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .alert .btn-close:hover {
            opacity: 1;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            background: var(--lighter-color);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: linear-gradient(135deg, #685C84, #6A5E86);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 1rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 24px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            cursor: pointer;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #685C84, #675B83);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
            background: linear-gradient(135deg, #675B83, #685C84);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
            background: linear-gradient(135deg, #059669, var(--success-color));
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #685C84;
            color: #685C84;
        }

        .btn-outline:hover {
            background: #685C84;
            color: white;
            transform: translateY(-3px);
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #675B83 0%, #685C84 100%);
            color: white;
            padding: 80px 0 40px;
            position: relative;
            margin-top: 100px;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        }

        .footer h5 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #F8F6F7;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: #B1A9BE;
            transform: translateX(5px);
        }

        /* Category Cards with Images */
        .category-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            border-radius: 16px;
            background: var(--lighter-color);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .category-image-container {
            position: relative;
            height: 150px;
            overflow: hidden;
        }

        .category-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .category-card:hover .category-image {
            transform: scale(1.05);
        }

        .category-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            padding: 20px 15px 15px;
            color: white;
        }

        .category-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Category card body adjustments for 5 columns */
        .category-card .card-body {
            padding: 0.75rem;
        }

        .category-card .btn-sm {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
        }

                /* Subcategory Images */
        .subcategory-image-container {
            position: relative;
            height: 120px;
            overflow: hidden;
        }

        .subcategory-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .subcategory-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            padding: 15px 10px 10px;
            color: white;
        }

        .subcategory-title {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .card:hover .subcategory-image {
            transform: scale(1.05);
        }

        /* Category Header Image */
        .category-header-image img {
            transition: transform 0.3s ease;
        }

        .category-header-image:hover img {
            transform: scale(1.02);
        }

        /* Responsive adjustments for category images */
        @media (max-width: 768px) {
            .category-image-container {
                height: 120px;
            }

            .category-title {
                font-size: 0.9rem;
            }

            .subcategory-image-container {
                height: 100px;
            }

            .subcategory-title {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .category-image-container {
                height: 100px;
            }

            .category-title {
                font-size: 0.8rem;
            }
        }

        /* Language Switcher */
        .language-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 10px 18px;
            border-radius: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            font-weight: 600;
        }

        .language-btn:hover {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Notifications */
        .notification-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(20px);
            background: rgba(255,255,255,0.95);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            min-width: 200px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .dropdown-menu.show {
            display: block;
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            border-radius: 8px;
            margin: 0.15rem 0.5rem;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.85rem;
            display: block;
            text-decoration: none;
            color: var(--text-color);
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, #685C84, #6A5E86);
            color: white;
            transform: translateX(5px);
            text-decoration: none;
        }

        /* Fix dropdown positioning */
        .dropdown {
            position: relative;
        }

        .dropdown-menu-end {
            left: auto !important;
            right: 0 !important;
        }

        [dir="rtl"] .dropdown-menu {
            left: auto;
            right: 0;
        }

        [dir="rtl"] .dropdown-menu-end {
            right: auto !important;
            left: 0 !important;
        }

        /* Ensure dropdowns are visible */
        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        /* Override Bootstrap dropdown styles */
        .dropdown-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block !important;
        }

        /* Fix dropdown positioning */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: auto;
        }

        [dir="rtl"] .dropdown-menu {
            left: auto;
            right: 0;
        }

        /* Ensure proper z-index */
        .navbar-nav .dropdown-menu {
            z-index: 1050;
        }

        /* Force dropdown visibility */
        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
        }

        /* Ensure dropdown container is positioned correctly */
        .navbar-nav .dropdown {
            position: relative;
        }

        /* Fix for RTL dropdowns */
        [dir="rtl"] .dropdown-menu-end {
            right: auto !important;
            left: 0 !important;
        }

        /* Animation for dropdown appearance */
        .dropdown-menu {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Ensure dropdowns don't get cut off */
        .navbar-nav {
            position: relative;
        }

        .navbar-nav .dropdown {
            position: static;
        }

        .navbar-nav .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: auto;
            margin-top: 0.5rem;
        }

        /* Fix for dropdown-menu-end */
        .navbar-nav .dropdown-menu-end {
            left: auto;
            right: 0;
        }

        /* Ensure proper stacking context */
        .navbar > .container {
            position: relative;
            z-index: 1001;
        }

        /* Ensure user dropdown is above other elements */
        .nav-item.dropdown {
            position: relative;
            z-index: 1002;
        }

        .nav-item.dropdown .dropdown-menu {
            z-index: 1003;
            position: absolute;
            top: 100%;
            left: 0;
            right: auto;
        }

        [dir="rtl"] .nav-item.dropdown .dropdown-menu {
            left: auto;
            right: 0;
        }

        /* Debug styles - remove if working */
        .dropdown-menu {
            border: 1px solid red !important;
        }

        .dropdown-menu.show {
            border: 1px solid green !important;
        }

        /* Simple dropdown fix */
        .dropdown-menu {
            display: none !important;
        }

        .dropdown-menu.show {
            display: block !important;
        }

        /* Force all dropdowns to work */
        .dropdown-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Ensure dropdown positioning */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
        }

        /* Specific styles for user dropdown */
        .nav-item.dropdown .dropdown-menu {
            min-width: 200px;
            margin-top: 0.5rem;
        }

        .nav-item.dropdown .dropdown-item {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item.dropdown .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        /* Ensure user dropdown is visible */
        .nav-item.dropdown .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
        }

        /* Force user dropdown to be visible when shown */
        .nav-item.dropdown .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
        }

        /* Override any Bootstrap hiding */
        .nav-item.dropdown .dropdown-menu.show {
            display: block !important;
        }

        /* Ensure dropdown is not hidden by overflow */
        .navbar-nav {
            overflow: visible;
        }

        .navbar {
            overflow: visible;
        }

        /* Force user dropdown visibility with important */
        .nav-item.dropdown .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
            z-index: 9999 !important;
        }

        /* Ensure proper stacking */
        .nav-item.dropdown {
            position: relative;
            z-index: 9998;
        }

        /* Ensure proper positioning for user dropdown */
        .nav-item.dropdown {
            position: relative;
        }

        .nav-item.dropdown .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: auto;
            margin-top: 0.5rem;
            min-width: 200px;
            background: white;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        [dir="rtl"] .nav-item.dropdown .dropdown-menu {
            left: auto;
            right: 0;
        }

        /* Force user dropdown to be visible */
        .nav-item.dropdown .dropdown-menu {
            display: none;
        }

        .nav-item.dropdown .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
        }

        /* Additional force for user dropdown */
        .nav-item.dropdown .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
            z-index: 9999 !important;
        }

        /* Force user dropdown to be visible with all properties */
        .nav-item.dropdown .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
            z-index: 9999 !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: auto !important;
        }

        [dir="rtl"] .nav-item.dropdown .dropdown-menu.show {
            left: auto !important;
            right: 0 !important;
        }

        /* Ensure user dropdown is not hidden */
        .nav-item.dropdown {
            overflow: visible;
        }

        .navbar-nav {
            overflow: visible;
        }

        /* Force user dropdown to be visible with all properties */
        .nav-item.dropdown .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
            z-index: 9999 !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: auto !important;
            background: white !important;
            border: 1px solid rgba(0,0,0,0.1) !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            min-width: 200px !important;
            margin-top: 0.5rem !important;
        }

        [dir="rtl"] .nav-item.dropdown .dropdown-menu.show {
            left: auto !important;
            right: 0 !important;
        }

        /* Ensure user dropdown items are visible */
        .nav-item.dropdown .dropdown-menu.show .dropdown-item {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            color: #333 !important;
            background: transparent !important;
        }

        .nav-item.dropdown .dropdown-menu.show .dropdown-item:hover {
            background: rgba(0,0,0,0.05) !important;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        .slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }

        .slide-in-right {
            animation: slideInRight 0.8s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0 40px;
                min-height: 50vh;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 0.9rem;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .nav-link {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem !important;
            }

            .card {
                margin-bottom: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .btn {
                padding: 8px 20px;
                font-size: 0.85rem;
            }

            .category-card {
                padding: 1.5rem 0.8rem;
            }

            .category-icon {
                font-size: 3rem;
            }

            .dropdown-menu {
                min-width: 180px;
            }

            .dropdown-item {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        }

        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .shadow-custom {
            box-shadow: var(--shadow-xl);
        }

        .border-gradient {
            border: 2px solid;
            border-image: linear-gradient(135deg, var(--primary-color), var(--accent-color)) 1;
        }

        /* Modern Footer Navigation */
        .footer-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 8px 0;
            transition: all 0.3s ease;
        }

        .footer-nav-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 16px;
        }

        .footer-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #6b7280;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 12px;
            position: relative;
            min-width: 60px;
        }

        .footer-nav-item:hover {
            color: var(--primary-color);
            transform: translateY(-2px);
            text-decoration: none;
        }

        .footer-nav-item.active {
            color: var(--primary-color);
        }

        .footer-nav-icon {
            position: relative;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
            transition: all 0.3s ease;
        }

        .footer-nav-icon i {
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .footer-nav-item:hover .footer-nav-icon i {
            transform: scale(1.1);
        }

        .footer-nav-text {
            font-size: 11px;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
            transition: all 0.3s ease;
        }

        /* Center Button (نشر إعلان) */
        .footer-nav-center {
            position: relative;
        }

        .footer-nav-icon-center {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #8b5cf6, #a855f7);
            border-radius: 50%;
            margin-bottom: 4px;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
            transition: all 0.3s ease;
        }

        .footer-nav-icon-center i {
            color: white;
            font-size: 20px;
            font-weight: bold;
        }

        .footer-nav-center:hover .footer-nav-icon-center {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }

        .footer-nav-center .footer-nav-text {
            color: #8b5cf6;
            font-weight: 600;
        }

        .footer-nav-center:hover .footer-nav-text {
            color: #7c3aed;
        }

        /* Special styling for provider center button */
        .footer-nav-center[href*="service-offers"] .footer-nav-icon-center {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .footer-nav-center[href*="service-offers"]:hover .footer-nav-icon-center {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .footer-nav-center[href*="service-offers"] .footer-nav-text {
            color: #10b981;
        }

        .footer-nav-center[href*="service-offers"]:hover .footer-nav-text {
            color: #059669;
        }

        /* Badge for messages and notifications */
        .footer-nav-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            animation: pulse 2s infinite;
        }

        .footer-nav-badge-messages {
            background: #ef4444;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        }

        .footer-nav-badge-notifications {
            background: #f59e0b;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
        }

        /* Active state for navigation items */
        .footer-nav-item.active .footer-nav-icon i {
            color: var(--primary-color);
        }

        .footer-nav-item.active .footer-nav-text {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .footer-nav-container {
                padding: 0 8px;
            }

            .footer-nav-item {
                padding: 6px 8px;
                min-width: 50px;
            }

            .footer-nav-icon {
                width: 20px;
                height: 20px;
            }

            .footer-nav-icon i {
                font-size: 18px;
            }

            .footer-nav-text {
                font-size: 10px;
            }

            .footer-nav-icon-center {
                width: 40px;
                height: 40px;
            }

            .footer-nav-icon-center i {
                font-size: 18px;
            }
        }

        /* Add padding to body to prevent content from being hidden behind footer */
        body {
            padding-bottom: 80px;
        }

        /* Hide footer nav on desktop if needed */
        @media (min-width: 769px) {
            .footer-nav {
                display: none;
            }

            body {
                padding-bottom: 0;
            }
        }

        /* Show footer nav on mobile */
        @media (max-width: 768px) {
            .footer-nav {
                display: block;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-tools me-2"></i>Endak
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">{{ __('messages.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}">{{ __('messages.categories') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('services.index') }}">{{ __('messages.services') }}</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('services.my-services') }}">{{ __('messages.my_services') }}</a>
                        </li>
                        @if(Auth::user()->isProvider())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('service-offers.my-offers') }}">{{ __('messages.my_offers') }}</a>
                            </li>
                        @endif
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">{{ __('messages.contact_us') }}</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <!-- Language Switcher -->
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link dropdown-toggle language-btn" href="#" role="button">
                            <i class="fas fa-globe me-1"></i>{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('language.switch', 'ar') }}">
                                <i class="fas fa-flag me-2"></i>العربية
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">
                                <i class="fas fa-flag me-2"></i>English
                            </a></li>
                        </ul>
                    </li>

                    <!-- Messages -->
                    @auth
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link position-relative dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-comments"></i>
                                <span id="messages-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem; padding: 0.2rem 0.4rem; display: none;">
                                    0
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="width: 280px; max-height: 300px; overflow-y: auto;">
                                <li class="dropdown-header d-flex justify-content-between align-items-center" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                                    <span>الرسائل</span>
                                    <a href="{{ route('messages.index') }}" class="text-decoration-none" style="font-size: 0.75rem;">عرض الكل</a>
                                </li>
                                <li><span class="dropdown-item-text text-muted" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;">آخر الرسائل ستظهر هنا</span></li>
                                <li><hr class="dropdown-divider" style="margin: 0.3rem 0;"></li>
                                <li><a class="dropdown-item text-center" href="{{ route('messages.index') }}" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;">عرض جميع الرسائل</a></li>
                            </ul>
                        </li>
                    @endauth
                    <!-- Notifications -->
                    @auth
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link position-relative dropdown-toggle" href="#" role="button">
                                <i class="fas fa-bell"></i>
                                @if(Auth::user()->unread_notifications_count > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">
                                        {{ Auth::user()->unread_notifications_count > 99 ? '99+' : Auth::user()->unread_notifications_count }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="width: 280px; max-height: 300px; overflow-y: auto;">
                                <li class="dropdown-header d-flex justify-content-between align-items-center" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                                    <span>الإشعارات</span>
                                    @if(Auth::user()->unread_notifications_count > 0)
                                        <a href="{{ route('notifications.index') }}" class="text-decoration-none" style="font-size: 0.75rem;">عرض الكل</a>
                                    @endif
                                </li>
                                @if(Auth::user()->unread_notifications->count() > 0)
                                    @foreach(Auth::user()->unread_notifications->take(4) as $notification)
                                        @php
                                            $notificationUrl = route('notifications.index');
                                            if ($notification->data && isset($notification->data['offer_id'])) {
                                                $offer = \App\Models\ServiceOffer::find($notification->data['offer_id']);
                                                if ($offer) {
                                                    $notificationUrl = route('service-offers.show', $offer->id);
                                                }
                                            }
                                        @endphp
                                        <li>
                                            <a class="dropdown-item py-1" href="{{ $notificationUrl }}" style="padding: 0.4rem 0.75rem;">
                                                <div class="d-flex align-items-start">
                                                    <i class="{{ $notification->icon }} me-2 mt-1" style="font-size: 0.9rem;"></i>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold" style="font-size: 0.8rem;">{{ $notification->title }}</div>
                                                        <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($notification->message, 40) }}</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $notification->created_at ? $notification->created_at->diffForHumans() : 'غير محدد' }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li><span class="dropdown-item-text text-muted" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;">لا توجد إشعارات جديدة</span></li>
                                @endif
                                <li><hr class="dropdown-divider" style="margin: 0.3rem 0;"></li>
                                <li><a class="dropdown-item text-center" href="{{ route('notifications.index') }}" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;">عرض جميع الإشعارات</a></li>
                            </ul>
                        </li>
                    @endauth

                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button">
                                @if(Auth::user()->image && file_exists(public_path('storage/' . Auth::user()->image)))
                                    <img src="{{ asset('storage/' . Auth::user()->image) }}"
                                         alt="{{ Auth::user()->name }}"
                                         class="rounded-circle me-2"
                                         style="width: 30px; height: 30px; object-fit: cover;"
                                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
                                @else
                                    <div class="rounded-circle me-2 d-flex align-items-center justify-content-center bg-primary text-white"
                                         style="width: 30px; height: 30px; font-size: 14px; font-weight: bold;">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('messages.new-design') }}">
                                    <i class="fas fa-comments"></i> الرسائل الجديدة
                                    <span id="messages-badge-menu" class="badge bg-danger ms-2" style="font-size: 0.7rem; padding: 0.2rem 0.4rem; display: none;">
                                        0
                                    </span>
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('messages.index') }}">
                                    <i class="fas fa-comments"></i> الرسائل القديمة
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('notifications.index') }}">
                                    <i class="fas fa-bell"></i> الإشعارات
                                    @if(Auth::user()->unread_notifications_count > 0)
                                        <span class="badge bg-danger ms-2" style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">{{ Auth::user()->unread_notifications_count }}</span>
                                    @endif
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('services.my-services') }}">
                                    <i class="fas fa-list"></i> {{ __('messages.my_services') }}
                                </a></li>
                                @if(Auth::user()->isProvider())
                                    <li><a class="dropdown-item" href="{{ route('service-offers.my-offers') }}">
                                        <i class="fas fa-handshake"></i> {{ __('messages.my_offers') }}
                                    </a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ Auth::user()->isProvider() ? route('provider.profile') : route('profile') }}">
                                    <i class="fas fa-user-edit"></i> {{ __('messages.profile') }}
                                </a></li>
                                @if(Auth::user()->isProvider() && !Auth::user()->hasCompleteProviderProfile())
                                    <li><a class="dropdown-item text-warning" href="{{ route('provider.complete-profile') }}">
                                        <i class="fas fa-exclamation-triangle"></i> إكمال الملف الشخصي
                                    </a></li>
                                @endif
                                @if(Auth::user()->isProvider() && Auth::user()->hasCompleteProviderProfile())
                                    <li><a class="dropdown-item text-primary" href="{{ route('provider.profile.edit') }}">
                                        <i class="fas fa-edit"></i> تعديل الملف الشخصي
                                    </a></li>
                                @endif
                                @if(Auth::user()->is_admin)
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-cog"></i> {{ __('messages.admin_panel') }}
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider" style="margin: 0.3rem 0;"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> {{ __('messages.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('messages.login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('messages.register') }}</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="container mt-4">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="container mt-4">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-tools me-2"></i>Endak</h5>
                    <p>{{ __('messages.welcome_subtitle') }}</p>
                </div>
                <div class="col-md-4">
                    <h5>{{ __('messages.quick_links') }}</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-light">{{ __('messages.home') }}</a></li>
                        <li><a href="{{ route('categories.index') }}" class="text-light">{{ __('messages.categories') }}</a></li>
                        <li><a href="{{ route('services.index') }}" class="text-light">{{ __('messages.services') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="text-light">{{ __('messages.contact_us') }}</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>{{ __('messages.contact_info') }}</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} Endak. {{ __('messages.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    <!-- Modern Footer Navigation -->
    <nav class="footer-nav">
        <div class="footer-nav-container">
            <a href="{{ route('home') }}" class="footer-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <div class="footer-nav-icon">
                    <i class="fas fa-home"></i>
                </div>
                <span class="footer-nav-text">الرئيسية</span>
            </a>

            @auth
                @if(Auth::user()->isProvider())
                    <!-- مزود الخدمة -->
                    <a href="{{ route('services.index') }}" class="footer-nav-item {{ request()->routeIs('services.my-services') ? 'active' : '' }}">
                        <div class="footer-nav-icon">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <span class="footer-nav-text">الخدمات</span>
                    </a>
                @else
                    <!-- مستخدم عادي -->
                    <a href="{{ route('services.my-services') }}" class="footer-nav-item {{ request()->routeIs('services.my-services') ? 'active' : '' }}">
                        <div class="footer-nav-icon">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <span class="footer-nav-text">إعلاناتي</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="footer-nav-item">
                    <div class="footer-nav-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <span class="footer-nav-text">إعلاناتي</span>
                </a>
            @endauth

            @auth
                @if(Auth::user()->isProvider())
                    <!-- مزود الخدمة - عروضي -->
                    <a href="{{ route('service-offers.my-offers') }}" class="footer-nav-item footer-nav-center {{ request()->routeIs('service-offers.my-offers') ? 'active' : '' }}">
                        <div class="footer-nav-icon footer-nav-icon-center">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <span class="footer-nav-text">عروضي</span>
                    </a>
                @else
                    <!-- مستخدم عادي - نشر إعلان -->
                    <a href="{{ route('categories.index') }}" class="footer-nav-item footer-nav-center">
                        <div class="footer-nav-icon footer-nav-icon-center">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span class="footer-nav-text">نشر إعلان</span>
                    </a>
                @endif
            @else
                <!-- غير مسجل دخول -->
                <a href="{{ route('categories.index') }}" class="footer-nav-item footer-nav-center">
                    <div class="footer-nav-icon footer-nav-icon-center">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span class="footer-nav-text">نشر إعلان</span>
                </a>
            @endauth

            @auth
                <a href="{{ route('messages.index') }}" class="footer-nav-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                    <div class="footer-nav-icon">
                        <i class="fas fa-comments"></i>
                        @if(Auth::user()->unread_messages_count > 0)
                            <span class="footer-nav-badge footer-nav-badge-messages">{{ Auth::user()->unread_messages_count > 99 ? '99+' : Auth::user()->unread_messages_count }}</span>
                        @endif
                    </div>
                    <span class="footer-nav-text">الرسائل</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="footer-nav-item">
                    <div class="footer-nav-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <span class="footer-nav-text">الرسائل</span>
                </a>
            @endauth

            @auth
                <a href="{{ route('notifications.index') }}" class="footer-nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <div class="footer-nav-icon">
                        <i class="fas fa-bell"></i>
                        @if(Auth::user()->unread_notifications_count > 0)
                            <span class="footer-nav-badge footer-nav-badge-notifications">{{ Auth::user()->unread_notifications_count > 99 ? '99+' : Auth::user()->unread_notifications_count }}</span>
                        @endif
                    </div>
                    <span class="footer-nav-text">الإشعارات</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="footer-nav-item">
                    <div class="footer-nav-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <span class="footer-nav-text">الإشعارات</span>
                </a>
            @endauth

            <a href="#" class="footer-nav-item" onclick="toggleMenu()">
                <div class="footer-nav-icon">
                    <i class="fas fa-bars"></i>
                </div>
                <span class="footer-nav-text">القائمة</span>
            </a>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Enhanced JavaScript -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add animation classes on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all cards and sections
        document.querySelectorAll('.card, .category-card, .section').forEach(el => {
            observer.observe(el);
        });

                // Enhanced button effects
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.01)';
            });

            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Auto-hide flash messages after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                if (alert && !alert.classList.contains('alert-dismissed')) {
                    alert.classList.add('alert-dismissed');
                    alert.style.animation = 'slideOutUp 0.5s ease-in forwards';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 500);
                }
            }, 5000);
        });

        // Add slideOutUp animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideOutUp {
                from {
                    transform: translateY(0);
                    opacity: 1;
                }
                to {
                    transform: translateY(-100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Card hover effects
        document.querySelectorAll('.card, .category-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.01)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Enhanced dropdown functionality
                        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing dropdowns...');

            // Simple dropdown functionality
            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const dropdown = this.closest('.dropdown');
                    const menu = dropdown.querySelector('.dropdown-menu');

                    // Close all other dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(otherMenu => {
                        if (otherMenu !== menu) {
                            otherMenu.classList.remove('show');
                        }
                    });

                    // Toggle current dropdown
                    menu.classList.toggle('show');

                    console.log('Dropdown toggled:', menu.classList.contains('show'));
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });

            // Close dropdowns when pressing Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });

            // Add hover effect for dropdown items
            document.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });

            // Debug: Log all dropdown elements
            console.log('All dropdown toggles:', document.querySelectorAll('.dropdown-toggle'));
            console.log('All dropdown menus:', document.querySelectorAll('.dropdown-menu'));
            console.log('User dropdown:', document.querySelector('.nav-item.dropdown'));

            // Test user dropdown specifically
            const userDropdown = document.querySelector('.nav-item.dropdown .dropdown-toggle');
            if (userDropdown) {
                console.log('User dropdown found:', userDropdown);
                userDropdown.addEventListener('click', function(e) {
                    console.log('User dropdown clicked!');
                    const menu = this.closest('.dropdown').querySelector('.dropdown-menu');
                    console.log('User menu:', menu);
                    if (menu) {
                        menu.style.display = 'block';
                        menu.style.opacity = '1';
                        menu.style.visibility = 'visible';
                        menu.style.transform = 'translateY(0)';
                        menu.style.zIndex = '9999';
                        console.log('User menu forced to show');
                    }
                });
            }

            // Additional dropdown fix
            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    const menu = this.nextElementSibling;
                    if (menu && menu.classList.contains('dropdown-menu')) {
                        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                    }
                });
            });
        });

        // Loading states for forms
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<span class="loading"></span> جاري الإرسال...';
                    submitBtn.disabled = true;
                }
            });
        });

        // Tooltip initialization
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Messages Update Script
        @auth
        function updateMessagesCount() {
            fetch('{{ route("messages.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const badge = document.getElementById('messages-badge');
                        const menuBadge = document.getElementById('messages-badge-menu');

                        if (data.count > 0) {
                            const count = data.count > 99 ? '99+' : data.count;
                            if (badge) {
                                badge.textContent = count;
                                badge.style.display = 'block';
                            }
                            if (menuBadge) {
                                menuBadge.textContent = count;
                                menuBadge.style.display = 'inline-block';
                            }
                        } else {
                            if (badge) {
                                badge.style.display = 'none';
                            }
                            if (menuBadge) {
                                menuBadge.style.display = 'none';
                            }
                        }
                    }
                })
                .catch(error => console.log('Error updating messages count:', error));
        }

        // تحديث عدد الرسائل كل 30 ثانية
        setInterval(updateMessagesCount, 30000);
        updateMessagesCount();
        @endauth

        // Notifications Update Script
        @auth
        setInterval(function() {
            fetch('/notifications/unread')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.badge');
                    const notificationCount = data.count;

                    if (notificationCount > 0) {
                        if (badge) {
                            badge.textContent = notificationCount > 99 ? '99+' : notificationCount;
                            badge.classList.add('notification-badge');
                        } else {
                            const bell = document.querySelector('.fa-bell');
                            if (bell && bell.parentElement) {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge';
                                newBadge.textContent = notificationCount > 99 ? '99+' : notificationCount;
                                bell.parentElement.appendChild(newBadge);
                            }
                        }
                    } else {
                        if (badge) {
                            badge.remove();
                        }
                    }
                })
                .catch(error => console.log('Error updating notifications:', error));
        }, 30000);
        @endauth

        // Parallax effect for hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero-section');
            if (parallax) {
                const speed = scrolled * 0.5;
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });

        // Search functionality enhancement
        const searchInput = document.querySelector('input[type="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const cards = document.querySelectorAll('.card, .category-card');

                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        card.style.display = 'block';
                        card.classList.add('fade-in-up');
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }

        // Theme toggle (if needed)
        const themeToggle = document.querySelector('#theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-theme');
                localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
            });
        }

        // Initialize theme from localStorage
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
        }

        // Footer Navigation Functions
        function toggleMenu() {
            // Create a modal or dropdown menu for the menu button
            @auth
                @if(Auth::user()->isProvider())
                    // مزود الخدمة
                    const menuItems = [
                        { icon: 'fas fa-user', text: 'الملف الشخصي', href: '{{ route("provider.profile") }}' },
                        { icon: 'fas fa-concierge-bell', text: 'خدماتي', href: '{{ route("services.my-services") }}' },
                        { icon: 'fas fa-handshake', text: 'عروضي', href: '{{ route("service-offers.my-offers") }}' },
                        { icon: 'fas fa-cog', text: 'الإعدادات', href: '#' },
                        { icon: 'fas fa-question-circle', text: 'المساعدة', href: '{{ route("contact") }}' },
                        { icon: 'fas fa-info-circle', text: 'حول التطبيق', href: '#' }
                    ];
                @else
                    // مستخدم عادي
                    const menuItems = [
                        { icon: 'fas fa-user', text: 'الملف الشخصي', href: '{{ route("profile") }}' },
                        { icon: 'fas fa-th-large', text: 'إعلاناتي', href: '{{ route("services.my-services") }}' },
                        { icon: 'fas fa-cog', text: 'الإعدادات', href: '#' },
                        { icon: 'fas fa-question-circle', text: 'المساعدة', href: '{{ route("contact") }}' },
                        { icon: 'fas fa-info-circle', text: 'حول التطبيق', href: '#' }
                    ];
                @endif
            @else
                // غير مسجل دخول
                const menuItems = [
                    { icon: 'fas fa-sign-in-alt', text: 'تسجيل الدخول', href: '{{ route("login") }}' },
                    { icon: 'fas fa-user-plus', text: 'إنشاء حساب', href: '{{ route("register") }}' },
                    { icon: 'fas fa-question-circle', text: 'المساعدة', href: '{{ route("contact") }}' },
                    { icon: 'fas fa-info-circle', text: 'حول التطبيق', href: '#' }
                ];
            @endauth

            // Create menu modal
            const menuModal = document.createElement('div');
            menuModal.className = 'footer-menu-modal';
            menuModal.innerHTML = `
                <div class="footer-menu-overlay" onclick="closeMenu()"></div>
                <div class="footer-menu-content">
                    <div class="footer-menu-header">
                        <h6>القائمة</h6>
                        <button onclick="closeMenu()" class="footer-menu-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="footer-menu-items">
                        ${menuItems.map(item => `
                            <a href="${item.href}" class="footer-menu-item">
                                <i class="${item.icon}"></i>
                                <span>${item.text}</span>
                            </a>
                        `).join('')}
                        @auth
                            <hr class="footer-menu-divider">
                            <a href="{{ route('logout') }}" class="footer-menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>تسجيل الخروج</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <hr class="footer-menu-divider">
                            <a href="{{ route('login') }}" class="footer-menu-item">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>تسجيل الدخول</span>
                            </a>
                        @endauth
                    </div>
                </div>
            `;

            // Add modal styles
            const modalStyles = document.createElement('style');
            modalStyles.textContent = `
                .footer-menu-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    z-index: 9999;
                    display: flex;
                    align-items: flex-end;
                }

                .footer-menu-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    backdrop-filter: blur(5px);
                }

                .footer-menu-content {
                    background: white;
                    border-radius: 20px 20px 0 0;
                    width: 100%;
                    max-height: 70vh;
                    overflow-y: auto;
                    position: relative;
                    z-index: 1;
                    animation: slideUp 0.3s ease-out;
                }

                .footer-menu-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px 20px 10px;
                    border-bottom: 1px solid #f0f0f0;
                }

                .footer-menu-header h6 {
                    margin: 0;
                    font-weight: 600;
                    color: #333;
                }

                .footer-menu-close {
                    background: none;
                    border: none;
                    font-size: 18px;
                    color: #666;
                    cursor: pointer;
                    padding: 5px;
                    border-radius: 50%;
                    transition: all 0.3s ease;
                }

                .footer-menu-close:hover {
                    background: #f0f0f0;
                    color: #333;
                }

                .footer-menu-items {
                    padding: 10px 0 30px;
                }

                .footer-menu-item {
                    display: flex;
                    align-items: center;
                    padding: 15px 20px;
                    text-decoration: none;
                    color: #333;
                    transition: all 0.3s ease;
                    border-bottom: 1px solid #f8f8f8;
                }

                .footer-menu-item:hover {
                    background: #f8f9fa;
                    color: var(--primary-color);
                    text-decoration: none;
                }

                .footer-menu-item i {
                    width: 24px;
                    margin-left: 15px;
                    font-size: 16px;
                }

                .footer-menu-item span {
                    font-weight: 500;
                }

                .footer-menu-divider {
                    margin: 10px 0;
                    border: none;
                    border-top: 1px solid #f0f0f0;
                }

                @keyframes slideUp {
                    from {
                        transform: translateY(100%);
                    }
                    to {
                        transform: translateY(0);
                    }
                }
            `;

            document.head.appendChild(modalStyles);
            document.body.appendChild(menuModal);

            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            const modal = document.querySelector('.footer-menu-modal');
            if (modal) {
                modal.style.animation = 'slideDown 0.3s ease-out';
                setTimeout(() => {
                    modal.remove();
                    document.body.style.overflow = '';
                }, 300);
            }
        }

        // Add slideDown animation
        const slideDownStyle = document.createElement('style');
        slideDownStyle.textContent = `
            @keyframes slideDown {
                from {
                    transform: translateY(0);
                }
                to {
                    transform: translateY(100%);
                }
            }
        `;
        document.head.appendChild(slideDownStyle);

        // Update footer navigation badges
        @auth
        function updateFooterNavBadges() {
            // Update messages badge
            fetch('{{ route("messages.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const messagesBadge = document.querySelector('.footer-nav-badge-messages');
                        if (data.count > 0) {
                            const count = data.count > 99 ? '99+' : data.count;
                            if (messagesBadge) {
                                messagesBadge.textContent = count;
                                messagesBadge.style.display = 'flex';
                            } else {
                                // Create badge if it doesn't exist
                                const messagesIcon = document.querySelector('.footer-nav-item[href*="messages"] .footer-nav-icon');
                                if (messagesIcon) {
                                    const newBadge = document.createElement('span');
                                    newBadge.className = 'footer-nav-badge footer-nav-badge-messages';
                                    newBadge.textContent = count;
                                    messagesIcon.appendChild(newBadge);
                                }
                            }
                        } else {
                            if (messagesBadge) {
                                messagesBadge.style.display = 'none';
                            }
                        }
                    }
                })
                .catch(error => console.log('Error updating messages badge:', error));

            // Update notifications badge
            fetch('/notifications/unread')
                .then(response => response.json())
                .then(data => {
                    const notificationsBadge = document.querySelector('.footer-nav-badge-notifications');
                    if (data.count > 0) {
                        const count = data.count > 99 ? '99+' : data.count;
                        if (notificationsBadge) {
                            notificationsBadge.textContent = count;
                            notificationsBadge.style.display = 'flex';
                        } else {
                            // Create badge if it doesn't exist
                            const notificationsIcon = document.querySelector('.footer-nav-item[href*="notifications"] .footer-nav-icon');
                            if (notificationsIcon) {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'footer-nav-badge footer-nav-badge-notifications';
                                newBadge.textContent = count;
                                notificationsIcon.appendChild(newBadge);
                            }
                        }
                    } else {
                        if (notificationsBadge) {
                            notificationsBadge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.log('Error updating notifications badge:', error));
        }

        // Update badges every 30 seconds
        setInterval(updateFooterNavBadges, 30000);
        updateFooterNavBadges();
        @endauth

        // Handle login requirement for protected links
        function handleProtectedLink(event, href) {
            @guest
                event.preventDefault();
                // Show login modal or redirect to login
                if (confirm('يجب تسجيل الدخول أولاً. هل تريد الانتقال لصفحة تسجيل الدخول؟')) {
                    window.location.href = '{{ route("login") }}';
                }
            @endguest
        }
    </script>

    @stack('scripts')

    <style>
        /* تحسينات إضافية للرسائل */
        .dropdown-menu {
            border: none !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
            border-radius: 12px !important;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
        }

        .dropdown-item {
            transition: all 0.2s ease !important;
            border-radius: 8px !important;
            margin: 2px 8px !important;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%) !important;
            color: white !important;
            transform: translateX(-5px) !important;
        }

        /* تحسين البادج */
        .badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* تحسين الأزرار */
        .btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        /* تحسين النافبار */
        .navbar {
            backdrop-filter: blur(20px) !important;
        }

        /* تحسين الكروت */
        .card {
            transition: all 0.3s ease !important;
        }

        .card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
        }

        /* أيقونة الواتساب */
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 80px;
            left: 20px;
            background-color: #25d366;
            color: white;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
            animation: pulse-whatsapp 2s infinite;
        }

        .whatsapp-float:hover {
            background-color: #128c7e;
            color: white;
            text-decoration: none;
            transform: scale(1.1);
        }

        @keyframes pulse-whatsapp {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(37, 211, 102, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }

        /* تحسين للهواتف */
        @media (max-width: 768px) {
            .whatsapp-float {
                width: 50px;
                height: 50px;
                font-size: 24px;
                bottom: 70px;
                left: 15px;
            }
        }
    </style>

    <!-- أيقونة الواتساب -->
    <a href="https://wa.me/966500000000" class="whatsapp-float" target="_blank" title="تواصل معنا عبر الواتساب">
        <i class="fab fa-whatsapp"></i>
    </a>
</body>
</html>
