<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Ujian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-warning">Edit Ujian</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="{{ route('ujian.update', $exam->id) }}" method="POST">
                            @csrf
                            @method('PUT') <div class="mb-3">
                                <label class="form-label fw-bold">Judul Ujian</label>
                                <input type="text" name="title" class="form-control" value="{{ $exam->title }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Durasi (Menit)</label>
                                <input type="number" name="duration" class="form-control" value="{{ $exam->duration }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Pengaturan Soal</label>
                                <div class="form-check form-switch bg-light p-3 rounded border d-flex align-items-center">
                                    <input class="form-check-input m-0 me-3" type="checkbox" role="switch" 
                                        id="random" name="random_question" value="1" 
                                        {{ $exam->random_question ? 'checked' : '' }} 
                                        style="width: 3em; height: 1.5em; cursor: pointer;">
                                    
                                    <div>
                                        <label class="form-check-label fw-bold text-dark" for="random" style="cursor: pointer;">
                                            Acak Urutan Soal
                                        </label>
                                        <div class="text-muted small" style="font-size: 0.85rem;">
                                            Jika aktif, urutan nomor soal akan berbeda untuk setiap siswa.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('dashboard.guru') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-warning fw-bold">Update Ujian</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>