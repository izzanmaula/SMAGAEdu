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

// Ambil data guru
$userid = $_SESSION['userid'];
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);

// Query untuk menghitung jumlah siswa
$query_jumlah = "SELECT COUNT(*) as total FROM kelas_siswa WHERE kelas_id = '$kelas_id'";
$result_jumlah = mysqli_query($koneksi, $query_jumlah);
$jumlah_siswa = mysqli_fetch_assoc($result_jumlah)['total'];

// Query untuk mengambil semua siswa di kelas ini
$query_siswa_all = "SELECT s.nama, s.foto_profil FROM siswa s 
                    JOIN kelas_siswa ks ON s.id = ks.siswa_id 
                    WHERE ks.kelas_id = '$kelas_id'";
$result_siswa = mysqli_query($koneksi, $query_siswa_all);

?>

<?php if(isset($_GET['pesan'])): ?>
    <div class="alert alert-<?php echo $_GET['pesan'] == 'siswa_dihapus' ? 'success' : 'danger'; ?> alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 1050;">
        <?php 
        if($_GET['pesan'] == 'siswa_dihapus') {
            echo "Siswa berhasil dihapus dari kelas!";
        } else if($_GET['pesan'] == 'gagal_hapus') {
            echo "Gagal menghapus siswa. Silakan coba lagi.";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
    // Auto hide alert after 3 seconds
    setTimeout(function() {
        document.querySelector('.alert').remove();
    }, 3000);
    </script>
<?php endif; ?>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <!-- Cropper.js JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <title>Kelas - SMAGAEdu</title>
</head>
<style>
        body{ 
            font-family: merriweather;
        }

        .color-web {
            background-color: rgb(218, 119, 86);
        }

        .btnPrimary {
            background-color: rgb(218, 119, 86);
            border: 0;
        }

        .btnPrimary:hover{
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
                        <a href="beranda_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded color-web p-2">
                                <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Beranda</p>
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
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Ujian</p>
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
                        <a href="bantuan.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/bantuan_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Bantuan</p>
                            </div>
                        </a>
                    </div>
                    
                <!-- Profile Dropdown -->
                <div class="mt-3 dropdown"> <!-- Tambahkan class dropdown di sini -->
                    <button class="btn btnPrimary d-flex align-items-center gap-3 p-2 rounded-3 border w-100" 
                            style="background-color: #F8F8F7;" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                        <p class="p-0 m-0 text-truncate" style="font-size: 12px;"><?php echo htmlspecialchars($guru['namaLengkap']); ?></p>
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
                        <a href="beranda_guru.php" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
                            <img src="assets/smagaedu.png" alt="" width="30px" class="logo_orange">
                            <div>
                                <h1 class="display-5  p-0 m-0" style="font-size: 20px; text-decoration: none;">SMAGAEdu</h1>
                                <p class="p-0 m-0 text-muted" style="font-size: 12px;">LMS</p>
                            </div>
                        </a>
                    </div>  
                    <div class="col">
                        <a href="beranda_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
                            <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
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
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
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
                    <div class="btn btnPrimary d-flex align-items-center gap-3 p-2 rounded-3 border dropdown-toggle" style="background-color: #F8F8F7;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                        <p class="p-0 m-0 text-truncate" style="font-size: 12px;"><?php echo htmlspecialchars($guru['namaLengkap']); ?></p>
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

                <!-- Container untuk background dengan efek hover -->
                <div class="background-container position-relative rounded mx-2 mx-md-0">
                    <!-- Background image -->
                    <div style="background-image: url(<?php 
                            echo !empty($data_kelas['background_image']) ? 
                                htmlspecialchars($data_kelas['background_image']) : 
                                'assets/bg.jpg'; 
                            ?>); 
                            height: 200px; 
                            padding-top: 120px; 
                            margin-top: 15px; 
                            background-position: center;
                            background-size: cover;" 
                        class="rounded text-white shadow latar-belakang">
                    </div>

                    <!-- Overlay dengan tombol (akan muncul saat hover) -->
                    <div class="background-overlay rounded d-flex align-items-center justify-content-center">
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalEditBackground">
                            <i class="fas fa-camera me-2"></i>Ganti Background
                        </button>
                    </div>

                    <!-- Tombol titik tiga tetap di posisinya -->
                    <div class="position-absolute top-0 end-0 m-3" style="z-index: 2;">
                        <button class="btn btn-light btn-sm rounded-circle" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalEditBackground">
                                Edit Background
                            </a></li>
                        </ul>
                    </div>

                    <!-- Konten (teks) dengan z-index lebih tinggi -->
                    <div class="position-absolute bottom-0 start-0 p-3" style="z-index: 2;">
                        <div>
                            <h5 class="display-5 p-0 m-0 text-white" 
                                style="font-weight: bold; font-size: 28px; font-size: clamp(24px, 5vw, 35px);">
                                <?php echo htmlspecialchars($data_kelas['mata_pelajaran']); ?>
                            </h5>
                            <h4 class="p-0 m-0 pb-3 text-white" style="font-size: clamp(16px, 4vw, 24px);">
                                Kelas <?php echo htmlspecialchars($data_kelas['tingkat']); ?>
                            </h4>       
                        </div>
                    </div>
                </div>

                <!-- hover untuk background kelas -->
                 <!-- CSS untuk efek hover -->
                <style>
                .latar-belakang {
                    filter: brightness(0.6);
                }
                .background-container {
                    position: relative;
                    cursor: pointer;
                    
                }

                .background-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: rgba(0, 0, 0, 0.5);
                    opacity: 0;
                    transition: opacity 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .background-container:hover .background-overlay {
                    opacity: 1;
                }

                /* Memastikan tombol tidak inherit opacity dari overlay */
                .background-overlay .btn {
                    transform: translateY(20px);
                    transition: transform 0.3s ease;
                }

                .background-container:hover .background-overlay .btn {
                    transform: translateY(0);
                }
                </style>

                    <div class="row mt-4 p-3 m-0 pt-0">
                    <div class="col-12 col-lg-8 p-0">
                        <div class="buatPosting rounded-3 gap-3 d-flex">
                            <div class="d-flex">
                                <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                    alt="Profile Image" 
                                    class="profile-img rounded-4 border-0 bg-white" style="width: 40px; object-fit:cover;">
                            </div>
                            <div style="background-color: rgb(231, 231, 231);" class="rounded-pill flex-fill btn btnPrimary text-start">
                                <p class="p-2 m-0 text-muted sapa1" data-bs-toggle="modal" data-bs-target="#modalTambahPostingan" style="font-size: 14px;">Halo, topik apa yang ingin Anda diskusikan bersama siswa?</p>
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

<!-- Modal Edit Background -->
<div class="modal fade" id="modalEditBackground" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Background Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tab navigation -->
                <ul class="nav nav-tabs mb-3" id="backgroundTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">
                            Upload Gambar
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="preset-tab" data-bs-toggle="tab" data-bs-target="#preset" type="button">
                            Pilih Template
                        </button>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content">
                    <!-- Upload Tab -->
                    <div class="tab-pane fade show active" id="upload">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="imageUpload" accept="image/*">
                        </div>
                        <div class="cropper-container" style="display: none;">
                            <div class="img-container" style="max-height: 400px;">
                                <img id="image" src="" alt="Picture">
                            </div>
                            <div class="mt-3">
                                <div class="cropper-controls">
                                    <button class="btn" id="rotateLeft" title="Rotate Left">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button class="btn" id="rotateRight" title="Rotate Right">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    <button class="btn" id="zoomIn" title="Zoom In">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                    <button class="btn" id="zoomOut" title="Zoom Out">
                                        <i class="fas fa-search-minus"></i>
                                    </button>
                                </div>                            
                            </div>
                        </div>
                    </div>

                    <!-- Preset Tab -->
                    <div class="tab-pane fade" id="preset">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="preset-image" data-image="assets/presets/bg1.jpg">
                                    <img src="assets/presets/bg1.jpg" class="img-fluid rounded">
                                </div>
                            </div>
                            <!-- Tambahkan preset lainnya sesuai kebutuhan -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveBackground">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- style untuk cropper -->
<style>
/* Warna box cropper */
.cropper-view-box {
    border-radius: 5px;
}

/* Warna garis cropper */
.cropper-line {
}

/* Warna handle di sudut dan sisi */
.cropper-point {
    width: 10px !important;
    height: 10px !important;
    opacity: 1;
    background-color: #e79e7c !important;
}

/* Warna overlay di luar area crop */
.cropper-container {
    /* background-color: rgba(0, 0, 0, 0.6); */
}

/* Styling untuk tombol kontrol */
.cropper-controls {
    margin-top: 15px;
    display: flex;
    gap: 10px;
    justify-content: center;
}

.cropper-controls .btn {
    background-color: #fff;
    border: 1px solid #e79e7c;
    color: #e79e7c;
    padding: 8px 15px;
    transition: all 0.3s ease;
}

.cropper-controls .btn:hover {
    background-color: #e79e7c;
    color: #fff;
}

.cropper-controls .btn i {
    font-size: 14px;
}

/* Container cropper */
.img-container {
    background-color: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
}

/* Modifikasi modal untuk tampilan yang lebih baik */
.modal-content {
    border-radius: 12px;
}

/* Style untuk preview gambar */
#image {
    max-width: 100%;
    display: block;
}
</style>

