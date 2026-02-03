<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Ujian Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="mb-0 fw-bold text-primary">Buat Jadwal Ujian Baru</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="<?php echo e(route('ujian.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Judul Ujian</label>
                                <input type="text" name="title" class="form-control" placeholder="Contoh: Kuis Matematika Bab 1" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Durasi (Menit)</label>
                                <input type="number" name="duration" class="form-control" placeholder="Contoh: 90" required>
                                <small class="text-muted">Berapa lama siswa boleh mengerjakan?</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Pengaturan Soal</label>
                                <div class="form-check form-switch bg-light p-3 rounded border d-flex align-items-center">
                                    <input class="form-check-input m-0 me-3" type="checkbox" role="switch" 
                                        id="random" name="random_question" value="1" checked
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

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?php echo e(route('dashboard.guru')); ?>" class="btn btn-light me-2">Batal</a>
                                <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Ujian</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html><?php /**PATH C:\xampp\htdocs\web-cbt\resources\views/dashboard/guru/create.blade.php ENDPATH**/ ?>