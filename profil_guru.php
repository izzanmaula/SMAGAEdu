<?php
session_start();
require "koneksi.php";
require_once 'ai_analysis.php';


if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

// Cek apakah ada parameter username
if(isset($_GET['username'])) {
    $username = mysqli_real_escape_string($koneksi, $_GET['username']);
} else {
    // Jika tidak ada parameter, gunakan username dari session
    $username = $_SESSION['userid'];
}

// Ambil data guru berdasarkan username
$query = "SELECT * FROM guru WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);

// Jika guru tidak ditemukan, redirect ke halaman sebelumnya
if(!$guru) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$report = generateAIReport($koneksi, $_SESSION['userid'], $_SESSION['level']);
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

<?php include 'includes/styles.php'; ?>

<div class="container-fluid">
        <div class="row">
            <!-- Sidebar for desktop -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Mobile navigation -->
            <?php include 'includes/mobile_nav.php'; ?>

            <!-- Settings Modal -->
            <?php include 'includes/settings_modal.php'; ?>

            
        </div>
    </div>    

            <!-- ini isi kontennya -->
            <div class="col col-inti p-0 p-md-3">
                <style>
                    .col-inti {
                        margin-left: 0;
                        padding: 1rem;
                        max-width: 100%; /* Ensure content doesn't overflow */                      
                    }
                    @media (min-width: 768px) {
                        .col-inti {
                            margin-left: 13rem;
                            margin-top: 0;
                            padding: 2rem;
                        }
                    }
                    @media screen and (max-width: 768px) {
                        .col-inti {
                            margin-left: 0.5rem;
                            margin-right: 0.5rem;
                            padding: 1rem;
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
                        position: relative;" 
                        class="rounded text-white shadow-lg latar-belakang">
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 0;" class="rounded"></div>
                        <div class="ps-3" style="position: relative; z-index: 2;"></div>
                    </div>
                    <style>
                        @media (max-width: 768px) {
                            .latar-belakang {
                                height: 200px;
                                padding-top: 150px;
                            }
                        }
                    </style>
                <div style="text-align: center;">
                    <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" alt="" width="200px" class="rounded-circle" style="background-color: white; margin-top: -150px; z-index: 10; position: relative; border: 3px solid white;">
                </div>
                <div class="text-center mt-1">
                    <h3 class="p-0 m-1"><?php echo htmlspecialchars($guru['namaLengkap']); ?></h3>
                    <p class="p-0 m-0"><?php echo htmlspecialchars($guru['jabatan']); ?></p>
                </div>
                <div class="mt-2 text-center d-flex flex-column flex-md-row flex-wrap justify-content-center gap-2">
                    <button class="btn border d-flex align-items-center justify-content-center gap-2 px-3 py-2 hover-effect" style="font-size: 14px; min-width: 160px;" data-bs-toggle="modal" data-bs-target="#gantinama">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit Nama</span>
                    </button>
                    <button class="btn border d-flex align-items-center justify-content-center gap-2 px-3 py-2 hover-effect" style="font-size: 14px; min-width: 160px;" data-bs-toggle="modal" data-bs-target="#gantifoto">
                        <i class="bi bi-image"></i>
                        <span>Edit Foto</span>
                    </button>
                    <button class="btn border d-flex align-items-center justify-content-center gap-2 px-3 py-2 hover-effect" style="font-size: 14px; min-width: 160px;" data-bs-toggle="modal" data-bs-target="#gantilebih">
                        <i class="bi bi-asterisk"></i>
                        <span>Edit Profil</span>
                    </button>
                </div>

<style>
.hover-effect {
    transition: all 0.3s ease;
    border: 1px solid #ddd;
    background: white;
}

.hover-effect:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    background: rgb(218, 119, 86);
    color: white;
    border-color: transparent;
}
</style>
                <div class="col d-flex justify-content-center mt-2">
                    <div class="row ">
                        <!-- pendidikan sekolah sebelum dan saat ini -->
                        <div class="">
                            <div class="d-flex gap-3 flex-column flex-md-row">
                                <!-- pendidikan sebelumnya -->
                                <?php if(!empty($guru['pendidikan_s1'])): ?>
                                <div class="border rounded-4 p-3 flex-fill">
                                    <img src="assets/lulusan.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0 pt-1">Lulusan</h6>
                                        <p class="p-0 m-0"><?php echo htmlspecialchars($guru['pendidikan_s1']); ?></p>        
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if(!empty($guru['jabatan'])): ?>
                                <div class="border rounded-4 p-3 flex-fill">
                                    <img src="assets/jabatan.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0 pt-1">Jabatan Saat Ini</h6>
                                        <p class="p-0 m-0"><?php echo htmlspecialchars($guru['jabatan']); ?></p>        
                                    </div>
                                </div>    
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- sertifikasi -->
                        <?php if(!empty($guru['sertifikasi1']) || !empty($guru['sertifikasi2']) || !empty($guru['sertifikasi3'])): ?>
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
                         <?php endif; ?>

                        <!-- riwayat publikasi -->
                        <?php if(!empty($guru['publikasi1']) || !empty($guru['publikasi2']) || !empty($guru['publikasi3'])): ?>
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
                         <?php endif; ?>

                        <!-- riwayat proyek -->
                        <?php if(!empty($guru['proyek1']) || !empty($guru['proyek2']) || !empty($guru['proyek3'])): ?>
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
                         <?php endif; ?>

                            <!-- Interaction Stats -->
                            <div class="d-flex mt-3">
                                <div class="border rounded-4 p-3 flex-fill">
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="assets/chat_ai.png" alt="" width="35px" height="35px" class="rounded">
                                        <div>
                                            <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Interaksi Anda dengan SMAGA AI</p>
                                            <p style="font-size: 12px;" class="text-muted p-0 m-0">Total interaksi dan pola komunikasi dengan AI</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3 g-2">
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <p style="font-size: 12px;" class="text-muted m-0">Total Interaksi</p>
                                                <h3 style="font-size: 20px;" class="m-0"><?php echo $report['user_info']['total_conversations']; ?></h3>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <p style="font-size: 12px;" class="text-muted m-0">Total Pertanyaan</p>
                                                <h3 style="font-size: 20px;" class="m-0"><?php echo $report['conversation_analysis']['interaction_patterns']['question_frequency']; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                         <!-- Character Traits -->
                            <div class="d-flex mt-3">
                                <div class="border rounded-4 p-3 flex-fill">
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="assets/karakter_ai.png" alt="" width="35px" height="35px" class="rounded">
                                        <div>
                                            <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Rekaman Karakter Anda</p>
                                            <p style="font-size: 12px;" class="text-muted p-0 m-0">Analisis karakter berdasarkan interaksi SMAGA AI</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <?php foreach ($report['conversation_analysis']['character_traits'] as $trait => $value): ?>
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span style="font-size: 12px;"><?php echo ucfirst($trait); ?></span>
                                                <span style="font-size: 12px;" class="text-muted"><?php echo number_format($value * 100, 0); ?>%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar color-web" role="progressbar" style="width: <?php echo $value * 100; ?>%"></div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Common Topics -->
                            <div class="d-flex mt-3">
                                <div class="border rounded-4 p-3 flex-fill">
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="assets/topik_ai.png" alt="" width="35px" height="35px" class="rounded">
                                        <div>
                                            <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Topik Paling Dibahas</p>
                                            <p style="font-size: 12px;" class="text-muted p-0 m-0">Topik yang sering dibahas dengan SMAGA AI</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <?php foreach ($report['conversation_analysis']['common_topics'] as $topic => $count): ?>
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span style="font-size: 12px;"><?php echo ucfirst($topic); ?></span>
                                                <span style="font-size: 12px;" class="text-muted"><?php echo $count; ?> kali</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar color-web" role="progressbar" 
                                                    style="width: <?php echo ($count / array_sum($report['conversation_analysis']['common_topics']) * 100); ?>%">
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- style untuk ai -->
                             <style>
                            .progress-bar {
                                transition: width 0.5s ease-in-out;
                            }
                            </style>
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
<div class="modal fade rounded" id="gantifoto" tabindex="-1" aria-labelledby="modalgantifoto" aria-hidden="true">
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