<!-- script untuk crop latar belakang -->
<script>
let cropper;
const imageElement = document.getElementById('image');
const inputElement = document.getElementById('imageUpload');
const cropperContainer = document.querySelector('.cropper-container');

inputElement.addEventListener('change', function(e) {
    const files = e.target.files;
    const reader = new FileReader();

    reader.onload = function() {
        if (cropper) {
            cropper.destroy();
        }

        imageElement.src = reader.result;
        cropperContainer.style.display = 'block';
        
        cropper = new Cropper(imageElement, {
            aspectRatio: 16 / 9,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            modal: false,
            guides: false,
            highlight: false,
            cropBoxMovable: false,
            cropBoxResizable: false,
            toggleDragModeOnDblclick: false,
        });
    };

    if (files && files[0]) {
        reader.readAsDataURL(files[0]);
    }
});

// Tombol kontrol cropper
document.getElementById('rotateLeft').addEventListener('click', () => cropper.rotate(-90));
document.getElementById('rotateRight').addEventListener('click', () => cropper.rotate(90));
document.getElementById('zoomIn').addEventListener('click', () => cropper.zoom(0.1));
document.getElementById('zoomOut').addEventListener('click', () => cropper.zoom(-0.1));

// Modifikasi bagian event listener saveBackground
document.getElementById('saveBackground').addEventListener('click', function() {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas({
            width: 1920,
            height: 1080
        });

        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('image', blob, 'background.jpg'); // Tambahkan nama file
            formData.append('kelas_id', '<?php echo $kelas_id; ?>');

            // Log FormData untuk debugging
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            fetch('save_background.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data); // Log response
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menyimpan background: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }, 'image/jpeg'); // Specify image format
    } else {
        alert('Silakan pilih dan crop gambar terlebih dahulu');
    }
});

