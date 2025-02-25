<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['userid']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$ujian_id = $_GET['id'];
$user_id = $_SESSION['userid'];

// Query untuk mengambil data ujian
$query_ujian = "SELECT u.*, k.nama_kelas as kelas 
                FROM ujian u 
                LEFT JOIN kelas k ON u.kelas_id = k.id 
                WHERE u.id = ?";
$stmt_ujian = $koneksi->prepare($query_ujian);
$stmt_ujian->bind_param("i", $ujian_id);
$stmt_ujian->execute();
$result_ujian = $stmt_ujian->get_result();
$data_ujian = $result_ujian->fetch_assoc();

$tanggal_selesai = $data_ujian['tanggal_selesai'];


// Query untuk mengambil data siswa
$userid = $_SESSION['userid'];
$query_siswa = "SELECT s.*,
    k.nama_kelas AS kelas_saat_ini,
    COALESCE(AVG(pg.nilai_akademik), 0) as nilai_akademik,
    COALESCE(AVG(pg.keaktifan), 0) as keaktifan,
    COALESCE(AVG(pg.pemahaman), 0) as pemahaman,
    COALESCE(AVG(pg.kehadiran_ibadah), 0) as kehadiran_ibadah, 
    COALESCE(AVG(pg.kualitas_ibadah), 0) as kualitas_ibadah,
    COALESCE(AVG(pg.pemahaman_agama), 0) as pemahaman_agama,
    COALESCE(AVG(pg.minat_bakat), 0) as minat_bakat,
    COALESCE(AVG(pg.prestasi), 0) as prestasi,
    COALESCE(AVG(pg.keaktifan_ekskul), 0) as keaktifan_ekskul,
    COALESCE(AVG(pg.partisipasi_sosial), 0) as partisipasi_sosial,
    COALESCE(AVG(pg.empati), 0) as empati,
    COALESCE(AVG(pg.kerja_sama), 0) as kerja_sama,
    COALESCE(AVG(pg.kebersihan_diri), 0) as kebersihan_diri,
    COALESCE(AVG(pg.aktivitas_fisik), 0) as aktivitas_fisik,
    COALESCE(AVG(pg.pola_makan), 0) as pola_makan,
    COALESCE(AVG(pg.kejujuran), 0) as kejujuran,
    COALESCE(AVG(pg.tanggung_jawab), 0) as tanggung_jawab,
    COALESCE(AVG(pg.kedisiplinan), 0) as kedisiplinan
    FROM siswa s 
    LEFT JOIN kelas_siswa ks ON s.id = ks.siswa_id 
    LEFT JOIN kelas k ON ks.kelas_id = k.id 
    LEFT JOIN pg ON s.id = pg.siswa_id 
    WHERE s.username = ?
    GROUP BY s.id, k.nama_kelas";

$stmt_siswa = mysqli_prepare($koneksi, $query_siswa);
mysqli_stmt_bind_param($stmt_siswa, "s", $userid);
mysqli_stmt_execute($stmt_siswa);
$result_siswa = mysqli_stmt_get_result($stmt_siswa);
$siswa = mysqli_fetch_assoc($result_siswa);


// Mengambil semua soal untuk ujian tersebut
$query = "SELECT * FROM bank_soal WHERE ujian_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $ujian_id);
$stmt->execute();
$result = $stmt->get_result();
$soal_array = $result->fetch_all(MYSQLI_ASSOC);

// Mengacak urutan soal
shuffle($soal_array);

