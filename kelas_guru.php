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
<?php include 'includes/styles.php'; ?>
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


            <!-- konten inti -->
            <div class="col col-inti p-0 p-md-3">
                <style>
                    .col-inti {
                        margin-left: 0;
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
                        <!-- Post Creator Card -->
                        <div class="create-post-card bg-white rounded-3 p-3 mb-4 border">
                            <!-- Desktop View -->
                            <div class="d-none d-md-flex align-items-center gap-3">
                                <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                     alt="Profile" class="rounded-circle" width="45" height="45" style="object-fit: cover;">
                                <div class="flex-grow-1">
                                    <button class="btn w-100 text-start px-4 rounded-pill border bg-light hover-bg" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalTambahPostingan">
                                        <span class="text-muted">Apa yang ingin Anda diskusikan dengan siswa?</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Mobile View -->
                            <div class="d-flex d-md-none gap-2">
                                <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                     alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                <button class="flex-grow-1 btn text-start rounded-pill border bg-light" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalTambahPostingan">
                                    <span class="text-muted" style="font-size: 0.9rem;">Mulai diskusi...</span>
                                </button>
                            </div>

                            <!-- Quick Actions -->
                            <div class="d-flex justify-content-around mt-3 pt-2 border-top">
                                <button class="btn btn-light flex-grow-1 me-2 d-flex align-items-center justify-content-center gap-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalTambahPostingan">
                                    <i class="bi bi-image text-success"></i>
                                    <span class="d-none d-md-inline">Foto/Video</span>
                                </button>
                                <button class="btn btn-light flex-grow-1 me-2 d-flex align-items-center justify-content-center gap-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalTambahPostingan">
                                    <i class="bi bi-file-earmark-text text-primary"></i>
                                    <span class="d-none d-md-inline">Dokumen</span>
                                </button>
                                <button class="btn btn-light flex-grow-1 d-flex align-items-center justify-content-center gap-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalTambahPostingan">
                                    <i class="bi bi-link-45deg text-warning"></i>
                                    <span class="d-none d-md-inline">Link</span>
                                </button>
                            </div>
                        </div>

                        <style>
                        .create-post-card {
                            transition: all 0.2s ease;
                        }

                        .hover-bg:hover {
                            background-color: #f0f0f0 !important;
                        }

                        .btn-light:hover {
                            background-color: #e9ecef;
                        }

                        @media (max-width: 768px) {
                            .create-post-card {
                                padding: 12px !important;
                                margin-bottom: 12px !important;
                            }
                            
                            .quick-actions button {
                                padding: 8px !important;
                            }
                        }
                        </style>
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
                                    <button class="btn p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi-three-dots-vertical text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end animate slideIn">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="#" onclick="hapusPostingan(<?php echo $post['id']; ?>)">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                                Hapus Postingan
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <style>
                            /* Animasi dropdown */
                            .animate {
                                animation-duration: 0.2s;
                                animation-fill-mode: both;
                                transform-origin: top center;
                            }

                            @keyframes slideIn {
                                0% {
                                    transform: scaleY(0);
                                    opacity: 0;
                                }
                                100% {
                                    transform: scaleY(1);
                                    opacity: 1;
                                }
                            }

                            .slideIn {
                                animation-name: slideIn;
                            }

                            /* Style dropdown */
                            .dropdown-menu {
                                padding: 0.5rem 0;
                                border-radius: 8px;
                                border: 1px solid rgba(0,0,0,0.08);
                                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                            }

                            .dropdown-item {
                                padding: 0.5rem 1rem;
                                font-size: 14px;
                            }

                            .dropdown-item:hover {
                                background-color: #f8f9fa;
                            }
                            </style>
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
                                        $imageCount = count($images);
                                        echo '<div class="image-container-'.$imageCount.' mt-2">';
                                        
                                        switch($imageCount) {
                                            case 1:
                                                // Single image - full width
                                                echo '<div class="single-image">';
                                                echo '<img src="'.$images[0]['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                                echo '</div>';
                                                break;
                                                
                                            case 2:
                                                // Two images side by side
                                                echo '<div class="dual-images">';
                                                foreach($images as $image) {
                                                    echo '<img src="'.$image['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                                }
                                                echo '</div>';
                                                break;
                                                
                                            case 3:
                                                // Two images top, one bottom
                                                echo '<div class="triple-images">';
                                                echo '<div class="top-images">';
                                                echo '<img src="'.$images[0]['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                                echo '<img src="'.$images[1]['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                                echo '</div>';
                                                echo '<div class="bottom-image">';
                                                echo '<img src="'.$images[2]['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                                echo '</div>';
                                                echo '</div>';
                                                break;
                                                
                                            case 4:
                                                // Two rows of two images
                                                echo '<div class="quad-images">';
                                                echo '<div class="image-row">';
                                                echo '<img src="'.$images[0]['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                                echo '<img src="'.$images[1]['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                                echo '</div>';
                                                echo '<div class="image-row">';
                                                echo '<img src="'.$images[2]['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                                echo '<img src="'.$images[3]['path_file'].'" alt="Lampiran" onclick="showImage(this.src)">';
                                                echo '</div>';
                                                echo '</div>';
                                                break;
                                        }
                                        echo '</div>';
                                        
                                        // Add CSS styles
                                        echo '<style>
                                            .single-image img {
                                                width: 100%;
                                                border-radius: 15px;
                                                cursor: pointer;
                                                height: 400px;
                                                object-fit: cover;
                                            }
                                            .dual-images {
                                                display: grid;
                                                grid-template-columns: 1fr 1fr;
                                                gap: 2px;
                                            }
                                            .dual-images img {
                                                width: 100%;
                                                height: 300px;
                                                object-fit: cover;
                                                cursor: pointer;
                                            }
                                            .dual-images img:first-child {
                                                border-radius: 15px 0 0 15px;
                                            }
                                            .dual-images img:last-child {
                                                border-radius: 0 15px 15px 0;
                                            }
                                            .triple-images .top-images {
                                                display: grid;
                                                grid-template-columns: 1fr 1fr;
                                                gap: 2px;
                                                margin-bottom: 2px;
                                            }
                                            .triple-images .top-images img {
                                                width: 100%;
                                                height: 200px;
                                                object-fit: cover;
                                                cursor: pointer;
                                            }
                                            .triple-images .top-images img:first-child {
                                                border-radius: 15px 0 0 0;
                                            }
                                            .triple-images .top-images img:last-child {
                                                border-radius: 0 15px 0 0;
                                            }
                                            .triple-images .bottom-image img {
                                                width: 100%;
                                                height: 300px;
                                                object-fit: cover;
                                                cursor: pointer;
                                                border-radius: 0 0 15px 15px;
                                            }
                                            .quad-images {
                                                display: grid;
                                                gap: 2px;
                                            }
                                            .quad-images .image-row {
                                                display: grid;
                                                grid-template-columns: 1fr 1fr;
                                                gap: 2px;
                                            }
                                            .quad-images img {
                                                width: 100%;
                                                height: 200px;
                                                object-fit: cover;
                                                cursor: pointer;
                                            }
                                            .quad-images .image-row:first-child img:first-child {
                                                border-radius: 15px 0 0 0;
                                            }
                                            .quad-images .image-row:first-child img:last-child {
                                                border-radius: 0 15px 0 0;
                                            }
                                            .quad-images .image-row:last-child img:first-child {
                                                border-radius: 0 0 0 15px;
                                            }
                                            .quad-images .image-row:last-child img:last-child {
                                                border-radius: 0 0 15px 0;
                                            }
                                            @media (max-width: 768px) {
                                                .single-image img {
                                                    height: 250px;
                                                }
                                                .dual-images img {
                                                    height: 200px;
                                                }
                                                .triple-images .top-images img {
                                                    height: 150px;
                                                }
                                                .triple-images .bottom-image img {
                                                    height: 200px;
                                                }
                                                .quad-images img {
                                                    height: 150px;
                                                }
                                            }
                                        </style>';
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
                                    <button class="btn btn-light flex-fill py-1 py-md-2 d-flex align-items-center justify-content-center gap-2" 
                                            id="like-btn-<?php echo $post['id']; ?>"
                                            onclick="toggleLike(<?php echo $post['id']; ?>)">
                                        <i class="bi <?php echo $is_liked ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up'; ?>"></i>
                                        <span class="d-none d-md-inline">Suka</span>
                                    </button>
                                    <button class="btn btn-light flex-fill py-1 py-md-2 d-flex align-items-center justify-content-center gap-2" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#commentModal-<?php echo $post['id']; ?>">
                                        <i class="bi bi-chat"></i>
                                        <span class="d-none d-md-inline">Komentar</span>
                                    </button>
                                    <button class="btn btn-light flex-fill py-1 py-md-2 d-flex align-items-center justify-content-center gap-2" 
                                            onclick='sharePost(<?php echo $post["id"]; ?>, <?php echo json_encode($post["konten"]); ?>)'>
                                        <i class="bi bi-share"></i>
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
                                            $query_komentar = "SELECT k.*, 
                                                                CASE 
                                                                    WHEN g.username IS NOT NULL THEN 'guru'
                                                                    ELSE 'siswa'
                                                                END as user_type,
                                                                g.foto_profil as foto_guru,
                                                                s.foto_profil as foto_siswa,
                                                                COALESCE(g.namaLengkap, s.nama) as nama_user 
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
                                                                <?php if($komentar['user_type'] == 'guru'): ?>
                                                                    <img src="<?php echo $komentar['foto_guru'] ? 'uploads/profil/'.$komentar['foto_guru'] : 'assets/pp.png'; ?>" 
                                                                        alt="" width="32px" height="32px" class="rounded-circle border">
                                                                <?php else: ?>
                                                                    <img src="<?php echo $komentar['foto_siswa'] ? $komentar['foto_siswa'] : 'assets/pp-siswa.png'; ?>" 
                                                                        alt="" width="32px" height="32px" class="rounded-circle border">
                                                                <?php endif; ?>
                                                                </div>
                                                                <div class="bubble-chat flex-grow-1">
                                                                    <div class="d-flex justify-content-between align-items-start rounded-4 p-3" style="background-color: #f0f2f5;">
                                                                        <div>
                                                                            <h6 class="p-0 m-0 mb-1" style="font-size: 13px; font-weight: 600;">
                                                                                <?php echo htmlspecialchars($komentar['nama_user']); ?>
                                                                            </h6>
                                                                            <p class="p-0 m-0 text-break" style="font-size: 13px; line-height: 1.4; word-wrap: break-word; max-width: 100%;">
                                                                                <?php echo nl2br(htmlspecialchars($komentar['konten'])); ?>
                                                                            </p>
                                                                        </div>
                                                                        <?php if($komentar['user_id'] == $_SESSION['userid'] || $_SESSION['level'] == 'guru'): ?>
                                                                        <div class="dropdown">
                                                                            <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                                                                <i class="fas fa-ellipsis-v"></i>
                                                                            </button>
                                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                                <li>
                                                                                    <button class="dropdown-item text-danger" 
                                                                                            onclick="hapusKomentar(<?php echo $komentar['id']; ?>, <?php echo $post['id']; ?>)">
                                                                                        <i class="fas fa-trash-alt me-2"></i>Hapus
                                                                                    </button>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <?php endif; ?>
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

<script>
function hapusKomentar(komentarId, postId) {
    if(confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
        fetch('hapus_komentar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `komentar_id=${komentarId}&post_id=${postId}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Refresh modal content
                location.reload();
            } else {
                alert('Gagal menghapus komentar: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus komentar');
        });
    }
}
</script>
                                            <div class="modal-footer p-2 border-top">
                                                <div class="d-flex gap-2 align-items-end w-100">
                                                    <div class="flex-shrink-0">
                                                    <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                                            alt="Profile Image" 
                                                            class="profile-img rounded-4 border-0 bg-white"
                                                            style="width: 35px;;">
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
                                            const container = document.querySelector(`#commentModal-${postId} .komentar-container`);
                                            const komentarHTML = `
                                                <div class="mb-3">
                                                    <div class="d-flex gap-3 ">
                                                        <div>
                                                            <img src="uploads/profil/${data.komentar.foto_profil}" onerror="this.src='assets/pp.png'" width="40px" class="rounded-circle border">
                                                        </div>
                                                        <div class="pt-2 pb-2 pe-4 ps-3 rounded-4" style="background-color: rgb(238, 238, 238);">
                                                            <h6 class="p-0 m-0" style="font-size: 12px;">${data.komentar.nama_user}</h6>
                                                            <p class="p-0 m-0" style="font-size: 14px;">${data.komentar.konten}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                            container.innerHTML += komentarHTML;
                                            document.querySelector(`#commentModal-${postId} textarea`).value = '';
                                                
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
                    <!-- Button download dibawah gambar -->
                    <script>
                    function downloadImage(imageUrl, fileName) {
                        fetch(imageUrl)
                            .then(response => response.blob())
                            .then(blob => {
                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.style.display = 'none';
                                a.href = url;
                                a.download = fileName || 'download.jpg';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);
                                document.body.removeChild(a);
                            })
                            .catch(error => console.error('Error downloading image:', error));
                    }

                    // Add download button to modal
                    document.getElementById('imageModal').querySelector('.modal-body').innerHTML += `
                        <div class="text-center mt-3">
                            <button class="btn btnPrimary text-white" onclick="downloadImage(document.getElementById('modalImage').src)">
                                <i class="bi bi-download me-2"></i>Download Gambar
                            </button>
                        </div>
                    `;
                    </script>
                    <div class="col">
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


                        <div class="catatanGuru p-4 rounded-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-journal-text fs-4" style="color: rgb(218, 119, 86);"></i>
                                    <h5 class="m-0"><strong>Catatan Guru</strong></h5>
                                </div>
                                <button class="btn btnPrimary text-white d-flex align-items-center gap-2 px-3" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#catatanModal">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>

                            <?php
                            $query_catatan = "SELECT * FROM catatan_guru WHERE kelas_id = '$kelas_id' ORDER BY created_at DESC";
                            $result_catatan = mysqli_query($koneksi, $query_catatan);
                            ?>

                            <?php if(isset($_GET['success'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <?php echo ($_GET['success'] == 'catatan_deleted') ? "Catatan berhasil dihapus!" : ""; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if(isset($_GET['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    <?php echo ($_GET['error'] == 'delete_failed') ? "Gagal menghapus catatan" : ""; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if(mysqli_num_rows($result_catatan) > 0): ?>
                                <div class="catatan-list">
                                    <?php while($catatan = mysqli_fetch_assoc($result_catatan)): ?>
                                        <div class="catatan-item p-4 rounded-4 mb-3 bg-light border">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-2 fw-bold"><?php echo htmlspecialchars($catatan['judul']); ?></h6>
                                                    <div class="d-flex align-items-center text-muted mb-3" style="font-size: 0.85rem;">
                                                        <i class="bi bi-calendar3 me-2"></i>
                                                        <?php echo date('d F Y', strtotime($catatan['created_at'])); ?>
                                                    </div>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm animate slideIn">
                                                        <li>
                                                            <a class="dropdown-item text-danger d-flex align-items-center" href="#" 
                                                               onclick="return confirm('Apakah Anda yakin ingin menghapus catatan ini?') && 
                                                                       (window.location.href='hapus_catatan.php?id=<?php echo $catatan['id']; ?>&kelas_id=<?php echo $kelas_id; ?>')">
                                                                <i class="bi bi-trash me-2"></i>Hapus Catatan
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <style>
                                                /* Animasi dropdown */
                                                .animate {
                                                    animation-duration: 0.2s;
                                                    animation-fill-mode: both;
                                                    transform-origin: top center;
                                                }

                                                @keyframes slideIn {
                                                    0% {
                                                        transform: scaleY(0);
                                                        opacity: 0;
                                                    }
                                                    100% {
                                                        transform: scaleY(1);
                                                        opacity: 1;
                                                    }
                                                }

                                                .slideIn {
                                                    animation-name: slideIn;
                                                }
                                                </style>
                                            </div>
                                            
                                            <div class="catatan-content">
                                                <p class="mb-3 text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                                                    <?php echo nl2br(htmlspecialchars($catatan['konten'])); ?>
                                                </p>
                                                <?php if($catatan['file_lampiran']): ?>
                                                    <a href="<?php echo htmlspecialchars($catatan['file_lampiran']); ?>" 
                                                       class="text-decoration-none d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-white border hover-shadow"
                                                       target="_blank">
                                                        <?php
                                                        $ext = pathinfo($catatan['file_lampiran'], PATHINFO_EXTENSION);
                                                        $icon = match($ext) {
                                                            'pdf' => 'bi-file-pdf-fill text-danger',
                                                            'doc','docx' => 'bi-file-word-fill text-primary',
                                                            'jpg','jpeg','png' => 'bi-file-image-fill text-success',
                                                            default => 'bi-file-earmark-fill text-secondary'
                                                        };
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
                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                        Mulai tambahkan catatan untuk kelas ini
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <style>
                        .catatan-item {
                            transition: all 0.2s ease;
                        }

                        .catatan-item:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                        }

                        .hover-shadow {
                            transition: all 0.2s ease;
                        }

                        .hover-shadow:hover {
                            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                        }

                        .catatanGuru {
                            border: 1px solid #dee2e6;
                        }

                        @media (max-width: 768px) {
                            .catatanGuru {
                                display: none;
                            }
                        }
                        </style>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Auto hide alerts
                            setTimeout(() => {
                                document.querySelectorAll('.alert').forEach(alert => {
                                    const bsAlert = new bootstrap.Alert(alert);
                                    bsAlert.close();
                                });
                            }, 3000);
                        });
                        </script>

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
<div class="daftarSiswa p-4 rounded-3 bg-white mt-3 border">
    <!-- Header dengan statistik -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="stats-icon rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 42px; height: 42px; background-color: rgba(218, 119, 86, 0.1);">
                <i class="bi bi-people fs-5" style="color: rgb(218, 119, 86);"></i>
            </div>
            <div>
                <h6 class="mb-1">Daftar Siswa</h6>
                <p class="m-0 text-muted" style="font-size: 14px;"><?php echo $jumlah_siswa; ?> siswa terdaftar</p>
            </div>
        </div>
        
        <!-- Dropdown menu aksi -->
        <div class="dropdown">
            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-gear me-1"></i> Kelola
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm animate slideIn">
            <li>
                <button class="dropdown-item d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#tambahSiswaModal">
                <i class="bi bi-person-plus"></i> 
                <span class="menu-text">Tambah Siswa</span>
                </button>
            </li>
            <li>
                <button class="dropdown-item d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#hapusSiswaModal">
                <i class="bi bi-person-x text-danger"></i>
                <span class="menu-text">Keluarkan Siswa</span>
                </button>
            </li>
            </ul>

            <style>
            .animate {
            animation-duration: 0.12s;
            animation-timing-function: ease-out;
            animation-fill-mode: both;
            transform-origin: top center;
            }

            .slideIn {
            animation-name: windowsDropdown;
            }

            @keyframes windowsDropdown {
            0% {
                transform: scaleY(0);
                opacity: 0;
            }
            100% {
                transform: scaleY(1);
                opacity: 1;
            }
            }

            .dropdown-menu {
            padding: 4px 0;
            border-radius: 4px;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }

            .dropdown-item {
            padding: 8px 16px;
            transition: background 0.1s;
            }

            .dropdown-item:hover {
            background-color: #f0f0f0;
            }

            .dropdown-item:active {
            background-color: #e5e5e5;
            }

            .menu-text {
            margin-left: 8px;
            }
            </style>
        </div>
    </div>

    <?php if(mysqli_num_rows($result_siswa) > 0): ?>
        <!-- Grid siswa -->
        <div class="student-grid mb-3">
            <?php 
            $query_siswa_terbaru = "SELECT s.nama, s.foto_profil, s.tingkat 
                                   FROM siswa s 
                                   JOIN kelas_siswa ks ON s.id = ks.siswa_id 
                                   WHERE ks.kelas_id = '$kelas_id'
                                   ORDER BY ks.created_at DESC LIMIT 6";
            $result_siswa_terbaru = mysqli_query($koneksi, $query_siswa_terbaru);
            
            while($siswa = mysqli_fetch_assoc($result_siswa_terbaru)): 
            ?>
                <div class="student-card p-2 rounded-3 d-flex align-items-center gap-2">
                    <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp.png'; ?>" 
                         alt="Profile" class="rounded-circle" width="36" height="36"
                         style="object-fit: cover;">
                    <div class="student-info">
                        <div class="student-name"><?php echo htmlspecialchars($siswa['nama']); ?></div>
                        <small class="text-muted">Kelas <?php echo htmlspecialchars($siswa['tingkat']); ?></small>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Tombol lihat semua -->
        <?php if($jumlah_siswa > 6): ?>
            <button class="btn btn-light w-100 text-center" 
                    data-bs-toggle="modal" 
                    data-bs-target="#lihatSemuaSiswaModal">
                Lihat Semua Siswa <i class="bi bi-arrow-right ms-1"></i>
            </button>
        <?php endif; ?>

    <?php else: ?>
        <!-- State kosong -->
        <div class="text-center py-4">
            <div class="empty-state mb-3">
                <i class="bi bi-people fs-1 text-muted"></i>
            </div>
            <h6 class="fw-bold mb-2">Belum Ada Siswa</h6>
            <p class="text-muted mb-3" style="font-size: 14px;">
                Mulai tambahkan siswa ke dalam kelas ini
            </p>
            <button class="btn btnPrimary text-white d-inline-flex align-items-center gap-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#tambahSiswaModal">
                <i class="bi bi-person-plus"></i>
                Tambah Siswa
            </button>
        </div>
    <?php endif; ?>
</div>

<style>
.student-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 8px;
}

.student-card {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.student-card:hover {
    background-color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.student-info {
    overflow: hidden;
}

.student-name {
    font-size: 14px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.empty-state {
    opacity: 0.5;
}

@media (max-width: 768px) {
    .student-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
}
</style>
<!-- Floating Action Button -->
<div class="floating-action-button d-block d-md-none">
    <!-- Main FAB -->
    <button class="btn btn-lg main-fab rounded-circle shadow" id="mainFab">
        <i class="bi bi-gear"></i>
    </button>
    
    <!-- Mini FABs -->
    <div class="mini-fabs">
        <!-- Catatan Button -->
        <button class="btn mini-fab rounded-circle shadow" 
                data-bs-toggle="modal" 
                data-bs-target="#semuaCatatanModal"
                title="Catatan">
            <i class="bi bi-journal-text"></i>
            <span class="fab-label">Catatan</span>
        </button>
        
        <!-- Siswa Button -->
        <button class="btn mini-fab rounded-circle shadow"
                data-bs-toggle="modal" 
                data-bs-target="#kelolaSiswaModal" 
                title="Kelola Siswa">
            <i class="bi bi-people"></i>
            <span class="fab-label">Kelola Siswa</span>
        </button>
    </div>

    <!-- Backdrop for FAB -->
    <div class="fab-backdrop"></div>
</div>

<style>
.floating-action-button {
    position: fixed;
    bottom: 6rem;
    right: 2rem;
    z-index: 1000;
}

.main-fab {
    width: 56px;
    height: 56px;
    background-color: rgb(218, 119, 86);
    color: white;
    transition: transform 0.3s;
    position: relative;
    z-index: 1002;
}

.main-fab:hover {
    background-color: rgb(219, 106, 68);
    color: white;
}

.main-fab.active {
    transform: rotate(45deg);
}

.mini-fabs {
    position: absolute;
    bottom: 70px;
    right: 8px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s;
    pointer-events: none;
    z-index: 1002;
}

.mini-fabs.show {
    opacity: 1;
    transform: translateY(0);
    pointer-events: all;
}

.mini-fab {
    width: 40px;
    height: 40px;
    background-color: white;
    color: rgb(218, 119, 86);
    border: 1px solid rgb(218, 119, 86);
    position: relative;
}

.mini-fab:hover {
    background-color: rgb(218, 119, 86);
    color: white;
}

/* Label style */
.fab-label {
    position: absolute;
    right: 50px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.2s;
    pointer-events: none;
    z-index: 1003;
}

.mini-fabs.show .mini-fab:hover .fab-label {
    opacity: 1;
    visibility: visible;
}

/* Backdrop style */
.fab-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 1001;
}

.fab-backdrop.show {
    opacity: 1;
    visibility: visible;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainFab = document.getElementById('mainFab');
    const miniFabs = document.querySelector('.mini-fabs');
    const backdrop = document.querySelector('.fab-backdrop');
    
    mainFab.addEventListener('click', function() {
        this.classList.toggle('active');
        miniFabs.classList.toggle('show');
        backdrop.classList.toggle('show');
    });
    
    // Close FAB menu when clicking backdrop
    backdrop.addEventListener('click', function() {
        mainFab.classList.remove('active');
        miniFabs.classList.remove('show');
        backdrop.classList.remove('show');
    });
    
    // Close FAB menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideFAB = event.target.closest('.floating-action-button');
        if (!isClickInsideFAB && miniFabs.classList.contains('show')) {
            mainFab.classList.remove('active');
            miniFabs.classList.remove('show');
            backdrop.classList.remove('show');
        }
    });
});
</script>

<!-- Modal Seluruh Catatan -->
<div class="modal fade" id="semuaCatatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Catatan Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <?php
                $query_all_catatan = "SELECT * FROM catatan_guru WHERE kelas_id = '$kelas_id' ORDER BY created_at DESC";
                $result_all_catatan = mysqli_query($koneksi, $query_all_catatan);
                
                if(mysqli_num_rows($result_all_catatan) > 0):
                ?>
                    <div class="catatan-list">
                        <?php while($catatan = mysqli_fetch_assoc($result_all_catatan)): ?>
                            <div class="catatan-item p-3 rounded-3 mb-3 bg-light border">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-2 fw-bold"><?php echo htmlspecialchars($catatan['judul']); ?></h6>
                                        <div class="d-flex align-items-center text-muted mb-2" style="font-size: 0.85rem;">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            <?php echo date('d F Y', strtotime($catatan['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item text-danger d-flex align-items-center gap-2" 
                                                   href="#" 
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus catatan ini?') && 
                                                           (window.location.href='hapus_catatan.php?id=<?php echo $catatan['id']; ?>&kelas_id=<?php echo $kelas_id; ?>')">
                                                    <i class="bi bi-trash"></i>
                                                    Hapus Catatan
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <p class="mb-3 text-secondary" style="font-size: 0.95rem;">
                                    <?php echo nl2br(htmlspecialchars($catatan['konten'])); ?>
                                </p>
                                
                                <?php if($catatan['file_lampiran']): ?>
                                    <a href="<?php echo htmlspecialchars($catatan['file_lampiran']); ?>" 
                                       class="text-decoration-none d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-white border"
                                       target="_blank">
                                        <?php
                                        $ext = pathinfo($catatan['file_lampiran'], PATHINFO_EXTENSION);
                                        $icon = match($ext) {
                                            'pdf' => 'bi-file-pdf-fill text-danger',
                                            'doc','docx' => 'bi-file-word-fill text-primary',
                                            'jpg','jpeg','png' => 'bi-file-image-fill text-success',
                                            default => 'bi-file-earmark-fill text-secondary'
                                        };
                                        ?>
                                        <i class="bi <?php echo $icon; ?>"></i>
                                        <span>Lihat Lampiran</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                        <h6 class="fw-bold mb-2">Belum Ada Catatan</h6>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">
                            Mulai tambahkan catatan untuk kelas ini
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" 
                        class="btn btnPrimary text-white w-100"
                        data-bs-toggle="modal" 
                        data-bs-target="#catatanModal">
                    <i class="bi bi-plus-lg me-2"></i>
                    Tambah Catatan Baru
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Update event listener for FAB catatan button
document.querySelector('.mini-fab[title="Catatan"]').addEventListener('click', function(e) {
    e.preventDefault();
    const semuaCatatanModal = new bootstrap.Modal(document.getElementById('semuaCatatanModal'));
    semuaCatatanModal.show();
});

// Add event listener for modal close
document.getElementById('semuaCatatanModal').addEventListener('hidden.bs.modal', function () {
    // Remove backdrop manually
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
    // Remove modal-open class from body
    document.body.classList.remove('modal-open');
    // Remove inline styles from body
    document.body.style.removeProperty('padding-right');
    document.body.style.removeProperty('overflow');
});
</script>
<!-- Modal Kelola Siswa -->
<div class="modal fade" id="kelolaSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Kelola Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-light border d-flex align-items-center gap-2 p-3" 
                            data-bs-toggle="modal" 
                            data-bs-target="#lihatSemuaSiswaModal">
                        <i class="bi bi-people-fill fs-4"></i>
                        <div class="text-start">
                            <h6 class="mb-0">Lihat Semua Siswa</h6>
                            <small class="text-muted">Lihat daftar lengkap siswa dalam kelas</small>
                        </div>
                    </button>
                    
                    <button class="btn btn-light border d-flex align-items-center gap-2 p-3" 
                            data-bs-toggle="modal" 
                            data-bs-target="#tambahSiswaModal">
                        <i class="bi bi-person-plus-fill fs-4"></i>
                        <div class="text-start">
                            <h6 class="mb-0">Tambah Siswa</h6>
                            <small class="text-muted">Tambahkan siswa baru ke kelas</small>
                        </div>
                    </button>
                    
                    <button class="btn btn-light border d-flex align-items-center gap-2 p-3" 
                            data-bs-toggle="modal" 
                            data-bs-target="#hapusSiswaModal">
                        <i class="bi bi-person-x-fill fs-4 text-danger"></i>
                        <div class="text-start">
                            <h6 class="mb-0">Keluarkan Siswa</h6>
                            <small class="text-muted">Keluarkan siswa dari kelas ini</small>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Tambah Siswa -->
<div class="modal fade" id="tambahSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tambah Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formTambahSiswa">
                    <!-- Input Group Kelas -->
                    <div class="mb-3">
                        <label class="form-label">Pilih Kelas</label>
                        <select class="form-select" id="tingkatKelas" required>
                        <option value="">Pilih salah satu</option>
                                    <option value="7">SMP Kelas 7</option>
                                    <option value="8">SMP Kelas 8</option>
                                    <option value="9">SMP Kelas 9</option>
                                    <option value="E">SMA Fase E</option>
                                    <option value="F">SMA Fase F</option>
                                    <option value="12">SMA Kelas 12</option>
                        </select>
                    </div>

                    <!-- Daftar Siswa dengan Checkbox -->
                    <div class="mb-3 p-3 rounded-2" style="background-color: rgb(238, 238, 238);">
                        <label class="form-label">Daftar Siswa</label>
                        <div class="daftar-siswa" style="max-height: 300px; overflow-y: auto;">
                            <!-- Daftar siswa akan dimuat di sini -->
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn color-web text-white">Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- script tambah siswa -->
<script>

    console.log('ngapain kalian sampe di sini? balik belajar sana');
// Ketika tingkat kelas berubah
document.getElementById('tingkatKelas').addEventListener('change', function() {
    const tingkat = this.value;
    const daftarSiswaDiv = document.querySelector('.daftar-siswa');
    
    if(tingkat) {
        // Fetch data siswa berdasarkan tingkat
        fetch(`get_siswa.php?tingkat=${tingkat}`)
            .then(response => response.text()) // Ubah ke text() karena response dari PHP adalah HTML
            .then(html => {
                // Tambahkan checkbox "Pilih Semua" di atas daftar siswa
                const pilihSemuaHTML = `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="pilihSemua">
                        <label class="form-check-label" for="pilihSemua">
                            Pilih Semua
                        </label>
                    </div>
                    <hr>
                `;
                
                daftarSiswaDiv.innerHTML = pilihSemuaHTML + html;
                
                // Event listener untuk "Pilih Semua" checkbox
                document.getElementById('pilihSemua').addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.siswa-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                daftarSiswaDiv.innerHTML = '<p class="text-danger">Terjadi kesalahan saat memuat data siswa</p>';
            });
    } else {
        daftarSiswaDiv.innerHTML = '<p class="text-muted">Pilih tingkat kelas terlebih dahulu</p>';
    }
});


document.getElementById('formTambahSiswa').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedSiswa = Array.from(document.querySelectorAll('.siswa-checkbox:checked'))
                              .map(checkbox => checkbox.value);
    
    if(selectedSiswa.length === 0) {
        alert('Pilih minimal satu siswa');
        return;
    }

    // Ambil kelas_id dari parameter URL atau dari variabel PHP
    const kelas_id = <?php echo $kelas_id; ?>;
    
    // Kirim data ke proses_tambah_siswa.php
    fetch('proses_tambah_siswa.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `siswa_ids=${JSON.stringify(selectedSiswa)}&kelas_id=${kelas_id}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Siswa berhasil ditambahkan ke kelas');
            // Tutup modal
            document.querySelector('#tambahSiswaModal .btn-close').click();
            // Refresh halaman
            location.reload();
        } else {
            alert('Gagal menambahkan siswa: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan siswa');
    });
});
</script>

<!-- Modal Hapus Siswa -->
<div class="modal fade" id="hapusSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tendang Siswa dari Kelas</h5>
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