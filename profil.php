<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

$userid = $_SESSION['userid'];
$query = "SELECT s.*,
    k.nama_kelas AS kelas_saat_ini,
    COALESCE(AVG(pg.nilai_akademik), 0) as nilai_akademik,
    COALESCE(AVG(pg.keaktifan), 0) as keaktifan,
    COALESCE(AVG(pg.pemahaman), 0) as pemahaman,
    COALESCE(AVG(pg.kehadiran_ibadah), 0) as kehadiran_ibadah,
    COALESCE(AVG(pg.kualitas_ibadah), 0) as kualitas_ibadah,
    COALESCE(AVG(pg.pemahaman_agama), 0) as pemahaman_agama,
    COALESCE(AVG(pg.minat_bakat), 0) as minat_bakat,
    COALESCE(AVG(pg.prestasi), 0) as prestasi,
    COALESCE(AVG(pg.keaktifan_ekskul), 0) as keaktifan_ekskul,
    COALESCE(AVG(pg.partisipasi_sosial), 0) as partisipasi_sosial,
    COALESCE(AVG(pg.empati), 0) as empati,
    COALESCE(AVG(pg.kerja_sama), 0) as kerja_sama,
    COALESCE(AVG(pg.kebersihan_diri), 0) as kebersihan_diri,
    COALESCE(AVG(pg.aktivitas_fisik), 0) as aktivitas_fisik,
    COALESCE(AVG(pg.pola_makan), 0) as pola_makan,
    COALESCE(AVG(pg.kejujuran), 0) as kejujuran,
    COALESCE(AVG(pg.tanggung_jawab), 0) as tanggung_jawab,
    COALESCE(AVG(pg.kedisiplinan), 0) as kedisiplinan
    FROM siswa s 
    LEFT JOIN kelas_siswa ks ON s.id = ks.siswa_id 
    LEFT JOIN kelas k ON ks.kelas_id = k.id 
    LEFT JOIN pg ON s.id = pg.siswa_id 
    WHERE s.username = ?
    GROUP BY s.id, k.nama_kelas";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);

// Function to get grade label and class
function getNilaiLabel($value) {
    if ($value >= 80) return ['Baik', 'text-success'];
    if ($value >= 60) return ['Cukup', 'text-warning'];
    return ['Kurang', 'text-danger'];
}

// Calculate category averages
function calculateCategoryAverage($values) {
    $validValues = array_filter($values, function($v) { return $v !== null; });
    return empty($validValues) ? 0 : round(array_sum($validValues) / count($validValues));
}

// Get category values
$belajar = calculateCategoryAverage([
    $siswa['nilai_akademik'],
    $siswa['keaktifan'],
    $siswa['pemahaman']
]);

$ibadah = calculateCategoryAverage([
    $siswa['kehadiran_ibadah'],
    $siswa['kualitas_ibadah'],
    $siswa['pemahaman_agama']
]);

$pengembangan = calculateCategoryAverage([
    $siswa['minat_bakat'],
    $siswa['prestasi'],
    $siswa['keaktifan_ekskul']
]);

$sosial = calculateCategoryAverage([
    $siswa['partisipasi_sosial'],
    $siswa['empati'],
    $siswa['kerja_sama']
]);

$kesehatan = calculateCategoryAverage([
    $siswa['kebersihan_diri'],
    $siswa['aktivitas_fisik'],
    $siswa['pola_makan']
]);

$karakter = calculateCategoryAverage([
    $siswa['kejujuran'],
    $siswa['tanggung_jawab'],
    $siswa['kedisiplinan']
]);

