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
                        <a href="#" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded  p-2">
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
                            <div class="d-flex align-items-center color-web rounded p-2">
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
                <div class="mt-3 dropdown"> <!-- Tambahkan class dropdown di sini -->
                    <button class="btn d-flex align-items-center gap-3 p-2 rounded-3 border w-100" 
                            style="background-color: #F8F8F7;" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                        <p class="p-0 m-0 text-truncate" style="font-size: 12px;">Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?></p>
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
                        <a href="profil.php" class="text-decoration-none text-black">
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
                            <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
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
    <div style="background-image: url(<?php echo !empty($kelas['background_image']) ? $kelas['background_image'] : 'assets/bg.jpg'; ?>); 
                height: 200px; 
                width: 98%;
                padding-top: 120px; 
                margin-top: 15px; 
                background-position: center;
                background-size: cover;" 
         class="rounded text-white shadow latar-belakang mx-2 mx-md-0">
        <div class="ps-3" style="position: relative; z-index: 999;">
            <div>
                <h5 class="display-5 p-0 m-0" 
                    style="font-weight: bold; font-size: 28px; font-size: clamp(24px, 5vw, 35px);">
                    <?php echo htmlspecialchars($kelas['mata_pelajaran']); ?>
                </h5>
                <h4 class="p-0 m-0 pb-3" style="font-size: clamp(16px, 4vw, 24px);">
                    Kelas <?php echo htmlspecialchars($kelas['tingkat']); ?>
                </h4>       
            </div>
        </div>
    </div>

    <div class="row m-0 mt-4 p-4 ps-0 pt-0">
        <div class="col-12 col-lg-8 p-0">
            <!-- Tampilkan postingan -->
        <?php if(mysqli_num_rows($result_postingan) > 0): ?>
            <?php while($postingan = mysqli_fetch_assoc($result_postingan)): 
                $jumlah_like = hitungLike($postingan['id'], $koneksi);
                $jumlah_komentar = hitungKomentar($postingan['id'], $koneksi);
                $sudah_like = sudahLike($postingan['id'], $_SESSION['userid'], $koneksi);
            ?>
            <div class=" p-md-3 mb-4 rounded-3 bg-white mx-md-0 postingan p-4" 
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
                                echo '<div id="imageContainer-'.$postingan['id'].'" class="image-grid mb-3">';
                                foreach($images as $image) {
                                    echo '<img src="'.$image['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                }
                                echo '</div>';
                            }

                            if(!empty($documents)) {
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
$query_catatan = "SELECT c.*, g.namaLengkap as nama_guru 
                  FROM catatan_guru c 
                  JOIN guru g ON c.guru_id = g.username 
                  WHERE c.kelas_id = $kelas_id 
                  ORDER BY c.created_at DESC";
$result_catatan = mysqli_query($koneksi, $query_catatan);
?>

<!-- Tampilan Catatan Guru -->
<div class="catatan-guru mb-4 border rounded-3 p-3 bg-white catatanGuru">
    <h5 class="mb-3"><strong>Catatan Guru</strong></h5>
    
    <?php if(mysqli_num_rows($result_catatan) > 0): ?>
        <div class="catatan-list">
            <?php while($catatan = mysqli_fetch_assoc($result_catatan)): ?>
                <div class="catatan-item p-3 rounded-3 mb-3" style="background-color:rgb(255, 239, 216); border: 1px solid #e9ecef;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1"><strong><?php echo htmlspecialchars($catatan['judul']); ?></strong></h6>
                            <small class="text-muted d-flex align-items-center gap-1">
                                <i class="bi bi-calendar3"></i>
                                <?php echo date('d M Y', strtotime($catatan['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                    
                    <div class="catatan-content mt-2">
                        <p class="mb-2" style="font-size: 14px;">
                            <?php echo nl2br(htmlspecialchars($catatan['konten'])); ?>
                        </p>
                        <?php if($catatan['file_lampiran']): ?>
                            <div class="file-attachment p-2 rounded-2 d-inline-flex align-items-center gap-2" 
                                 style="background-color: white; border: 1px solid #dee2e6;">
                                <?php
                                $ext = pathinfo($catatan['file_lampiran'], PATHINFO_EXTENSION);
                                $icon = 'bi-file-earmark';
                                switch($ext) {
                                    case 'pdf': $icon = 'bi-file-pdf'; break;
                                    case 'doc': case 'docx': $icon = 'bi-file-word'; break;
                                    case 'jpg': case 'jpeg': case 'png': $icon = 'bi-file-image'; break;
                                }
                                ?>
                                <i class="bi <?php echo $icon; ?>"></i>
                                <a href="<?php echo htmlspecialchars($catatan['file_lampiran']); ?>" 
                                   class="text-decoration-none text-black" target="_blank">
                                    Lihat Lampiran
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-4" style="background-color: #f8f9fa; border-radius: 8px;">
            <p class="text-muted mb-1" style="font-size: 16px;">Tidak ada catatan</p>
            <p class="text-muted mb-0" style="font-size: 14px;">Guru belum menambahkan catatan apapun</p>
        </div>
    <?php endif; ?>
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
                                            $query_komentar = "SELECT k.*, COALESCE(g.namaLengkap, s.nama) as nama_user 
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
                                                                    <img src="assets/pp.png" alt="" width="32px" height="32px" class="rounded-circle border">
                                                                </div>
                                                                <div class="bubble-chat flex-grow-1">
                                                                    <div class="rounded-4 p-3" style="background-color: #f0f2f5;">
                                                                        <h6 class="p-0 m-0 mb-1" style="font-size: 13px; font-weight: 600;">
                                                                            <?php echo htmlspecialchars($komentar['nama_user']); ?>
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
                                                    <img src="<?php echo !empty($_SESSION['foto_profil']) ? 'uploads/profil/'.$_SESSION['foto_profil'] : 'assets/pp.png'; ?>" alt="Profile" width="35px" height="35px" class="rounded-circle">
                                                    </div>
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
                                                                onclick="submitComment(<?php echo $postingan['id']; ?>)">
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
                                        const form = document.querySelector(`.komentar-form[data-postid="${postId}"]`);
                                        const textarea = form.querySelector('textarea');
                                        const konten = textarea.value.trim();

                                        if(!konten) return;

                                        // Kirim komentar ke server
                                        fetch('tambah_komentar.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/x-www-form-urlencoded',
                                            },
                                            body: `postingan_id=${postId}&konten=${encodeURIComponent(konten)}`
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if(data.status === 'success') {
                                                // Tambahkan komentar ke DOM
                                                const container = document.querySelector(`#commentModal${postId} .komentar-container`);
                                                const komentarHTML = `
                                                    <div class="d-flex gap-3 mb-3">
                                                        <div>
                                                            <img src="assets/pp.png" alt="" width="40px" class="rounded-circle border">
                                                        </div>
                                                        <div class="pt-2 pb-2 pe-4 ps-3 rounded-4" style="background-color: rgb(238, 238, 238);">
                                                            <h6 class="p-0 m-0" style="font-size: 12px;">${data.komentar.nama_user}</h6>
                                                            <p class="p-0 m-0" style="font-size: 14px;">${data.komentar.konten}</p>
                                                        </div>
                                                    </div>
                                                `;
                                                container.insertAdjacentHTML('beforeend', komentarHTML);
                                                
                                                // Reset textarea
                                                textarea.value = '';
                                                
                                                // Update jumlah komentar di postingan
                                                const countElement = document.querySelector(`#post-${postId} .comment-count`);
                                                const currentCount = parseInt(countElement.textContent);
                                                countElement.textContent = currentCount + 1;
                                            }
                                        });
                                    }
                                    </script>



</body>
</html>