</script>


<!-- Modal Tambah Postingan -->
<div class="modal fade" id="modalTambahPostingan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><strong>Buat Postingan</strong></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="tambah_postingan.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Info Profil -->
                    <div class="d-flex gap-3 mb-3">
                        <div class="border-4">
                        <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                            alt="Profile Image" 
                            class="profile-img rounded-4 border-0 bg-white" style="width: 40px;">
                        </div>
                        <div>
                            <h6 class="p-0 m-0"><?php echo htmlspecialchars($guru['namaLengkap']); ?></h6>
                            <p class="p-0 m-0 text-muted" style="font-size: 12px;">
                                <?php echo htmlspecialchars($guru['jabatan']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Form Postingan -->
                    <div class="mb-3">
                        <div class="form-floating">
                            <textarea class="form-control border bg-light" 
                                    name="konten" 
                                    placeholder="Tulis pendapat Anda disini" 
                                    style="height: 150px" 
                                    required></textarea>
                            <label class="text-muted">Tulis pendapat Anda disini</label>
                        </div>
                    </div>

                    <!-- Preview Lampiran -->
                    <div id="previewContainer" class="mb-3 d-none">
                        <div class="border rounded p-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Lampiran</small>
                                <button type="button" class="btn-close" onClick="clearFileInput()"></button>
                            </div>
                            <div id="filePreview" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>

                    <!-- Tombol Lampiran -->
                    <div class="d-flex gap-2">
                        <input type="file" class="d-none" id="fileInput" name="lampiran[]" multiple 
                               accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" onChange="handleFileSelect(this)">
                        <button type="button" class="btn btnPrimary btn-light btn-sm" onClick="document.getElementById('fileInput').click()">
                            <i class="bi bi-paperclip text-white">Lampirkan</i> 
                        </button>
                        <small class="text-muted my-auto">Gambar, PDF, & Dokumen</small>
                    </div>

                    <input type="hidden" name="kelas_id" value="<?php echo $kelas_id; ?>">
                </div>

                <div class="modal-footer btn-group  border-0">
                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btnPrimary px-4 text-white">Posting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script untuk preview file -->
<script>
function handleFileSelect(input) {
    const previewContainer = document.getElementById('previewContainer');
    const filePreview = document.getElementById('filePreview');
    filePreview.innerHTML = '';

    if (input.files.length > 0) {
        previewContainer.classList.remove('d-none');
        
        Array.from(input.files).forEach(file => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'border rounded p-2 d-flex align-items-center gap-2';
            
            // Icon berdasarkan tipe file
            let icon = 'bi-file-earmark';
            if (file.type.startsWith('image/')) icon = 'bi-file-image';
            else if (file.type.includes('pdf')) icon = 'bi-file-pdf';
            else if (file.type.includes('doc')) icon = 'bi-file-word';
            
            fileDiv.innerHTML = `
                <i class="bi ${icon}"></i>
                <small class="text-truncate" style="max-width: 150px;">${file.name}</small>
            `;
            
            filePreview.appendChild(fileDiv);
        });
    } else {
        previewContainer.classList.add('d-none');
    }
}

