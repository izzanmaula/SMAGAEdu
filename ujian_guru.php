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

// Ambil semua ujian yang dibuat oleh guru yang sedang login
$query_ujian = "SELECT u.*, k.mata_pelajaran, k.background_image, k.tingkat FROM ujian u 
                JOIN kelas k ON u.kelas_id = k.id 
                WHERE u.guru_id = '$userid' 
                ORDER BY u.created_at DESC";

$result_ujian = mysqli_query($koneksi, $query_ujian);

                // Kemudian query untuk kelas
                $query_kelas = "SELECT k.*, COUNT(ks.siswa_id) as jumlah_siswa 
                FROM kelas k 
                LEFT JOIN kelas_siswa ks ON k.id = ks.kelas_id 
                WHERE k.guru_id = '$userid' AND k.is_archived = 0
                GROUP BY k.id";

                $result_kelas = mysqli_query($koneksi, $query_kelas); 


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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> 
    <title>Ujian - SMAGAEdu</title>
</head>
<style>
        .custom-card {
            width: 300px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0;
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

        @media (max-width: 768px) {
            .custom-card {
                width: 100%; /* Full width di mobile */
                max-width: 350px; /* Maximum width tetap 300px */
            }
        }
        body{ 
            font-family: merriweather;
        }
        @media (max-width: 768px) {
            body {
                padding-top: 56px; /* Sesuaikan dengan tinggi navbar */
            }
        }
        .color-web {
            background-color: rgb(218, 119, 86);
        }
        .btn {
            transition: background-color 0.3s ease;
            border: 0;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: rgb(219, 106, 68);
        }
</style>
<?php include 'includes/styles.php'; ?>
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


            <!-- ini isi kontennya -->
            <div class="col p-4 col-utama">
                <style>
                    .col-utama {
                        margin-left: 13rem;
                    }
                    @media (max-width: 768px) {
                        .col-utama {
                            margin-left: 0;
                            margin-top: 10px;
                        }
                    }
                    .class-card {
                        background: white;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                    }
                    .class-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                    }
                    .class-banner {
                        height: 160px;
                        background-size: cover;
                        background-position: center;
                        position: relative;
                    }
                    .profile-circle {
                        width: 70px;
                        height: 70px;
                        border-radius: 50%;
                        border: 3px solid white;
                        position: absolute;
                        bottom: -35px;
                        left: 20px;
                        background: white;
                        object-fit: cover;
                    }
                    .class-content {
                        padding: 2.5rem 1.5rem 1.5rem;
                    }
                    .class-title {
                        font-size: 1.25rem;
                        font-weight: bold;
                        margin: 0;
                    }
                    .class-meta {
                        color: #666;
                        font-size: 0.9rem;
                        margin-top: 0.5rem;
                    }
                    .action-buttons {
                        display: flex;
                        gap: 0.5rem;
                        margin-top: 1.5rem;
                    }
                    .btn-enter {
                        flex: 1;
                        padding: 8px;
                        border-radius: 8px;
                        background: #da7756;
                        text-align: center;
                        color: white;
                        border: none;
                        transition: background 0.3s ease;
                    }
                    .btn-enter:hover {
                        background: #c96845;
                        color: white;
                    }
                    .btn-more {
                        width: 38px;
                        padding: 8px;
                        border-radius: 8px;
                        border: 1px solid #eee;
                        background: white;
                        color: #666;
                    }
                    .btn-more:hover {
                        background: #f8f9fa;
                    }
                </style>

                <div class="row justify-content-between align-items-center mb-0 mb-md-4">
                    <!-- Alert messages -->
                    <?php if(isset($_GET['pesan'])): ?>
                        <div class="alert alert-dismissible fade show <?php 
                            echo $_GET['pesan'] == 'hapus_berhasil' ? 'alert-success' : 'alert-danger'; 
                        ?>" role="alert">
                            <?php
                            switch($_GET['pesan']) {
                                case 'hapus_berhasil':
                                    echo "Ujian berhasil dihapus";
                                    break;
                                case 'hapus_gagal':
                                    echo "Gagal menghapus ujian";
                                    break;
                                case 'tidak_ditemukan':
                                    echo "Ujian tidak ditemukan";
                                    break;
                            }
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="col-12 col-md-auto mb-3 mb-md-0">
                        <h3 style="font-weight: bold;">Ujian</h3>
                    </div>

                    <!-- Desktop button -->
                    <div class="d-none d-md-block col-md-auto">
                        <a href="buat_ujian.php" class="btn color-web text-white d-flex align-items-center">
                            <i class="bi bi-plus-lg me-2"></i>
                            Buat Ujian
                        </a>
                    </div>
                </div>

                <div class="row g-4">
                    <?php 
                    if(mysqli_num_rows($result_ujian) > 0):
                        while($ujian = mysqli_fetch_assoc($result_ujian)): 
                        $bg_image = !empty($ujian['background_image']) ? $ujian['background_image'] : 'assets/bg.jpg';
                    ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="class-card">
                                <div class="class-banner" style="background-image: url('<?php echo $bg_image; ?>')">
                                    <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                         class="profile-circle">
                                </div>
                                <div class="class-content">
                                    <h4 class="class-title mb-3"><?php echo htmlspecialchars($ujian['judul']); ?></h4>
                                    
                                    <div class="class-meta" style="font-size: 12px;">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-book me-2 text-muted"></i>
                                                    <span class="text-dark"><?php echo htmlspecialchars($ujian['mata_pelajaran']); ?></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Added class/grade info -->
                                            <div class="col-12">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-mortarboard me-2 text-muted"></i>
                                                    <span class="text-dark">Kelas <?php echo htmlspecialchars($ujian['tingkat']); ?></span>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person me-2 text-muted"></i>
                                                    <span class="text-secondary"><?php echo htmlspecialchars($guru['namaLengkap']); ?></span>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-calendar-event me-2 text-muted"></i>
                                                    <span class="text-secondary"><?php echo date('l, d F Y', strtotime($ujian['tanggal_mulai'])); ?></span>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-clock me-2 text-muted"></i>
                                                    <span class="text-secondary"><?php 
                                                        echo date('H:i', strtotime($ujian['tanggal_mulai'])) . ' - ' . 
                                                             date('H:i', strtotime($ujian['tanggal_selesai'])); 
                                                    ?></span>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-hourglass-split me-2 text-muted"></i>
                                                    <span class="text-secondary"><?php echo $ujian['durasi']; ?> menit</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="action-buttons mt-3">
                                        <a href="buat_soal.php?ujian_id=<?php echo $ujian['id']; ?>" 
                                           class="btn-enter text-decoration-none">
                                           <i class="bi bi-eye me-2"></i>Lihat
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn-more" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="detail_hasil_ujian.php?ujian_id=<?php echo $ujian['id']; ?>">
                                                        <i class="bi bi-clipboard2-check me-2"></i>Hasil Ujian
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" 
                                                       onclick="hapusUjian(<?php echo $ujian['id']; ?>); return false;">
                                                        <i class="bi bi-trash me-2"></i>Hapus Ujian
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">Belum ada ujian yang dibuat.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mt-4">
                    <a href="buat_ujian.php" class="btn color-web text-white w-100 d-flex d-md-none align-items-center justify-content-center" style="border: 2px dashed #ccc; background-color: transparent; min-height: 100px;">
                        <div class="text-center">
                            <i class="bi bi-plus-lg mb-2" style="font-size: 2rem; color: #666;"></i>
                            <div style="color: #666;">Tambah Ujian Baru</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

<script>
function hapusUjian(id) {
    if(confirm('Apakah Anda yakin ingin menghapus ujian ini? Semua soal yang terkait juga akan terhapus.')) {
        window.location.href = 'hapus_ujian.php?id=' + id;
    }
}
</script>
</body>
</html>