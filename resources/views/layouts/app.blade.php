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
            --accent-color: #4f46e5; /* Modern Indigo */
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--main-bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* --- Sidebar Modern --- */
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
            padding: 1.5rem 1rem;
        }

        .sidebar-brand {
            padding: 0.5rem 1rem 2rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-item { margin-bottom: 5px; }

        .sidebar .nav-link {
            color: var(--text-muted);
            padding: 12px 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar .nav-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-right: 12px;
        }

        .sidebar .nav-link:hover {
            background: #f9fafb;
            color: var(--accent-color);
        }

        .sidebar .nav-link.active {
            background: var(--accent-color);
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #9ca3af;
            margin: 20px 0 10px 15px;
            font-weight: 700;
        }

        /* --- Main Content & Navbar --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s;
        }

        .top-navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 0.8rem 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .user-profile-img {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Tombol Logout Custom */
        .btn-logout {
            margin-top: auto;
            color: #ef4444 !important;
            background: #fef2f2;
            border: none;
        }
        .btn-logout:hover { background: #fee2e2 !important; }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { left: -260px; }
            .main-content { margin-left: 0; }
            .sidebar.active { left: 0; }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="bg-primary text-white p-2 rounded-3">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <span>SmartCBT</span>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.guru') ? 'active' : '' }}" href="{{ route('dashboard.guru') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <div class="nav-section-title">Master Data</div>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('kelas.*') ? 'active' : '' }}" href="{{ route('kelas.index') }}">
                    <i class="fas fa-layer-group"></i> Data Kelas
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}" href="{{ route('siswa.index') }}">
                    <i class="fas fa-user-graduate"></i> Data Siswa
                </a>
            </li>

            <div class="nav-section-title">Akademik</div>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ujian.*') ? 'active' : '' }}" href="{{ route('ujian.index') }}"> 
                    <i class="fas fa-file-alt me-2"></i> Manajemen Ujian
                </a>
            </li>

            <li class="nav-item mt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link btn-logout w-100 border-0">
                        <i class="fas fa-power-off"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-0 fw-bold text-dark">@yield('nav-greeting', 'Selamat Datang!')</h5>
                <small class="text-muted d-none d-md-block">
                    {{-- Jangan tulis Carbon di sini secara statis --}}
                    @yield('nav-description')
                </small>
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
                <div class="alert alert-success border-0 shadow-sm rounded-4 fade show" role="alert">
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
            // Pilih semua elemen alert
            var alerts = document.querySelectorAll('.alert');

            alerts.forEach(function (alert) {
                // Set waktu 3 detik (3000 milidetik)
                setTimeout(function () {
                    // Gunakan API Bootstrap untuk menutup alert dengan efek fade
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 3000);
            });
        });
    </script>

    @yield('scripts')
</body>
</html>