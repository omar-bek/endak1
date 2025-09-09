<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'لوحة الإدارة') - Endak</title>

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }

        .admin-sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            top: 0;
            right: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .admin-sidebar.collapsed {
            width: 70px;
        }

        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }

        .admin-sidebar .nav-link i {
            width: 20px;
            margin-left: 10px;
        }

        .admin-main {
            margin-right: 250px;
            transition: all 0.3s ease;
        }

        .admin-main.expanded {
            margin-right: 70px;
        }

        .admin-header {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 0;
        }

        .admin-content {
            padding: 30px;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .stats-card .icon {
            font-size: 2.5rem;
            color: #667eea;
        }

        .btn-toggle-sidebar {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-main {
                margin-right: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Admin Sidebar -->
    <nav class="admin-sidebar" id="adminSidebar">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-tools me-2"></i>
                    <span class="sidebar-text">Endak Admin</span>
                </h5>
                <button class="btn-toggle-sidebar" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <ul class="nav flex-column">
                <!-- لوحة التحكم -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="sidebar-text">لوحة التحكم</span>
                    </a>
                </li>

                <!-- إدارة المحتوى -->
                <li class="nav-item">
                    <div class="nav-link text-muted">
                        <i class="fas fa-folder"></i>
                        <span class="sidebar-text">إدارة المحتوى</span>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-th-large"></i>
                        <span class="sidebar-text">الأقسام الرئيسية</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.sub_categories.*') ? 'active' : '' }}" href="{{ route('admin.sub_categories.index') }}">
                        <i class="fas fa-layer-group"></i>
                        <span class="sidebar-text">الأقسام الفرعية</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.fields.*') ? 'active' : '' }}" href="{{ route('admin.categories.fields.index', 1) }}">
                        <i class="fas fa-list-alt"></i>
                        <span class="sidebar-text">حقول الأقسام</span>
                    </a>
                </li>

                <!-- إدارة المواقع -->
                <li class="nav-item">
                    <div class="nav-link text-muted">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="sidebar-text">إدارة المواقع</span>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}" href="{{ route('admin.cities.index') }}">
                        <i class="fas fa-city"></i>
                        <span class="sidebar-text">إدارة المدن</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.category-cities.*') ? 'active' : '' }}" href="{{ route('admin.category-cities.index') }}">
                        <i class="fas fa-link"></i>
                        <span class="sidebar-text">ربط الأقسام بالمدن</span>
                    </a>
                </li>

                <!-- إدارة الخدمات -->
                <li class="nav-item">
                    <div class="nav-link text-muted">
                        <i class="fas fa-cogs"></i>
                        <span class="sidebar-text">إدارة الخدمات</span>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}">
                        <i class="fas fa-concierge-bell"></i>
                        <span class="sidebar-text">جميع الخدمات</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.service-offers.*') ? 'active' : '' }}" href="{{ route('admin.service-offers.index') }}">
                        <i class="fas fa-handshake"></i>
                        <span class="sidebar-text">عروض الخدمات</span>
                    </a>
                </li>

                <!-- إدارة المستخدمين -->
                <li class="nav-item">
                    <div class="nav-link text-muted">
                        <i class="fas fa-users"></i>
                        <span class="sidebar-text">إدارة المستخدمين</span>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-user"></i>
                        <span class="sidebar-text">جميع المستخدمين</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.providers.*') ? 'active' : '' }}" href="{{ route('admin.providers.index') }}">
                        <i class="fas fa-user-tie"></i>
                        <span class="sidebar-text">مزودي الخدمات</span>
                    </a>
                </li>

                <!-- إدارة الطلبات -->
                <li class="nav-item">
                    <div class="nav-link text-muted">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="sidebar-text">إدارة الطلبات</span>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-list"></i>
                        <span class="sidebar-text">جميع الطلبات</span>
                    </a>
                </li>

                <!-- إدارة النظام -->
                <li class="nav-item">
                    <div class="nav-link text-muted">
                        <i class="fas fa-cog"></i>
                        <span class="sidebar-text">إدارة النظام</span>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.system-settings.*') ? 'active' : '' }}" href="{{ route('admin.system-settings.index') }}">
                        <i class="fas fa-sliders-h"></i>
                        <span class="sidebar-text">إعدادات النظام</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}" href="{{ route('admin.backups.index') }}">
                        <i class="fas fa-database"></i>
                        <span class="sidebar-text">النسخ الاحتياطية</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="{{ route('admin.logs.index') }}">
                        <i class="fas fa-file-alt"></i>
                        <span class="sidebar-text">سجلات النظام</span>
                    </a>
                </li>

                <!-- روابط خارجية -->
                <li class="nav-item mt-4">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        <span class="sidebar-text">العودة للموقع</span>
                    </a>
                </li>

                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="sidebar-text">تسجيل الخروج</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="admin-main" id="adminMain">
        <!-- Header -->
        <header class="admin-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">@yield('page-title', 'لوحة الإدارة')</h4>
                    <div class="d-flex align-items-center">
                        <span class="me-3">مرحباً، {{ Auth::user()->name }}</span>
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=667eea&color=fff"
                             alt="Avatar" class="rounded-circle" width="40" height="40">
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const main = document.getElementById('adminMain');
            const sidebarTexts = document.querySelectorAll('.sidebar-text');

            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');

            sidebarTexts.forEach(text => {
                text.style.display = sidebar.classList.contains('collapsed') ? 'none' : 'inline';
            });
        }

        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('show');
        }
    </script>

    @stack('scripts')
</body>
</html>
