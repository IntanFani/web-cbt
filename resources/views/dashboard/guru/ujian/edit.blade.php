@extends('layouts.app')

@section('title', 'Edit Ujian')
@section('nav-greeting', 'Pengelolaan Soal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-warning p-2 rounded-3 me-3" style="background: #fffbeb;">
                            <i class="fas fa-edit text-warning"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Edit Detail Ujian</h5>
                            <p class="text-muted small mb-0">Perbarui informasi dan konfigurasi ujian di bawah ini.</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('ujian.update', $exam->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Judul Ujian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-file-signature text-muted"></i></span>
                                    <input type="text" name="title" class="form-control bg-light border-0 py-2" 
                                           value="{{ $exam->title }}" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Durasi Pengerjaan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="far fa-clock text-muted"></i></span>
                                    <input type="number" name="duration" class="form-control bg-light border-0 py-2" 
                                           value="{{ $exam->duration }}" required>
                                    <span class="input-group-text bg-light border-0 text-muted">Menit</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-3">Pengaturan Keamanan</label>
                            <div class="p-4 rounded-4 border-0 shadow-sm bg-light">
                                <div class="form-check form-switch d-flex align-items-center ps-0">
                                    <div class="flex-grow-1">
                                        <label class="form-check-label fw-bold text-dark d-block mb-1" for="random" style="cursor: pointer;">
                                            Acak Urutan Soal
                                        </label>
                                        <p class="text-muted mb-0 small">
                                            Jika diaktifkan, setiap siswa akan menerima urutan nomor soal yang berbeda untuk meminimalisir kecurangan.
                                        </p>
                                    </div>
                                    <input class="form-check-input ms-3" type="checkbox" role="switch" 
                                           id="random" name="random_question" value="1" 
                                           {{ $exam->random_question ? 'checked' : '' }}
                                           style="width: 3.2em; height: 1.6em; cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        <div class="p-3 mb-5 rounded-4 border-0 d-flex align-items-center justify-content-between" style="background: #fffbeb;">
                            <div class="d-flex align-items-center">
                                <div class="me-3 bg-white p-2 rounded-circle shadow-sm">
                                    <i class="fas fa-info-circle text-warning"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small text-muted">Token Saat Ini:</p>
                                    <span class="fw-bold fs-5 text-dark font-monospace">{{ $exam->token }}</span>
                                </div>
                            </div>
                            </div>

                        <hr class="opacity-25 mb-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('ujian.index') }}" class="btn btn-light rounded-3 px-5 fw-semibold border">Batal</a>
                            
                            <button type="submit" class="btn btn-warning rounded-3 px-5 fw-bold shadow-sm text-dark">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection