<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

$userid = $_SESSION['userid'];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <title>Profil - SMAGAEdu</title>
</head>
<style>
        body{ 
            font-family: merriweather;
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
                                color: white;
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
                                <p class="p-0 m-0">Beranda</p>
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
                            <div class="d-flex align-items-center color-web rounded p-2">
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
                        <div class="d-flex align-items-center  rounded p-2" style="">
                            <img src="assets/beranda_outfill.png" alt="" width="50px" class="pe-4">
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
                        <div class="d-flex align-items-center bg-white shadow-sm rounded p-2" style="">
                            <img src="assets/profil_fill.png" alt="" width="50px" class="pe-4">
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



            <!-- ini isi kontennya -->
            <div class="col pt-0 p-2 p-md-4 col-utama">
                <style>
                    .col-utama {
                        margin-left: 0;
                    }
                    @media (min-width: 768px) {
                        .col-utama {
                            margin-left: 13rem;
                        }
                    }
                </style>
                
                <!-- Background Profile -->
                <div class="rounded text-white shadow-lg latar-belakang position-relative" 
                     style="background-image: url(assets/bg-profil.png); 
                            height: 200px; 
                            padding-top: 200px; 
                            margin-top: 56px;
                            background-position: center;
                            background-size: cover;">
                    <div class="rounded position-absolute top-0 start-0 w-100 h-100" 
                         style="background: rgba(0, 0, 0, 0.5);"></div>
                    <div class="ps-3 position-relative"></div>
                </div>

                <!-- Profile Picture -->
                <div class="text-center">
                    <img src="<?php echo !empty($siswa['foto_profil']) ? 'uploads/profil/'.$siswa['foto_profil'] : 'assets/pp.png'; ?>" 
                         alt="" 
                         class="rounded-circle position-relative"
                         style="width: 120px; height: 120px; margin-top: -60px; border: 5px solid white; object-fit: cover;">
                </div>

                <!-- Profile Info -->
                <div class="text-center mt-2">
                    <h3 class="p-0 m-1"><?php echo htmlspecialchars($siswa['nama']); ?></h3>
                    <p class="p-0 m-0">Siswa Kelas <?php echo htmlspecialchars($siswa['tingkat']); ?></p>
                </div>

                <div class="px-3 px-md-5">
                    <hr class="text-muted">
                </div>

                <!-- Profile Content -->
                <div class="container-fluid px-2 px-md-4">
                    <div class="row g-3">
                        <!-- Previous Education & Current Class -->
                        <div class="col-12">
                            <div class="d-flex flex-column flex-md-row gap-3">
                                <div class="border rounded-4 p-3 flex-fill d-flex gap-2 align-items-center">
                                    <img src="assets/sekolah-sebelumnya.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Pendidikan Sebelumnya</p>
                                        <p class="p-0 m-0"><?php echo !empty($siswa['pendidikan_sebelumnya']) ? htmlspecialchars($siswa['pendidikan_sebelumnya']) : 'Belum diisi'; ?></p>
                                    </div>
                                </div>
                                <div class="border rounded-4 p-3 flex-fill d-flex gap-2 align-items-center">
                                    <img src="assets/kelas-saat-ini.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Kelas Saat Ini</p>
                                        <p class="p-0 m-0"><?php echo !empty($siswa['tingkat']) ? htmlspecialchars($siswa['tingkat']) : 'Belum diisi'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Report Card -->
                        <div class="col-12">
                            <div class="border rounded-4 p-3">
                                <div class="d-flex gap-2 align-items-center mb-3">
                                    <img src="assets/nilai-raport.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Rata-Rata Nilai Lapor</p>
                                        <p class="text-muted p-0 m-0" style="font-size: 12px;">Kumpulan seluruh rekam nilai siswa</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Kelas</th>
                                                <th>Semester</th>
                                                <th>Rata-Rata</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Table content remains the same -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Rest of the content follows similar pattern -->
                        <!-- Convert all d-flex containers to use col-12 col-md-6 for responsive layout -->
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/gaya-belajar.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Gaya Belajar</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['gaya_belajar']) ? htmlspecialchars($siswa['gaya_belajar']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/iq.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Hasil Tes IQ</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['hasil_iq']) ? htmlspecialchars($siswa['hasil_iq']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/iq.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Hasil Tes IQ</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['hasil_iq']) ? htmlspecialchars($siswa['hasil_iq']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/literasi.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Kemampuan Literasi</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['kemampuan_literasi']) ? htmlspecialchars($siswa['kemampuan_literasi']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/numerik.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Kemampuan Berhitung</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['kemampuan_berhitung']) ? htmlspecialchars($siswa['kemampuan_berhitung']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/minat-siswa.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Minat Siswa</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['minat_siswa']) ? htmlspecialchars($siswa['minat_siswa']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/hobi.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Hobi Siswa</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['hobi']) ? htmlspecialchars($siswa['hobi']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/mental.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Kesehatan Mental</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['kesehatan_mental']) ? htmlspecialchars($siswa['kesehatan_mental']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/emosi.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Pengembangan Emosional</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['pengembangan_emosional']) ? htmlspecialchars($siswa['pengembangan_emosional']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/kesehatan.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Penyakit Bawaan</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['penyakit_bawaan']) ? htmlspecialchars($siswa['penyakit_bawaan']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 d-flex gap-2 align-items-center">
                                <img src="assets/sosial.png" alt="" width="35px" height="35px" class="rounded">
                                <div>
                                    <p class="p-0 m-0" style="font-size: 12px; font-weight: bold;">Kehidupan Sosial</p>
                                    <p class="p-0 m-0"><?php echo !empty($siswa['kehidupan_sosial']) ? htmlspecialchars($siswa['kehidupan_sosial']) : 'Belum diisi guru'; ?></p>
                                </div>
                            </div>
                        </div>




                        
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="gantifoto" tabindex="-1">
   <div class="modal-dialog modal-lg modal-dialog-centered">
       <div class="modal-content rounded-4 shadow">
           <div class="modal-header border-0 pb-0">
               <h5 class="modal-title fw-bold">Ubah Foto</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
           </div>
           
           <form action="upload_foto.php" method="post" enctype="multipart/form-data">
               <div class="modal-body p-4">
                   <div class="row g-4 justify-content-center">
                       <!-- Profile Photo -->
                       <div class="col-md-6">
                           <div class="text-center">
                               <div class="position-relative d-inline-block">
                                   <div id="preview-container" class="rounded-circle overflow-hidden border border-4 border-light shadow-lg mb-3 d-flex align-items-center justify-content-center" 
                                        style="width:180px; height:180px;">
                                       <img id="preview" src="" style="display:none; width:100%; height:100%; object-fit:cover;">
                                       <i class="bi bi-person-circle display-1 text-secondary"></i>
                                   </div>
                                   <label class="position-absolute bottom-0 end-0 mb-3 me-2">
                                       <span class="btn btn-light btn-sm rounded-circle shadow-sm">
                                           <i class="bi bi-camera-fill text-primary"></i>
                                       </span>
                                       <input type="file" name="foto_profil" id="foto_profil" class="d-none" accept="image/*">
                                   </label>
                               </div>
                               <small class="d-block text-muted">Foto Profil</small>
                           </div>
                       </div>

                       <!-- Background Photo --> 
                       <div class="col-md-6">
                           <div class="text-center">
                               <div class="position-relative d-inline-block">
                                   <div class="rounded-4 overflow-hidden border border-2 border-light shadow-lg mb-3 bg-light d-flex align-items-center justify-content-center" 
                                        style="width:300px; height:180px;">
                                       <i class="bi bi-image display-2 text-secondary"></i>
                                   </div>
                                   <label class="position-absolute bottom-0 end-0 mb-2 me-2">
                                       <span class="btn btn-light btn-sm rounded-circle shadow-sm">
                                           <i class="bi bi-image text-secondary"></i>
                                       </span>
                                       <input type="file" name="foto_latarbelakang" class="d-none" accept="image/*">
                                   </label>
                               </div>
                               <small class="d-block text-muted">Foto Latar</small>
                           </div>
                       </div>
                   </div>

                   <!-- Cropper Modal -->
                   <div class="modal fade" id="cropperModal" tabindex="-1">
                       <div class="modal-dialog">
                           <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title">Crop Image</h5>
                                   <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                               </div>
                               <div class="modal-body">
                                   <div style="max-width: 100%;">
                                       <img id="cropperImage" src="" style="display: block; max-width: 100%;">
                                   </div>
                               </div>
                               <div class="modal-footer">
                                   <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                   <button type="button" class="btn color-web text-white" id="cropButton">Crop</button>
                               </div>
                           </div>
                       </div>
                   </div>

                   <input type="hidden" name="cropped_image" id="cropped_image">
               </div>

               <div class="modal-footer border-0 pt-0">
                   <button type="submit" class="btn color-web w-100 text-white rounded-3">Simpan Perubahan</button>
               </div>
           </form>
       </div>
   </div>
</div>


<style>
.modal-content {
   border: none;
}
.btn-light {
   background: rgba(255,255,255,0.9);
   backdrop-filter: blur(4px);
}
</style>

<!-- script untuk crop -->
<script>
let cropper;
const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));