// Get grade
function getGrade($value) {
    if ($value >= 80) return ['Baik', 'text-success'];
    if ($value >= 60) return ['Cukup', 'text-warning'];
    return ['Kurang', 'text-danger'];
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
                    <h1 class="p-0 m-0" style="font-size: 20px;">Profil</h1>
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
                <div class="offcanvas-body d-flex justify-content-between flex-column">
                    <div class="d-flex flex-column gap-2">
                        <!-- Menu Beranda -->
                        <a href="beranda.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded  p-2">
                                <img src="assets/beranda_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Beranda</p>
                            </div>
                        </a>
                        
                        
                        <!-- Menu Ujian -->
                        <a href="ujian.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center  rounded p-2">
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
                                <img src="assets/profil_fill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Profil</p>
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
                <div class="mt-3 dropup"> <!-- Tambahkan class dropdown di sini -->
                    <button class="btn d-flex align-items-center gap-3 p-2 rounded-3 border w-100" 
                            style="background-color:rgb(255, 252, 248);" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                            <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp-siswa.png'; ?>" 
                                    alt="Profile Picture" 
                                    class="rounded-circle" 
                                    style="width: 25px; height: 25px;object-fit: cover; z-index: 99999;">
                            <p class="p-0 m-0 text-truncate" style="font-size: 12px;">Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?></p>
                    </button>
                    <ul class="dropdown-menu w-100" style="font-size: 12px;"> <!-- Tambahkan w-100 agar lebar sama -->
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="logout.php" style="color: red;">Keluar</a></li>
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
                        <div class="d-flex align-items-center rounded p-2" style="">
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
                        <img src="<?php echo $siswa['foto_profil'] ? $siswa['foto_profil'] : 'assets/pp-siswa.png'; ?>" 
                                    alt="Profile Picture" 
                                    class="rounded-circle" 
                                    style="width: 30px; height: 30px;object-fit: cover; z-index: 99999;">
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
    <div class="row g-4">
        <!-- Kolom Kiri (Profil dan Nilai) -->
        <div class="col-md-8">
            <div class="row mb-4">
                <!-- Profile Card -->
                <div class="col-12">
                    <div class="profile-header rounded-4 p-4" style="background-color: rgb(218, 119, 86);">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center">
                                <img src="<?php echo !empty($siswa['foto_profil']) ? 'uploads/profil/'.$siswa['foto_profil'] : 'assets/pp.png'; ?>" 
                                    class="rounded-circle" width="100" height="100" style="object-fit: cover;">
                            </div>
                            <div class="col-md-9 text-white mt-4 mt-md-0">
                                <h2 class="fw-bold mb-1"><?php echo htmlspecialchars($siswa['nama']); ?></h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><i class="bi bi-person-badge me-2"></i>NIS: <?php echo htmlspecialchars($siswa['nis']); ?></p>
                                        <p class="mb-1"><i class="bi bi-mortarboard me-2"></i>Kelas/Fase <?php echo htmlspecialchars($siswa['tingkat']); ?></p>
                                        <p class="mb-1"><i class="bi bi-calendar me-2"></i>Tahun Masuk: <?php echo htmlspecialchars($siswa['tahun_masuk']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($siswa['no_hp']); ?></p>
                                        <p class="mb-1"><i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars($siswa['alamat']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- grafik chart -->
            <!-- Dropdown and Charts Container -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="border rounded-4 p-4 shadow-sm">
                        <div style="height: 200px">
                            <canvas id="barChart"></canvas>
                        </div>

                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                        const ctx = document.getElementById('barChart').getContext('2d');
                        const barChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Belajar', 'Ibadah', 'Pengembangan', 'Sosial', 'Kesehatan', 'Karakter'],
                                datasets: [{
                                    data: [
                                        <?php echo $belajar; ?>,
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
                    </div>
                </div>
            </div>

<!-- Content Grid -->
<div class="row g-4">
    <!-- Behavior & Character -->
    <div class="col-md-4">
        <div class="border rounded-4 p-4 shadow-sm">
            <h5 class="mb-4" style="font-size: 14px;"><i class="bi bi-mortarboard-fill text-primary"></i> Pendampingan</h5>
            <div class="mb-4">
                <div class="mb-2">
                    <h6 class="m-0">Belajar</h6>
                    <?php list($label, $class) = getGrade($belajar); ?>
                    <span class="p-0 m-0 badge <?= $class ?>"><?= $label ?> (<?= $belajar ?>%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar color-web" style="width: <?= $belajar ?>%"></div>
                </div>
                <div class="mt-2 text-muted small" style="font-size: 12px;">
                    <div class="d-flex justify-content-between">
                        <span>Nilai Akademik</span>
                        <span><?= round($siswa['nilai_akademik']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Keaktifan</span>
                        <span><?= round($siswa['keaktifan']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Pemahaman</span>
                        <span><?= round($siswa['pemahaman']) ?>%</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="mb-2">
                    <h6 class="m-0">Ibadah</h6>
                    <?php list($label, $class) = getGrade($ibadah); ?>
                    <span class="badge p-0 m-0 <?= $class ?>"><?= $label ?> (<?= $ibadah ?>%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar color-web" style="width: <?= $ibadah ?>%"></div>
                </div>
                <div class="mt-2 text-muted small" style="font-size: 12px;">
                    <div class="d-flex justify-content-between">
                        <span>Kehadiran Ibadah</span>
                        <span><?= round($siswa['kehadiran_ibadah']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Kualitas Ibadah</span>
                        <span><?= round($siswa['kualitas_ibadah']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Pemahaman Agama</span>
                        <span><?= round($siswa['pemahaman_agama']) ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="border rounded-4 p-4 shadow-sm">
            <h5 class="mb-4" style="font-size: 14px;"><i class="bi bi-graph-up-arrow text-success"></i> Pengembangan</h5>
            <div class="mb-4">
                <div class="mb-2">
                    <h6 class="m-0">Pengembangan Diri</h6>
                    <?php list($label, $class) = getGrade($pengembangan); ?>
                    <span class="badge p-0 m-0 <?= $class ?>"><?= $label ?> (<?= $pengembangan ?>%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar color-web" style="width: <?= $pengembangan ?>%"></div>
                </div>
                <div class="mt-2 text-muted small" style="font-size: 12px;">
                    <div class="d-flex justify-content-between">
                        <span>Minat Bakat</span>
                        <span><?= round($siswa['minat_bakat']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Prestasi</span>
                        <span><?= round($siswa['prestasi']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Keaktifan Ekstrakurikuler</span>
                        <span><?= round($siswa['keaktifan_ekskul']) ?>%</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="mb-2">
                    <h6 class="m-0">Sosial</h6>
                    <?php list($label, $class) = getGrade($sosial); ?>
                    <span class="badge p-0 m-0 <?= $class ?>"><?= $label ?> (<?= $sosial ?>%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar color-web" style="width: <?= $sosial ?>%"></div>
                </div>
                <div class="mt-2 text-muted small" style="font-size: 12px;">
                    <div class="d-flex justify-content-between">
                        <span>Partisipasi Sosial</span>
                        <span><?= round($siswa['partisipasi_sosial']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Empati</span>
                        <span><?= round($siswa['empati']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Kerja Sama</span>
                        <span><?= round($siswa['kerja_sama']) ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="border rounded-4 p-4 shadow-sm">
            <h5 class="mb-4" style="font-size: 14px;"><i class="bi bi-shield-check text-warning"></i> Kesehatan & Karakter</h5>
            <div class="mb-4">
                <div class="mb-2">
                    <h6 class="m-0">Kesehatan</h6>
                    <?php list($label, $class) = getGrade($kesehatan); ?>
                    <span class="badge p-0 m-0 <?= $class ?>"><?= $label ?> (<?= $kesehatan ?>%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar color-web" style="width: <?= $kesehatan ?>%"></div>
                </div>
                <div class="mt-2 text-muted small" style="font-size: 12px;">
                    <div class="d-flex justify-content-between">
                        <span>Kebersihan Diri</span>
                        <span><?= round($siswa['kebersihan_diri']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Aktivitas Fisik</span>
                        <span><?= round($siswa['aktivitas_fisik']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Pola Makan</span>
                        <span><?= round($siswa['pola_makan']) ?>%</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="mb-2">
                    <h6 class="m-0">Karakter</h6>
                    <?php list($label, $class) = getGrade($karakter); ?>
                    <span class="badge p-0 m-0 <?= $class ?>"><?= $label ?> (<?= $karakter ?>%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar color-web" style="width: <?= $karakter ?>%"></div>
                </div>
                <div class="mt-2 text-muted small" style="font-size: 12px;">
                    <div class="d-flex justify-content-between">
                        <span>Kejujuran</span>
                        <span><?= round($siswa['kejujuran']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tanggung Jawab</span>
                        <span><?= round($siswa['tanggung_jawab']) ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Kedisiplinan</span>
                        <span><?= round($siswa['kedisiplinan']) ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- Kolom Kanan (AI Assistant) -->
        <div class="col-md-4 ai-col">
            <div class="sticky-top" style="top: 20px;">
                <!-- AI Assistant Card -->
                <div class="border rounded-4 shadow-sm" style="height: 36rem;">
                    <!-- AI Header -->
                    <div class="p-3 border-bottom d-flex align-items-center gap-2">
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background-color: #da775620; width: 40px; height: 40px;">
                            <i class="bi bi-stars text-primary" style="color: #da7756 !important; font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h5 class="m-0" style="color: #da7756;">SMAGA AI</h5>
                            <small class="text-muted">Analisis Profil SMAGA AI</small>
                        </div>
                    </div>

                    <div class="p-3">
                    <?php
                    $groq_api_key = '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';
                    $http_code = 0; // Inisialisasi variabel
                    
                    if(!empty($groq_api_key)) {
                        // Format data untuk prompt
                    $categories = [
                        'Belajar' => [
                            'Nilai Akademik' => $siswa['nilai_akademik'],
                            'Keaktifan' => $siswa['keaktifan'],
                            'Pemahaman' => $siswa['pemahaman']
                        ],
                        'Ibadah' => [
                            'Kehadiran Ibadah' => $siswa['kehadiran_ibadah'],
                            'Kualitas Ibadah' => $siswa['kualitas_ibadah'],
                            'Pemahaman Agama' => $siswa['pemahaman_agama']
                        ],
                        'Pengembangan' => [
                            'Minat Bakat' => $siswa['minat_bakat'],
                            'Prestasi' => $siswa['prestasi'],
                            'Keaktifan Ekstrakurikuler' => $siswa['keaktifan_ekskul']
                        ],
                        'Sosial' => [
                            'Partisipasi Sosial' => $siswa['partisipasi_sosial'],
                            'Empati' => $siswa['empati'],
                            'Kerja Sama' => $siswa['kerja_sama']
                        ],
                        'Kesehatan' => [
                            'Kebersihan Diri' => $siswa['kebersihan_diri'],
                            'Aktivitas Fisik' => $siswa['aktivitas_fisik'],
                            'Pola Makan' => $siswa['pola_makan']
                        ],
                        'Karakter' => [
                            'Kejujuran' => $siswa['kejujuran'],
                            'Tanggung Jawab' => $siswa['tanggung_jawab'],
                            'Kedisiplinan' => $siswa['kedisiplinan']
                        ]
                    ];

                    // Modifikasi bagian prompt
                    $prompt = "Halo {$siswa['nama']}! Aku akan bantu analisis perkembanganmu di SMAGAEdu. Berikut datanya:\n\n";

                    foreach($categories as $category => $subjects) {
                        $prompt .= "- {$category}: ";
                        $prompt .= implode(', ', array_map(
                            fn($subject, $value) => "$subject (" . round($value) . "%)",
                            array_keys($subjects), 
                            $subjects
                        ));
                        $prompt .= "\n";
                    }

                    $prompt .= "\nBuatkan analisis dengan struktur:
                    1. Salam penyemangat menggunakan nama panggilan
                    2. 1 kalimat positif tentang prestasi terbaik
                    3. 2 area perlu perbaikan dengan bahasa kasual
                    4. 2 saran konkret untuk perbaikan
                    5. Kalimat penutup motivasi

                    Gunakan:
                    - Bahasa Indonesia santai seperti teman sebaya
                    - Emoji yang relevan
                    - Fokus beri saran siswa harus ngapain
                    - Kalimat pendek maksimal 10 kata
                    - Hindari istilah teknis
                    - Gunakan hashtag #BelajarBersamaSMAGAEdu";

                // eksekusi api
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => 'https://api.groq.com/openai/v1/chat/completions',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode([
                        'model' => 'mixtral-8x7b-32768',
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                        'temperature' => 0.5,
                        'max_tokens' => 700
                    ]),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $groq_api_key
                    ]
                ]);

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if($http_code == 200) {
                            $response_data = json_decode($response, true);
                            $raw_text = $response_data['choices'][0]['message']['content'];
                            $formatted_text = nl2br(htmlspecialchars($raw_text));
                            $formatted_text = preg_replace(
                                '/(Halo|Hai|Wah|Nilai|Saran|Rekomendasi|Tips|Solusi|Yuk|Ayo|Perhatian|Poin|Kesimpulan)(.*?)(:|!)/', 
                                '<strong>$1$2$3</strong>', 
                                $formatted_text
                            );
                            $formatted_text = str_replace('*', '•', $formatted_text);
                            $full_text = htmlspecialchars($response_data['choices'][0]['message']['content']);
                            $words = explode(' ', strip_tags($raw_text));
                    ?>
                        <style>
                            .ai-col {
                                animation: fadeIn 1s ease-in-out;
                            }
                            @keyframes fadeIn {
                                0% {
                                    opacity: 0;
                                }
                                100% {
                                    opacity: 1;
                                }
                            }
                            #aiResponseContainer {
                                white-space: pre-wrap;
                                padding: 1rem;
                                background-color: #fff;
                                border-radius: 0.5rem;
                                font-size: 0.9rem;
                                line-height: 1.6;
                            }
                            #aiResponseContainer strong {
                                color: #da7756;
                                font-weight: 600;
                            }
                            .ai-expand-btn {
                                background-color: #da7756;
                                color: white;
                                border: none;
                                padding: 0.5rem 1rem;
                                border-radius: 0.5rem;
                                font-size: 0.9rem;
                                transition: all 0.3s ease;
                            }
                            .ai-expand-btn:hover {
                                background-color: #c56a4d;
                            }
                        </style>

                        <div id="aiResponseContainer" style="height: 25rem; overflow-y: auto;"></div>
                        
                        <div class="mt-3 d-flex">
                            <button class="ai-expand-btn flex-fill" data-bs-toggle="modal" data-bs-target="#aiDetailModal">
                                <i class="bi bi-arrows-angle-expand"></i> Lihat Detail
                            </button>
                        </div>

                        <script>
                        (function() {
                            const container = document.getElementById('aiResponseContainer');
                            const words = <?= json_encode($words) ?>;
                            let index = 0;
                            
                            function typeWord() {
                                if(index < words.length) {
                                    container.innerHTML += (index > 0 ? ' ' : '') + words[index];
                                    container.scrollTop = container.scrollHeight;
                                    index++;
                                    setTimeout(typeWord, 50);
                                }
                            }
                            
                            typeWord();
                        })();
                        </script>
                    <?php
                        } else {
                            echo "<div class='alert alert-warning rounded-3'>
                                    <i class='bi bi-exclamation-triangle me-2'></i>
                                    Asisten sedang offline. Kode error: {$http_code}
                                  </div>";
                        }
                    } else {
                        echo "<div class='alert alert-warning rounded-3'>
                                <i class='bi bi-exclamation-triangle me-2'></i>
                                Fitur AI belum dikonfigurasi
                              </div>";
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                        <!-- Modal -->
                        <div class="modal fade" id="aiDetailModal" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header text-white" style="background-color: #c56a4d;">
                                        <h5 class="modal-title">
                                            <i class="bi bi-robot me-2"></i>
                                            Analisis Lengkap SMAGA AI
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" style="height: calc(100vh - 200px); overflow-y: auto;">
                                        <div class="p-3">
                                            <?= nl2br($full_text) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

            
</body>
</html>