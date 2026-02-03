@extends('layouts.app')

@section('title', 'Jadwal Ujian')
@section('nav-greeting', 'Jadwal Ujian')
@section('nav-description', 'Mengatur sesi ujian baru.')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-2 rounded-3 me-3" style="background: #eef2ff;">
                            <i class="fas fa-calendar-plus text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Buat Jadwal Ujian Baru</h5>
                            <p class="text-muted small mb-0">Isi formulir di bawah untuk mengatur sesi ujian baru.</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('ujian.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Judul Ujian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-edit text-muted"></i></span>
                                    <input type="text" name="title" class="form-control bg-light border-0 py-2" 
                                           placeholder="Contoh: Kuis Matematika Bab 1" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Durasi Pengerjaan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="far fa-clock text-muted"></i></span>
                                    <input type="number" name="duration" class="form-control bg-light border-0 py-2" 
                                           placeholder="90" required>
                                    <span class="input-group-text bg-light border-0 text-muted">Menit</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-3">Pengaturan Keamanan</label>
                            <div class="p-4 rounded-4 border-0 shadow-sm bg-light">
                                <div class="form-check form-switch d-flex align-items-center ps-0">
                                    <div class="flex-grow-1">
                                        <label class="form-check-label fw-bold text-dark d-block mb-1" for="random">
                                            Acak Urutan Soal
                                        </label>
                                        <p class="text-muted mb-0 small">
                                            Jika diaktifkan, setiap siswa akan menerima urutan nomor soal yang berbeda untuk meminimalisir kecurangan.
                                        </p>
                                    </div>
                                    <input class="form-check-input ms-3" type="checkbox" role="switch" 
                                           id="random" name="random_question" value="1" checked
                                           style="width: 3.2em; height: 1.6em; cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        <div class="p-3 mb-5 rounded-4 border-0 d-flex align-items-center" style="background: #eff6ff;">
                            <div class="me-3 bg-white p-2 rounded-circle shadow-sm">
                                <i class="fas fa-info-circle text-primary"></i>
                            </div>
                            <p class="mb-0 small text-muted">
                                <strong>Tips:</strong> Setelah menyimpan, sistem akan mengarahkan Anda ke halaman <strong>Kelola Soal</strong> untuk memasukkan butir pertanyaan.
                            </p>
                        </div>

                        <hr class="opacity-25 mb-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('ujian.index') }}" class="btn btn-light rounded-3 px-5 fw-semibold border">Batal</a>
                            
                            <button type="submit" class="btn btn-primary rounded-3 px-5 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i>Simpan Jadwal Ujian
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection