@extends('layouts.app')

@section('title', 'Manajemen Ujian')
@section('nav-greeting', 'Manajemen Ujian')
@section('nav-description', 'Pantau dan kelola semua ujian aktif Anda di sini.')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('ujian.create') }}" class="btn btn-primary rounded-3 px-4 shadow-sm py-2">
            <i class="fas fa-plus me-2"></i>Buat Ujian Baru
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3">
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
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3">
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
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3">
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
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Daftar Ujian Saya</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-muted small fw-bold">Judul Ujian</th>
                            <th class="py-3 border-0 text-muted small fw-bold">Token</th>
                            <th class="py-3 border-0 text-muted small fw-bold">Durasi</th>
                            <th class="text-center pe-4 py-3 border-0 text-muted small fw-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $exam->title }}</div>
                                    <small class="text-muted">
                                        {{ $exam->questions->count() }} Pertanyaan 
                                        @if($exam->questions->count() == 0)
                                            <span class="text-danger fst-italic">(Belum ada soal)</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill fw-bold" style="background: #fef3c7; color: #92400e; padding: 8px 15px;">
                                        {{ $exam->token }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="far fa-clock me-2"></i>
                                        <span>{{ $exam->duration }}m</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('ujian.questions', $exam->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-3" title="Kelola Soal">
                                            <i class="fas fa-list me-1"></i> Soal
                                        </a>
                                        
                                        <a href="{{ route('ujian.results', $exam->id) }}" class="btn btn-sm btn-outline-success rounded-3 px-3" title="Lihat Hasil">
                                            <i class="fas fa-chart-bar me-1"></i> Hasil
                                        </a>

                                        <a href="{{ route('ujian.edit', $exam->id) }}" class="btn btn-sm btn-outline-warning rounded-3 px-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2" onclick="alertHapusUjian({{ $exam->id }})" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <form id="delete-exam-{{ $exam->id }}" action="{{ route('ujian.delete', $exam->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-folder-open fa-3x text-muted opacity-25 mb-3"></i>
                                        <h6 class="text-muted fw-bold">Belum ada ujian</h6>
                                        <p class="text-muted small mb-3">Silakan buat ujian baru untuk memulai.</p>
                                        <a href="{{ route('ujian.create') }}" class="btn btn-sm btn-primary rounded-pill px-4">
                                            <i class="fas fa-plus me-1"></i> Buat Ujian
                                        </a>
                                    </div>
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
    // PERBAIKAN PENTING: Gunakan 'window.' di depannya
    window.alertHapusUjian = function(id) {
        
        Swal.fire({
            title: 'Hapus Ujian?',
            text: "Semua data nilai siswa untuk ujian ini akan ikut terhapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Cari form berdasarkan ID dan submit
                var form = document.getElementById('delete-exam-' + id);
                if (form) {
                    form.submit();
                } else {
                    Swal.fire('Error', 'Form tidak ditemukan!', 'error');
                }
            }
        });

    };
</script>
@endsection