function clearFileInput() {
    const fileInput = document.getElementById('fileInput');
    fileInput.value = '';
    document.getElementById('previewContainer').classList.add('d-none');
    document.getElementById('filePreview').innerHTML = '';
}
</script>


                    <!-- Konten Utama -->                        
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
                                    <a href="profil_guru.php">
                                    <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                        alt="Profile Image" 
                                        class="profile-img rounded-4 border-0 bg-white" style="width: 40px;">
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
                                    echo '<div class="container mt-3 p-2 bg-light rounded">';
                                    
                                    // Array untuk memisahkan gambar dan dokumen
                                    $images = [];
                                    $documents = [];
                                    
                                    while($lampiran = mysqli_fetch_assoc($result_lampiran)) {
                                        if(strpos($lampiran['tipe_file'], 'image') !== false) {
                                            $images[] = $lampiran;
                                        } else {
                                            $documents[] = $lampiran;
                                        }
                                    }

                                    // Tampilkan gambar jika ada
                                    if(!empty($images)) {
                                        echo '<div id="imageContainer-'.$post['id'].'" class="image-grid mb-3">';
                                        foreach($images as $image) {
                                            echo '<img src="'.$image['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                        }
                                        echo '</div>';
                                    }

                                    // Tampilkan dokumen non-gambar jika ada
                                    if(!empty($documents)) {
                                        echo '<div class="document-list">';
                                        foreach($documents as $doc) {
                                            $extension = pathinfo($doc['nama_file'], PATHINFO_EXTENSION);
                                            $icon = '';
                                            
                                            // Set icon berdasarkan tipe file
                                            switch(strtolower($extension)) {
                                                case 'pdf':
                                                    $icon = 'bi-file-pdf-fill text-danger';
                                                    break;
                                                case 'doc':
                                                case 'docx':
                                                    $icon = 'bi-file-word-fill text-primary';
                                                    break;
                                                case 'xls':
                                                case 'xlsx':
                                                    $icon = 'bi-file-excel-fill text-success';
                                                    break;
                                                case 'ppt':
                                                case 'pptx':
                                                    $icon = 'bi-file-ppt-fill text-warning';
                                                    break;
                                                default:
                                                    $icon = 'bi-file-text-fill text-secondary';
                                            }
                                            
                                            echo '<div class="doc-item mb-2 p-2 bg-white rounded border">';
                                            echo '<a href="'.$doc['path_file'].'" class="text-decoration-none text-dark d-flex align-items-center gap-2" target="_blank">';
                                            echo '<i class="bi '.$icon.' fs-4"></i>';
                                            echo '<div>';
                                            echo '<div class="doc-name">'.htmlspecialchars($doc['nama_file']).'</div>';
                                            echo '<small class="text-muted">'.strtoupper($extension).' file</small>';
                                            echo '</div>';
                                            echo '</a>';
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                    }
                                    
                                    echo '</div>';
                                }
                                ?>

                                <style>
                                    .doc-item {
                                        transition: all 0.2s ease;
                                    }

                                    .doc-item:hover {
                                        background-color: #f8f9fa !important;
                                    }

                                    .doc-name {
                                        max-width: 200px;
                                        overflow: hidden;
                                        text-overflow: ellipsis;
                                        white-space: nowrap;
                                    }

                                    @media (max-width: 768px) {
                                        .doc-name {
                                            max-width: 150px;
                                        }
                                    }
                                </style>

                                <!-- query untuk mendapatkan jumlah like, komen, dan status like user sedang login -->
                                <?php
                                // Query untuk mendapatkan jumlah like
                                $query_like_count = "SELECT COUNT(*) as total FROM likes_postingan WHERE postingan_id = '{$post['id']}'";
                                $result_like_count = mysqli_query($koneksi, $query_like_count);
                                $like_count = mysqli_fetch_assoc($result_like_count)['total'];

                                // Query untuk mendapatkan jumlah komentar
                                $query_comment_count = "SELECT COUNT(*) as total FROM komentar_postingan WHERE postingan_id = '{$post['id']}'";
                                $result_comment_count = mysqli_query($koneksi, $query_comment_count);
                                $comment_count = mysqli_fetch_assoc($result_comment_count)['total'];

                                // Cek status like untuk user yang sedang login
                                $user_id = $_SESSION['userid'];
                                $check_like = "SELECT id FROM likes_postingan WHERE postingan_id = '{$post['id']}' AND user_id = '$user_id'";
                                $like_result = mysqli_query($koneksi, $check_like);
                                $is_liked = mysqli_num_rows($like_result) > 0;
                                ?>
                                <div class="mt-3 d-flex gap-3">
                                    <p><strong><span id="like-count-<?php echo $post['id']; ?>"><?php echo $like_count; ?></span></strong> Suka</p>
                                    <p><strong><?php echo $comment_count; ?></strong> Pendapat</p>
                                </div>
                                <div class="d-flex gap-2 justify-content-between mt-3 ps-2 pe-2" style="font-size: 14px;">
                                    <button class="btnPrimary btn <?php echo $is_liked ? 'bi-arrow-up-circle-fill' : 'bi-arrow-up-circle'; ?> text-white flex-fill py-1 py-md-2" 
                                            id="like-btn-<?php echo $post['id']; ?>"
                                            onclick="toggleLike(<?php echo $post['id']; ?>)">
                                        <span class="d-none d-md-inline">Suka</span>
                                    </button>
                                    <button class="btn btnPrimary bi-chat flex-fill text-white py-1 py-md-2" id="ShowCommentButton" 
                                            data-bs-toggle="modal" data-bs-target="#commentModal-<?php echo $post['id']; ?>">
                                        <span class="d-none d-md-inline">Pendapat</span>
                                    </button>
                                    <button class="btn btnPrimary bi-share text-white flex-fill py-1 py-md-2" 
                                            onclick='sharePost(<?php echo $post["id"]; ?>, <?php echo json_encode($post["konten"]); ?>)'>
                                        <span class="d-none d-md-inline">Bagikan</span>
                                    </button>                                
                                </div>

                                <!-- script aksi like -->
                                <script>
                                function toggleLike(postId) {
                                    const button = document.getElementById(`like-btn-${postId}`);
                                    const countElement = document.getElementById(`like-count-${postId}`);
                                    
                                    // Toggle class terlebih dahulu untuk feedback instant ke user
                                    const isCurrentlyLiked = button.classList.contains('bi-arrow-up-circle-fill');
                                    const currentCount = parseInt(countElement.textContent);

                                    if (isCurrentlyLiked) {
                                        button.classList.remove('bi-arrow-up-circle-fill');
                                        button.classList.add('bi-arrow-up-circle');
                                        countElement.textContent = currentCount - 1;
                                    } else {
                                        button.classList.remove('bi-arrow-up-circle');
                                        button.classList.add('bi-arrow-up-circle-fill');
                                        countElement.textContent = currentCount + 1;
                                    }

                                    // Kirim request ke server
                                    fetch('toggle_like.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: `postingan_id=${postId}`
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if(data.status === 'success') {
                                            // Update jumlah like dari server (untuk memastikan akurasi)
                                            countElement.textContent = data.like_count;
                                        } else {
                                            // Jika gagal, kembalikan ke status sebelumnya
                                            if (isCurrentlyLiked) {
                                                button.classList.remove('bi-arrow-up-circle');
                                                button.classList.add('bi-arrow-up-circle-fill');
                                                countElement.textContent = currentCount;
                                            } else {
                                                button.classList.remove('bi-arrow-up-circle-fill');
                                                button.classList.add('bi-arrow-up-circle');
                                                countElement.textContent = currentCount;
                                            }
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        // Jika terjadi error, kembalikan ke status sebelumnya
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

                                <!-- script untuk berbagi -->
                                <script>
                                async function sharePost(postId, content) {
                                    // Buat URL untuk postingan
                                    const postUrl = `${window.location.origin}/smagaBelajar/kelas_guru.php?id=${getUrlParameter('id')}&post=${postId}`;
                                    
                                    // Potong konten jika terlalu panjang
                                    const shortContent = content.length > 100 ? content.substring(0, 97) + '...' : content;
                                    
                                    // Cek apakah Web Share API tersedia (biasanya di mobile)
                                    if (navigator.share) {
                                        try {
                                            await navigator.share({
                                                title: 'Bagikan Postingan',
                                                text: shortContent,
                                                url: postUrl
                                            });
                                        } catch (err) {
                                            console.log('Error sharing:', err);
                                        }
                                    } else {
                                        // Jika Web Share API tidak tersedia (desktop), gunakan modal
                                        showShareModal(postId, postUrl);
                                    }
                                }

                                function showShareModal(postId, postUrl) {
                                    // Cek apakah modal sudah ada
                                    let shareModal = document.getElementById(`shareModal-${postId}`);
                                    
                                    if (!shareModal) {
                                        // Buat modal jika belum ada
                                        const modalHtml = `
                                            <div class="modal fade" id="shareModal-${postId}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title">Bagikan Postingan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="d-flex flex-column gap-2">
                                                                <button onclick="shareToWhatsApp('${postUrl}')" class="btn btn-success d-flex align-items-center gap-2">
                                                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                                                </button>
                                                                <button onclick="shareToTelegram('${postUrl}')" class="btn btn-primary d-flex align-items-center gap-2">
                                                                    <i class="bi bi-telegram"></i> Telegram
                                                                </button>
                                                                <button onclick="copyLink('${postUrl}')" class="btn btn-secondary d-flex align-items-center gap-2">
                                                                    <i class="bi bi-link-45deg"></i> Salin Link
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        document.body.insertAdjacentHTML('beforeend', modalHtml);
                                        shareModal = document.getElementById(`shareModal-${postId}`);
                                    }
                                    
                                    // Tampilkan modal
                                    new bootstrap.Modal(shareModal).show();
                                }

                                // Fungsi helper untuk mendapatkan parameter dari URL
                                function getUrlParameter(name) {
                                    const params = new URLSearchParams(window.location.search);
                                    return params.get(name);
                                }

                                // Fungsi untuk berbagi ke platform spesifik
                                function shareToWhatsApp(url) {
                                    window.open(`https://wa.me/?text=${encodeURIComponent(url)}`, '_blank');
                                }

                                function shareToTelegram(url) {
                                    window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}`, '_blank');
                                }

                                function copyLink(url) {
                                    navigator.clipboard.writeText(url).then(() => {
                                        // Tampilkan toast atau alert bahwa link berhasil disalin
                                        alert('Link berhasil disalin!');
                                    });
                                }
                                </script>

                                <!-- style modal berbagi -->
                                <style>
                                .share-option {
                                    transition: all 0.2s ease;
                                }

                                .share-option:hover {
                                    transform: translateY(-2px);
                                }

                                /* Animasi untuk toast */
                                .toast {
                                    position: fixed;
                                    bottom: 20px;
                                    right: 20px;
                                    z-index: 1050;
                                }

                                @media (max-width: 768px) {
                                    .toast {
                                        left: 20px;
                                        right: 20px;
                                    }
                                }
                                </style>
                                <!-- modal komentar -->
                                <div class="modal fade" id="commentModal-<?php echo $post['id']; ?>" tabindex="-1" aria-labelledby="modalKomentar" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                        <div class="modal-content">
                                            <?php
                                            // Query untuk mengambil komentar di awal
                                            $query_komentar = "SELECT k.*, COALESCE(g.namaLengkap, s.nama) as nama_user 
                                                            FROM komentar_postingan k 
                                                            LEFT JOIN guru g ON k.user_id = g.username 
                                                            LEFT JOIN siswa s ON k.user_id = s.username 
                                                            WHERE k.postingan_id = '{$post['id']}' 
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
                                                        <img src="assets/pp.png" alt="Profile" width="35px" height="35px" class="rounded-circle">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <form class="komentar-form" data-postid="<?php echo $post['id']; ?>">
                                                            <div class="form-group">
                                                                <textarea class="form-control bg-light border-0" 
                                                                        rows="1" 
                                                                        placeholder="Tulis pendapat Anda..." 
                                                                        style="resize: none; font-size: 14px;"
                                                                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                                                        required></textarea>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <button class="btn color-web text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                                style="width: 35px; height: 35px;"
                                                                onclick="submitKomentar(<?php echo $post['id']; ?>)">
                                                            <i class="bi bi-send-fill"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <style>
                                /* Style untuk modal komentar */
                                .modal-fullscreen-sm-down {
                                    padding: 0;
                                }

                                @media (max-width: 576px) {
                                    .modal-fullscreen-sm-down {
                                        margin: 0;
                                    }
                                    
                                    .modal-fullscreen-sm-down .modal-content {
                                        border-radius: 0;
                                        min-height: 100vh;
                                        display: flex;
                                        flex-direction: column;
                                    }
                                    
                                    .modal-fullscreen-sm-down .modal-body {
                                        flex: 1;
                                    }
                                }

                                /* Style untuk textarea yang auto-expand */
                                .form-control {
                                    min-height: 40px;
                                    padding: 8px 12px;
                                }

                                .form-control:focus {
                                    box-shadow: none;
                                    border-color: #ced4da;
                                }

                                /* Style untuk bubble chat */
                                .bubble-chat {
                                    max-width: 85%;
                                }

                                /* Custom scrollbar */
                                .komentar-container::-webkit-scrollbar {
                                    width: 6px;
                                }

                                .komentar-container::-webkit-scrollbar-track {
                                    background: #f1f1f1;
                                }

                                .komentar-container::-webkit-scrollbar-thumb {
                                    background: #ddd;
                                    border-radius: 3px;
                                }

                                .komentar-container::-webkit-scrollbar-thumb:hover {
                                    background: #ccc;
                                }
                                </style>
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
                                                const container = document.querySelector(`#commentModal-${postId} .komentar-container`);
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
                            gap: 8px;
                            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                        }

                        .image-grid img {
                            width: 100%;
                            height: 150px;
                            object-fit: cover;
                            cursor: pointer;
                            transition: transform 0.2s;
                        }

                        .image-grid img:hover {
                            transform: scale(1.02);
                        }

                        /* Style untuk item file */
                        .file-item {
                            background-color: white;
                            transition: all 0.2s;
                            min-width: 200px;
                        }

                        .file-item:hover {
                            background-color: #f8f9fa;
                            border-color: #dee2e6;
                        }

                        /* Responsive styling */
                        @media (max-width: 768px) {
                            .image-grid {
                                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                            }
                            
                            .image-grid img {
                                height: 120px;
                            }

                            .file-item {
                                min-width: 100%;
                            }
                        }

                        /* Style untuk attachments container */
                        .attachments {
                            border: 1px solid #dee2e6;
                            background-color: #f8f9fa;
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
                    <div style="border: 1px solid rgb(238, 238, 238);" class="tentangKelas p-3 rounded-3 gap-3 bg-white mb-3">
                        <h5><strong>Tentang Kelas ini</strong></h5>
                        <div class="w-100">
                            <?php if(!empty($data_kelas['deskripsi'])): ?>
                                <p class="p-0 m-0" style="font-size: 14px;"><?php echo nl2br(htmlspecialchars($data_kelas['deskripsi'])); ?></p>
                            <?php else: ?>
                                <p class="text-muted p-0 m-0" style="font-size: 14px;">Guru tidak memberikan deskripsi</p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex mt-3">
                            <button class="btn btnPrimary text-white flex-fill" data-bs-toggle="modal" data-bs-target="#deskripsimodal">Edit</button>
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
                                    <form action="update_deskripsi.php" method="POST">
                                        <div class="modal-body">
                                            <p style="font-size: 14px;">Apa ada deskripsi khusus untuk kelas Anda?</p>
                                            <div class="mt-3">
                                                <div class="form-floating">
                                                    <textarea class="form-control" name="deskripsi" placeholder="Apa pendapat Anda?" style="height: 100px;"><?php echo htmlspecialchars($data_kelas['deskripsi']); ?></textarea>
                                                    <label>Kelas ini bertujuan untuk ..</label>
                                                </div>
                                            </div>
                                            <input type="hidden" name="kelas_id" value="<?php echo $kelas_id; ?>">
                                        </div>
                                        <div class="modal-footer d-flex">
                                            <button type="submit" class="btn btnPrimary text-white flex-fill">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div class="catatanGuru p-3 rounded-3 bg-white" style="border: 1px solid rgb(238, 238, 238);">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0"><strong>Catatan Guru</strong></h5>
        <button class="btn btnPrimary text-white d-flex align-items-center gap-2" 
                data-bs-toggle="modal" 
                data-bs-target="#catatanModal">
            <i class="bi bi-plus-circle"></i>
            <span>Tambah</span>
        </button>
    </div>

    <?php
    // Query untuk mengambil catatan guru
    $query_catatan = "SELECT * FROM catatan_guru WHERE kelas_id = '$kelas_id' ORDER BY created_at DESC";
    $result_catatan = mysqli_query($koneksi, $query_catatan);
    ?>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 1050;">
        <?php 
        if($_GET['success'] == 'catatan_deleted') {
            echo "Catatan berhasil dihapus!";
        }
        ?>
    </div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 1050;">
        <?php 
        if($_GET['error'] == 'delete_failed') {
            echo "Gagal menghapus catatan. Silakan coba lagi.";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<script>
// Auto hide alerts after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
});
</script>

    <?php if(mysqli_num_rows($result_catatan) > 0): ?>
        <div class="catatan-list">
            <?php while($catatan = mysqli_fetch_assoc($result_catatan)): ?>
                <div class="catatan-item p-3 rounded-3 mb-3" style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1"><strong><?php echo htmlspecialchars($catatan['judul']); ?></strong></h6>
                            <small class="text-muted d-flex align-items-center gap-1">
                                <i class="bi bi-calendar3"></i>
                                <?php echo date('d M Y', strtotime($catatan['created_at'])); ?>
                            </small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item text-danger" href="#" 
                                       onclick="if(confirm('Hapus catatan ini?')) window.location.href='hapus_catatan.php?id=<?php echo $catatan['id']; ?>&kelas_id=<?php echo $kelas_id; ?>'">
                                        <i class="bi bi-trash me-2"></i>Hapus
                                    </a>
                                </li>
                            </ul>
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
                                   class="text-decoration-none" target="_blank">
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
            <p class="text-muted " style="font-size: 18px;">Tidak ada catatan</p>
            <p class="text-muted mt-2 mb-0" style="font-size: 14px;">Belum ada catatan yang ditambahkan</p>
        </div>
    <?php endif; ?>
</div>

<style>
.catatan-item {
    transition: all 0.2s ease;
}

.catatan-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.file-attachment {
    transition: all 0.2s ease;
}

.file-attachment:hover {
    background-color: #f8f9fa !important;
}

@media (max-width: 768px) {
    .catatan-item {
        transform: none !important;
    }
    .daftarSiswa {
        display: none;
    }
}
</style>

<!-- Daftar Siswa -->
<div class="daftarSiswa p-3 rounded-3 bg-white mt-3" style="border: 1px solid rgb(238, 238, 238);">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0"><strong>Daftar Siswa</strong></h5>
        <p class="m-0 text-muted"><?php echo $jumlah_siswa; ?> siswa</p>
    </div>

    <?php if(mysqli_num_rows($result_siswa) > 0): ?>
        <div class="student-list">
            <div class="row g-2">
                <?php 
                // Mengambil 4 siswa terbaru
                $query_siswa_terbaru = "SELECT s.nama, s.foto_profil FROM siswa s 
                                      JOIN kelas_siswa ks ON s.id = ks.siswa_id 
                                      WHERE ks.kelas_id = '$kelas_id'
                                      ORDER BY ks.created_at DESC LIMIT 4";
                $result_siswa_terbaru = mysqli_query($koneksi, $query_siswa_terbaru);
                
                while($siswa = mysqli_fetch_assoc($result_siswa_terbaru)): 
                ?>
                    <div class="col-6">
                        <div class="student-card p-2 rounded-3 d-flex align-items-center gap-2" 
                             style="background-color: #f8f9fa;">
                            <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp.png'; ?>" 
                                 alt="Profile" class="rounded-circle" width="32" height="32">
                            <span class="student-name" style="font-size: 14px;">
                                <?php echo htmlspecialchars($siswa['nama']); ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Button Group -->
        <div class="d-flex gap-2 mt-3">
            <button class="btn color-web btn-light text-center flex-grow-1" 
                    data-bs-toggle="modal" 
                    data-bs-target="#lihatSemuaSiswaModal">
                <i class="bi bi-people text-white"></i>
            </button>
            <button class="btn color-web btn-light text-center flex-grow-1" 
                    data-bs-toggle="modal" 
                    data-bs-target="#tambahSiswaModal">
                <i class="bi bi-person-plus text-white"></i>
            </button>
            <button class="btn btn-danger text-center flex-grow-1" 
            data-bs-toggle="modal" 
            data-bs-target="#hapusSiswaModal">
            <i class="bi bi-person-x"></i>
        </button>
        </div>
    <?php else: ?>
        <div class="text-center py-4" style="background-color: #f8f9fa; border-radius: 8px;">
            <img src="assets/no-data.png" alt="Tidak ada siswa" style="width: 80px; opacity: 0.5;">
            <p class="text-muted mt-2 mb-0" style="font-size: 14px;">Belum ada siswa dalam kelas ini</p>
            
            <!-- Hanya tampilkan tombol tambah -->
            <div class="mt-3">
                <button class="btn color-web text-white d-flex align-items-center gap-2 mx-auto" 
                        data-bs-toggle="modal" 
                        data-bs-target="#tambahSiswaModal">
                    <i class="bi bi-person-plus"></i>
                    Tambah Siswa
                </button>
            </div>
        </div>
        <?php endif; ?>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="tambahSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Undang Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-4">
                    <i class="bi bi-qr-code text-primary display-1"></i>
                </div>
                <div class="mb-4">
                    <p class="mb-2 text-muted">Bagikan kode kelas ini kepada siswa</p>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <h4 class="m-0 fw-bold"><?php echo $kelas_id; ?></h4>
                        <button class="btn btn-light btn-sm" onclick="copyKodeKelas('<?php echo $kelas_id; ?>')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
                <div class="border-top pt-3">
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Siswa dapat bergabung dengan memasukkan kode kelas ini di menu "Gabung Kelas"
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Siswa -->
<div class="modal fade" id="hapusSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Hapus Siswa dari Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <?php
                $query_siswa_hapus = "SELECT s.id, s.nama, s.foto_profil, s.tingkat 
                                    FROM siswa s 
                                    JOIN kelas_siswa ks ON s.id = ks.siswa_id 
                                    WHERE ks.kelas_id = '$kelas_id' 
                                    ORDER BY s.nama ASC";
                $result_siswa_hapus = mysqli_query($koneksi, $query_siswa_hapus);
                
                if(mysqli_num_rows($result_siswa_hapus) > 0): 
                ?>
                    <div class="list-siswa">
                        <?php while($siswa = mysqli_fetch_assoc($result_siswa_hapus)): ?>
                            <div class="student-card p-2 rounded-3 d-flex align-items-center justify-content-between gap-2 mb-2" 
                                 style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp.png'; ?>" 
                                         alt="Profile" class="rounded-circle" width="40" height="40">
                                    <div>
                                        <div class="fw-medium"><?php echo htmlspecialchars($siswa['nama']); ?></div>
                                        <small class="text-muted">Kelas <?php echo htmlspecialchars($siswa['tingkat']); ?></small>
                                    </div>
                                </div>
                                <button class="btn btn-danger btn-sm" 
                                        onclick="hapusSiswa(<?php echo $siswa['id']; ?>, '<?php echo htmlspecialchars($siswa['nama']); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">Belum ada siswa dalam kelas ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk copy kode kelas
function copyKodeKelas(kode) {
    navigator.clipboard.writeText(kode).then(() => {
        alert('Kode kelas berhasil disalin!');
    });
}

// Fungsi untuk konfirmasi dan hapus siswa
function hapusSiswa(siswaId, namaSiswa) {
    if(confirm(`Apakah Anda yakin ingin menghapus ${namaSiswa} dari kelas ini?`)) {
        window.location.href = `hapus_siswa.php?siswa_id=${siswaId}&kelas_id=<?php echo $kelas_id; ?>`;
    }
}
</script>

<!-- Modal Lihat Semua Siswa -->
<div class="modal fade" id="lihatSemuaSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Daftar Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Statistik Siswa -->
                <div class="student-stats p-3 rounded-3 mb-3" style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px; background-color: rgba(218, 119, 86, 0.1);">
                            <i class="bi bi-people fs-4" style="color: rgb(218, 119, 86);"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Siswa</h6>
                            <h4 class="m-0"><strong><?php echo $jumlah_siswa; ?></strong> siswa</h4>
                        </div>
                    </div>
                </div>

                <!-- Daftar Siswa -->
                <?php
                $query_semua_siswa = "SELECT s.nama, s.foto_profil, s.tingkat 
                                    FROM siswa s 
                                    JOIN kelas_siswa ks ON s.id = ks.siswa_id 
                                    WHERE ks.kelas_id = '$kelas_id' 
                                    ORDER BY s.nama ASC";
                $result_semua_siswa = mysqli_query($koneksi, $query_semua_siswa);
                
                if(mysqli_num_rows($result_semua_siswa) > 0): 
                    while($siswa = mysqli_fetch_assoc($result_semua_siswa)): 
                ?>
                    <div class="student-card p-2 rounded-3 d-flex align-items-center gap-2 mb-2" 
                         style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                        <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp.png'; ?>" 
                             alt="Profile" class="rounded-circle" width="40" height="40">
                        <div>
                            <div class="fw-medium"><?php echo htmlspecialchars($siswa['nama']); ?></div>
                            <small class="text-muted">Kelas <?php echo htmlspecialchars($siswa['tingkat']); ?></small>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else: 
                ?>
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">Belum ada siswa dalam kelas ini</p>
                    </div>
                <?php 
                endif; 
                ?>
            </div>
        </div>
    </div>
</div>

<style>
.student-card {
    transition: all 0.2s ease;
    border: 1px solid #e9ecef;
}

.student-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.student-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: calc(100% - 40px); /* 40px untuk gambar + padding */
}
</style>

                            


<!-- Modal Tambah Catatan -->
<div class="modal fade" id="catatanModal" tabindex="-1" aria-labelledby="catatanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="catatanModalLabel"><strong>Tambah Catatan</strong></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="tambah_catatan.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Catatan</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Catatan</label>
                        <textarea class="form-control" name="konten" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran (opsional)</label>
                        <input type="file" class="form-control" name="file_lampiran">
                        <div class="form-text">Format yang didukung: PDF, DOC, DOCX, PPT, PPTX, JPG, PNG</div>
                    </div>
                    <input type="hidden" name="kelas_id" value="<?php echo $kelas_id; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btnPrimary text-white">Simpan Catatan</button>
                </div>
            </form>
        </div>
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