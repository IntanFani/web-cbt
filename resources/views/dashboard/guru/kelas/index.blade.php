@extends('layouts.app')

@section('title', 'Kelola Data Kelas')
@section('nav-greeting', 'Daftar Kelas')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75 small fw-bold text-uppercase">Total Kelas</h6>
                        <h2 class="fw-bold mb-0">{{ $kelas->count() }}</h2>
                    </div>
                    <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                        <i class="fas fa-school fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 bg-info text-white p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75 small fw-bold text-uppercase">Total Siswa</h6>
                        <h2 class="fw-bold mb-0">{{ \App\Models\User::where('role', 'siswa')->count() }}</h2>
                    </div>
                    <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-white p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75 small fw-bold text-uppercase">Rata-rata Populasi</h6>
                        <h2 class="fw-bold mb-0">
                            {{ $kelas->count() > 0 ? round(\App\Models\User::where('role', 'siswa')->count() / $kelas->count()) : 0 }}
                        </h2>
                    </div>
                    <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Data Kelas Terdaftar</h6>
                        <small class="text-muted small">Kelola semua daftar kelas aktif</small>
                    </div>
                    <button type="button" class="btn btn-primary rounded-3 fw-bold px-4 py-2 shadow-sm border-0 transition-3d" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Kelas Baru
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 py-3 text-muted small fw-bold" width="80">NO</th>
                                    <th class="border-0 py-3 text-muted small fw-bold">NAMA KELAS</th>
                                    <th class="border-0 py-3 text-muted small fw-bold text-center">POPULASI</th>
                                    <th class="text-center border-0 py-3 text-muted small fw-bold" width="220">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelas as $index => $k)
                                    <tr>
                                        <td class="ps-4 fw-medium text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm text-primary rounded-3 me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px; background: #eef2ff;">
                                                    {{ substr($k->nama_kelas, 0, 1) }}
                                                </div>
                                                <div class="fw-bold text-dark fs-6">{{ $k->nama_kelas }}</div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('siswa.index', ['filter_kelas' => $k->id]) }}" class="text-decoration-none">
                                                <div class="d-inline-flex align-items-center bg-light px-3 py-1 rounded-pill hover-info transition-02">
                                                    <i class="fas fa-users text-info me-2" style="font-size: 0.85rem;"></i>
                                                    <span class="fw-bold text-dark me-1">{{ $k->students->count() }}</span>
                                                    <span class="text-muted small">Siswa</span>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-icon-sm btn-edit" onclick="editKelas({{ $k->id }}, '{{ $k->nama_kelas }}')" title="Edit Kelas">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ route('siswa.index', ['filter_kelas' => $k->id]) }}" class="btn btn-icon-sm btn-view" title="Lihat Siswa">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <button type="button" class="btn btn-icon-sm btn-delete" onclick="confirmDelete({{ $k->id }}, '{{ $k->nama_kelas }}')" title="Hapus Kelas">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                <form action="{{ route('kelas.delete', $k->id) }}" method="POST" id="delete-form-{{ $k->id }}" class="d-none">
                                                    @csrf @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <img src="https://illustrations.popsy.co/gray/folder-is-empty.svg" style="width: 150px;" class="mb-3 opacity-50">
                                            <h6 class="text-muted fw-normal">Belum ada data kelas terdaftar.</h6>
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

<div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control rounded-3 border-0 bg-light py-3 px-3 shadow-none focus-primary" 
                               placeholder="Misal: XII RPL 1" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 gap-2">
                    <button type="button" class="btn btn-light rounded-3 fw-semibold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4 shadow-sm border-0">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditKelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Edit Nama Kelas</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditKelas" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nama Kelas Baru</label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" class="form-control rounded-3 border-0 bg-light py-3 px-3 shadow-none focus-primary" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 gap-2">
                    <button type="button" class="btn btn-light rounded-3 fw-semibold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-3 fw-bold px-4 text-white shadow-sm border-0">Update Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-icon-sm {
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px; border: none; transition: all 0.2s ease;
    }
    .btn-edit { background: #fff9db; color: #f08c00; }
    .btn-edit:hover { background: #f08c00; color: white; transform: translateY(-2px); }
    .btn-view { background: #e3fafc; color: #0c8599; }
    .btn-view:hover { background: #0c8599; color: white !important; transform: translateY(-2px); }
    .btn-delete { background: #fff5f5; color: #fa5252; }
    .btn-delete:hover { background: #fa5252; color: white; transform: translateY(-2px); }

    .focus-primary:focus {
        background-color: #fff !important;
        border: 1px solid #4f46e5 !important;
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.1) !important;
    }
    .transition-3d:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
    }
    .transition-02 { transition: 0.2s; }
    .hover-info:hover { background: #e3fafc !important; transform: scale(1.05); }
</style>
@endsection

@section('scripts')
<script>
    function editKelas(id, nama) {
        const modal = new bootstrap.Modal(document.getElementById('modalEditKelas'));
        const form = document.getElementById('formEditKelas');
        const input = document.getElementById('edit_nama_kelas');
        
        form.action = `/kelas/${id}`; // Sesuaikan route update Anda
        input.value = nama;
        modal.show();
    }

    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Kelas?',
            text: `Hapus kelas "${nama}"? Semua data siswa akan terlepas dari kelas ini.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fa5252',
            cancelButtonColor: '#adb5bd',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection