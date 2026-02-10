@extends('layouts.app')

@section('title', 'Kelola Soal - ' . $exam->title)
@section('nav-greeting', 'Pengelolaan Soal')
@section('nav-description', 'Ujian: ' . $exam->title)

@section('back-button')
    <a href="{{ route('ujian.index') }}" class="nav-back-btn">
        <i class="fas fa-arrow-left"></i>
    </a>
@endsection

@section('content')
    <div class="container-fluid py-3">

        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">Daftar Soal</h6>
                </div>

                <div class="d-flex gap-2 me-2">
                    <button class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold shadow-sm border-0 transition-3d"
                        data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Soal
                    </button>
                    <button class="btn btn-success btn-sm rounded-3 px-3 py-2 fw-bold shadow-sm border-0 transition-3d"
                        data-bs-toggle="modal" data-bs-target="#importModal" title="Import via CSV">
                        <i class="fas fa-file-excel me-1"></i> Import
                    </button>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 py-2 mb-3 d-flex align-items-center">
                <i class="fas fa-check-circle me-2 ms-2"></i>
                <div class="small fw-bold">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 py-2 mb-3 d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2 ms-2"></i>
                <div class="small fw-bold">{{ session('error') }}</div>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                @forelse($exam->questions as $index => $q)
                    <div class="card border-0 shadow-sm rounded-4 mb-2 overflow-hidden card-question">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill px-3 py-1 fw-bold shadow-sm"
                                        style="font-size: 0.7rem;">SOAL #{{ $index + 1 }}</span>
                                    <span class="ms-3 text-muted fw-semibold" style="font-size: 0.7rem;">
                                        <i class="far fa-dot-circle me-1"></i>
                                        {{ strtoupper(str_replace('_', ' ', $q->type ?? 'Pilihan Ganda')) }}
                                    </span>
                                </div>

                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-light text-danger rounded-3"
                                        onclick="alertHapus({{ $q->id }})" style="padding: 2px 8px;">
                                        <i class="fas fa-trash-alt fa-xs"></i>
                                    </button>
                                    <form id="delete-form-{{ $q->id }}"
                                        action="{{ route('questions.delete', $q->id) }}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </div>

                            <div class="ms-1 mb-3">
                                <div class="text-dark fw-bold mb-2" style="line-height: 1.3; font-size: 0.9rem;">
                                    {{ $q->question_text }}
                                </div>

                                @if ($q->image)
                                    <div class="mb-3 mt-2 ms-1">
                                        <img src="{{ Storage::url($q->image) }}" class="rounded-4 border shadow-sm"
                                            style="max-height: 200px; width: auto; object-fit: contain; display: block;"
                                            onerror="this.onerror=null; this.src='https://placehold.co/400x200?text=Gambar+Tidak+Ada';">
                                    </div>
                                @endif
                            </div>

                            <div class="row g-2 ms-1">
                                @foreach ($q->options as $idx => $opt)
                                    <div class="col-md-6">
                                        <div class="p-2 px-3 rounded-3 border d-flex align-items-center {{ $opt->is_correct ? 'border-success bg-soft-success border-2' : 'border-light bg-light opacity-85' }}"
                                            style="{{ $opt->is_correct ? 'background: #f0fdf4 !important;' : '' }}">

                                            <div class="flex-shrink-0 {{ $opt->is_correct ? 'bg-success text-white' : 'bg-white text-muted border' }} rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm text-uppercase"
                                                style="width: 22px; height: 22px; font-size: 0.65rem;">
                                                {{ chr(65 + $idx) }}
                                            </div>

                                            <div class="ms-2 {{ $opt->is_correct ? 'text-success fw-bold' : 'text-dark' }}"
                                                style="font-size: 0.8rem;">
                                                {{ $opt->option_text }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-4 py-4 text-center bg-white">
                        <div class="card-body text-muted">
                            <i class="fas fa-clipboard-list fa-2x mb-2 opacity-25"></i>
                            <p class="small mb-0">Belum ada pertanyaan.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pt-3 px-4 pb-0">
                    <h6 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Buat Soal Baru</h6>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('questions.store', $exam->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Tipe Soal</label>
                                <select name="type" id="typeSelector" class="form-select bg-light border-0 rounded-3 py-2 shadow-none focus-primary" required>
                                    <option value="pilihan_ganda">Pilihan Ganda</option>
                                    <option value="benar_salah">Benar / Salah</option>
                                    <option value="essay">Esai (Uraian)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Gambar Soal (Opsional)</label>
                                <input type="file" name="question_image" class="form-control bg-light border-0 rounded-3 py-2 shadow-none focus-primary" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Teks Pertanyaan</label>
                            <textarea name="question_text" class="form-control bg-light border-0 rounded-3 py-2 shadow-none focus-primary" rows="3" placeholder="Tuliskan butir soal di sini..." required></textarea>
                        </div>

                        <div id="section-options">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="font-size: 0.65rem;">Kunci Jawaban</label>

                            <div id="wrapper-pilihan-ganda" class="row g-2 mb-3">
                                @foreach (['A', 'B', 'C', 'D'] as $key => $label)
                                    <div class="col-md-6">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-text bg-white border-0">
                                                <input class="form-check-input custom-radio-key mt-0" type="radio" name="correct_answer" value="{{ $key }}" id="radio_pg_{{ $key }}">
                                            </div>
                                            <span class="input-group-text bg-white border-0 fw-bold text-primary pe-1">{{ $label }}</span>
                                            <input type="text" name="options[]" class="form-control bg-light border-0 py-2 rounded-3 shadow-none focus-primary" placeholder="Opsi {{ $label }}" id="input_pg_{{ $key }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="wrapper-benar-salah" class="row g-2 mb-3" style="display: none;">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="correct_answer_bs" id="ans_benar" value="1">
                                    <label class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold" for="ans_benar">
                                        <i class="fas fa-check me-2"></i> BENAR
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="correct_answer_bs" id="ans_salah" value="0">
                                    <label class="btn btn-outline-danger w-100 rounded-3 py-2 fw-bold" for="ans_salah">
                                        <i class="fas fa-times me-2"></i> SALAH
                                    </label>
                                </div>
                                <input type="hidden" name="bs_options[]" value="Benar">
                                <input type="hidden" name="bs_options[]" value="Salah">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-3 px-4 gap-2">
                        <button type="button" class="btn btn-light btn-sm rounded-3 fw-semibold px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-bold px-4 shadow-sm border-0 transition-3d">
                            <i class="fas fa-save me-1"></i> Simpan Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pt-3 px-4 pb-0">
                    <h6 class="fw-bold mb-0" id="importModalLabel"><i class="fas fa-file-import me-2 text-success"></i>Import Soal via CSV</h6>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('ujian.importQuestions', $exam->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 rounded-4 mb-4" style="background-color: #f0f9ff;">
                            <div class="d-flex small">
                                <i class="fas fa-info-circle text-info mt-1 me-2"></i>
                                <div>
                                    <strong class="d-block mb-1">Petunjuk Kolom CSV:</strong>
                                    <ol class="mb-0 ps-3">
                                        <li>Teks Pertanyaan</li>
                                        <li>Tipe (pilihan_ganda/benar_salah/essay)</li>
                                        <li>Opsi A | 4. Opsi B | 5. Opsi C | 6. Opsi D</li>
                                        <li>Jawaban (A/B/C/D atau Benar/Salah)</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Pilih File CSV</label>
                            <input type="file" name="file" class="form-control bg-light border-0 rounded-3 py-2 shadow-none focus-primary" accept=".csv,.txt" required>
                            <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;">*Gunakan pemisah koma (,)</small>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pb-3 px-4 gap-2">
                        <button type="button" class="btn btn-light btn-sm rounded-3 fw-semibold px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm rounded-3 fw-bold px-4 shadow-sm border-0 transition-3d">
                            <i class="fas fa-upload me-1"></i> Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-success { background-color: #f0fdf4; }
        .transition-3d:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08) !important; }
        .focus-primary:focus { background-color: #fff !important; border: 1px solid #4f46e5 !important; box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.1) !important; }
        .card-question { border-left: 4px solid #4f46e5 !important; }
        .custom-radio-key { width: 1.1rem; height: 1.1rem; cursor: pointer; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function alertHapus(id) {
            Swal.fire({
                title: 'Hapus Soal?',
                text: "Data soal akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }

        // Script Selector Tipe Soal Manual
        document.getElementById('typeSelector').addEventListener('change', function() {
            const sectionOptions = document.getElementById('section-options');
            const wrapperPG = document.getElementById('wrapper-pilihan-ganda');
            const wrapperBS = document.getElementById('wrapper-benar-salah');

            const inputsPG = wrapperPG.querySelectorAll('input[type="text"]');
            const radiosPG = wrapperPG.querySelectorAll('input[type="radio"]');
            const radiosBS = wrapperBS.querySelectorAll('input[name="correct_answer_bs"]');

            if (this.value === 'pilihan_ganda') {
                sectionOptions.style.display = 'block';
                wrapperPG.style.display = 'flex';
                wrapperBS.style.display = 'none';
                inputsPG.forEach(input => input.required = true);
                if (radiosPG.length > 0) radiosPG[0].required = true;
                radiosBS.forEach(radio => radio.required = false);
            } else if (this.value === 'benar_salah') {
                sectionOptions.style.display = 'block';
                wrapperPG.style.display = 'none';
                wrapperBS.style.display = 'flex';
                inputsPG.forEach(input => input.required = false);
                radiosPG.forEach(radio => radio.required = false);
                if (radiosBS.length > 0) radiosBS[0].required = true;
            } else {
                sectionOptions.style.display = 'none';
                inputsPG.forEach(input => input.required = false);
                radiosPG.forEach(radio => radio.required = false);
                radiosBS.forEach(radio => radio.required = false);
            }
        });
    </script>
@endsection