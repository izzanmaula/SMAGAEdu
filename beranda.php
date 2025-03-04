<?php
session_start();
require "koneksi.php";


if (!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

// Ambil userid dari session
$userid = $_SESSION['userid'];


// Ambil data guru
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);



// Ambil data siswa
$userid = $_SESSION['userid'];
$query = "SELECT * FROM siswa WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);
$query_kelas = "SELECT k.*, g.namaLengkap as nama_guru, g.foto_profil as guru_foto 
                FROM kelas k 
                JOIN kelas_siswa ks ON k.id = ks.kelas_id 
                JOIN guru g ON k.guru_id = g.username
                JOIN siswa s ON ks.siswa_id = s.id
                WHERE s.username = ? AND ks.is_archived = 0";


$stmt_kelas = mysqli_prepare($koneksi, $query_kelas);
mysqli_stmt_bind_param($stmt_kelas, "s", $userid);
mysqli_stmt_execute($stmt_kelas);
$result_kelas = mysqli_stmt_get_result($stmt_kelas);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <title>Beranda - SMAGAEdu</title>
</head>
<style>
    .custom-card {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .menu-samping {
            display: none;
        }

        body {
            padding-top: 60px;
        }

        .custom-card {
            max-width: 100%;
        }
    }

    .custom-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .custom-card .profile-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 3px solid white;
        margin-top: -40px;
    }

    .custom-card .card-body {
        text-align: left;
    }

    body {
        font-family: merriweather;
    }

    .color-web {
        background-color: rgb(218, 119, 86);
    }
</style>

