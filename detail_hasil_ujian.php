<?php
session_start();
require "koneksi.php";

$ujian_id = $_GET['ujian_id'];

// Query informasi ujian
$query_ujian = "SELECT u.*, k.tingkat 
                FROM ujian u
                JOIN kelas k ON u.kelas_id = k.id 
                WHERE u.id = '$ujian_id'";
$result_ujian = mysqli_query($koneksi, $query_ujian);

if (!$result_ujian || mysqli_num_rows($result_ujian) == 0) {
    die("Ujian tidak ditemukan");
}

$ujian = mysqli_fetch_assoc($result_ujian);
$ujian['judul'] = $ujian['judul'] ?? 'Judul Tidak Tersedia';
$ujian['mata_pelajaran'] = $ujian['mata_pelajaran'] ?? 'Mata Pelajaran Tidak Tersedia';
$ujian['tingkat'] = $ujian['tingkat'] ?? 'Tidak Diketahui';

// Total questions query
$query_total = "SELECT COUNT(*) as total FROM bank_soal WHERE ujian_id = '$ujian_id'";
$result_total = mysqli_query($koneksi, $query_total);
$total_questions = mysqli_fetch_assoc($result_total)['total'];

// Peserta query
$query_peserta = "
    SELECT 
        s.id as siswa_id,
        s.nama,
        COUNT(DISTINCT ju.id) as attempted_questions,
        SUM(CASE WHEN ju.jawaban = bs.jawaban_benar THEN 1 ELSE 0 END) as correct_answers,
        SUM(CASE WHEN ju.jawaban != bs.jawaban_benar AND ju.jawaban IS NOT NULL THEN 1 ELSE 0 END) as wrong_answers
    FROM siswa s
    JOIN kelas_siswa ks ON s.id = ks.siswa_id
    LEFT JOIN jawaban_ujian ju ON s.id = ju.siswa_id AND ju.ujian_id = '$ujian_id'
    LEFT JOIN bank_soal bs ON bs.id = ju.soal_id
    WHERE ks.kelas_id = '{$ujian['kelas_id']}'
    GROUP BY s.id
";
$result_peserta = mysqli_query($koneksi, $query_peserta);

if (!$result_peserta || mysqli_num_rows($result_peserta) == 0) {
    die("Tidak ada peserta ujian");
}

$peserta = array();
while ($row = mysqli_fetch_assoc($result_peserta)) {
    $peserta[] = $row;
}

// Menghitung persentase nilai
// Replace the original calculation block with this:
$rata_rata = 0;
$nilai_tertinggi = 0;
$nilai_terendah = 0;

if ($total_questions > 0 && count($peserta) > 0) {
    foreach ($peserta as $p) {
        $nilai = ($p['correct_answers'] / $total_questions) * 100;
        $rata_rata += $nilai;
        $nilai_tertinggi = max($nilai_tertinggi, $nilai);
        $nilai_terendah = min($nilai_terendah == 0 ? $nilai : $nilai_terendah, $nilai);
    }
    $rata_rata /= count($peserta);
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
    <title>Hasil Ujian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> 

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Merriweather', serif;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.1);
        }
    </style>
</head>
<body>


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

<!-- Top Section -->
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-white pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo htmlspecialchars($ujian['mata_pelajaran']); ?></h3>
                            <p class="text-muted mb-0">
                                <?php echo htmlspecialchars($ujian['judul']); ?> | 
                                Kelas <?php echo htmlspecialchars($ujian['tingkat']); ?>
                            </p>
                        </div>
                        <a href="beranda_guru.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                <?php if ($total_questions > 0): ?>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Total Peserta</h6>
                                    <h4 class="mb-0"><?php echo mysqli_num_rows($result_peserta); ?> Siswa</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Rata-rata Nilai</h6>
                                    <h4 class="mb-0"><?php echo number_format($rata_rata, 1); ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Nilai Tertinggi</h6>
                                    <h4 class="mb-0"><?php echo $nilai_tertinggi; ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Nilai Terendah</h6>
                                    <h4 class="mb-0"><?php echo $nilai_terendah; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th>Nama Siswa</th>
                <th class="text-center">Status</th>
                <th class="text-center">Benar</th>
                <th class="text-center">Salah</th>
                <th class="text-center">Tidak Dijawab</th>
                <th class="text-center">Nilai</th>
                <th class="text-center">Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $result_peserta = mysqli_query($koneksi, $query_peserta);
            while($peserta = mysqli_fetch_assoc($result_peserta)): 
                $unattempted = $total_questions - ($peserta['correct_answers'] + $peserta['wrong_answers']);
                $nilai = ($peserta['correct_answers'] / $total_questions) * 100;
            ?>
            <tr>
                <td class="fw-medium"><?php echo htmlspecialchars($peserta['nama']); ?></td>
                <td class="text-center">
                    <?php if($peserta['attempted_questions'] > 0): ?>
                        <span class="badge bg-success">Selesai</span>
                    <?php else: ?>
                        <span class="badge bg-warning">Belum Mengerjakan</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <span class="text-success fw-medium"><?php echo $peserta['correct_answers']; ?></span>
                </td>
                <td class="text-center">
                    <span class="text-danger fw-medium"><?php echo $peserta['wrong_answers']; ?></span>
                </td>
                <td class="text-center">
                    <span class="text-muted fw-medium"><?php echo $unattempted; ?></span>
                </td>
                <td class="text-center">
                    <span class="fw-medium"><?php echo number_format($nilai, 1); ?></span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary" 
                            onclick="window.location.href='detail_jawaban.php?ujian_id=<?php echo $ujian_id; ?>&siswa_id=<?php echo $peserta['siswa_id']; ?>'">
                        Lihat Detail
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php else: ?>
                <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Data tidak tersedia</h4>
                        <p>Tidak ada hasil ujian untuk ujian ini, pastikan ujian Anda telah selesai.</p>
                    </div>
                </div>
            <?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>