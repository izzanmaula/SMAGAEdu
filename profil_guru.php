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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <title>Profil - SMAGAEdu</title>
</head>
<style>
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
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Ujian</p>
                            </div>
                        </a>
                        
                        <!-- Menu Profil -->
                        <a href="profil_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded color-web p-2">
                                <img src="assets/profil_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Profil</p>
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
            <div class="col-auto vh-100 p-2 p-md-4 shadow-sm menu-samping d-none d-md-block" style="background-color:rgb(238, 236, 226)">
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
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Ujian</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="profil_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded shadow-sm bg-white p-2" style="">
                            <img src="assets/profil_fill.png" alt="" width="50px" class="pe-4">
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
                        <a href="bantuan_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/bantuan_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Bantuan</p>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="row dropdown">
                    <div class="btn d-flex align-items-center gap-3 p-2 rounded-3 border dropdown-toggle" style="background-color: #F8F8F7;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" alt="" width="30px" class="rounded-circle" style="background-color: white;">
                        <p class="p-0 m-0 text-truncate" style="font-size: 12px;"><?php echo $guru['namaLengkap']; ?></p>
                    </div>
                    <!-- dropdown menu option -->
                    <ul class="dropdown-menu" style="font-size: 12px;">
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="#">Keluar</a></li>
                      </ul>
                </div>
            </div>


            <!-- ini isi kontennya -->
            <div class="col col-inti p-0 p-md-3">
                <style>
                    .col-inti {
                        margin-left: 0;
                        padding-left: 5rem;
                        margin-top: 56px; /* Height of mobile navbar */
                        padding-right: 0 !important; /* Remove right padding */
                        max-width: 100%; /* Ensure content doesn't overflow */   
                        padding: 5rem;                         
                    }
                    @media (min-width: 768px) {
                        .col-inti {
                            margin-left: 13rem;
                            margin-top: 0;
                        }
                    }
                    @media screen and (max-width: 768px) {
                        .col-inti {
                            margin-left: 10px;
                            margin-top: 56px; /* Height of mobile navbar */
                        }
                        
                    }                
                </style>
                    <div style="
                    background-image: url('<?php echo !empty($guru['foto_latarbelakang']) ? 'uploads/background/'.$guru['foto_latarbelakang'] : 'assets/bg-profil.png'; ?>'); 
                    height: 300px; 
                    padding-top: 200px; 
                    margin-top: 15px; 
                    background-position: center; 
                    background-size: cover;
                    position: relative;" class="rounded text-white shadow-lg latar-belakang">
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 0;" class="rounded"></div>
                    <div class="ps-3" style="position: relative; z-index: 2;"></div>
                </div>
                <div style="text-align: center;">
                    <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" alt="" width="200px" class="rounded-circle" style="background-color: white; margin-top: -150px; z-index: 10; position: relative; border: 3px solid white;">
                </div>
                <div class="text-center mt-1">
                    <h3 class="p-0 m-1"><?php echo htmlspecialchars($guru['namaLengkap']); ?></h3>
                    <p class="p-0 m-0"><?php echo htmlspecialchars($guru['jabatan']); ?></p>
                </div>
                <div class="mt-2 text-center d-flex flex-wrap justify-content-center gap-2">
                    <button class="btn border bi-pencil-square" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#gantinama">    Edit Nama Anda</button>
                    <button class="btn border bi-image" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#gantifoto">    Edit Foto dan Latar Belakang</button>
                    <button class="btn border bi-asterisk" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#gantilebih">    Edit Rekam Anda</button>
                </div>
                <div class="px-5">
                    <hr class="text-muted">
                </div>
                <div class="col d-flex justify-content-center mt-2">
                    <div class="row ">
                        <!-- pendidikan sekolah sebelum dan saat ini -->
                        <div class="">
                            <div class="d-flex gap-3 flex-column flex-md-row">
                                <!-- pendidikan sebelumnya -->
                                <div class="border rounded-4 p-3 flex-fill">
                                    <img src="assets/lulusan.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0 pt-1">Lulusan</h6>
                                        <p class="p-0 m-0"><?php echo htmlspecialchars($guru['pendidikan_s1']); ?></p>        
                                    </div>
                                </div>
                                <div class="border rounded-4 p-3 flex-fill">
                                    <img src="assets/jabatan.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0 pt-1">Jabatan Saat Ini</h6>
                                        <p class="p-0 m-0"><?php echo htmlspecialchars($guru['jabatan']); ?></p>        
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <!-- sertifikasi -->
                         <div class="d-flex mt-3">
                            <div class="border rounded-4 p-3 flex-fill">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="assets/sertifikat.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Sertifikasi</h6>
                                        <p style="font-size: 12px;" class="text-muted p-0 m-0">Sertifikat yang dimiliki</p>        
                                    </div>    
                                </div>
                                <div class="p-3">
                                <?php if(!empty($guru['sertifikasi1'])): ?>
                                    <li><?php echo htmlspecialchars($guru['sertifikasi1']); ?></li>
                                <?php endif; ?>
                                <?php if(!empty($guru['sertifikasi2'])): ?>
                                    <li><?php echo htmlspecialchars($guru['sertifikasi2']); ?></li>
                                <?php endif; ?>
                                <?php if(!empty($guru['sertifikasi3'])): ?>
                                    <li><?php echo htmlspecialchars($guru['sertifikasi3']); ?></li>
                                <?php endif; ?>
                                </div>
                            </div>
                         </div>

                        <!-- riwayat publikasi -->
                        <div class="d-flex mt-3">
                            <div class="border rounded-4 p-3 flex-fill">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="assets/publikasi.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Publikasi</h6>
                                        <p style="font-size: 12px;" class="text-muted p-0 m-0">Riwayat penulisan yang telah di publikasi</p>        
                                    </div>    
                                </div>
                                <div class="p-3">
                                <?php if(!empty($guru['publikasi1'])): ?>
                                    <li><?php echo htmlspecialchars($guru['publikasi1']); ?></li>
                                <?php endif; ?>
                                <?php if(!empty($guru['publikasi2'])): ?>
                                    <li><?php echo htmlspecialchars($guru['publikasi2']); ?></li>
                                <?php endif; ?>
                                <?php if(!empty($guru['publikasi3'])): ?>
                                    <li><?php echo htmlspecialchars($guru['publikasi3']); ?></li>
                                <?php endif; ?>
                                        </div>
                            </div>
                         </div>

                        <!-- riwayat publikasi -->
                        <div class="d-flex mt-3">
                            <div class="border rounded-4 p-3 flex-fill">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="assets/proyek.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Riwayat Proyek</h6>
                                        <p style="font-size: 12px;" class="text-muted p-0 m-0">Riwayat proyek pendidikan yang telah di ikuti</p>        
                                    </div>    
                                </div>
                                <div class="p-3">
                                <?php if(!empty($guru['proyek1'])): ?>
                                    <li><?php echo htmlspecialchars($guru['proyek1']); ?></li>
                                <?php endif; ?>
                                <?php if(!empty($guru['proyek2'])): ?>
                                    <li><?php echo htmlspecialchars($guru['proyek2']); ?></li>
                                <?php endif; ?>
                                <?php if(!empty($guru['proyek3'])): ?>
                                    <li><?php echo htmlspecialchars($guru['proyek3']); ?></li>
                                <?php endif; ?>                                
                            </div>
                            </div>
                         </div>
                        </div>

                        
                    </div>
                    </div>

                </div>
            </div>

            
            <!-- modal untuk ganti nama -->
            <div class="modal fade" id="gantinama" tabindex="-1" aria-labelledby="modalgantinamalabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalgantinamalabel">Edit Profil</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="update_profil_guru.php" method="POST">
                <div class="modal-body">
                    <div class="form-floating">
                        <input type="text" name="nama" class="form-control" id="floatingInputValue" value="<?php echo htmlspecialchars($guru['namaLengkap']); ?>" required>
                        <label for="floatingInputValue">Nama dan Gelar</label>
                    </div>
                </div>
                <div class="modal-footer d-flex">
                    <button type="submit" name="update_nama" class="btn color-web flex-fill" style="color: white;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ganti foto -->
