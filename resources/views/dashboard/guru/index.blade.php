@extends('layouts.app')

@section('title', 'Dashboard Guru')
@section('nav-greeting', 'Selamat Datang!')

@section('nav-description')
    {{-- Tanggal hanya dipanggil di sini untuk dashboard --}}
    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
@endsection

@section('content')

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Total Siswa</h6>
                        <h3 class="fw-bold mb-0">{{ $totalSiswa }}</h3>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Ujian Saya</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $totalUjian }}</h3>
                    </div>
                    <i class="fas fa-file-alt fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Rata-rata Nilai</h6>
                        <h3 class="fw-bold mb-0 text-warning">
                            {{ number_format($avgNilai, 1) }}
                        </h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Ujian Selesai</h6>
                        <h3 class="fw-bold mb-0 text-info">{{ $totalSelesai }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-history me-2 text-primary"></i>Penyelesaian Ujian Terkini
                </h6>
                <a href="{{ route('ujian.index') }}" class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-3 shadow-sm">
                    Lihat Semua
                </a>
            </div>
            
            <div class="card-body p-0 pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase border-0">Nama Siswa</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase border-0">Ujian</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase border-0">Waktu Selesai</th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase border-0 text-end">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $activity)
                                <tr>
                                    <td class="ps-4 border-bottom-0">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($activity->user->name) }}&background=random&color=fff" 
                                                     class="rounded-circle shadow-sm" width="35">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ $activity->user->name }}</h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    {{ $activity->user->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="border-bottom-0">
                                        <span class="text-dark fw-semibold" style="font-size: 0.9rem;">
                                            {{ $activity->exam->title }}
                                        </span>
                                    </td>

                                    <td class="border-bottom-0">
                                        <span class="text-muted small">
                                            <i class="far fa-clock me-1"></i> {{ $activity->updated_at->diffForHumans() }}
                                        </span>
                                    </td>

                                    <td class="pe-4 text-end border-bottom-0">
                                        @if($activity->score >= 70)
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">
                                                {{ number_format($activity->score, 0) }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-3 py-2 rounded-pill">
                                                {{ number_format($activity->score, 0) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 border-bottom-0">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="opacity-25 mb-3">
                                        <p class="text-muted small mb-0">Belum ada aktivitas ujian terbaru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">

            @php
                use App\Models\User;

                // 1. Ambil 5 Siswa Terbaik (Rata-rata Tertinggi)
                $topStudents = User::where('role', 'siswa')
                    ->whereHas('examSessions') // Hanya siswa yang pernah ujian
                    ->withAvg('examSessions', 'score')
                    ->orderByDesc('exam_sessions_avg_score')
                    ->take(5)
                    ->get();

                // 2. Ambil 5 Siswa Perlu Bantuan (Rata-rata Terendah / Di bawah 70)
                $lowStudents = User::where('role', 'siswa')
                    ->whereHas('examSessions')
                    ->withAvg('examSessions', 'score')
                    ->orderBy('exam_sessions_avg_score', 'asc') // Urutkan dari yang terkecil
                    ->take(5)
                    ->get();
            @endphp
            
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark">Monitoring Siswa</h6>
                        <a href="{{ route('siswa.index') }}" class="text-decoration-none small text-primary fw-bold">Lihat Semua</a>
                    </div>
                    
                    <ul class="nav nav-pills nav-fill gap-2 p-1 bg-light rounded-3" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-3 py-2 small fw-bold" id="top-tab" data-bs-toggle="tab" data-bs-target="#top-content" type="button">
                                <i class="fas fa-trophy me-1 text-warning"></i> Prestasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 small fw-bold" id="low-tab" data-bs-toggle="tab" data-bs-target="#low-content" type="button">
                                <i class="fas fa-exclamation-circle me-1 text-danger"></i> Remedial
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0 pt-2">
                    <div class="tab-content" id="studentTabsContent">
                        
                        <div class="tab-pane fade show active" id="top-content" role="tabpanel">
                            <div class="list-group list-group-flush">
                                @forelse($topStudents as $index => $student)
                                    <div class="list-group-item border-0 px-4 py-3 d-flex align-items-center hover-bg">
                                        <div class="me-3 position-relative">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=random&color=fff" 
                                                 class="rounded-circle shadow-sm" width="35">
                                            @if($index == 0)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-white" style="font-size: 0.6rem;">
                                                    <i class="fas fa-crown"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ $student->name }}</h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ $student->kelas->nama_kelas ?? '-' }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">
                                                {{ number_format($student->exam_sessions_avg_score, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="opacity-25 mb-2">
                                        <p class="text-muted small mb-0">Belum ada data nilai.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="tab-pane fade" id="low-content" role="tabpanel">
                            <div class="list-group list-group-flush">
                                @forelse($lowStudents as $student)
                                    <div class="list-group-item border-0 px-4 py-3 d-flex align-items-center hover-bg">
                                        <div class="me-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=fee2e2&color=ef4444" 
                                                 class="rounded-circle shadow-sm" width="35">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ $student->name }}</h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ $student->kelas->nama_kelas ?? '-' }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold">
                                                {{ number_format($student->exam_sessions_avg_score, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fas fa-check-circle fa-2x text-success opacity-50 mb-2"></i>
                                        <p class="text-muted small mb-0">Semua siswa aman!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

           <!-- <div class="card border-0 shadow-sm rounded-4 bg-primary text-white overflow-hidden position-relative">
                <div class="card-body p-4 position-relative" style="z-index: 2;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3">
                            <i class="fas fa-plus fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Buat Ujian Baru</h6>
                            <p class="text-white-50 small mb-0">Jadwalkan tes sekarang.</p>
                        </div>
                    </div>
                    <a href="{{ route('ujian.create') }}" class="btn btn-light text-primary fw-bold w-100 shadow-sm stretched-link">
                        Mulai Buat
                    </a>
                </div>
                <div class="position-absolute top-0 end-0 opacity-10" style="margin-right: -10px; margin-top: -10px;">
                    <i class="fas fa-shapes fa-5x"></i>
                </div>
            </div>-->

        </div>

        <style>
            /* Sedikit style tambahan untuk hover effect list */
            .hover-bg:hover { background-color: #f8f9fa; cursor: default; }
            .nav-pills .nav-link.active { background-color: white; color: #000; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
            .nav-pills .nav-link { color: #6c757d; }
        </style>
    </div>
@endsection