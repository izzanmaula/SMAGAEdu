<?php
session_start();
require "koneksi.php";


if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
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

        body{ 
            font-family: merriweather;
        }
        .color-web {
            background-color: rgb(218, 119, 86);
        }

</style>
<body>
    

<title>SMAGAAI - SMAGAEdu</title>
</head>
<style>
        body{ 
            font-family: merriweather;
        }
        .color-web {
            background-color: rgb(218, 119, 86);
        }
</style>
    <style>
        .col-utama {
            margin-left: 13rem;
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
    

    <!-- Navbar Mobile -->
    <nav class="navbar navbar-dark d-md-none color-web fixed-top">
        <div class="container-fluid">
            <!-- Logo dan Nama -->
            <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="#">
                <img src="assets/logo_white.png" alt="" width="30px" class="logo_putih">
            <div>
                    <h1 class="p-0 m-0" style="font-size: 20px;">Beranda</h1>
                    <p class="p-0 m-0 d-none d-md-block" style="font-size: 12px;">LMS</p>
                </div>
            </a>
            
            <!-- Tombol Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <span class="navbar-toggler-icon" style="color:white"></span>
            </button>
            
            <!-- Offcanvas/Sidebar Mobile -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" style="font-size: 30px;">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body d-flex justify-content-between flex-column">
                    <div class="d-flex flex-column gap-2">
                        <!-- Menu Beranda -->
                        <a href="#" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center color-web rounded  p-2">
                                <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Beranda</p>
                            </div>
                        </a>
                        
                        
                        <!-- Menu Ujian -->
                        <a href="ujian.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Ujian</p>
                            </div>
                        </a>

                        <!-- Menu ai -->
                        <a href="ai.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">SMAGA AI</p>
                            </div>
                        </a>

                        
                        <!-- Menu Profil -->
                        <a href="profil.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/profil_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Profil</p>
                            </div>
                        </a>
                        
                        
                        <!-- Menu Bantuan -->
                        <a href="bantuan.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/bantuan_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Bantuan</p>
                            </div>
                        </a>
                    </div>
                    
                <!-- Profile Dropdown -->
                <div class="mt-3 dropup"> <!-- Tambahkan class dropdown di sini -->
                    <button class="btn d-flex align-items-center gap-3 p-2 rounded-3 border w-100" 
                            style="background-color:rgb(255, 252, 248);" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                            <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp.png'; ?>" 
                                    alt="Profile Picture" 
                                    class="rounded-circle" 
                                    style="width: 25px; height: 25px;object-fit: cover; z-index: 99999;">
                            <p class="p-0 m-0 text-truncate" style="font-size: 12px;">Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?></p>
                    </button>
                    <ul class="dropdown-menu w-100" style="font-size: 12px;"> <!-- Tambahkan w-100 agar lebar sama -->
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="logout.php" style="color: red;">Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

     <!-- row col untuk halaman utama -->
     <div class="container-fluid">
        <div class="row">
        <div class="col-auto vh-100 p-2 p-md-4 shadow-sm menu-samping d-none d-md-block" style="background-color:rgb(238, 236, 226)">
                <style>
                .menu-samping {
                    position: fixed;
                    width: 13rem;
                    z-index: 1000;
                    /* Tambahkan flexbox dan height */
                    display: flex;
                    flex-direction: column;
                    height: 100vh;
                    
                }
                .menu-content {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                }
                .menu-atas {
                    height: calc(100vh - 80px); /* 80px adalah perkiraan tinggi dropdown */

                }
                .menu-bawah {
                    position: fixed;
                    bottom: 1rem;
                    width: 10rem; /* Sesuaikan dengan lebar menu */
                }
                .col-utama {
                    margin-left: 0;
                }
                @media (min-width: 768px) {
                    .col-utama {
                        margin-left: 13rem;
                    }
                }
                </style>
                <div class="menu-atas">
                    <div class="ps-1 mb-3">
                        <a href="beranda.php" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
                            <img src="assets/smagaedu.png" alt="" width="30px" class="logo_orange">
                            <div>
                                <h1 class="display-5  p-0 m-0" style="font-size: 20px; text-decoration: none;">SMAGAEdu</h1>
                                <p class="p-0 m-0 text-muted" style="font-size: 12px;">LMS</p>
                            </div>
                        </a>
                    </div>  
                    <div class="col">
                        <a href="beranda.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center bg-white shadow-sm rounded p-2" style="">
                            <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Beranda</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="ujian.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Ujian</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="profil.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/profil_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Profil</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="ai.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">SMAGA AI</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="bantuan.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/bantuan_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Bantuan</p>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="menu-bawah">
                    <div class="row dropdown">
                        <div class="btn d-flex align-items-center gap-3 p-2 rounded-3 border dropdown-toggle" style="background-color: #F8F8F7;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp-siswa.png'; ?>" 
                                    alt="Profile Picture" 
                                    class="rounded-circle" 
                                    style="width: 30px; height: 30px;object-fit: cover; z-index: 99999;">
                            <p class="p-0 m-0 text-truncate" style="font-size: 12px;"><?php echo htmlspecialchars($_SESSION['nama']); ?></p>
                        </div>
                        <!-- dropdown menu option -->
                        <ul class="dropdown-menu" style="font-size: 12px;">
                            <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                            <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ini isi kontennya -->
<!-- Isi konten -->
<div class="col p-4 col-utama mt-1 mt-md-0">
        <div class="row justify-content-between align-items-center mb-1">
            <div class="salam col-12 col-md-auto mb-3 mb-md-0">
                <h3 style="font-weight: bold;">Beranda</h3>
            </div>

            <style>
                @media screen and (max-width: 768px) {
                    .salam {
                        display: none;
                    }
                    .col-utama {
                        padding-top: 0 !important;
                    }
                }
            </style>

                    <!-- Tombol desktop -->
                    <div class="d-none d-md-flex col-md-auto">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modal_arsip_kelas" 
                                class="btn d-flex align-items-center justify-content-center border p-2 ms-2">
                                <i class="bi bi-archive me-3"></i>
                            <p class="m-0"> Arsip Kelas</p>
                        </button>
                    </div>


        <!-- Daftar Kelas -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 m-0">
            <?php if(mysqli_num_rows($result_kelas) > 0): 
                while($kelas = mysqli_fetch_assoc($result_kelas)): ?>
                <div class="col ps-0">
                    <div class="custom-card w-100">
                        <!-- Background Image -->
                        <?php if(!empty($kelas['background_image'])): ?>
                            <img src="<?php echo htmlspecialchars($kelas['background_image']); ?>" 
                                alt="Background Image" 
                                class="card-img-top">
                        <?php else: ?>
                            <img src="assets/bg.jpg" 
                                alt="Default Background Image" 
                                class="card-img-top">
                        <?php endif; ?>
                        
                        <!-- Profile Image -->
                        <div class="card-body" style="text-align: right; padding-right: 30px; background-color: white;">
                            <?php 
                            // Ambil data guru untuk kelas ini
                            $guru_id = $kelas['guru_id'];
                            $query_guru = "SELECT foto_profil FROM guru WHERE username = '$guru_id'";
                            $result_guru = mysqli_query($koneksi, $query_guru);
                            $data_guru = mysqli_fetch_assoc($result_guru);
                            ?>
                            <a href="profil_guru.php">
                                <img src="<?php echo !empty($data_guru['foto_profil']) ? 'uploads/profil/'.$data_guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                    alt="Profile Image" 
                                    class="profile-img rounded-4 border-0 bg-white">
                            </a>
                        </div>

                        <div class="ps-3">
                            <h5 class="mt-3 p-0 mb-1" style="font-weight: bold; font-size: 20px;">
                                <?php echo htmlspecialchars($kelas['mata_pelajaran']); ?>
                            </h5>
                            <p class="p-0 m-0" style="font-size: 12px;">
                                <?php echo htmlspecialchars($kelas['nama_guru']); ?>
                            </p>
                        </div>
                        <div class="d-flex btn-group gap-2 p-3">
                                    <a href="kelas.php?id=<?php echo $kelas['id']; ?>" 
                                    class="btn color-web w-45 rounded" 
                                    style="text-decoration: none; color: white;">
                                        Masuk
                                    </a>
                                    <div class="btn-group dropup w-25">
                                        <button type="button" class="btn btn-secondary dropdown-toggle rounded w-100" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end animate slideIn" style="margin-bottom:5px;">
                                            <style>
                                            .animate {
                                                animation-duration: 0.2s;
                                                animation-fill-mode: both;
                                            }
                                            
                                            @keyframes slideIn {
                                                from {
                                                    transform: translateY(10px);
                                                    opacity: 0;
                                                }
                                                to {
                                                    transform: translateY(0);
                                                    opacity: 1;
                                                }
                                            }

                                            .slideIn {
                                                animation-name: slideIn;
                                            }

                                            .dropdown-item {
                                                padding: 8px 16px;
                                                transition: background-color 0.2s;
                                            }

                                            .dropdown-item:hover {
                                                background-color: #f8f9fa;
                                            }

                                            .dropdown-item i {
                                                margin-right: 8px;
                                                width: 16px;
                                            }
                                            </style>
                                            <li>
                                                <a class="dropdown-item" href="archive_kelas_siswa.php?id=<?php echo $kelas['id']; ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin mengarsipkan kelas ini?');">
                                                    <i class="bi bi-archive"></i> Arsipkan
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <style>
                                        .hapus {
                                            z-index: 0;
                                        }
                                    </style>
                                </div>
                    </div>
                </div>
            <?php endwhile; 
            else: ?>
                <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                    <p class="text-muted">Kamu tidak memiliki kelas saat ini, silahkan hubungi guru untuk memasukkan kamu ke dalam kelasnya.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


        <!-- modal untuk gabung kelas -->
     <!-- Modal -->
     <div class="modal fade" id="modal_tambah_kelas" tabindex="-1" aria-labelledby="label_tambah_kelas" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="label_tambah_kelas" style="font-weight: bold;">Gabung Kelas</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- masukkan kode kelas -->
                 <div>
                    <label for="input_kode_kelas" class="form-label">Kode Kelas</label>
                    <input type="text" class="form-control" id="input_kode_kelas" placeholder="Masukkan kode kelas"></label>
                 </div>

            </div>
            <div class="modal-footer d-flex">
            <button type="button" class="btn color-web text-white flex-fill">Masuk</button>
            </div>
        </div>
        </div>
    </div>


    <!-- Modal Arsip Kelas -->
<div class="modal fade" id="modal_arsip_kelas" tabindex="-1" aria-labelledby="label_arsip_kelas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content rounded-4 border-0">
            <!-- Header -->
            <div class="modal-header border-0 px-4 pt-4">
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

                if(mysqli_num_rows($result_arsip) > 0): ?>
                    <div class="row g-4">
                        <?php while($kelas_arsip = mysqli_fetch_assoc($result_arsip)): ?>
                            <div class="col-12">
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
                                                    <img src="<?php echo !empty($kelas_arsip['guru_foto']) ? 'uploads/profil/'.$kelas_arsip['guru_foto'] : 'assets/pp.png'; ?>" 
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