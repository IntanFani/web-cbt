<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian: {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Agar soal tidak nempel ke bawah */
        body { padding-bottom: 60px; background-color: #f8f9fa; }
        
        /* Style Nomor Soal di Sidebar */
        .btn-nomor {
            width: 40px; height: 40px; margin: 4px; font-weight: bold;
            border-radius: 8px; border: 1px solid #dee2e6;
            transition: all 0.2s;
        }
        .btn-nomor:hover { background-color: #e9ecef; }
        .btn-nomor.active { background-color: #0d6efd; color: white; border-color: #0d6efd; box-shadow: 0 0 10px rgba(13,110,253,0.3); }
        .btn-nomor.answered { background-color: #198754; color: white; border-color: #198754; }

        /* Timer Sticky di Atas */
        .timer-bar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1030;
            background: #212529; color: #fff; padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .main-content { margin-top: 80px; }

        /* Card Soal */
        .soal-card { border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .nav-card { border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        /* Transisi Collapse */
        .collapse-icon { transition: transform 0.3s ease; }
        .collapsed .collapse-icon { transform: rotate(-180deg); }
    </style>
</head>
<body>

    <div class="timer-bar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="bg-warning text-dark px-3 py-1 rounded-pill me-3 fw-bold font-monospace d-flex align-items-center">
                <i class="fas fa-stopwatch me-2"></i>
                <span id="countdown">Loading...</span>
            </div>
            <span class="d-none d-md-inline text-light opacity-75 small">Ujian Berlangsung</span>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <span class="d-none d-md-inline fw-bold me-3">{{ Auth::user()->name }}</span>
            
            <button class="btn btn-outline-light btn-sm d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarSoal">
                <i class="fas fa-th-large"></i>
            </button>

            <button class="btn btn-danger btn-sm px-3 rounded-pill fw-bold" onclick="confirmSelesai()">
                <i class="fas fa-check-circle me-1"></i> Selesai
            </button>
        </div>
    </div>

    <div class="container-fluid main-content">
        <div class="row g-4">
            
            <div class="col-md-3 d-none d-md-block">
                <div class="card nav-card sticky-top" style="top: 80px; z-index: 1020;">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center" 
                         data-bs-toggle="collapse" data-bs-target="#navContent" style="cursor: pointer;">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-map-signs me-2 text-primary"></i> Navigasi Soal</h6>
                        <i class="fas fa-chevron-up text-muted collapse-icon"></i>
                    </div>
                    
                    <div class="collapse show" id="navContent">
                        <div class="card-body bg-light rounded-bottom-4">
                            <div class="d-flex flex-wrap justify-content-center gap-1" id="nav-desktop">
                                </div>
                            
                            <div class="mt-3 pt-3 border-top d-flex justify-content-center gap-3 small text-muted">
                                <div class="d-flex align-items-center">
                                    <div class="rounded bg-primary me-1" style="width: 12px; height: 12px;"></div> Aktif
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rounded bg-success me-1" style="width: 12px; height: 12px;"></div> Dijawab
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rounded bg-white border me-1" style="width: 12px; height: 12px;"></div> Kosong
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="offcanvas offcanvas-end rounded-start-4" tabindex="-1" id="sidebarSoal">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold"><i class="fas fa-list-ol me-2"></i> Daftar Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body bg-light">
                    <div class="d-flex flex-wrap justify-content-center gap-2" id="nav-mobile">
                        </div>
                </div>
            </div>

            <div class="col-md-9 col-12">
                @foreach($exam->questions as $index => $q)
                    <div class="card soal-card mb-3 soal-container {{ $index == 0 ? '' : 'd-none' }}" id="soal-{{ $index }}">
                        
                        <div class="card-header bg-white border-0 py-3 px-4 rounded-top-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-primary mb-0">Soal No. {{ $index + 1 }}</h5>
                            <span class="badge bg-light text-muted border rounded-pill px-3">
                                Pilihan Ganda
                            </span>
                        </div>

                        <div class="card-body p-4">
                            <div class="fs-5 mb-4 text-dark lh-base">
                                {!! nl2br(e($q->question_text)) !!}
                            </div>
                            
                            <div class="list-group list-group-flush gap-2">
                                @foreach($q->options as $opt)
                                    <label class="list-group-item rounded-3 border px-3 py-3 shadow-sm list-group-item-action cursor-pointer d-flex align-items-center">
                                        <input class="form-check-input me-3 my-0 border-2" type="radio" 
                                               name="jawaban_{{ $q->id }}" 
                                               value="{{ $opt->id }}"
                                               style="width: 1.3em; height: 1.3em;"
                                               onclick="simpanJawaban({{ $q->id }}, {{ $opt->id }}, {{ $index }})">
                                        <span class="fs-6">{{ $opt->option_text }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 py-3 px-4 rounded-bottom-4 d-flex justify-content-between">
                            @if($index > 0)
                                <button class="btn btn-outline-secondary rounded-pill px-4" onclick="pindahSoal({{ $index - 1 }})">
                                    <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                                </button>
                            @else
                                <div></div>
                            @endif

                            @if($index < $exam->questions->count() - 1)
                                <button class="btn btn-primary rounded-pill px-4" onclick="pindahSoal({{ $index + 1 }})">
                                    Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            @else
                                <button class="btn btn-success rounded-pill px-4" onclick="confirmSelesai()">
                                    Selesai <i class="fas fa-check ms-2"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // ... (Script JS Logika Ujian SAMA SEPERTI SEBELUMNYA) ...
        // ... (Copy Paste bagian <script> dari jawaban sebelumnya kesini) ...
        
        // ==========================================
        // 1. DATA PENTING DARI LARAVEL
        // ==========================================
        const sessionId = "{{ $session->id }}";
        const csrfToken = "{{ csrf_token() }}";
        const totalSoal = {{ $exam->questions->count() }};
        const endTime   = new Date("{{ $session->end_time }}").getTime(); 
        let currentSoal = 0;

        // Ambil daftar jawaban yang sudah tersimpan
        const answeredQuestions = @json($session->answers->pluck('option_id', 'question_id'));

        // ==========================================
        // 2. LOGIC NAVIGASI & TIMER
        // ==========================================
        
        function generateNav() {
            let html = '';
            for (let i = 0; i < totalSoal; i++) {
                html += `<button class="btn btn-nomor ${i === 0 ? 'active' : ''}" 
                        id="nav-btn-${i}" onclick="pindahSoal(${i})">${i + 1}</button>`;
            }
            document.getElementById('nav-desktop').innerHTML = html;
            document.getElementById('nav-mobile').innerHTML = html;
        }
        
        generateNav();

        window.pindahSoal = function(index) {
            document.getElementById(`soal-${currentSoal}`).classList.add('d-none');
            // Hapus class active dari tombol sebelumnya, TAPI jangan hapus class answered kalau ada
            let oldBtn = document.getElementById(`nav-btn-${currentSoal}`);
            oldBtn.classList.remove('active');
            
            document.getElementById(`soal-${index}`).classList.remove('d-none');
            let newBtn = document.getElementById(`nav-btn-${index}`);
            newBtn.classList.add('active');
            
            currentSoal = index;
        }

        let timer = setInterval(function() {
            let now = new Date().getTime();
            let distance = endTime - now;

            if (distance < 0) {
                clearInterval(timer);
                document.getElementById("countdown").innerHTML = "HABIS";
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Ujian akan dikumpulkan otomatis.',
                    icon: 'warning',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Paksa submit form selesai
                    // (Logic submit otomatis bisa ditambahkan disini)
                    window.location.href = "{{ route('dashboard.siswa') }}"; 
                });
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
            fetch("{{ route('ujian.simpan') }}", {
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
                    document.getElementById(`nav-btn-${index}`).classList.add('answered');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // ==========================================
        // 4. RESTORE JAWABAN
        // ==========================================
        for (const [qId, optId] of Object.entries(answeredQuestions)) {
            let radioBtn = document.querySelector(`input[name="jawaban_${qId}"][value="${optId}"]`);
            if (radioBtn) {
                radioBtn.checked = true;
                let soalDiv = radioBtn.closest('.soal-container');
                if (soalDiv) {
                    let index = soalDiv.id.replace('soal-', '');
                    let navBtn = document.getElementById(`nav-btn-${index}`);
                    if (navBtn) navBtn.classList.add('answered');
                }
            }
        }

        window.confirmSelesai = function() {
            Swal.fire({
                title: 'Selesaikan Ujian?',
                text: "Pastikan semua jawaban sudah terisi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Cek Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Nilai...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('ujian.selesai', $exam->id) }}";
                    
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
</html>
