<?php
session_start();
require "koneksi.php";

// Cek apakah user sudah login dan merupakan siswa
if (!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

// Ambil ID kelas dari parameter URL
if (!isset($_GET['id'])) {
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

if (mysqli_num_rows($result_akses) == 0) {
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
// Modify the query_postingan part in kelas.php to include student posts
$query_postingan = "SELECT 
    p.*,
    COALESCE(g.namaLengkap, s.nama) as nama_poster,
    COALESCE(g.foto_profil, s.foto_profil) as foto_poster,
    g.jabatan as jabatan_guru,
    t.id as tugas_id,
    t.judul as judul_tugas,
    t.batas_waktu,
    t.status as tugas_status,
    p.user_type
FROM postingan_kelas p
LEFT JOIN guru g ON p.user_id = g.username AND p.user_type = 'guru'
LEFT JOIN siswa s ON p.user_id = s.username AND p.user_type = 'siswa'
LEFT JOIN tugas t ON p.id = t.postingan_id
WHERE p.kelas_id = '$kelas_id'
ORDER BY p.created_at DESC";
$result_postingan = mysqli_query($koneksi, $query_postingan);

// Ambil jumlah siswa di kelas ini
$query_jumlah_siswa = "SELECT COUNT(*) as total FROM kelas_siswa WHERE kelas_id = '$kelas_id'";
$result_jumlah = mysqli_query($koneksi, $query_jumlah_siswa);
$jumlah_siswa = mysqli_fetch_assoc($result_jumlah)['total'];

// Fungsi untuk mengecek apakah user sudah like postingan
function sudahLike($postingan_id, $user_id, $koneksi)
{
    $query = "SELECT * FROM likes_postingan 
              WHERE postingan_id = '$postingan_id' 
              AND user_id = '$user_id'";
    $result = mysqli_query($koneksi, $query);
    return mysqli_num_rows($result) > 0;
}

// Fungsi untuk menghitung jumlah like pada postingan
function hitungLike($postingan_id, $koneksi)
{
    $query = "SELECT COUNT(*) as total FROM likes_postingan 
              WHERE postingan_id = '$postingan_id'";
    $result = mysqli_query($koneksi, $query);
    return mysqli_fetch_assoc($result)['total'];
}

// Fungsi untuk menghitung jumlah komentar pada postingan
function hitungKomentar($postingan_id, $koneksi)
{
    $query = "SELECT COUNT(*) as total FROM komentar_postingan 
              WHERE postingan_id = '$postingan_id'";
    $result = mysqli_query($koneksi, $query);
    return mysqli_fetch_assoc($result)['total'];
}

// Ambil komentar untuk setiap postingan
function ambilKomentar($postingan_id, $koneksi)
{
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

function formatFileSize($bytes)
{
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <!-- Cropper.js JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <title>Kelas - SMAGAEdu</title>
</head>
<?php include 'includes/styles.php'; ?>
<style>
    body {
        font-family: Merriweather;
    }

    .color-web {
        background-color: rgb(218, 119, 86);
    }

    .btnPrimary {
        background-color: rgb(218, 119, 86);
        border: 0;
    }

    .btnPrimary:hover {
        background-color: rgb(219, 106, 68);

    }

    .text-primary {
        color: rgb(218, 119, 86);
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




            <!-- konten inti -->
            <div class="col col-inti p-0 p-md-3">
                <style>
                    .col-inti {
                        margin-left: 0;
                        padding-right: 0 !important;
                        /* Remove right padding */
                        max-width: 100%;
                        /* Ensure content doesn't overflow */
                        animation: fadeInUp 0.5s;
                        opacity: 1;

                    }

                    @keyframes fadeInUp {
                        from {
                            opacity: 0;
                            transform: translateY(20px);
                        }

                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
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
                    <div style="background-image: url(<?php echo !empty($kelas['background_image']) ? $kelas['background_image'] : 'assets/bg.jpg'; ?>); 
                            height: 200px; 
                            padding-top: 120px; 
                            margin-top: 15px; 
                            background-position: center;
                            background-size: cover;"
                        class="rounded text-white shadow latar-belakang">
                    </div>

                    <!-- Overlay dengan tombol (akan muncul saat hover) -->
                    <div class="background-overlay rounded d-flex align-items-center justify-content-center">
                        <!-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalEditBackground">
                            <i class="fas fa-camera me-2"></i>Ganti Background
                        </button> -->
                    </div>

                    <!-- Konten (teks) dengan z-index lebih tinggi -->
                    <div class="position-absolute bottom-0 start-0 p-3" style="z-index: 2;">
                        <div>
                            <h5 class="display-5 p-0 m-0 text-white"
                                style="font-weight: bold; font-size: 28px; font-size: clamp(24px, 5vw, 35px);">
                                <?php
                                if (isset($kelas['is_public']) && $kelas['is_public']) {
                                    echo htmlspecialchars($kelas['nama_kelas']);
                                } else {
                                    echo htmlspecialchars($kelas['mata_pelajaran']);
                                }
                                ?>
                            </h5>
                            <h4 class="p-0 m-0 pb-3 text-white" style="font-size: clamp(16px, 4vw, 24px);">
                                Kelas <?php echo htmlspecialchars($kelas['tingkat']); ?>
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

                    @media screen and (min-width: 768px) {
                        .background-container {
                            margin-right: 1.5rem !important;
                        }
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

                <div class="row mt-4 p-0 m-0 p-2">
                    <div class="col-12 col-lg-8 p-0">

                        <!-- postingan, hanya untuk kelas publik -->
                        <!-- Student Posting Form (ONLY for public classes) -->
                        <!-- Student Post Creator Card (ONLY for public classes) -->
                        <?php if (isset($kelas['is_public']) && $kelas['is_public']): ?>
                            <div class="create-post-card bg-white rounded-3 p-3 mb-4 border">
                                <!-- Desktop View -->
                                <div class="d-none d-md-flex align-items-center gap-3">
                                    <img src="<?php
                                                if (!empty($siswa['photo_url'])) {
                                                    // If using avatar from DiceBear
                                                    if ($siswa['photo_type'] === 'avatar') {
                                                        echo $siswa['photo_url'];
                                                    }
                                                    // If using uploaded photo
                                                    else if ($siswa['photo_type'] === 'upload') {
                                                        echo $siswa['photo_url'];
                                                    }
                                                } else {
                                                    // Default image
                                                    echo 'assets/pp.png';
                                                }
                                                ?>" alt="Profile" class="rounded-circle" width="45" height="45" style="object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <button class="btn w-100 text-start px-4 rounded-pill border bg-light hover-bg"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTambahPostinganSiswa">
                                            <span class="text-muted">Apa yang ingin kamu bagikan dengan kelas?</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Mobile View -->
                                <div class="d-flex d-md-none gap-2">
                                    <img src="<?php
                                                if (!empty($siswa['photo_url'])) {
                                                    if ($siswa['photo_type'] === 'avatar') {
                                                        echo $siswa['photo_url'];
                                                    } else if ($siswa['photo_type'] === 'upload') {
                                                        echo $siswa['photo_url'];
                                                    }
                                                } else {
                                                    echo 'assets/pp.png';
                                                }
                                                ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    <button class="flex-grow-1 btn text-start rounded-pill border bg-light"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalTambahPostinganSiswa">
                                        <span class="text-muted" style="font-size: 0.9rem;">Mulai diskusi...</span>
                                    </button>
                                </div>

                                <!-- Quick Actions -->
                                <div class="d-flex justify-content-around mt-3 pt-2 border-top">
                                    <button class="btn btn-light flex-grow-1 me-2 d-flex align-items-center justify-content-center gap-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalTambahPostinganSiswa">
                                        <i class="bi bi-image text-success"></i>
                                        <span class="d-none d-md-inline">Foto/Video</span>
                                    </button>
                                    <button class="btn btn-light flex-grow-1 me-2 d-flex align-items-center justify-content-center gap-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalTambahPostinganSiswa">
                                        <i class="bi bi-file-earmark-text text-primary"></i>
                                        <span class="d-none d-md-inline">Dokumen</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Tambah Postingan Siswa -->
                            <div class="modal fade" id="modalTambahPostinganSiswa" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title fw-semibold">Buat Postingan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body p-4">
                                            <form action="proses_postingan_siswa.php" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="kelas_id" value="<?php echo $kelas_id; ?>">

                                                <!-- Author Info -->
                                                <div class="d-flex align-items-center mb-3">
                                                    <img src="<?php
                                                                if (!empty($siswa['photo_url'])) {
                                                                    if ($siswa['photo_type'] === 'avatar') {
                                                                        echo $siswa['photo_url'];
                                                                    } else if ($siswa['photo_type'] === 'upload') {
                                                                        echo $siswa['photo_url'];
                                                                    }
                                                                } else {
                                                                    echo 'assets/pp.png';
                                                                }
                                                                ?>" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                                                    <div>
                                                        <div class="fw-medium"><?php echo htmlspecialchars($siswa['nama']); ?></div>
                                                        <small class="text-muted">Siswa</small>
                                                    </div>
                                                </div>

                                                <!-- Content -->
                                                <div class="form-group mb-3">
                                                    <textarea class="form-control border-0 bg-light"
                                                        name="konten" rows="5"
                                                        placeholder="Apa yang ingin kamu bagikan?"
                                                        style="border-radius: 12px; resize: none;"
                                                        required></textarea>
                                                </div>

                                                <!-- File Preview -->
                                                <div id="previewContainer" class="mb-3 d-none">
                                                    <div id="imagePreview" class="d-flex flex-wrap gap-2"></div>
                                                </div>

                                                <!-- File Upload -->
                                                <div class="attachment-box bg-light rounded-3 p-3 mb-3"
                                                    onclick="document.getElementById('file_upload_siswa').click()">
                                                    <input type="file" id="file_upload_siswa" name="lampiran[]"
                                                        class="d-none" multiple
                                                        accept="image/*,.pdf,.doc,.docx"
                                                        onchange="showSelectedFilesSiswa(this)">
                                                    <div class="text-center">
                                                        <i class="bi bi-cloud-upload fs-3 mb-2" style="color: rgb(218, 119, 86);"></i>
                                                        <p class="mb-0 text-muted">Klik untuk menambah lampiran</p>
                                                        <small class="text-muted">atau drag & drop file di sini</small>
                                                    </div>
                                                    <div id="selectedFilesSiswa" class="selected-files mt-2"></div>
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="d-grid">
                                                    <button type="submit" class="btn py-2 rounded-4"
                                                        style="background-color: rgb(218, 119, 86); color: white;">
                                                        Kirim Postingan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function showSelectedFilesSiswa(input) {
                                    const previewContainer = document.getElementById('previewContainer');
                                    const imagePreview = document.getElementById('imagePreview');
                                    const selectedFiles = document.getElementById('selectedFilesSiswa');

                                    if (input.files.length > 0) {
                                        previewContainer.classList.remove('d-none');
                                        imagePreview.innerHTML = ''; // Clear previous previews
                                        selectedFiles.innerHTML = ''; // Clear filename list

                                        for (let i = 0; i < input.files.length; i++) {
                                            const file = input.files[i];

                                            // Create file item display
                                            const fileItem = document.createElement('div');
                                            fileItem.classList.add('file-item', 'd-flex', 'align-items-center', 'bg-white', 'p-2', 'rounded', 'mb-2');

                                            // Icon based on file type
                                            let iconClass = 'bi-file-earmark';
                                            if (file.type.includes('image')) {
                                                iconClass = 'bi-file-image text-success';
                                            } else if (file.name.endsWith('.pdf')) {
                                                iconClass = 'bi-file-pdf text-danger';
                                            } else if (file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                                                iconClass = 'bi-file-word text-primary';
                                            }

                                            // File size formatting
                                            const fileSize = file.size < 1024 * 1024 ?
                                                Math.round(file.size / 1024) + ' KB' :
                                                Math.round(file.size / (1024 * 1024) * 10) / 10 + ' MB';

                                            fileItem.innerHTML = `
                <div class="file-icon me-2">
                    <i class="bi ${iconClass} fs-4"></i>
                </div>
                <div class="file-info flex-grow-1">
                    <div class="file-name text-truncate">${file.name}</div>
                    <small class="text-muted">${fileSize}</small>
                </div>
                <button type="button" class="btn-close btn-sm" 
                    onclick="removeFile(this, ${i})"></button>
            `;

                                            selectedFiles.appendChild(fileItem);

                                            // Create preview for images
                                            if (file.type.match('image.*')) {
                                                const reader = new FileReader();
                                                reader.onload = function(e) {
                                                    const imgContainer = document.createElement('div');
                                                    imgContainer.classList.add('position-relative', 'img-preview');

                                                    const img = document.createElement('img');
                                                    img.src = e.target.result;
                                                    img.style.height = '100px';
                                                    img.style.width = '100px';
                                                    img.style.objectFit = 'cover';
                                                    img.classList.add('rounded');

                                                    imgContainer.appendChild(img);
                                                    imagePreview.appendChild(imgContainer);
                                                };
                                                reader.readAsDataURL(file);
                                            }
                                        }
                                    } else {
                                        previewContainer.classList.add('d-none');
                                        selectedFiles.innerHTML = '';
                                    }
                                }

                                function removeFile(button, index) {
                                    const input = document.getElementById('file_upload_siswa');
                                    const dt = new DataTransfer();

                                    for (let i = 0; i < input.files.length; i++) {
                                        if (i !== index) {
                                            dt.items.add(input.files[i]);
                                        }
                                    }

                                    input.files = dt.files;
                                    button.closest('.file-item').remove();

                                    // Re-render the file preview
                                    showSelectedFilesSiswa(input);
                                }
                            </script>

                            <style>
                                .attachment-box {
                                    border: 2px dashed #ddd;
                                    border-radius: 12px;
                                    cursor: pointer;
                                    transition: all 0.2s ease;
                                }

                                .attachment-box:hover {
                                    background-color: #f8f9fa;
                                    border-color: rgb(218, 119, 86);
                                }

                                .file-item {
                                    border: 1px solid #eee;
                                    transition: all 0.2s ease;
                                }

                                .file-item:hover {
                                    background-color: #f8f9fa !important;
                                }

                                .file-name {
                                    max-width: 200px;
                                    font-size: 14px;
                                }

                                .img-preview {
                                    display: inline-block;
                                    margin-right: 8px;
                                    margin-bottom: 8px;
                                }

                                @media (max-width: 768px) {
                                    .file-name {
                                        max-width: 150px;
                                    }
                                }
                            </style>
                        <?php endif; ?>




                        <!-- Konten Utama -->
                        <!-- postingan guru -->
                        <?php
                        if (mysqli_num_rows($result_postingan) > 0) {
                            while ($post = mysqli_fetch_assoc($result_postingan)) {
                                // Format tanggal
                                $timestamp = strtotime($post['created_at']);
                                $today = strtotime('today');
                                $yesterday = strtotime('yesterday');
                                $time = date("h:i A", $timestamp);

                                if ($timestamp >= $today) {
                                    $tanggal = "Hari ini, " . $time;
                                } elseif ($timestamp >= $yesterday) {
                                    $tanggal = "Kemarin, " . $time;
                                } else {
                                    $tanggal = date("d F", $timestamp) . ", " . $time;
                                }
                        ?>
                                <div class="mt- p-md-3 mb-4 rounded-3 bg-white mx-md-0 postingan p-4"
                                    style="border: 1px solid rgb(226, 226, 226);">
                                    <div class="d-flex gap-3">
                                        <div>
                                            <img src="<?php
                                                        if ($post['user_type'] == 'guru') {
                                                            // For teachers (guru), use foto_poster from uploads/profil directory
                                                            echo !empty($post['foto_poster']) ? 'uploads/profil/' . $post['foto_poster'] : 'assets/pp.png';
                                                        } else {
                                                            // For students (siswa), we need to query for the student's photo info
                                                            $student_username = $post['user_id'];
                                                            $query_student = "SELECT photo_url, photo_type FROM siswa WHERE username = '$student_username'";
                                                            $result_student = mysqli_query($koneksi, $query_student);
                                                            $student_data = mysqli_fetch_assoc($result_student);

                                                            if (!empty($student_data) && !empty($student_data['photo_url'])) {
                                                                // Use photo_url directly since it already contains the full path
                                                                echo $student_data['photo_url'];
                                                            } else {
                                                                // Default image
                                                                echo 'assets/pp.png';
                                                            }
                                                        }
                                                        ?>" alt="Profile Image"
                                                class="profile-img rounded-circle border-0 bg-white"
                                                style="width: 40px; height: 40px; object-fit: cover;">

                                        </div>

                                        <div class="">
                                            <h6 class="p-0 m-0 fw-bold">
                                                <?php echo $post['nama_poster']; ?>
                                                <?php if ($post['user_type'] == 'guru'): ?>
                                                    <span class="badge ms-1" style="font-size: 10px; background-color:rgb(218, 119, 86); color:white;">Guru</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="p-0 m-0 text-muted" style="font-size: 12px;"><?php echo $tanggal; ?></p>
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
                                            border: 1px solid rgba(0, 0, 0, 0.08);
                                            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
                                        <?php if ($post['jenis_postingan'] == 'tugas'): ?>
                                            <!-- UI for Tugas with minimalist iOS style -->
                                            <div class="tugas-container mt-3">
                                                <!-- Badge dan Judul -->
                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <span class="badge" style="background: rgb(218, 119, 86); padding: 6px 12px; border-radius: 20px; font-weight: 500;">TUGAS</span>
                                                    <h5 class="mb-0" style="font-weight: 600;"><?php echo htmlspecialchars($post['judul_tugas']); ?></h5>
                                                </div>

                                                <!-- Box Info Tugas -->
                                                <div class="tugas-info-box p-1 rounded-4 mb-3">
                                                    <!-- Batas Waktu -->
                                                    <div class="tugas-deadline d-flex align-items-center mb-3">
                                                        <div class="deadline-icon me-3 p-2 rounded-5" style="background: rgba(218, 119, 86, 0.1);">
                                                            <i class="bi bi-clock" style="color: rgb(218, 119, 86); font-size: 1.2rem;"></i>
                                                        </div>
                                                        <div>
                                                            <div class="text-muted" style="font-size: 13px;">Batas Pengumpulan</div>
                                                            <div style="font-weight: 500; font-size: 15px;">
                                                                <?php echo date("d M Y, H:i", strtotime($post['batas_waktu'])); ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Status Pengumpulan -->
                                                    <?php
                                                    // Di bagian status pengumpulan
                                                    $now = new DateTime();
                                                    $deadline = new DateTime($post['batas_waktu']);

                                                    // Cek apakah tugas ditutup & telat
                                                    $is_late = $now > $deadline;
                                                    $is_closed = $is_late || ($post['tugas_status'] === 'closed');

                                                    // Cek status pengumpulan
                                                    $query_pengumpulan = "SELECT pt.*, t.poin_maksimal 
                                                    FROM pengumpulan_tugas pt
                                                    JOIN tugas t ON pt.tugas_id = t.id
                                                    WHERE pt.tugas_id = '{$post['tugas_id']}' 
                                                    AND pt.siswa_id = '$userid'";
                                                    $result_pengumpulan = mysqli_query($koneksi, $query_pengumpulan);
                                                    $sudah_mengumpulkan = mysqli_num_rows($result_pengumpulan) > 0;
                                                    $data_pengumpulan = $sudah_mengumpulkan ? mysqli_fetch_assoc($result_pengumpulan) : null;
                                                    ?>

                                                    <div class="tugas-progress d-flex align-items-center mb-3">
                                                        <div class="progress-icon me-3 p-2 rounded-5" style="background: rgba(218, 119, 86, 0.1);">
                                                            <i class="bi bi-clipboard-check" style="color: rgb(218, 119, 86); font-size: 1.2rem;"></i>
                                                        </div>
                                                        <div>
                                                            <div class="text-muted" style="font-size: 13px;">Status</div>
                                                            <div style="font-weight: 500; font-size: 15px;">
                                                                <?php if ($sudah_mengumpulkan): ?>
                                                                    <span class="text-success">Sudah Dikumpulkan</span>
                                                                <?php else: ?>
                                                                    <span class="text-<?php echo $is_late ? 'danger' : 'warning'; ?>">
                                                                        <?php echo $is_late ? 'Terlambat' : 'Belum Dikumpulkan'; ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Deskripsi -->
                                                    <div class="tugas-deadline d-flex align-items-center mb-3">
                                                        <div class="deadline-icon me-3 p-2 rounded-5" style="background: rgba(218, 119, 86, 0.1);">
                                                            <i class="bi bi-file-text" style="color: rgb(218, 119, 86); font-size: 1.2rem;"></i>
                                                        </div>
                                                        <div>
                                                            <div class="text-muted" style="font-size: 13px;">Deskripsi Tugas</div>
                                                            <div style="font-weight: 500; font-size: 15px;">
                                                                <?php echo nl2br(htmlspecialchars($post['konten'])); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Lampiran Tugas dari Guru -->
                                                    <?php
                                                    $query_lampiran = "SELECT * FROM lampiran_tugas WHERE tugas_id = '{$post['tugas_id']}'";
                                                    $result_lampiran = mysqli_query($koneksi, $query_lampiran);

                                                    if (mysqli_num_rows($result_lampiran) > 0):
                                                    ?>
                                                        <div class="tugas-deadline d-flex align-items-center mb-3">
                                                            <div class="deadline-icon me-3 p-2 rounded-5" style="background: rgba(218, 119, 86, 0.1);">
                                                                <i class="bi bi-file-earmark-text" style="color: rgb(218, 119, 86); font-size: 1.2rem;"></i>
                                                            </div>
                                                            <div class="w-100">
                                                                <div class="text-muted mb-2" style="font-size: 13px;">Lampiran dari Guru</div>
                                                                <div class="lampiran-list">
                                                                    <?php while ($lampiran = mysqli_fetch_assoc($result_lampiran)):
                                                                        $ext = pathinfo($lampiran['nama_file'], PATHINFO_EXTENSION);
                                                                        $icon = match (strtolower($ext)) {
                                                                            'pdf' => 'bi-file-pdf-fill text-danger',
                                                                            'doc', 'docx' => 'bi-file-word-fill text-primary',
                                                                            'xls', 'xlsx' => 'bi-file-excel-fill text-success',
                                                                            'ppt', 'pptx' => 'bi-file-ppt-fill text-warning',
                                                                            'jpg', 'jpeg', 'png', 'gif' => 'bi-file-image-fill text-info',
                                                                            default => 'bi-file-earmark-fill text-secondary'
                                                                        };
                                                                    ?>
                                                                        <div class="ios-attachment mb-2">
                                                                            <a href="<?php echo $lampiran['path_file']; ?>"
                                                                                class="ios-attachment-item d-flex align-items-center text-decoration-none"
                                                                                download>
                                                                                <div class="ios-attachment-icon flex-shrink-0">
                                                                                    <i class="bi <?php echo $icon; ?>"></i>
                                                                                </div>
                                                                                <div class="ios-attachment-info flex-grow-1 min-w-0">
                                                                                    <div class="ios-attachment-name text-truncate" title="<?php echo htmlspecialchars($lampiran['nama_file']); ?>">
                                                                                        <?php
                                                                                        $ext = strtolower(pathinfo($lampiran['nama_file'], PATHINFO_EXTENSION));
                                                                                        switch ($ext) {
                                                                                            case 'pdf':
                                                                                                echo 'File PDF';
                                                                                                break;
                                                                                            case 'doc':
                                                                                            case 'docx':
                                                                                                echo 'Dokumen Word';
                                                                                                break;
                                                                                            case 'xls':
                                                                                            case 'xlsx':
                                                                                                echo 'File Excel';
                                                                                                break;
                                                                                            case 'ppt':
                                                                                            case 'pptx':
                                                                                                echo 'File Powerpoint';
                                                                                                break;
                                                                                            case 'jpg':
                                                                                            case 'jpeg':
                                                                                            case 'png':
                                                                                            case 'gif':
                                                                                                echo 'File Gambar';
                                                                                                break;
                                                                                            default:
                                                                                                echo 'File tidak terdeteksi';
                                                                                        }
                                                                                        ?>
                                                                                    </div>
                                                                                    <div class="ios-attachment-size"><?php echo formatFileSize($lampiran['ukuran_file']); ?></div>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    <?php endwhile; ?>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <style>
                                                            .ios-attachment {
                                                                background: #f0f2f5;
                                                                border-radius: 12px;
                                                                overflow: hidden;
                                                                transition: all 0.2s ease;
                                                            }

                                                            .ios-attachment-item {
                                                                padding: 12px 16px;
                                                                color: inherit;
                                                            }

                                                            .ios-attachment-icon {
                                                                width: 40px;
                                                                height: 40px;
                                                                background: white;
                                                                border-radius: 10px;
                                                                display: flex;
                                                                align-items: center;
                                                                justify-content: center;
                                                                margin-right: 12px;
                                                                font-size: 1.2rem;
                                                            }

                                                            .ios-attachment-info {
                                                                min-width: 0;
                                                                padding-right: 12px;
                                                            }

                                                            .ios-attachment-name {
                                                                font-size: 14px;
                                                                font-weight: 500;
                                                                white-space: nowrap;
                                                                overflow: hidden;
                                                                text-overflow: ellipsis;
                                                                color: #000;
                                                                margin-bottom: 2px;
                                                                max-width: 100%;
                                                            }

                                                            .ios-attachment-size {
                                                                font-size: 12px;
                                                                color: #8e8e93;
                                                            }

                                                            .ios-attachment-download {
                                                                color: #8e8e93;
                                                                font-size: 1.2rem;
                                                            }

                                                            /* Mobile optimization */
                                                            @media (max-width: 576px) {
                                                                .ios-attachment-info {
                                                                    max-width: calc(100% - 100px);
                                                                    /* Account for icon and download button */
                                                                }

                                                                .ios-attachment-name {
                                                                    font-size: 13px;
                                                                    max-width: 100%;
                                                                }

                                                                .ios-attachment-size {
                                                                    font-size: 11px;
                                                                }

                                                                .ios-attachment-icon {
                                                                    width: 32px;
                                                                    height: 32px;
                                                                    font-size: 1rem;
                                                                    margin-right: 8px;
                                                                }

                                                                .ios-attachment-item {
                                                                    padding: 8px 12px;
                                                                }
                                                            }
                                                        </style>

                                                    <?php endif; ?>
                                                    <!-- Tombol Aksi -->
                                                    <?php if (!$sudah_mengumpulkan): ?>
                                                        <button type="button"
                                                            class="btn w-100 d-flex align-items-center justify-content-center gap-2"
                                                            style="background: <?php echo $is_closed ? '#dc3545' : 'rgb(218, 119, 86)'; ?>; 
                   color: white; 
                   border-radius: 12px; 
                   padding: 12px; 
                   font-weight: 500;"
                                                            <?php echo $is_closed ? 'disabled' : ''; ?>
                                                            onclick="<?php echo !$is_closed ? 'kumpulkanTugas(' . $post['tugas_id'] . ')' : ''; ?>">
                                                            <?php if ($is_closed): ?>
                                                                <i class="bi bi-x-circle"></i>
                                                            <?php else: ?>
                                                                <i class="bi bi-upload"></i>
                                                            <?php endif; ?>
                                                            <?php
                                                            if ($is_closed) {
                                                                echo 'Tugas Telah Ditutup';
                                                            } else {
                                                                echo 'Kumpulkan Tugas';
                                                            }
                                                            ?>
                                                        </button>
                                                    <?php else: ?>
                                                        <div class="submitted-info p-4 rounded-4 position-relative"
                                                            style="background: #f8f9fa; border: 1px solid #e9ecef;">

                                                            <!-- Header Section -->
                                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                                <div class="status-icon rounded-circle d-flex align-items-center justify-content-center"
                                                                    style="width: 40px; height: 40px; background: rgba(52, 199, 89, 0.1);">
                                                                    <i class="bi bi-check-circle-fill" style="color: #34c759;"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 fw-semibold">Tugasmu Terkirim</h6>
                                                                    <small class="text-muted">
                                                                        <?php echo date('d M Y, H:i', strtotime($data_pengumpulan['waktu_pengumpulan'])); ?>
                                                                    </small>
                                                                </div>
                                                            </div>

                                                            <!-- File Information -->
                                                            <a href="<?php echo $data_pengumpulan['file_path']; ?>"
                                                                class="text-decoration-none"
                                                                target="_blank">
                                                                <div class="file-card p-3 rounded-4 d-flex align-items-center gap-3"
                                                                    style="background: white; border: 1px solid #e9ecef; transition: all 0.2s ease;">
                                                                    <div class="file-icon rounded-3 d-flex align-items-center justify-content-center"
                                                                        style="width: 48px; height: 48px; background: rgba(0,0,0,0.05);">
                                                                        <?php
                                                                        $ext = pathinfo($data_pengumpulan['nama_file'], PATHINFO_EXTENSION);
                                                                        $icon = match (strtolower($ext)) {
                                                                            'pdf' => 'bi-file-pdf-fill text-danger',
                                                                            'doc', 'docx' => 'bi-file-word-fill text-primary',
                                                                            'xls', 'xlsx' => 'bi-file-excel-fill text-success',
                                                                            'ppt', 'pptx' => 'bi-file-ppt-fill text-warning',
                                                                            'jpg', 'jpeg', 'png', 'gif' => 'bi-file-image-fill text-info',
                                                                            default => 'bi-file-earmark-fill text-secondary'
                                                                        };
                                                                        ?>
                                                                        <i class="bi <?php echo $icon; ?> fs-4"></i>
                                                                    </div>
                                                                    <div class="file-info flex-grow-1">
                                                                        <div class="text-truncate fw-medium" style="color: #1c1c1e;">
                                                                            <?php
                                                                            $ext = pathinfo($data_pengumpulan['nama_file'], PATHINFO_EXTENSION);
                                                                            switch (strtolower($ext)) {
                                                                                case 'pdf':
                                                                                    echo 'PDF File';
                                                                                    break;
                                                                                case 'doc':
                                                                                case 'docx':
                                                                                    echo 'Word Document';
                                                                                    break;
                                                                                case 'jpg':
                                                                                case 'jpeg':
                                                                                case 'png':
                                                                                case 'gif':
                                                                                    echo 'Image File';
                                                                                    break;
                                                                                case 'xls':
                                                                                case 'xlsx':
                                                                                    echo 'Excel File';
                                                                                    break;
                                                                                case 'ppt':
                                                                                case 'pptx':
                                                                                    echo 'PowerPoint File';
                                                                                    break;
                                                                                default:
                                                                                    echo 'File';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                        <small class="text-muted">
                                                                            Klik untuk membuka file
                                                                        </small>
                                                                    </div>
                                                                    <i class="bi bi-chevron-right text-muted"></i>
                                                                </div>
                                                            </a>

                                                            <style>
                                                                .file-card:hover {
                                                                    transform: translateY(-1px);
                                                                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                                                                }

                                                                .file-card:active {
                                                                    transform: translateY(0);
                                                                    opacity: 0.8;
                                                                }

                                                                @media (max-width: 768px) {
                                                                    .submitted-info {
                                                                        padding: 1rem !important;
                                                                    }
                                                                }
                                                            </style>
                                                        </div>
                                                        <?php if ($data_pengumpulan['nilai'] !== null || $data_pengumpulan['komentar_guru'] !== null): ?>
                                                            <div class="assessment-info mt-3 rounded-4" style="background: #f8f9fa; border: 1px solid #e9ecef;">

                                                                <!-- Header Section -->
                                                                <div class="d-flex align-items-center gap-3 mb-3">
                                                                    <div class="status-icon rounded-circle d-flex align-items-center justify-content-center"
                                                                        style="width: 40px; height: 40px; background: rgba(52, 199, 89, 0.1);">
                                                                        <i class="bi bi-check-circle-fill" style="color: #34c759;"></i>
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-0 fw-semibold">Tugasmu Sudah Dinilai</h6>
                                                                        <small class="p-0 m-0 text-muted"><?php echo date('d M Y, H:i', strtotime($data_pengumpulan['tanggal_penilaian'])); ?></small>
                                                                    </div>
                                                                </div>

                                                                <!-- Nilai guru -->
                                                                <?php if ($data_pengumpulan['nilai'] !== null): ?>
                                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                                        <div class="assessment-icon p-2 rounded-circle"
                                                                            style="background: rgba(218, 119, 86, 0.1);">
                                                                            <i class="bi bi-star-fill"
                                                                                style="color: rgb(218, 119, 86); font-size: 1rem;"></i>
                                                                        </div>
                                                                        <div>
                                                                            <small class="text-muted d-block" style="font-size: 12px;">Nilai</small>
                                                                            <span class="fw-medium" style="font-size: 14px;">
                                                                                <?php echo $data_pengumpulan['nilai']; ?>/<?php echo $data_pengumpulan['poin_maksimal']; ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <!-- Komentar Guru -->
                                                                <?php if ($data_pengumpulan['komentar_guru'] !== null): ?>
                                                                    <div class="d-flex align-items-start gap-2">
                                                                        <div class="assessment-icon p-2 rounded-circle"
                                                                            style="background: rgba(218, 119, 86, 0.1);">
                                                                            <i class="bi bi-chat-left-text"
                                                                                style="color: rgb(218, 119, 86); font-size: 1rem;"></i>
                                                                        </div>
                                                                        <div>
                                                                            <small class="text-muted d-block" style="font-size: 12px;">Komentar Guru</small>
                                                                            <p class="mb-0" style="font-size: 14px;">
                                                                                <?php echo nl2br(htmlspecialchars($data_pengumpulan['komentar_guru'])); ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>




                                                </div>
                                            </div>

                                            <style>
                                                .btn {
                                                    transition: all 0.2s ease;
                                                }

                                                .btn:hover {
                                                    background: rgba(218, 119, 86, 0.9) !important;
                                                    transform: translateY(-1px);
                                                }

                                                .btn:active {
                                                    transform: translateY(0);
                                                }

                                                @media (max-width: 768px) {
                                                    .tugas-info-box {
                                                        padding: 0px !important;
                                                    }
                                                }

                                                .assessment-info {
                                                    padding: 18px;
                                                }
                                            </style>
                                        <?php else: ?>
                                            <!-- UI Normal untuk Postingan Biasa -->
                                            <div class="mt-3">
                                                <p class="textPostingan"><?php echo nl2br(htmlspecialchars($post['konten'])); ?></p>
                                            </div>
                                        <?php endif; ?>


                                        <!-- Modal Kumpulkan Tugas (iOS Style) -->
                                        <div class="modal fade" id="modalKumpulkanTugas" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content" style="border-radius: 16px; border: none;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <div class="w-100  position-relative">
                                                            <h5 class="modal-title fw-semibold mb-0" style="font-size: 18px;">Kumpulkan Tugas</h5>
                                                            <button type="button" class="btn-close position-absolute top-50 translate-middle-y" style="right: 0;" data-bs-dismiss="modal" aria-label="Close"></button>

                                                        </div>
                                                    </div>

                                                    <form action="kumpulkan_tugas.php" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body px-4">

                                                            <input type="hidden" name="tugas_id" id="tugas_id_input">

                                                            <!-- File Upload Section -->
                                                            <div class="mb-4">
                                                                <label class="form-label fw-medium" style="font-size: 15px;">Upload File Tugas</label>
                                                                <p class="p-0 m-0 mb-2" style="font-size: 12px;">Pastikan jawabanmu yang akan dikirim telah final, seluruh jawaban yang sudah terkirim tidak akan bisa untuk di edit kembali</p>
                                                                <div class="upload-container p-4 rounded-4 d-flex flex-column align-items-center justify-content-center"
                                                                    style="background: #f0f2f5; border: 2px dashed #ccc; min-height: 120px;">
                                                                    <i class="bi bi-cloud-upload fs-1 mb-2" style="color: #666;"></i>
                                                                    <input type="file"
                                                                        class="form-control visually-hidden"
                                                                        name="file_tugas"
                                                                        id="file_tugas"
                                                                        required>
                                                                    <label for="file_tugas" class="btn btn-light border mb-2">Pilih File</label>
                                                                    <div id="selected-file" class="small text-muted text-center">Belum ada file dipilih</div>
                                                                    <div class="form-text text-center mt-2">Format: PDF, DOC, DOCX, JPG, PNG</div>
                                                                </div>
                                                            </div>

                                                            <!-- Notes Section -->
                                                            <!-- Comment Section -->
                                                            <div class="mb-4">
                                                                <label class="form-label fw-medium mb-2" style="font-size: 15px;">
                                                                    <i class="bi bi-chat-left-text me-1"></i>
                                                                    Pesan untuk Guru
                                                                </label>
                                                                <textarea class="form-control border-0"
                                                                    name="pesan_siswa"
                                                                    rows="3"
                                                                    style="background: #f0f2f5; border-radius: 12px; resize: none;"
                                                                    placeholder="Tambahkan pesan atau keterangan untuk guru... (opsional)"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="mb-4 px-4">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="agreeCheck" required>
                                                                <label class="form-check-label" for="agreeCheck">
                                                                    <p style="font-size: 12px;">Saya telah memahami bahwa tugas yang telah di upload tidak bisa di batalkan atau di rubah redaksinya
                                                                    </p>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <!-- Footer -->
                                                        <div class="modal-footer border-0 px-4 py-3 pb-4">
                                                            <button type="submit"
                                                                class="btn w-100 d-flex align-items-center justify-content-center gap-2"
                                                                style="background: rgb(218, 119, 86); color: white; border-radius: 12px; padding: 12px; font-weight: 500;">
                                                                <i class="bi bi-upload"></i>
                                                                Kirim
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <style>
                                            .modal-content {
                                                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                                            }

                                            .upload-container {
                                                transition: all 0.3s ease;
                                            }

                                            .upload-container:hover {
                                                border-color: rgb(218, 119, 86);
                                                background: #f8f9fa;
                                            }

                                            .form-control:focus {
                                                box-shadow: none;
                                                background: #e8eaed !important;
                                            }

                                            /* Custom animation for modal */
                                            .modal.fade .modal-dialog {
                                                transition: transform 0.2s ease-out;
                                                transform: scale(0.95);
                                            }

                                            .modal.show .modal-dialog {
                                                transform: scale(1);
                                            }
                                        </style>

                                        <script>
                                            // Show selected filename
                                            document.getElementById('file_tugas').addEventListener('change', function() {
                                                const fileName = this.files[0]?.name || 'Belum ada file dipilih';
                                                document.getElementById('selected-file').textContent = fileName;
                                            });
                                        </script>

                                        <script>
                                            function kumpulkanTugas(tugasId) {
                                                document.getElementById('tugas_id_input').value = tugasId;
                                                const modal = new bootstrap.Modal(document.getElementById('modalKumpulkanTugas'));
                                                modal.show();
                                            }
                                        </script>

                                        <?php
                                        // Query untuk mengambil lampiran
                                        $postingan_id = $post['id'];
                                        $query_lampiran = "SELECT * FROM lampiran_postingan WHERE postingan_id = '$postingan_id'";
                                        $result_lampiran = mysqli_query($koneksi, $query_lampiran);

                                        if (mysqli_num_rows($result_lampiran) > 0) {
                                            echo '<div class="container mt-3 p-0 mb-4 bg-light rounded">';

                                            // Array untuk memisahkan gambar dan dokumen
                                            $images = [];
                                            $documents = [];

                                            while ($lampiran = mysqli_fetch_assoc($result_lampiran)) {
                                                if (strpos($lampiran['tipe_file'], 'image') !== false) {
                                                    $images[] = $lampiran;
                                                } else {
                                                    $documents[] = $lampiran;
                                                }
                                            }

                                            // Tampilkan gambar jika ada
                                            if (!empty($images)) {
                                                $imageCount = count($images);
                                                echo '<div class="image-container-' . $imageCount . ' mt-2">';

                                                switch ($imageCount) {
                                                    case 1:
                                                        // Single image - full width
                                                        echo '<div class="single-image">';
                                                        echo '<img src="' . $images[0]['path_file'] . '" alt="Lampiran" onclick="showImage(this.src)">';
                                                        echo '</div>';
                                                        break;

                                                    case 2:
                                                        // Two images side by side
                                                        echo '<div class="dual-images">';
                                                        foreach ($images as $image) {
                                                            echo '<img src="' . $image['path_file'] . '" alt="Lampiran" onclick="showImage(this.src)">';
                                                        }
                                                        echo '</div>';
                                                        break;

                                                    case 3:
                                                        // Two images top, one bottom
                                                        echo '<div class="triple-images">';
                                                        echo '<div class="top-images">';
                                                        echo '<img src="' . $images[0]['path_file'] . '" alt="Lampiran" onclick="showImage(this.src)">';
                                                        echo '<img src="' . $images[1]['path_file'] . '" alt="Lampiran" onclick="showImage(this.src)">';
                                                        echo '</div>';
                                                        echo '<div class="bottom-image">';
                                                        echo '<img src="' . $images[2]['path_file'] . '" alt="Lampiran" onclick="showImage(this.src)">';
                                                        echo '</div>';
                                                        echo '</div>';
                                                        break;

                                                    case 4:
                                                        // Two rows of two images
                                                        echo '<div class="quad-images">';
                                                        echo '<div class="image-row">';
                                                        echo '<img src="' . $images[0]['path_file'] . '" alt="Lampiran" onclick="showImage(this.src)">';
                                                        echo '<img src="' . $images[1]['path_file'] . '" alt="Lampiran" onclick="showImage(this.src)">';
                                                        echo '</div>';
                                                        echo '<div class="image-row">';
                                                        echo '<img src="' . $images[2]['path_file'] . '" alt="Lampiran" onclick="showImage(this.src)">';
                                                        echo '<img src="' . $images[3]['path_file'] . '" alt="Lampiran" onclick="showImage(this.src)">';
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
                                            if (!empty($documents)) {
                                                echo '<div class="document-list">';
                                                foreach ($documents as $doc) {
                                                    $extension = pathinfo($doc['nama_file'], PATHINFO_EXTENSION);
                                                    $icon = '';

                                                    // Set icon berdasarkan tipe file
                                                    switch (strtolower($extension)) {
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

                                                    echo '<div class="doc-item mb-2 p-2 bg-white d-flex align-items-center rounded border">';
                                                    echo '<a href="' . $doc['path_file'] . '" class="text-decoration-none text-dark d-flex align-items-center gap-2 flex-grow-1" target="_blank">';
                                                    echo '<i class="bi ' . $icon . ' fs-4"></i>';
                                                    echo '<div>';
                                                    echo '<div class="doc-name">' . htmlspecialchars($doc['nama_file']) . '</div>';
                                                    echo '<small class="text-muted">' . strtoupper($extension) . ' file</small>';
                                                    echo '</div>';
                                                    echo '</a>';
                                                    echo '<a href="' . $doc['path_file'] . '" class="text-decoration-none text-muted" download>';
                                                    echo '<i class="bi bi-download me-2"></i>';
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
                                        // Query untuk mendapatkan jumlah dan detail reaksi
                                        $query_reactions = "SELECT emoji, COUNT(*) as count 
                                FROM emoji_reactions 
                                WHERE postingan_id = '{$post['id']}' 
                                GROUP BY emoji";
                                        $result_reactions = mysqli_query($koneksi, $query_reactions);

                                        $reactions_html = '';
                                        $total_reactions = 0;
                                        while ($reaction = mysqli_fetch_assoc($result_reactions)) {
                                            $reactions_html .= "{$reaction['emoji']} {$reaction['count']} ";
                                            $total_reactions += $reaction['count'];
                                        }

                                        // Cek reaksi user yang sedang login
                                        $check_reaction = "SELECT emoji FROM emoji_reactions 
                                WHERE postingan_id = '{$post['id']}' 
                                AND user_id = '$userid'";
                                        $reaction_result = mysqli_query($koneksi, $check_reaction);
                                        $user_reaction = mysqli_fetch_assoc($reaction_result);
                                        $current_emoji = $user_reaction ? $user_reaction['emoji'] : null;

                                        // Cek reaksi user yang sudah ada //button like
                                        $check_emoji = "SELECT emoji FROM emoji_reactions WHERE postingan_id = '{$post['id']}' AND user_id = '$userid'";
                                        $emoji_result = mysqli_query($koneksi, $check_emoji);
                                        $user_emoji = mysqli_fetch_assoc($emoji_result);
                                        $current_emoji = $user_emoji ? $user_emoji['emoji'] : null;

                                        // Query untuk mendapatkan jumlah komentar
                                        $query_comment_count = "SELECT COUNT(*) as total FROM komentar_postingan WHERE postingan_id = '{$post['id']}'";
                                        $result_comment_count = mysqli_query($koneksi, $query_comment_count);
                                        $comment_count = mysqli_fetch_assoc($result_comment_count)['total'];
                                        ?>

                                        <!-- informasi like dan komen -->
                                        <div class="d-flex gap-2" style="font-size: 14px;">
                                            <div class="badge rounded-pill bg-light border px-3 py-2">
                                                <span id="reactions-count-<?php echo $post['id']; ?>" class="reactions-count text-black">
                                                    <?php echo $reactions_html ?: "<i class='bi bi-hand-thumbs-up me-1'></i>$total_reactions"; ?>
                                                </span>
                                            </div>
                                            <div class="badge rounded-pill bg-light text-black border px-3 py-2">
                                                <i class="bi bi-chat me-1"></i>
                                                <span><strong><?php echo $comment_count; ?></strong></span>
                                            </div>
                                        </div>


                                        <!-- Ganti bagian tombol like dengan yang lebih sederhana -->
                                        <div class="d-flex gap-2 justify-content-between mt-3 ps-2 pe-2" style="font-size: 14px;">
                                            <button class="btn btn-light flex-fill py-1 py-md-2 d-flex align-items-center justify-content-center gap-2"
                                                id="like-btn-<?php echo $post['id']; ?>"
                                                onclick="toggleLike(<?php echo $post['id']; ?>)">
                                                <?php if ($current_emoji): ?>
                                                    <i class="bi bi-hand-thumbs-up-fill" style="color: rgb(218, 119, 86);"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-hand-thumbs-up"></i>
                                                <?php endif; ?>
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

                                        <!-- script dan animasi aksi like dan emoji -->

                                        <style>
                                            .reaction-bar {
                                                /* Style yang sudah ada */
                                                position: absolute;
                                                bottom: 100%;
                                                left: 50%;
                                                transform: translateX(-50%);
                                                background: white;
                                                border-radius: 20px;
                                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                                                margin-bottom: 10px;
                                                z-index: 1000;
                                                width: max-content;
                                                min-width: 200px;

                                                /* Tambahkan animasi */
                                                opacity: 0;
                                                transform: translateX(-50%) scale(0.5);
                                                transition: all 0.3s cubic-bezier(0.18, 0.89, 0.32, 1.28);
                                            }

                                            .reaction-bar.show {
                                                opacity: 1;
                                                transform: translateX(-50%) scale(1);
                                            }

                                            .reaction-emoji {
                                                cursor: pointer;
                                                padding: 5px 10px;
                                                font-size: 1.5rem;
                                                opacity: 0;
                                                transform: translateY(20px);
                                                transition: all 0.2s ease-out;
                                            }

                                            /* Animasi untuk setiap emoji */
                                            .reaction-bar.show .reaction-emoji:nth-child(1) {
                                                transition-delay: 0.1s;
                                            }

                                            .reaction-bar.show .reaction-emoji:nth-child(2) {
                                                transition-delay: 0.15s;
                                            }

                                            .reaction-bar.show .reaction-emoji:nth-child(3) {
                                                transition-delay: 0.2s;
                                            }

                                            .reaction-bar.show .reaction-emoji:nth-child(4) {
                                                transition-delay: 0.25s;
                                            }

                                            .reaction-bar.show .reaction-emoji:nth-child(5) {
                                                transition-delay: 0.3s;
                                            }

                                            .reaction-bar.show .reaction-emoji {
                                                opacity: 1;
                                                transform: translateY(0);
                                            }

                                            .reaction-emoji:hover {
                                                transform: scale(1.3);
                                            }
                                        </style>

                                        <script>
                                            // Add audio element for like sound
                                            const likeSound = new Audio('assets/like_rev.mp3'); // Make sure to add an audio file

                                            function updateReactionDisplay(postId, reactions, currentEmoji) {
                                                const button = document.getElementById(`like-btn-${postId}`);
                                                const countElement = document.getElementById(`like-count-${postId}`);
                                                const reactionBar = document.getElementById(`reaction-bar-${postId}`);

                                                let totalCount = 0;
                                                let displayText = '';

                                                for (const [emoji, count] of Object.entries(reactions)) {
                                                    totalCount += count;
                                                    if (count > 0) {
                                                        displayText += `${emoji} ${count} `;
                                                    }
                                                }

                                                const buttonText = button.querySelector('span');
                                                if (currentEmoji) {
                                                    if (buttonText) {
                                                        buttonText.textContent = `${currentEmoji} Suka`;
                                                    }
                                                    button.querySelector('i').classList.add('text-primary');
                                                } else {
                                                    if (buttonText) {
                                                        buttonText.textContent = 'Suka';
                                                    }
                                                    button.querySelector('i').classList.remove('text-primary');
                                                }

                                                countElement.innerHTML = displayText || `${totalCount} Suka`;

                                                reactionBar.classList.remove('show');
                                                setTimeout(() => {
                                                    reactionBar.style.display = 'none';
                                                }, 300);
                                            }

                                            function showReactionBar(event, postId) {
                                                event.preventDefault();
                                                const reactionBar = document.getElementById(`reaction-bar-${postId}`);

                                                document.querySelectorAll('.reaction-bar').forEach(bar => {
                                                    if (bar.id !== `reaction-bar-${postId}`) {
                                                        bar.classList.remove('show');
                                                        setTimeout(() => {
                                                            bar.style.display = 'none';
                                                        }, 300);
                                                    }
                                                });

                                                if (reactionBar.style.display === 'none') {
                                                    reactionBar.style.display = 'block';
                                                    requestAnimationFrame(() => {
                                                        reactionBar.classList.add('show');
                                                    });

                                                    setTimeout(() => {
                                                        document.addEventListener('click', function closeReactionBar(e) {
                                                            if (!reactionBar.contains(e.target) &&
                                                                !document.getElementById(`like-btn-${postId}`).contains(e.target)) {
                                                                reactionBar.classList.remove('show');
                                                                setTimeout(() => {
                                                                    reactionBar.style.display = 'none';
                                                                }, 300);
                                                                document.removeEventListener('click', closeReactionBar);
                                                            }
                                                        });
                                                    }, 0);
                                                } else {
                                                    reactionBar.classList.remove('show');
                                                    setTimeout(() => {
                                                        reactionBar.style.display = 'none';
                                                    }, 300);
                                                }
                                            }

                                            function toggleLike(postId, emoji = '👍') {
                                                const button = document.getElementById(`like-btn-${postId}`);
                                                const icon = button.querySelector('i');

                                                fetch('toggle_like.php', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/x-www-form-urlencoded',
                                                        },
                                                        body: `postingan_id=${postId}&emoji=${emoji}`
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            // Update icon
                                                            if (data.is_liked) {
                                                                icon.classList.replace('bi-hand-thumbs-up', 'bi-hand-thumbs-up-fill');
                                                                icon.style.color = 'rgb(218, 119, 86)'; // Changed from text-primary to direct color

                                                                // Smooth scale up animation for like
                                                                icon.style.transition = 'transform 0.3s cubic-bezier(0.18, 0.89, 0.32, 1.28)';
                                                                icon.style.transform = 'scale(1.5)';
                                                                setTimeout(() => {
                                                                    icon.style.transform = 'scale(1)';
                                                                }, 300);

                                                                // Play like sound
                                                                likeSound.play().catch(error => console.log('Error playing sound:', error));
                                                            } else {
                                                                icon.classList.replace('bi-hand-thumbs-up-fill', 'bi-hand-thumbs-up');
                                                                icon.style.color = ''; // Reset color to default

                                                                // Push effect animation for unlike
                                                                icon.style.transition = 'transform 0.2s ease-in-out';
                                                                icon.style.transform = 'translateX(-4px)';
                                                                setTimeout(() => {
                                                                    icon.style.transform = 'translateX(4px)';
                                                                }, 100);
                                                                setTimeout(() => {
                                                                    icon.style.transform = 'translateX(0)';
                                                                }, 200);
                                                            }

                                                            // Update reaction display
                                                            const countElement = document.getElementById(`reactions-count-${postId}`);
                                                            if (data.reactions) {
                                                                let displayText = '';
                                                                for (const [emoji, count] of Object.entries(data.reactions)) {
                                                                    if (count > 0) {
                                                                        displayText += `${emoji} ${count} `;
                                                                    }
                                                                }
                                                                countElement.innerHTML = displayText || `<i class='bi bi-hand-thumbs-up me-1'></i>0`;
                                                            }
                                                        }
                                                    })
                                                    .catch(error => console.error('Error:', error));
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
                                                        <h1 class="modal-title fs-5" id="modalKomentar" style="z-index: 1; ">
                                                            <div class="d-flex flex-column">
                                                                <strong>Komentar</strong>
                                                                <span class="text-muted fs-6" style="font-size: 12px !important;">Total <?php echo mysqli_num_rows($result_komentar); ?> Komentar</span>
                                                            </div>
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <!-- Body Komentar dengan Scroll -->
                                                    <div class="modal-body p-0">
                                                        <div class="komentar-container px-3" style="overflow-y: auto;">
                                                            <?php
                                                            if (mysqli_num_rows($result_komentar) > 0) {
                                                                while ($komentar = mysqli_fetch_assoc($result_komentar)) {
                                                            ?>
                                                                    <div class="comment-thread mb-3">
                                                                        <!-- Main comment -->
                                                                        <div class="d-flex gap-3">
                                                                            <div class="flex-shrink-0">
                                                                                <?php if ($komentar['user_type'] == 'guru'): ?>
                                                                                    <img src="<?php
                                                                                                if (!empty($komentar['foto_guru'])) {
                                                                                                    echo 'uploads/profil/' . $komentar['foto_guru'];
                                                                                                } else {
                                                                                                    echo 'assets/pp.png';
                                                                                                }
                                                                                                ?>"
                                                                                        alt="Teacher Profile"
                                                                                        class="profile-img rounded-4 border-0 bg-white"
                                                                                        style="width: 32px; height: 32px;">
                                                                                <?php else: ?>
                                                                                    <img src="<?php
                                                                                                if (!empty($siswa['photo_url'])) {
                                                                                                    // Jika menggunakan avatar dari DiceBear
                                                                                                    if ($siswa['photo_type'] === 'avatar') {
                                                                                                        echo $siswa['photo_url'];
                                                                                                    }
                                                                                                    // Jika menggunakan foto upload
                                                                                                    else if ($siswa['photo_type'] === 'upload') {
                                                                                                        echo $siswa['photo_url'];
                                                                                                    }
                                                                                                } else {
                                                                                                    // Gambar default
                                                                                                    echo 'assets/pp.png';
                                                                                                }
                                                                                                ?>"
                                                                                        alt="Student Profile"
                                                                                        class="profile-img rounded-4 border-0 bg-white"
                                                                                        style="width: 32px; height: 32px;">
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <div class="comment-bubble p-2 rounded-3" style="background-color: #f0f2f5;">
                                                                                    <div class="fw-semibold" style="font-size: 13px;">
                                                                                        <?php echo htmlspecialchars($komentar['nama_user']); ?>
                                                                                    </div>
                                                                                    <div style="font-size: 13px;">
                                                                                        <?php echo nl2br(htmlspecialchars($komentar['konten'])); ?>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="comment-actions d-flex gap-3 mt-1" style="font-size: 12px; opacity: 1;">
                                                                                    <div class="comment-reactions position-relative">
                                                                                        <div class="d-flex align-items-center">
                                                                                            <button class="btn btn-sm text-muted p-0"
                                                                                                id="comment-like-btn-<?php echo $komentar['id']; ?>"
                                                                                                onclick="showCommentReactionBar(event, <?php echo $komentar['id']; ?>)">
                                                                                                <?php
                                                                                                // Get user's current reaction
                                                                                                $user_reaction_query = "SELECT emoji FROM comment_reactions WHERE comment_id = ? AND user_id = ?";
                                                                                                $stmt = mysqli_prepare($koneksi, $user_reaction_query);
                                                                                                mysqli_stmt_bind_param($stmt, "is", $komentar['id'], $_SESSION['userid']);
                                                                                                mysqli_stmt_execute($stmt);
                                                                                                $user_reaction = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

                                                                                                if ($user_reaction) {
                                                                                                    echo "<p class='p-0 m-0' style='font-size: 12px;'>{$user_reaction['emoji']} ";
                                                                                                    switch ($user_reaction['emoji']) {
                                                                                                        case '👍':
                                                                                                            echo "Ok";
                                                                                                            break;
                                                                                                        case '❤️':
                                                                                                            echo "Cinta";
                                                                                                            break;
                                                                                                        case '😂':
                                                                                                            echo "Wkwk";
                                                                                                            break;
                                                                                                        case '😮':
                                                                                                            echo "GG";
                                                                                                            break;
                                                                                                        case '😢':
                                                                                                            echo "Ya Allah";
                                                                                                            break;
                                                                                                    }
                                                                                                    echo "</p>";
                                                                                                } else {
                                                                                                    echo "<p class='p-0 m-0' style='font-size: 12px;'>Suka</p>";
                                                                                                }
                                                                                                ?>
                                                                                            </button>

                                                                                            <!-- reaction bar -->
                                                                                            <!-- Add this right after the button -->
                                                                                            <div id="comment-reaction-bar-<?php echo $komentar['id']; ?>"
                                                                                                class="reaction-bar-comment"
                                                                                                style="display: none;">
                                                                                                <div class="d-flex justify-content-around p-2">
                                                                                                    <span onclick="toggleCommentReaction(<?php echo $komentar['id']; ?>, '👍')" class="reaction-emoji">👍</span>
                                                                                                    <span onclick="toggleCommentReaction(<?php echo $komentar['id']; ?>, '❤️')" class="reaction-emoji">❤️</span>
                                                                                                    <span onclick="toggleCommentReaction(<?php echo $komentar['id']; ?>, '😂')" class="reaction-emoji">😂</span>
                                                                                                    <span onclick="toggleCommentReaction(<?php echo $komentar['id']; ?>, '😮')" class="reaction-emoji">😮</span>
                                                                                                    <span onclick="toggleCommentReaction(<?php echo $komentar['id']; ?>, '😢')" class="reaction-emoji">😢</span>
                                                                                                </div>
                                                                                            </div>

                                                                                            <!-- Reaction counts -->
                                                                                            <?php
                                                                                            $count_query = "SELECT emoji, COUNT(*) as count FROM comment_reactions WHERE comment_id = ? GROUP BY emoji";
                                                                                            $stmt = mysqli_prepare($koneksi, $count_query);
                                                                                            mysqli_stmt_bind_param($stmt, "i", $komentar['id']);
                                                                                            mysqli_stmt_execute($stmt);
                                                                                            $reactions = mysqli_stmt_get_result($stmt);

                                                                                            if (mysqli_num_rows($reactions) > 0) {
                                                                                            ?>
                                                                                                <div class='ms-2 reaction-counts position-relative'>
                                                                                                    <div class='d-flex align-items-center' onclick="toggleReactionPopover(<?php echo $komentar['id']; ?>)">
                                                                                                        <?php
                                                                                                        $total = 0;
                                                                                                        $emoji_stack = [];
                                                                                                        while ($row = mysqli_fetch_assoc($reactions)) {
                                                                                                            $total += $row['count'];
                                                                                                            $emoji_stack[] = $row['emoji'];
                                                                                                        }
                                                                                                        foreach (array_slice($emoji_stack, 0, 3) as $emoji) {
                                                                                                            echo "<span class='reaction-icon'>$emoji</span>";
                                                                                                        }
                                                                                                        echo "<span class='ms-1 reaction-count'>$total</span>";
                                                                                                        ?>
                                                                                                    </div>

                                                                                                    <!-- Popover for reactions -->
                                                                                                    <div id="reaction-popover-<?php echo $komentar['id']; ?>" class="reaction-popover" style="z-index: 1080 !important;">
                                                                                                        <div id="reaction-content-<?php echo $komentar['id']; ?>" class="p-2">
                                                                                                            <!-- Content will be loaded here -->
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                            <?php
                                                                                            }
                                                                                            ?>

                                                                                        </div>
                                                                                    </div>


                                                                                    <!-- Style for reaction bar -->
                                                                                    <style>
                                                                                        .comment-reactions {
                                                                                            position: relative;
                                                                                        }

                                                                                        .reaction-bar-comment {
                                                                                            position: absolute;
                                                                                            top: -40px;
                                                                                            left: 0;
                                                                                            background: white;
                                                                                            border-radius: 20px;
                                                                                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                                                                                            z-index: 1000;
                                                                                            width: max-content;
                                                                                            min-width: 200px;
                                                                                            opacity: 0;
                                                                                            transform: translateY(10px);
                                                                                            transition: all 0.3s ease;
                                                                                        }

                                                                                        .reaction-bar-comment.show {
                                                                                            opacity: 1;
                                                                                            transform: translateY(0);
                                                                                        }

                                                                                        .reaction-emoji {
                                                                                            cursor: pointer;
                                                                                            padding: 5px 10px;
                                                                                            font-size: 1.2rem;
                                                                                            transition: all 0.2s ease;
                                                                                            opacity: 0;
                                                                                            transform: translateY(10px);
                                                                                        }

                                                                                        .reaction-bar-comment.show .reaction-emoji {
                                                                                            opacity: 1;
                                                                                            transform: translateY(0);
                                                                                        }

                                                                                        .reaction-emoji:hover {
                                                                                            transform: scale(1.4);
                                                                                        }

                                                                                        .reaction-bar-comment.show .reaction-emoji:nth-child(1) {
                                                                                            transition-delay: 0.1s;
                                                                                        }

                                                                                        .reaction-bar-comment.show .reaction-emoji:nth-child(2) {
                                                                                            transition-delay: 0.15s;
                                                                                        }

                                                                                        .reaction-bar-comment.show .reaction-emoji:nth-child(3) {
                                                                                            transition-delay: 0.2s;
                                                                                        }

                                                                                        .reaction-bar-comment.show .reaction-emoji:nth-child(4) {
                                                                                            transition-delay: 0.25s;
                                                                                        }

                                                                                        .reaction-bar-comment.show .reaction-emoji:nth-child(5) {
                                                                                            transition-delay: 0.3s;
                                                                                        }

                                                                                        .reaction-counts {
                                                                                            display: flex;
                                                                                            align-items: center;
                                                                                            background: #f0f2f5;
                                                                                            padding: 2px 8px;
                                                                                            border-radius: 10px;
                                                                                            cursor: pointer;
                                                                                        }

                                                                                        .reaction-icon {
                                                                                            margin-left: -4px;
                                                                                            font-size: 12px;
                                                                                        }

                                                                                        .reaction-count {
                                                                                            font-size: 12px;
                                                                                            color: #65676b;
                                                                                        }

                                                                                        .reaction-icon:first-child {
                                                                                            margin-left: 0;
                                                                                        }
                                                                                    </style>

                                                                                    <!-- style untuk popup emoji -->
                                                                                    <style>
                                                                                        .reaction-popover {
                                                                                            position: fixed;
                                                                                            top: 50%;
                                                                                            left: 50%;
                                                                                            transform: translate(-50%, -50%);
                                                                                            background: white;
                                                                                            border-radius: 12px;
                                                                                            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
                                                                                            width: 250px;
                                                                                            max-height: 300px;
                                                                                            overflow-y: auto;
                                                                                            z-index: 1080;
                                                                                            opacity: 0;
                                                                                            visibility: hidden;
                                                                                            transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
                                                                                        }

                                                                                        /* Add backdrop styling with fade */
                                                                                        .popover-backdrop {
                                                                                            position: fixed;
                                                                                            top: 0;
                                                                                            left: 0;
                                                                                            right: 0;
                                                                                            bottom: 0;
                                                                                            background: rgba(0, 0, 0, 0.5) !important;
                                                                                            z-index: 1070;
                                                                                            opacity: 0;
                                                                                            visibility: hidden;
                                                                                            transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
                                                                                        }

                                                                                        /* Modal backdrop darker */
                                                                                        .modal-backdrop {
                                                                                            background-color: rgba(0, 0, 0, 0.7) !important;
                                                                                        }

                                                                                        .reaction-popover .p-2 {
                                                                                            padding: 8px !important;
                                                                                        }

                                                                                        .reaction-popover h6 {
                                                                                            font-size: 13px;
                                                                                            color: #666;
                                                                                            margin: 0;
                                                                                        }

                                                                                        /* Clean scrollbar */
                                                                                        .reaction-popover::-webkit-scrollbar {
                                                                                            width: 4px;
                                                                                        }

                                                                                        .reaction-popover::-webkit-scrollbar-track {
                                                                                            background: transparent;
                                                                                        }

                                                                                        .reaction-popover::-webkit-scrollbar-thumb {
                                                                                            background: #ddd;
                                                                                            border-radius: 4px;
                                                                                        }

                                                                                        /* Reaction groups styling */
                                                                                        .reaction-group {
                                                                                            padding: 4px 0;
                                                                                        }

                                                                                        .reaction-group-header {
                                                                                            display: flex;
                                                                                            align-items: center;
                                                                                            gap: 6px;
                                                                                            padding: 4px 8px;
                                                                                        }

                                                                                        .reaction-group-emoji {
                                                                                            font-size: 16px;
                                                                                        }

                                                                                        .reaction-group-count {
                                                                                            font-size: 12px;
                                                                                            color: #888;
                                                                                        }

                                                                                        /* User list */
                                                                                        .reaction-user {
                                                                                            display: flex;
                                                                                            align-items: center;
                                                                                            padding: 6px 8px;
                                                                                            transition: all 0.2s;
                                                                                        }

                                                                                        .reaction-user:hover {
                                                                                            background-color: #f8f8f8;
                                                                                        }

                                                                                        .reaction-user img {
                                                                                            width: 28px;
                                                                                            height: 28px;
                                                                                            border-radius: 50%;
                                                                                            margin-right: 8px;
                                                                                        }

                                                                                        .reaction-user-name {
                                                                                            font-size: 13px;
                                                                                            color: #333;
                                                                                        }

                                                                                        /* Show states for fade effect */
                                                                                        .reaction-popover.show {
                                                                                            opacity: 1;
                                                                                            visibility: visible;
                                                                                        }

                                                                                        .popover-backdrop.show {
                                                                                            opacity: 1;
                                                                                            visibility: visible;
                                                                                        }
                                                                                    </style>

                                                                                    <!-- script suka komentar -->
                                                                                    <script>
                                                                                        // Add audio element for reaction sound
                                                                                        const reactionSound = new Audio('assets/like_rev.mp3');

                                                                                        function showCommentReactionBar(event, commentId) {
                                                                                            event.preventDefault();
                                                                                            event.stopPropagation();
                                                                                            const reactionBar = document.getElementById(`comment-reaction-bar-${commentId}`);

                                                                                            document.querySelectorAll('.reaction-bar-comment.show').forEach(bar => {
                                                                                                if (bar.id !== `comment-reaction-bar-${commentId}`) {
                                                                                                    closeReactionBar(bar);
                                                                                                }
                                                                                            });

                                                                                            if (reactionBar.style.display === 'none') {
                                                                                                reactionBar.style.display = 'block';
                                                                                                requestAnimationFrame(() => {
                                                                                                    reactionBar.classList.add('show');
                                                                                                });

                                                                                                setTimeout(() => {
                                                                                                    document.addEventListener('click', closeOnClickOutside);
                                                                                                }, 0);
                                                                                            } else {
                                                                                                closeReactionBar(reactionBar);
                                                                                            }
                                                                                        }

                                                                                        function closeReactionBar(reactionBar) {
                                                                                            reactionBar.classList.remove('show');
                                                                                            setTimeout(() => {
                                                                                                reactionBar.style.display = 'none';
                                                                                            }, 300);
                                                                                            document.removeEventListener('click', closeOnClickOutside);
                                                                                        }

                                                                                        function closeOnClickOutside(event) {
                                                                                            const openBars = document.querySelectorAll('.reaction-bar-comment.show');
                                                                                            openBars.forEach(bar => {
                                                                                                if (!bar.contains(event.target) &&
                                                                                                    !event.target.closest('.btn-sm')) {
                                                                                                    closeReactionBar(bar);
                                                                                                }
                                                                                            });
                                                                                        }

                                                                                        document.querySelectorAll('.reaction-bar-comment').forEach(bar => {
                                                                                            bar.addEventListener('click', (e) => e.stopPropagation());
                                                                                        });

                                                                                        function toggleCommentReaction(commentId, emoji) {
                                                                                            fetch('toggle_comment_reaction.php', {
                                                                                                    method: 'POST',
                                                                                                    headers: {
                                                                                                        'Content-Type': 'application/x-www-form-urlencoded',
                                                                                                    },
                                                                                                    body: `comment_id=${commentId}&emoji=${emoji}`
                                                                                                })
                                                                                                .then(response => response.json())
                                                                                                .then(data => {
                                                                                                    if (data.success) {
                                                                                                        const button = document.getElementById(`comment-like-btn-${commentId}`);
                                                                                                        const reactionBar = document.getElementById(`comment-reaction-bar-${commentId}`);

                                                                                                        // Play sound effect when adding reaction
                                                                                                        reactionSound.play().catch(error => console.log('Error playing sound:', error));

                                                                                                        let reactionText;
                                                                                                        switch (emoji) {
                                                                                                            case '👍':
                                                                                                                reactionText = 'Ok';
                                                                                                                break;
                                                                                                            case '❤️':
                                                                                                                reactionText = 'Cinta';
                                                                                                                break;
                                                                                                            case '😂':
                                                                                                                reactionText = 'Wkwk';
                                                                                                                break;
                                                                                                            case '😮':
                                                                                                                reactionText = 'GG';
                                                                                                                break;
                                                                                                            case '😢':
                                                                                                                reactionText = 'Ya Allah';
                                                                                                                break;
                                                                                                            default:
                                                                                                                reactionText = 'Suka';
                                                                                                        }

                                                                                                        button.innerHTML = `<p class="p-0 m-0" style="font-size: 12px; me-2 ">${emoji} ${reactionText}</p>`;
                                                                                                        closeReactionBar(reactionBar);
                                                                                                    }
                                                                                                });
                                                                                        }

                                                                                        function toggleReactionPopover(commentId) {
                                                                                            const popover = document.getElementById(`reaction-popover-${commentId}`);
                                                                                            const content = document.getElementById(`reaction-content-${commentId}`);

                                                                                            document.querySelectorAll('.reaction-popover.show').forEach(p => {
                                                                                                if (p.id !== `reaction-popover-${commentId}`) {
                                                                                                    p.classList.remove('show');
                                                                                                }
                                                                                            });

                                                                                            if (!popover.classList.contains('show')) {
                                                                                                fetch(`get_reaction_details.php?comment_id=${commentId}`)
                                                                                                    .then(response => response.text())
                                                                                                    .then(html => {
                                                                                                        content.innerHTML = html;
                                                                                                        popover.classList.add('show');
                                                                                                        setTimeout(() => {
                                                                                                            document.addEventListener('click', closePopoverOutside);
                                                                                                        }, 0);
                                                                                                    });
                                                                                            } else {
                                                                                                popover.classList.remove('show');
                                                                                                document.removeEventListener('click', closePopoverOutside);
                                                                                            }
                                                                                        }

                                                                                        function closePopoverOutside(event) {
                                                                                            const popover = document.querySelector('.reaction-popover.show');
                                                                                            if (popover && !popover.contains(event.target) &&
                                                                                                !event.target.closest('.reaction-counts')) {
                                                                                                popover.classList.remove('show');
                                                                                                document.removeEventListener('click', closePopoverOutside);
                                                                                            }
                                                                                        }
                                                                                    </script>
                                                                                    <!-- script untuk  -->
                                                                                    <button class="btn btn-sm p-0 text-muted text-decoration-none" onclick="replyToComment(<?php echo $komentar['id']; ?>, '<?php echo $komentar['nama_user']; ?>', <?php echo $post['id']; ?>)">
                                                                                        <p class="p-0 m-0" style="font-size: 12px;">Balas</p>
                                                                                    </button>
                                                                                </div>

                                                                                <!-- Reply section -->
                                                                                <div class="replies-section mt-2">
                                                                                    <?php
                                                                                    $query_replies = "SELECT r.*, COALESCE(g.namaLengkap, s.nama) as nama_user,
                                                                            CASE WHEN g.username IS NOT NULL THEN g.foto_profil ELSE s.foto_profil END as foto_profil,
                                                                            CASE WHEN g.username IS NOT NULL THEN 'guru' ELSE 'siswa' END as user_type
                                                                            FROM komentar_replies r
                                                                            LEFT JOIN guru g ON r.user_id = g.username
                                                                            LEFT JOIN siswa s ON r.user_id = s.username
                                                                            WHERE r.komentar_id = {$komentar['id']}
                                                                            ORDER BY r.created_at ASC";
                                                                                    $result_replies = mysqli_query($koneksi, $query_replies);
                                                                                    while ($reply = mysqli_fetch_assoc($result_replies)) {
                                                                                    ?>
                                                                                        <div class="d-flex gap-2 mb-2">
                                                                                            <div class="flex-shrink-0">
                                                                                                <img src="<?php
                                                                                                            if (!empty($siswa['photo_url'])) {
                                                                                                                // Jika menggunakan avatar dari DiceBear
                                                                                                                if ($siswa['photo_type'] === 'avatar') {
                                                                                                                    echo $siswa['photo_url'];
                                                                                                                }
                                                                                                                // Jika menggunakan foto upload
                                                                                                                else if ($siswa['photo_type'] === 'upload') {
                                                                                                                    echo $siswa['photo_url'];
                                                                                                                }
                                                                                                            } else {
                                                                                                                // Gambar default
                                                                                                                echo 'assets/pp.png';
                                                                                                            }
                                                                                                            ?>" alt="Profile Image"
                                                                                                    class="profile-img rounded-4 border-0 bg-white"
                                                                                                    style="width: 35px;">

                                                                                            </div>
                                                                                            <div class="flex-grow-1">
                                                                                                <div class="reply-bubble p-2 rounded-3" style="background-color: #f0f2f5; font-size: 12px;">
                                                                                                    <div class="fw-semibold">
                                                                                                        <?php echo htmlspecialchars($reply['nama_user']); ?>
                                                                                                    </div>
                                                                                                    <?php echo nl2br(htmlspecialchars($reply['konten'])); ?>
                                                                                                </div>
                                                                                                <div class="reply-actions mt-1" style="font-size: 11px;">
                                                                                                    <button class="btn btn-sm p-0 text-muted me-2">Reaksi</button>
                                                                                                    <button class="btn btn-sm p-0 text-muted">Balas</button>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                <?php
                                                                }
                                                            } else {
                                                                ?>

                                                                <div class="text-center py-4">
                                                                    <div class="mb-3">
                                                                        <i class="fas fa-comments text-muted" style="font-size: 48px;"></i>
                                                                    </div>
                                                                    <p class="text-muted">Belum ada komentar</p>
                                                                </div>
                                                            <?php
                                                            }
                                                            ?>
                                                        </div>

                                                        <style>
                                                            .replies-section {
                                                                margin-left: 32px;
                                                            }

                                                            .comment-bubble,
                                                            .reply-bubble {
                                                                display: inline-block;
                                                                max-width: 100%;
                                                                word-wrap: break-word;
                                                            }

                                                            .comment-actions,
                                                            .reply-actions {
                                                                opacity: 1;
                                                            }

                                                            .comment-actions:hover,
                                                            .reply-actions:hover {
                                                                opacity: 1;
                                                            }

                                                            .reply-bubble {
                                                                background-color: #f0f2f5;
                                                            }
                                                        </style>


                                                        <script>
                                                            function replyToComment(commentId, userName) {
                                                                const textarea = document.querySelector(`#commentModal-${postId} textarea`);
                                                                textarea.value = `@${userName} `;
                                                                textarea.focus();

                                                                // Store the comment ID for reply
                                                                textarea.dataset.replyTo = commentId;
                                                            }

                                                            function submitComment(postId) {
                                                                const form = document.querySelector(`#commentModal-${postId} .komentar-form`);
                                                                const textarea = form.querySelector('textarea');
                                                                const replyTo = textarea.dataset.replyTo;

                                                                const data = new FormData();
                                                                data.append('postingan_id', postId);
                                                                data.append('konten', textarea.value);

                                                                const endpoint = replyTo ? 'tambah_balasan.php' : 'tambah_komentar.php';
                                                                if (replyTo) {
                                                                    data.append('komentar_id', replyTo);
                                                                }

                                                                fetch(endpoint, {
                                                                        method: 'POST',
                                                                        body: data
                                                                    })
                                                                    .then(response => response.json())
                                                                    .then(data => {
                                                                        if (data.status === 'success') {
                                                                            location.reload();
                                                                        }
                                                                    });
                                                            }
                                                        </script>
                                                        <script>
                                                            let currentPostId; // Store current post ID globally

                                                            function replyToComment(commentId, userName, postId) {
                                                                currentPostId = postId;
                                                                const textarea = document.querySelector(`#commentModal-${postId} textarea`);
                                                                textarea.value = `@${userName} `;
                                                                textarea.focus();
                                                                textarea.dataset.replyTo = commentId;
                                                            }

                                                            function toggleReaction(commentId) {
                                                                const reactionBar = document.createElement('div');
                                                                reactionBar.className = 'reaction-bar bg-white shadow rounded p-2';
                                                                reactionBar.innerHTML = `
                                                    <div class="d-flex gap-2">
                                                        <span onclick="addReaction(${commentId}, '👍')" class="reaction-emoji">👍</span>
                                                        <span onclick="addReaction(${commentId}, '❤️')" class="reaction-emoji">❤️</span>
                                                        <span onclick="addReaction(${commentId}, '😊')" class="reaction-emoji">😊</span>
                                                        <span onclick="addReaction(${commentId}, '😢')" class="reaction-emoji">😢</span>
                                                    </div>
                                                `;

                                                                const button = event.currentTarget;
                                                                if (button.nextElementSibling?.classList.contains('reaction-bar')) {
                                                                    button.nextElementSibling.remove();
                                                                } else {
                                                                    button.parentElement.insertBefore(reactionBar, button.nextElementSibling);
                                                                }
                                                            }

                                                            function addReaction(commentId, emoji) {
                                                                // Add reaction handling logic here
                                                                console.log(`Added ${emoji} to comment ${commentId}`);
                                                            }
                                                        </script>



                                                        <style>
                                                            .replies-section {
                                                                font-size: 0.9em;
                                                            }

                                                            .reply-content {
                                                                background-color: #f8f9fa;
                                                                border-radius: 12px;
                                                                padding: 8px 12px;
                                                                margin-left: 40px;
                                                            }

                                                            .comment-actions {
                                                                opacity: 0;
                                                                transition: opacity 0.2s;
                                                            }

                                                            .comment-content:hover .comment-actions {
                                                                opacity: 1;
                                                            }

                                                            .reaction-emoji {
                                                                cursor: pointer;
                                                                padding: 4px;
                                                                transition: transform 0.2s;
                                                            }

                                                            .reaction-emoji:hover {
                                                                transform: scale(1.2);
                                                            }

                                                            .reaction-bar {
                                                                position: absolute;
                                                                margin-top: -40px;
                                                                z-index: 1000;
                                                            }
                                                        </style>

                                                    </div>

                                                    <!-- Footer dengan Input Komentar -->

                                                    <script>
                                                        function hapusKomentar(komentarId, postId) {
                                                            if (confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
                                                                fetch('hapus_komentar.php', {
                                                                        method: 'POST',
                                                                        headers: {
                                                                            'Content-Type': 'application/x-www-form-urlencoded',
                                                                        },
                                                                        body: `komentar_id=${komentarId}&post_id=${postId}`
                                                                    })
                                                                    .then(response => response.json())
                                                                    .then(data => {
                                                                        if (data.success) {
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
                                                                <img src="<?php
                                                                            if (!empty($siswa['photo_url'])) {
                                                                                // Jika menggunakan avatar dari DiceBear
                                                                                if ($siswa['photo_type'] === 'avatar') {
                                                                                    echo $siswa['photo_url'];
                                                                                }
                                                                                // Jika menggunakan foto upload
                                                                                else if ($siswa['photo_type'] === 'upload') {
                                                                                    echo $siswa['photo_url'];
                                                                                }
                                                                            } else {
                                                                                // Gambar default
                                                                                echo 'assets/pp.png';
                                                                            }
                                                                            ?>" alt="Profile Image"
                                                                    class="profile-img rounded-4 border-0 bg-white"
                                                                    style="width: 35px;">

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
                                            // Tambahkan style untuk animasi
                                            const styleSheet = document.createElement("style");
                                            styleSheet.textContent = `
@keyframes highlightComment {
    0% {
        background-color: rgba(218, 119, 86, 0.2);
        transform: translateY(20px);
        opacity: 0;
    }
    50% {
        background-color: rgba(218, 119, 86, 0.2);
        transform: translateY(0);
        opacity: 1;
    }
    100% {
        background-color: transparent;
        transform: translateY(0);
        opacity: 1;
    }
}

.new-comment {
    animation: highlightComment 2s ease-out forwards;
}
`;
                                            document.head.appendChild(styleSheet);

                                            function submitKomentar(postId) {
                                                const form = document.querySelector(`.komentar-form[data-postid="${postId}"]`);
                                                const textarea = form.querySelector('textarea');
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
                                                            const container = document.querySelector(`#commentModal-${postId} .komentar-container`);

                                                            // Get the correct profile photo URL
                                                            let photoUrl = data.komentar.foto_profil || 'assets/pp.png';

                                                            const komentarHTML = `
                <div class="d-flex gap-3 mb-3 new-comment">
                    <div class="flex-shrink-0">
                        <img src="<?php
                                    if (!empty($siswa['photo_url'])) {
                                        // Jika menggunakan avatar dari DiceBear
                                        if ($siswa['photo_type'] === 'avatar') {
                                            echo $siswa['photo_url'];
                                        }
                                        // Jika menggunakan foto upload
                                        else if ($siswa['photo_type'] === 'upload') {
                                            echo $siswa['photo_url'];
                                        }
                                    } else {
                                        // Gambar default
                                        echo 'assets/pp.png';
                                    }
                                    ?>"
                        alt="" 
                        width="32px" 
                        height="32px" 
                        class="rounded-circle border"
                        style="object-fit: cover;">
                    </div>
                    <div class="bubble-chat flex-grow-1">
                        <div class="rounded-4 p-3" style="background-color: #f0f2f5;">
                            <h6 class="p-0 m-0 mb-1" style="font-size: 13px; font-weight: 600;">
                                ${data.komentar.nama_user}
                            </h6>
                            <p class="p-0 m-0" style="font-size: 13px; line-height: 1.4;">
                                ${data.komentar.konten}
                            </p>
                        </div>
                        <small class="text-muted ms-1" style="font-size: 11px;">
                            Baru saja terkirim
                        </small>
                    </div>
                </div>
            `;

                                                            // Tambahkan komentar baru di bagian bawah
                                                            container.insertAdjacentHTML('beforeend', komentarHTML);

                                                            // Ambil elemen komentar yang baru ditambahkan
                                                            const newComment = container.lastElementChild;

                                                            // Scroll ke komentar baru dengan animasi smooth
                                                            setTimeout(() => {
                                                                newComment.scrollIntoView({
                                                                    behavior: 'smooth',
                                                                    block: 'center'
                                                                });
                                                            }, 100);

                                                            // Reset textarea
                                                            textarea.value = '';
                                                            textarea.style.height = 'auto';

                                                            // Update jumlah komentar di modal
                                                            const countElement = document.querySelector(`#commentModal-${postId} .modal-title .text-muted`);
                                                            if (countElement) {
                                                                countElement.textContent = parseInt(countElement.textContent) + 1;
                                                            }
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
                            if (confirm('Apakah Anda yakin ingin menghapus postingan ini?')) {
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
                    <!-- Image Modal -->
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-body p-0 position-relative">
                                    <!-- Close button -->
                                    <button type="button" class="btn-close position-absolute end-0 m-3"
                                        data-bs-dismiss="modal" aria-label="Close"
                                        style="z-index: 1050; background-color: white;"></button>

                                    <!-- Image -->
                                    <img src="" id="modalImage" class="w-100 img-fluid" alt="Preview">

                                    <!-- Download button -->
                                    <button onclick="downloadImage(document.getElementById('modalImage').src)"
                                        class="btn btn-sm position-absolute bottom-0 end-0 m-3"
                                        style="background-color: rgba(255,255,255,0.9);">
                                        <i class="bi bi-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function downloadImage(imageUrl) {
                            fetch(imageUrl)
                                .then(response => response.blob())
                                .then(blob => {
                                    const url = window.URL.createObjectURL(blob);
                                    const a = document.createElement('a');
                                    a.href = url;
                                    a.download = 'image.jpg';
                                    document.body.appendChild(a);
                                    a.click();
                                    window.URL.revokeObjectURL(url);
                                    document.body.removeChild(a);
                                })
                                .catch(error => console.error('Error:', error));
                        }
                    </script>

                    <style>
                        .modal-content {
                            border: none;
                            border-radius: 8px;
                            overflow: hidden;
                        }

                        .btn-close:hover {
                            background-color: rgba(255, 255, 255, 0.9) !important;
                        }

                        .modal-body button {
                            transition: opacity 0.2s;
                        }

                        .modal-body button:hover {
                            opacity: 0.8;
                        }
                    </style>
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
                                                    <textarea class="form-control" name="deskripsi" placeholder="Apa pendapat Anda?" style="height: 100px;"><?php echo htmlspecialchars($kelas['deskripsi']); ?></textarea>
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
                            </div>

                            <?php
                            $query_catatan = "SELECT * FROM catatan_guru WHERE kelas_id = '$kelas_id' ORDER BY created_at DESC";
                            $result_catatan = mysqli_query($koneksi, $query_catatan);
                            ?>

                            <?php if (isset($_GET['success'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <?php echo ($_GET['success'] == 'catatan_deleted') ? "Catatan berhasil dihapus!" : ""; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    <?php echo ($_GET['error'] == 'delete_failed') ? "Gagal menghapus catatan" : ""; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (mysqli_num_rows($result_catatan) > 0): ?>
                                <div class="catatan-list">
                                    <?php while ($catatan = mysqli_fetch_assoc($result_catatan)): ?>
                                        <div class="catatan-item p-4 rounded-2 mb-3 bg-light border">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-2 fw-bold"><?php echo htmlspecialchars($catatan['judul']); ?></h6>
                                                    <div class="d-flex align-items-center text-muted mb-3" style="font-size: 0.85rem;">
                                                        <i class="bi bi-calendar3 me-2"></i>
                                                        <?php echo date('d F Y', strtotime($catatan['created_at'])); ?>
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
                                                </style>
                                            </div>

                                            <div class="catatan-content">
                                                <p class="mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                                                    <?php echo nl2br(htmlspecialchars($catatan['konten'])); ?>
                                                </p>
                                                <div class="">
                                                    <div class="d-flex">
                                                        <?php if ($catatan['file_lampiran']): ?>
                                                            <a href="<?php echo htmlspecialchars($catatan['file_lampiran']); ?>"
                                                                class="text-decoration-none flex-fill d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-white border hover-shadow"
                                                                target="_blank">
                                                                <?php
                                                                $ext = pathinfo($catatan['file_lampiran'], PATHINFO_EXTENSION);
                                                                $icon = match ($ext) {
                                                                    'pdf' => 'bi-file-pdf-fill text-danger',
                                                                    'doc', 'docx' => 'bi-file-word-fill text-primary',
                                                                    'jpg', 'jpeg', 'png' => 'bi-file-image-fill text-success',
                                                                    default => 'bi-file-earmark-fill text-secondary'
                                                                };
                                                                ?>
                                                                <i class="bi <?php echo $icon; ?>"></i>
                                                                <span class="text-black">Unduh lampiran</span>
                                                            </a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center p-5 rounded-4" style="background-color:rgb(255, 245, 240);">
                                    <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                                    <h6 class="fw-bold mb-2">Belum Ada Catatan</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                        Mulai tambahkan catatan untuk kelas ini
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <style>
                    .catatan-item {
                        transition: all 0.2s ease;
                    }

                    .catatan-item:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                    }

                    .hover-shadow {
                        transition: all 0.2s ease;
                    }

                    .hover-shadow:hover {
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
                        <i class="bi bi-list"></i>
                    </button>

                    <!-- Mini FABs -->
                    <div class="mini-fabs">
                        <!-- Catatan Button -->
                        <button class="btn mini-fab rounded-circle shadow"
                            data-bs-toggle="modal"
                            data-bs-target="#semuaCatatanModal"
                            title="Catatan">
                            <i class="bi bi-journal-text"></i>
                            <span class="fab-label">Catatan Guru</span>
                        </button>

                        <!-- Agenda Button -->
                        <button class="btn mini-fab rounded-circle shadow"
                            data-bs-toggle="modal"
                            data-bs-target="#agendaSiswaModal"
                            title="Agenda">
                            <i class="bi bi-calendar-event"></i>
                            <span class="fab-label">Agenda Siswa</span>
                        </button>

                    </div>

                    <!-- Backdrop for FAB -->
                    <div class="fab-backdrop"></div>
                </div>

                <!-- Modal Agenda Siswa -->
                <div class="modal fade" id="agendaSiswaModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h5 class="modal-title fw-bold">Agenda Siswa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center p-5">
                                <i class="bi bi-tools fs-1 text-muted mb-3"></i>
                                <h6 class="fw-bold mb-2">Fitur Sedang Maintenance</h6>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                    Kami sedang melakukan perbaikan pada fitur ini. Mohon kembali lagi nanti.
                                </p>
                            </div>
                        </div>
                    </div>
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
                        background: rgba(0, 0, 0, 0.7);
                        color: white;
                        padding: 4px 8px;
                        border-radius: 4px;
                        font-size: 12px;
                        white-space: nowrap;
                        opacity: 1;
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
                        background: rgba(0, 0, 0, 0.5);
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

                                if (mysqli_num_rows($result_all_catatan) > 0):
                                ?>
                                    <div class="catatan-list">
                                        <?php while ($catatan = mysqli_fetch_assoc($result_all_catatan)): ?>
                                            <div class="catatan-item p-3 rounded mb-3 bg-light border">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-2 fw-bold"><?php echo htmlspecialchars($catatan['judul']); ?></h6>
                                                        <div class="d-flex align-items-center text-muted mb-2" style="font-size: 0.85rem;">
                                                            <i class="bi bi-calendar3 me-2"></i>
                                                            <?php echo date('d F Y', strtotime($catatan['created_at'])); ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <p class="mb-3 text-secondary" style="font-size: 0.95rem;">
                                                    <?php echo nl2br(htmlspecialchars($catatan['konten'])); ?>
                                                </p>

                                                <?php if ($catatan['file_lampiran']): ?>
                                                    <a href="<?php echo htmlspecialchars($catatan['file_lampiran']); ?>"
                                                        class="text-decoration-none d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-white border"
                                                        target="_blank">
                                                        <?php
                                                        $ext = pathinfo($catatan['file_lampiran'], PATHINFO_EXTENSION);
                                                        $icon = match ($ext) {
                                                            'pdf' => 'bi-file-pdf-fill text-danger',
                                                            'doc', 'docx' => 'bi-file-word-fill text-primary',
                                                            'jpg', 'jpeg', 'png' => 'bi-file-image-fill text-success',
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
                                    <div class="text-center p-4">
                                        <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                                        <h6 class="fw-bold mb-2">Belum Ada Catatan</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                            Mulai tambahkan catatan untuk kelas ini
                                        </p>
                                    </div>
                                <?php endif; ?>
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
                    document.getElementById('semuaCatatanModal').addEventListener('hidden.bs.modal', function() {
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
                    // Ketika tingkat kelas berubah
                    document.getElementById('tingkatKelas').addEventListener('change', function() {
                        const tingkat = this.value;
                        const daftarSiswaDiv = document.querySelector('.daftar-siswa');

                        if (tingkat) {
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

                        if (selectedSiswa.length === 0) {
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
                                if (data.success) {
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

                                if (mysqli_num_rows($result_siswa_hapus) > 0):
                                ?>
                                    <div class="list-siswa">
                                        <?php while ($siswa = mysqli_fetch_assoc($result_siswa_hapus)): ?>
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
                        if (confirm(`Apakah Anda yakin ingin menghapus ${namaSiswa} dari kelas ini?`)) {
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

                                if (mysqli_num_rows($result_semua_siswa) > 0):
                                    while ($siswa = mysqli_fetch_assoc($result_semua_siswa)):
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
                        max-width: calc(100% - 40px);
                        /* 40px untuk gambar + padding */
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
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>



</body>

</html>