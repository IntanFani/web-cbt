@extends('layouts.app')

@section('title', 'Hasil Ujian - ' . $exam->title)
@section('nav-greeting', 'Laporan Nilai Siswa')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Judul Ujian: <span class="fw-bold text-primary">{{ $exam->title }}</span></p>
        </div>
        <a href="{{ route('ujian.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-2">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 p-3 rounded-3 me-3">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 opacity-75 small fw-bold">TOTAL PESERTA</h6>
                        <h3 class="fw-bold mb-0">{{ $sessions->count() }} <span class="fs-6 fw-normal">Siswa</span></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-2">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 p-3 rounded-3 me-3">
                        <i class="fas fa-trophy fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 opacity-75 small fw-bold">NILAI TERTINGGI</h6>
                        <h3 class="fw-bold mb-0">{{ $sessions->max('score') ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-info text-white p-2">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 p-3 rounded-3 me-3">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 opacity-75 small fw-bold">RATA-RATA NILAI</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($sessions->avg('score'), 1) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('ujian.results', $exam->id) }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">KELAS</label>
                    <select name="filter_kelas" class="form-select bg-light border-0 py-2">
                        <option value="">Semua Kelas</option>
                        @foreach ($allKelas as $k)
                            <option value="{{ $k->id }}" {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">ANGKATAN</label>
                    <select name="filter_angkatan" class="form-select bg-light border-0 py-2">
                        <option value="">Semua Angkatan</option>
                        @foreach ($allAngkatan as $thn)
                            <option value="{{ $thn }}" {{ request('filter_angkatan') == $thn ? 'selected' : '' }}>
                                Angkatan {{ $thn }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-dark rounded-3 px-4 fw-bold">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('ujian.export', $exam->id) }}?filter_kelas={{ request('filter_kelas') }}&filter_angkatan={{ request('filter_angkatan') }}" 
                           class="btn btn-success rounded-3 px-4 fw-bold" target="_blank">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </a>
                        @if (request('filter_kelas') || request('filter_angkatan'))
                            <button type="button" class="btn btn-danger rounded-3 px-3 fw-bold" onclick="confirmBulkReset()">
                                <i class="fas fa-undo me-1"></i> Reset Massal
                            </button>
                        @endif
                    </div>
                </div>
            </form>

            <form id="bulk-reset-form" action="{{ route('ujian.resetBulk', $exam->id) }}" method="POST" class="d-none">
                @csrf
                <input type="hidden" name="filter_kelas" value="{{ request('filter_kelas') }}">
                <input type="hidden" name="filter_angkatan" value="{{ request('filter_angkatan') }}">
            </form>
        </div>
    </div>

    @if (request('filter_kelas'))
        <div class="alert alert-soft-primary border-0 rounded-4 py-3 mb-4" style="background: #eef2ff; color: #4f46e5;">
            <i class="fas fa-award me-2"></i> Menampilkan Peringkat untuk <strong>Kelas Terpilih</strong>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-muted small fw-bold" width="120">RANKING</th>
                            <th class="py-3 border-0 text-muted small fw-bold">NAMA SISWA</th>
                            <th class="py-3 border-0 text-muted small fw-bold text-center">NILAI</th>
                            <th class="py-3 border-0 text-muted small fw-bold text-center">STATUS</th>
                            <th class="py-3 border-0 text-muted small fw-bold text-center">WAKTU SELESAI</th>
                            <th class="text-end pe-4 py-3 border-0 text-muted small fw-bold">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $index => $session)
                            <tr>
                                <td class="ps-4">
                                    @if($index == 0)
                                        <span class="badge bg-warning text-dark rounded-pill px-3">🥇 Juara 1</span>
                                    @elseif($index == 1)
                                        <span class="badge bg-secondary text-white rounded-pill px-3">🥈 Juara 2</span>
                                    @elseif($index == 2)
                                        <span class="badge bg-brown text-white rounded-pill px-3" style="background: #cd7f32;">🥉 Juara 3</span>
                                    @else
                                        <span class="ms-3 fw-bold text-muted">#{{ $index + 1 }}</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($session->user->name) }}&background=random&color=fff" 
                                             class="rounded-circle me-3" width="35">
                                        <span class="fw-bold text-dark">{{ $session->user->name }}</span>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <span class="fs-5 fw-bold {{ $session->score >= 70 ? 'text-success' : 'text-danger' }}">
                                        {{ $session->score }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if ($session->score >= 70)
                                        <span class="badge rounded-pill px-3" style="background: #d1fae5; color: #065f46;">Lulus</span>
                                    @else
                                        <span class="badge rounded-pill px-3" style="background: #fee2e2; color: #991b1b;">Remedial</span>
                                    @endif
                                </td>

                                <td class="text-center text-muted small">
                                    {{ \Carbon\Carbon::parse($session->end_time)->format('d/m/Y') }}<br>
                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</span>
                                </td>

                                <td class="text-end pe-4">
                                    <form action="{{ route('ujian.reset', $session->id) }}" method="POST" class="d-inline" id="reset-form-{{ $session->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-3"
                                                onclick="confirmReset({{ $session->id }}, '{{ $session->user->name }}')">
                                            <i class="fas fa-redo-alt me-1"></i> Reset
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/gray/creative-work.svg" style="width: 150px;" class="mb-3 opacity-50">
                                    <p class="text-muted">Belum ada siswa yang menyelesaikan ujian.</p>
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

@section('scripts')
<script>
    function confirmReset(id, nama) {
        Swal.fire({
            title: 'Reset Ujian?',
            text: "Siswa " + nama + " dapat mengerjakan ulang. Nilai saat ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Reset Nilai!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('reset-form-' + id).submit();
            }
        })
    }

    function confirmBulkReset() {
        Swal.fire({
            title: 'Reset Massal?',
            text: "Semua nilai siswa pada filter saat ini akan dihapus. Tindakan ini berisiko!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Reset Semua!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulk-reset-form').submit();
            }
        })
    }
</script>
@endsection