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
        <div class="container-fluid px-0">
            <!-- Logo dan Nama -->
            <a class="navbar-brand ms-2 d-flex align-items-center gap-2 text-white" href="<?php echo ($_SESSION['level'] == 'guru') ? 'beranda_guru.php' : 'beranda.php'; ?>">
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
                            <div class="d-flex align-items-center rounded color-web p-2">
                                <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Beranda</p>
                            </div>
                        </a>
                        
                        <!-- Menu Cari -->
                        <a href="cari.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/pencarian.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Cari</p>
                            </div>
                        </a>
                        
                        <!-- Menu Ujian -->
                        <a href="ujian.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Ujian</p>
                            </div>
                        </a>
                        
                        <!-- Menu Profil -->
                        <a href="profil.php" class="text-decoration-none text-black">
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
                        <p class="p-0 m-0" style="font-size: 12px;">Halo, Ayundy</p>
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
    <div class="container-fluid px-0">
        <div class="row g-0">
        <div class="col-auto vh-100 p-2 p-md-4 shadow-sm menu-samping d-none d-md-block" style="background-color:rgb(238, 236, 226)">
                <style>
                    .menu-samping {
                        position: fixed;
                        width: 13rem;
                        z-index: 1000;
                    }
                </style>
                <div class="row gap-0">
                    <div class="ps-3 mb-3">
                        <a href="beranda.php" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
                            <img src="assets/smagaedu.png" alt="" width="30px" class="logo_orange">
                            <div>
                                <h1 class="display-5  p-0 m-0" style="font-size: 20px; text-decoration: none;">SMAGAEdu</h1>
                                <p class="p-0 m-0 text-muted" style="font-size: 12px;">LMS</p>
                            </div>
                        </a>
                    </div>  
                    <div class="col">
                        <a href="#" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
                            <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Beranda</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="cari.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/pencarian.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Cari</p>
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
                        <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                      </ul>
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

    <div class="row mt-4 p-4 pt-0">
        <div class="col-12 col-lg-8 p-0">
            <!-- Tampilkan postingan -->
            <?php while($postingan = mysqli_fetch_assoc($result_postingan)): 
                $jumlah_like = hitungLike($postingan['id'], $koneksi);
                $jumlah_komentar = hitungKomentar($postingan['id'], $koneksi);
                $sudah_like = sudahLike($postingan['id'], $_SESSION['userid'], $koneksi);
            ?>
            <div class="mt-4 p-md-3 mb-4 rounded-3 bg-white mx-md-0 postingan p-4" 
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
                    <!-- Like dan Komentar info -->
                    <div class="mt-3 d-flex gap-3">
                        <p><strong><?php echo $jumlah_like; ?></strong> Suka</p>
                        <p><strong><?php echo $jumlah_komentar; ?></strong> Pendapat</p>
                    </div>
                    <!-- Tombol aksi -->
                    <div class="d-flex gap-2 justify-content-between mt-3 ps-2 pe-2" style="font-size: 14px;">
                        <button class="btn bi-arrow-up-circle text-white flex-fill py-1 py-md-2 <?php echo $sudah_like ? 'active' : ''; ?>"
                                onclick="likePost(<?php echo $postingan['id']; ?>)">
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
        </div>


                    <!-- col untuk samping -->
                    <div class="col">
                        <div style="border: 1px solid rgb(238, 238, 238);"  class="tentangKelas p-3 rounded-3 gap-3 bg-white mb-3 shadow-sm" >
                            <h5><strong>Tentang Kelas ini</strong></p>
                                <div class="w-100">
                                    <p class="text-muted p-0 m-0" style="font-size: 14px;">Guru tidak memberikan deskripsi</p>
                                </div>
                                <div class="d-flex mt-3">
                                    <button class="btn text-white flex-fill" data-bs-toggle="modal" data-bs-target="#deskripsimodal">Edit</button>
                                </div>
                        </div>
                        <!-- style untuk tentang kelas -->
                         <style>
                            @media screen and (max-width: 768px) {
                                .tentangKelas {
                                    display: none;
                                }
                            }
                         </style>
                        <!-- modal untuk guru input deskripsi kelas -->
                                <div class="modal fade" id="deskripsimodal" tabindex="-1" aria-labelledby="modaldeskripsi" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="modaldeskripsi"><strong>Edit Deskripsi Kelas</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p style="font-size: 14px;">Apa ada deskripsi khusus untuk kelas Anda?</p>
                                                <div class="mt-3">
                                                    <div class="form-floating">
                                                        <textarea class="form-control" placeholder="Apa pendapat Anda?" id="pendapat" style="height: 100px;"></textarea>
                                                        <label for="pendapat">Kelas ini bertujuan untuk ..</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex">
                                                <button class="btn text-white flex-fill">Tambah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        <div style="border: 1px solid rgb(238, 238, 238);"  class="catatanGuru p-3 rounded-3 gap-3 bg-white shadow-sm" >
                            <h5><strong>Catatan Guru</strong></p>
                                <div class="w-100">
                                    <p class="text-muted p-0 m-0" style="font-size: 14px;">Guru tidak memberikan Catatan</p>
                                </div>
                                <div class="d-flex mt-3">
                                    <button class="btn btn-primary flex-fill">Tambah</button>
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

    <!-- Tambahkan ini di bagian bawah file, sebelum closing body tag -->

