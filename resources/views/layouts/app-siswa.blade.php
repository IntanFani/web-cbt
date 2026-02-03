<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Area Siswa')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; overflow-x: hidden; }
        
        /* --- SIDEBAR STYLING --- */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #ffffff;
            color: #1b2347;
            position: fixed;
            top: 0; left: 0;
            transition: margin 0.3s; /* Efek geser halus */
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        
        /* Kelas untuk menyembunyikan sidebar (digeser ke kiri negatif) */
        .sidebar.toggled { margin-left: -260px; }

        .sidebar-brand { padding: 20px; font-size: 1.4rem; font-weight: 700; text-align: center; border-bottom: 1px solid rgba(0,0,0,0.05); color: #0ea5e9; }
        .sidebar-menu { padding: 20px 10px; }
        .nav-link { color: #64748b; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; display: flex; align-items: center; text-decoration: none; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { background-color: #e0f2fe; color: #0ea5e9; font-weight: 600; }
        .nav-link i { width: 25px; text-align: center; margin-right: 10px; font-size: 1.1rem; }
        .nav-title { font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; margin: 20px 0 10px 15px; font-weight: 700; letter-spacing: 0.5px; }

        /* --- MAIN CONTENT STYLING --- */
        .main-content {
            margin-left: 260px; /* Default ada margin kiri */
            padding: 20px;
            transition: margin 0.3s; /* Efek geser halus */
        }
        
        /* Jika sidebar disembunyikan, konten melebar penuh */
        .main-content.toggled { margin-left: 0; }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            /* Di layar kecil, sidebar default sembunyi */
            .sidebar { margin-left: -260px; }
            .sidebar.toggled { margin-left: 0; } /* Jika di-toggle, dia muncul */
            
            /* Di layar kecil, konten default penuh */
            .main-content { margin-left: 0; }
            
            /* Overlay Gelap saat sidebar muncul di HP */
            .sidebar-overlay {
                display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.5); z-index: 999;
            }
            .sidebar-overlay.active { display: block; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap me-2"></i>CBT SISWA
        </div>
        <div class="sidebar-menu">
            <div class="nav-title">Menu Utama</div>
            <a href="{{ route('dashboard.siswa') }}" class="nav-link {{ request()->routeIs('dashboard.siswa') ? 'active' : '' }}">
                <i class="fas fa-columns"></i> Dashboard
            </a>
            <a href="{{ route('ujian.history') }}" class="nav-link {{ request()->routeIs('ujian.history') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Riwayat Ujian
            </a>
            
            <div class="nav-title">Akun</div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link bg-transparent border-0 w-100 text-start text-danger">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        
        <div class="mb-4 bg-white rounded-4 shadow-sm p-3 px-4 d-flex justify-content-between align-items-center sticky-top" style="top: 20px; z-index: 900;">
            <div class="d-flex align-items-center">
                
                <button class="btn btn-light text-primary me-3 border-0 shadow-sm" id="sidebarToggle">
                    <i class="fas fa-bars fa-lg"></i>
                </button>

                <div>
                    @hasSection('header-content')
                        @yield('header-content')
                    @else
                        <h5 class="fw-bold text-dark mb-0">@yield('page-title')</h5>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center">
                <div class="text-end me-3 d-none d-md-block">
                    <h6 class="fw-bold mb-0 text-dark">{{ Auth::user()->name }}</h6>
                    <small class="text-muted" style="font-size: 0.8rem;">Peserta Ujian</small>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=0EA5E9&color=fff" class="rounded-circle shadow-sm" width="40">
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');

            // Fungsi Toggle
            function toggleSidebar() {
                sidebar.classList.toggle('toggled');
                content.classList.toggle('toggled');
                overlay.classList.toggle('active');
            }

            // Event Klik Tombol
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Mencegah event bubbling
                toggleSidebar();
            });

            // Event Klik Overlay (Tutup menu saat klik area gelap di HP)
            overlay.addEventListener('click', function() {
                toggleSidebar();
            });
        });
    </script>

    @yield('scripts')
</body>
</html>