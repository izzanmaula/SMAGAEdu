<?php
session_start();
require "koneksi.php";


if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

// Ambil userid dari session
$userid = $_SESSION['userid'];

// Get exams for classes the student is enrolled in
$query = "SELECT u.*, k.mata_pelajaran, k.tingkat 
          FROM ujian u
          JOIN kelas k ON u.kelas_id = k.id 
          JOIN kelas_siswa ks ON k.id = ks.kelas_id
          JOIN siswa s ON ks.siswa_id = s.id
          WHERE s.username = '$userid'

          ORDER BY u.tanggal_mulai ASC";

$result = mysqli_query($koneksi, $query);




// function untuk waktu ujian
function getExamStatus($startTime, $endTime) {
    date_default_timezone_set('Asia/Jakarta');
    $now = time();
    $start = strtotime($startTime);
    $end = strtotime($endTime);
    
    if ($now < $start) {
        $diffSeconds = $start - $now;
        $hours = floor($diffSeconds / 3600);
        $minutes = floor(($diffSeconds % 3600) / 60);
        return [
            'status' => 'waiting',
            'countdown' => "$hours jam $minutes menit"
        ];
    } elseif ($now >= $start && $now <= $end) {
        return [
            'status' => 'ongoing',
            'countdown' => ''
        ];
    } else {
        return [
            'status' => 'ended',
            'countdown' => ''
        ];
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
                            <div class="d-flex align-items-center rounded  p-2">
                                <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Beranda</p>
                            </div>
                        </a>
                        
                        
                        <!-- Menu Ujian -->
                        <a href="ujian.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center color-web rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Ujian</p>
                            </div>
                        </a>

                        <!-- Menu ai -->
                        <a href="ai.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center  rounded p-2">
                                <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">SMAGA AI</p>
                            </div>
                        </a>

                        
                        <!-- Menu Profil -->
                        <a href="profil.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
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
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/beranda_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Beranda</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="ujian.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center  bg-white shadow-sm rounded p-2" style="">
                            <img src="assets/ujian_fill.png" alt="" width="50px" class="pe-4">
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
<!-- Isi konten -->
<div class="col p-4 col-utama mt-1 mt-md-0">
    <div class="row justify-content-between align-items-center mb-1">
        <div class="col-12 col-md-auto mb-3 mb-md-0">
            <h3 style="font-weight: bold;">Ujian</h3>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 m-0">
    <?php if(mysqli_num_rows($result) > 0): 
        while($ujian = mysqli_fetch_assoc($result)):
        // Get teacher data inside the loop
            $guru_id = $ujian['guru_id'];
            $query_guru = "SELECT foto_profil FROM guru WHERE username = '$guru_id'";
            $result_guru = mysqli_query($koneksi, $query_guru);
            $data_guru = mysqli_fetch_assoc($result_guru);
    ?>
        <div class="col ps-0">
            <div class="custom-card w-100">
                <img src="assets/bg.jpg" alt="Background Image" class="card-img-top">
                
                <div class="card-body" style="text-align: right; padding-right: 30px; background-color: white;">
                <img src="<?php echo !empty($data_guru['foto_profil']) ? 'uploads/profil/'.$data_guru['foto_profil'] : 'assets/pp.png'; ?>" 
                    alt="Profile Image" 
                    class="profile-img rounded-4 border-0 bg-white">
                </div>

                <div class="ps-3">
                    <h5 class="mt-3 p-0 mb-1" style="font-weight: bold; font-size: 20px;">
                        <?php echo htmlspecialchars($ujian['mata_pelajaran']); ?>
                    </h5>
                    <p class="p-0 m-0" style="font-size: 12px;">
                        <?php echo date('d/m/Y H:i', strtotime($ujian['tanggal_mulai'])); ?>
                        (<?php echo $ujian['durasi']; ?> menit)
                    </p>
                </div>

                <div class="d-flex btn-group gap-2 p-3">
                    <?php 
                    $examStatus = getExamStatus($ujian['tanggal_mulai'], $ujian['tanggal_selesai']);
                    if ($examStatus['status'] === 'ongoing'): ?>
                        <a href="mulai_ujian.php?id=<?php echo $ujian['id']; ?>" 
                           class="btn color-web w-100 rounded text-white">
                            Mulai Ujian
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100 rounded" disabled>
                            <?php echo $examStatus['status'] === 'waiting' ? $examStatus['countdown'] : 'Ujian Selesai'; ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; 
    else: ?>
        <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
            <p class="text-muted">Tidak ada ujian yang tersedia saat ini.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>