<body>

    </head>
    <style>
        body {
            font-family: merriweather;
        }

        .color-web {
            background-color: rgb(218, 119, 86);
        }
    </style>
    <style>
        .col-utama {
            margin-left: 13rem;
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
            .menu-samping {
                display: none;
            }

            .col-utama {
                margin-left: 0;
            }
        }

        .message {
            max-width: 30%;
            margin-bottom: 1rem;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
        }

        .user-message {
            background-color: #EEECE2;
            margin-left: auto;
        }

        .ai-message {
            border: 1px solid #EEECE2;
            margin-right: auto;
        }

        .loading {
            animation-duration: 3s;
        }
    </style>
    </style>

    <body>

        <?php include 'includes/styles.php'; ?>

        <div class="container-fluid">
            <div class="row">
                <!-- sidebar buat view dekstopp -->
                <?php include 'includes/sidebar_siswa.php'; ?>

                <!-- Mobile navigation -->
                <?php include 'includes/mobile_nav siswa.php'; ?>

                <!-- Settings Modal -->
                <?php include 'includes/settings_modal.php'; ?>


            </div>
        </div>

        <!-- iOS style alerts that auto-dismiss after 2 seconds -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 'keluar_kelas_berhasil'): ?>
            <div class="ios-alert ios-alert-success" id="successAlert">
                <div class="ios-alert-content">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Berhasil keluar dari kelas
                </div>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('successAlert').classList.add('ios-alert-hide');
                    setTimeout(function() {
                        document.getElementById('successAlert').remove();
                    }, 500);
                }, 2000);
            </script>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="ios-alert ios-alert-danger" id="errorAlert">
                <div class="ios-alert-content">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <?php
                    switch ($_GET['error']) {
                        case 'kelas_tidak_ditemukan':
                            echo 'Kelas tidak ditemukan';
                            break;
                        case 'bukan_kelas_umum':
                            echo 'Anda hanya dapat keluar dari kelas umum';
                            break;
                        case 'keluar_kelas_gagal':
                            echo 'Gagal keluar dari kelas. Silakan coba lagi';
                            break;
                        default:
                            echo 'Terjadi kesalahan';
                    }
                    ?>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('errorAlert').classList.add('ios-alert-hide');
                    setTimeout(function() {
                        document.getElementById('errorAlert').remove();
                    }, 500);
                }, 2000);
            </script>
        <?php endif; ?>

        <style>
            .ios-alert {
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                max-width: 90%;
                min-width: 280px;
                z-index: 9999;
                border-radius: 14px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
                animation: iosAlertFadeIn 0.3s ease forwards;
                opacity: 0;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }

            .ios-alert-success {
                background-color: rgba(53, 199, 89, 0.95);
                color: white;
            }

            .ios-alert-danger {
                background-color: rgba(255, 59, 48, 0.95);
                color: white;
            }

            .ios-alert-content {
                padding: 14px 18px;
                font-size: 16px;
                font-weight: 500;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .ios-alert-hide {
                animation: iosAlertFadeOut 0.3s ease forwards;
            }

            @keyframes iosAlertFadeIn {
                from {
                    opacity: 0;
                    transform: translate(-50%, -20px);
                }

                to {
                    opacity: 1;
                    transform: translate(-50%, 0);
                }
            }

            @keyframes iosAlertFadeOut {
                from {
                    opacity: 1;
                    transform: translate(-50%, 0);
                }

                to {
                    opacity: 0;
                    transform: translate(-50%, -20px);
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .ios-alert {
                    animation: none;
                    opacity: 1;
                }

                .ios-alert-hide {
                    animation: none;
                    opacity: 0;
                }
            }
        </style>

        <!-- ini isi kontennya -->
        <!-- Isi konten -->
        <div class="col p-2 col-utama mt-1 mt-md-0">
            <div class="p-md-3 pb-md-2 d-flex ms-3 ms-md-0 px-2 mb-3 salam justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-0">Beranda</h3>
                </div>


                <!-- Minimalist Tab Navigation -->
                <div class="nav-container d-inline-flex">
                    <ul class="nav nav-pills border bg-light  p-1" id="kelasTab" style="border-radius: 15px;" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-pill" id="khusus-tab" data-bs-toggle="tab" data-bs-target="#khusus" type="button" role="tab">
                                <i class="bi bi-bookmark d-none d-md-inline me-1"></i>
                                <span class="d-none d-md-inline">Kelas yang diikuti</span>
                                <span class="d-inline d-md-none">Diikuti</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill" id="umum-tab" data-bs-toggle="tab" data-bs-target="#umum" type="button" role="tab">
                                <i class="bi bi-globe d-none d-md-inline me-1"></i>
                                <span class="d-none d-md-inline">Jelajahi Kelas</span>
                                <span class="d-inline d-md-none">Jelajahi</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <style>
                    .nav-container {
                        margin-right: 10px;
                    }

                    .nav-pills .nav-link {
                        color: #666;
                        font-size: 0.8rem;
                        padding: 0.3rem 0.8rem;
                        font-weight: 500;
                    }

                    .nav-pills .nav-link.active {
                        background-color: #da7756;
                    }
                </style>

                <div class="d-flex d-none d-md-block">
                    <button type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#modal_arsip_kelas"
                        class="btn btn-light border d-flex align-items-center gap-2 px-3" style="border-radius: 15px;">
                        <i class="bi bi-archive"></i>
                        <span class="d-none d-md-inline" style="font-size: 12px;">Arsip Kelas</span>
                    </button>
                </div>



            </div>



            <!-- fab arsip -->
            <!-- Floating Action Button -->
            <div class="floating-action-button d-block d-md-none">
                <!-- Main FAB -->
                <button class="btn btn-lg main-fab rounded-circle shadow" id="mainFab">
                    <i class="bi bi-plus-lg"></i>
                </button>

                <!-- Mini FABs -->
                <div class="mini-fabs">
                    <!-- Buat Kelas Button -->
                    <button class="btn mini-fab rounded-circle shadow"
                        data-bs-toggle="modal"
                        data-bs-target="#modal_tambah_kelas"
                        title="Buat Kelas">
                        <i class="bi bi-plus-lg"></i>
                        <span class="fab-label">Gabung Kelas</span>
                    </button>

                    <!-- Arsip Button -->
                    <button class="btn mini-fab rounded-circle shadow"
                        data-bs-toggle="modal"
                        data-bs-target="#modal_arsip_kelas"
                        title="Arsip">
                        <i class="bi bi-archive"></i>
                        <span class="fab-label">Arsip Kelas</span>
                    </button>
                </div>

                <!-- Backdrop for FAB -->
                <div class="fab-backdrop"></div>
            </div>

            <style>
                /* Floating Action Button Styling */
                .floating-action-button {
                    position: fixed;
                    bottom: 80px;
                    right: 20px;
                    z-index: 1050;
                }

                .main-fab {
                    width: 56px;
                    height: 56px;
                    background: #da7756;
                    color: white;
                    transition: transform 0.3s;
                    position: relative;
                    z-index: 1052;
                }

                .main-fab:hover {
                    background: #c56647;
                    color: white;
                }

                .main-fab.active {
                    transform: rotate(45deg);
                }

                .mini-fabs {
                    position: absolute;
                    bottom: 70px;
                    right: 7px;
                    display: flex;
                    flex-direction: column;
                    gap: 16px;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s;
                    z-index: 1052;
                }

                .mini-fabs.show {
                    opacity: 1;
                    visibility: visible;
                }

                .mini-fab {
                    width: 42px;
                    height: 42px;
                    background: white;
                    color: #666;
                    transform: scale(0);
                    transition: transform 0.3s;
                    position: relative;
                }

                .mini-fabs.show .mini-fab {
                    transform: scale(1);
                }

                .mini-fab:hover {
                    background: #f8f9fa;
                    color: #da7756;
                }

                /* Label style */
                .fab-label {
                    position: absolute;
                    right: 50px;
                    top: 50%;
                    transform: translateY(-50%);
                    background: rgba(0, 0, 0, 0.7);
                    color: white;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 12px;
                    white-space: nowrap;
                    transition: opacity 0.2s;
                    pointer-events: none;
                }

                .mini-fab:hover .fab-label {
                    opacity: 1;
                    visibility: visible;
                }

                /* Backdrop style */
                .fab-backdrop {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s;
                    z-index: 1051;
                }

                .fab-backdrop.show {
                    opacity: 1;
                    visibility: visible;
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const mainFab = document.getElementById('mainFab');
                    const miniFabs = document.querySelector('.mini-fabs');
                    const backdrop = document.querySelector('.fab-backdrop');
                    let isOpen = false;

                    mainFab.addEventListener('click', function(e) {
                        e.stopPropagation();
                        isOpen = !isOpen;
                        mainFab.classList.toggle('active');
                        miniFabs.classList.toggle('show');
                        backdrop.classList.toggle('show');
                    });

                    // Close menu when clicking backdrop
                    backdrop.addEventListener('click', function() {
                        isOpen = false;
                        mainFab.classList.remove('active');
                        miniFabs.classList.remove('show');
                        backdrop.classList.remove('show');
                    });

                    // Prevent menu from closing when clicking menu items
                    miniFabs.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
            </script>



            <div class="row justify-content-center align-items-center m-0 p-0 mb-1">

                <style>
                    .salam {
                        padding-top: 1rem !important;
                    }

                    @media screen and (max-width: 768px) {
                        .salam {}

                        .col-utama {
                            padding-top: 0 !important;
                        }
                    }
                </style>

                <!-- Jumbotron yang akan berubah berdasarkan tab yang aktif -->
                <div class="jumbotron jumbotron-fluid mb-md-2 d-none d-md-block">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <!-- Konten untuk tab Diikuti (khusus) -->
                                <div id="jumbotron-khusus" class="jumbotron-content active">
                                    <h2 class="display-5">
                                        <?php

                                        date_default_timezone_set('Asia/Jakarta');
                                        $hour = date('H');
                                        if ($hour >= 5 && $hour < 12) {
                                            echo "Selamat Pagi";
                                        } else if ($hour >= 12 && $hour < 15) {
                                            echo "Selamat Siang";
                                        } else if ($hour >= 15 && $hour < 19) {
                                            echo "Selamat Sore";
                                        } else {
                                            echo "Selamat Malam";
                                        }
                                        ?>,
                                        <span style="color: rgb(218, 119, 86);"><?php echo ucwords($siswa['nama']); ?></span>
                                    </h2>
                                    <p class="lead">Lanjutkan Pembelajaranmu Bersama Guru dalam Kelas Sekolah. Selamat Belajar.</p>
                                </div>

                                <!-- Konten untuk tab Umum -->
                                <div id="jumbotron-umum" class="jumbotron-content" style="display: none;">
                                    <h2 class="display-5">Jelajahi Ilmu Tanpa Batas!</span></h2>
                                    <p class="lead">Jelajahi kelas umum, diskusi, grup santai, semua tersedia untuk kamu.</p>
                                </div>
                            </div>

                            <div class="col-md-6 text-center d-none d-md-block">
                                <!-- Gambar untuk tab Diikuti (khusus) -->
                                <img src="assets/jumbo_khusus.png" class="img-fluid jumbotron-image" id="jumbotron-image" alt="Ilustrasi kelas" style="max-height: 20rem;">
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // Menangani perubahan jumbotron berdasarkan tab yang aktif
                    document.addEventListener('DOMContentLoaded', function() {
                        // Menambahkan event listener pada tab-tab
                        document.getElementById('khusus-tab').addEventListener('shown.bs.tab', function() {
                            // Menampilkan konten jumbotron untuk "Diikuti" (Khusus)
                            document.getElementById('jumbotron-khusus').style.display = 'block';
                            document.getElementById('jumbotron-umum').style.display = 'none';
                            // Mengganti gambar
                            document.getElementById('jumbotron-image').src = 'assets/jumbo_khusus.png';
                            document.getElementById('jumbotron-image').classList.add('img-fluid');
                            document.getElementById('jumbotron-image').style.maxHeight = '20rem';
                        });

                        document.getElementById('umum-tab').addEventListener('shown.bs.tab', function() {
                            // Menampilkan konten jumbotron untuk "Umum"
                            document.getElementById('jumbotron-khusus').style.display = 'none';
                            document.getElementById('jumbotron-umum').style.display = 'block';
                            // Mengganti gambar
                            document.getElementById('jumbotron-image').src = 'assets/jumbo_umum.png';
                            document.getElementById('jumbotron-image').classList.add('img-fluid');
                            document.getElementById('jumbotron-image').style.maxHeight = '20rem';
                        });
                    });
                </script>

                <style>
                    /* Gaya untuk jumbotron */
                    .jumbotron {
                        border-radius: 15px;
                    }

                    /* Efek transisi untuk gambar */
                    .jumbotron-image {
                        transition: all 0.3s ease-in-out;
                    }

                    /* Efek transisi untuk konten jumbotron */
                    .jumbotron-content {
                        transition: opacity 0.3s ease-in-out;
                    }
                </style>



                <!-- Tab Content -->
                <div class="tab-content" id="kelasTabContent">
                    <!-- Tab Kelas Khusus -->
                    <div class="tab-pane fade show active" id="khusus" role="tabpanel" aria-labelledby="khusus-tab">
                        <div class="row g-4 mx-0">
                            <?php if (mysqli_num_rows($result_kelas) > 0):
                                while ($kelas = mysqli_fetch_assoc($result_kelas)):
                                    $bg_image = !empty($kelas['background_image']) ? $kelas['background_image'] : 'assets/bg.jpg';
                            ?>
                                    <div class="col-12 col-md-6 col-lg-4 mb-1">
                                        <div class="class-card border">
                                            <div class="class-banner" style="background-image: url('<?php echo $bg_image; ?>')">
                                                <div class="profile-circle-wrapper">
                                                    <img src="<?php echo !empty($kelas['guru_foto']) ? 'uploads/profil/' . $kelas['guru_foto'] : 'assets/pp.png'; ?>" class="profile-circle">
                                                </div>

                                            </div>
                                            <div class="class-content">
                                                <h4 class="class-title">
                                                    <?php
                                                    if ($kelas['is_public']) {
                                                        echo htmlspecialchars($kelas['nama_kelas']);
                                                    } else {
                                                        echo htmlspecialchars($kelas['mata_pelajaran']);
                                                    }
                                                    ?>

                                                    <?php if ($kelas['is_public']): ?>
                                                        <span class="badge bg-success ms-2" style="font-size: 10px;"><i class="bi bi-globe me-1"></i>Publik</span>
                                                    <?php endif; ?>
                                                </h4>
                                                <div class="class-meta mb-2">
                                                    <div class="d-flex text-muted small mt-1">
                                                        <i class="bi bi-person me-2"></i>
                                                        <?php echo htmlspecialchars($kelas['nama_guru']); ?>
                                                    </div>

                                                    <div class="d-flex text-muted small mt-1">
                                                        <i class="bi bi-book me-2"></i>
                                                        <?php if (!empty($kelas['deskripsi'])): ?>
                                                            <div class="description-container">
                                                                <span class="truncated-text">
                                                                    <?php echo (strlen($kelas['deskripsi']) > 50) ?
                                                                        htmlspecialchars(substr($kelas['deskripsi'], 0, 50)) . '... ' :
                                                                        htmlspecialchars($kelas['deskripsi']); ?>
                                                                    <?php if (strlen($kelas['deskripsi']) > 50): ?>
                                                                        <a href="#" onclick="return toggleReadMore(this)" class="text-muted">Selengkapnya</a>
                                                                    <?php endif; ?>
                                                                </span>
                                                                <span class="full-text" style="display: none;">
                                                                    <?php echo htmlspecialchars($kelas['deskripsi']); ?>
                                                                    <a href="#" onclick="return toggleReadMore(this)">Sembunyikan</a>
                                                                </span>
                                                            </div>
                                                        <?php else: ?>
                                                            Tidak ada deskripsi
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="action-buttons">
                                                    <a href="kelas.php?id=<?php echo $kelas['id']; ?>"
                                                        class="btn-enter text-decoration-none d-flex align-items-center justify-content-center">
                                                        Masuk
                                                    </a>
                                                    <div class="dropdown">
                                                        <button class="btn-more d-flex align-items-center justify-content-center" data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end animate slideIn">
                                                            <li>
                                                                <button class="dropdown-item d-flex align-items-center"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#archiveConfirmModal"
                                                                    data-kelas-id="<?php echo $kelas['id']; ?>">
                                                                    <i class="bi bi-archive me-2"></i>Arsipkan
                                                                </button>
                                                            </li>
                                                            <?php if ($kelas['is_public']): ?>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center text-danger" href="#"
                                                                        onclick="confirmLeaveClass(<?php echo $kelas['id']; ?>, '<?php echo $kelas['is_public'] ? htmlspecialchars($kelas['nama_kelas']) : htmlspecialchars($kelas['mata_pelajaran']); ?>')">
                                                                        <i class="bi bi-box-arrow-right me-2"></i>Keluar dari kelas
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile;
                            else: ?>
                                <div class="col-12 text-center my-5 py-5">
                                    <i class="bi bi-journal-x d-block mx-auto mb-3" style="font-size: 3rem; color: #6c757d;"></i>
                                    <h5 class="fw-bold">Belum Ada Kelas</h5>
                                    <p class="text-muted">Hubungi guru untuk bergabung ke dalam kelas atau cek tab "Umum" untuk kelas yang tersedia</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tab Kelas Umum -->
                    <div class="tab-pane fade" id="umum" role="tabpanel" aria-labelledby="umum-tab">

                        <!-- kontainer muncul di mobile -->
                        <div class="card shadow-none mx-2 rounded-3 border mb-2 position-relative mobile-alert-card d-md-none">
                            <button type="button" class="btn-close position-absolute close-mobile-alert" style="top: 8px; right: 8px; font-size: 0.7rem;" aria-label="Close"></button>
                            <div class="card-body d-flex align-items-center py-2 px-3">
                                <div>
                                    <h6 class="card-title fw-semibold mb-1" style="font-size:14px;">Jelajahi Ilmu Tanpa Batas!</h6>
                                    <p class="card-text text-muted mb-0" style="font-size:12px;">Temukan kelas umum dan diskusi menarik.</p>
                                </div>
                                <img src="assets/umum.png" width="70" class="ms-2" alt="Jelajahi Ilmu">
                            </div>
                        </div>

                        <div class="row g-4 mx-0" id="kelas-umum-container">




                            <!-- Kelas umum akan dimuat dengan AJAX -->
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border" style="color: #da7756;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3">Memuat kelas umum...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Script untuk memuat kelas umum saat tab diklik -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Tambahkan event listener untuk tab Umum
                        document.getElementById('umum-tab').addEventListener('click', function() {
                            loadPublicClasses();
                        });

                        // Load public classes on initial load if the "Umum" tab is active
                        if (document.getElementById('umum-tab').classList.contains('active')) {
                            loadPublicClasses();
                        }

                        function loadPublicClasses() {
                            const container = document.getElementById('kelas-umum-container');

                            // Tampilkan loading
                            container.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border" style="color: #da7756;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3">Memuat kelas umum...</p>
                            </div>
                        `;

                            // Buat AJAX request untuk fetch kelas publik
                            fetch('get_public_classes.php')
                                .then(response => response.json())
                                .then(data => {
                                    if (data.length === 0) {
                                        container.innerHTML = `
                                        <div class="col-12 text-center my-5 py-5">
                                            <i class="bi bi-globe-asia-australia d-block mx-auto mb-3" style="font-size: 3rem; color: #6c757d;"></i>
                                            <h5 class="fw-bold">Tidak Ada Kelas Umum</h5>
                                            <p class="text-muted">Belum ada kelas umum yang tersedia untuk tingkat kelas Anda</p>
                                        </div>
                                    `;
                                        return;
                                    }

                                    // Render kelas publik - fixed layout with proper row and column structure
                                    let htmlContent = '';
                                    data.forEach(kelas => {
                                        const bgImage = kelas.background_image ? kelas.background_image : 'assets/bg.jpg';
                                        const guruFoto = kelas.foto_profil ? `uploads/profil/${kelas.foto_profil}` : 'assets/pp.png';
                                        htmlContent += `


<div class="col-12 col-md-6 col-lg-4">
    <div class="class-card border h-100">
        <div class="class-banner" style="background-image: url('${bgImage}');">
            <div class="profile-circle-wrapper">
                <img src="${guruFoto}" class="profile-circle">
            </div>
        </div>
        <div class="class-content">
            <h4 class="class-title">
                ${kelas.is_public ? kelas.nama_kelas : kelas.mata_pelajaran}
                ${kelas.is_public ? '<span class="badge bg-success small ms-2" style="font-size:12px;"><i class="bi bi-globe"></i> Publik</span>' : ''}
                ${kelas.is_joined ? '<span class="badge small" style="background-color:rgb(218, 119, 86);"><i class="bi bi-person-check"></i> Mengikuti</span>' : ''}

            </h4>
            <div class="class-meta mb-2">
                <div class="d-flex text-muted small mt-1">
                    <i class="bi bi-person me-2"></i>
                    ${kelas.nama_guru}
                </div>
                <div class="d-flex text-muted small mt-1">
    <i class="bi bi-book me-2"></i>
    ${kelas.deskripsi ? 
        `<div class="description-container">
            <span class="truncated-text">${kelas.deskripsi.length > 50 ? kelas.deskripsi.substring(0, 50) + '... ' : kelas.deskripsi}
                ${kelas.deskripsi.length > 50 ? '<a href="#" onclick="return toggleReadMore(this)">Selengkapnya</a>' : ''}
            </span>
            <span class="full-text" style="display: none;">${kelas.deskripsi} <a href="#" onclick="return toggleReadMore(this)">Sembunyikan</a></span>
         </div>` 
        : 'Tidak ada deskripsi'}
</div>
            </div>

            <div class="action-buttons">
                ${kelas.is_joined 
                    ? `<a href="kelas.php?id=${kelas.id}" class="btn-enter text-decoration-none d-flex align-items-center justify-content-center">Masuk</a>
                       <div class="dropdown">
                           <button class="btn-more d-flex align-items-center justify-content-center" data-bs-toggle="dropdown">
                               <i class="bi bi-three-dots"></i>
                           </button>
                           <ul class="dropdown-menu dropdown-menu-end animate slideIn">
                               <li>
                                   <a class="dropdown-item d-flex align-items-center text-danger" href="#" 
                                      onclick="confirmLeaveClass(${kelas.id}, '${kelas.is_public ? kelas.nama_kelas : kelas.mata_pelajaran}')">
                                      <i class="bi bi-box-arrow-right me-2"></i>Keluar dari kelas
                                   </a>
                               </li>
                           </ul>
                       </div>`
                    : `<a href="gabung_kelas_publik.php?id=${kelas.id}" class="btn-join text-decoration-none d-flex align-items-center justify-content-center" style="border: 1px solid black; background-color: white; color: black;">Ikuti</a>`
                }
            </div>
        </div>
    </div>
</div>
`;
                                    });


                                    container.innerHTML = htmlContent;

                                    // Add event listeners to close buttons after content is loaded
                                    document.querySelectorAll('.close-mobile-alert').forEach(button => {
                                        button.addEventListener('click', function() {
                                            const card = this.closest('.mobile-alert-card');
                                            if (card) {
                                                card.style.display = 'none';
                                            }
                                        });
                                    });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    container.innerHTML = `
                                    <div class="col-12 text-center my-5 py-5">
                                        <i class="bi bi-exclamation-triangle d-block mx-auto mb-3" style="font-size: 3rem; color: #dc3545;"></i>
                                        <h5 class="fw-bold">Terjadi Kesalahan</h5>
                                        <p class="text-muted">Gagal memuat kelas umum. Silakan coba lagi.</p>
                                    </div>
                                `;
                                });
                        }
                    });

                    // Tambahkan script ini setelah script yang memuat kelas umum
                    function confirmLeaveClass(classId, className) {
                        // Set nama kelas dan href tombol konfirmasi
                        document.getElementById('leaveClassNameSpan').textContent = className;
                        document.getElementById('confirmLeaveButton').href = 'keluar_kelas_umum.php?id=' + classId;

                        // Tampilkan modal
                        const leaveModal = new bootstrap.Modal(document.getElementById('leaveClassModal'));
                        leaveModal.show();
                    }

                    // Tambahkan kode ini ke file JavaScript Anda
                    function toggleReadMore(element) {
                        const container = element.closest('.description-container');
                        const truncatedText = container.querySelector('.truncated-text');
                        const fullText = container.querySelector('.full-text');

                        if (truncatedText.style.display !== 'none') {
                            truncatedText.style.display = 'none';
                            fullText.style.display = 'inline';
                        } else {
                            truncatedText.style.display = 'inline';
                            fullText.style.display = 'none';
                        }

                        return false; // Mencegah browser menggulir ke atas halaman
                    }
                </script>

                <!-- modal konfirmasi keluar dari kelas umum -->

                <!-- Modal Konfirmasi Keluar Kelas -->
                <div class="modal fade" id="leaveClassModal" tabindex="-1" aria-labelledby="leaveClassModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0">
                            <div class="modal-body p-4 text-center">
                                <i class="bi bi-box-arrow-right d-block mb-3" style="color: #dc3545; font-size: 48px;"></i>
                                <h5 class="fw-bold mb-2">Keluar dari Kelas</h5>
                                <p class="text-muted mb-4">Apakah kamu yakin ingin keluar dari kelas <span id="leaveClassNameSpan" class="fw-semibold"></span>?</p>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary flex-grow-1" data-bs-dismiss="modal">Batal</button>
                                    <a href="#" id="confirmLeaveButton" class="btn btn-danger flex-grow-1">Keluar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .class-banner {
                        height: 120px;
                        background-size: cover;
                        background-position: center;
                        position: relative;
                    }

                    .profile-circle-wrapper {
                        position: absolute;
                        bottom: -24px;
                        left: 85%;
                        transform: translateX(-50%);
                    }

                    .profile-circle {
                        width: 48px;
                        height: 48px;
                        border-radius: 50%;
                        border: 3px solid white;
                        background: white;
                        object-fit: cover;
                    }

                    .class-content {
                        padding: 2rem 1.5rem 1.5rem;
                    }

                    .action-buttons {
                        display: flex;
                        gap: 0.5rem;
                        margin-top: 1rem;
                        height: 38px;
                    }

                    .btn-enter {
                        flex: 1;
                        border-radius: 8px;
                        border: none;
                        background: #da7756;
                        color: white;
                        font-weight: 500;
                        transition: all 0.3s ease;
                        height: 100%;
                    }

                    .btn-enter:hover {
                        background: #c56548;
                    }

                    .btn-join {
                        flex: 1;
                        border-radius: 8px;
                        border: none;
                        background: #c56548;
                        color: white;
                        font-weight: 500;
                        transition: all 0.3s ease;
                        height: 100%;
                    }

                    .btn-more {
                        width: 38px;
                        border-radius: 8px;
                        border: 1px solid #eee;
                        background: white;
                        color: #666;
                        height: 100%;
                        transition: all 0.3s ease;
                    }

                    .btn-more:hover {
                        background: #f8f9fa;
                    }

                    .class-card {
                        transition: all 0.3s ease;
                        border-radius: 12px;
                        overflow: hidden;
                        background: white;
                    }

                    .class-card:hover {
                        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
                    }

                    .class-title {
                        font-size: 1.1rem;
                        font-weight: bold;
                        margin-bottom: 0.5rem;
                    }

                    .class-meta {
                        color: #666;
                        font-size: 0.9rem;
                        margin-bottom: 1rem;
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

                    [data-aos="fade-up"] {
                        animation: fadeInUp 0.6s ease forwards;
                    }
                </style>


                <!-- modal untuk konfirmasi arsip -->

                <!-- Archive Confirmation Modal -->
                <div class="modal fade" id="archiveConfirmModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 16px;">
                            <div class="modal-body text-center p-4">
                                <h5 class="mt-3 fw-bold">Arsipkan Kelas</h5>
                                <p class="mb-4">Apakah kamu yakin ingin mengarsipkan kelas <strong id="kelasToArchive"></strong>?</p>
                                <div class="d-flex gap-2 btn-group">
                                    <button type="button" class="btn border px-4" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                                    <a href="#" id="confirmArchiveBtn" class="btn text-white px-4" style="border-radius: 12px; background-color:rgb(218, 119, 86);">Arsipkan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const archiveModal = document.getElementById('archiveConfirmModal');
                        archiveModal.addEventListener('show.bs.modal', function(event) {
                            const button = event.relatedTarget;
                            const kelasId = button.getAttribute('data-kelas-id');
                            const kelasName = button.closest('.class-card').querySelector('.class-title').textContent;

                            document.getElementById('kelasToArchive').textContent = kelasName;
                            const confirmBtn = document.getElementById('confirmArchiveBtn');
                            confirmBtn.href = 'archive_kelas_siswa.php?id=' + kelasId;
                        });
                    });
                </script>


                <!-- modal untuk gabung kelas -->
                <!-- Modal -->
                <div class="modal fade" id="modal_tambah_kelas" tabindex="-1" aria-labelledby="label_tambah_kelas" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0">
                            <div class="modal-body p-4 text-center">
                                <span class="bi bi-sign-stop-lights-fill d-block mb-3" style="color: #c56548; font-size:70px;"></span>
                                <h5 class="fw-semibold mb-2">Tunggu guru memasukkanmu kedalam kelas</h5>
                                <p class="text-muted mb-4">Kamu akan masuk setelah guru memasukkanmu ke dalam kelas secara otomatis</p>

                                <button class="btn w-100 rounded color-web text-white py-2" data-bs-dismiss="modal">
                                    Oke, saya mengerti
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
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


                <!-- Modal Arsip Kelas -->
                <div class="modal fade " id="modal_arsip_kelas" tabindex="-1" aria-labelledby="label_arsip_kelas" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                        <div class="modal-content bg-white">
                            <!-- Header -->
                            <div class="modal-header border-0 pb-0">
                                <div>
                                    <h1 class="modal-title fs-5 fw-bold" id="label_arsip_kelas">Kelas yang Diarsipkan</h1>
                                    <p class="text-muted small mb-0">Daftar kelas yang telah diarsipkan</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <!-- Body -->
                            <div class="modal-body px-4">
                                <?php
                                // Query untuk mengambil kelas yang diarsipkan
                                $query_arsip = "SELECT k.*, g.namaLengkap as nama_guru, g.foto_profil as guru_foto
                                               FROM kelas k 
                                               JOIN kelas_siswa ks ON k.id = ks.kelas_id 
                                               JOIN guru g ON k.guru_id = g.username
                                               JOIN siswa s ON ks.siswa_id = s.id
                                               WHERE s.username = ? AND ks.is_archived = 1";

                                $stmt_arsip = mysqli_prepare($koneksi, $query_arsip);
                                mysqli_stmt_bind_param($stmt_arsip, "s", $userid);
                                mysqli_stmt_execute($stmt_arsip);
                                $result_arsip = mysqli_stmt_get_result($stmt_arsip);

                                if (mysqli_num_rows($result_arsip) > 0) {
                                ?>
                                    <div class="row g-4">
                                        <?php while ($kelas_arsip = mysqli_fetch_assoc($result_arsip)) { ?>
                                            <div class="col-12">
                                                <div class="card border-1 shadow-none">
                                                    <div class="row g-0">
                                                        <!-- Gambar Kelas -->
                                                        <div class="col-md-4">
                                                            <img src="<?php echo !empty($kelas_arsip['background_image']) ? htmlspecialchars($kelas_arsip['background_image']) : 'assets/bg.jpg'; ?>"
                                                                class="img-fluid rounded-start h-100"
                                                                style="object-fit: cover;"
                                                                alt="Background Image">
                                                        </div>

                                                        <!-- Informasi Kelas -->
                                                        <div class="col-md-8">
                                                            <div class="card-body shadow-none border-2">
                                                                <div class="d-flex justify-content-between align-items-start">
                                                                    <div>
                                                                        <h5 class="card-title fw-bold mb-1">
                                                                            <?php echo htmlspecialchars($kelas_arsip['mata_pelajaran']); ?>
                                                                        </h5>
                                                                        <p class="card-text text-muted small">
                                                                            Kelas <?php echo htmlspecialchars($kelas_arsip['tingkat']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <img src="<?php echo !empty($kelas_arsip['guru_foto']) ? 'uploads/profil/' . $kelas_arsip['guru_foto'] : 'assets/pp.png'; ?>"
                                                                        class="rounded-circle"
                                                                        width="40"
                                                                        height="40"
                                                                        style="object-fit: cover;"
                                                                        alt="Profile">
                                                                </div>

                                                                <!-- Action Buttons -->
                                                                <div class="d-flex gap-2 mt-3">
                                                                    <a href="unarchive_kelas_siswa.php?id=<?php echo $kelas_arsip['id']; ?>"
                                                                        class="btn color-web text-white btn-sm flex-grow-1">
                                                                        <i class="bi bi-box-arrow-up-right me-1"></i>
                                                                        Keluarkan
                                                                    </a>
                                                                    <button type="button"
                                                                        class="btn btn-outline-danger btn-sm"
                                                                        onclick="if(confirm('Apakah Anda yakin ingin menghapus kelas ini?')) window.location.href='hapus_kelas.php?id=<?php echo $kelas_arsip['id']; ?>'">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="text-center py-5">
                                        <img src="assets/arsip.png" alt="" width="200rem" class="opacity-50">
                                        <p class="text-muted mb-0">Belum ada kelas yang diarsipkan</p>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    /* Modal Archive Styling */
                    #modal_arsip_kelas .modal-content {
                        border-radius: 15px;
                    }

                    #modal_arsip_kelas .card {
                        transition: transform 0.2s;
                        border-radius: 12px;
                    }

                    #modal_arsip_kelas .card:hover {
                        transform: translateY(-2px);
                    }

                    #modal_arsip_kelas .btn {
                        border-radius: 8px;
                        padding: 8px 16px;
                    }

                    /* Responsive adjustments */
                    @media (max-width: 767px) {
                        #modal_arsip_kelas .modal-dialog {
                            margin: 1rem;
                        }

                        #modal_arsip_kelas .col-md-4 img {
                            height: 150px;
                            width: 100%;
                            border-radius: 12px 12px 0 0 !important;
                        }

                        #modal_arsip_kelas .card-body {
                            padding: 1rem;
                        }
                    }
                </style>





    </body>

</html>