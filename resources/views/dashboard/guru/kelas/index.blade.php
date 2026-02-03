@extends('layouts.app')

@section('title', 'Kelola Data Kelas')
@section('nav-greeting', 'Daftar Kelas')
@section('nav-description', 'Kelola distribusi dan pengelompokan siswa berdasarkan kelas.')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-end align-items-center mb-4">
        <a href="{{ route('ujian.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Kelas Baru
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('kelas.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control rounded-3 border-0 bg-light py-3 px-3" 
                                   placeholder="Misal: XII RPL 1" required>
                            <div class="form-text mt-2 small text-muted">
                                <i class="fas fa-info-circle me-1"></i> Gunakan format penamaan yang konsisten.
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 rounded-3 shadow-sm">
                            <i class="fas fa-save me-2"></i>Simpan Data Kelas
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">Data Kelas Terdaftar</h6>
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill" style="background: #eef2ff;">
                        Total: {{ $kelas->count() }} Kelas
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 py-3 text-muted small fw-bold" width="80">No</th>
                                    <th class="border-0 py-3 text-muted small fw-bold">Nama Kelas</th>
                                    <th class="border-0 py-3 text-muted small fw-bold text-center">Jumlah Siswa</th>
                                    <th class="text-center pe-0 border-0 py-3 text-muted small fw-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelas as $index => $k)
                                    <tr>
                                        <td class="ps-4 fw-medium text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark fs-6">{{ $k->nama_kelas }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center bg-light px-2 py-1 rounded-pill">
                                                <i class="fas fa-users text-primary me-2" style="font-size: 0.8rem;"></i>
                                                <span class="fw-bold text-dark me-1">{{ $k->students->count() }}</span>
                                                <span class="text-muted small">Siswa</span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-5">
                                            <form action="{{ route('kelas.delete', $k->id) }}" method="POST" id="delete-form-{{ $k->id }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-3 px-3" 
                                                        onclick="confirmDelete({{ $k->id }}, '{{ $k->nama_kelas }}')">
                                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <img src="https://illustrations.popsy.co/gray/folder-is-empty.svg" style="width: 140px;" class="mb-3 opacity-50">
                                            <h6 class="text-muted fw-normal">Belum ada data kelas yang terdaftar.</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Kelas?',
            text: "Data kelas " + nama + " akan dihapus permanen. Siswa di dalamnya akan kehilangan asosiasi kelas.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-4',
                confirmButton: 'rounded-3',
                cancelButton: 'rounded-3'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection