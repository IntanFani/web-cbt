<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Manajemen Siswa</h4>
            <p class="text-muted">Kelola akun siswa yang terdaftar di sistem.</p>
        </div>
        <div>
            <a href="<?php echo e(route('dashboard.guru')); ?>" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="<?php echo e(route('siswa.create')); ?>" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Tambah Siswa
            </a>
        </div>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body bg-light p-3">
            <form action="<?php echo e(route('siswa.index')); ?>" method="GET" class="row g-2">
                
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="<?php echo e(request('search')); ?>">
                </div>

                <div class="col-md-3">
                    <select name="filter_kelas" class="form-select">
                        <option value="">-- Semua Kelas --</option>
                        <?php $__currentLoopData = $kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k->id); ?>" <?php echo e(request('filter_kelas') == $k->id ? 'selected' : ''); ?>>
                                <?php echo e($k->nama_kelas); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="filter_angkatan" class="form-select">
                        <option value="">-- Semua Angkatan --</option>
                        <?php $__currentLoopData = $angkatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($thn); ?>" <?php echo e(request('filter_angkatan') == $thn ? 'selected' : ''); ?>>
                                Angkatan <?php echo e($thn); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Kelas</th> 
                        <th>Angkatan</th> 
                        <th>Tanggal Bergabung</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-4"><?php echo e($index + 1); ?></td>
                            <td class="fw-bold"><?php echo e($s->name); ?></td>
                            <td><?php echo e($s->email); ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo e($s->kelas->nama_kelas ?? 'Tanpa Kelas'); ?></span>
                            </td>
                            <td><?php echo e($s->angkatan ?? '-'); ?></td> 
                            <td><?php echo e($s->created_at->format('d M Y')); ?></td>
                            <td class="text-end pe-4">
                                <a href="<?php echo e(route('siswa.edit', $s->id)); ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="<?php echo e(route('siswa.delete', $s->id)); ?>" method="POST" class="d-inline" id="delete-form-<?php echo e($s->id); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo e($s->id); ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada data siswa.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3">
                <?php echo e($siswa->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Siswa?',
            text: "Akun ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

</body>
</html><?php /**PATH C:\xampp\htdocs\web-cbt\resources\views/dashboard/guru/siswa/index.blade.php ENDPATH**/ ?>