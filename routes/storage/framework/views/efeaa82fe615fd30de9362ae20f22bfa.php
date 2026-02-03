<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Soal - <?php echo e($exam->title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Kelola Soal</h4>
            <p class="text-muted mb-0">Ujian: <?php echo e($exam->title); ?></p>
        </div>
        <a href="<?php echo e(route('dashboard.guru')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Soal Baru
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('questions.store', $exam->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Pertanyaan</label>
                            <textarea name="question_text" class="form-control" rows="3" placeholder="Tulis pertanyaan di sini..." required></textarea>
                        </div>

                        <label class="form-label">Pilihan Jawaban</label>
                        
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="correct_answer" value="0" required>
                                <span class="ms-2 fw-bold">A</span>
                            </div>
                            <input type="text" name="options[]" class="form-control" placeholder="Jawaban A" required>
                        </div>

                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="correct_answer" value="1">
                                <span class="ms-2 fw-bold">B</span>
                            </div>
                            <input type="text" name="options[]" class="form-control" placeholder="Jawaban B" required>
                        </div>

                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="correct_answer" value="2">
                                <span class="ms-2 fw-bold">C</span>
                            </div>
                            <input type="text" name="options[]" class="form-control" placeholder="Jawaban C" required>
                        </div>

                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="correct_answer" value="3">
                                <span class="ms-2 fw-bold">D</span>
                            </div>
                            <input type="text" name="options[]" class="form-control" placeholder="Jawaban D" required>
                        </div>

                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-info-circle me-1"></i> Klik bulatan (Radio Button) untuk memilih kunci jawaban.
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            Simpan Soal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <h5 class="mb-3">Daftar Soal (<?php echo e($exam->questions->count()); ?>)</h5>
            
            <?php $__empty_1 = true; $__currentLoopData = $exam->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold">No. <?php echo e($index + 1); ?></h6>
                            <form id="delete-form-<?php echo e($q->id); ?>" action="<?php echo e(route('questions.delete', $q->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                
                                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="alertHapus(<?php echo e($q->id); ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        
                        <p class="mb-3"><?php echo e($q->question_text); ?></p>
                        
                        <ul class="list-group list-group-flush small">
                            <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item <?php echo e($opt->is_correct ? 'bg-success bg-opacity-10 fw-bold text-success' : ''); ?>">
                                    <?php if($opt->is_correct): ?> <i class="fas fa-check me-2"></i> <?php endif; ?>
                                    <?php echo e($opt->option_text); ?>

                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                    <p>Belum ada soal di ujian ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function alertHapus(id) {
        Swal.fire({
            title: 'Hapus Soal Ini?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Merah (Bahaya)
            cancelButtonColor: '#3085d6', // Biru (Batal)
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Cari form berdasarkan ID unik tadi, lalu submit
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
</body>
</html><?php /**PATH C:\xampp\htdocs\web-cbt\resources\views/dashboard/guru/questions.blade.php ENDPATH**/ ?>