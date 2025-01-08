<?php
session_start();
require "koneksi.php"; // Tambahkan ini

// Cek session
if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

// Ambil ID kelas dari parameter URL
if(!isset($_GET['id'])) {
    header("Location: beranda_guru.php");
    exit();
}

$kelas_id = mysqli_real_escape_string($koneksi, $_GET['id']);
// Query untuk mengambil informasi kelas
$query_kelas = "SELECT * FROM kelas WHERE id = '$kelas_id' AND guru_id = '{$_SESSION['userid']}'";
$result_kelas = mysqli_query($koneksi, $query_kelas);

if(mysqli_num_rows($result_kelas) == 0) {
    header("Location: beranda_guru.php");
    exit();
}

$data_kelas = mysqli_fetch_assoc($result_kelas);

// Query untuk mengambil postingan dari kelas ini
$query_postingan = "SELECT 
    p.*,
    g.namaLengkap as nama_pembuat,
    g.jabatan
FROM postingan_kelas p
JOIN guru g ON p.user_id = g.username
WHERE p.kelas_id = '$kelas_id'
ORDER BY p.created_at DESC";

$result_postingan = mysqli_query($koneksi, $query_postingan);

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

            <!-- konten inti -->
            <div class="col col-inti p-0 p-md-3">
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
                </style>
                    <div style="background-image: url(assets/bg.jpg); 
                                height: 200px; 
                                padding-top: 120px; 
                                margin-top: 15px; 
                                background-position: center;
                                background-size: cover;
                                " 
                        class="rounded text-white shadow latar-belakang mx-2 mx-md-0">
                        <!-- ... -->
                        <div class="ps-3" style="position: relative; z-index: 999;">
                            <div>
                                <h5 class="display-5 p-0 m-0" 
                                    style="font-weight: bold; font-size: 28px; font-size: clamp(24px, 5vw, 35px);">
                                    Pendidikan Agama Islam
                                </h5>
                                <h4 class="p-0 m-0 pb-3" style="font-size: clamp(16px, 4vw, 24px);">
                                    Kelas 7
                                </h4>       
                            </div>
                        </div>
                    </div>                
                    <div class="row mt-4 p-4 pt-0">
                    <div class="col-12 col-lg-8 p-0">
                        <div class="buatPosting rounded-3 gap-3 d-flex">
                            <div class="d-flex">
                                <a href="profil.php">
                                    <img src="assets/pp.png" alt="" width="50px" class="rounded-circle">
                                </a>
                            </div>
                            <div style="background-color: rgb(231, 231, 231);" class="rounded-pill flex-fill btn text-start">
                                <p class="p-2 m-0 text-muted sapa1" data-bs-toggle="modal" data-bs-target="#modalTambahPostingan" style="font-size: 14px;">Topik apa yang ingin Anda diskusikan bersama siswa, Ayundy?</p>
                                <p class="p-2 m-0 text-muted sapa2" data-bs-toggle="modal" data-bs-target="#modalTambahPostingan" style="font-size: 12px;">Ingin diskusikan tentang apa?</p>
                            </div>
                            <!-- style font -->
                             <style>
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
                        </div>

                        <!-- modal untuk tambah pendapat -->
                        <div class="modal fade" id="modalTambahPostingan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5" id="exampleModalLabel"><strong>Buat Pendapat</strong></h1>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="d-flex gap-3">
                                        <div>
                                            <a href="profil.html">
                                                <img src="assets/pp.png" alt="" width="40px" class="rounded-circle">
                                            </a>
                                        </div>
                                        <div class="">
                                            <h6 class="p-0 m-0">Ayundy Anditaningrum, S.Ag</h6>
                                            <p class="p-0 m-0 text-muted" style="font-size: 12px;">Guru Pendidikan Agama Islam</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="form-floating">
                                            <textarea class="form-control" placeholder="Apa pendapat Anda?" id="pendapat" style="height: 100px;"></textarea>
                                            <label for="pendapat">Apa pendapat Anda?</label>
                                        </div>
                                    </div>
                                    <div class="row p-2 mt-1 justify-content-between">
                                        <div class="col p-0 m-1">
                                            <div class="d-flex"> 
                                                <div class="flex-fill">
                                                    <input type="file" class="btn btn-secondary file-input" id="camerainput"></input>
                                                    <label for="camerainput" style="background-color: rgb(237, 237, 237); font-size: 12px;" class="btn bi-file-earmark-fill">    Tambah Dokumen</label>
                                                    <input type="file"  class="btn btn-secondary file-input" id="fileInput"></input>
                                                    <label for="fileInput" style="background-color: rgb(237, 237, 237); font-size: 12px;" class="btn bi-image-fill">   Tambah Gambar</label>
                                                    <input type="image" class="btn btn-secondary bi-camera-fill file-input" id="camerainput"></input>
                                                    <label for="camerainput" style="background-color: rgb(237, 237, 237); font-size: 12px;" class="btn bi-camera-fill">    Ambil Foto</label>
    
                                                </div>
                                            </div>
                                        </div>
                                        <style>
                                            .file-input {
                                                display: none;
                                            }
                                        </style>
                                    </div>
                                </div>
                                <div class="modal-footer d-flex">
                                  <button type="button"  class="btn btn-primary flex-fill">Kirim</button>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- alert selesai hapus -->
                        
                        <!-- postingan guru -->
                        <?php 
                        if(mysqli_num_rows($result_postingan) > 0) {
                            while($post = mysqli_fetch_assoc($result_postingan)) {
                                // Format tanggal
                                $tanggal = date("d F Y", strtotime($post['created_at']));
                        ?>
                         <div class="mt-4 p-md-3 mb-4 rounded-3 bg-white mx-md-0 postingan p-4" 
                         style="border: 1px solid rgb(226, 226, 226);">
                            <div class="d-flex gap-3">
                                <div>
                                    <a href="profil.html">
                                        <img src="assets/pp.png" alt="" width="40px" class="rounded-circle">
                                    </a>
                                </div>
                                <div class="">
                                    <h6 class="p-0 m-0"><?php echo htmlspecialchars($post['nama_pembuat']); ?></h6>
                                    <p class="p-0 m-0 text-muted" style="font-size: 12px;">Diposting pada <?php echo $tanggal; ?></p>
                                </div>
                                <div class="flex-fill text-end dropdown">
                                    <button class="bg-light border rounded" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="assets/dot.png" alt="" width="20px"></button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="hapusPostingan(<?php echo $post['id']; ?>)">Hapus Pendapat</a></li>

                                    </ul>
                                </div>
                            </div>
                            <div class="">
                                <div class="mt-3">
                                    <p class="textPostingan"><?php echo nl2br(htmlspecialchars($post['konten'])); ?></p>
                                    
                                    <!-- style untk text postingan -->
                                     <style>
                                        @media screen and (max-width: 768px) {
                                            .textPostingan {
                                                font-size: 14px;
                                            }   
                                        }
                                     </style>
                                </div>

                                <?php
                                // Query untuk mengambil lampiran
                                $postingan_id = $post['id'];
                                $query_lampiran = "SELECT * FROM lampiran_postingan WHERE postingan_id = '$postingan_id'";
                                $result_lampiran = mysqli_query($koneksi, $query_lampiran);
                                
                                if(mysqli_num_rows($result_lampiran) > 0) {
                                    echo '<div class="container mt-4 p-0">';
                                    echo '<div id="imageContainer-'.$post['id'].'" class="image-grid">';
                                    while($lampiran = mysqli_fetch_assoc($result_lampiran)) {
                                        if(strpos($lampiran['tipe_file'], 'image') !== false) {
                                            echo '<img src="'.$lampiran['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                        }
                                    }
                                    echo '</div></div>';
                                }
                                ?>

                                <div class="mt-3 d-flex gap-3">
                                    <p><strong>20</strong> Suka</p>
                                    <p><strong>3</strong> Pendapat</p>
                                </div>
                                <div class="d-flex gap-2 justify-content-between mt-3 ps-2 pe-2" style="font-size: 14px;">
                                    <button class="btn bi-arrow-up-circle text-white flex-fill py-1 py-md-2">
                                        <span class="d-none d-md-inline">Suka</span>
                                    </button>
                                    <button class="btn bi-chat flex-fill text-white py-1 py-md-2" id="ShowCommentButton" 
                                            data-bs-toggle="modal" data-bs-target="#commentModal">
                                        <span class="d-none d-md-inline">Pendapat</span>
                                    </button>
                                    <button class="btn bi-share text-white flex-fill py-1 py-md-2">
                                        <span class="d-none d-md-inline">Bagikan</span>
                                    </button>
                                </div>

                                <!-- style untuk postingan mobile view -->

                                 <!-- modal menampilkan komentar -->
                                <!-- modal untuk tambah pendapat -->
                                <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="modalKomentar" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="modalKomentar"><strong>Pendapat</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class=" gap-3">
                                                    <!-- foto ptofil komen -->
                                                     <div class="d-flex gap-3">
                                                        <div>
                                                            <img src="assets/pp-siswa2.png" alt="" width="40px" class="rounded-circle border">
                                                        </div>
                                                        <div class="pt-2 pb-2 pe-4 ps-3 rounded-4 mb-3" style="background-color: rgb(238, 238, 238);">
                                                            <h6 class="p-0 m-0" style="font-size: 12px;">Hilan Arjinan</h6>
                                                            <p class="p-0 m-0" style="font-size: 14px;">Njih, terima kasih bu</p>
                                                        </div>    
                                                     </div>
                                                     <div class="d-flex gap-3">
                                                        <div>
                                                            <a href="profil-siswa.html">
                                                                <img src="assets/pp-siswa.png" alt="" width="40px" class="rounded-circle border">
                                                            </a>
                                                        </div>
                                                        <div class="pt-2 pb-2 pe-4 ps-3 rounded-4 mb-3" style="background-color: rgb(238, 238, 238);">
                                                            <h6 class="p-0 m-0" style="font-size: 12px;">Dhio Lintang Winarto</h6>
                                                            <p class="p-0 m-0" style="font-size: 14px;">Terima kasih bu, apakah besok kisi-kisinya ada di ujian semua bu?</p>
                                                        </div>
                                                     </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex align-items-center">
                                                <!-- Gambar -->
                                                <div class="me-3">
                                                    <img src="assets/pp.png" alt="Profile Picture" width="40px" class="rounded-circle">
                                                </div>
                                                <!-- Textarea -->
                                                <div class="flex-fill">
                                                    <div class="form-floating">
                                                        <textarea class="form-control" style="width: 100%;" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
                                                        <label for="floatingTextarea">Pendapat Anda</label>
                                                    </div>
                                                </div>
                                                <div class="">
                                                    <button class="btn bi-send"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                  <!-- logika komentar -->
                                  <script>
                                    
                                  </script>
                            </div>
                         </div>
                         <?php 
                            }
                        } else {
                            echo '<div class="mt-4 text-center text-muted">Belum ada postingan</div>';
                        }
                        ?>
                    </div>
                    <!-- fungsi untuk menghapus postingan di bagian bawah file -->
                    <script>
                    function hapusPostingan(id) {
                        if(confirm('Apakah Anda yakin ingin menghapus postingan ini?')) {
                            window.location.href = `hapus_postingan.php?id=${id}&kelas_id=<?php echo $kelas_id; ?>`;
                        }
                    }

                    function showImage(src) {
                        document.getElementById('modalImage').src = src;
                        var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                        imageModal.show();
                    }
                    </script>
                    <!-- style untuk device grid -->
                     <style>
                        .image-grid {
                            display: grid;
                            gap: 5px;
                        }
                        /* Layout for different number of images */
                        .image-grid.one {
                            grid-template-columns: 1fr;
                        }
                        .image-grid.two {
                            grid-template-columns: 1fr 1fr;
                        }
                        .image-grid.three {
                            grid-template-columns: 2fr 1fr;
                            grid-template-rows: auto auto;
                        }
                        .image-grid.three img:nth-child(1) {
                            grid-row: span 2;
                        }
                        .image-grid.four {
                            grid-template-columns: 1fr 1fr;
                        }
                        .image-grid img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            cursor: pointer;
                            border: 1px solid grey;
                            border-radius: 5px;
                        }
                         
                        @media (max-width: 768px) {
                            .image-grid.three {
                                grid-template-columns: 1fr;
                                grid-template-rows: auto;
                            }
                            .image-grid.three img:nth-child(1) {
                                grid-row: auto;
                            }
                            .image-grid img {
                                max-height: 200px;
                            }
                        }
                     </style>
                     <!-- script untuk img container -->
                      <script>
                        // Example images (replace these with dynamic content if needed)
                        const images = [
                            "assets/kisi.jpg",
                            "assets/kisi2.webp",
                            "assets/kisi3.webp",
                        ];
                
                        const imageContainer = document.getElementById("imageContainer");
                
                        // Set grid class based on number of images
                        if (images.length === 1) {
                            imageContainer.classList.add("one");
                        } else if (images.length === 2) {
                            imageContainer.classList.add("two");
                        } else if (images.length === 3) {
                            imageContainer.classList.add("three");
                        } else if (images.length >= 4) {
                            imageContainer.classList.add("four");
                        }
                
                        // Add images to the grid
                        images.forEach(src => {
                            const img = document.createElement("img");
                            img.src = src;
                            img.alt = "Image";
                            img.setAttribute("data-bs-toggle", "modal");
                            img.setAttribute("data-bs-target", "#imageModal");
                            img.addEventListener("click", () => {
                                document.getElementById("modalImage").src = src;
                            });
                            imageContainer.appendChild(img);
                        });
                    </script>
                    <!-- modal gambarnya -->
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <img src="" id="modalImage" width="100%" class="img-fluid" alt="Modal Preview">
                                </div>
                            </div>
                        </div>
                    </div>
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

</body>
</html>