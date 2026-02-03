<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo e(route('dashboard.guru')); ?>">
                <i class="fas fa-chalkboard-teacher me-2"></i>Panel Guru
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Halo, <?php echo e(Auth::user()->name); ?></span>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Manajemen Kelas</h4>
                <p class="text-muted">Tambah dan kelola daftar kelas di sekolah.</p>
            </div>
            <a href="<?php echo e(route('dashboard.guru')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Kelas Baru
                    </div>
                    <div class="card-body p-4">
                        <form action="<?php echo e(route('kelas.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Kelas</label>
                                <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: X RPL 1" required>
                                <small class="text-muted">Gunakan nama yang jelas dan unik.</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                Simpan Kelas
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Nama Kelas</th>
                                    <th>Jumlah Siswa</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?php echo e($index + 1); ?></td>
                                        <td>
                                            <span class="badge bg-info text-dark fs-6"><?php echo e($k->nama_kelas); ?></span>
                                        </td>
                                        <td>
                                            <i class="fas fa-users me-1 text-secondary"></i> 
                                            <?php echo e($k->students->count()); ?> Siswa
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="<?php echo e(route('kelas.delete', $k->id)); ?>" method="POST" id="delete-form-<?php echo e($k->id); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo e($k->id); ?>, '<?php echo e($k->nama_kelas); ?>')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-school fa-3x mb-3 opacity-25"></i>
                                            <p>Belum ada data kelas.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function confirmDelete(id, nama) {
            Swal.fire({
                title: 'Hapus Kelas?',
                text: "Kelas " + nama + " akan dihapus. Siswa di kelas ini akan menjadi 'Tanpa Kelas'.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\web-cbt\resources\views/dashboard/guru/kelas/index.blade.php ENDPATH**/ ?>