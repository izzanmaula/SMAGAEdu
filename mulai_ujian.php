<?php
// mulai_ujian.php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$ujian_id = $_GET['id'];
$query = "SELECT * FROM bank_soal WHERE ujian_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $ujian_id);
$stmt->execute();
$result = $stmt->get_result();
$soal_array = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    @keyframes warning-background {
        0% { background: red; }
        50% { background: white; }
        100% { background: red; }
    }
    .warning-active {
        display: none;  /* Hide by default */
        animation: warning-background 0.5s infinite;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        opacity: 0.7;
    }
    </style>
    <script>
        // Initialize when document loads
document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('startExamModal'));
    modal.show();

    const warningDiv = document.getElementById('warningOverlay');
    
    document.getElementById('startFullscreenExam').addEventListener('click', () => {
        enableFullscreen();
        setTimeout(() => modal.hide(), 500);
    });

    // Add fullscreen change listeners
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenChange);
});
// Define the enableFullscreen function

window.onbeforeunload = function(e) {
    e.preventDefault();
    e.returnValue = '';
    return 'Dilarang menutup tab ujian!';
};

let warningDiv = document.createElement('div');
warningDiv.className = 'warning-active';
document.body.appendChild(warningDiv);

function handleFullscreenChange() {
    if (!document.fullscreenElement && 
        !document.webkitFullscreenElement && 
        !document.mozFullScreenElement && 
        !document.msFullscreenElement) {

        warningDiv.style.display = 'flex';
            
            
        // Play warning sound
        const audio = new Audio('assets/warning.mp3');
        audio.loop = true;
        audio.volume = 1.0;
        audio.play();
            
        // Update modal
        document.querySelector('#startExamModal .modal-footer').style.display = 'none';
        document.querySelector('#startExamModal .modal-header').innerHTML = 
            '<h5 class="modal-title text-danger">PERINGATAN KERAS!</h5>';
        document.querySelector('#startExamModal .modal-body').innerHTML = 
            '<p class="text-danger text-center"><b>ANDA TELAH KELUAR DARI MODE UJIAN!</b><br>' +
            'PELANGGARAN INI TELAH DICATAT<br>SEGERA PANGGIL PENGAWAS ANDA</p>';
        
        const modal = new bootstrap.Modal(document.getElementById('startExamModal'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    } else {
        // Remove warning when back in fullscreen
        warningDiv.style.display = 'none';
        const audio = document.querySelector('audio');
        if (audio) audio.pause();
    }
}

// Update your existing enableFullscreen function
function enableFullscreen() {
    const element = document.documentElement;
    if (element.requestFullscreen) {
        element.requestFullscreen();
    } else if (element.webkitRequestFullscreen) {
        element.webkitRequestFullscreen();
    } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
    } else if (element.msRequestFullscreen) {
        element.msRequestFullscreen();
    }
    
  // Remove warning when enabling fullscreen
  warningDiv.classList.add('d-none');
    const audio = document.querySelector('audio');
    if (audio) audio.pause();
}

// Block keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Block Esc key
    if (e.key === 'Escape') {
        e.preventDefault();
        return false;
    }
    
    // Block combinations with Ctrl, Alt, Windows key
    if (e.ctrlKey || e.altKey || e.metaKey) {
        e.preventDefault();
        return false;
    }

    // Block F1-F12 keys
    if (e.key.match(/F\d+/)) {
        e.preventDefault(); 
        return false;
    }
});

