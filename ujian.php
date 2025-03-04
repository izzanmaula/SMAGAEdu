<?php
session_start();
require "koneksi.php";


if (!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

// Ambil userid dari session
$userid = $_SESSION['userid'];

// Get exams for classes the student is enrolled in
$query = "SELECT u.*, k.mata_pelajaran, k.tingkat, k.background_image,
          (SELECT COUNT(*) FROM jawaban_ujian ju 
           WHERE ju.ujian_id = u.id AND ju.siswa_id = s.id) as sudah_ujian
          FROM ujian u
          JOIN kelas k ON u.kelas_id = k.id 
          JOIN kelas_siswa ks ON k.id = ks.kelas_id
          JOIN siswa s ON ks.siswa_id = s.id
          WHERE s.username = '$userid'
          ORDER BY u.tanggal_mulai ASC";

$result = mysqli_query($koneksi, $query);




// function untuk waktu ujian
function getExamStatus($startTime, $endTime)
{
    date_default_timezone_set('Asia/Jakarta');
    $now = time();
    $start = strtotime($startTime);
    $end = strtotime($endTime);

    if ($now < $start) {
        $diffSeconds = $start - $now;
        $hours = floor($diffSeconds / 3600);
        $minutes = floor(($diffSeconds % 3600) / 60);
        return [
            'status' => 'waiting',
            'countdown' => "$hours jam $minutes menit"
        ];
    } elseif ($now >= $start && $now <= $end) {
        return [
            'status' => 'ongoing',
            'countdown' => ''
        ];
    } else {
        return [
            'status' => 'ended',
            'countdown' => ''
        ];
    }
}

$query_siswa = "SELECT s.*, 
    k.nama_kelas AS kelas_saat_ini 
    FROM siswa s 
    LEFT JOIN kelas_siswa ks ON s.id = ks.siswa_id 
    LEFT JOIN kelas k ON ks.kelas_id = k.id 
    WHERE s.username = ?";

$stmt_siswa = mysqli_prepare($koneksi, $query_siswa);
mysqli_stmt_bind_param($stmt_siswa, "s", $userid);
mysqli_stmt_execute($stmt_siswa);
$result_siswa = mysqli_stmt_get_result($stmt_siswa);
$siswa = mysqli_fetch_assoc($result_siswa);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <title>Beranda - SMAGAEdu</title>
</head>
<style>
    body {
        font-family: merriweather;
    }

    .color-web {
        background-color: rgb(218, 119, 86);
    }

    /* modal */
    .modal-content {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .modal .btn {
        font-weight: 500;
        transition: all 0.2s;
    }

    .modal .btn:active {
        transform: scale(0.98);
    }

    .modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: transform 0.2s ease-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }
</style>

<style>
    .col-utama {
        padding-top: 0.7rem;
        padding-left: 14rem !important;
        animation: fadeInUp 0.5s;
        opacity: 1;

    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .col-utama {
            padding-left: 0rem !important;
        }

        .judul {
            display: none;
        }

        .salam {
            display: block;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
    }
</style>


<body>

    <?php include 'includes/styles.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar for desktop -->
            <?php include 'includes/sidebar_siswa.php'; ?>

            <!-- Mobile navigation -->
            <?php include 'includes/mobile_nav siswa.php'; ?>

            <!-- Settings Modal -->
            <?php include 'includes/settings_modal.php'; ?>


        </div>
    </div>


    <!-- ini isi kontennya -->
    <!-- Isi konten -->
    <div class="col col-utama mt-1 p-md-0 mt-md-0">
        <div class="p-md-3 pb-md-2 d-flex ms-3 mt-md-0 ms-md-0 p-3 mb-1 salam justify-content-between align-items-center">
            <div class="mt-1">
                <h3 class="fw-bold mb-0">Ujian</h3>
            </div>
            <div class="d-flex d-none d-md-block">
                <button type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#modal_jadwal_ujian"
                    class="btn btn-light border d-flex align-items-center gap-2 px-3" style="border-radius: 15px;">
                    <i class="bi bi-calendar-date"></i>
                    <span class="d-none d-md-inline" style="font-size: 12px;">Jadwal Ujian</span>
                </button>
            </div>
        </div>


        <!-- Modal Jadwal Ujian -->
        <div class="modal fade" id="modal_jadwal_ujian" tabindex="-1" aria-labelledby="jadwalUjianModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="border-radius: 16px;">
                    <div class="modal-body p-4">
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 fw-bold">Tidak Ada Data</h5>
                            <p class="text-muted">Saat ini belum ada jadwal ujian yang tersedia</p>
                        </div>
                    </div>
                    <div class="modal-footer d-flex">
                        <button type="button" class="btn flex-fill" data-bs-dismiss="modal" style="background-color: rgb(218, 119, 86); color: white; border-radius: 12px;">Tutup</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Jumbotron yang akan berubah berdasarkan tab yang aktif -->
        <div class="jumbotron jumbotron-fluid mb-md-2 d-none d-md-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <!-- Konten untuk tab Diikuti (khusus) -->
                        <div id="jumbotron-khusus" class="jumbotron-content active">
                            <h2 class="display-5">
                                Aggap Ujian ini Game, dan <span style="color: rgb(198, 99, 66);">Kamu Jagoannya!</span>
                            </h2>
                            <p class="lead">Pilih ujian di bawah sesuai dengan jadwal ujian kamu, tetap sportif dan jangan lupa berdoa.</p>
                        </div>
                    </div>

                    <div class="col-md-6 text-center d-none d-md-block">
                        <!-- Gambar untuk tab Diikuti (khusus) -->
                        <img src="assets/ujian_siswa.png" class="img-fluid jumbotron-image" id="jumbotron-image" alt="Ilustrasi kelas" style="max-height: 20rem;">
                    </div>
                </div>
            </div>
        </div>


        <style>
            .class-card {
                border-radius: 12px;
                overflow: hidden;
                background: white;
                opacity: 0;
                transform: translateY(20px);
                animation: fadeInUp 0.5s ease forwards;
                will-change: transform;
            }

            .class-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .class-banner {
                height: 160px;
                background-size: cover;
                background-position: center;
                position: relative;
            }

            .profile-circle {
                width: 70px;
                height: 70px;
                border-radius: 50%;
                border: 3px solid #fff;
                position: absolute;
                bottom: -35px;
                right: 20px;
                object-fit: cover;
                background: #fff;
            }

            .class-content {
                padding: 20px;
            }

            .class-title {
                font-size: 18px;
                font-weight: 600;
            }

            /* Add delay for each card */
            .col-12:nth-child(1) .class-card {
                animation-delay: 0.1s;
            }

            .col-12:nth-child(2) .class-card {
                animation-delay: 0.2s;
            }

            .col-12:nth-child(3) .class-card {
                animation-delay: 0.3s;
            }

            .col-12:nth-child(4) .class-card {
                animation-delay: 0.4s;
            }

            .col-12:nth-child(5) .class-card {
                animation-delay: 0.5s;
            }

            .col-12:nth-child(6) .class-card {
                animation-delay: 0.6s;
            }

            .btn-enter {
                background: rgb(218, 119, 86);
                color: #fff;
                padding: 8px 20px;
                border-radius: 6px;
                display: inline-flex;
                align-items: center;
                transition: background 0.3s;
            }

            .btn-enter:hover {
                background: rgb(198, 99, 66);
                color: #fff;
            }

            .btn-more {
                background: none;
                border: none;
                padding: 8px;
                color: #666;
            }
        </style>

        <div class="row g-4 m-0">
            <?php if (mysqli_num_rows($result) > 0):
                while ($ujian = mysqli_fetch_assoc($result)):
                    $guru_id = $ujian['guru_id'];
                    $query_guru = "SELECT foto_profil, namaLengkap FROM guru WHERE username = '$guru_id'";
                    $result_guru = mysqli_query($koneksi, $query_guru);
                    $guru = mysqli_fetch_assoc($result_guru);
                    $bg_image = !empty($ujian['background_image']) ? $ujian['background_image'] : 'assets/bg.jpg';


                    // Tambahkan pengecekan sebelum mendefinisikan fungsi
                    if (!function_exists('formatDurasi')) {
                        function formatDurasi($menit)
                        {
                            if ($menit >= 60) {
                                $jam = floor($menit / 60);
                                $sisa_menit = $menit % 60;

                                if ($sisa_menit > 0) {
                                    return $jam . " jam " . $sisa_menit . " menit";
                                } else {
                                    return $jam . " jam";
                                }
                            } else {
                                return $menit . " menit";
                            }
                        }
                    }
            ?>
                    <div class="col-12 col-md-6 pt-0 mt-1 p-md-3 col-lg-4" style="padding: 2rem;">
                        <div class="class-card border" style="transition: all 0.3s ease;">
                            <div class="class-banner" style="background-image: url('<?php echo $bg_image; ?>')">
                                <style>
                                    /* Animasi terpisah untuk fade-in awal */
                                    @keyframes fadeInUp {
                                        from {
                                            opacity: 0;
                                            transform: translateY(20px);
                                        }

                                        to {
                                            opacity: 1;
                                            transform: translateY(0);
                                        }
                                    }

                                    [data-aos="fade-up"] {
                                        opacity: 0;
                                        animation: fadeInUp 0.6s ease forwards;
                                    }
                                </style>
                                <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/' . $guru['foto_profil'] : 'assets/pp.png'; ?>"
                                    class="profile-circle" data-aos="fade-up">
                            </div>
                            <div class="class-content">
                                <h4 class="class-title mb-3"><?php echo htmlspecialchars($ujian['judul']); ?></h4>

                                <div class="class-meta" style="font-size: 12px;">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-book me-2 text-muted"></i>
                                                <span class="text-dark"><?php echo htmlspecialchars($ujian['mata_pelajaran']); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-mortarboard me-2 text-muted"></i>
                                                <span class="text-dark">Kelas <?php echo htmlspecialchars($ujian['tingkat']); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person me-2 text-muted"></i>
                                                <span class="text-secondary"><?php echo htmlspecialchars($guru['namaLengkap']); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                                <span class="text-secondary"><?php echo date('l, d F Y', strtotime($ujian['tanggal_mulai'])); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-clock me-2 text-muted"></i>
                                                <span class="text-secondary">Waktu Ujian <?php echo formatDurasi($ujian['durasi']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <?php
                                    $examStatus = getExamStatus($ujian['tanggal_mulai'], $ujian['tanggal_selesai']);
                                    if ($examStatus['status'] === 'ongoing'): ?>
                                        <?php if ($ujian['sudah_ujian'] > 0): ?>
                                            <button class="btn btn-secondary px-3 py-2 w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#ujianSelesaiModal">
                                                <i class="bi bi-check-circle me-1"></i> Sudah Ujian
                                            </button>
                                        <?php else: ?>
                                            <a href="mulai_ujian.php?id=<?php echo $ujian['id']; ?>"
                                                class="btn btn-primary px-3 py-2 w-100"
                                                style="background: rgb(218, 119, 86); border: none;">
                                                <i class="bi bi-play-circle me-1"></i> Mulai Ujian
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="btn btn-light px-3 py-2 w-100 border"
                                            <?php if ($examStatus['status'] === 'ended'): ?>
                                            data-bs-toggle="modal"
                                            data-bs-target="#ujianSelesaiModal"
                                            <?php elseif ($examStatus['status'] === 'waiting'): ?>
                                            data-bs-toggle="modal"
                                            data-bs-target="#waitingModal"
                                            <?php endif; ?>>
                                            <i class="bi bi-clock me-1"></i>
                                            <?php
                                            if ($examStatus['status'] === 'waiting'): ?>
                                                <?php echo $examStatus['countdown'] . " tersisa"; ?>
                                            <?php else: ?>
                                                Ujian Selesai
                                            <?php endif; ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <div class="col-12 py-5" style="position: relative; min-height: 250px;">
                    <div style="position: absolute; top: 15rem; left: 35rem; transform: translate(-50%, -50%); text-align: center; width: 100%;">
                        <i class="bi bi-journal-x" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 style="margin-top: 1rem; margin-bottom: 0.5rem; font-weight: 600;">Belum Ada Ujian</h5>
                        <p style="color: #6c757d; margin-bottom: 0; font-size: 0.9rem;">Belum ada ujian tersedia saat ini</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <style>

        </style>

        <!-- Modal Ujian Selesai -->
        <div class="modal fade" id="ujianSelesaiModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white" style="border-radius: 16px;">
                    <div class="modal-body text-center p-4">
                        <img src="assets/ujian_selesai.png" alt="" width="200rem">
                        <h5 class="mt-3 fw-bold">Sesi Ujian Telah Kadaluarsa</h5>
                        <p class="mb-4">Kamu tidak dapat mengakses ujian ini karena sudah menyelesaikan ujian atau sesi ujian telah berakhir.</p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn px-4 w-100 flex-fill" style="background-color: rgb(218, 119, 86); color:white; border-radius: 12px;" data-bs-dismiss="modal">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ujian belum dimulai -->
        <div class="modal fade" id="waitingModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 16px;">
                    <div class="modal-body text-center p-4">
                        <i class="bi bi-dash-circle-fill" style="font-size: 3rem; color:rgb(218, 119, 86);"></i>
                        <h5 class="mt-3 fw-bold">Ujian belum di mulai</h5>
                        <p class="mb-4">Kamu terlalu bersemngat, cek lagi kalau sudah waktu ujian jadi manfaatkan waktumu untuk belajar. Ok?</p>
                        <div class="d-flex gap-2 btn-group">
                            <button type="button" class="btn px-4 w-100" style="background-color: rgb(218, 119, 86); color:white; border-radius: 12px;" data-bs-dismiss="modal">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



</body>

</html>