<div class="modal fade" id="gantifoto" tabindex="-1" aria-labelledby="modalgantifoto" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalgantifoto">Edit Foto Anda</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex p-2 gap-3">
                    <!-- Button untuk profil -->
                    <div class="btn ganti-latar text-center p-3 rounded-2 w-100">
                        <input type="file" id="input-foto-profil" class="d-none" accept="image/*">
                        <label for="input-foto-profil" class="mb-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="cursor: pointer;">
                            <img src="assets/profil_fill.png" width="40px" alt="">
                            <p style="font-size: 13px;" class="mb-0">Ubah Foto</p>
                        </label>
                    </div>
                    
                    <!-- Button untuk latar belakang -->
                    <div class="btn ganti-latar text-center p-3 rounded-2 w-100">
                        <input type="file" id="input-foto-latar" class="d-none" accept="image/*">
                        <label for="input-foto-latar" class="mb-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="cursor: pointer;">
                            <img src="assets/background.png" width="40px" alt="">
                            <p style="font-size: 13px;" class="mb-0">Ubah Latar Belakang</p>
                        </label>
                    </div>
                </div>

                <!-- Container untuk cropper -->
                <div id="cropper-container" class="mt-3 d-none">
                    <div class="img-container">
                        <img id="crop-image" src="" alt="Gambar untuk di-crop">
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-secondary me-2" id="cancel-crop">Batal</button>
                        <button type="button" class="btn color-web text-white" id="crop-submit">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form tersembunyi untuk submit hasil crop -->
