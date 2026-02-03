<!DOCTYPE html>
<html>
<head>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Laporan Nilai Ujian</h3>
    <p>
        <strong>Judul Ujian:</strong> <?php echo e($exam->title); ?> <br>
        <strong>Tanggal Download:</strong> <?php echo e(date('d M Y')); ?>

    </p>

    <table>
        <thead>
            <tr>
                <th style="background-color: #4CAF50; color: white;">Peringkat</th>
                <th style="background-color: #4CAF50; color: white;">Nama Siswa</th>
                <th style="background-color: #4CAF50; color: white;">Kelas</th>
                <th style="background-color: #4CAF50; color: white;">Angkatan</th>
                <th style="background-color: #4CAF50; color: white;">Waktu Selesai</th>
                <th style="background-color: #4CAF50; color: white;">Nilai Akhir</th>
                <th style="background-color: #4CAF50; color: white;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($session->user->name); ?></td>
                <td><?php echo e($session->user->kelas->nama_kelas ?? '-'); ?></td>
                <td>'<?php echo e($session->user->angkatan ?? '-'); ?></td>
                <td><?php echo e($session->updated_at->format('d/m/Y H:i')); ?></td>
                <td style="font-weight: bold;"><?php echo e($session->score); ?></td>
                <td><?php echo e($session->score >= 70 ? 'Lulus' : 'Remedial'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html><?php /**PATH C:\xampp\htdocs\web-cbt\resources\views/dashboard/guru/export_excel.blade.php ENDPATH**/ ?>