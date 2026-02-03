@extends('layouts.app')

@section('title', 'Tambah Siswa Baru')
@section('page-title', 'Pendaftaran Siswa')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            
            <div class="mb-4">
                <a href="{{ route('siswa.index') }}" class="text-decoration-none text-muted small fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> KEMBALI KE DAFTAR SISWA
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-2 rounded-3 me-3" style="background: #eef2ff;">
                            <i class="fas fa-user-plus text-primary"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Tambah Akun Siswa</h5>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">Pastikan alamat email yang dimasukkan aktif untuk keperluan login siswa.</p>

                    <form action="{{ route('siswa.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-2 px-3 rounded-3" 
                                       placeholder="Masukkan nama lengkap siswa" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Alamat Email</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2 px-3 rounded-3" 
                                       placeholder="siswa@contoh.com" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Kelas</label>
                                <select name="kelas_id" class="form-select bg-light border-0 py-2 px-3 rounded-3" required>
                                    <option value="" selected disabled>Pilih Kelas...</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Tahun Angkatan</label>
                                <input type="number" name="angkatan" class="form-control bg-light border-0 py-2 px-3 rounded-3" 
                                       placeholder="2024" min="2020" max="2099" required>
                            </div>

                            <div class="col-12 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Password Akun</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control bg-light border-0 py-2 px-3 rounded-3" 
                                           placeholder="Minimal 6 karakter" required>
                                </div>
                                <div class="form-text small mt-2">Password ini akan digunakan siswa untuk login pertama kali.</div>
                            </div>
                        </div>

                        <hr class="opacity-25 mb-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('siswa.index') }}" class="btn btn-light rounded-3 px-4 fw-semibold">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i>Simpan Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 p-3 bg-soft-info rounded-4 border-0 d-flex align-items-start" style="background: #f0f9ff;">
                <i class="fas fa-info-circle text-info mt-1 me-3"></i>
                <p class="mb-0 small text-muted">
                    Siswa yang baru ditambahkan secara otomatis akan memiliki status <strong>Aktif</strong> dan dapat langsung mengakses ujian yang sesuai dengan kelasnya.
                </p>
            </div>
            
        </div>
    </div>
</div>
@endsection