<form id="crop-form" action="update_profil_guru.php" method="POST" class="d-none">
    <input type="hidden" name="cropped_image" id="cropped-image-input">
    <input type="hidden" name="image_type" id="image-type-input">
</form>

<script>
let cropper = null;
let currentImageType = null;

// Fungsi untuk menangani file yang dipilih
function handleImageSelect(e, imageType) {
    const file = e.target.files[0];
    if (file) {
        currentImageType = imageType;
        const reader = new FileReader();
        reader.onload = function(e) {
            const cropImage = document.getElementById('crop-image');
            cropImage.src = e.target.result;
            
            // Tampilkan container cropper
            document.getElementById('cropper-container').classList.remove('d-none');
            
            // Inisialisasi cropper
            if (cropper) {
                cropper.destroy();
            }
            
            // Konfigurasi berbeda untuk profil dan latar belakang
            const cropperOptions = imageType === 'profil' ? {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                cropBoxResizable: false,
                cropBoxMovable: false,
                minCropBoxWidth: 200,
                minCropBoxHeight: 200
            } : {
                aspectRatio: 16/9,
                viewMode: 1,
                dragMode: 'move'
            };
            
            cropper = new Cropper(cropImage, cropperOptions);
        };
        reader.readAsDataURL(file);
    }
}

// Event listener untuk input file
document.getElementById('input-foto-profil').addEventListener('change', (e) => handleImageSelect(e, 'profil'));
document.getElementById('input-foto-latar').addEventListener('change', (e) => handleImageSelect(e, 'latar'));

// Event listener untuk tombol batal
document.getElementById('cancel-crop').addEventListener('click', function() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('cropper-container').classList.add('d-none');
    document.getElementById('input-foto-profil').value = '';
    document.getElementById('input-foto-latar').value = '';
});

// Event listener untuk tombol simpan
document.getElementById('crop-submit').addEventListener('click', function() {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 2048,
            maxHeight: 2048,
            fillColor: '#fff'
        });
        
        const croppedImageData = canvas.toDataURL('image/jpeg');
        document.getElementById('cropped-image-input').value = croppedImageData;
        document.getElementById('image-type-input').value = currentImageType;
        
        // Submit form
        document.getElementById('crop-form').submit();
    }
});

// Reset cropper saat modal ditutup
document.getElementById('gantifoto').addEventListener('hidden.bs.modal', function () {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('cropper-container').classList.add('d-none');
    document.getElementById('input-foto-profil').value = '';
    document.getElementById('input-foto-latar').value = '';
});
</script>

<style>
.img-container {
    max-height: 400px;
}
.img-container img {
    max-width: 100%;
    max-height: 100%;
}
</style>


<!-- Modal edit rekam -->
<div class="modal fade" id="gantilebih" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalgantinamalabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalgantinamalabel">Edit Deskripsi Guru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="update_profil_guru.php" method="POST">
                <div class="modal-body">
                    <!-- Riwayat Pendidikan -->
                    <div class="mt-3">
                        <label for="pendidikansebelumnya" class="form-label" style="font-size: 13px;">Riwayat Pendidikan</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text">S1</span>
                            <input type="text" name="pendidikan_s1" class="form-control" value="<?php echo htmlspecialchars($guru['pendidikan_s1']); ?>" placeholder="Contoh: Universitas Muhammadiyah Surakarta">    
                        </div>      
                        <div class="input-group mb-2">
                            <span class="input-group-text">S2</span>
                            <input type="text" name="pendidikan_s2" class="form-control" value="<?php echo htmlspecialchars($guru['pendidikan_s2']); ?>" placeholder="Contoh: Universitas Gajah Mada">    
                        </div>             
                        <div class="input-group mb-2">
                            <span class="input-group-text">Lainnya</span>
                            <input type="text" name="pendidikan_lainnya" class="form-control" value="<?php echo htmlspecialchars($guru['pendidikan_lainnya']); ?>" placeholder="Contoh: Pelatihan/Pendidikan lainnya">    
                        </div>                    
                    </div>

                    <!-- jabatan -->
                    <div class="mt-3">
                        <label class="form-label p-0 m-0" style="font-size: 13px;">Jabatan Sekolah Saat ini</label>
                        <div id="jabatan" class="form-text p-0 m-0" style="font-size: 12px;">Pilih jabatan tertinggi Anda</div>
                        <select class="form-select" name="jabatan">
                            <option value="">Pilih salah satu</option>
                            <option value="Kepala Sekolah" <?php echo ($guru['jabatan'] == 'Kepala Sekolah') ? 'selected' : ''; ?>>Kepala Sekolah</option>
                            <option value="Wakil Kepala Sekolah" <?php echo ($guru['jabatan'] == 'Wakil Kepala Sekolah') ? 'selected' : ''; ?>>Wakil Kepala Sekolah</option>
                            <option value="Bag. Kurikulum" <?php echo ($guru['jabatan'] == 'Bag. Kurikulum') ? 'selected' : ''; ?>>Bag. Kurikulum</option>
                            <option value="Bag. Kesiswaan" <?php echo ($guru['jabatan'] == 'Bag. Kesiswaan') ? 'selected' : ''; ?>>Bag. Kesiswaan</option>
                            <option value="Kepala Tata Usaha" <?php echo ($guru['jabatan'] == 'Kepala Tata Usaha') ? 'selected' : ''; ?>>Kepala Tata Usaha</option>
                            <option value="Wali Kelas" <?php echo ($guru['jabatan'] == 'Wali Kelas') ? 'selected' : ''; ?>>Wali Kelas</option>
                            <option value="Bag. Ekonomi Bisnis" <?php echo ($guru['jabatan'] == 'Bag. Ekonomi Bisnis') ? 'selected' : ''; ?>>Bag. Ekonomi Bisnis</option>
                            <option value="Staf IT" <?php echo ($guru['jabatan'] == 'Staf IT') ? 'selected' : ''; ?>>Staf IT</option>
                            <option value="Staf TU" <?php echo ($guru['jabatan'] == 'Staf TU') ? 'selected' : ''; ?>>Staf TU</option>
                        </select>  
                    </div>

                    <!-- Sertifikasi -->
                    <div class="mt-3">
                        <label class="form-label p-0 m-0" style="font-size: 13px;">Sertifikasi</label>
                        <p class="text-muted p-0 m-0" style="font-size: 12px;">Pilih sertifikasi terbaru Anda</p>
                        <div class="input-group mb-2">
                            <span class="input-group-text">1</span>
                            <input type="text" name="sertifikasi1" class="form-control" value="<?php echo htmlspecialchars($guru['sertifikasi1']); ?>" placeholder="Masukkan sertifikasi pertama">    
                        </div>      
                        <div class="input-group mb-2">
                            <span class="input-group-text">2</span>
                            <input type="text" name="sertifikasi2" class="form-control" value="<?php echo htmlspecialchars($guru['sertifikasi2']); ?>" placeholder="Masukkan sertifikasi kedua">    
                        </div>             
                        <div class="input-group mb-2">
                            <span class="input-group-text">3</span>
                            <input type="text" name="sertifikasi3" class="form-control" value="<?php echo htmlspecialchars($guru['sertifikasi3']); ?>" placeholder="Masukkan sertifikasi ketiga">    
                        </div>                    
                    </div>

                    <!-- Publikasi -->
                    <div class="mt-3">
                        <label class="form-label p-0 m-0" style="font-size: 13px;">Publikasi</label>
                        <p class="text-muted p-0 m-0" style="font-size: 12px;">Pilih publikasi terbaru Anda</p>
                        <div class="input-group mb-2">
                            <span class="input-group-text">1</span>
                            <input type="text" name="publikasi1" class="form-control" value="<?php echo htmlspecialchars($guru['publikasi1']); ?>" placeholder="Masukkan publikasi pertama">    
                        </div>      
                        <div class="input-group mb-2">
                            <span class="input-group-text">2</span>
                            <input type="text" name="publikasi2" class="form-control" value="<?php echo htmlspecialchars($guru['publikasi2']); ?>" placeholder="Masukkan publikasi kedua">    
                        </div>             
                        <div class="input-group mb-2">
                            <span class="input-group-text">3</span>
                            <input type="text" name="publikasi3" class="form-control" value="<?php echo htmlspecialchars($guru['publikasi3']); ?>" placeholder="Masukkan publikasi ketiga">    
                        </div>                    
                    </div>

                    <!-- Proyek -->
                    <div class="mt-3">
                        <label class="form-label" style="font-size: 13px;">Proyek</label>
                        <p class="text-muted p-0 m-0" style="font-size: 12px;">Pilih proyek terbaru Anda</p>
                        <div class="input-group mb-2">
                            <span class="input-group-text">1</span>
                            <input type="text" name="proyek1" class="form-control" value="<?php echo htmlspecialchars($guru['proyek1']); ?>" placeholder="Masukkan proyek pertama">    
                        </div>      
                        <div class="input-group mb-2">
                            <span class="input-group-text">2</span>
                            <input type="text" name="proyek2" class="form-control" value="<?php echo htmlspecialchars($guru['proyek2']); ?>" placeholder="Masukkan proyek kedua">    
                        </div>             
                        <div class="input-group mb-2">
                            <span class="input-group-text">3</span>
                            <input type="text" name="proyek3" class="form-control" value="<?php echo htmlspecialchars($guru['proyek3']); ?>" placeholder="Masukkan proyek ketiga">    
                        </div>                    
                    </div>
                </div>
                <div class="modal-footer d-flex">
                    <button type="submit" name="update_info" class="btn color-web flex-fill" style="color: white;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>