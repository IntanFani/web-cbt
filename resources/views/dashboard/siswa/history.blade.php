@extends('layouts.app-siswa')

@section('title', 'Riwayat Ujian')
@section('page-title', 'Riwayat Hasil Ujian')

@section('content')
<div class="container-fluid px-0">
    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">Nama Ujian</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Guru</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Tanggal</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase text-center">Nilai</th>
                            <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $history->exam->title }}</div>
                                    <small class="text-muted">{{ $history->exam->questions->count() ?? 0 }} Soal</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle p-2 me-2 text-primary">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <span>{{ $history->exam->teacher->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <i class="far fa-calendar-alt me-1 text-muted"></i>
                                    {{ $history->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2 fs-6 {{ $history->score >= 70 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $history->score }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <span class="badge bg-soft-success text-success bg-opacity-10 border border-success px-3">
                                        <i class="fas fa-check-circle me-1"></i> Selesai
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-25 mb-3">
                                    <p class="text-muted mb-0">Belum ada riwayat ujian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection