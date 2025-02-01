<?php
session_start();
require "koneksi.php";
if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

// Tambahkan debug info di sini
// echo "Debug seluruh session:<br>";
// var_dump($_SESSION);
// echo "<br><br>";

// Ambil userid dari session
$userid = $_SESSION['userid'];

// Get current student ID from URL or set default
$siswa_id = isset($_GET['siswa_id']) ? $_GET['siswa_id'] : null;
// Ambil parameter dari URL
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : 1;
$selected_tahun_ajaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : date('Y') . '/' . (date('Y') + 1);

// Ambil data statistik siswa
$statistik = null;
if($siswa_id) {
    $query_statistik = "SELECT * FROM pg 
                       WHERE siswa_id = ? 
                       AND semester = ? 
                       AND tahun_ajaran = ? 
                       ORDER BY created_at DESC 
                       LIMIT 1";
    
    $stmt = mysqli_prepare($koneksi, $query_statistik);
    mysqli_stmt_bind_param($stmt, "iis", $siswa_id, $selected_semester, $selected_tahun_ajaran);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $statistik = mysqli_fetch_assoc($result);
}

// Inisialisasi nilai default
$akademik = 0;
$ibadah = 0;
$akademik = 0;
$ibadah = 0; 
$pengembangan = 0;
$sosial = 0;
$kesehatan = 0;
$karakter = 0;

// Hitung rata-rata jika ada data
if ($statistik) {
    $nilai_akademik = $statistik['nilai_akademik'] ?? 0;
    $keaktifan = $statistik['keaktifan'] ?? 0;
    $pemahaman = $statistik['pemahaman'] ?? 0;
    $akademik = ($nilai_akademik + $keaktifan + $pemahaman) / 3;

    $kehadiran_ibadah = $statistik['kehadiran_ibadah'] ?? 0;
    $kualitas_ibadah = $statistik['kualitas_ibadah'] ?? 0;
    $pemahaman_agama = $statistik['pemahaman_agama'] ?? 0;
    $ibadah = ($kehadiran_ibadah + $kualitas_ibadah + $pemahaman_agama) / 3;

    $minat_bakat = $statistik['minat_bakat'] ?? 0;
    $prestasi = $statistik['prestasi'] ?? 0;
    $keaktifan_ekskul = $statistik['keaktifan_ekskul'] ?? 0;
    $pengembangan = ($minat_bakat + $prestasi + $keaktifan_ekskul) / 3;

    $partisipasi_sosial = $statistik['partisipasi_sosial'] ?? 0;
    $empati = $statistik['empati'] ?? 0;
    $kerja_sama = $statistik['kerja_sama'] ?? 0;
    $sosial = ($partisipasi_sosial + $empati + $kerja_sama) / 3;

    $kebersihan_diri = $statistik['kebersihan_diri'] ?? 0;
    $aktivitas_fisik = $statistik['aktivitas_fisik'] ?? 0;
    $pola_makan = $statistik['pola_makan'] ?? 0;
    $kesehatan = ($kebersihan_diri + $aktivitas_fisik + $pola_makan) / 3;

    $kejujuran = $statistik['kejujuran'] ?? 0;
    $tanggung_jawab = $statistik['tanggung_jawab'] ?? 0;
    $kedisiplinan = $statistik['kedisiplinan'] ?? 0;
    $karakter = ($kejujuran + $tanggung_jawab + $kedisiplinan) / 3;
}


if(!isset($_GET['semester']) && !isset($_GET['tahun_ajaran'])) {
    // Default hanya jika tidak ada parameter GET
    $current_month = date('n');
    $selected_semester = ($current_month >= 7 && $current_month <= 12) ? 1 : 2;
    $current_year = date('Y');
    $selected_tahun_ajaran = ($selected_semester == 1) ? $current_year . '/' . ($current_year + 1) : ($current_year - 1) . '/' . $current_year;
}


// Get all students from database
$query_all_students = "SELECT id, nama, tingkat FROM siswa ORDER BY tingkat, nama";
$result_students = mysqli_query($koneksi, $query_all_students);


// Di bagian atas file, setelah mendapatkan data siswa
$current_student = null;
if($siswa_id) {
    $query_current = "SELECT * FROM siswa WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query_current);
    mysqli_stmt_bind_param($stmt, "i", $siswa_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $current_student = mysqli_fetch_assoc($result);
}  

// Ambil data guru
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);

// ambil data siswa 
$query_siswa = "SELECT * FROM siswa WHERE username = '$userid'";
$result_siswa = mysqli_query($koneksi, $query_siswa);
$siswa = mysqli_fetch_assoc($result_siswa);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>E-Raport Progressive Guidance - SMAGAEdu</title>
</head>
<style>
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
        body{ 
            font-family: merriweather;
        }
        .color-web {
            background-color: rgb(218, 119, 86);
            transition: background-color 0.3s ease;
        }

        .color-web:hover{
            background-color: rgb(206, 100, 65);
        }

        body::-webkit-scrollbar {
            display: none;
        }

        body {
            -ms-overflow-style: none;  /* for Internet Explorer, Edge */
            scrollbar-width: none;  /* for Firefox */
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
            <div class="col p-4 col-utama mt-1 mt-md-0">
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
            
            <div class="container-fluid">
                <!-- Row 1: All Information -->
                <div class="row mb-4">
                    <!-- Left Column: Student Profile -->
                    <div class="col-md-3">
                        <div class="card mb-3 border">

                            <div class="card-body text-center pt-2">
                                <div class="position-relative mb-3 d-inline-block">
                                    <img src="<?php echo !empty($current_student['foto_profil']) ? 'uploads/profil/'.$current_student['foto_profil'] : 'assets/pp.png'; ?>" 
                                        class="rounded-circle object-fit-cover" 
                                        width="80" 
                                        height="80"
                                        style="border: 2px solidrgb(219, 219, 219);">
                                    
                                    <?php if($current_student): ?>
                                        <button type="button" 
                                            class="btn btn-sm position-absolute bottom-0 end-0 p-1 rounded-circle shadow-sm"
                                            style="background: #fff; border: 2px solid #f8f9fa;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#fotoModal">
                                            <i class="bi bi-pencil text-muted" style="font-size: 12px;"></i>
                                        </button>

                                        <!-- Modal Ganti Foto -->
                                        <div class="modal fade" id="fotoModal" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">E-Raport P. Guidance</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="update_foto_siswa.php" method="POST" enctype="multipart/form-data" id="formFoto">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="siswa_id" value="<?php echo $current_student['id']; ?>">
                                                            <input type="hidden" name="croppedImage" id="croppedImage">
                                                            
                                                            <div class="text-start">
                                                                <h5 class="fw-bold">Gambar Profil</h5>
                                                                <p style="font-size: 12px;">Perbarui foto siswa Anda dengan menekan tambah foto baru di bawah, perubahan foto
                                                                    akan berdampak pada akun LMS siswa
                                                                </p>
                                                            </div>
                                                            
                                                            <div class="img-container mb-3" style="max-height: 400px;">
                                                                <img id="image" class="rounded-circle" src="<?php echo !empty($current_student['foto_profil']) ? 'uploads/profil/'.$current_student['foto_profil'] : 'assets/pp.png'; ?>" 
                                                                    style="max-width: 100%; max-height: 300px;">
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <input type="file" name="foto_profil" id="foto_profil" class="d-none" accept="image/*" onchange="loadImage(this)">
                                                                <label for="foto_profil" class="btn mb-0 d-flex px-3" style="background-color: rgb(206, 100, 65);">
                                                                    <i class="bi bi-cloud-upload text-white"></i>
                                                                    <p class="p-0 m-0 text-white ms-2"> Pilih</p>
                                                                </label>
                                                                <button type="button" class="btn d-flex px-3" style="background-color: rgb(206, 100, 65);"" onclick="cropAndSubmit()">
                                                                    <i class="bi bi-check-lg text-white"></i>
                                                                    <p class="p-0 m-0 text-white ms-2"> Simpan</p>
                                                                </button>
                                                            </div>
                                                            <div class="form-text mt-2">Format: JPG, PNG (Max. 2MB)</div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <style>
                                        .img-container {
                                            margin: 20px auto;
                                            max-width: 100%;
                                        }
                                        .cropper-view-box,
                                        .cropper-face {
                                            border-radius: 50%;
                                        }
                                        </style>

                                        <script>
                                        let cropper;

                                        function loadImage(input) {
                                            if (input.files && input.files[0]) {
                                                const reader = new FileReader();
                                                reader.onload = function(e) {
                                                    if (cropper) {
                                                        cropper.destroy();
                                                    }
                                                    const image = document.getElementById('image');
                                                    image.src = e.target.result;
                                                    cropper = new Cropper(image, {
                                                        aspectRatio: 1,
                                                        viewMode: 1,
                                                        dragMode: 'move',
                                                        autoCropArea: 1,
                                                        cropBoxResizable: false,
                                                        cropBoxMovable: false,
                                                        guides: false,
                                                        center: true,
                                                        highlight: false
                                                    });
                                                }
                                                reader.readAsDataURL(input.files[0]);
                                            }
                                        }

                                        function cropAndSubmit() {
                                            if (!cropper) {
                                                document.getElementById('formFoto').submit();
                                                return;
                                            }
                                            
                                            cropper.getCroppedCanvas({
                                                width: 300,
                                                height: 300
                                            }).toBlob((blob) => {
                                                const formData = new FormData(document.getElementById('formFoto'));
                                                formData.append('croppedImage', blob);
                                                
                                                fetch('update_foto_siswa.php', {
                                                    method: 'POST',
                                                    body: formData
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if(data.success) {
                                                        location.reload();
                                                    } else {
                                                        alert('Gagal mengupload foto');
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error:', error);
                                                    alert('Terjadi kesalahan');
                                                });
                                            });
                                        }
                                        </script>
                                        
                                    <script>
                                    function previewImage(input) {
                                        if (input.files && input.files[0]) {
                                            var reader = new FileReader();
                                            reader.onload = function(e) {
                                                document.getElementById('previewFoto').src = e.target.result;
                                            }
                                            reader.readAsDataURL(input.files[0]);
                                        }
                                    }
                                    </script>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex flex-column align-items-center p-2">
                                    <h6 class="mb-1 fw-bold" style="font-size: 14px;">
                                        <?php echo $current_student ? htmlspecialchars($current_student['nama']) : '-'; ?>
                                    </h6>
                                    
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-light text-dark border">
                                            Kelas <?php echo $current_student ? htmlspecialchars($current_student['tingkat']) : '-'; ?>
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-light text-dark border">
                                            Semester <?php echo $selected_semester; ?>
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            TA. <?php echo $selected_tahun_ajaran; ?>
                                        </span>
                                    </div>

                                    <div class="d-flex gap-1 w-100" <?php echo !$current_student ? 'style="display:none;"' : ''; ?>>
                                        <button class="btn btn-sm btn-light border flex-fill d-flex align-items-center justify-content-center gap-1" style="font-size: 11px;">
                                            <i class="bi bi-printer"></i>
                                            Print
                                        </button>
                                        <button class="btn btn-sm btn-light border flex-fill d-flex align-items-center justify-content-center gap-1" style="font-size: 11px;">
                                            <i class="bi bi-file-pdf"></i>
                                            PDF
                                        </button>
                                    </div>
                                </div>
                            </div>  
                        </div>

                        <!-- Mobile view - Combined search -->
                        <div class="card mb-3 d-block d-md-none">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Pencarian</h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" id="mobileSearchForm">
                                    <div class="mb-2">
                                        <label class="form-label small mb-1">Pilih Siswa</label>
                                        <select name="siswa_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">Pilih Siswa...</option>
                                            <?php 
                                            // Reset pointer and sort students alphabetically
                                            mysqli_data_seek($result_students, 0);
                                            $students = array();
                                            while($student = mysqli_fetch_assoc($result_students)) {
                                                $students[] = $student;
                                            }
                                            usort($students, function($a, $b) {
                                                return strcmp($a['nama'], $b['nama']);
                                            });
                                            
                                            foreach($students as $student): 
                                            ?>
                                                <option value="<?= $student['id'] ?>" <?= ($siswa_id == $student['id']) ? 'selected' : '' ?>>
                                                    <?= $student['nama'] ?> - <?= $student['tingkat'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label small mb-1">Semester</label>
                                        <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="1" <?php echo ($selected_semester == 1) ? 'selected' : ''; ?>>Semester 1</option>
                                            <option value="2" <?php echo ($selected_semester == 2) ? 'selected' : ''; ?>>Semester 2</option>
                                        </select>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label small mb-1">Tahun Ajaran</label>
                                        <select name="tahun_ajaran" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <?php
                                            $current_year = date('Y');
                                            for($i = $current_year - 5; $i <= $current_year + 5; $i++) {
                                                $tahun_option = $i . '/' . ($i + 1);
                                                $selected = ($tahun_option == $selected_tahun_ajaran) ? 'selected' : '';
                                                echo "<option value='$tahun_option' $selected>$tahun_option</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Desktop view - Separate cards (hidden on mobile) -->
                        <div class="d-none d-md-block">
                            <!-- cari siswa -->
                            <div class="card mb-3">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">Cari Siswa</h6>
                                </div>
                                <div class="card-body">
                                    <form action="" method="GET">
                                        <div class="input-group input-group-sm">
                                            <select name="siswa_id" class="form-select" onchange="window.location.href = 'raport_pg.php?siswa_id=' + this.value">
                                                <option value="">Pilih Siswa...</option>
                                                <?php 
                                                mysqli_data_seek($result_students, 0); // Reset pointer
                                                while($student = mysqli_fetch_assoc($result_students)): 
                                                ?>
                                                    <option value="<?= $student['id'] ?>" <?= ($siswa_id == $student['id']) ? 'selected' : '' ?>>
                                                        <?= $student['nama'] ?> - <?= $student['tingkat'] ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Semester dan Tahun Ajaran -->
                            <div class="card mb-3">
                                <div class="card-body p-3">
                                    <form method="GET" id="semesterForm">
                                        <input type="hidden" name="siswa_id" value="<?php echo $siswa_id; ?>">
                                        
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Semester</label>
                                            <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="1" <?php echo ($selected_semester == 1) ? 'selected' : ''; ?>>Semester 1</option>
                                                <option value="2" <?php echo ($selected_semester == 2) ? 'selected' : ''; ?>>Semester 2</option>
                                            </select>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label small mb-1">Tahun Ajaran</label>
                                            <select name="tahun_ajaran" class="form-select" onchange="this.form.submit()">
                                                <?php
                                                $current_year = date('Y');
                                                for($i = $current_year - 5; $i <= $current_year + 5; $i++) {
                                                    $tahun_option = $i . '/' . ($i + 1);
                                                    $selected = ($tahun_option == $selected_tahun_ajaran) ? 'selected' : '';
                                                    echo "<option value='$tahun_option' $selected>$tahun_option</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        

                    </div>

                    <!-- Right Column: Quick Stats -->
                    <!-- Dropdown and Charts Container -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <div style="height: 200px">
                                    <canvas id="barChart"></canvas>
                                </div>
                            </div>

                            <script>
                            // Update to bar chart
                            const ctx = document.getElementById('barChart').getContext('2d');
                            const barChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['Akademik', 'Ibadah', 'Pengembangan', 'Sosial', 'Kesehatan', 'Karakter'],
                                    datasets: [{
                                        data: [
                                            <?php echo $akademik; ?>,
                                            <?php echo $ibadah; ?>,
                                            <?php echo $pengembangan; ?>,
                                            <?php echo $sosial; ?>,
                                            <?php echo $kesehatan; ?>, 
                                            <?php echo $karakter; ?>
                                        ],
                                        backgroundColor: 'rgba(218, 119, 86, 0.2)',
                                        borderColor: 'rgb(218, 119, 86)',
                                        borderWidth: 1,
                                        borderRadius: 8
                                    }]
                                },
                                options: {
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100,
                                            ticks: {
                                                stepSize: 20
                                            }
                                        }
                                    }
                                }
                            });
                            </script>
                            
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Akademik -->
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background-color: rgba(218, 119, 86, 0.1);">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-book me-2" style="color: rgb(206, 100, 65);"></i>
                                                <h6 class="mb-0">Akademik (<?php echo number_format($akademik, 1); ?>%)</h6>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted" style="font-size: 12px;">Nilai Akademik: <?php echo number_format($statistik['nilai_akademik'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Keaktifan: <?php echo number_format($statistik['keaktifan'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Pemahaman: <?php echo number_format($statistik['pemahaman'] ?? 0, 1); ?>%</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ibadah -->
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background-color: rgba(218, 119, 86, 0.1);">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-circle me-2" style="color: rgb(206, 100, 65);"></i>
                                                <h6 class="mb-0">Ibadah (<?php echo number_format($ibadah, 1); ?>%)</h6>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted" style="font-size: 12px;">Kehadiran Ibadah: <?php echo number_format($statistik['kehadiran_ibadah'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Kualitas Ibadah: <?php echo number_format($statistik['kualitas_ibadah'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Pemahaman Agama: <?php echo number_format($statistik['pemahaman_agama'] ?? 0, 1); ?>%</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pengembangan -->
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background-color: rgba(218, 119, 86, 0.1);">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-person-plus me-2" style="color: rgb(206, 100, 65);"></i>
                                                <h6 class="mb-0">Pengembangan (<?php echo number_format($pengembangan, 1); ?>%)</h6>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted" style="font-size: 12px;">Minat Bakat: <?php echo number_format($statistik['minat_bakat'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Prestasi: <?php echo number_format($statistik['prestasi'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Keaktifan Ekstrakurikuler: <?php echo number_format($statistik['keaktifan_ekskul'] ?? 0, 1); ?>%</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sosial -->
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background-color: rgba(218, 119, 86, 0.1);">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-people me-2" style="color: rgb(206, 100, 65);"></i>
                                                <h6 class="mb-0">Sosial (<?php echo number_format($sosial, 1); ?>%)</h6>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted" style="font-size: 12px;">Partisipasi Sosial: <?php echo number_format($statistik['partisipasi_sosial'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Empati: <?php echo number_format($statistik['empati'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Kerja Sama: <?php echo number_format($statistik['kerja_sama'] ?? 0, 1); ?>%</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kesehatan -->
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background-color: rgba(218, 119, 86, 0.1);">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-heart-pulse me-2" style="color: rgb(206, 100, 65);"></i>
                                                <h6 class="mb-0">Kesehatan (<?php echo number_format($kesehatan, 1); ?>%)</h6>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted" style="font-size: 12px;">Kebersihan Diri: <?php echo number_format($statistik['kebersihan_diri'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Aktivitas Fisik: <?php echo number_format($statistik['aktivitas_fisik'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Pola Makan: <?php echo number_format($statistik['pola_makan'] ?? 0, 1); ?>%</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Karakter -->
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background-color: rgba(218, 119, 86, 0.1);">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-star me-2" style="color: rgb(206, 100, 65);"></i>
                                                <h6 class="mb-0">Karakter (<?php echo number_format($karakter, 1); ?>%)</h6>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted" style="font-size: 12px;">Kejujuran: <?php echo number_format($statistik['kejujuran'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Tanggung Jawab: <?php echo number_format($statistik['tanggung_jawab'] ?? 0, 1); ?>%</small>
                                                <br>
                                                <small class="text-muted" style="font-size: 12px;">Kedisiplinan: <?php echo number_format($statistik['kedisiplinan'] ?? 0, 1); ?>%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAB for mobile
                    <div class=" floating-action-button position-fixed bottom-0 end-0 m-3 d-md-none">
                        <button class="btn btn-lg rounded-circle shadow color-web" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileInput" aria-controls="mobileInput">
                            <i class="bi bi-pencil-square text-white"></i>
                        </button>
                    </div>

                    <style>
                    .floating-action-button {
                        position: fixed;
                        bottom: 60px !important;
                        right: 30px;
                        text-align: right;
                    }

                    </style> -->

                                    <!-- Floating Action Button -->
                <div class="floating-action-button d-block d-md-none">
                    <!-- Main FAB -->
                    <button class="btn btn-lg main-fab rounded-circle shadow" id="mainFab" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileInput" aria-controls="mobileInput">
                        <i class="bi bi-pencil-square"></i>
                    </button>                    
                </div>

                <style>
                /* Floating Action Button Styling */
                .floating-action-button {
                        position: fixed;
                        bottom: 70px !important;
                        right: 30px;
                        text-align: right;
                    }

                .main-fab {
                    width: 56px;
                    height: 56px;
                    background: #da7756;
                    color: white;
                    transition: transform 0.3s;
                }

                .main-fab:hover {
                    background: #c56647;
                    color: white;
                }
                </style>



                    <!-- Offcanvas for mobile input -->
                    <div class="offcanvas offcanvas-bottom h-75 d-md-none" tabindex="-1" id="mobileInput">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title">Input Statistik</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <!-- Copy the entire form content here but remove the card wrapper -->
                            <form action="pg_statistik.php" method="POST">
                                    <?php if(isset($_GET['success'])): ?>
                                    <div class="alert alert-success py-2 px-3 mb-3" role="alert" style="font-size: 14px;">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle me-2"></i>
                                            <span>Data berhasil disimpan</span>
                                            <button type="button" class="btn-close btn-close-sm ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if(isset($_GET['error'])): ?>
                                    <div class="alert alert-danger py-2 px-3 mb-3" role="alert" style="font-size: 14px;">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-exclamation-circle me-2"></i>
                                            <span>Gagal menyimpan data</span>
                                            <button type="button" class="btn-close btn-close-sm ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    </div>
                                    <?php endif; ?>


                                <div class="card-body">
                                <input type="hidden" name="siswa_id" value="<?= $siswa_id ?>">
                                <input type="hidden" name="semester" value="<?= $selected_semester ?>">
                                <input type="hidden" name="tahun_ajaran" value="<?= $selected_tahun_ajaran ?>">
                                <input type="hidden" name="nilai_akademik" value="<?= $statistik['nilai_akademik'] ?? '' ?>">
                                <input type="hidden" name="keaktifan" value="<?= $statistik['keaktifan'] ?? '' ?>">
                                <input type="hidden" name="pemahaman" value="<?= $statistik['pemahaman'] ?? '' ?>">
                                <input type="hidden" name="kehadiran_ibadah" value="<?= $statistik['kehadiran_ibadah'] ?? '' ?>">
                                <input type="hidden" name="kualitas_ibadah" value="<?= $statistik['kualitas_ibadah'] ?? '' ?>">
                                <input type="hidden" name="pemahaman_agama" value="<?= $statistik['pemahaman_agama'] ?? '' ?>">
                                <input type="hidden" name="minat_bakat" value="<?= $statistik['minat_bakat'] ?? '' ?>">
                                <input type="hidden" name="prestasi" value="<?= $statistik['prestasi'] ?? '' ?>">
                                <input type="hidden" name="keaktifan_eskul" value="<?= $statistik['keaktifan_eskul'] ?? '' ?>">
                                <input type="hidden" name="partisipasi_sosial" value="<?= $statistik['partisipasi_sosial'] ?? '' ?>">
                                <input type="hidden" name="empati" value="<?= $statistik['empati'] ?? '' ?>">
                                <input type="hidden" name="kerja_sama" value="<?= $statistik['kerja_sama'] ?? '' ?>">
                                <input type="hidden" name="kebersihan_diri" value="<?= $statistik['kebersihan_diri'] ?? '' ?>">
                                <input type="hidden" name="aktivitas_fisik" value="<?= $statistik['aktivitas_fisik'] ?? '' ?>">
                                <input type="hidden" name="pola_makan" value="<?= $statistik['pola_makan'] ?? '' ?>">
                                <input type="hidden" name="kejujuran" value="<?= $statistik['kejujuran'] ?? '' ?>">
                                <input type="hidden" name="tanggung_jawab" value="<?= $statistik['tanggung_jawab'] ?? '' ?>">
                                <input type="hidden" name="kedisiplinan" value="<?= $statistik['kedisiplinan'] ?? '' ?>">
                                <input type="hidden" name="nama" value="<?= $siswa['nama'] ?? '' ?>">
                                <div class="accordion" id="statistikAccordion">

                                    <!-- Identitas Siswa -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#identitas" style="font-size: 12px;">
                                                <i class="bi bi-person me-2"></i> Profil Siswa
                                            </button>
                                        </h2>
                                        <div id="identitas" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">NIS</label>
                                                    <input type="number" name="nis" class="form-control form-control-sm" value="<?php echo $current_student['nis'] ?? ''; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Nama Lengkap</label>
                                                    <input type="text" name="nama" class="form-control form-control-sm" value="<?php echo $current_student['nama'] ?? ''; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Tahun Masuk</label>
                                                    <input type="number" name="tahun_masuk" class="form-control form-control-sm" value="<?php echo $current_student['tahun_masuk'] ?? ''; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Nomor HP</label>
                                                    <input type="number" name="no_hp" class="form-control form-control-sm" value="<?php echo $current_student['no_hp'] ?? ''; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Alamat</label>
                                                    <textarea name="alamat" class="form-control form-control-sm" rows="2"><?php echo $current_student['alamat'] ?? ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pendampingan Belajar -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#belajar" style="font-size: 12px;">
                                                <i class="bi bi-book me-2"></i> Pendampingan Belajar
                                            </button>
                                        </h2>
                                        <div id="belajar" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Nilai Akademik (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Hasil evaluasi pembelajaran dalam bentuk nilai/skor</p>
                                                    <input type="number" name="nilai_akademik" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['nilai_akademik']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Keaktifan (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Tingkat partisipasi siswa dalam proses pembelajaran maupun di luar pembelajaran</p>
                                                    <input type="number" name="keaktifan" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['keaktifan']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Pemahaman (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kemampuan menangkap dan menerapkan materi yang dipelajari</p>
                                                    <input type="number" name="pemahaman" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['pemahaman']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pendampingan Ibadah -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ibadah" style="font-size: 12px;">
                                                <i class="bi bi-circle me-2"></i> Pendampingan Ibadah
                                            </button>
                                        </h2>
                                        <div id="ibadah" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Kehadiran Ibadah (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Tingkat kedisiplinan dalam kegiatan ibadah wajib/sunah seperti shalat dhuha maupun kegiatan religius lainya</p>
                                                    <input type="number" name="kehadiran_ibadah" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kehadiran_ibadah']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label  p-0 m-0" style="font-size: 12px;">Kualitas Ibadah (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kesesuaian dan ketertiban di dalam pelaksanaan tata cara ibadah</p>
                                                    <input type="number" name="kualitas_ibadah" class="form-control form-control-sm" min="0" max="100"  placeholder="<?php echo $nilai['kualitas_ibadah']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" style="font-size: 12px;">Pemahaman Agama (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Pengetahuan tentang dasar-dasar agama dan penerapannya menurut Tarjih Muhammadiyah</p>
                                                    <input type="number" name="pemahaman_agama" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['pemahaman_agama']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pengembangan Diri -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pengembangan" style="font-size: 12px;">
                                                <i class="bi bi-person-plus me-2"></i> Pengembangan Diri
                                            </button>
                                        </h2>
                                        <div id="pengembangan" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Minat Bakat (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Potensi dan ketertarikan dalam bidang yang diminati siswa</p>
                                                    <input type="number" name="minat_bakat" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['minat_bakat']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Prestasi (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Pencapaian siswa dalam berbagai bidang akademik/non-akademik</p>
                                                    <input type="number" name="prestasi" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['prestasi']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Keaktifan Ekstrakurikuler (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Partisipasi dan keaktifan siswa dalam kegiatan ekstrakurikuler</p>
                                                    <input type="number" name="keaktifan_ekskul" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['keaktifan_ekskul']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sosial -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sosial" style="font-size: 12px;">
                                                <i class="bi bi-people me-2"></i> Sosial Kemasyarakatan
                                            </button>
                                        </h2>
                                        <div id="sosial" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Partisipasi Kegiatan Sosial (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Keterlibatan dalam aktivitas kemasyarakatan dalam lingkugan sekolah maupun masyarakat</p>
                                                    <input type="number" name="partisipasi_sosial" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['partisipasi_sosial']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Empati (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kepekaan terhadap kondisi dan kebutuhan orang lain</p>
                                                    <input type="number" name="empati" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['empati']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Kerja Sama (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kemampuan berkolaborasi dalam kelompok</p>
                                                    <input type="number" name="kerja_sama" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kerja_sama']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kesehatan -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#kesehatan" style="font-size: 12px;">
                                                <i class="bi bi-heart-pulse me-2"></i> Kesehatan
                                            </button>
                                        </h2>
                                        <div id="kesehatan" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Kebersihan Diri (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Perawatan kebersihan dan kerapihan tubuh dan lingkungan</p>
                                                    <input type="number" name="kebersihan_diri" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kebersihan_diri']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Aktivitas Fisik (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Keterlibatan dalam kegiatan olahraga/gerak badan</p>
                                                    <input type="number" name="aktivitas_fisik" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['aktivitas_fisik']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Pola Makan (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Keteraturan dan kualitas asupan makanan</p>
                                                    <input type="number" name="pola_makan" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['pola_makan']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Karakter -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#karakter" style="font-size: 12px;">
                                                <i class="bi bi-star me-2"></i> Karakter
                                            </button>
                                        </h2>
                                        <div id="karakter" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Kejujuran (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;"> Keselarasan antara ucapan dan tindakan</p>
                                                    <input type="number" name="kejujuran" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kejujuran']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Tanggung Jawab (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kesediaan menyelesaikan tugas dan kewajiban</p>
                                                    <input type="number" name="tanggung_jawab" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['tanggung_jawab']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Disiplin (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Ketaatan terhadap aturan dan jadwal yang ditetapkan</p>
                                                    <input type="number" name="kedisiplinan" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kedisiplinan']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 d-grid">
                                    <button type="submit" class="btn" style="background-color: rgb(206, 100, 65); color: white;">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>



                    <!-- Original desktop version with d-none d-md-block -->
                    <div class="col-md-3 d-none d-md-block">
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">Input Statistik</h6>
                                </div>
                                <div class="px-3 pt-3">
                                    <?php if(isset($_GET['success'])): ?>
                                    <div class="alert alert-success py-2 px-3 mb-3" role="alert" style="font-size: 14px;">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle me-2"></i>
                                            <span>Data berhasil disimpan</span>
                                            <button type="button" class="btn-close btn-close-sm ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if(isset($_GET['error'])): ?>
                                    <div class="alert alert-danger py-2 px-3 mb-3" role="alert" style="font-size: 14px;">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-exclamation-circle me-2"></i>
                                            <span>Gagal menyimpan data</span>
                                            <button type="button" class="btn-close btn-close-sm ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body">
                                <form action="pg_statistik.php" method="POST">
                                <input type="hidden" name="siswa_id" value="<?= $siswa_id ?>">
                                <input type="hidden" name="semester" value="<?= $selected_semester ?>">
                                <input type="hidden" name="tahun_ajaran" value="<?= $selected_tahun_ajaran ?>">
                                <input type="hidden" name="nilai_akademik" value="<?= $statistik['nilai_akademik'] ?? '' ?>">
                                <input type="hidden" name="keaktifan" value="<?= $statistik['keaktifan'] ?? '' ?>">
                                <input type="hidden" name="pemahaman" value="<?= $statistik['pemahaman'] ?? '' ?>">
                                <input type="hidden" name="kehadiran_ibadah" value="<?= $statistik['kehadiran_ibadah'] ?? '' ?>">
                                <input type="hidden" name="kualitas_ibadah" value="<?= $statistik['kualitas_ibadah'] ?? '' ?>">
                                <input type="hidden" name="pemahaman_agama" value="<?= $statistik['pemahaman_agama'] ?? '' ?>">
                                <input type="hidden" name="minat_bakat" value="<?= $statistik['minat_bakat'] ?? '' ?>">
                                <input type="hidden" name="prestasi" value="<?= $statistik['prestasi'] ?? '' ?>">
                                <input type="hidden" name="keaktifan_eskul" value="<?= $statistik['keaktifan_eskul'] ?? '' ?>">
                                <input type="hidden" name="partisipasi_sosial" value="<?= $statistik['partisipasi_sosial'] ?? '' ?>">
                                <input type="hidden" name="empati" value="<?= $statistik['empati'] ?? '' ?>">
                                <input type="hidden" name="kerja_sama" value="<?= $statistik['kerja_sama'] ?? '' ?>">
                                <input type="hidden" name="kebersihan_diri" value="<?= $statistik['kebersihan_diri'] ?? '' ?>">
                                <input type="hidden" name="aktivitas_fisik" value="<?= $statistik['aktivitas_fisik'] ?? '' ?>">
                                <input type="hidden" name="pola_makan" value="<?= $statistik['pola_makan'] ?? '' ?>">
                                <input type="hidden" name="kejujuran" value="<?= $statistik['kejujuran'] ?? '' ?>">
                                <input type="hidden" name="tanggung_jawab" value="<?= $statistik['tanggung_jawab'] ?? '' ?>">
                                <input type="hidden" name="kedisiplinan" value="<?= $statistik['kedisiplinan'] ?? '' ?>">
                                <input type="hidden" name="nama" value="<?= $siswa['nama'] ?? '' ?>">

                                
                                <div class="accordion" id="statistikAccordion">

                                    <!-- Identitas Siswa -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#identitas" style="font-size: 12px;">
                                                <i class="bi bi-person me-2"></i> Profil Siswa
                                            </button>
                                        </h2>
                                        <div id="identitas" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">NIS</label>
                                                    <input type="number" name="nis" class="form-control form-control-sm" value="<?php echo $current_student['nis'] ?? ''; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Nama Lengkap</label>
                                                    <input type="text" name="nama" class="form-control form-control-sm" value="<?php echo $current_student['nama'] ?? ''; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Tahun Masuk</label>
                                                    <input type="number" name="tahun_masuk" class="form-control form-control-sm" value="<?php echo $current_student['tahun_masuk'] ?? ''; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Nomor HP</label>
                                                    <input type="number" name="no_hp" class="form-control form-control-sm" value="<?php echo $current_student['no_hp'] ?? ''; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Alamat</label>
                                                    <textarea name="alamat" class="form-control form-control-sm" rows="2"><?php echo $current_student['alamat'] ?? ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pendampingan Belajar -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#belajar" style="font-size: 12px;">
                                                <i class="bi bi-book me-2"></i> Pendampingan Belajar
                                            </button>
                                        </h2>
                                        <div id="belajar" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Nilai Akademik (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Hasil evaluasi pembelajaran dalam bentuk nilai/skor</p>
                                                    <input type="number" name="nilai_akademik" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['nilai_akademik']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Keaktifan (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Tingkat partisipasi siswa dalam proses pembelajaran maupun di luar pembelajaran</p>
                                                    <input type="number" name="keaktifan" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['keaktifan']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Pemahaman (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kemampuan menangkap dan menerapkan materi yang dipelajari</p>
                                                    <input type="number" name="pemahaman" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['pemahaman']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pendampingan Ibadah -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ibadah" style="font-size: 12px;">
                                                <i class="bi bi-circle me-2"></i> Pendampingan Ibadah
                                            </button>
                                        </h2>
                                        <div id="ibadah" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Kehadiran Ibadah (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Tingkat kedisiplinan dalam kegiatan ibadah wajib/sunah seperti shalat dhuha maupun kegiatan religius lainya</p>
                                                    <input type="number" name="kehadiran_ibadah" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kehadiran_ibadah']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label  p-0 m-0" style="font-size: 12px;">Kualitas Ibadah (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kesesuaian dan ketertiban di dalam pelaksanaan tata cara ibadah</p>
                                                    <input type="number" name="kualitas_ibadah" class="form-control form-control-sm" min="0" max="100"  placeholder="<?php echo $nilai['kualitas_ibadah']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" style="font-size: 12px;">Pemahaman Agama (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Pengetahuan tentang dasar-dasar agama dan penerapannya menurut Tarjih Muhammadiyah</p>
                                                    <input type="number" name="pemahaman_agama" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['pemahaman_agama']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pengembangan Diri -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pengembangan" style="font-size: 12px;">
                                                <i class="bi bi-person-plus me-2"></i> Pengembangan Diri
                                            </button>
                                        </h2>
                                        <div id="pengembangan" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Minat Bakat (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Potensi dan ketertarikan dalam bidang yang diminati siswa</p>
                                                    <input type="number" name="minat_bakat" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['minat_bakat']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Prestasi (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Pencapaian siswa dalam berbagai bidang akademik/non-akademik</p>
                                                    <input type="number" name="prestasi" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['prestasi']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Keaktifan Ekstrakurikuler (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Partisipasi dan keaktifan siswa dalam kegiatan ekstrakurikuler</p>
                                                    <input type="number" name="keaktifan_ekskul" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['keaktifan_ekskul']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sosial -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sosial" style="font-size: 12px;">
                                                <i class="bi bi-people me-2"></i> Sosial Kemasyarakatan
                                            </button>
                                        </h2>
                                        <div id="sosial" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Partisipasi Kegiatan Sosial (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Keterlibatan dalam aktivitas kemasyarakatan dalam lingkugan sekolah maupun masyarakat</p>
                                                    <input type="number" name="partisipasi_sosial" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['partisipasi_sosial']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Empati (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kepekaan terhadap kondisi dan kebutuhan orang lain</p>
                                                    <input type="number" name="empati" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['empati']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Kerja Sama (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kemampuan berkolaborasi dalam kelompok</p>
                                                    <input type="number" name="kerja_sama" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kerja_sama']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kesehatan -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#kesehatan" style="font-size: 12px;">
                                                <i class="bi bi-heart-pulse me-2"></i> Kesehatan
                                            </button>
                                        </h2>
                                        <div id="kesehatan" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Kebersihan Diri (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Perawatan kebersihan dan kerapihan tubuh dan lingkungan</p>
                                                    <input type="number" name="kebersihan_diri" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kebersihan_diri']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Aktivitas Fisik (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Keterlibatan dalam kegiatan olahraga/gerak badan</p>
                                                    <input type="number" name="aktivitas_fisik" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['aktivitas_fisik']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Pola Makan (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Keteraturan dan kualitas asupan makanan</p>
                                                    <input type="number" name="pola_makan" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['pola_makan']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Karakter -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#karakter" style="font-size: 12px;">
                                                <i class="bi bi-star me-2"></i> Karakter
                                            </button>
                                        </h2>
                                        <div id="karakter" class="accordion-collapse collapse" data-bs-parent="#statistikAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Kejujuran (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;"> Keselarasan antara ucapan dan tindakan</p>
                                                    <input type="number" name="kejujuran" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kejujuran']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Tanggung Jawab (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Kesediaan menyelesaikan tugas dan kewajiban</p>
                                                    <input type="number" name="tanggung_jawab" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['tanggung_jawab']?? "Belum ada data"; ?>">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label p-0 m-0" style="font-size: 12px;">Disiplin (%)</label>
                                                    <p class="text-muted p-0 m-0 mb-1" style="font-size: 10px;">Ketaatan terhadap aturan dan jadwal yang ditetapkan</p>
                                                    <input type="number" name="kedisiplinan" class="form-control form-control-sm" min="0" max="100" placeholder="<?php echo $nilai['kedisiplinan']?? "Belum ada data"; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 d-grid">
                                    <button type="submit" class="btn" style="background-color: rgb(206, 100, 65); color: white;">Simpan</button>
                                </div>
                                </form>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-3">

                    </div>


                </div>
            </div>

            <!-- style accordion -->
            <style>
                            .accordion-button {
                                background-color: #ffffff;
                                border: none;
                                box-shadow: none;
                                padding: 0.8rem 1.2rem;
                                font-weight: 500;
                                color: black;
                                transition: all 0.3s ease;
                            }

                            .accordion-button:not(.collapsed) {
                                background-color: rgb(218, 119, 86);
                                color: white;
                            }

                            .accordion-button:focus {
                                box-shadow: none;
                                border-color: rgba(206, 206, 206, 0.2);
                            }

                            .accordion-button:not(.collapsed)::after {
                                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
                            }

                            .accordion-button:hover {
                                background-color: rgba(218, 119, 86, 0.1);
                            }

                            .accordion-button:not(.collapsed):hover {
                                background-color: rgb(206, 100, 65);
                            }

                            .accordion-item {
                                margin-bottom: 0.5rem;
                                border-radius: 0.5rem !important;
                                overflow: hidden;
                                box-shadow: none;
                            }

                            .accordion-body {
                                box-shadow: none;
                                padding: 1rem;
                            }
                            </style>


</body>
</html>