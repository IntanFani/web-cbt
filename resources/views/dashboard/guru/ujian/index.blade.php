@extends('layouts.app')

{{-- @var \Illuminate\Support\Collection|\App\Models\Kelas[] $classes --}}

@section('title', 'Manajemen Ujian')
@section('nav-greeting', 'Manajemen Ujian')
@section('nav-description', 'Pantau dan kelola semua ujian aktif Anda di sini.')

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark">Ringkasan Ujian</h5>
            <button type="button" class="btn btn-primary rounded-3 px-4 shadow-sm py-2 fw-bold transition-3d border-0"
                data-bs-toggle="modal" data-bs-target="#modalBuatUjian">
                <i class="fas fa-plus me-2"></i>Buat Ujian Baru
            </button>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-3 rounded-4 me-3" style="background: #eef2ff;">
                            <i class="fas fa-file-alt text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small fw-bold text-uppercase">Total Ujian</h6>
                            <h4 class="fw-bold mb-0">{{ $exams->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-success p-3 rounded-4 me-3" style="background: #ecfdf5;">
                            <i class="fas fa-check-double text-success fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small fw-bold text-uppercase">Siap Dikerjakan</h6>
                            <h4 class="fw-bold mb-0">
                                {{ $exams->filter(fn($e) => $e->questions->count() > 0)->count() }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-warning p-3 rounded-4 me-3" style="background: #fffbeb;">
                            <i class="fas fa-user-graduate text-warning fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small fw-bold text-uppercase">Akses Cepat</h6>
                            <div class="d-flex gap-2">
                                <a href="{{ route('kelas.index') }}" class="text-decoration-none small fw-bold">Kelas</a>
                                <span class="text-muted">•</span>
                                <a href="{{ route('siswa.index') }}" class="text-decoration-none small fw-bold">Siswa</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-4 px-4">
                <h6 class="fw-bold mb-0">Daftar Ujian Saya</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0 text-muted small fw-bold">JUDUL UJIAN</th>
                                <th class="py-3 border-0 text-muted small fw-bold text-center">TOKEN</th>
                                <th class="py-3 border-0 text-muted small fw-bold">DURASI</th>
                                <th class="text-end pe-4 py-3 border-0 text-muted small fw-bold">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark fs-6">{{ $exam->title }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1 text-primary"></i>
                                            {{ $exam->kelas->name ?? 'Semua Kelas' }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-layer-group me-1"></i> {{ $exam->questions->count() }}
                                            Pertanyaan
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill fw-bold font-monospace"
                                            style="background: #fef3c7; color: #92400e; padding: 10px 18px; font-size: 0.9rem; letter-spacing: 1px;">
                                            {{ $exam->token }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center text-muted fw-medium">
                                            <i class="far fa-clock me-2 text-primary"></i>
                                            <span>{{ $exam->duration }} Menit</span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('ujian.questions', $exam->id) }}"
                                                class="btn btn-icon-custom btn-soft-primary" title="Kelola Soal">
                                                <i class="fas fa-list"></i>
                                            </a>
                                            <a href="{{ route('ujian.results', $exam->id) }}"
                                                class="btn btn-icon-custom btn-soft-success" title="Lihat Hasil">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <a href="{{ route('ujian.edit', $exam->id) }}"
                                                class="btn btn-icon-custom btn-soft-warning" title="Edit Jadwal">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-icon-custom btn-soft-danger"
                                                onclick="alertHapusUjian({{ $exam->id }})" title="Hapus Ujian">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>

                                        <form id="delete-exam-{{ $exam->id }}"
                                            action="{{ route('ujian.delete', $exam->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/gray/data-analysis.svg"
                                            style="width: 160px;" class="mb-3 opacity-50">
                                        <h6 class="text-muted fw-normal">Belum ada ujian yang dibuat.</h6>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBuatUjian" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-3 rounded-4 me-3" style="background: #eef2ff;">
                            <i class="fas fa-calendar-plus fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold">Buat Jadwal Ujian Baru</h5>
                            <p class="text-muted small mb-0">Lengkapi formulir di bawah untuk memulai sesi ujian.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form action="{{ route('ujian.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 p-md-5">
                        <div class="row">
                            <div class="col-md-8 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Judul Ujian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="fas fa-edit text-muted"></i></span>
                                    <input type="text" name="title"
                                        class="form-control bg-light border-0 py-3 px-3 shadow-none focus-primary"
                                        placeholder="Misal: Ujian Akhir Semester Ganjil" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Durasi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="far fa-clock text-muted"></i></span>
                                    <input type="number" name="duration"
                                        class="form-control bg-light border-0 py-3 shadow-none focus-primary"
                                        placeholder="90" required>
                                    <span class="input-group-text bg-light border-0 fw-bold text-muted">Menit</span>
                                </div>
                            </div>
                            <select name="kelas_id" class="form-control text-dark fw-bold" required>
                                <option value="" >-- Pilih Kelas --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->nama_kelas }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-3">Pengaturan
                                Keamanan</label>
                            <div class="p-4 rounded-4 bg-light border-start border-4 border-primary shadow-sm">
                                <div class="form-check form-switch d-flex align-items-center ps-0">
                                    <div class="flex-grow-1">
                                        <label class="form-check-label fw-bold text-dark d-block mb-1" for="random">
                                            Acak Urutan Soal
                                        </label>
                                        <p class="text-muted mb-0 small">Aktifkan untuk memberikan urutan soal berbeda bagi
                                            setiap siswa.</p>
                                    </div>
                                    <input class="form-check-input ms-3 custom-switch" type="checkbox" role="switch"
                                        id="random" name="random_question" value="1" checked>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 rounded-4 d-flex align-items-center" style="background: #eff6ff;">
                            <i class="fas fa-info-circle text-primary me-3"></i>
                            <p class="mb-0 small text-primary fw-medium">
                                Setelah disimpan, Anda akan diarahkan untuk mengisi butir soal.
                            </p>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pb-4 px-4 gap-2">
                        <button type="button" class="btn btn-light rounded-3 fw-semibold px-4 py-2"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-primary rounded-3 fw-bold px-4 py-2 shadow-sm border-0 transition-3d">
                            <i class="fas fa-save me-2"></i>Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Custom Styling for Action Buttons */
        .btn-icon-custom {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: none;
            transition: all 0.2s;
        }

        .btn-soft-primary {
            background: #eef2ff;
            color: #4f46e5;
        }

        .btn-soft-primary:hover {
            background: #4f46e5;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-soft-success {
            background: #ecfdf5;
            color: #10b981;
        }

        .btn-soft-success:hover {
            background: #10b981;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-soft-warning {
            background: #fffbeb;
            color: #f59e0b;
        }

        .btn-soft-warning:hover {
            background: #f59e0b;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-soft-danger {
            background: #fff5f5;
            color: #ef4444;
        }

        .btn-soft-danger:hover {
            background: #ef4444;
            color: #fff;
            transform: translateY(-2px);
        }

        .focus-primary:focus {
            background-color: #fff !important;
            border: 1px solid #4f46e5 !important;
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.1) !important;
        }

        .custom-switch {
            width: 3.2rem !important;
            height: 1.6rem !important;
            cursor: pointer;
        }

        .transition-3d:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection

@section('scripts')
    <script>
        window.alertHapusUjian = function(id) {
            Swal.fire({
                title: 'Hapus Ujian?',
                text: "Semua data nilai siswa untuk ujian ini akan ikut terhapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-exam-' + id).submit();
                }
            });
        };
    </script>
@endsection
