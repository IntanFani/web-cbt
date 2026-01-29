<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi CBT Sekolah')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        /* Sidebar Styling */
        .sidebar {
            min-height: 100vh;
            background: #343a40; /* Warna Gelap */
            color: #fff;
            position: fixed;
            width: 250px;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 15px 20px;
            border-left: 4px solid transparent;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: #0d6efd; /* Warna Biru Aktif */
            border-left-color: #fff;
        }
        .sidebar-brand {
            padding: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        
        /* Content Wrapper */
        .main-content {
            margin-left: 250px; /* Sebesar lebar sidebar */
            padding: 20px;
            transition: all 0.3s;
        }

        /* Navbar Styling */
        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
            padding: 15px 30px;
            margin-bottom: 30px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap me-2"></i>E-Learning
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.guru') ? 'active' : '' }}" href="{{ route('dashboard.guru') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item mt-3 mb-1 ms-3 text-uppercase small text-muted">Master Data</li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('kelas.*') ? 'active' : '' }}" href="{{ route('kelas.index') }}">
                    <i class="fas fa-school me-2"></i> Data Kelas
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}" href="{{ route('siswa.index') }}">
                    <i class="fas fa-users me-2"></i> Data Siswa
                </a>
            </li>

            <li class="nav-item mt-3 mb-1 ms-3 text-uppercase small text-muted">Akademik</li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ujian.*') ? 'active' : '' }}" href="{{ route('dashboard.guru') }}"> 
                    <i class="fas fa-file-alt me-2"></i> Manajemen Ujian
                </a>
            </li>

            <li class="nav-item mt-5">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link bg-transparent border-0 text-danger w-100 text-start">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <div class="main-content">
        
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold text-secondary">@yield('page-title', 'Dashboard')</h5>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end">
                    <small class="d-block text-muted">Login sebagai Guru</small>
                    <span class="fw-bold">{{ Auth::user()->name }}</span>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=0D8ABC&color=fff" class="rounded-circle" width="40">
            </div>
        </div>

        <div class="content-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')

</body>
</html>