document.getElementById('foto_profil').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        if (validateFile(file)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Set image source for cropper
                document.getElementById('cropperImage').src = e.target.result;
                // Show cropper modal
                cropperModal.show();
                // Initialize cropper
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(document.getElementById('cropperImage'), {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: false,
                    center: false,
                    highlight: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    toggleDragModeOnDblclick: false
                });
            };
            reader.readAsDataURL(file);
        }
    }
});

document.getElementById('cropButton').addEventListener('click', function() {
    const croppedCanvas = cropper.getCroppedCanvas({
        width: 180,
        height: 180
    });
    
    // Update preview
    const preview = document.getElementById('preview');
    preview.src = croppedCanvas.toDataURL();
    preview.style.display = 'block';
    
    // Store cropped image data
    document.getElementById('cropped_image').value = croppedCanvas.toDataURL();
    
    // Hide cropper modal
    cropperModal.hide();
    
    // Destroy cropper instance
    cropper.destroy();
    cropper = null;
});

function validateFile(file) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (file.size > maxSize) {
        alert('File terlalu besar. Maksimal 5MB');
        return false;
    }

    if (!allowedTypes.includes(file.type)) {
        alert('Format file tidak didukung. Gunakan JPG, PNG atau GIF');
        return false;
    }

    return true;
}
</script>

<!-- Preview script -->
<script>
document.querySelectorAll('input[type="file"]').forEach(input => {
   input.onchange = function() {
       const file = this.files[0];
       const preview = this.closest('.text-center').querySelector('.preview-img');
       
       if(file) {
           preview.src = URL.createObjectURL(file);
       }
   }
});

document.querySelectorAll('input[type="file"]').forEach(input => {
   input.addEventListener('change', function() {
       if (this.files && this.files[0]) {
           const reader = new FileReader();
           const preview = this.closest('.text-center').querySelector('.preview-img');
           
           reader.onload = function(e) {
               preview.src = e.target.result;
               preview.classList.add('preview-loaded');
           };

           reader.readAsDataURL(this.files[0]);
       }
   });
});

// Validasi file
function validateFile(input) {
   const file = input.files[0];
   const maxSize = 5 * 1024 * 1024; // 5MB
   const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

   if (!file) return false;

   if (file.size > maxSize) {
       alert('File terlalu besar. Maksimal 5MB');
       input.value = '';
       return false;
   }

   if (!allowedTypes.includes(file.type)) {
       alert('Format file tidak didukung. Gunakan JPG, PNG atau GIF');
       input.value = '';
       return false;
   }

   return true;
}

document.querySelectorAll('input[type="file"]').forEach(input => {
   input.addEventListener('change', function() {
       if (validateFile(this)) {
           const reader = new FileReader();
           const preview = this.closest('.text-center').querySelector('.preview-img');
           
           reader.onload = e => {
               preview.src = e.target.result;
               preview.classList.add('preview-loaded');
           };

           reader.readAsDataURL(this.files[0]);
       }
   });
});
</script>
<style>
/* Animasi ketika preview dimuat */
.preview-loaded {
   animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
   from { opacity: 0; }
   to { opacity: 1; }
}


.upload-preview {
   transition: transform 0.2s;
   cursor: pointer;
}

.upload-preview:hover {
   transform: scale(1.05);
}

.preview-img {
   box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>
</body>
</html>