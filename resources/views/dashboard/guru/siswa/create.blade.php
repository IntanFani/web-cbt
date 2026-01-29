<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Siswa Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">Tambah Akun Siswa</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('siswa.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" placeholder="budi@sekolah.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tahun Angkatan</label>
                            <input type="number" name="angkatan" class="form-control" placeholder="Contoh: 2024" min="2020" max="2099" required>
                            <div class="form-text text-muted">Tahun masuk siswa.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('siswa.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Siswa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>