</script>
    <style>
        body {
            overflow-y: auto !important;
            background-color: #f8f9fa;
            font-family: merriweather;
        }
        .soal-numbers {
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 10px;
        }
        .soal-number {
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }
        .soal-number:hover {
            transform: scale(1.1);
        }
        .soal-content {
            height: calc(90vh - 70px);
            overflow-y: auto;
            padding: 15px;
        }
        .option-card {
            cursor: pointer;
            transition: all 0.2s;
            padding: 15px !important;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .option-card:hover {
            background-color: #e9ecef;
        }
        .option-card.selected {
            background-color: #da7756;
            color: white;
        }
        .color-web {
            background-color: rgb(218, 119, 86);
            transition: background-color 0.3s ease;
        }
        .color-web:hover {
            background-color: rgb(206, 100, 65);
        }
        .soal-number[data-status="answered"] {
            background-color: #da7756;
            color: white;
        }
        .soal-number[data-status="marked"] {
            background-color: #dc3545;
            color: white;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .soal-numbers {
                height: auto;
                max-height: 200px;
                margin-bottom: 1rem;
            }

            .soal-content {
                height: auto;
                margin-bottom: 100px;
                padding: 10px;
            }

            .bottom-navigation {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                padding: 15px;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
                margin-left: 0 !important;
                width: 100%;
                z-index: 1000;
            }

            .bottom-navigation button {
                padding: 12px;
                min-width: 44px;
                font-size: 20px;
            }

            .bottom-navigation button p {
                display: none;
            }

            .option-card {
                padding: 20px !important;
            }

            .navbar {
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            .col-md-3 {
                padding: 0;
            }
        }
    </style>
</head>
<body id="examBody">
    <div id="warningOverlay" class="warning-active"></div>
    <nav class="navbar color-web">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-2">
                <img src="assets/smagaedu.png" alt="" width="30px" class="logo_orange">
                <h1 class="display-5 p-0 m-0 text-white" style="font-size: 20px;">SMAGAEdu</h1>
            </div>
        </div>
    </nav>

    <div class="d-md-none p-2">
        <button class="btn text-white w-100" style="background-color: rgb(255, 141, 103);" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
            Daftar Soal <i class="bi bi-chevron-down"></i>
        </button>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 collapse d-md-block" id="mobileNav">
                <div class="soal-numbers">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <?php foreach($soal_array as $index => $soal): ?>
                            <div class="soal-number rounded border d-flex align-items-center justify-content-center" 
                                data-soal="<?php echo $index; ?>"
                                data-status="unanswered">
                                <?php echo $index + 1; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="soal-content">
                    <form id="exam-form">
                        <?php foreach($soal_array as $index => $soal): ?>
                            <div class="soal-page <?php echo $index === 0 ? '' : 'd-none'; ?>" 
                                 data-index="<?php echo $index; ?>">
                                <h5 class="mb-4">Soal <?php echo $index + 1; ?></h5>
                                <p class="mb-4"><?php echo $soal['pertanyaan']; ?></p>
                                
                                <?php
                                $options = ['a' => $soal['jawaban_a'], 
                                          'b' => $soal['jawaban_b'], 
                                          'c' => $soal['jawaban_c'], 
                                          'd' => $soal['jawaban_d']];
                                foreach($options as $key => $value):
                                ?>
                                    <div class="option-card p-3 rounded border mb-3" 
                                         data-value="<?php echo $key; ?>">
                                        <?php echo strtoupper($key) . ". " . $value; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </form>
                </div>
                <div class="bottom-navigation d-flex justify-content-between bg-white">
                    <button class="btn color-web text-white bi-chevron-left" id="prev"></button>
                    <div class="d-flex gap-2">
                        <button class="btn btn-danger text-white bi-bookmark-star-fill" id="mark">
                            <p class="d-none d-md-block p-0 m-0 mt-1">Tandai</p>
                        </button>
                        <button class="btn btn-secondary text-white bi-border" id="clear">
                            <p class="d-none d-md-block p-0 m-0 mt-1">Hapus</p>
                        </button>
                        <button class="btn btn-success text-white bi-flag-fill" id="finish">
                            <p class="d-none d-md-block p-0 m-0 mt-1">Selesai</p>
                        </button>
                    </div>
                    <button class="btn color-web text-white bi-chevron-right" id="next"></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="finishModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 text-white" style="background-color: #da7756;">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Keamanan Ujian
                    </h5>
                </div>
                <div class="modal-body px-4 py-4 text-center">
                    <i class="bi bi-question-circle mb-3" style="font-size: 3rem; color:#da7756"></i>
                    <h5 class="mb-3">Apakah Anda yakin ingin menyelesaikan ujian?</h5>
                    <p class="text-muted small mb-0">
                        Setelah ujian diselesaikan, Anda tidak dapat mengubah jawaban lagi
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Saya cek jawaban dulu
                    </button>
                    <button type="button" style="background-color: #da7756;" class="btn text-white px-4" id="confirmFinish">
                        <i class="bi bi-check-circle me-2"></i>
                        Ok, kumpulkan
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 text-white" style="background-color:#da7756;">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>
                    Keamanan Ujian
                </h5>
            </div>
            <div class="modal-body px-4 py-5 text-center">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill" style="font-size: 5rem; color:#da7756"></i>
                </div>
                <h4 class="mb-3">Ujian Berhasil Diselesaikan</h4>
                <p class="mb-4">
                    Terima kasih telah mengerjakan ujian dengan baik.<br>
                    Kamu bisa klik 'leave' atau atau 'tinggalkan' pada peringatan di atas
                </p>
                <div class="spinner-border" role="status" style="color: #da7756;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentSoal = 0;
        const totalSoal = <?php echo count($soal_array); ?>;
        const answers = new Map();
        const markedQuestions = new Set();

        function showSoal(index) {
            $('.soal-page').addClass('d-none');
            $(`.soal-page[data-index="${index}"]`).removeClass('d-none');
            currentSoal = index;
        }

        $('.soal-number').click(function() {
            const index = $(this).data('soal');
            showSoal(index);
        });

        $('.option-card').click(function() {
            const soalIndex = $(this).closest('.soal-page').data('index');
            const jawaban = $(this).data('value');
            
            $(this).closest('.soal-page').find('.option-card').removeClass('selected');
            $(this).addClass('selected');
            
            answers.set(soalIndex, jawaban);
            $(`.soal-number[data-soal="${soalIndex}"]`).attr('data-status', 'answered');

            $.post('save_jawaban.php', {
                ujian_id: <?php echo $ujian_id; ?>,
                soal_index: soalIndex,
                jawaban: jawaban
            });
        });

        $('#mark').click(() => {
            if(markedQuestions.has(currentSoal)) {
                markedQuestions.delete(currentSoal);
                $(`.soal-number[data-soal="${currentSoal}"]`).attr('data-status', 
                    answers.has(currentSoal) ? 'answered' : 'unanswered'
                );
            } else {
                markedQuestions.add(currentSoal);
                $(`.soal-number[data-soal="${currentSoal}"]`).attr('data-status', 'marked');
            }
        });

        $('#prev').click(() => {
            if(currentSoal > 0) {
                showSoal(currentSoal - 1);
            }
        });

        $('#next').click(() => {
            if(currentSoal < totalSoal - 1) {
                showSoal(currentSoal + 1);
            }
        });

        // Simpan jawaban di localStorage
        function saveAnswer(soalIndex, jawaban) {
            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            let answers = JSON.parse(localStorage.getItem(storageKey) || '{}');
            answers[soalIndex] = jawaban;
            localStorage.setItem(storageKey, JSON.stringify(answers));
        }

        // Load jawaban saat halaman dimuat
        function loadAnswers() {
            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            const answers = JSON.parse(localStorage.getItem(storageKey) || '{}');
            
            Object.entries(answers).forEach(([index, jawaban]) => {
                $(`.soal-page[data-index="${index}"] .option-card[data-value="${jawaban}"]`).addClass('selected');
                $(`.soal-number[data-soal="${index}"]`).attr('data-status', 'answered');
            });
        }

        // Update click handler
        $('.option-card').click(function() {
            const soalIndex = $(this).closest('.soal-page').data('index');
            const jawaban = $(this).data('value');
            
            $(this).closest('.soal-page').find('.option-card').removeClass('selected');
            $(this).addClass('selected');
            
            saveAnswer(soalIndex, jawaban);
            $(`.soal-number[data-soal="${soalIndex}"]`).attr('data-status', 'answered');
        });

        // Panggil loadAnswers saat halaman dimuat
        $(document).ready(loadAnswers);

        // Fungsi hapus jawaban
        $('#clear').click(() => {
            const soalIndex = currentSoal;
            
            // Hapus visual selection
            $(`.soal-page[data-index="${soalIndex}"] .option-card`).removeClass('selected');
            
            // Hapus status jawaban
            $(`.soal-number[data-soal="${soalIndex}"]`).attr('data-status', 'unanswered');
            
            // Hapus dari localStorage
            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            let answers = JSON.parse(localStorage.getItem(storageKey) || '{}');
            delete answers[soalIndex];
            localStorage.setItem(storageKey, JSON.stringify(answers));
            
            // Hapus dari database
            $.post('save_jawaban.php', {
                ujian_id: <?php echo $ujian_id; ?>,
                soal_index: soalIndex,
                jawaban: null
            });
        });

        $('#finish').click(() => {
    const finishModal = new bootstrap.Modal(document.getElementById('finishModal'));
    finishModal.show();
});


$('#confirmFinish').click(() => {
    const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
    const answers = JSON.parse(localStorage.getItem(storageKey) || '{}');
    
    $.post('submit_ujian.php', {
        ujian_id: <?php echo $ujian_id; ?>,
        answers: JSON.stringify(answers)
    }, function(response) {
        try {
            const result = JSON.parse(response);
            if(result.success) {
                $('#finishModal').modal('hide');
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                setTimeout(() => window.location.href = 'ujian.php', 2000);
            } else {
                alert('Terjadi kesalahan: ' + (result.error || 'Undefined error'));
            }
        } catch(e) {
            alert('Terjadi kesalahan parsing');
        }
    }).fail(() => alert('Terjadi kesalahan koneksi'));
});

</script>


<!-- Tambahkan ini sebelum penutup </body> -->
<div class="modal fade" id="startExamModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shield-lock me-2" style="color: #da7756;"></i>
                    Mode Keamanan Ujian
                </h5>
            </div>
            <div class="modal-body px-4">
                <div class="">
                    <p class="mb-2 fw-bold text-dark">Penting:</p>
                    <ul class="mb-0 small" style="color: black;">
                        <li>Ujian akan berjalan dalam mode layar penuh</li>
                        <li>Dilarang keras keluar dari mode layar penuh</li>
                        <li>Ujian akan dimulai saat kamu menekan tombol mulai ujian di bawah</li>
                        <li>Jangan lupa berdoa, dan semoga berhasil</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn color-web text-white px-4" id="startFullscreenExam">
                    Mulai Ujian <i class="bi bi-arrow-right-circle ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

</body>
</html>