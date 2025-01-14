<?php
session_start();
require "koneksi.php";
if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

// Ambil data guru
$userid = $_SESSION['userid'];
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);

// Ambil semua ujian yang dibuat oleh guru yang sedang login
$query_ujian = "SELECT u.*, k.mata_pelajaran FROM ujian u 
                JOIN kelas k ON u.kelas_id = k.id 
                WHERE u.guru_id = '$userid' 
                ORDER BY u.created_at DESC";
$result_ujian = mysqli_query($koneksi, $query_ujian);
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
    <title>Ujian - SMAGAEdu</title>
</head>
<style>
        .custom-card {
            width: 300px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
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

        @media (max-width: 768px) {
            .custom-card {
                width: 100%; /* Full width di mobile */
                max-width: 300px; /* Maximum width tetap 300px */
            }
        }

        .merriweather-light {
        font-family: "Merriweather", serif;
        font-weight: 300;
        font-style: normal;
        }

        .merriweather-regular {
        font-family: "Merriweather", serif;
        font-weight: 400;
        font-style: normal;
        }

        .merriweather-bold {
        font-family: "Merriweather", serif;
        font-weight: 700;
        font-style: normal;
        }

        .merriweather-black {
        font-family: "Merriweather", serif;
        font-weight: 900;
        font-style: normal;
        }

        .merriweather-light-italic {
        font-family: "Merriweather", serif;
        font-weight: 300;
        font-style: italic;
        }

        .merriweather-regular-italic {
        font-family: "Merriweather", serif;
        font-weight: 400;
        font-style: italic;
        }

        .merriweather-bold-italic {
        font-family: "Merriweather", serif;
        font-weight: 700;
        font-style: italic;
        }

        .merriweather-black-italic {
        font-family: "Merriweather", serif;
        font-weight: 900;
        font-style: italic;
        }
        body{ 
            font-family: merriweather;
        }
        @media (max-width: 768px) {
            body {
                padding-top: 56px; /* Sesuaikan dengan tinggi navbar */
            }
        }
        .color-web {
            background-color: rgb(218, 119, 86);
        }
        .btn {
                                transition: background-color 0.3s ease;
                                border: 0;
                                border-radius: 5px;
                            }
                            .btn:hover{
                                background-color: rgb(219, 106, 68);
                            }


</style>
<body>

    <!-- Navbar Mobile -->
    <nav class="navbar navbar-dark d-md-none color-web fixed-top">
        <div class="container-fluid">
            <!-- Logo dan Nama -->
            <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="beranda_guru.php">
                <img src="assets/logo_white.png" alt="" width="30px" class="logo_putih">
            <div>
                    <h1 class="p-0 m-0" style="font-size: 20px;">SMAGAEdu</h1>
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
                <div class="offcanvas-body">
                    <div class="d-flex flex-column gap-2">
                        <!-- Menu Beranda -->
                        <a href="beranda_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded  p-2">
                                <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 ">Beranda</p>
                            </div>
                        </a>
                        
                        <!-- Menu Cari -->
                        <a href="cari_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/pencarian.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Cari</p>
                            </div>
                        </a>
                        
                        <!-- Menu Ujian -->
                        <a href="ujian_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center color-web rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Ujian</p>
                            </div>
                        </a>
                        
                        <!-- Menu Profil -->
                        <a href="profil_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/profil_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Profil</p>
                            </div>
                        </a>
                        
                        <!-- Menu AI -->
                        <a href="ai.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Gemini</p>
                            </div>
                        </a>
                        
                        <!-- Menu Bantuan -->
                        <a href="bantuan_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/bantuan_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Bantuan</p>
                            </div>
                        </a>
                    </div>
                    
                <!-- Profile Dropdown -->
                <div class="mt-3 dropdown"> <!-- Tambahkan class dropdown di sini -->
                    <button class="btn d-flex align-items-center gap-3 p-2 rounded-3 border w-100" 
                            style="background-color: #F8F8F7;" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                                <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>"  width="30px" class="rounded-circle" style="background-color: white;">
                            <p class="p-0 m-0" style="font-size: 12px;"><?php echo $guru['namaLengkap']; ?></p>
                    </button>
                    <ul class="dropdown-menu w-100" style="font-size: 12px;"> <!-- Tambahkan w-100 agar lebar sama -->
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    
     <!-- row col untuk halaman utama -->
     <div class="container-fluid">
        <div class="row">
            <div class="col-3 col-md-2 vh-100 p-4 shadow-sm menu-samping" style="background-color:rgb(238, 236, 226)">
                <style>
                    .menu-samping {
                        position: fixed;
                        width: 13rem;
                        z-index: 1000;
                    }
                    @media (max-width: 768px) {
                        .menu-samping {
                            display: none;
                        }
                    }                
                </style>
                <div class="row gap-0">
                    <div class="ps-3 mb-3">
                        <a href="beranda_guru.php" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
                            <img src="assets/smagaedu.png" alt="" width="30px">
                            <div>
                                <h1 class="display-5  p-0 m-0" style="font-size: 20px; text-decoration: none;">SMAGAEdu</h1>
                                <p class="p-0 m-0 text-muted" style="font-size: 12px;">LMS</p>
                            </div>
                        </a>
                    </div>  
                    <div class="col">
                        <a href="beranda_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/beranda_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Beranda</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="cari_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/pencarian.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Cari</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="ujian_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
                            <img src="assets/ujian_fill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Ujian</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="profil_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/profil_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Profil</p>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="row gap-0" style="margin-bottom: 15rem;">
                    <div class="col">
                        <a href="ai.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Gemini</p>
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
                <div class="row dropdown">
                    <div class="btn d-flex align-items-center gap-3 p-2 rounded-3 border dropdown-toggle" style="background-color: #F8F8F7;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                        <p class="p-0 m-0" style="font-size: 12px;">Halo, Ayundy</p>
                    </div>
                    <!-- dropdown menu option -->
                    <ul class="dropdown-menu" style="font-size: 12px;">
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="#">Keluar</a></li>
                      </ul>
                </div>
            </div>


            <!-- ini isi kontennya -->
            <div class="col p-4 col-utama">
                <style>
                    .col-utama{
                        margin-left: 13rem;
                    }
                    @media (max-width: 768px) {
                            .col-utama {
                                margin-left: 0;
                                margin-top: 10px; /* Untuk memberikan space dari fixed navbar mobile */
                            }
                    }
                </style>
                <div class="row justify-content-between align-items-center mb-1">
                    <!-- Tambahkan setelah div row justify-content-between -->
                    <?php if(isset($_GET['pesan'])): ?>
                        <div class="alert alert-dismissible fade show <?php 
                            echo $_GET['pesan'] == 'hapus_berhasil' ? 'alert-success' : 'alert-danger'; 
                        ?>" role="alert">
                            <?php
                            switch($_GET['pesan']) {
                                case 'hapus_berhasil':
                                    echo "Ujian berhasil dihapus";
                                    break;
                                case 'hapus_gagal':
                                    echo "Gagal menghapus ujian";
                                    break;
                                case 'tidak_ditemukan':
                                    echo "Ujian tidak ditemukan";
                                    break;
                            }
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <div class="col-12 col-md-auto mb-3 mb-md-0">
                        <h3 style="font-weight: bold;">Ujian</h3>
                    </div>
                    <!-- Tombol desktop -->
                    <div class="d-none d-md-block col-md-auto">
                        <a href="buat_ujian.php" 
                        class="btn d-flex align-items-center justify-content-center border p-2 text-decoration-none text-dark">
                            <img src="assets/tambah.png" alt="Tambah" width="25px" class="me-2">
                            <p class="m-0">Buat Ujian</p>
                        </a>                    
                    </div>

                    <!-- Floating button untuk mobile -->
                    <div class="position-fixed bottom-0 end-0 d-md-none m-4">
                        <a href="buat_ujian.php" class="text-decoration-none">
                        <button type="button" data-bs-toggle="modal"
                                class="btn color-web rounded-circle shadow d-flex align-items-center justify-content-center" 
                                style="width: 56px; height: 56px;">
                            <img src="assets/tambah.png" alt="Tambah" width="25px" class="m-0" style="filter: brightness(0) invert(1);">
                        </button>
                        </a>
                    </div>

                    <style>
                        /* Animasi hover untuk floating button */
                        .position-fixed.bottom-0.end-0 {
                                margin-right: -75% !important; /* Memastikan tidak ada margin yang mengganggu */
                            }
                            .btn.color-web {
                                transition: transform 0.3s ease, box-shadow 0.3s ease;
                            }
                            .btn.color-web:hover {
                                transform: scale(1.1);
                                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                            }                    
                    </style>
                </div>

                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php 
                    if(mysqli_num_rows($result_ujian) > 0) {
                        while($ujian = mysqli_fetch_assoc($result_ujian)) { 
                    ?>
                        <div class="col">
                            <div class="custom-card">
                                <img src="assets/bg.jpg" alt="Background Image">
                                <div class="card-body" style="text-align: right; padding-right: 30px; background-color: white;">
                                    <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" alt="Profile Image" class="profile-img rounded-4 border-0 bg-white">
                                </div>
                                <div class="ps-3">
                                    <h5 class="mt-3 p-0 mb-1" style="font-weight: bold; font-size: 20px;">
                                        <?php echo htmlspecialchars($ujian['mata_pelajaran']); ?>
                                    </h5>
                                    <p class="p-0 m-0" style="font-size: 12px;">
                                        <?php echo htmlspecialchars($guru['namaLengkap']); ?>
                                    </p>
                                </div>
                                <div style="font-size: 12px;" class="ps-3 pt-2">
                                    <p class="p-0 m-0">Ujian dilaksanakan pada :</p>
                                    <p class="p-0 m-0">
                                        <?php echo date('l, d F Y', strtotime($ujian['tanggal_mulai'])); ?>
                                    </p>
                                </div>
                                <div class="d-flex btn-group gap-2 p-3">
                                    <a href="buat_soal.php?ujian_id=<?php echo $ujian['id']; ?>"
                                        class="btn color-web text-white w-45 rounded text-decoration-none">
                                        Lihat
                                    </a>
                                    <button onclick="hapusUjian(<?php echo $ujian['id']; ?>)" 
                                            class="btn btn-danger w-45 rounded">
                                        Hapus
                                    </button>
                                </div>                            
                            </div>
                        </div>
                    <?php 
                        }
                    } else {
                    ?>
                        <div class="container">
                            <div class="text-center position-absolute top-50 start-50 translate-middle text-center w-100">
                                <p>Belum ada ujian yang dibuat.</p>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>

    
            </div>
        </div>
    </div>

<script>
function hapusUjian(id) {
    if(confirm('Apakah Anda yakin ingin menghapus ujian ini? Semua soal yang terkait juga akan terhapus.')) {
        window.location.href = 'hapus_ujian.php?id=' + id;
    }
}
</script>
</body>
</html>