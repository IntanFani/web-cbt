@extends('layouts.app')

@section('title', 'Edit Data Siswa')
@section('nav-greeting', 'Perbarui Akun Siswa')
@section('nav-description', 'Perbarui informasi profil atau kredensial login siswa dengan teliti.')

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
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-warning p-2 rounded-3 me-3" style="background: #fffbeb;">
                                <i class="fas fa-user-edit text-warning"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">Profil & Akademik</h5>
                                <p class="text-muted small mb-0">ID Siswa: <span class="fw-bold text-primary">#{{ $siswa->id }}</span></p>
                            </div>
                        </div>
                        <div class="d-none d-md-block">
                            <span class="badge rounded-pill bg-light text-muted fw-normal p-2 px-3 border">
                                <i class="fas fa-calendar-alt me-1"></i> Terdaftar: {{ $siswa->created_at->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-3 px-3 rounded-3 shadow-none focus-warning" 
                                       value="{{ $siswa->name }}" required>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Alamat Email</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-3 px-3 rounded-3 shadow-none focus-warning" 
                                       value="{{ $siswa->email }}" required>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Kelas</label>
                                <select name="kelas_id" class="form-select bg-light border-0 py-3 px-3 rounded-3 shadow-none focus-warning" required>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Tahun Angkatan</label>
                                <input type="number" name="angkatan" class="form-control bg-light border-0 py-3 px-3 rounded-3 shadow-none focus-warning" 
                                       value="{{ $siswa->angkatan }}" required>
                            </div>

                            <div class="col-12 mt-2">
                                <h6 class="fw-bold text-danger mb-3">Keamanan Akun</h6>
                                <label class="form-label small fw-bold text-muted text-uppercase">Ganti Password (Kosongkan jika tidak diganti)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-3 px-3">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control bg-light border-0 py-3 px-3 rounded-end-3 shadow-none focus-warning" 
                                           placeholder="Masukkan password baru jika perlu">
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="bg-soft-primary p-3 rounded-3 d-flex align-items-center" style="background: #f0f7ff;">
                                    <i class="fas fa-info-circle text-primary me-3"></i>
                                    <small class="text-dark">Sistem mengenali perubahan data secara otomatis. Pastikan email tetap aktif untuk keperluan login.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="opacity-25 my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('siswa.index') }}" class="btn btn-light rounded-3 px-4 py-2.5 fw-semibold text-muted">Batal</a>
                            <button type="submit" class="btn btn-warning rounded-3 px-4 py-2.5 fw-bold shadow-sm border-0">
                                <i class="fas fa-check-circle me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-muted small">
                    <i class="fas fa-history me-1"></i> Terakhir diperbarui {{ $siswa->updated_at->diffForHumans() }}
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Focus kuning untuk mode Edit */
    .focus-warning:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.15) !important;
        border: 1px solid #ffc107 !important;
    }

    .card {
        animation: slideUp 0.4s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection