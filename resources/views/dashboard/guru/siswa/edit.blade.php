@extends('layouts.app')

@section('title', 'Edit Data Siswa')
@section('page-title', 'Perbarui Akun Siswa')

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
                        <div class="bg-soft-warning p-2 rounded-3 me-3" style="background: #fffbeb;">
                            <i class="fas fa-user-edit text-warning"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Edit Akun Siswa</h5>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-2 px-3 rounded-3" 
                                       value="{{ $siswa->name }}" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Alamat Email</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2 px-3 rounded-3" 
                                       value="{{ $siswa->email }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Kelas</label>
                                <select name="kelas_id" class="form-select bg-light border-0 py-2 px-3 rounded-3" required>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Tahun Angkatan</label>
                                <input type="number" name="angkatan" class="form-control bg-light border-0 py-2 px-3 rounded-3" 
                                       value="{{ $siswa->angkatan }}" required>
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label small fw-bold text-muted text-uppercase">Password Baru (Opsional)</label>
                                <input type="password" name="password" class="form-control bg-light border-0 py-2 px-3 rounded-3" 
                                       placeholder="Isi hanya jika ingin mengganti password">
                            </div>
                        </div>

                        <div class="bg-soft-primary p-3 rounded-3 mb-4 mt-2" style="background: #eff6ff;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                <small class="text-primary fw-medium">Kosongkan kolom password jika tidak ada perubahan.</small>
                            </div>
                        </div>

                        <hr class="opacity-25 mb-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('siswa.index') }}" class="btn btn-light rounded-3 px-4 fw-semibold">Batal</a>
                            <button type="submit" class="btn btn-warning rounded-3 px-4 fw-bold shadow-sm text-dark">
                                <i class="fas fa-sync-alt me-2"></i>Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-muted small">
                    <i class="fas fa-history me-1"></i> Terakhir diperbarui: {{ $siswa->updated_at->diffForHumans() }}
                </p>
            </div>
            
        </div>
    </div>
</div>
@endsection