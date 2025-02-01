<?php
session_start();
require "koneksi.php";

// Cek apakah user sudah login dan merupakan siswa
if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

// Ambil ID kelas dari parameter URL
if(!isset($_GET['id'])) {
    header("Location: beranda.php");
    exit();
}
$kelas_id = $_GET['id'];

// Validasi akses kelas
$userid = $_SESSION['userid'];
$query_akses = "SELECT k.* FROM kelas k 
                JOIN kelas_siswa ks ON k.id = ks.kelas_id 
                JOIN siswa s ON ks.siswa_id = s.id 
                WHERE s.username = '$userid' AND k.id = '$kelas_id'";
$result_akses = mysqli_query($koneksi, $query_akses);

if(mysqli_num_rows($result_akses) == 0) {
    header("Location: beranda.php");
    exit();
}

$kelas = mysqli_fetch_assoc($result_akses);

// Ambil informasi guru yang mengajar kelas ini
$guru_id = $kelas['guru_id'];
$query_guru = "SELECT * FROM guru WHERE username = '$guru_id'";
$result_guru = mysqli_query($koneksi, $query_guru);
$guru = mysqli_fetch_assoc($result_guru);

// Ambil postingan di kelas ini
$query_postingan = "SELECT pk.*, 
                    g.namaLengkap as nama_guru,
                    g.foto_profil as foto_guru,
                    g.jabatan as jabatan_guru
                    FROM postingan_kelas pk
                    JOIN guru g ON pk.user_id = g.username
                    WHERE pk.kelas_id = '$kelas_id'
                    ORDER BY pk.created_at DESC";
$result_postingan = mysqli_query($koneksi, $query_postingan);

// Ambil jumlah siswa di kelas ini
$query_jumlah_siswa = "SELECT COUNT(*) as total FROM kelas_siswa WHERE kelas_id = '$kelas_id'";
$result_jumlah = mysqli_query($koneksi, $query_jumlah_siswa);
$jumlah_siswa = mysqli_fetch_assoc($result_jumlah)['total'];

// Fungsi untuk mengecek apakah user sudah like postingan
function sudahLike($postingan_id, $user_id, $koneksi) {
    $query = "SELECT * FROM likes_postingan 
              WHERE postingan_id = '$postingan_id' 
              AND user_id = '$user_id'";
    $result = mysqli_query($koneksi, $query);
    return mysqli_num_rows($result) > 0;
}

// Fungsi untuk menghitung jumlah like pada postingan
function hitungLike($postingan_id, $koneksi) {
    $query = "SELECT COUNT(*) as total FROM likes_postingan 
              WHERE postingan_id = '$postingan_id'";
    $result = mysqli_query($koneksi, $query);
    return mysqli_fetch_assoc($result)['total'];
}

// Fungsi untuk menghitung jumlah komentar pada postingan
function hitungKomentar($postingan_id, $koneksi) {
    $query = "SELECT COUNT(*) as total FROM komentar_postingan 
              WHERE postingan_id = '$postingan_id'";
    $result = mysqli_query($koneksi, $query);
    return mysqli_fetch_assoc($result)['total'];
}

// Ambil komentar untuk setiap postingan
function ambilKomentar($postingan_id, $koneksi) {
    $query = "SELECT kp.*, s.nama as nama_siswa, s.foto_profil as foto_siswa,
              g.namaLengkap as nama_guru, g.foto_profil as foto_guru,
              IF(s.id IS NOT NULL, 'siswa', 'guru') as user_type
              FROM komentar_postingan kp
              LEFT JOIN siswa s ON kp.user_id = s.username
              LEFT JOIN guru g ON kp.user_id = g.username
              WHERE kp.postingan_id = '$postingan_id'
              ORDER BY kp.created_at DESC";
    return mysqli_query($koneksi, $query);
}

$query = "SELECT s.*, 
    k.nama_kelas AS kelas_saat_ini 
    FROM siswa s 
    LEFT JOIN kelas_siswa ks ON s.id = ks.siswa_id 
    LEFT JOIN kelas k ON ks.kelas_id = k.id 
    WHERE s.username = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Kelas - SMAGAEdu</title>
