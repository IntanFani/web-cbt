@extends('layouts.app')

@section('title', 'Kelola Soal - ' . $exam->title)
@section('nav-greeting', 'Pengelolaan Soal')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <small class="text-muted mb-0">Ujian: <span class="text-primary fw-semibold">{{ $exam->title }}</span></small>
        </div>
        <a href="{{ route('ujian.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 py-3 mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fa-lg"></i>
                <div>{{ session('success') }}</div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 10;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Soal Baru
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('questions.store', $exam->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pertanyaan</label>
                            <textarea name="question_text" class="form-control bg-light border-0 rounded-3" 
                                      rows="4" placeholder="Tuliskan pertanyaan ujian di sini..." required></textarea>
                        </div>

                        <label class="form-label small fw-bold text-muted text-uppercase mb-3">Pilihan Jawaban & Kunci</label>
                        
                        @foreach(['A', 'B', 'C', 'D'] as $key => $label)
                        <div class="input-group mb-3 custom-input-group">
                            <div class="input-group-text bg-white border-end-0 rounded-start-3">
                                <input class="form-check-input mt-0 cursor-pointer" type="radio" 
                                       name="correct_answer" value="{{ $key }}" {{ $key == 0 ? 'required' : '' }}
                                       title="Set sebagai kunci jawaban">
                                <span class="ms-2 fw-bold text-primary">{{ $label }}</span>
                            </div>
                            <input type="text" name="options[]" class="form-control bg-white border-start-0 py-2 rounded-end-3" 
                                   placeholder="Tulis jawaban {{ $label }}" required>
                        </div>
                        @endforeach

                        <div class="p-3 rounded-3 mb-4" style="background: #f0f9ff; border: 1px dashed #bae6fd;">
                            <small class="text-primary">
                                <i class="fas fa-lightbulb me-2"></i>Pilih salah satu <strong>Radio Button</strong> di samping huruf (A-D) untuk menetapkan kunci jawaban.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-3 shadow-sm">
                            <i class="fas fa-save me-2"></i>Simpan Soal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark">Daftar Soal</h5>
                
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-excel me-2"></i>Import CSV
                    </button>
                    
                    <span class="badge bg-white text-primary border px-3 py-2 rounded-pill shadow-sm d-flex align-items-center">
                        {{ $exam->questions->count() }} Soal
                    </span>
                </div>
            </div>
            
            @forelse($exam->questions as $index => $q)
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden card-question">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary rounded-pill px-3 py-2">Soal No. {{ $index + 1 }}</span>
                            <form id="delete-form-{{ $q->id }}" action="{{ route('questions.delete', $q->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light text-danger rounded-3 p-2 px-3" 
                                        onclick="alertHapus({{ $q->id }})" title="Hapus Soal">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                        
                        <p class="text-dark fw-medium fs-6 mb-4">{{ $q->question_text }}</p>
                        
                        <div class="row g-2">
                            @foreach($q->options as $idx => $opt)
                                <div class="col-12">
                                    <div class="p-3 rounded-3 border d-flex align-items-center {{ $opt->is_correct ? 'border-success bg-soft-success shadow-sm' : 'border-light bg-light opacity-75' }}"
                                         style="{{ $opt->is_correct ? 'background: #f0fdf4;' : '' }}">
                                            @if($opt->is_correct)
                                                <div class="bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px;">
                                                    <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                                                </div>
                                            @else
                                                <div class="bg-white border rounded-circle me-3 d-flex align-items-center justify-content-center text-muted small fw-bold" style="width: 24px; height: 24px;">
                                                    {{ chr(65 + $idx) }}
                                                </div>
                                            @endif
                                            <span class="{{ $opt->is_correct ? 'text-success fw-bold' : 'text-muted' }}">{{ $opt->option_text }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
                    <div class="card-body">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" style="width: 120px;" class="mb-4 opacity-50">
                        <h6 class="text-muted fw-normal">Ujian ini belum memiliki pertanyaan.</h6>
                        <p class="small text-muted mb-4">Anda bisa input manual atau import dari Excel.</p>
                        <button class="btn btn-outline-success btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-excel me-1"></i> Coba Import CSV
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Import Soal dari CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 bg-soft-primary small rounded-3">
                    <strong>Format CSV:</strong><br>
                    Kolom 1: Pertanyaan<br>
                    Kolom 2-5: Opsi A, B, C, D<br>
                    Kolom 6: Kunci Jawaban (Angka 1-4)<br>
                </div>
                <form action="{{ route('ujian.importQuestions', $exam->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Upload File</label>
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success fw-bold rounded-3">
                            <i class="fas fa-upload me-2"></i>Upload & Proses
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function alertHapus(id) {
        Swal.fire({
            title: 'Hapus Soal?',
            text: "Butir soal ini akan dihapus secara permanen.",
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