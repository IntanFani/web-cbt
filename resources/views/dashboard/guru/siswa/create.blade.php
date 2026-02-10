@extends('layouts.app')

@section('title', 'Tambah Siswa Baru')
@section('nav-greeting', 'Pendaftaran Siswa')
@section('nav-description', 'Lengkapi formulir untuk mendaftarkan siswa ke dalam sistem.')

@section('back-button')
    <a href="{{ route('siswa.index') }}" class="nav-back-btn">
        <i class="fas fa-arrow-left"></i>
    </a>
@endsection

@section('content')
<div class="container-fluid px-0 px-md-2">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-2 rounded-3 me-3" style="background: #eef2ff;">
                            <i class="fas fa-user-plus text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Informasi Akun & Akademik</h5>
                            <p class="text-muted small mb-0">Pastikan data email dan kelas sudah benar.</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('siswa.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-3 px-3 rounded-3 shadow-none focus-primary" 
                                       placeholder="Masukkan nama lengkap siswa" required>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Alamat Email</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-3 px-3 rounded-3 shadow-none focus-primary" 
                                       placeholder="siswa@contoh.com" required>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Kelas</label>
                                <select name="kelas_id" class="form-select bg-light border-0 py-3 px-3 rounded-3 shadow-none focus-primary" required>
                                    <option value="" selected disabled>Pilih Kelas...</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Tahun Angkatan</label>
                                <input type="number" name="angkatan" class="form-control bg-light border-0 py-3 px-3 rounded-3 shadow-none focus-primary" 
                                       placeholder="2024" min="2020" max="2099" required>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Password Akun</label>
                                <input type="password" name="password" class="form-control bg-light border-0 py-3 px-3 rounded-3 shadow-none focus-primary" 
                                       placeholder="Minimal 6 karakter" required>
                            </div>

                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 border-0 d-flex align-items-center mb-2">
                                    <i class="fas fa-shield-alt text-primary me-3"></i>
                                    <span class="small text-muted">Siswa akan menggunakan <strong>Email</strong> dan <strong>Password</strong> di atas untuk masuk ke panel ujian mereka.</span>
                                </div>
                            </div>
                        </div>

                        <hr class="opacity-25 my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('siswa.index') }}" class="btn btn-light rounded-3 px-4 py-2.5 fw-semibold text-muted">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 py-2.5 fw-bold shadow-sm border-0">
                                <i class="fas fa-save me-2"></i>Simpan Data Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 p-3 rounded-4 border-0 d-flex align-items-center" style="background: #f0f9ff; border: 1px dashed #b9e2fe !important;">
                <i class="fas fa-info-circle text-info fs-5 me-3"></i>
                <p class="mb-0 small text-dark">
                    <strong>Catatan:</strong> Siswa yang baru ditambahkan secara otomatis akan berstatus <strong>Aktif</strong> dan dapat langsung mengerjakan ujian yang tersedia untuk kelas mereka.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Fokus Input ala Gemini/Indigo */
    .focus-primary:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.1) !important;
        border: 1px solid #4f46e5 !important;
    }

    /* Memperbaiki tinggi input agar seragam */
    .form-control, .form-select {
        transition: all 0.2s;
    }

    /* Memberikan sedikit animasi pada card */
    .card {
        animation: slideUp 0.4s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection