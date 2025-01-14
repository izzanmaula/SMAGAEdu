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

// Ambil daftar kelas yang diikuti
$query_kelas = "SELECT k.*, g.namaLengkap as nama_guru 
                FROM kelas k 
                JOIN kelas_siswa ks ON k.id = ks.kelas_id 
                JOIN guru g ON k.guru_id = g.username
                WHERE ks.siswa_id = ?";

$stmt_kelas = mysqli_prepare($koneksi, $query_kelas);
mysqli_stmt_bind_param($stmt_kelas, "i", $siswa['id']);
mysqli_stmt_execute($stmt_kelas);
$result_kelas = mysqli_stmt_get_result($stmt_kelas);

// Debug - bisa dihapus nanti setelah berhasil
if (!$result_kelas) {
    echo "Error query kelas: " . mysqli_error($koneksi);
    exit();

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
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">    <title>Masuk - Smagaedu</title>
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
    <div class="container-fluid">
        <div class="row">
        <div class="col-auto vh-100 p-2 p-md-4 shadow-sm menu-samping d-none d-md-block" style="background-color:rgb(238, 236, 226)">
                <style>
                    .menu-samping {
                        position: fixed;
                        width: 13rem;
                        z-index: 1000;
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

            <!-- ini isi kontennya -->
<!-- Isi konten -->
<div class="col p-4 col-utama mt-1 mt-md-0">
        <div class="row justify-content-between align-items-center mb-1">
            <div class="col-12 col-md-auto mb-3 mb-md-0">
                <h3 style="font-weight: bold;">Beranda</h3>
            </div>

            <!-- Tombol Gabung Kelas untuk Desktop -->
            <div class="d-none d-md-block col-md-auto">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modal_gabung_kelas" 
                        class="btn d-flex align-items-center justify-content-center border p-2">
                    <img src="assets/tambah.png" alt="Tambah" width="25px" class="me-2">
                    <p class="m-0">Gabung Kelas</p>
                </button>
            </div>

            <!-- Floating Button untuk Mobile -->
            <div class="position-fixed bottom-0 end-0 d-md-none m-4">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modal_gabung_kelas" 
                        class="btn color-web rounded-circle shadow d-flex align-items-center justify-content-center" 
                        style="width: 56px; height: 56px;">
                    <img src="assets/tambah.png" alt="Tambah" width="25px" class="m-0" style="filter: brightness(0) invert(1);">
                </button>
            </div>
        </div>

        <!-- Alert untuk pesan -->
        <?php if(isset($_GET['pesan'])): ?>
            <div class="alert alert-<?php 
                echo $_GET['pesan'] == 'bergabung_sukses' ? 'success' : 
                    ($_GET['pesan'] == 'sudah_terdaftar' ? 'warning' : 'danger'); 
                ?> alert-dismissible fade show" role="alert">
                <?php 
                switch($_GET['pesan']) {
                    case 'bergabung_sukses':
                        echo "Berhasil bergabung ke kelas!";
                        break;
                    case 'sudah_terdaftar':
                        echo "Anda sudah terdaftar di kelas ini.";
                        break;
                    case 'kode_tidak_valid':
                        echo "Kode kelas tidak valid.";
                        break;
                    default:
                        echo "Terjadi kesalahan.";
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Daftar Kelas -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if(mysqli_num_rows($result_kelas) > 0): 
                while($kelas = mysqli_fetch_assoc($result_kelas)): ?>
                <div class="col">
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
                               class="btn color-web w-100 rounded" 
                               style="text-decoration: none; color: white;">
                                Masuk
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; 
            else: ?>
                <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                    <p class="text-muted">Anda belum memiliki kelas. Silahkan bergabung ke kelas dengan memasukkan kode kelas.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Gabung Kelas -->
    <div class="modal fade" id="modal_gabung_kelas" tabindex="-1" aria-labelledby="label_gabung_kelas" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="label_gabung_kelas" style="font-weight: bold;">Gabung Kelas</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="gabung_kelas.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode_kelas" class="form-label">Kode Kelas</label>
                            <input type="text" class="form-control" id="kode_kelas" name="kode_kelas" 
                                   placeholder="Masukkan kode kelas" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn color-web text-white w-100">Gabung</button>
                    </div>
                </form>
            </div>
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



</body>
</html>