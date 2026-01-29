<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Ujian - {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Hasil Ujian Siswa</h4>
                <p class="text-muted mb-0">Ujian: <span class="fw-bold text-dark">{{ $exam->title }}</span></p>
            </div>
            <a href="{{ route('dashboard.guru') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <h6 class="opacity-75">Total Peserta</h6>
                        <h3 class="fw-bold">{{ $sessions->count() }} Siswa</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <h6 class="opacity-75">Nilai Tertinggi</h6>
                        <h3 class="fw-bold">{{ $sessions->max('score') ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <h6 class="opacity-75">Rata-rata Nilai</h6>
                        <h3 class="fw-bold">{{ number_format($sessions->avg('score'), 1) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body bg-light">
                <form action="{{ route('ujian.results', $exam->id) }}" method="GET" class="row g-2 align-items-end">

                    <div class="col-md-3">
                        <label class="fw-bold small mb-1">Filter Kelas</label>
                        <select name="filter_kelas" class="form-select form-select-sm">
                            <option value="">-- Semua Kelas --</option>
                            @foreach ($allKelas as $k)
                                <option value="{{ $k->id }}"
                                    {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="fw-bold small mb-1">Filter Angkatan</label>
                        <select name="filter_angkatan" class="form-select form-select-sm">
                            <option value="">-- Semua Angkatan --</option>
                            @foreach ($allAngkatan as $thn)
                                <option value="{{ $thn }}"
                                    {{ request('filter_angkatan') == $thn ? 'selected' : '' }}>
                                    Angkatan {{ $thn }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm fw-bold flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>

                        <a href="{{ route('ujian.export', $exam->id) }}?filter_kelas={{ request('filter_kelas') }}&filter_angkatan={{ request('filter_angkatan') }}" 
                        class="btn btn-success btn-sm fw-bold flex-grow-1" target="_blank">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </a>
                    </div>

                    @if (request('filter_kelas') || request('filter_angkatan'))
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="confirmBulkReset()">
                                <i class="fas fa-bomb me-1"></i> Reset Siswa Terfilter
                            </button>
                        </div>
                    @endif
                </form>

                <form id="bulk-reset-form" action="{{ route('ujian.resetBulk', $exam->id) }}" method="POST"
                    class="d-none">
                    @csrf
                    <input type="hidden" name="filter_kelas" value="{{ request('filter_kelas') }}">
                    <input type="hidden" name="filter_angkatan" value="{{ request('filter_angkatan') }}">
                </form>
            </div>
        </div>

        @if (request('filter_kelas'))
            <div class="alert alert-info py-2 mb-3">
                <i class="fas fa-trophy me-2"></i> Menampilkan Peringkat untuk <strong>Kelas Terpilih</strong>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Peringkat</th>
                            <th>Nama Siswa</th>
                            <th>Waktu Selesai</th>
                            <th>Nilai</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $index => $session)
                            <tr>
                                <td class="ps-4 fw-bold">#{{ $index + 1 }}</td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-secondary"></i>
                                        </div>
                                        <span class="fw-bold">{{ $session->user->name }}</span>
                                    </div>
                                </td>

                                <td class="text-muted small">
                                    {{ \Carbon\Carbon::parse($session->end_time)->format('d M Y, H:i') }}
                                </td>

                                <td class="fw-bold fs-5">{{ $session->score }}</td>

                                <td>
                                    @if ($session->score >= 70)
                                        <span class="badge bg-success">Lulus</span>
                                    @else
                                        <span class="badge bg-danger">Remedial</span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    <form action="{{ route('ujian.reset', $session->id) }}" method="POST"
                                        class="d-inline" id="reset-form-{{ $session->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmReset({{ $session->id }}, '{{ $session->user->name }}')">
                                            <i class="fas fa-redo-alt me-1"></i> Reset / Ulang
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-users-slash fa-3x mb-3 opacity-25"></i>
                                    <p>Belum ada siswa yang menyelesaikan ujian ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmReset(id, nama) {
            Swal.fire({
                title: 'Reset Ujian Siswa?',
                text: "Siswa atas nama " + nama +
                    " akan bisa mengerjakan ujian ini lagi dari awal. Nilai lama akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Merah
                cancelButtonColor: '#3085d6', // Biru
                confirmButtonText: 'Ya, Reset Ujian!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reset-form-' + id).submit();
                }
            })
        }

        function confirmBulkReset() {
            Swal.fire({
                title: 'AWAS! Reset Massal?',
                text: "Anda akan me-reset ujian milik SEMUA SISWA yang sedang ditampilkan saat ini. Tindakan ini tidak bisa dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Reset Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulk-reset-form').submit();
                }
            })
        }
    </script>

</body>

</html>
