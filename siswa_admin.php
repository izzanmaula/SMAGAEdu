<?php
session_start();
require "koneksi.php";

// Check if the logged-in user is an admin
if (!isset($_SESSION['userid']) || $_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Get admin data
$userid = $_SESSION['userid'];
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);

// Query to get all student data
$query_siswa = "SELECT s.*, 
                (SELECT COUNT(*) FROM kelas_siswa WHERE siswa_id = s.id) as jumlah_kelas 
                FROM siswa s ORDER BY s.nama ASC";
$result_siswa = mysqli_query($koneksi, $query_siswa);

// Handle student addition form submission
if (isset($_POST['tambah_siswa'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $tahun_masuk = mysqli_real_escape_string($koneksi, $_POST['tahun_masuk']);
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);

    // Check if username already exists
    $cek_username = mysqli_query($koneksi, "SELECT * FROM siswa WHERE username = '$username'");
    if (mysqli_num_rows($cek_username) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        // Insert new student
        $insert_query = "INSERT INTO siswa (username, password, nama, tingkat, tahun_masuk, nis) 
                         VALUES ('$username', '$password', '$nama', '$tingkat', '$tahun_masuk', '$nis')";
        if (mysqli_query($koneksi, $insert_query)) {
            $success = "Siswa berhasil ditambahkan!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error = "Terjadi kesalahan: " . mysqli_error($koneksi);
        }
    }
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    // Mulai transaksi database untuk memastikan semua atau tidak ada yang terhapus
    mysqli_begin_transaction($koneksi);

    try {
        // Hapus data kelas_siswa terlebih dahulu
        mysqli_query($koneksi, "DELETE FROM kelas_siswa WHERE siswa_id = '$id'");

        // Hapus data pg jika ada
        mysqli_query($koneksi, "DELETE FROM pg WHERE siswa_id = '$id'");

        // Hapus data pengumpulan_tugas jika ada
        mysqli_query($koneksi, "DELETE FROM pengumpulan_tugas WHERE siswa_id = '$id'");

        // Hapus data jawaban_ujian jika ada
        mysqli_query($koneksi, "DELETE FROM jawaban_ujian WHERE siswa_id = '$id'");

        // Hapus siswa
        mysqli_query($koneksi, "DELETE FROM siswa WHERE id = '$id'");

        // Commit transaksi jika semua berhasil
        mysqli_commit($koneksi);

        $success = "Siswa berhasil dihapus!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($koneksi);
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Manajemen Siswa - SMAGAEdu</title>
    <style>
        body {
            font-family: 'Merriweather', serif;
        }

        .color-web {
            background-color: rgb(218, 119, 86);
        }

        .btn-primary {
            background-color: rgb(218, 119, 86);
            border-color: rgb(218, 119, 86);
        }

        .btn-primary:hover {
            background-color: rgb(190, 100, 70);
            border-color: rgb(190, 100, 70);
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .profile-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table th {
            font-weight: 600;
            color: #444;
        }

        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 5px;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Student detail row styling */
        .student-details {
            background-color: #f8f9fa;
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.5s ease-out;
            border-radius: 0 0 12px 12px;
        }

        .student-details.show {
            max-height: 800px;
            transition: max-height 0.5s ease-in;
        }

        .student-item {
            cursor: pointer;
        }

        .student-item:hover {
            background-color: rgba(218, 119, 86, 0.05);
        }

        .student-detail-container {
            padding: 20px;
        }

        .detail-section {
            border-left: 3px solid rgb(218, 119, 86);
            padding-left: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <?php include 'includes/styles.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar for desktop -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Mobile navigation -->
            <?php include 'includes/mobile_nav.php'; ?>
        </div>
    </div>


    <!-- style animasi modal -->
    <style>
        .modal-content {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .modal .btn {
            font-weight: 500;
            transition: all 0.2s;
        }

        .modal .btn:active {
            transform: scale(0.98);
        }

        .modal.fade .modal-dialog {
            transform: scale(0.95);
            transition: transform 0.2s ease-out;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }
    </style>


    <!-- Main Content -->
    <div class="col col-inti p-0 p-md-3">
        <style>
            .col-inti {
                margin-left: 0;
                padding: 1rem;
                max-width: 100%;
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

        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
                <h2 class="mb-0 fw-bold">Manajemen Siswa</h2>
                <button class="btn btn-white border" style="border-radius:15px;" data-bs-toggle="modal" data-bs-target="#tambahSiswaModal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
                </button>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger animate-fade-in">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success animate-fade-in">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- Statistik Siswa -->
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="card shadow-none border animate-fade-in position-relative overflow-hidden">
                        <div class="card-body">
                            <h5 class="card-title">Total Siswa</h5>
                            <h2 class="display-4 mt-3 fw-bold"><?php echo mysqli_num_rows($result_siswa); ?></h2>
                            <i class="bi bi-people-fill position-absolute opacity-25"
                                style="bottom: -85px; right: -15px; font-size: 150px; color:rgb(218, 119, 86);"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card shadow-none border animate-fade-in position-relative overflow-hidden">
                        <div class="card-body">
                            <h5 class="card-title">Total Kelas</h5>
                            <?php
                            $query_kelas = "SELECT COUNT(*) as total FROM kelas";
                            $result_kelas = mysqli_query($koneksi, $query_kelas);
                            $total_kelas = mysqli_fetch_assoc($result_kelas)['total'];
                            ?>
                            <h2 class="display-4 mt-3 fw-bold"><?php echo $total_kelas; ?></h2>
                            <i class="bi bi-building position-absolute opacity-25"
                                style="bottom: -85px; right: -15px; font-size: 150px; color:rgb(218, 119, 86);"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card shadow-none border animate-fade-in position-relative overflow-hidden">
                        <div class="card-body">
                            <h5 class="card-title">Siswa Aktif</h5>
                            <?php
                            $query_aktif = "SELECT COUNT(DISTINCT siswa_id) as total FROM kelas_siswa WHERE status = 'active'";
                            $result_aktif = mysqli_query($koneksi, $query_aktif);
                            $total_aktif = mysqli_fetch_assoc($result_aktif)['total'];
                            ?>
                            <h2 class="display-4 mt-3 fw-bold"><?php echo $total_aktif; ?></h2>
                            <i class="bi bi-person-check-fill position-absolute opacity-25"
                                style="bottom: -85px; right: -15px; font-size: 150px; color:rgb(218, 119, 86);"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- style tabel siswa -->
            <style>
    /* Efek hover pada baris tabel */
    .siswa-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }
    
    .siswa-item:hover {
        background-color: rgba(218, 119, 86, 0.05);
        cursor: pointer;
        transform: translateX(3px);
    }
    
    /* Efek aktif pada baris yang diklik */
    .siswa-item.active {
        background-color: rgba(218, 119, 86, 0.1);
        border-left: 3px solid rgb(218, 119, 86);
    }
    
    /* Animasi untuk baris detail */
    .student-details {
        background-color: #f8f9fa;
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.5s ease-out, opacity 0.4s ease-out;
        opacity: 0;
    }
    
    .student-details.show {
        max-height: 1500px; /* Nilai ini bisa disesuaikan */
        opacity: 1;
        transition: max-height 0.5s ease-in, opacity 0.4s ease-in;
        box-shadow: inset 0 5px 10px -5px rgba(0,0,0,0.1);
    }
</style>


            <!-- iOS-style Student List Card -->
            <div class="card animate-fade-in shadow-none border" style="border-radius: 20px;">
                <div class="card-body p-0">
                    <!-- Search bar iOS style -->
                    <div class="px-4 pt-4 pb-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light border-0" style="border-radius: 12px 0 0 12px;">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="searchSiswa" class="form-control bg-light border-0"
                                placeholder="Cari siswa..." style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>

                    <div class="table-responsive px-2">
                        <table class="table table-borderless align-middle">
                            <thead class="text-muted" style="font-size: 0.85rem; font-weight: 600;">
                                <tr>
                                    <th class="ps-3" style="width: 10%">Foto</th>
                                    <th style="width: 25%">Nama Lengkap</th>
                                    <th style="width: 15%">Username</th>
                                    <th style="width: 15%">Tingkat</th>
                                    <th style="width: 15%">Kelas</th>
                                    <th style="width: 20%"></th>
                                </tr>
                            </thead>
                            <tbody id="siswaTableBody">
                                <?php if (mysqli_num_rows($result_siswa) > 0): ?>
                                    <?php while ($siswa = mysqli_fetch_assoc($result_siswa)): ?>
                                        <tr class="siswa-item" data-siswa-id="<?php echo $siswa['id']; ?>">
                                            <td class="ps-3">
                                                <div style="width: 48px; height: 48px; overflow: hidden; border-radius: 12px;" class="border">
                                                    <img src="<?php
                                                                if (!empty($siswa['photo_type'])) {
                                                                    if ($siswa['photo_type'] === 'avatar') {
                                                                        echo $siswa['photo_url'];
                                                                    } else if ($siswa['photo_type'] === 'upload') {
                                                                        echo 'uploads/profil/' . $siswa['foto_profil'];
                                                                    }
                                                                } else {
                                                                    echo 'assets/pp.png';
                                                                }
                                                                ?>"
                                                        alt="<?php echo htmlspecialchars($siswa['nama']); ?>"
                                                        class="w-100 h-100" style="object-fit: cover;">
                                                </div>
                                            </td>
                                            <td class="fw-medium"><?php echo htmlspecialchars($siswa['nama']); ?></td>
                                            <td class="text-muted"><?php echo htmlspecialchars($siswa['username']); ?></td>
                                            <td><span class="text-muted"><?php echo htmlspecialchars($siswa['tingkat'] ?: 'Belum diatur'); ?></span></td>
                                            <td>
                                                <span class="badge" style="background-color: rgba(218, 119, 86, 0.15); color: rgb(218, 119, 86); font-weight: 600; padding: 5px 10px; border-radius: 6px;">
                                                    <?php echo $siswa['jumlah_kelas']; ?> kelas
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="edit_siswa.php?id=<?php echo $siswa['id']; ?>"
                                                        class="btn btn-sm text-muted" style="background: none;">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm text-danger deleteBtn"
                                                        style="background: none;"
                                                        data-id="<?php echo $siswa['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($siswa['nama']); ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Expandable Student Details Row -->
                                        <tr>
                                            <td colspan="6" class="p-0">
                                                <div class="student-details" id="details-<?php echo $siswa['id']; ?>">
                                                    <div class="student-detail-container">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="detail-section">
                                                                    <h6 class="fw-bold mb-3">Informasi Pribadi</h6>
                                                                    <div class="mb-2">
                                                                        <span class="text-muted">NIS:</span>
                                                                        <span class="ms-2 fw-medium"><?php echo $siswa['nis'] ?: 'Belum diatur'; ?></span>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <span class="text-muted">Tahun Masuk:</span>
                                                                        <span class="ms-2 fw-medium"><?php echo $siswa['tahun_masuk'] ?: 'Belum diatur'; ?></span>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <span class="text-muted">No. HP:</span>
                                                                        <span class="ms-2 fw-medium"><?php echo $siswa['no_hp'] ?: 'Belum diatur'; ?></span>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <span class="text-muted">Alamat:</span>
                                                                        <span class="ms-2 fw-medium"><?php echo $siswa['alamat'] ?: 'Belum diatur'; ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="detail-section">
                                                                    <h6 class="fw-bold mb-3">Kelas Terdaftar</h6>
                                                                    <?php
                                                                    $query_kelas_siswa = "SELECT k.nama_kelas, k.mata_pelajaran, g.namaLengkap as guru 
                                                                                        FROM kelas_siswa ks 
                                                                                        JOIN kelas k ON ks.kelas_id = k.id 
                                                                                        LEFT JOIN guru g ON k.guru_id = g.username 
                                                                                        WHERE ks.siswa_id = {$siswa['id']}
                                                                                        LIMIT 5";
                                                                    $result_kelas_siswa = mysqli_query($koneksi, $query_kelas_siswa);

                                                                    if (mysqli_num_rows($result_kelas_siswa) > 0) {
                                                                        echo '<div class="list-group list-group-flush">';
                                                                        while ($kelas = mysqli_fetch_assoc($result_kelas_siswa)) {
                                                                            echo '<div class="list-group-item bg-light px-0 py-2 border-0">';
                                                                            echo '<div class="d-flex justify-content-between align-items-center">';
                                                                            echo '<span class="fw-medium">' . htmlspecialchars($kelas['nama_kelas']) . '</span>';
                                                                            echo '</div>';
                                                                            echo '<div class="small text-muted">' . htmlspecialchars($kelas['mata_pelajaran']) . ' • ' . htmlspecialchars($kelas['guru']) . '</div>';
                                                                            echo '</div>';
                                                                        }
                                                                        echo '</div>';
                                                                    } else {
                                                                        echo '<p class="text-muted">Tidak ada kelas terdaftar</p>';
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Progressive Guidance Stats -->
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <div class="detail-section">
                                                                    <h6 class="fw-bold mb-3">Progressive Guidance</h6>
                                                                    <?php
                                                                    $query_pg = "SELECT * FROM pg WHERE siswa_id = {$siswa['id']} ORDER BY semester DESC, tahun_ajaran DESC LIMIT 1";
                                                                    $result_pg = mysqli_query($koneksi, $query_pg);

                                                                    if (mysqli_num_rows($result_pg) > 0) {
                                                                        $pg = mysqli_fetch_assoc($result_pg);
                                                                    ?>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6 col-lg-4">
                                                                                <div class="card border bg-light shadow-none">
                                                                                    <div class="card-body p-3">
                                                                                        <h6 class="card-title mb-2">Belajar</h6>
                                                                                        <?php 
                                                                                        // Get latest non-zero/non-null academic values
                                                                                        $query_latest = "SELECT 
                                                                                            COALESCE(MAX(NULLIF(nilai_akademik,0)), 0) as nilai_akademik,
                                                                                            COALESCE(MAX(NULLIF(keaktifan,0)), 0) as keaktifan,
                                                                                            COALESCE(MAX(NULLIF(pemahaman,0)), 0) as pemahaman,
                                                                                            semester,
                                                                                            tahun_ajaran
                                                                                        FROM pg 
                                                                                        WHERE siswa_id = {$siswa['id']}
                                                                                        AND (nilai_akademik > 0 OR keaktifan > 0 OR pemahaman > 0)";
                                                                                        
                                                                                        $result_latest = mysqli_query($koneksi, $query_latest);
                                                                                        $latest_data = mysqli_fetch_assoc($result_latest);

                                                                                        $nilai_akademik = intval($latest_data['nilai_akademik']);
                                                                                        $keaktifan = intval($latest_data['keaktifan']); 
                                                                                        $pemahaman = intval($latest_data['pemahaman']);
                                                                                        $belajar = ($nilai_akademik + $keaktifan + $pemahaman) / 3;
                                                                                        ?>
                                                                                        <div class="progress mb-3" style="height: 8px;">
                                                                                            <div class="progress-bar color-web" style="width: <?php echo $belajar; ?>%"></div>
                                                                                        </div>
                                                                                        
                                                                                        <ul class="list-unstyled mb-2">
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Akademik:</span>
                                                                                                <span class="fw-medium"><?php echo $nilai_akademik; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Keaktifan:</span>
                                                                                                <span class="fw-medium"><?php echo $keaktifan; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Pemahaman:</span>
                                                                                                <span class="fw-medium"><?php echo $pemahaman; ?>%</span>
                                                                                            </li>
                                                                                        </ul>
                                                                                        
                                                                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top">
                                                                                            <span class="badge bg-light text-dark"><?php echo round($belajar); ?>%</span>
                                                                                            <small class="text-muted">Sem <?php echo $latest_data['semester']; ?> Th. <?php echo $latest_data['tahun_ajaran']; ?></small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6 col-lg-4">
                                                                                <div class="card border bg-light shadow-none">
                                                                                    <div class="card-body p-3">
                                                                                        <h6 class="card-title mb-2">Ibadah</h6>
                                                                                        <?php
                                                                                        // Get latest non-zero/non-null ibadah values
                                                                                        $query_latest_ibadah = "SELECT
                                                                                            COALESCE(MAX(NULLIF(kehadiran_ibadah,0)), 0) AS kehadiran_ibadah,
                                                                                            COALESCE(MAX(NULLIF(kualitas_ibadah,0)), 0) AS kualitas_ibadah,
                                                                                            COALESCE(MAX(NULLIF(pemahaman_agama,0)), 0) AS pemahaman_agama,
                                                                                            semester,
                                                                                            tahun_ajaran
                                                                                        FROM pg
                                                                                        WHERE siswa_id = {$siswa['id']}
                                                                                        AND (kehadiran_ibadah > 0 OR kualitas_ibadah > 0 OR pemahaman_agama > 0)";

                                                                                        $result_latest_ibadah = mysqli_query($koneksi, $query_latest_ibadah);
                                                                                        $latest_data_ibadah = mysqli_fetch_assoc($result_latest_ibadah);

                                                                                        $kehadiran_ibadah = intval($latest_data_ibadah['kehadiran_ibadah']);
                                                                                        $kualitas_ibadah = intval($latest_data_ibadah['kualitas_ibadah']);
                                                                                        $pemahaman_agama = intval($latest_data_ibadah['pemahaman_agama']);
                                                                                        $ibadah = ($kehadiran_ibadah + $kualitas_ibadah + $pemahaman_agama) / 3;
                                                                                        ?>
                                                                                        <div class="progress mb-3" style="height: 8px;">
                                                                                            <div class="progress-bar color-web" style="width: <?php echo $ibadah; ?>%"></div>
                                                                                        </div>

                                                                                        <ul class="list-unstyled mb-2">
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Kehadiran:</span>
                                                                                                <span class="fw-medium"><?php echo $kehadiran_ibadah; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Kualitas:</span>
                                                                                                <span class="fw-medium"><?php echo $kualitas_ibadah; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Pemahaman:</span>
                                                                                                <span class="fw-medium"><?php echo $pemahaman_agama; ?>%</span>
                                                                                            </li>
                                                                                        </ul>

                                                                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top">
                                                                                            <span class="badge bg-light text-dark"><?php echo round($ibadah); ?>%</span>
                                                                                            <small class="text-muted">Sem <?php echo $latest_data_ibadah['semester']; ?> Th. <?php echo $latest_data_ibadah['tahun_ajaran']; ?></small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6 col-lg-4">
                                                                                <div class="card border bg-light shadow-none">
                                                                                    <div class="card-body p-3">
                                                                                        <h6 class="card-title mb-2">Pengembangan</h6>
                                                                                        <?php
                                                                                        // Get latest non-zero/non-null pengembangan values
                                                                                        $query_latest_pengembangan = "SELECT
                                                                                            COALESCE(MAX(NULLIF(minat_bakat,0)), 0) AS minat_bakat,
                                                                                            COALESCE(MAX(NULLIF(prestasi,0)), 0) AS prestasi,
                                                                                            COALESCE(MAX(NULLIF(keaktifan_ekskul,0)), 0) AS keaktifan_ekskul,
                                                                                            semester,
                                                                                            tahun_ajaran
                                                                                        FROM pg
                                                                                        WHERE siswa_id = {$siswa['id']}
                                                                                        AND (minat_bakat > 0 OR prestasi > 0 OR keaktifan_ekskul > 0)";

                                                                                        $result_latest_pengembangan = mysqli_query($koneksi, $query_latest_pengembangan);
                                                                                        $latest_data_pengembangan = mysqli_fetch_assoc($result_latest_pengembangan);

                                                                                        $minat_bakat = intval($latest_data_pengembangan['minat_bakat']);
                                                                                        $prestasi = intval($latest_data_pengembangan['prestasi']);
                                                                                        $keaktifan_ekskul = intval($latest_data_pengembangan['keaktifan_ekskul']);
                                                                                        $pengembangan = ($minat_bakat + $prestasi + $keaktifan_ekskul) / 3;
                                                                                        ?>
                                                                                        <div class="progress mb-3" style="height: 8px;">
                                                                                            <div class="progress-bar color-web" style="width: <?php echo $pengembangan; ?>%"></div>
                                                                                        </div>

                                                                                        <ul class="list-unstyled mb-2">
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Minat & Bakat:</span>
                                                                                                <span class="fw-medium"><?php echo $minat_bakat; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Prestasi:</span>
                                                                                                <span class="fw-medium"><?php echo $prestasi; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Keaktifan:</span>
                                                                                                <span class="fw-medium"><?php echo $keaktifan_ekskul; ?>%</span>
                                                                                            </li>
                                                                                        </ul>

                                                                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top">
                                                                                            <span class="badge bg-light text-dark"><?php echo round($pengembangan); ?>%</span>
                                                                                            <small class="text-muted">Sem <?php echo $latest_data_pengembangan['semester']; ?> Th. <?php echo $latest_data_pengembangan['tahun_ajaran']; ?></small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6 col-lg-4">
                                                                                <div class="card border bg-light shadow-none">
                                                                                    <div class="card-body p-3">
                                                                                        <h6 class="card-title mb-2">Sosial</h6>
                                                                                        <?php
                                                                                        // Get latest non-zero/non-null sosial values
                                                                                        $query_latest_sosial = "SELECT
                                                                                            COALESCE(MAX(NULLIF(partisipasi_sosial,0)), 0) AS partisipasi_sosial,
                                                                                            COALESCE(MAX(NULLIF(empati,0)), 0) AS empati,
                                                                                            COALESCE(MAX(NULLIF(kerja_sama,0)), 0) AS kerja_sama,
                                                                                            semester,
                                                                                            tahun_ajaran
                                                                                        FROM pg
                                                                                        WHERE siswa_id = {$siswa['id']}
                                                                                        AND (partisipasi_sosial > 0 OR empati > 0 OR kerja_sama > 0)";

                                                                                        $result_latest_sosial = mysqli_query($koneksi, $query_latest_sosial);
                                                                                        $latest_data_sosial = mysqli_fetch_assoc($result_latest_sosial);

                                                                                        $partisipasi_sosial = intval($latest_data_sosial['partisipasi_sosial']);
                                                                                        $empati = intval($latest_data_sosial['empati']);
                                                                                        $kerja_sama = intval($latest_data_sosial['kerja_sama']);
                                                                                        $sosial = ($partisipasi_sosial + $empati + $kerja_sama) / 3;
                                                                                        ?>
                                                                                        <div class="progress mb-3" style="height: 8px;">
                                                                                            <div class="progress-bar color-web" style="width: <?php echo $sosial; ?>%"></div>
                                                                                        </div>

                                                                                        <ul class="list-unstyled mb-2">
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Partisipasi:</span>
                                                                                                <span class="fw-medium"><?php echo $partisipasi_sosial; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Empati:</span>
                                                                                                <span class="fw-medium"><?php echo $empati; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Kerja Sama:</span>
                                                                                                <span class="fw-medium"><?php echo $kerja_sama; ?>%</span>
                                                                                            </li>
                                                                                        </ul>

                                                                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top">
                                                                                            <span class="badge bg-light text-dark"><?php echo round($sosial); ?>%</span>
                                                                                            <small class="text-muted">Sem <?php echo $latest_data_sosial['semester']; ?> Th. <?php echo $latest_data_sosial['tahun_ajaran']; ?></small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6 col-lg-4">
                                                                                <div class="card border bg-light shadow-none">
                                                                                    <div class="card-body p-3">
                                                                                        <h6 class="card-title mb-2">Kesehatan</h6>
                                                                                        <?php
                                                                                        // Get latest non-zero/non-null kesehatan values
                                                                                        $query_latest_kesehatan = "SELECT
                                                                                            COALESCE(MAX(NULLIF(kebersihan_diri,0)), 0) AS kebersihan_diri,
                                                                                            COALESCE(MAX(NULLIF(aktivitas_fisik,0)), 0) AS aktivitas_fisik,
                                                                                            COALESCE(MAX(NULLIF(pola_makan,0)), 0) AS pola_makan,
                                                                                            semester,
                                                                                            tahun_ajaran
                                                                                        FROM pg
                                                                                        WHERE siswa_id = {$siswa['id']}
                                                                                        AND (kebersihan_diri > 0 OR aktivitas_fisik > 0 OR pola_makan > 0)";

                                                                                        $result_latest_kesehatan = mysqli_query($koneksi, $query_latest_kesehatan);
                                                                                        $latest_data_kesehatan = mysqli_fetch_assoc($result_latest_kesehatan);

                                                                                        $kebersihan = intval($latest_data_kesehatan['kebersihan_diri']);
                                                                                        $aktivitas = intval($latest_data_kesehatan['aktivitas_fisik']);
                                                                                        $pola_makan = intval($latest_data_kesehatan['pola_makan']);
                                                                                        $kesehatan = ($kebersihan + $aktivitas + $pola_makan) / 3;
                                                                                        ?>
                                                                                        <div class="progress mb-3" style="height: 8px;">
                                                                                            <div class="progress-bar color-web" style="width: <?php echo $kesehatan; ?>%"></div>
                                                                                        </div>

                                                                                        <ul class="list-unstyled mb-2">
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Kebersihan:</span>
                                                                                                <span class="fw-medium"><?php echo $kebersihan; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Aktivitas:</span>
                                                                                                <span class="fw-medium"><?php echo $aktivitas; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Pola Makan:</span>
                                                                                                <span class="fw-medium"><?php echo $pola_makan; ?>%</span>
                                                                                            </li>
                                                                                        </ul>

                                                                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top">
                                                                                            <span class="badge bg-light text-dark"><?php echo round($kesehatan); ?>%</span>
                                                                                            <small class="text-muted">Sem <?php echo $latest_data_kesehatan['semester']; ?> Th. <?php echo $latest_data_kesehatan['tahun_ajaran']; ?></small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6 col-lg-4">
                                                                                <div class="card border bg-light shadow-none">
                                                                                    <div class="card-body p-3">
                                                                                        <h6 class="card-title mb-2">Karakter</h6>
                                                                                        <?php
                                                                                        // Get latest non-zero/non-null karakter values
                                                                                        $query_latest_karakter = "SELECT
                                                                                            COALESCE(MAX(NULLIF(kejujuran,0)), 0) AS kejujuran,
                                                                                            COALESCE(MAX(NULLIF(tanggung_jawab,0)), 0) AS tanggung_jawab,
                                                                                            COALESCE(MAX(NULLIF(kedisiplinan,0)), 0) AS kedisiplinan,
                                                                                            semester,
                                                                                            tahun_ajaran
                                                                                        FROM pg
                                                                                        WHERE siswa_id = {$siswa['id']}
                                                                                        AND (kejujuran > 0 OR tanggung_jawab > 0 OR kedisiplinan > 0)";

                                                                                        $result_latest_karakter = mysqli_query($koneksi, $query_latest_karakter);
                                                                                        $latest_data_karakter = mysqli_fetch_assoc($result_latest_karakter);

                                                                                        $kejujuran = intval($latest_data_karakter['kejujuran']);
                                                                                        $tanggung_jawab = intval($latest_data_karakter['tanggung_jawab']);
                                                                                        $kedisiplinan = intval($latest_data_karakter['kedisiplinan']);
                                                                                        $karakter = ($kejujuran + $tanggung_jawab + $kedisiplinan) / 3;
                                                                                        ?>
                                                                                        <div class="progress mb-3" style="height: 8px;">
                                                                                            <div class="progress-bar color-web" style="width: <?php echo $karakter; ?>%"></div>
                                                                                        </div>

                                                                                        <ul class="list-unstyled mb-2">
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Kejujuran:</span>
                                                                                                <span class="fw-medium"><?php echo $kejujuran; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Tanggung Jawab:</span>
                                                                                                <span class="fw-medium"><?php echo $tanggung_jawab; ?>%</span>
                                                                                            </li>
                                                                                            <li class="d-flex justify-content-between small mb-1">
                                                                                                <span class="text-muted">• Kedisiplinan:</span>
                                                                                                <span class="fw-medium"><?php echo $kedisiplinan; ?>%</span>
                                                                                            </li>
                                                                                        </ul>

                                                                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top">
                                                                                            <span class="badge bg-light text-dark"><?php echo round($karakter); ?>%</span>
                                                                                            <small class="text-muted">Sem <?php echo $latest_data_karakter['semester']; ?> Th. <?php echo $latest_data_karakter['tahun_ajaran']; ?></small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php
                                                                    } else {
                                                                        echo '<p class="text-muted">Belum ada data penilaian</p>';
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center py-4">
                                                <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">Tidak ada data siswa</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <div class="modal-body px-4 text-center">
                            <div class="mb-4">
                                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="mb-3 fw-bold">Hapus " <strong id="deleteSiswaName"></strong> " dari SMAGAEdu?</h5>
                            <p class="text-muted">Anda akan menghapus data siswa <strong id="deleteSiswaName"></strong> dari database. Pastikan seluruh tindakan Anda telah sesuai</p>
                        </div>
                        <div class="modal-footer border-0 pt-0 btn-group">
                            <button type="button" class="btn btn-light" style="border-radius: 12px; padding: 10px 20px;" data-bs-dismiss="modal">Batal</button>
                            <a href="#" id="deleteSiswaLink" class="btn btn-danger" style="border-radius: 12px; padding: 10px 20px;">Hapus</a>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Set data for delete modal
                document.addEventListener('DOMContentLoaded', function() {
                    const deleteModal = document.getElementById('deleteModal');
                    if (deleteModal) {
                        deleteModal.addEventListener('show.bs.modal', function(event) {
                            const button = event.relatedTarget; // Button that triggered the modal
                            const id = button.getAttribute('data-id');
                            const name = button.getAttribute('data-name');

                            document.querySelectorAll('#deleteSiswaName').forEach(el => {
                                el.textContent = name;
                            });
                            document.getElementById('deleteSiswaLink').href = '?hapus=' + id;
                        });
                    }

                    // Student row expansion functionality
                    const studentRows = document.querySelectorAll('.siswa-item');
                    studentRows.forEach(row => {
                        row.addEventListener('click', function(e) {
                            // Don't expand if clicking on buttons
                            if (e.target.closest('.btn') || e.target.closest('a')) {
                                return;
                            }

                            const studentId = this.getAttribute('data-siswa-id');
                            const detailsElement = document.getElementById('details-' + studentId);

                            // Toggle the current clicked row
                            if (detailsElement) {
                                if (detailsElement.classList.contains('show')) {
                                    detailsElement.classList.remove('show');
                                    this.classList.remove('active');
                                } else {
                                    // First close all other open details
                                    document.querySelectorAll('.student-details.show').forEach(detail => {
                                        detail.classList.remove('show');
                                    });
                                    // First close all other open details
                                    document.querySelectorAll('.student-details.show').forEach(detail => {
                                        detail.classList.remove('show');
                                    });
                                    document.querySelectorAll('.siswa-item.active').forEach(item => {
                                        item.classList.remove('active');
                                    });

                                    // Open the clicked one
                                    detailsElement.classList.add('show');
                                    this.classList.add('active');
                                }
                            }
                        });
                    });
                });

                // Simple search functionality
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('searchSiswa');
                    const rows = document.querySelectorAll('.siswa-item');

                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();

                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            const detailId = row.getAttribute('data-siswa-id');
                            const detailRow = document.getElementById('details-' + detailId);

                            if (text.includes(searchTerm)) {
                                row.style.display = '';
                                // If there's a next row (which contains details), show it too
                                if (detailRow) {
                                    detailRow.parentElement.parentElement.style.display = '';
                                }
                            } else {
                                row.style.display = 'none';
                                // If there's a next row (which contains details), hide it too
                                if (detailRow) {
                                    detailRow.parentElement.parentElement.style.display = 'none';
                                }
                            }
                        });
                    });
                });
            </script>
        </div>
    </div>

    <!-- Modal Tambah Siswa -->
    <div class="modal fade" id="tambahSiswaModal" tabindex="-1" aria-labelledby="tambahSiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="tambahSiswaModalLabel">Tambah Siswa Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body px-4">
                        <div class="mb-4">
                            <label for="username" class="form-label fw-medium small mb-2">Username</label>
                            <input type="text" class="form-control bg-white border-0" style="border-radius: 12px; padding: 12px 15px;"
                                id="username" name="username" required>
                            <small class="text-muted">Username akan digunakan untuk login</small>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium small mb-2">Password</label>
                            <input type="password" class="form-control bg-white border-0" style="border-radius: 12px; padding: 12px 15px;"
                                id="password" name="password" required>
                        </div>
                        <div class="mb-4">
                            <label for="nama" class="form-label fw-medium small mb-2">Nama Lengkap</label>
                            <input type="text" class="form-control bg-white border-0" style="border-radius: 12px; padding: 12px 15px;"
                                id="nama" name="nama" required>
                        </div>
                        <div class="mb-4">
                            <label for="tingkat" class="form-label fw-medium small mb-2">Tingkat/Kelas</label>
                            <select class="form-select bg-white border-0" style="border-radius: 12px; padding: 12px 15px;"
                                id="tingkat" name="tingkat">
                                <option value="">Pilih Tingkat</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="tahun_masuk" class="form-label fw-medium small mb-2">Tahun Masuk</label>
                            <input type="number" class="form-control bg-white border-0" style="border-radius: 12px; padding: 12px 15px;"
                                id="tahun_masuk" name="tahun_masuk" min="2000" max="2099" step="1" value="<?php echo date('Y'); ?>">
                        </div>
                        <div class="mb-4">
                            <label for="nis" class="form-label fw-medium small mb-2">NIS</label>
                            <input type="text" class="form-control bg-white border-0" style="border-radius: 12px; padding: 12px 15px;"
                                id="nis" name="nis">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" style="border-radius: 12px; padding: 10px 20px;"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_siswa" class="btn btn-primary"
                            style="border-radius: 12px; padding: 10px 20px;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-hide alerts after 5 seconds
        window.addEventListener('DOMContentLoaded', (event) => {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 1s';
                    setTimeout(() => {
                        alert.remove();
                    }, 1000);
                });
            }, 5000);
        });
    </script>
</body>

</html>