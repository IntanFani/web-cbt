@extends('layouts.app')

@section('title', 'Kelola Data Siswa')
@section('nav-greeting', 'Database Siswa')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-muted">Total siswa terdaftar: <span
                        class="badge bg-soft-primary text-primary">{{ $siswa->total() }} Siswa</span></p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('siswa.create') }}" class="btn btn-primary rounded-3 px-3 shadow-sm">
                    <i class="fas fa-user-plus me-2"></i>Tambah Siswa
                </a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalImport">
                    <i class="fas fa-file-excel me-1"></i> Import Excel (CSV)
                </button>

                <div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Import Data Siswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        Format CSV: <b>Nama, Email, Password, Nama Kelas, Angkatan</b><br>
                                        Contoh Nama Kelas: <b>VIII A</b> (Harus sama persis dengan di database)
                                    </div>
                                    <div class="mb-3">
                                        <label>Pilih File CSV</label>
                                        <input type="file" name="file" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Mulai Import</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('siswa.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">CARI SISWA</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0"
                                placeholder="Nama atau email..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">FILTER KELAS</label>
                        <select name="filter_kelas" class="form-select bg-light border-0">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}"
                                    {{ request('filter_kelas') == $k->id || request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">ANGKATAN</label>
                        <select name="filter_angkatan" class="form-select bg-light border-0">
                            <option value="">Semua Angkatan</option>
                            @foreach ($angkatan as $thn)
                                <option value="{{ $thn }}"
                                    {{ request('filter_angkatan') == $thn ? 'selected' : '' }}>
                                    Angkatan {{ $thn }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100 fw-bold rounded-3 py-2">
                            Filter Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0 text-muted small fw-bold" width="60">NO</th>
                                <th class="py-3 border-0 text-muted small fw-bold">SISWA</th>
                                <th class="py-3 border-0 text-muted small fw-bold">KELAS / ANGKATAN</th>
                                <th class="py-3 border-0 text-muted small fw-bold">TANGGAL BERGABUNG</th>
                                <th class="text-end pe-4 py-3 border-0 text-muted small fw-bold">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $index => $s)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $siswa->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($s->name) }}&background=random&color=fff"
                                                class="rounded-3 me-3" width="40">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $s->name }}</div>
                                                <div class="small text-muted">{{ $s->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="mb-1">
                                            <span class="badge bg-soft-info text-info px-3 py-2 rounded-pill"
                                                style="background: #e0f2fe;">
                                                {{ $s->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                            </span>
                                        </div>
                                        <small class="text-muted fw-medium">Angkatan {{ $s->angkatan ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i>
                                            {{ $s->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('siswa.edit', $s->id) }}"
                                                class="btn btn-sm btn-outline-warning rounded-3 px-2" title="Edit Siswa">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2"
                                                onclick="confirmDelete({{ $s->id }}, '{{ $s->name }}')"
                                                title="Hapus Siswa">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>

                                        <form action="{{ route('siswa.delete', $s->id) }}" method="POST" class="d-none"
                                            id="delete-form-{{ $s->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/gray/data-report.svg"
                                            style="width: 150px;" class="mb-3 opacity-50">
                                        <p class="text-muted">Tidak ada data siswa yang ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 px-2">
            <div class="text-muted small">
                Menampilkan {{ $siswa->firstItem() }} sampai {{ $siswa->lastItem() }} dari {{ $siswa->total() }} siswa
            </div>
            <div>
                {{ $siswa->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(id, nama) {
            Swal.fire({
                title: 'Hapus Akun Siswa?',
                text: "Data " + nama + " akan dihapus secara permanen dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