</head>
<style>
        body{ 
            font-family: merriweather;
        }

        .color-web {
            background-color: rgb(218, 119, 86);
        }

        .btn {
            background-color: rgb(218, 119, 86);
            border: 0;
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
            <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="#">
                <img src="assets/logo_white.png" alt="" width="30px" class="logo_putih">
            <div>
                    <h1 class="p-0 m-0" style="font-size: 20px;">Kelas</h1>
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
                        <a href="beranda.php" class="text-decoration-none text-black">
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
                            <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp-siswa.png'; ?>" 
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
            
            <style>
                    .col-inti {
                        margin-left: 0;
                        margin-top: 56px; /* Height of mobile navbar */
                        padding-right: 0 !important; /* Remove right padding */
                        max-width: 100%; /* Ensure content doesn't overflow */                            
                    }
                        @media (min-width: 768px) {
                            .col-inti {
                                    margin-left: 13rem;
                                    margin-top: 0;
                                }
                            }                
                            @media screen and (max-width: 768px) {
                                            .textPostingan {
                                                font-size: 14px;
                                            }   

                                        }

                                        @media screen and (max-width: 768px) {
                                    .sapa1 {
                                        display: none;
                                    }
                                    .sapa2{
                                        display: block;
                                    }
                                    .buatPosting {
                                        border: none!important;
                                        box-shadow: none!important;
                                    }
                                }
                                @media screen and (min-width: 768px) {
                                    .sapa1 {
                                        display: block;
                                    }
                                    .sapa2{
                                        display: none;
                                    }
                                }
                                

                </style>


<div class="col col-inti p-0 p-md-3">
    <div class="text-white shadow latar-belakang mx-2 mx-md-0 position-relative" style="height: 200px; margin-top: 15px;">
        <!-- Background dengan brightness filter -->
        <div style="
            background-image: url(<?php echo !empty($kelas['background_image']) ? $kelas['background_image'] : 'assets/bg.jpg'; ?>);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-position: center;
            background-size: cover;
            filter: brightness(0.5);" class="rounded">
        </div>
        
        <!-- Konten text di atas background -->
        <div class="position-relative h-100">
            <div class="position-absolute bottom-0 start-0 p-3">
                <h5 class="display-5 p-0 m-0" style="font-weight: bold; font-size: clamp(24px, 5vw, 35px);">
                    <?php echo htmlspecialchars($kelas['mata_pelajaran']); ?>
                </h5>
                <h4 class="p-0 m-0 pb-3" style="font-size: clamp(16px, 4vw, 24px);">
                    Kelas <?php echo htmlspecialchars($kelas['tingkat']); ?>
                </h4>
            </div>
        </div>
    </div>
</div>


    <div class="row m-0 mt-4">
        <div class="col-12 col-utama col-lg-7 p-0">
            <!-- Tampilkan postingan -->
        <?php if(mysqli_num_rows($result_postingan) > 0): ?>
            <?php while($postingan = mysqli_fetch_assoc($result_postingan)): 
                $jumlah_like = hitungLike($postingan['id'], $koneksi);
                $jumlah_komentar = hitungKomentar($postingan['id'], $koneksi);
                $sudah_like = sudahLike($postingan['id'], $_SESSION['userid'], $koneksi);
            ?>
            <style>
                .col-utama {
                    margin-left: 13rem;
                    width: 55%;
                }
                @media screen and (max-width: 768px) {
                    .col-utama {
                        margin-left: 0;
                        width: 100%;
                    }
                }
            </style>
            <div class="p-md-3 mb-4 rounded-3 bg-white mx-md-0 postingan p-4" 
                 style="border: 1px solid rgb(226, 226, 226);">
                <div class="d-flex gap-3">
                    <div>
                        <img src="<?php echo !empty($postingan['foto_guru']) ? 'uploads/profil/'.$postingan['foto_guru'] : 'assets/pp.png'; ?>" 
                             alt="" width="40px" class="rounded-circle">
                    </div>
                    <div class="">
                        <h6 class="p-0 m-0"><?php echo htmlspecialchars($postingan['nama_guru']); ?></h6>
                        <p class="p-0 m-0 text-muted" style="font-size: 12px;">
                            Diposting pada <?php echo date('d F Y', strtotime($postingan['created_at'])); ?>
                        </p>
                    </div>
                </div>
                <div class="">
                    <div class="mt-3">
                        <p class="textPostingan"><?php echo nl2br(htmlspecialchars($postingan['konten'])); ?></p>
                    </div>
                    <!-- lampiran -->
                    <!-- Tambahkan di bagian postingan setelah konten -->
                    <div class="mt-3">
                        <?php
                        $postingan_id = $postingan['id'];
                        $query_lampiran = "SELECT * FROM lampiran_postingan WHERE postingan_id = '$postingan_id'";
                        $result_lampiran = mysqli_query($koneksi, $query_lampiran);

                        if(mysqli_num_rows($result_lampiran) > 0) {
                            echo '<div class="container mt-3 p-2 bg-light rounded">';
                            
                            $images = [];
                            $documents = [];
                            
                            while($lampiran = mysqli_fetch_assoc($result_lampiran)) {
                                if(strpos($lampiran['tipe_file'], 'image') !== false) {
                                    $images[] = $lampiran;
                                } else {
                                    $documents[] = $lampiran;
                                }
                            }

                            if(!empty($images)) {
                                $imageCount = count($images);
                                $gridClass = '';
                                
                                // Tentukan class berdasarkan jumlah gambar
                                switch($imageCount) {
                                    case 1:
                                        $gridClass = 'single-image';
                                        break;
                                    case 2:
                                        $gridClass = 'two-images';
                                        break;
                                    case 3:
                                        $gridClass = 'three-images';
                                        break;
                                    case 4:
                                        $gridClass = 'four-images';
                                        break;
                                    default:
                                        $gridClass = 'four-images'; // Default untuk > 4 gambar
                                }

                                echo '<div id="imageContainer-'.$postingan['id'].'" class="'.$gridClass.' mb-3">';
                                foreach($images as $index => $image) {
                                    if($index < 4) { // Hanya tampilkan maksimal 4 gambar
                                        echo '<img src="'.$image['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                    }
                                }
                                echo '</div>';
                                
                                echo '<style>
                                    .single-image img {
                                        width: 100%;
                                        max-height: 500px;
                                        object-fit: cover;
                                        border-radius: 8px;
                                        cursor: pointer;
                                    }
                                    .two-images {
                                        display: grid;
                                        grid-template-columns: 1fr 1fr;
                                        gap: 4px;
                                    }
                                    .two-images img {
                                        width: 100%;
                                        height: 300px;
                                        object-fit: cover;
                                        border-radius: 8px;
                                        cursor: pointer;
                                    }
                                    .three-images {
                                        display: grid;
                                        grid-template-areas: 
                                            "img1 img2"
                                            "img3 img3";
                                        gap: 4px;
                                    }
                                    .three-images img:nth-child(1) { grid-area: img1; }
                                    .three-images img:nth-child(2) { grid-area: img2; }
                                    .three-images img:nth-child(3) { grid-area: img3; }
                                    .three-images img {
                                        width: 100%;
                                        height: 300px;
                                        object-fit: cover;
                                        border-radius: 8px;
                                        cursor: pointer;
                                    }
                                    .four-images {
                                        display: grid;
                                        grid-template-columns: 1fr 1fr;
                                        gap: 4px;
                                    }
                                    .four-images img {
                                        width: 100%;
                                        height: 250px;
                                        object-fit: cover;
                                        border-radius: 8px;
                                        cursor: pointer;
                                    }
                                </style>';
                            }

                            if(!empty($documents)) {
                                echo '<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content" style="background: rgba(255,255,255,0.95); border-radius: 15px;">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title">Preview Gambar</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0 d-flex justify-content-center align-items-center" 
                                                 style="min-height: 300px; background: #f8f9fa;">
                                                <img src="" id="modalImage" class="img-fluid" 
                                                     style="max-height: 70vh; object-fit: contain; border-radius: 8px;">
                                            </div>
                                            <div class="modal-footer border-0" style="text-align: right;">
                                                <a href="" id="downloadBtn" class="btn color-web text-white" download>
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                function showImage(imgSrc) {
                                    // Set image source for preview
                                    document.getElementById("modalImage").src = imgSrc;
                                    
                                    // Set download link
                                    document.getElementById("downloadBtn").href = imgSrc;
                                    
                                    // Show modal
                                    var modal = new bootstrap.Modal(document.getElementById("imageModal"));
                                    modal.show();
                                }
                                </script>';
                                echo '<div class="document-list">';
                                foreach($documents as $doc) {
                                    $extension = pathinfo($doc['nama_file'], PATHINFO_EXTENSION);
                                    $icon = 'bi-file-text-fill text-secondary';
                                    
                                    switch(strtolower($extension)) {
                                        case 'pdf': $icon = 'bi-file-pdf-fill text-danger'; break;
                                        case 'doc': case 'docx': $icon = 'bi-file-word-fill text-primary'; break;
                                        case 'xls': case 'xlsx': $icon = 'bi-file-excel-fill text-success'; break;
                                        case 'ppt': case 'pptx': $icon = 'bi-file-ppt-fill text-warning'; break;
                                    }
                                    
                                    echo '<div class="doc-item mb-2 p-2 bg-white rounded border">';
                                    echo '<a href="'.$doc['path_file'].'" class="text-decoration-none text-dark d-flex align-items-center gap-2" target="_blank">';
                                    echo '<i class="bi '.$icon.' fs-4"></i>';
                                    echo '<div><div class="doc-name">'.htmlspecialchars($doc['nama_file']).'</div>';
                                    echo '<small class="text-muted">'.strtoupper($extension).' file</small></div>';
                                    echo '</a></div>';
                                }
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <!-- Like dan Komentar info -->
                    <div class="mt-3 d-flex gap-3">
                        <p><strong><?php echo $jumlah_like; ?></strong> Suka</p>
                        <p><strong><?php echo $jumlah_komentar; ?></strong> Pendapat</p>
                    </div>
                    <!-- Tombol aksi -->
                    <div class="d-flex gap-2 justify-content-between mt-3 ps-2 pe-2" style="font-size: 14px;">

                    <!-- Ganti bagian tombol like yang lama dengan ini -->
                    <button class="btnPrimary btn text-white flex-fill py-1 py-md-2" 
                            id="like-btn-<?php echo $postingan['id']; ?>"
                            onclick="likePost(<?php echo $postingan['id']; ?>)">
                        <i class="<?php echo $sudah_like ? 'bi bi-arrow-up-circle-fill' : 'bi bi-arrow-up-circle'; ?>"></i>
                        <span id="like-count-<?php echo $postingan['id']; ?>"><?php echo $jumlah_like; ?></span>
                        <span class="d-none d-md-inline">Suka</span>
                    </button>


                        <button class="btn bi-chat flex-fill text-white py-1 py-md-2" 
                                onclick="showComments(<?php echo $postingan['id']; ?>)"
                                data-bs-toggle="modal" data-bs-target="#commentModal<?php echo $postingan['id']; ?>">
                            <span class="d-none d-md-inline">Pendapat</span>
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-4 rounded-3 bg-white" style="border: 1px solid rgb(226, 226, 226);">
                <i class="bi bi-search text-muted" style="font-size: 50px;"></i>
                <p class="text-muted mb-1" style="font-size: 16px;">Belum ada postingan</p>
                <p class="text-muted mb-0" style="font-size: 14px;">Guru belum membuat postingan apapun</p>
            </div>
        <?php endif; ?>
        </div>


        <!-- col untuk samping -->
        <div class="col">
            <?php
            // Query untuk mengambil catatan guru
            $query_catatan = "SELECT c.*, g.namaLengkap as nama_guru, g.foto_profil as foto_guru 
                            FROM catatan_guru c 
                            JOIN guru g ON c.guru_id = g.username 
                            WHERE c.kelas_id = $kelas_id 
                            ORDER BY c.created_at DESC";
            $result_catatan = mysqli_query($koneksi, $query_catatan);
            ?>

            <!-- Tampilan Catatan Guru -->
            <div class="catatan-guru mb-4 border rounded-3 p-3 bg-white catatanGuru">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0"><i class="bi bi-journal-text me-2"></i><strong>Catatan Guru</strong></h5>
                    <span class="badge bg-secondary"><?php echo mysqli_num_rows($result_catatan); ?> Catatan</span>
                </div>

                <?php if(mysqli_num_rows($result_catatan) > 0): ?>
                    <div class="catatan-list">
                        <?php while($catatan = mysqli_fetch_assoc($result_catatan)): ?>
                            <div class="catatan-item p-3 rounded-3 mb-3 border-start border-4" 
                                 style="background-color: #f8f9fa; border-left-color: rgb(218, 119, 86)!important;">
                                <!-- Header Catatan -->
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <img src="<?php echo !empty($catatan['foto_guru']) ? 'uploads/profil/'.$catatan['foto_guru'] : 'assets/pp.png'; ?>" 
                                         class="rounded-circle" width="40" height="40" 
                                         style="object-fit: cover;">
                                    <div>
                                        <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($catatan['judul']); ?></h6>
                                        <div class="d-flex gap-3 text-muted" style="font-size: 12px;">
                                            <span class="d-flex align-items-center gap-1">
                                                <i class="bi bi-calendar3"></i>
                                                <?php echo date('d M Y', strtotime($catatan['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Konten Catatan -->
                                <div class="catatan-content">
                                    <p class="mb-3" style="font-size: 14px; line-height: 1.6;">
                                        <?php echo nl2br(htmlspecialchars($catatan['konten'])); ?>
                                    </p>
                                    <?php if($catatan['file_lampiran']): ?>
                                        <a href="<?php echo htmlspecialchars($catatan['file_lampiran']); ?>" 
                                           class="btn btn-light btn-sm d-inline-flex align-items-center gap-2 border"
                                           target="_blank">
                                            <?php
                                            $ext = pathinfo($catatan['file_lampiran'], PATHINFO_EXTENSION);
                                            $icon = 'bi-file-earmark';
                                            switch($ext) {
                                                case 'pdf': $icon = 'bi-file-pdf-fill text-danger'; break;
                                                case 'doc': case 'docx': $icon = 'bi-file-word-fill text-primary'; break;
                                                case 'jpg': case 'jpeg': case 'png': $icon = 'bi-file-image-fill text-success'; break;
                                            }
                                            ?>
                                            <i class="bi <?php echo $icon; ?>"></i>
                                            <span>Lihat Lampiran</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 bg-light rounded-4">
                        <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                        <h6 class="fw-bold mb-2">Belum Ada Catatan</h6>
                        <p class="text-muted mb-0" style="font-size: 12px;">
                            Guru belum menambahkan catatan
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
                        <!-- style untuk catatan guru -->
                         <style>
                            @media screen and (max-width: 768px) {
                                .catatanGuru {
                                    display: none;
                                }
                            }
                         </style>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <!-- Floating Action Button for mobile -->
    <button class="btn btn-primary rounded-circle position-fixed d-md-none" 
        style="bottom: 20px; right: 20px; width: 56px; height: 56px; background-color: rgb(218, 119, 86); border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
        data-bs-toggle="modal" 
        data-bs-target="#catatanModal">
        <i class="bi bi-journal-text fs-4"></i>
    </button>

    <!-- Modal Catatan for mobile -->
    <div class="modal fade" id="catatanModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">
                <i class="bi bi-journal-text me-2"></i>
                <strong>Catatan Guru</strong>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            <?php if(mysqli_num_rows($result_catatan) > 0): ?>
                <?php 
                mysqli_data_seek($result_catatan, 0);
                while($catatan = mysqli_fetch_assoc($result_catatan)): 
                ?>
                <div class="catatan-item p-3 rounded-3 mb-3 border-start border-4" 
                     style="background-color: #f8f9fa; border-left-color: rgb(218, 119, 86)!important;">
                    <div class="d-flex align-items-start gap-3 mb-3">
                    <img src="<?php echo !empty($catatan['foto_guru']) ? 'uploads/profil/'.$catatan['foto_guru'] : 'assets/pp.png'; ?>" 
                         class="rounded-circle" width="40" height="40" 
                         style="object-fit: cover;">
                    <div>
                        <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($catatan['judul']); ?></h6>
                        <div class="d-flex gap-3 text-muted" style="font-size: 12px;">
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-calendar3"></i>
                            <?php echo date('d M Y', strtotime($catatan['created_at'])); ?>
                        </span>
                        </div>
                    </div>
                    </div>
                    <div class="catatan-content">
                    <p class="mb-3" style="font-size: 14px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($catatan['konten'])); ?>
                    </p>
                    <?php if($catatan['file_lampiran']): ?>
                        <a href="<?php echo htmlspecialchars($catatan['file_lampiran']); ?>" 
                           class="btn btn-light btn-sm d-inline-flex align-items-center gap-2 border"
                           target="_blank">
                        <i class="bi bi-file-earmark"></i>
                        <span>Lihat Lampiran</span>
                        </a>
                    <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5">
                <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                <h6 class="fw-bold mb-2">Belum Ada Catatan</h6>
                <p class="text-muted mb-0" style="font-size: 12px;">
                    Guru belum menambahkan catatan
                </p>
                </div>
            <?php endif; ?>
            </div>
        </div>
        </div>
    </div>

    <!-- Tambahkan ini di bagian bawah file, sebelum closing body tag -->

<!-- Modal Komentar untuk setiap postingan -->
<?php 
mysqli_data_seek($result_postingan, 0); // Reset pointer hasil query
while($postingan = mysqli_fetch_assoc($result_postingan)): 
?>
                                <div class="modal fade" id="commentModal<?php echo $postingan['id']; ?>" tabindex="-1" aria-labelledby="modalKomentar" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                        <div class="modal-content">
                                            <?php
                                            $query_komentar = "SELECT k.*,
                                                    g.namaLengkap as nama_guru, g.foto_profil as foto_guru,
                                                    s.nama as nama_siswa, s.foto_profil as foto_siswa,
                                                    CASE 
                                                        WHEN g.username IS NOT NULL THEN 'guru'
                                                        WHEN s.username IS NOT NULL THEN 'siswa'
                                                    END as user_type
                                                    FROM komentar_postingan k
                                                    LEFT JOIN guru g ON k.user_id = g.username 
                                                    LEFT JOIN siswa s ON k.user_id = s.username
                                                    WHERE k.postingan_id = '{$postingan['id']}'
                                                    ORDER BY k.created_at ASC";
                                            $result_komentar = mysqli_query($koneksi, $query_komentar);
                                            ?>

                                            <div class="modal-header border-0">
                                                <h1 class="modal-title fs-5" id="modalKomentar">
                                                    <strong>Pendapat</strong>
                                                    <span class="text-muted fs-6 ms-2"><?php echo mysqli_num_rows($result_komentar); ?></span>
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            
                                            <!-- Body Komentar dengan Scroll -->
                                            <div class="modal-body p-0">
                                                <div class="komentar-container px-3" style="max-height: 60vh; overflow-y: auto;">
                                                    <?php
                                                    if(mysqli_num_rows($result_komentar) > 0) {
                                                        while($komentar = mysqli_fetch_assoc($result_komentar)) {
                                                        ?>
                                                            <div class="d-flex gap-3 mb-3">
                                                                <div class="flex-shrink-0">
                                                                    <?php if($komentar['user_type'] == 'guru'): ?>
                                                                        <img src="<?php echo $komentar['foto_guru'] ? 'uploads/profil/'.$komentar['foto_guru'] : 'assets/pp.png'; ?>" 
                                                                            alt="" width="32px" height="32px" class="rounded-circle border">
                                                                    <?php else: ?>
                                                                        <img src="<?php echo $komentar['foto_siswa'] ? $komentar['foto_siswa'] : 'assets/pp-siswa.png'; ?>" 
                                                                            alt="" width="32px" height="32px" class="rounded-circle border">
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="bubble-chat flex-grow-1">
                                                                    <div class="rounded-4 p-3" style="background-color: <?php echo $komentar['user_type'] == 'guru' ? '#e3f2fd' : '#f0f2f5'; ?>;">
                                                                        <h6 class="p-0 m-0 mb-1" style="font-size: 13px; font-weight: 600;">
                                                                            <?php echo $komentar['user_type'] == 'guru' ? $komentar['nama_guru'] : $komentar['nama_siswa']; ?>
                                                                        </h6>
                                                                        <p class="p-0 m-0" style="font-size: 13px; line-height: 1.4;">
                                                                            <?php echo nl2br(htmlspecialchars($komentar['konten'])); ?>
                                                                        </p>
                                                                    </div>
                                                                    <small class="text-muted" style="font-size: 11px;">
                                                                        <?php echo date('d M Y, H:i', strtotime($komentar['created_at'])); ?>
                                                                    </small>
                                                                </div>
                                                            </div>

                                                        <?php
                                                        }
                                                    } else {
                                                        echo '<div class="text-center text-muted py-4">Belum ada pendapat</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                            <!-- Footer dengan Input Komentar -->
                                            <div class="modal-footer p-2 border-top">
                                                <div class="d-flex gap-2 align-items-end w-100">
                                                    <div class="flex-shrink-0">
                                                        <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp-siswa.png'; ?>" 
                                                        alt="Profile Picture" 
                                                        class="rounded-circle" 
                                                        style="width: 30px; height: 30px;padding-top:-60px ;object-fit: cover; z-index: 99999;">                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <form class="komentar-form" data-postid="<?php echo $postingan['id']; ?>">
                                                            <div class="form-group">
                                                            <textarea class="form-control bg-light border-0 comment-input-<?php echo $postingan['id']; ?>" 
                                                                rows="1" 
                                                                placeholder="Tulis pendapat kamu" 
                                                                style="resize: none; font-size: 14px;"
                                                                oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                                                required></textarea>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                    <button class="btn color-web text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                            style="width: 35px; height: 35px;"
                                                            onclick="submitKomentar(<?php echo $postingan['id']; ?>)">
                                                        <i class="bi bi-send-fill"></i>
                                                    </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
            <?php endwhile; ?>

<!-- JavaScript untuk handle like dan komentar -->
<script>
function likePost(postId) {
    // Ambil elemen button dan count
    const button = document.querySelector(`#like-btn-${postId} i`);
    const countElement = document.querySelector(`#like-count-${postId}`);
    
    if (!button || !countElement) {
        console.error('Button or count element not found:', postId);
        return;
    }
    
    // Cek status like
    const isCurrentlyLiked = button.classList.contains('bi-arrow-up-circle-fill');
    const currentCount = parseInt(countElement.textContent);

    // Update tampilan
    if (isCurrentlyLiked) {
        button.classList.remove('bi-arrow-up-circle-fill');
        button.classList.add('bi-arrow-up-circle');
        countElement.textContent = currentCount - 1;
    } else {
        button.classList.remove('bi-arrow-up-circle');
        button.classList.add('bi-arrow-up-circle-fill');
        countElement.textContent = parseInt(currentCount) + 1;
    }

    // Kirim request ke server
    fetch('like_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'postingan_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Berhasil - tampilan sudah diupdate sebelumnya
            console.log('Like berhasil diupdate');
        } else {
            // Gagal - kembalikan ke status sebelumnya
            if (isCurrentlyLiked) {
                button.classList.remove('bi-arrow-up-circle');
                button.classList.add('bi-arrow-up-circle-fill');
                countElement.textContent = currentCount;
            } else {
                button.classList.remove('bi-arrow-up-circle-fill');
                button.classList.add('bi-arrow-up-circle');
                countElement.textContent = currentCount;
            }
            console.error('Like gagal:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Error - kembalikan ke status sebelumnya
        if (isCurrentlyLiked) {
            button.classList.remove('bi-arrow-up-circle');
            button.classList.add('bi-arrow-up-circle-fill');
            countElement.textContent = currentCount;
        } else {
            button.classList.remove('bi-arrow-up-circle-fill');
            button.classList.add('bi-arrow-up-circle');
            countElement.textContent = currentCount;
        }
    });
}
</script>

                                  <!-- logika komentar -->
                                  <script>
function submitKomentar(postId) {
    const textarea = document.querySelector(`.comment-input-${postId}`);
    const konten = textarea.value.trim();
    
    if (!konten) return;

    fetch('tambah_komentar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `postingan_id=${postId}&konten=${encodeURIComponent(konten)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const container = document.querySelector(`#commentModal${postId} .komentar-container`);
            const bgColor = data.komentar.user_type === 'guru' ? '#e3f2fd' : '#f0f2f5';
            
            const komentarHTML = `
                <div class="d-flex gap-3 mb-3">
                    <div class="flex-shrink-0">
                        <img src="${data.komentar.foto_profil}" 
                             alt="" 
                             width="32px" 
                             height="32px" 
                             class="rounded-circle border"
                             style="object-fit: cover;">
                    </div>
                    <div class="bubble-chat flex-grow-1">
                        <div class="rounded-4 p-3" style="background-color: ${bgColor};">
                            <h6 class="p-0 m-0 mb-1" style="font-size: 13px; font-weight: 600;">
                                ${data.komentar.nama_user}
                            </h6>
                            <p class="p-0 m-0" style="font-size: 13px; line-height: 1.4;">
                                ${data.komentar.konten}
                            </p>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">
                            Baru saja
                        </small>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('afterbegin', komentarHTML);
            textarea.value = '';
            textarea.style.height = 'auto';

            const countElement = document.querySelector(`#commentModal${postId} .modal-title .text-muted`);
            if(countElement) {
                countElement.textContent = parseInt(countElement.textContent) + 1;
            }
        }
    });
}                                    </script>



</body>
</html>