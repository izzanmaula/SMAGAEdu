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
$query_kelas = "SELECT k.*, g.namaLengkap as nama_guru 
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
        <div class="col p-2 col-utama mt-1 mt-md-0">
            <div class="p-md-3 pb-md-0 d-md-flex ms-3 ms-md-0 salam justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-0">Beranda</h3>
                </div>

                <div class="d-flex gap-2 d-none d-md-block">
                    <button type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#modal_arsip_kelas"
                        class="btn btn-light border d-flex align-items-center gap-2 px-3">
                        <i class="bi bi-archive"></i>
                        <span class="d-none d-md-inline">Arsip Kelas</span>
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



                <!-- Classes Grid -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php if (mysqli_num_rows($result_kelas) > 0):
                        while ($kelas = mysqli_fetch_assoc($result_kelas)):
                            $bg_image = !empty($kelas['background_image']) ? $kelas['background_image'] : 'assets/bg.jpg';
                    ?>
                            <div class="col" data-aos="fade-up">
                                <div class="class-card border">
                                    <div class="class-banner" style="background-image: url('<?php echo $bg_image; ?>');">
                                        <div class="profile-circle-wrapper">
                                            <?php
                                            $guru_id = $kelas['guru_id'];
                                            $query_guru = "SELECT foto_profil FROM guru WHERE username = '$guru_id'";
                                            $result_guru = mysqli_query($koneksi, $query_guru);
                                            $data_guru = mysqli_fetch_assoc($result_guru);
                                            ?>
                                            <a href="profil_guru.php">
                                                <img src="<?php echo !empty($data_guru['foto_profil']) ? 'uploads/profil/' . $data_guru['foto_profil'] : 'assets/pp.png'; ?>"
                                                    class="profile-circle">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="class-content">
                                        <h4 class="class-title"><?php echo htmlspecialchars($kelas['mata_pelajaran']); ?></h4>
                                        <div class="class-meta"><?php echo htmlspecialchars($kelas['nama_guru']); ?></div>

                                        <div class="action-buttons">
                                            <a href="kelas.php?id=<?php echo $kelas['id']; ?>"
                                                class="btn-enter text-decoration-none d-flex align-items-center justify-content-center">
                                                Masuk
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn-more d-flex align-items-center justify-content-center" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end animate">
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center"
                                                            href="#" data-bs-toggle="modal" data-bs-target="#archiveConfirmModal"
                                                            data-kelas-id="<?php echo $kelas['id']; ?>">
                                                            <i class="bi bi-archive me-2"></i>Arsipkan
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <style>
                                                .animate {
                                                    animation-duration: 0.3s;
                                                    animation-fill-mode: both;
                                                    animation-name: dropdownAnimation;
                                                    transform-origin: top;
                                                }

                                                @keyframes dropdownAnimation {
                                                    from {
                                                        opacity: 0;
                                                        transform: scaleY(0);
                                                    }

                                                    to {
                                                        opacity: 1;
                                                        transform: scaleY(1);
                                                    }
                                                }
                                            </style>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile;
                    else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 mb-2">Belum Ada Kelas</h5>
                            <p class="text-muted mb-0">Hubungi guru untuk bergabung ke dalam kelas</p>
                        </div>
                    <?php endif; ?>
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
                    }

                    .class-card:hover {
                        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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

                <style>
                    .class-card {
                        border-radius: 12px;
                        overflow: hidden;
                        background: white;
                    }

                    .class-banner {
                        height: 140px;
                        background-size: cover;
                        background-position: center;
                        position: relative;
                        display: flex;
                        justify-content: flex-end;
                        padding: 1rem;
                    }

                    .profile-circle {
                        width: 60px;
                        height: 60px;
                        border-radius: 50%;
                        border: 3px solid white;
                        object-fit: cover;
                    }

                    .class-content {
                        padding: 1.5rem;
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


                    .btn-enter {
                        flex: 1;
                        border-radius: 8px;
                        border: none;
                        background: rgb(218, 119, 86);
                        color: white;
                        font-weight: 500;
                        transition: background 0.3s ease;
                        height: 38px;
                    }

                    .btn-more {
                        width: 38px;
                        border-radius: 8px;
                        border: 1px solid #eee;
                        background: white;
                        color: #666;
                    }

                    .dropdown-item {
                        padding: 8px 16px;
                    }
                </style>
            </div>


            <!-- modal untuk konfirmasi arsip -->

            <!-- Archive Confirmation Modal -->
            <div class="modal fade" id="archiveConfirmModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 16px;">
                        <div class="modal-body text-center p-4">
                            <i class="bi bi-archive" style="font-size: 3rem; color:rgb(218, 119, 86);"></i>
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
            <div class="modal fade" id="modal_arsip_kelas" tabindex="-1" aria-labelledby="label_arsip_kelas" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content rounded-4 border-0">
                        <!-- Header -->
                        <div class="modal-header border-0 px-4 pt-4 pb-0">
                            <div>
                                <h5 class="modal-title fw-bold mb-1">Kelas yang Diarsipkan</h5>
                                <p class="text-muted small mb-0">Daftar kelas yang telah diarsipkan</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body px-4">
                            <?php
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

                            if (mysqli_num_rows($result_arsip) > 0): ?>
                                <div class="row g-4 p-0 m-0">
                                    <?php while ($kelas_arsip = mysqli_fetch_assoc($result_arsip)): ?>
                                        <div class="col-12 ms-0 ps-0">
                                            <div class="card border shadow-sm rounded-4 overflow-hidden">
                                                <div class="row g-0">
                                                    <!-- Gambar Kelas -->
                                                    <div class="col-md-4">
                                                        <img src="<?php echo !empty($kelas_arsip['background_image']) ? htmlspecialchars($kelas_arsip['background_image']) : 'assets/bg.jpg'; ?>"
                                                            class="h-100 w-100"
                                                            style="object-fit: cover;"
                                                            alt="Background Image">
                                                    </div>

                                                    <!-- Informasi Kelas -->
                                                    <div class="col-md-8">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                                <div>
                                                                    <h5 class="card-title fw-bold mb-1">
                                                                        <?php echo htmlspecialchars($kelas_arsip['mata_pelajaran']); ?>
                                                                    </h5>
                                                                    <p class="text-muted mb-0">
                                                                        <small><?php echo htmlspecialchars($kelas_arsip['nama_guru']); ?></small>
                                                                    </p>
                                                                    <p class="text-muted mb-0">
                                                                        <small>Kelas <?php echo htmlspecialchars($kelas_arsip['tingkat']); ?></small>
                                                                    </p>
                                                                </div>
                                                                <img src="<?php echo !empty($kelas_arsip['guru_foto']) ? 'uploads/profil/' . $kelas_arsip['guru_foto'] : 'assets/pp.png'; ?>"
                                                                    class="rounded-circle shadow-sm border border-2"
                                                                    width="45"
                                                                    height="45"
                                                                    style="object-fit: cover;"
                                                                    alt="Foto Profil Pengajar">
                                                            </div>

                                                            <!-- Action Buttons -->
                                                            <div class="d-flex gap-2">
                                                                <a href="unarchive_kelas_siswa.php?id=<?php echo $kelas_arsip['id']; ?>"
                                                                    class="btn color-web text-white flex-grow-1 rounded-3">
                                                                    <i class="bi bi-box-arrow-up-right me-2"></i>
                                                                    Keluarkan dari Arsip
                                                                </a>
                                                                <button type="button"
                                                                    class="btn btn-outline-danger rounded-3"
                                                                    onclick="if(confirm('Apakah Anda yakin ingin menghapus kelas ini dari arsip?')) window.location.href='hapus_kelas.php?id=<?php echo $kelas_arsip['id']; ?>'">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-archive text-muted fs-1 mb-3"></i>
                                    <p class="text-muted mb-0">Belum ada kelas yang diarsipkan</p>
                                </div>
                            <?php endif; ?>
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