<?php
session_start();
require "koneksi.php";

$ujian_id = $_GET['ujian_id'];
$siswa_id = $_GET['siswa_id'];

// Get student info
$query_siswa = "SELECT nama FROM siswa WHERE id = '$siswa_id'";
$siswa = mysqli_fetch_assoc(mysqli_query($koneksi, $query_siswa));

// Get exam details
$query_ujian = "SELECT judul, mata_pelajaran FROM ujian WHERE id = '$ujian_id'";
$ujian = mysqli_fetch_assoc(mysqli_query($koneksi, $query_ujian));

// Get questions and answers
$query_jawaban = "
    SELECT 
        bs.id as soal_id,
        bs.pertanyaan,
        bs.jawaban_a,
        bs.jawaban_b,
        bs.jawaban_c,
        bs.jawaban_d,
        UPPER(bs.jawaban_benar) as jawaban_benar,
        UPPER(ju.jawaban) as jawaban_siswa
    FROM bank_soal bs
    LEFT JOIN jawaban_ujian ju ON bs.id = ju.soal_id AND ju.siswa_id = '$siswa_id'
    WHERE bs.ujian_id = '$ujian_id'
    ORDER BY bs.id ASC
";
$result_jawaban = mysqli_query($koneksi, $query_jawaban);

// Ambil data guru
$userid = $_SESSION['userid'];
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Jawaban Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">


    <style>
        .answer-box {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }
        .correct-answer {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .wrong-answer {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .option {
            padding: 8px;
            margin: 5px 0;
            border-radius: 4px;
        }
        .selected-correct {
            background-color: #d4edda;
        }
        .selected-wrong {
            background-color: #f8d7da;
        }
        .correct-option {
            font-weight: bold;
            color: #28a745;
        }
        body {
            font-family: 'Merriweather', serif;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar Mobile -->
    <nav class="navbar navbar-dark d-md-none color-web fixed-top">
        <div class="container-fluid">
            <!-- Logo dan Nama -->
            <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="beranda_guru.php">
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
                            <div class="d-flex align-items-center color-web rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Ujian</p>
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
            <div class="col-3 col-md-2 vh-100 p-4 shadow-sm menu-samping" style="background-color:rgb(238, 236, 226)">
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
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
                            <img src="assets/ujian_fill.png" alt="" width="50px" class="pe-4">
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
                    <div class="btn d-flex align-items-center gap-3 p-2 rounded-3 border dropdown-toggle" style="background-color: #F8F8F7;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>"  width="30px" class="rounded-circle" style="background-color: white;">
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
<div class="col p-4 col-utama">
    <style>
                            .col-utama{
                        margin-left: 13rem;
                    }
                    @media (max-width: 768px) {
                            .col-utama {
                                margin-left: 0;
                                margin-top: 10px; /* Untuk memberikan space dari fixed navbar mobile */
                            }
                    }

    </style>

    <div class="container">
        <div class="card border-0">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3">
                    <div>
                        <h5 class="mb-1 text-secondary">
                            <i class="bi bi-book me-2"></i><?php echo htmlspecialchars($ujian['mata_pelajaran']); ?>
                        </h5>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="detail_hasil_ujian.php?ujian_id=<?php echo $ujian_id; ?>" 
                           class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="bi bi-printer me-1"></i>Cetak
                        </button>
                    </div>
                </div>
                <div class="pt-3">
                    <p class="mb-2">
                        <i class="bi bi-file-text"></i>
                        <strong>Ujian:</strong> <?php echo htmlspecialchars($ujian['judul']); ?>
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bi bi-person"></i>
                        <strong>Siswa:</strong> <?php echo htmlspecialchars($siswa['nama']); ?>
                    </p>
                </div>
            </div>
            <div class="card-body">
                <?php 
                $no = 1;
                while($jawaban = mysqli_fetch_assoc($result_jawaban)) {
                    $is_correct = $jawaban['jawaban_siswa'] === $jawaban['jawaban_benar'];
                    $status_class = $jawaban['jawaban_siswa'] ? ($is_correct ? 'correct-answer' : 'wrong-answer') : '';
                ?>
                    <div class="answer-box <?php echo $status_class; ?>">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Soal <?php echo $no; ?></h5>
                            <?php if($jawaban['jawaban_siswa']): ?>
                                <span class="badge <?php echo $is_correct ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $is_correct ? 'Benar' : 'Salah'; ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Tidak Dijawab</span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="mb-3"><?php echo htmlspecialchars($jawaban['pertanyaan']); ?></p>
                        
                        <?php
                        $options = ['A', 'B', 'C', 'D'];
                        foreach($options as $option) {
                            $option_key = 'jawaban_' . strtolower($option);
                            $is_selected = $jawaban['jawaban_siswa'] === $option;
                            $is_correct_option = $jawaban['jawaban_benar'] === $option;
                            
                            $option_class = '';
                            if($is_selected && $is_correct) $option_class = 'selected-correct';
                            else if($is_selected) $option_class = 'selected-wrong';
                            else if($is_correct_option) $option_class = 'correct-option';
                        ?>
                            <div class="option <?php echo $option_class; ?>">
                                <?php echo $option . '. ' . htmlspecialchars($jawaban[$option_key]); ?>
                                <?php if($is_selected): ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php endif; ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php 
                    $no++;
                } 
                ?>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>