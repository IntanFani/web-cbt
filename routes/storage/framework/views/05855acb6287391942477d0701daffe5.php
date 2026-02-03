<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian: <?php echo e($exam->title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Agar soal tidak nempel ke bawah */
        body { padding-bottom: 60px; background-color: #f8f9fa; }
        
        /* Style Nomor Soal di Sidebar */
        .btn-nomor {
            width: 40px; height: 40px; margin: 3px; font-weight: bold;
            border-radius: 5px; border: 1px solid #ced4da;
        }
        .btn-nomor.active { background-color: #0d6efd; color: white; border-color: #0d6efd; }
        .btn-nomor.answered { background-color: #198754; color: white; border-color: #198754; }

        /* Timer Sticky di Atas */
        .timer-bar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1030;
            background: #212529; color: #fff; padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .main-content { margin-top: 70px; }
    </style>
</head>
<body>

    <div class="timer-bar d-flex justify-content-between align-items-center">
        <div class="fw-bold">
            <span class="d-none d-md-inline">Sisa Waktu: </span>
            <span id="countdown" class="text-warning fs-5 font-monospace">Loading...</span>
        </div>
        <div>
            <span class="d-none d-md-inline me-2"><?php echo e(Auth::user()->name); ?></span>
            <button class="btn btn-sm btn-outline-light d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarSoal">
                📋 Soal
            </button>
            <button class="btn btn-sm btn-danger ms-2" onclick="confirmSelesai()">Selesai Ujian</button>
        </div>
    </div>

    <div class="container-fluid main-content">
        <div class="row">
            
            <div class="col-md-3 d-none d-md-block">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Navigasi Soal</div>
                    <div class="card-body text-center" id="nav-desktop">
                        </div>
                </div>
            </div>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarSoal">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">Daftar Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body text-center" id="nav-mobile">
                    </div>
            </div>

            <div class="col-md-9 col-12">
                <?php $__currentLoopData = $exam->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card shadow-sm soal-container mb-3 <?php echo e($index == 0 ? '' : 'd-none'); ?>" id="soal-<?php echo e($index); ?>">
                        <div class="card-body">
                            <h5 class="card-title">Soal No. <?php echo e($index + 1); ?></h5>
                            <p class="card-text fs-5 mt-3"><?php echo nl2br(e($q->question_text)); ?></p>
                            
                            <hr>
                            
                            <div class="list-group">
                                <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="list-group-item list-group-item-action">
                                        <input class="form-check-input me-1" type="radio" 
                                               name="jawaban_<?php echo e($q->id); ?>" 
                                               value="<?php echo e($opt->id); ?>"
                                               onclick="simpanJawaban(<?php echo e($q->id); ?>, <?php echo e($opt->id); ?>, <?php echo e($index); ?>)">
                                        <?php echo e($opt->option_text); ?>

                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <?php if($index > 0): ?>
                                <button class="btn btn-secondary" onclick="pindahSoal(<?php echo e($index - 1); ?>)">⬅ Sebelumnya</button>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>

                            <?php if($index < $exam->questions->count() - 1): ?>
                                <button class="btn btn-primary" onclick="pindahSoal(<?php echo e($index + 1); ?>)">Selanjutnya ➡</button>
                            <?php else: ?>
                                <button class="btn btn-success" onclick="confirmSelesai()">Selesai ✅</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ==========================================
        // 1. DATA PENTING DARI LARAVEL
        // ==========================================
        const sessionId = "<?php echo e($session->id); ?>";
        const csrfToken = "<?php echo e(csrf_token()); ?>";
        const totalSoal = <?php echo e($exam->questions->count()); ?>;
        const endTime   = new Date("<?php echo e($session->end_time); ?>").getTime(); 
        let currentSoal = 0;

        // Ambil daftar jawaban yang sudah tersimpan di database (agar tombol hijau saat refresh)
        // Kita ubah data PHP array ke format JSON JavaScript
        const answeredQuestions = <?php echo json_encode($session->answers->pluck('option_id', 'question_id'), 512) ?>;

        // ==========================================
        // 2. LOGIC NAVIGASI & TIMER
        // ==========================================
        
        // Generate Tombol Navigasi Soal
        function generateNav() {
            let html = '';
            // Kita loop dari index 0 sampai total soal
            for (let i = 0; i < totalSoal; i++) {
                // Cek apakah soal ini sudah dijawab? (Ada di list answeredQuestions?)
                // Karena array index dimulai dari 0, tapi ID soal beda, 
                // nanti kita perlu logic tambahan kalau mau sync ID. 
                // TAPI, untuk penanda warna sederhana, kita pakai class manual dulu via JS.
                
                html += `<button class="btn btn-nomor ${i === 0 ? 'active' : ''}" 
                        id="nav-btn-${i}" onclick="pindahSoal(${i})">${i + 1}</button>`;
            }
            document.getElementById('nav-desktop').innerHTML = html;
            document.getElementById('nav-mobile').innerHTML = html;
            
            // Loop lagi untuk menandai soal yang SUDAH dijawab (warna hijau)
            // Kita butuh ID soal asli untuk mencocokkan dengan data 'answeredQuestions'
            // (Fitur ini akan sempurna kalau kita mapping ID soal ke Index, 
            //  untuk sekarang manual dulu saat klik).
        }
        
        // Jalankan fungsi navigasi
        generateNav();

        // Pindah Soal
        window.pindahSoal = function(index) {
            // Hide soal lama
            document.getElementById(`soal-${currentSoal}`).classList.add('d-none');
            document.getElementById(`nav-btn-${currentSoal}`).classList.remove('active');
            
            // Show soal baru
            document.getElementById(`soal-${index}`).classList.remove('d-none');
            document.getElementById(`nav-btn-${index}`).classList.add('active');
            
            currentSoal = index;
        }

        // Timer Hitung Mundur
        let timer = setInterval(function() {
            let now = new Date().getTime();
            let distance = endTime - now;

            if (distance < 0) {
                clearInterval(timer);
                document.getElementById("countdown").innerHTML = "WAKTU HABIS";
                alert("Waktu Ujian Telah Habis!");
                window.location.href = "<?php echo e(route('dashboard.siswa')); ?>"; 
                return;
            }

            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("countdown").innerHTML = 
                (hours < 10 ? "0" : "") + hours + ":" +
                (minutes < 10 ? "0" : "") + minutes + ":" +
                (seconds < 10 ? "0" : "") + seconds;
        }, 1000);

        // ==========================================
        // 3. LOGIC SIMPAN JAWABAN (AJAX)
        // ==========================================
        window.simpanJawaban = function(questionId, optionId, index) {
            // Kirim data ke server
            fetch("<?php echo e(route('ujian.simpan')); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    question_id: questionId,
                    option_id: optionId
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    console.log("Jawaban tersimpan!");
                    // Ubah warna tombol navigasi jadi hijau
                    document.getElementById(`nav-btn-${index}`).classList.add('answered');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // ==========================================
        // 4. RESTORE JAWABAN (Saat Refresh)
        // ==========================================
        // Kode ini akan menandai tombol hijau & radio button yang terpilih 
        // berdasarkan data dari database saat halaman dimuat.
        
        // Looping data jawaban dari server
        for (const [qId, optId] of Object.entries(answeredQuestions)) {
            // 1. Cari radio button yang punya value == optId dan centang
            let radioBtn = document.querySelector(`input[name="jawaban_${qId}"][value="${optId}"]`);
            if (radioBtn) {
                radioBtn.checked = true;
                
                // 2. Cari tombol navigasi terkait dan beri warna hijau
                // (Kita cari parent div soalnya untuk tahu index-nya)
                let soalDiv = radioBtn.closest('.soal-container');
                if (soalDiv) {
                    // Ambil index dari ID elemen (contoh: "soal-0" -> index 0)
                    let index = soalDiv.id.replace('soal-', '');
                    let navBtn = document.getElementById(`nav-btn-${index}`);
                    if (navBtn) navBtn.classList.add('answered');
                }
            }
        }

        window.confirmSelesai = function() {
            Swal.fire({
                title: 'Selesaikan Ujian?',
                text: "Pastikan semua jawaban sudah terisi. Kamu tidak bisa mengubahnya lagi setelah ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6', // Warna biru
                cancelButtonColor: '#d33',    // Warna merah
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Masih Ragu'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading biar terlihat prosesnya
                    Swal.fire({
                        title: 'Memproses Nilai...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // --- LOGIC KIRIM FORM (Sama seperti sebelumnya) ---
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "<?php echo e(route('ujian.selesai', $exam->id)); ?>";
                    
                    let csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\web-cbt\resources\views/dashboard/siswa/ujian.blade.php ENDPATH**/ ?>