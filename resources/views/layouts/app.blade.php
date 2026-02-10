<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi CBT Sekolah')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --sidebar-bg: #ffffff;
            --main-bg: #f3f4f6;
            --accent-color: #4f46e5;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --sidebar-width: 260px;
            --sidebar-width-collapsed: 72px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--main-bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* --- SIDEBAR CONFIGURATION --- */
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            white-space: nowrap;
            overflow: hidden;
        }

        body.sidebar-collapsed .sidebar { width: var(--sidebar-width-collapsed); }

        .sidebar-header {
            height: 60px;
            display: flex;
            align-items: center;
            padding-left: 18px;
            margin-bottom: 5px;
        }

        .btn-toggle-sidebar {
            background: transparent;
            border: none;
            font-size: 1.2rem;
            color: var(--text-muted);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-toggle-sidebar:hover { background: #eff6ff; color: var(--accent-color); }

        .sidebar-brand {
            padding: 0 18px 20px;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
        }

        body.sidebar-collapsed .sidebar-brand span { display: none; opacity: 0; }
        body.sidebar-collapsed .sidebar-brand { padding: 0; justify-content: center; margin-bottom: 10px; }

        .sidebar .nav { padding: 0 10px; }
        .nav-item { margin-bottom: 4px; }

        .sidebar .nav-link {
            color: var(--text-muted);
            padding: 10px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.2s;
            height: 44px;
        }

        .sidebar .nav-link i { width: 24px; font-size: 1.1rem; margin-right: 12px; text-align: center; display: flex; justify-content: center; }
        .sidebar .nav-link:hover { background: #f9fafb; color: var(--accent-color); }
        .sidebar .nav-link.active { background: var(--accent-color); color: #fff !important; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }

        body.sidebar-collapsed .sidebar .nav-link { justify-content: center; padding: 10px 0; }
        body.sidebar-collapsed .sidebar .nav-link span, body.sidebar-collapsed .nav-section-title { display: none; }
        body.sidebar-collapsed .sidebar .nav-link i { margin-right: 0; }

        .nav-section-title { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin: 15px 0 5px 15px; font-weight: 700; }

        /* --- MAIN CONTENT LAYOUT --- */
        .main-content { margin-left: var(--sidebar-width); padding: 1.5rem 2rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        body.sidebar-collapsed .main-content { margin-left: var(--sidebar-width-collapsed); }

        /* Navbar Atas - Perbaikan Padding & Flex */
        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 0.8rem 1.2rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255,255,255,0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .user-profile-img { width: 40px; height: 40px; border-radius: 12px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

        .btn-logout { color: #ef4444 !important; background: #fef2f2; }
        .btn-logout:hover { background: #fee2e2 !important; }

        .btn-mobile-toggle { display: none; }

        /* Style khusus untuk tombol kembali di Navbar */
        .nav-back-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            margin-right: 15px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .nav-back-btn:hover {
            background: #f9fafb;
            color: var(--accent-color);
            border-color: var(--accent-color);
        }

        @media (max-width: 992px) {
            .sidebar { left: -100%; width: var(--sidebar-width); } 
            .main-content { margin-left: 0; padding: 1rem; }
            body.mobile-sidebar-active .sidebar { left: 0; }
            .btn-mobile-toggle { display: block; background: transparent; border: none; font-size: 1.4rem; color: var(--text-main); margin-right: 15px; }
            .sidebar-header .btn-toggle-sidebar { display: none; }
            .sidebar-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; display: none; opacity: 0; transition: opacity 0.3s; }
            body.mobile-sidebar-active .sidebar-overlay { display: block; opacity: 1; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar">
        <div class="sidebar-header">
            <button class="btn-toggle-sidebar" id="sidebarToggle" title="Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="sidebar-brand">
            <div class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="min-width: 36px; height: 36px;">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <span>SmartCBT</span>
        </div>

        <ul class="nav flex-column flex-grow-1">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.guru') ? 'active' : '' }}" href="{{ route('dashboard.guru') }}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </li>

            <div class="nav-section-title">Master Data</div>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('kelas.*') ? 'active' : '' }}" href="{{ route('kelas.index') }}">
                    <i class="fas fa-layer-group"></i> <span>Data Kelas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}" href="{{ route('siswa.index') }}">
                    <i class="fas fa-user-graduate"></i> <span>Data Siswa</span>
                </a>
            </li>

            <div class="nav-section-title">Akademik</div>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ujian.*') ? 'active' : '' }}" href="{{ route('ujian.index') }}"> 
                    <i class="fas fa-file-alt"></i> <span>Manajemen Ujian</span>
                </a>
            </li>

            <li class="nav-item mt-auto mb-3 pt-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link btn-logout w-100 border-0">
                        <i class="fas fa-sign-out-alt"></i> <span>Keluar</span>
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn-mobile-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="d-flex align-items-center">
                    @yield('back-button')
                    <div>
                        <h5 class="m-0 fw-bold text-dark">@yield('nav-greeting', 'Selamat Datang!')</h5>
                        <small class="text-muted d-none d-md-block">
                            @yield('nav-description')
                        </small>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <span class="fw-semibold d-block" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                    <span class="badge bg-soft-primary text-primary" style="font-size: 0.7rem; background: #e0e7ff;">Administrator</span>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=4f46e5&color=fff" class="user-profile-img">
            </div>
        </nav>

        <div class="content-body">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;
            const toggleBtn = document.getElementById('sidebarToggle');
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            
            if (isCollapsed && window.innerWidth > 992) {
                body.classList.add('sidebar-collapsed');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
                });
            }

            window.toggleSidebar = function() {
                body.classList.toggle('mobile-sidebar-active');
            }

            setTimeout(function () {
                document.querySelectorAll('.alert').forEach(function (alert) {
                    new bootstrap.Alert(alert).close();
                });
            }, 3000);
        });
    </script>
    @yield('scripts')
</body>
</html>