<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - CBT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            transition: all 0.3s ease;
        }

        .card-icon {
            font-size: 3rem;
            color: #dee2e6;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-laptop-code me-2"></i>CBT Sekolah
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <span class="text-white small">Halo, <strong>{{ Auth::user()->name }}</strong></span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">

        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold text-dark border-start border-5 border-primary ps-3">Daftar Ujian</h3>
                <p class="text-muted ms-3">Silakan pilih ujian yang tersedia di bawah ini.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-trophy me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            @forelse($exams as $exam)
                @php
                    // Cek apakah siswa sudah pernah mengerjakan ujian ini?
                    // Kita ambil data session pertama (karena sudah difilter di controller)
                    $session = $exam->sessions->first();

                    // Tentukan status default 'belum_mulai'
                    $status = $session ? $session->status : 'belum_mulai';
                @endphp

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body position-relative">
                            <span class="position-absolute top-0 end-0 badge bg-light text-dark m-3 border">
                                <i class="fas fa-clock me-1"></i> {{ $exam->duration }} Menit
                            </span>

                            <div class="mb-3 mt-2">
                                <i class="fas fa-file-alt card-icon text-primary opacity-25"></i>
                            </div>

                            <h5 class="card-title fw-bold text-dark">{{ $exam->title }}</h5>

                            <p class="card-text text-muted small mb-4">
                                <i class="fas fa-user-tie me-1"></i> Guru: {{ $exam->teacher->name ?? 'Admin' }}
                            </p>

                            <hr class="text-muted opacity-25">

                            <div class="d-grid">
                                @if ($status == 'belum_mulai')
                                    <a href="{{ route('ujian.start', $exam->id) }}" class="btn btn-primary fw-bold">
                                        Kerjakan Sekarang <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                @elseif($status == 'ongoing')
                                    <a href="{{ route('ujian.show', $exam->id) }}"
                                        class="btn btn-warning fw-bold text-dark">
                                        <i class="fas fa-history me-2"></i> Lanjutkan Ujian
                                    </a>
                                @elseif($status == 'completed' || $status == 'finished')
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-check-circle me-2"></i> Sudah Selesai
                                    </button>
                                    <div class="text-center mt-2">
                                        <small class="text-success fw-bold">Nilai: {{ $session->score ?? 0 }}</small>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <strong>Belum ada ujian!</strong>
                            <p class="mb-0">Saat ini belum ada jadwal ujian yang aktif untukmu. Silakan cek lagi
                                nanti.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
