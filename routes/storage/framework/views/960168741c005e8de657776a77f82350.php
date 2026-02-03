<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - CBT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-chalkboard-teacher me-2"></i>Panel Guru
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Halo, <?php echo e(Auth::user()->name); ?></span>
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-secondary">Daftar Ujian Saya</h3>
            
            <div>
                <a href="<?php echo e(route('kelas.index')); ?>" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-school me-2"></i>Data Kelas
                </a>

                <a href="<?php echo e(route('siswa.index')); ?>" class="btn btn-outline-dark me-2">
                    <i class="fas fa-users-cog me-2"></i>Kelola Siswa
                </a>
                
                <a href="<?php echo e(route('ujian.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Buat Ujian Baru
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Judul Ujian</th>
                            <th>Kode Token</th>
                            <th>Durasi</th>
                            <th>Soal</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo e($exam->title); ?></td>
                                <td><span class="badge bg-warning text-dark fs-6"><?php echo e($exam->token); ?></span></td>
                                <td><?php echo e($exam->duration); ?> Menit</td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($exam->questions->count()); ?> Soal</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?php echo e(route('ujian.questions', $exam->id)); ?>" class="btn btn-sm btn-outline-primary" title="Kelola Soal">
                                        <i class="fas fa-list"></i> Soal
                                    </a>
                                    <a href="<?php echo e(route('ujian.edit', $exam->id)); ?>" class="btn btn-sm btn-outline-warning" title="Edit Ujian">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-exam-<?php echo e($exam->id); ?>" action="<?php echo e(route('ujian.delete', $exam->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="alertHapusUjian(<?php echo e($exam->id); ?>)" title="Hapus Ujian">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="<?php echo e(route('ujian.results', $exam->id)); ?>" class="btn btn-sm btn-outline-success" title="Lihat Hasil Siswa">
                                        <i class="fas fa-trophy"></i> Hasil
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                    <p>Belum ada ujian yang dibuat. Yuk buat sekarang!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function alertHapusUjian(id) {
            Swal.fire({
                title: 'Hapus Ujian Ini?',
                text: "PERINGATAN: Semua soal dan nilai siswa di ujian ini akan ikut terhapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Merah
                cancelButtonColor: '#3085d6', // Biru
                confirmButtonText: 'Ya, Hapus Semuanya!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-exam-' + id).submit();
                }
            })
        }
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\web-cbt\resources\views/dashboard/guru/index.blade.php ENDPATH**/ ?>