// Menyimpan urutan soal yang teracak dalam session
$_SESSION['soal_order_' . $ujian_id] = array_column($soal_array, 'id');
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
            0% {
                background: red;
            }

            50% {
                background: white;
            }

            100% {
                background: red;
            }
        }

        .warning-active {
            display: none;
            /* Hide by default */
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

        // Declare the variable at the very top, before any function definitions or event listeners
        let warningAudio = null;
        let warningDiv = document.createElement('div');
        warningDiv.className = 'warning-active';
        document.body.appendChild(warningDiv);

        // Initialize when document loads
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('startExamModal'));
            modal.show();

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

        function handleFullscreenChange() {
            if (!document.fullscreenElement &&
                !document.webkitFullscreenElement &&
                !document.mozFullScreenElement &&
                !document.msFullscreenElement) {

                warningDiv.style.display = 'flex';

                // Create new warning audio
                if (warningAudio) {
                    warningAudio.pause();
                    warningAudio.currentTime = 0;
                }
                warningAudio = new Audio('assets/warning.mp3');
                warningAudio.loop = true;
                warningAudio.volume = 1.0;
                warningAudio.play();

                // Simple minimalist warning modal
                document.querySelector('#startExamModal .modal-header').innerHTML =
                    '<h5 class="modal-title fw-bold">' +
                    '<i class="bi bi-shield-lock me-2" style="color: #da7756;"></i>' +
                    'EduGuard</h5>';

                document.querySelector('#startExamModal .modal-body').innerHTML =
                    '<div class="text-center py-3">' +
                    '<span class="bi bi-eye-fill" style="font-size: 3rem; color:#da7756;"></span>' +
                    '<h4 class="mt-3">PERINGATAN!</h4>' +
                    '<p class="mb-3">Keluar dari layar penuh terdeteksi, sesi ujian terkunci</p>' +
                    '<p class="text-muted" style="font-size:12px">Untuk alasan keamanan ujian, jika kamu memuat ulang atau kembali menuju halaman awal ujian maka kami akan menghapus seluruh jawaban ujianmu, panggil pengawasmu untuk mengisi password di bawah untuk melanjutkan ujian</p>' +
                    '<input type="password" class="form-control" id="supervisorPassword" placeholder="Password">' +
                    '</div>';

                document.querySelector('#startExamModal .modal-footer').innerHTML =
                    '<div class="d-flex w-100">' +
                    '<button class="btn color-web text-white flex-fill mx-3" onclick="checkPassword()">Lanjutkan</button>' +
                    '</div>';

                const modal = new bootstrap.Modal(document.getElementById('startExamModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();

            } else {
                // Remove warning when back in fullscreen
                warningDiv.style.display = 'none';
                if (warningAudio) {
                    warningAudio.pause();
                    warningAudio.currentTime = 0;
                    warningAudio = null;
                }
            }
        }

        function checkPassword() {
            const password = document.getElementById('supervisorPassword').value;
            if (password === 'admin') {
                // Stop the warning sound
                if (warningAudio) {
                    warningAudio.pause();
                    warningAudio.currentTime = 0;
                    warningAudio = null;
                }
                enableFullscreen();
                const modal = bootstrap.Modal.getInstance(document.getElementById('startExamModal'));
                modal.hide();
            } else {
                alert('Password salah, panggil pengawas untuk membuka kunci');
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

        .soal-numbers .soal-number[data-status="answered"] {
            background-color: #da7756 !important;
            color: white !important;
        }

        .soal-numbers .soal-number[data-status="marked"] {
            background-color: #dc3545 !important;
            color: white !important;
        }

        .soal-number {
    transition: background-color 0.3s ease, color 0.3s ease;
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
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
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
                    <!-- Info Siswa -->
                    <div class="card mb-3 border" style="border-radius: 16px; background: rgba(255,255,255,0.95);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div>
                                    <img src="<?php echo !empty($siswa['photo_url']) ?
                                                    ($siswa['photo_type'] === 'avatar' ? $siswa['photo_url'] : ($siswa['photo_type'] === 'upload' ? $siswa['photo_url'] : 'assets/pp.png'))
                                                    : 'assets/pp.png'; ?>"
                                        class="rounded-circle border"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold" style="color: #1c1c1e;"><?php echo $_SESSION['nama']; ?></h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge" style="background: rgba(218, 119, 86, 0.1); color: rgb(218, 119, 86); font-weight: normal; padding: 5px 10px; border-radius: 12px;">
                                            <?php echo $data_ujian['judul']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Countdown Timer -->
                            <div class="p-3 rounded-4" style="background: #f2f2f7;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-clock" style="color:rgb(218, 119, 86);"></i>
                                    <div class="d-flex align-items-center gap-2">
                                        <small class="text-secondary">Sisa Waktu:</small>
                                        <span id="countdown" style="font-weight: 600; color: rgb(218, 119, 86); font-size: 15px;"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- script untuk siswa waktu  -->
                            <script>
                                // Ambil waktu selesai dari PHP
                                const endTime = new Date('<?php echo $tanggal_selesai; ?>').getTime();

                                // Update countdown setiap detik
                                const timer = setInterval(function() {
                                    // Waktu sekarang
                                    const now = new Date().getTime();

                                    // Selisih waktu
                                    const distance = endTime - now;

                                    // Hitung waktu untuk jam, menit dan detik
                                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                    // Format tampilan countdown
                                    const countdownDisplay = document.getElementById('countdown');
                                    countdownDisplay.innerHTML = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

                                    // Jika waktu habis
                                    if (distance < 0) {
                                        clearInterval(timer);
                                        countdownDisplay.innerHTML = "00:00:00";

                                        // Tampilkan modal waktu habis
                                        const timeoutModal = new bootstrap.Modal(document.getElementById('timeoutModal'));
                                        timeoutModal.show();

                                        // Submit jawaban otomatis
                                        const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
                                        const answers = JSON.parse(localStorage.getItem(storageKey) || '{}');

                                        $.post('submit_ujian.php', {
                                            ujian_id: <?php echo $ujian_id; ?>,
                                            answers: JSON.stringify(answers)
                                        }, function(response) {
                                            console.log('Response dari submit:', response);
                                            setTimeout(() => {
                                                window.location.href = 'ujian.php';
                                            }, 3000);
                                        });
                                    }
                                }, 1000);

                                // Styling untuk countdown yang mendekati habis
                                function updateCountdownStyle(minutes) {
                                    const countdownElement = document.getElementById('countdown');
                                    if (minutes <= 5) { // Jika sisa 5 menit atau kurang
                                        countdownElement.style.color = '#dc3545'; // Warna merah
                                        countdownElement.style.fontWeight = 'bold';

                                        if (minutes <= 2) { // Jika sisa 2 menit atau kurang
                                            // Tambahkan animasi berkedip
                                            countdownElement.style.animation = 'blink 1s infinite';
                                        }
                                    }
                                }

                                // Tambahkan CSS untuk animasi berkedip
                                const style = document.createElement('style');
                                style.textContent = `
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
`;
                                document.head.appendChild(style);
                            </script>
                        </div>
                    </div>

                    <!-- Daftar Soal -->
                    <div class="card border" style="border-radius: 16px; background: rgba(255,255,255,0.95);">
                        <div class="card-body p-4">
                            <h6 class="card-title mb-4" style="color: #1c1c1e; font-weight: 600;">Daftar Soal</h6>
                            <div class="d-flex flex-wrap gap-2 justify-content-start">
                                <?php foreach ($soal_array as $index => $soal): ?>
                                    <div class="soal-number rounded-3 border-0 d-flex align-items-center justify-content-center"
                                        data-soal="<?php echo $index; ?>"
                                        data-status="unanswered"
                                        style="background: #f2f2f7;color:black; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                        <?php echo $index + 1; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Keterangan Status -->
                            <div class="mt-4 p-3 rounded-4" style="background: #f2f2f7;">
                                <div class="d-flex gap-3 align-items-center mb-2">
                                    <div class="soal-number rounded-3"
                                        style="width:24px; height:24px; background:#f2f2f7; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);"></div>
                                    <small style="color: #3c3c43;">Belum dijawab</small>
                                </div>
                                <div class="d-flex gap-3 align-items-center mb-2">
                                    <div class="soal-number rounded-3"
                                        style="width:24px; height:24px; background:#da7756; box-shadow: 0 1px 2px rgba(218,119,86,0.3);"></div>
                                    <small style="color: #3c3c43;">Sudah dijawab</small>
                                </div>
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="soal-number rounded-3"
                                        style="width:24px; height:24px; background:#dc3545; box-shadow: 0 1px 2px rgba(220,53,69,0.3);"></div>
                                    <small style="color: #3c3c43;">Ditandai</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-9">
                <div class="soal-content">
                    <form id="exam-form">
                        <?php foreach ($soal_array as $index => $soal): ?>
                            <div class="soal-page <?php echo $index === 0 ? '' : 'd-none'; ?>"
                                data-index="<?php echo $index; ?>">
                                <h5 class="mb-4">Soal <?php echo $index + 1; ?></h5>

                                <?php if (!empty($soal['gambar_soal'])): ?>
                                    <div class="mb-3">
                                        <img src="<?php echo htmlspecialchars($soal['gambar_soal']); ?>"
                                            alt="Gambar soal <?php echo $index + 1; ?>"
                                            class="img-fluid rounded shadow-sm text-start"
                                            style="max-height: 300px; width: auto; display: block;">
                                    </div>
                                <?php endif; ?>
                                <p class="mb-4"><?php echo $soal['pertanyaan']; ?></p>

                                <?php
                                $options = [
                                    'a' => $soal['jawaban_a'],
                                    'b' => $soal['jawaban_b'],
                                    'c' => $soal['jawaban_c'],
                                    'd' => $soal['jawaban_d']
                                ];
                                foreach ($options as $key => $value):
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
                <div class="bottom-navigation d-flex justify-content-between bg-white p-3" style="box-shadow: 0 -1px 0 rgba(0,0,0,0.1);">
                    <button class="btn" id="prev" style="border: none; background: none;">
                        <i class="bi bi-chevron-left" style="font-size: 24px; color: #007AFF;"></i>
                    </button>
                    <div class="d-flex gap-3">
                        <button class="btn d-flex flex-column align-items-center" id="mark" style="border: none; background: none;">
                            <i class="bi bi-bookmark" style="font-size: 24px; color: #FF3B30;"></i>
                            <span class="d-none d-md-block" style="font-size: 12px; color: #8E8E93;">Tandai</span>
                        </button>
                        <button class="btn d-flex flex-column align-items-center" id="clear" style="border: none; background: none;">
                            <i class="bi bi-x-circle" style="font-size: 24px; color: #8E8E93;"></i>
                            <span class="d-none d-md-block" style="font-size: 12px; color: #8E8E93;">Hapus</span>
                        </button>
                        <button class="btn d-flex flex-column align-items-center" id="finish" style="border: none; background: none;">
                            <i class="bi bi-flag" style="font-size: 24px; color: #34C759;"></i>
                            <span class="d-none d-md-block" style="font-size: 12px; color: #8E8E93;">Selesai</span>
                        </button>
                    </div>
                    <button class="btn" id="next" style="border: none; background: none;">
                        <i class="bi bi-chevron-right" style="font-size: 24px; color: #007AFF;"></i>
                    </button>
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
                        EduGuard
                    </h5>
                </div>
                <div class="modal-body px-4 py-4 text-center">
                    <i class="bi bi-question-circle mb-3" style="font-size: 3rem; color:#da7756"></i>
                    <h5 class="mb-3">Apakah Anda yakin ingin menyelesaikan ujian?</h5>
                    <p class="text-muted small mb-0">
                        Setelah ujian diselesaikan, kamu tidak dapat mengubah jawaban lagi
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Aku cek jawaban dulu
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
            if (markedQuestions.has(currentSoal)) {
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
            if (currentSoal > 0) {
                showSoal(currentSoal - 1);
            }
        });

        $('#next').click(() => {
            if (currentSoal < totalSoal - 1) {
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
                    if (result.success) {
                        $('#finishModal').modal('hide');
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                        setTimeout(() => window.location.href = 'ujian.php', 2000);
                    } else {
                        alert('Terjadi kesalahan: ' + (result.error || 'Undefined error'));
                    }
                } catch (e) {
                    alert('Terjadi kesalahan parsing');
                }
            }).fail(() => alert('Terjadi kesalahan koneksi'));
        });
    </script>


    <!-- Tambahkan ini sebelum penutup </body> -->
    <div class="modal fade" id="startExamModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-body text-center p-4">
                    <img src="<?php
                                if (!empty($siswa['photo_url'])) {
                                    // Jika menggunakan avatar dari DiceBear
                                    if ($siswa['photo_type'] === 'avatar') {
                                        echo $siswa['photo_url'];
                                    }
                                    // Jika menggunakan foto upload
                                    else if ($siswa['photo_type'] === 'upload') {
                                        echo $siswa['photo_url'];
                                    }
                                } else {
                                    // Gambar default
                                    echo 'assets/pp.png';
                                }
                                ?>" width="120px" class="rounded-circle border mb-3">
                    <h5 class="fw-bold">Halo, <?php echo $_SESSION['nama']; ?></h5>
                    <p class="fw-bold mb-4">Sudah siap untuk ujian kali ini?</p>

                    <div class="text-start">
                        <p class="mb-2 fw-bold"><i class="bi bi-info-circle me-2" style="color:rgb(218, 119, 86);"></i>Peraturan Ujian:</p>
                        <ul class="small text-secondary mb-4">
                            <li>Patuhi seluruh peraturan ujian sesuai dengan ketentuan pengawas</li>
                            <li>Dilarang keluar dari mode layar penuh</li>
                            <li>Ujian dimulai setelah menekan tombol mulai</li>
                            <li>Jika kamu menyegarkan halaman ujian atau kembali ke halaman ujian, kami akan menghapus seluruh jawaban ujianmu</li>
                            <li>Jangan lupa berdoa sebelum memulai</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn text-white px-4 flex-fill" data-bs-dismiss="modal"
                            style="border-radius: 12px; background-color: rgb(206, 100, 65);">
                            Kembali
                            <button type="button" class="btn text-white px-4 flex-fill" id="startFullscreenExam"
                                style="border-radius: 12px; background-color: rgb(218, 119, 86);">
                                Mulai Sekarang <i class="bi bi-arrow-right-circle ms-2"></i>
                            </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal waktu habis -->
    <div class="modal fade" id="timeoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-clock" style="font-size: 3rem; color:rgb(218, 119, 86);"></i>
                    <h5 class="mt-3 fw-bold">Ups, Waktu Ujian Telah Berakhir</h5>
                    <p class="mb-4">Jawaban Anda akan dikumpulkan secara otomatis, terima kasih telah mengikuti ujian</p>
                    <div class="d-flex gap-2 btn-group">
                        <button type="button" class="btn text-white flex-fill px-4" id="confirmTimeout" style="border-radius: 12px; background-color: rgb(218, 119, 86);">
                            Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- script untuk fungsi batas waktu ujian -->

    <script>
        const endTime = new Date('<?php echo $tanggal_selesai; ?>').getTime();
        console.log('Waktu selesai:', new Date(endTime));

        function checkTimeout() {
            const now = new Date().getTime();
            console.log('Waktu sekarang:', new Date(now));
            console.log('Sisa waktu:', Math.floor((endTime - now) / 1000), 'detik');

            if (now >= endTime) {
                console.log('Waktu habis!');
                const timeoutModal = new bootstrap.Modal(document.getElementById('timeoutModal'));
                timeoutModal.show();
                clearInterval(timeoutChecker);
            }
        }

        // Debug setiap 5 detik (lebih lama untuk memudahkan debugging)
        const timeoutChecker = setInterval(checkTimeout, 5000);

        // Debug untuk tombol Ok
        document.getElementById('confirmTimeout').addEventListener('click', function() {
            console.log('Tombol Ok diklik');

            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            const answers = JSON.parse(localStorage.getItem(storageKey) || '{}');
            console.log('Jawaban yang akan disubmit:', answers);

            $.post('submit_ujian.php', {
                ujian_id: <?php echo $ujian_id; ?>,
                answers: JSON.stringify(answers)
            }, function(response) {
                console.log('Response dari submit:', response);
                window.location.href = 'ujian.php';
            }).fail(function(error) {
                console.error('Error saat submit:', error);
                alert('Terjadi kesalahan saat submit jawaban');
            });
        });
    </script>

    <!-- waktu untuk menghapus jawaban siswa jika refresh atau kembali -->
    <script>
        // Tambahkan event handler untuk window beforeunload dan unload
        window.addEventListener('beforeunload', function(e) {
            // Hapus data dari localStorage
            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            localStorage.removeItem(storageKey);

            // Kirim request untuk menghapus jawaban dari database
            navigator.sendBeacon('delete_jawaban.php', JSON.stringify({
                ujian_id: <?php echo $ujian_id; ?>,
                siswa_id: <?php echo $_SESSION['userid']; ?>
            }));

            e.preventDefault();
            e.returnValue = 'Dilarang menutup tab ujian!';
        });

        // Handler saat halaman akan di-refresh atau ditutup
        window.addEventListener('unload', function() {
            // Hapus data dari localStorage
            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            localStorage.removeItem(storageKey);
        });

        // Function untuk menghapus data saat timeout atau selesai ujian
        function cleanupData() {
            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            localStorage.removeItem(storageKey);

            $.post('delete_jawaban.php', {
                ujian_id: <?php echo $ujian_id; ?>,
                siswa_id: <?php echo $_SESSION['userid']; ?>
            });
        }

        // Update confirmTimeout handler
        document.getElementById('confirmTimeout').addEventListener('click', function() {
            console.log('Tombol Ok diklik');

            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            const answers = JSON.parse(localStorage.getItem(storageKey) || '{}');

            $.post('submit_ujian.php', {
                ujian_id: <?php echo $ujian_id; ?>,
                answers: JSON.stringify(answers)
            }, function(response) {
                console.log('Response dari submit:', response);
                cleanupData(); // Hapus data setelah submit berhasil
                window.location.href = 'ujian.php';
            }).fail(function(error) {
                console.error('Error saat submit:', error);
                alert('Terjadi kesalahan saat submit jawaban');
            });
        });
    </script>

</body>

</html>