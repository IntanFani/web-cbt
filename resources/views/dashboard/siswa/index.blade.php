@extends('layouts.app-siswa')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard Ujian')

@section('header-content')
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary me-3 d-none d-md-block">
            <i class="fas fa-user-graduate fa-lg"></i>
        </div>
        <div>
            <h4 class="fw-bold text-dark mb-1">Halo, {{ Auth::user()->name }}! 👋</h4>
            <p class="text-muted small mb-0">Selamat mengerjakan ujian, jujur itu hebat!</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid px-0">

        {{-- Notifikasi Error --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white overflow-hidden position-relative">
                    <div class="card-body p-4 position-relative" style="z-index: 2;">
                        <div class="d-flex align-items-center">
                            <div class="me-4">
                                <i class="fas fa-laptop-code fa-3x opacity-50"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Daftar Ujian Tersedia</h5>
                                <p class="mb-0 opacity-75">Silakan pilih ujian di bawah ini sesuai jadwal.</p>
                            </div>
                        </div>
                    </div>
                    <div class="position-absolute top-0 end-0 opacity-10" style="margin-right: -20px; margin-top: -20px;">
                        <i class="fas fa-shapes fa-10x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($exams as $exam)
                @php
                    $session = $exam->sessions->first();
                    $status = $session ? $session->status : 'belum_mulai';
                @endphp

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-top transition-all">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-center gap-2">
                                @if ($status == 'completed' || $status == 'finished')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i> Selesai
                                    </span>
                                @elseif($status == 'ongoing')
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">
                                        <i class="fas fa-spinner fa-spin me-1"></i> Berjalan
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                        <i class="fas fa-lock me-1"></i> Belum Mulai
                                    </span>
                                @endif
                            </div>
                            <span class="text-muted small fw-bold">
                                <i class="far fa-clock me-1"></i> {{ $exam->duration }}m
                            </span>
                        </div>

                        <div class="card-body px-4 pb-4 pt-2 d-flex flex-column">
                            <div class="mb-3">
                                <h5 class="card-title fw-bold text-dark mb-1 title-fixed" title="{{ $exam->title }}">
                                    {{ $exam->title }}
                                </h5>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-chalkboard-teacher me-1 text-primary"></i>
                                    Guru: {{ $exam->teacher->name ?? 'Admin' }}
                                </p>
                            </div>

                            <hr class="opacity-10 my-3 mt-auto">

                            <div class="d-grid">
                                @if ($status == 'belum_mulai')
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#modalToken{{ $exam->id }}">
                                        <i class="fas fa-play me-1"></i> Kerjakan
                                    </button>
                                @elseif($status == 'ongoing')
                                    <a href="{{ route('ujian.show', $exam->id) }}"
                                        class="btn btn-warning fw-bold text-dark rounded-pill py-2 shadow-sm btn-sm">
                                        <i class="fas fa-history me-2"></i> Lanjutkan Ujian
                                    </a>
                                @else
                                    <a href="{{ route('ujian.history') }}"
                                        class="btn btn-link btn-sm w-100 mt-2 text-decoration-none text-muted small">
                                        Lihat Nilai di Riwayat &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL TOKEN: Ditaruh di luar card agar tidak bentrok dengan CSS Transform --}}
                @if ($status == 'belum_mulai')
                <div class="modal fade" id="modalToken{{ $exam->id }}" tabindex="-1" aria-labelledby="label{{ $exam->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header border-0 pb-0 px-4 pt-4">
                                <h5 class="fw-bold mb-0">Konfirmasi Ujian</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('ujian.start', $exam->id) }}" method="POST">
                                @csrf
                                <div class="modal-body text-center p-4">
                                    <p class="text-muted mb-4">Silakan masukkan <strong>Token Ujian</strong> untuk memulai pengerjaan <strong>{{ $exam->title }}</strong>.</p>
                                    
                                    <div class="form-group mb-3">
                                        <input type="text" name="token"
                                            class="form-control form-control-lg text-center fw-bold border-2 border-primary shadow-none"
                                            placeholder="MASUKKAN TOKEN" 
                                            required 
                                            autocomplete="off"
                                            style="letter-spacing: 3px; text-transform: uppercase;">
                                    </div>
                                    
                                    <div class="d-flex justify-content-center gap-3 py-2">
                                        <div class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                            <i class="far fa-clock me-1 text-primary"></i> {{ $exam->duration }} Menit
                                        </div>
                                        <div class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                            <i class="fas fa-list-ol me-1 text-primary"></i> {{ $exam->questions->count() }} Soal
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Mulai Sekarang</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

            @empty
                <div class="col-12 text-center py-5">
                    <div class="card border-0 shadow-sm rounded-4 p-5">
                        <div class="mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="opacity-25">
                        </div>
                        <h5 class="fw-bold text-muted">Tidak Ada Ujian Aktif</h5>
                        <p class="text-muted small">Saat ini belum ada ujian yang tersedia untuk kelas kamu.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .hover-top {
            transition: all 0.3s ease;
        }

        .hover-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .title-fixed {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 3rem;
        }

        /* Perbaikan untuk Modal Flicker */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5) !important;
        }
        
        body.modal-open {
            padding-right: 0 !important;
            overflow: hidden;
        }
    </style>
@endsection