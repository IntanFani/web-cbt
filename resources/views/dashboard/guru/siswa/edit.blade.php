<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-warning">Edit Akun Siswa</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ $siswa->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $siswa->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tahun Angkatan</label>
                            <input type="number" name="angkatan" class="form-control" value="{{ $siswa->angkatan }}" placeholder="Contoh: 2024" required>
                        </div>
                        
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="fas fa-info-circle me-1"></i> Kosongkan password jika tidak ingin menggantinya.
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin ganti password">
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('siswa.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-warning fw-bold px-4">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>