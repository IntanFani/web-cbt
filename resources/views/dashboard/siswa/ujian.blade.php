<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian: {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { padding-bottom: 60px; background-color: #f8f9fa; }
        .btn-nomor {
            width: 40px; height: 40px; font-weight: bold; border-radius: 8px; 
            border: 1px solid #dee2e6; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0; transition: all 0.2s;
            background-color: white; cursor: pointer;
        }
        #nav-desktop {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
            gap: 8px; justify-items: center;
        }
        .btn-nomor:hover { background-color: #e9ecef; }
        .btn-nomor.active { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd; box-shadow: 0 0 10px rgba(13,110,253,0.3); }
        .btn-nomor.answered { background-color: #198754; color: white; border-color: #198754; }
        .timer-bar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1030;
            background: #212529; color: #fff; padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .main-content { margin-top: 80px; }
        .soal-card, .nav-card { border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .img-soal { max-width: 100%; height: auto; border-radius: 10px; margin-bottom: 20px; display: block; }
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
            <button class="btn btn-danger btn-sm px-3 rounded-pill fw-bold" onclick="confirmSelesai()">
                <i class="fas fa-check-circle me-1"></i> Selesai
            </button>
        </div>
    </div>

    <div class="container-fluid main-content">
        <div class="row g-4">
            <div class="col-md-3 d-none d-md-block">
                <div class="card nav-card sticky-top" style="top: 80px; z-index: 1020;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-map-signs me-2 text-primary"></i> Navigasi Soal</h6>
                    </div>
                    <div class="card-body bg-light rounded-bottom-4">
                        <div id="nav-desktop"></div>
                        <div class="mt-3 pt-3 border-top d-flex justify-content-center gap-3 small text-muted">
                            <div class="d-flex align-items-center"><div class="rounded bg-primary me-1" style="width: 12px; height: 12px;"></div> Aktif</div>
                            <div class="d-flex align-items-center"><div class="rounded bg-success me-1" style="width: 12px; height: 12px;"></div> Dijawab</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-12">
                @foreach($exam->questions as $index => $q)
                    <div class="card soal-card mb-3 soal-container {{ $index == 0 ? '' : 'd-none' }}" id="soal-{{ $index }}">
                        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-primary mb-0">Soal No. {{ $index + 1 }}</h5>
                            <span class="badge bg-info text-dark border rounded-pill px-3">
                                {{ strtolower($q->type) == 'essay' ? 'Essay' : 'Pilihan Ganda' }}
                            </span>
                        </div>

                        <div class="card-body p-4">
                            @if($q->image)
                                <img src="{{ asset('storage/' . $q->image) }}" class="img-soal shadow-sm">
                            @endif

                            <div class="fs-5 mb-4 text-dark lh-base">{!! nl2br(e($q->question_text)) !!}</div>
                            
                            @if(strtolower($q->type) == 'essay')
                                <div class="form-group">
                                    <label class="fw-bold mb-2 text-muted">Jawaban Anda:</label>
                                    <textarea class="form-control border-2 shadow-sm" 
                                              name="jawaban_essay_{{ $q->id }}" 
                                              id="input-essay-{{ $index }}"
                                              rows="6" 
                                              placeholder="Tuliskan jawaban essay Anda di sini..."
                                              oninput="autoSaveEssay({{ $q->id }}, this.value, {{ $index }})"
                                              onblur="simpanJawabanEssay({{ $q->id }}, this.value, {{ $index }})"></textarea>
                                </div>
                            @else
                                <div class="list-group list-group-flush gap-2">
                                    @foreach($q->options as $opt)
                                        <label class="list-group-item rounded-3 border px-3 py-3 shadow-sm list-group-item-action d-flex align-items-center">
                                            <input class="form-check-input me-3 my-0 border-2" type="radio" 
                                                   name="jawaban_{{ $q->id }}" 
                                                   value="{{ $opt->id }}"
                                                   onclick="simpanJawabanPG({{ $q->id }}, {{ $opt->id }}, {{ $index }})">
                                            <span>{{ $opt->option_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between">
                            <button class="btn btn-outline-secondary rounded-pill px-4 {{ $index == 0 ? 'invisible' : '' }}" onclick="pindahSoal({{ $index - 1 }})">
                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                            </button>

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
        const sessionId = "{{ $session->id }}";
        const csrfToken = "{{ csrf_token() }}";
        const totalSoal = {{ $exam->questions->count() }};
        const endTime   = new Date("{{ $session->end_time }}").getTime(); 
        let currentSoal = 0;
        let saveTimeout = null;

        const answeredPG = @json($session->answers->whereNotNull('option_id')->pluck('option_id', 'question_id'));
        const answeredEssay = @json($session->answers->whereNotNull('essay_answer')->pluck('essay_answer', 'question_id'));

        function generateNav() {
            let html = '';
            for (let i = 0; i < totalSoal; i++) {
                html += `<div class="btn-nomor ${i === 0 ? 'active' : ''}" id="nav-btn-${i}" onclick="pindahSoal(${i})">${i + 1}</div>`;
            }
            document.getElementById('nav-desktop').innerHTML = html;
        }
        generateNav();

        window.pindahSoal = function(index) {
            // FORCE SAVE: Sebelum pindah, cek jika ada textarea di soal sekarang
            let activeTextArea = document.querySelector(`#soal-${currentSoal} textarea`);
            if (activeTextArea) {
                let qId = activeTextArea.name.replace('jawaban_essay_', '');
                simpanJawabanEssay(qId, activeTextArea.value, currentSoal);
            }

            document.getElementById(`soal-${currentSoal}`).classList.add('d-none');
            document.getElementById(`nav-btn-${currentSoal}`).classList.remove('active');
            document.getElementById(`soal-${index}`).classList.remove('d-none');
            document.getElementById(`nav-btn-${index}`).classList.add('active');
            
            currentSoal = index;
            window.scrollTo(0, 0);
        }

        // Fungsi Auto-Save saat mengetik (mencegah data hilang jika tidak klik tombol)
        window.autoSaveEssay = function(qId, text, index) {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                simpanJawabanEssay(qId, text, index);
            }, 1000); // Simpan otomatis setelah 1 detik berhenti mengetik
        }

        window.simpanJawabanPG = function(qId, optId, index) {
            sendRequest({ session_id: sessionId, question_id: qId, option_id: optId }, index);
        }

        window.simpanJawabanEssay = function(qId, text, index) {
            if(text.trim() === "") return;
            sendRequest({ session_id: sessionId, question_id: qId, essay_answer: text }, index);
        }

        function sendRequest(data, index) {
            fetch("{{ route('ujian.simpan') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    document.getElementById(`nav-btn-${index}`).classList.add('answered');
                }
            }).catch(err => console.error("Gagal menyimpan:", err));
        }

        document.addEventListener("DOMContentLoaded", () => {
            for (const [qId, optId] of Object.entries(answeredPG)) {
                let radio = document.querySelector(`input[name="jawaban_${qId}"][value="${optId}"]`);
                if(radio) {
                    radio.checked = true;
                    let idx = radio.closest('.soal-container').id.replace('soal-', '');
                    document.getElementById(`nav-btn-${idx}`).classList.add('answered');
                }
            }
            for (const [qId, text] of Object.entries(answeredEssay)) {
                let area = document.querySelector(`textarea[name="jawaban_essay_${qId}"]`);
                if(area) {
                    area.value = text;
                    let idx = area.closest('.soal-container').id.replace('soal-', '');
                    document.getElementById(`nav-btn-${idx}`).classList.add('answered');
                }
            }
        });

        let timer = setInterval(function() {
            let distance = endTime - new Date().getTime();
            if (distance < 0) { clearInterval(timer); submitUjian(); return; }
            let hours = Math.floor(distance / 3600000);
            let minutes = Math.floor((distance % 3600000) / 60000);
            let seconds = Math.floor((distance % 60000) / 1000);
            document.getElementById("countdown").innerHTML = hours + ":" + minutes + ":" + seconds;
        }, 1000);

        window.confirmSelesai = () => {
            // Pastikan jawaban terakhir tersimpan sebelum submit
            let activeTextArea = document.querySelector(`#soal-${currentSoal} textarea`);
            if (activeTextArea) {
                let qId = activeTextArea.name.replace('jawaban_essay_', '');
                simpanJawabanEssay(qId, activeTextArea.value, currentSoal);
            }
            Swal.fire({ title: 'Selesai?', text: "Simpan semua jawaban dan akhiri ujian?", icon: 'warning', showCancelButton: true }).then(r => { if(r.isConfirmed) submitUjian(); });
        }

        function submitUjian() {
            let f = document.createElement('form'); f.method='POST'; f.action="{{ route('ujian.selesai', $exam->id) }}";
            let i = document.createElement('input'); i.type='hidden'; i.name='_token'; i.value=csrfToken;
            f.appendChild(i); document.body.appendChild(f); f.submit();
        }
    </script>
</body>
</html>