<!-- Modal Komentar untuk setiap postingan -->
<?php 
mysqli_data_seek($result_postingan, 0); // Reset pointer hasil query
while($postingan = mysqli_fetch_assoc($result_postingan)): 
?>
<div class="modal fade" id="commentModal<?php echo $postingan['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5"><strong>Pendapat</strong></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="comments-container-<?php echo $postingan['id']; ?> gap-3">
                    <?php 
                    $komentar_result = ambilKomentar($postingan['id'], $koneksi);
                    while($komentar = mysqli_fetch_assoc($komentar_result)): 
                    ?>
                    <div class="d-flex gap-3 mb-3">
                        <div>
                            <img src="<?php 
                                if($komentar['user_type'] == 'siswa') {
                                    echo !empty($komentar['foto_siswa']) ? 'uploads/profil/'.$komentar['foto_siswa'] : 'assets/pp-siswa.png';
                                } else {
                                    echo !empty($komentar['foto_guru']) ? 'uploads/profil/'.$komentar['foto_guru'] : 'assets/pp.png';
                                }
                            ?>" alt="" width="40px" class="rounded-circle border">
                        </div>
                        <div class="pt-2 pb-2 pe-4 ps-3 rounded-4" style="background-color: rgb(238, 238, 238);">
                            <h6 class="p-0 m-0" style="font-size: 12px;">
                                <?php echo $komentar['user_type'] == 'siswa' ? $komentar['nama_siswa'] : $komentar['nama_guru']; ?>
                            </h6>
                            <p class="p-0 m-0" style="font-size: 14px;"><?php echo htmlspecialchars($komentar['konten']); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="modal-footer d-flex align-items-center">
                <div class="me-3">
                    <img src="<?php echo !empty($_SESSION['foto_profil']) ? 'uploads/profil/'.$_SESSION['foto_profil'] : 'assets/pp-siswa.png'; ?>" 
                         alt="Profile Picture" width="40px" class="rounded-circle">
                </div>
                <div class="flex-fill">
                    <div class="form-floating">
                        <textarea class="form-control comment-input-<?php echo $postingan['id']; ?>" 
                                  placeholder="Pendapat Anda" style="height: 60px;"></textarea>
                        <label>Pendapat Anda</label>
                    </div>
                </div>
                <button class="btn bi-send" onclick="submitComment(<?php echo $postingan['id']; ?>)"></button>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>

<!-- JavaScript untuk handle like dan komentar -->
<script>
function likePost(postId) {
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
            location.reload(); // Refresh untuk update tampilan
        }
    });
}

function submitComment(postId) {
    const commentText = document.querySelector(`.comment-input-${postId}`).value;
    if(!commentText.trim()) return;

    fetch('add_comment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `postingan_id=${postId}&konten=${encodeURIComponent(commentText)}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload(); // Refresh untuk update tampilan
        }
    });
}
</script>


</body>
</html>