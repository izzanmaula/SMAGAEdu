<?php
session_start();
require "koneksi.php";

// Cek session - izinkan guru dan admin
if (!isset($_SESSION['userid']) || ($_SESSION['level'] != 'guru' && $_SESSION['level'] != 'admin')) {
    header("Location: index.php");
    exit();
}

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
        s.photo_type,
        s.photo_url,
        s.foto_profil,
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


// ambil soal 
// Query untuk mendapatkan statistik jawaban per soal
// Query untuk mendapatkan statistik jawaban per soal
// Query untuk mendapatkan statistik jawaban per soal
$query_soal_stats = "
    SELECT 
        bs.id as soal_id,
        bs.pertanyaan,
        bs.jawaban_benar,
        bs.jawaban_a,
        bs.jawaban_b,
        bs.jawaban_c,
        bs.jawaban_d,
        bs.gambar_soal,
        COUNT(ju.id) as total_menjawab,
        COUNT(CASE WHEN UPPER(ju.jawaban) = UPPER(bs.jawaban_benar) THEN 1 END) as total_benar,
        COUNT(CASE WHEN ju.jawaban IS NOT NULL AND UPPER(ju.jawaban) != UPPER(bs.jawaban_benar) THEN 1 END) as total_salah,
        COUNT(CASE WHEN UPPER(ju.jawaban) = 'A' THEN 1 END) as jawab_a,
        COUNT(CASE WHEN UPPER(ju.jawaban) = 'B' THEN 1 END) as jawab_b,
        COUNT(CASE WHEN UPPER(ju.jawaban) = 'C' THEN 1 END) as jawab_c,
        COUNT(CASE WHEN UPPER(ju.jawaban) = 'D' THEN 1 END) as jawab_d
    FROM bank_soal bs
    LEFT JOIN jawaban_ujian ju ON bs.id = ju.soal_id AND ju.ujian_id = '$ujian_id'
    WHERE bs.ujian_id = '$ujian_id'
    GROUP BY bs.id
    ORDER BY total_salah DESC";

$result_soal_stats = mysqli_query($koneksi, $query_soal_stats);
$soal_stats = [];
while ($row = mysqli_fetch_assoc($result_soal_stats)) {
    $soal_stats[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Hasil Ujian</title>

    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- CSS kustom setelah Bootstrap -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <body>

        <style>
            .navbar {
                display: none;
            }

            body {
                font-family: 'Merriweather', serif;
            }

            @media screen and (max-width: 768px) {
                .navbar {
                    display: block !important;
                }

                .menu-samping {
                    display: none;
                }
            }

            @media screen and (max-width: 768px) {
                .container-fluid {
                    display: none;
                }
            }
        </style>


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

        <!-- animasi modal -->
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

        <!-- Mobile view blocker (iOS style) -->
        <div class="mobile-blocker d-md-none position-fixed top-0 start-0 w-100 h-100 bg-white" style="z-index: 9999;">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 px-4">
                <div class="text-center mb-4">
                    <i class="bi bi-laptop display-1 text-secondary"></i>
                </div>
                <h4 class="mb-3 fw-bold text-dark">Akses Ditolak</h4>
                <p class="text-center text-secondary mb-4" style="font-size: 12px;">
                    Halaman ini hanya dapat diakses pada perangkat laptop atau tablet.
                    Silakan gunakan perangkat dengan layar yang lebih besar.
                </p>
                <a href="ujian_guru.php" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm" style="background-color: rgb(218, 119, 86); border: none;">
                    <i class="bi bi-arrow-left me-2"></i>
                    Kembali 
                </a>
            </div>
        </div>



        <!-- ini isi kontennya -->
        <div class="col p-4 col-utama">
            <style>
                .col-utama {
                    margin-left: 13rem;
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

                @media (max-width: 768px) {
                    .col-utama {
                        margin-left: 0;
                        margin-top: 10px;
                        /* Untuk memberikan space dari fixed navbar mobile */
                    }
                }
            </style>

            <!-- Top Section -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 mb-4">
                            <div class="card-header bg-white border-0 pb-0">
                                <div class="d-flex border-0 justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-0"><?php echo htmlspecialchars($ujian['mata_pelajaran']); ?></h3>
                                        <p class="text-muted mb-0">
                                            <?php echo htmlspecialchars($ujian['judul']); ?> |
                                            Kelas <?php echo htmlspecialchars($ujian['tingkat']); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <button onclick="window.print()" class="btn bg-white border me-2">
                                            <i class="bi bi-printer"></i> Print
                                        </button>
                                    </div>
                                </div>

                            </div>


                            <div class="card-body">
                                <?php if ($total_questions > 0): ?>
                                    <div class="row mb-4 g-3">
                                        <div class="col-md-3">
                                            <div class="card rounded-4 border-1 h-100" style="position: relative; overflow: hidden;">
                                                <div style="position: absolute; right: -20px; bottom: -70px; opacity: 0.1;">
                                                    <i class="bi bi-people" style="font-size: 140px; color: rgb(218, 119, 86);"></i>
                                                </div>
                                                <div class="card-body p-4">
                                                    <div>
                                                        <h6 class="mb-2">Total Peserta</h6>
                                                        <h3 class="m-0 fw-bold">
                                                            <?php echo mysqli_num_rows($result_peserta); ?>
                                                            <span class="fs-6 fw-normal text-muted">Siswa</span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card rounded-4 border-1 h-100" style="position: relative; overflow: hidden;">
                                                <div style="position: absolute; right: -20px; bottom: -70px; opacity: 0.1;">
                                                    <i class="bi bi-spellcheck" style="font-size: 140px; color: rgb(218, 119, 86);"></i>
                                                </div>
                                                <div class="card-body p-4">
                                                    <div>
                                                        <h6 class="mb-2">Rata-rata Nilai</h6>
                                                        <h3 class="m-0 fw-bold">
                                                            <?php echo number_format($rata_rata, 1); ?>
                                                            <span class="fs-6 fw-normal text-muted">/100</span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card rounded-4 border-1 h-100" style="position: relative; overflow: hidden;">
                                                <div style="position: absolute; right: -20px; bottom: -100px; opacity: 0.1;">
                                                    <i class="bi bi-trophy" style="font-size: 140px; color: rgb(218, 119, 86);"></i>
                                                </div>
                                                <div class="card-body p-4">
                                                    <div>
                                                        <h6 class="mb-2">Nilai Tertinggi</h6>
                                                        <h3 class="m-0 fw-bold">
                                                            <?php echo number_format($nilai_tertinggi, 1); ?>
                                                            <span class="fs-6 fw-normal text-muted">/100</span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card rounded-4 border-1 h-100" style="position: relative; overflow: hidden;">
                                                <div style="position: absolute; right: -20px; bottom: -100px; opacity: 0.1;">
                                                    <i class="bi bi-flag" style="font-size: 140px; color: rgb(218, 119, 86);"></i>
                                                </div>
                                                <div class="card-body p-4">
                                                    <div>
                                                        <h6 class="mb-2">Nilai Terendah</h6>
                                                        <h3 class="m-0 fw-bold">
                                                            <?php echo number_format($nilai_terendah, 1); ?>
                                                            <span class="fs-6 fw-normal text-muted">/100</span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Grafik Analisis Soal -->
                                    <div class="mt-4">
                                        <div class="card border" style="border-radius: 15px;">
                                            <div class="card-body p-4">
                                                <div class="chart-container">
                                                    <canvas id="soalChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <style>
                                        .chart-container {
                                            position: relative;
                                            height: 200px;
                                            overflow-x: auto;
                                            overflow-y: hidden;
                                            border-radius: 12px;
                                        }

                                        #soalChart {
                                            min-width: 100px;
                                            height: 100% !important;
                                        }

                                        .btn-group .btn {
                                            transition: all 0.3s ease;
                                        }

                                        .btn-group .btn.active {
                                            background-color: rgb(218, 119, 86);
                                            color: white;
                                            transform: scale(1.02);
                                            box-shadow: 0 2px 8px rgba(218, 119, 86, 0.3);
                                        }

                                        .btn-group .btn:not(.active) {
                                            color: #666;
                                        }

                                        .btn-group .btn:hover:not(.active) {
                                            background-color: rgba(218, 119, 86, 0.1);
                                            color: rgb(218, 119, 86);
                                        }

                                        /* Custom scrollbar for chart container */
                                        .chart-container::-webkit-scrollbar {
                                            height: 8px;
                                        }

                                        .chart-container::-webkit-scrollbar-track {
                                            background: #f1f1f1;
                                            border-radius: 4px;
                                        }

                                        .chart-container::-webkit-scrollbar-thumb {
                                            background: rgba(218, 119, 86, 0.5);
                                            border-radius: 4px;
                                        }

                                        .chart-container::-webkit-scrollbar-thumb:hover {
                                            background: rgba(218, 119, 86, 0.7);
                                        }
                                    </style>
                                    <!-- Modal Detail Soal -->
                                    <div class="modal fade" id="detailSoalModal" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-semibold">Detail Soal</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body px-4">
                                                    <!-- Pertanyaan dengan header yang jelas -->
                                                    <div class="card mb-4">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Pertanyaan</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="modalPertanyaan" class="text-dark"></div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <!-- Statistik jawaban -->
                                                        <div class="col-lg-6">
                                                            <div class="card mb-4">
                                                                <div class="card-header bg-light">
                                                                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistik Jawaban</h6>
                                                                </div>
                                                                <div class="card-body shadow-none border">
                                                                    <!-- Chart jawaban -->
                                                                    <div class="chart-container mb-3">
                                                                        <canvas id="jawabanChart" height="200"></canvas>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Pilihan jawaban -->
                                                        <div class="col-lg-6">
                                                            <div class="card mb-4">
                                                                <div class="card-header bg-light">
                                                                    <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Pilihan Jawaban</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <ul class="list-group" id="pilihanJawaban">
                                                                        <!-- Pilihan jawaban akan diisi oleh JavaScript -->
                                                                    </ul>
                                                                    <div class="mt-3 small text-muted">
                                                                        <i class="bi bi-info-circle"></i> Pilihan yang benar ditandai dengan warna.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <style>
                                        .modal-content {
                                            border-radius: 16px;
                                            border: none;
                                        }

                                        .modal-header {
                                            padding: 1.25rem 1.5rem;
                                        }

                                        .modal-header .btn-close {
                                            background-size: 12px;
                                            opacity: 0.5;
                                        }

                                        .card {
                                            border-radius: 12px;
                                            border: 1px solid rgba(0, 0, 0, 0.08);
                                            overflow: hidden;
                                        }

                                        .card-header {
                                            padding: 0.75rem 1.25rem;
                                            background-color: white !important;
                                            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                                        }

                                        .list-group-item {
                                            border: none;
                                            background: #f8f9fa;
                                            margin-bottom: 8px;
                                            border-radius: 10px !important;
                                            font-size: 0.95rem;
                                            padding: 0.75rem 1rem;
                                            display: flex;
                                            justify-content: space-between;
                                            align-items: center;
                                        }

                                        .list-group-item-success {
                                            background-color: rgba(218, 119, 86, 0.1);
                                            color: rgb(218, 119, 86);
                                            border-left: 4px solid rgb(218, 119, 86);
                                        }

                                        .badge {
                                            font-size: 0.75rem;
                                            font-weight: 500;
                                            padding: 0.35rem 0.65rem;
                                            border-radius: 8px;
                                        }

                                        .chart-container {
                                            position: relative;
                                            height: 200px;
                                        }

                                        .progress {
                                            height: 8px;
                                            border-radius: 4px;
                                            margin-bottom: 8px;
                                        }

                                        .progress-bar {
                                            border-radius: 4px;
                                        }

                                        @media (max-width: 992px) {
                                            .modal-dialog {
                                                max-width: 95%;
                                                margin: 1rem auto;
                                            }
                                        }
                                    </style>
                                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                    <script>
                                        // Data dari PHP
                                        const soalData = <?php echo json_encode($soal_stats); ?>;
                                        let soalChart;

                                        // Fungsi untuk menginisialisasi grafik batang
                                        function initSoalChart() {
                                            const ctx = document.getElementById('soalChart').getContext('2d');

                                            if (soalChart) {
                                                soalChart.destroy();
                                            }

                                            const labels = soalData.map((_, index) => `Soal ${index + 1}`);
                                            const correctData = soalData.map(item => parseInt(item.total_benar));
                                            const incorrectData = soalData.map(item => parseInt(item.total_salah));

                                            soalChart = new Chart(ctx, {
                                                type: 'bar',
                                                data: {
                                                    labels: labels,
                                                    datasets: [{
                                                            label: 'Jawaban Benar',
                                                            data: correctData,
                                                            backgroundColor: 'rgba(75, 192, 192, 0.8)',
                                                            borderColor: 'rgba(75, 192, 192, 1)',
                                                            borderWidth: 1
                                                        },
                                                        {
                                                            label: 'Jawaban Salah',
                                                            data: incorrectData,
                                                            backgroundColor: 'rgba(255, 99, 132, 0.8)',
                                                            borderColor: 'rgba(255, 99, 132, 1)',
                                                            borderWidth: 1
                                                        }
                                                    ]
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    scales: {
                                                        y: {
                                                            beginAtZero: true,
                                                            ticks: {
                                                                stepSize: 1
                                                            }
                                                        },
                                                        x: {
                                                            grid: {
                                                                display: false
                                                            }
                                                        }
                                                    },
                                                    plugins: {
                                                        tooltip: {
                                                            callbacks: {
                                                                label: function(context) {
                                                                    const total = parseInt(soalData[context.dataIndex].total_menjawab);
                                                                    const value = context.raw;
                                                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                                    return [
                                                                        `${context.dataset.label}: ${value} siswa`,
                                                                        `Persentase: ${percentage}%`
                                                                    ];
                                                                }
                                                            }
                                                        },
                                                        legend: {
                                                            position: 'top',
                                                            labels: {
                                                                usePointStyle: true,
                                                                padding: 20
                                                            }
                                                        }
                                                    },
                                                    onClick: (event, elements) => {
                                                        if (elements.length > 0) {
                                                            const index = elements[0].index;
                                                            showSoalDetail(soalData[index]);
                                                        }
                                                    }
                                                }
                                            });
                                        }

                                        // Inisialisasi grafik
                                        initSoalChart();

                                        // Fungsi untuk menampilkan detail soal
                                        function showSoalDetail(soal) {
                                            console.log('Detail soal:', soal);

                                            const modalElement = document.getElementById('detailSoalModal');
                                            if (!modalElement) {
                                                console.error('Modal element not found!');
                                                return;
                                            }

                                            // Inisialisasi modal menggunakan Bootstrap 5
                                            const modal = new bootstrap.Modal(modalElement);

                                            // Set pertanyaan
                                            let pertanyaanHtml = soal.pertanyaan;
                                            if (soal.gambar_soal) {
                                                pertanyaanHtml += `<br><img src="${soal.gambar_soal}" class="img-fluid mt-2" alt="Gambar Soal">`;
                                            }

                                            // Hitung persentase
                                            const totalMenjawab = parseInt(soal.total_menjawab) || 0;
                                            const persentaseBenar = totalMenjawab > 0 ?
                                                ((parseInt(soal.total_benar) / totalMenjawab) * 100).toFixed(1) : 0;
                                            const persentaseSalah = totalMenjawab > 0 ?
                                                ((parseInt(soal.total_salah) / totalMenjawab) * 100).toFixed(1) : 0;

                                            pertanyaanHtml += `
                                                    <div class="card mt-3" style="border-radius: 16px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                                        <div class="card-body p-4">
                                                            <h6 class="fw-bold mb-3" style="color: rgb(218, 119, 86);">Statistik Jawaban</h6>
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <span class="text-muted">Total Menjawab</span>
                                                                <span class="fw-bold">${totalMenjawab} siswa</span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="text-muted">Benar</span>
                                                                    <span class="fw-bold" style="color: rgb(218, 119, 86);">${soal.total_benar || 0} siswa</span>
                                                                </div>
                                                                <div class="progress" style="height: 8px; border-radius: 4px; background-color: #f0f0f0;">
                                                                    <div class="progress-bar" style="width: ${persentaseBenar}%; background-color: rgb(218, 119, 86); border-radius: 4px;"></div>
                                                                </div>
                                                                <div class="text-end mt-1">
                                                                    <small style="color: rgb(218, 119, 86);">${persentaseBenar}%</small>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="text-muted">Salah</span>
                                                                    <span class="fw-bold" style="color: #dc3545;">${soal.total_salah || 0} siswa</span>
                                                                </div>
                                                                <div class="progress" style="height: 8px; border-radius: 4px; background-color: #f0f0f0;">
                                                                    <div class="progress-bar bg-danger" style="width: ${persentaseSalah}%; border-radius: 4px;"></div>
                                                                </div>
                                                                <div class="text-end mt-1">
                                                                    <small class="text-danger">${persentaseSalah}%</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;

                                            const pertanyaanElement = document.getElementById('modalPertanyaan');
                                            if (pertanyaanElement) {
                                                pertanyaanElement.innerHTML = pertanyaanHtml;
                                            }

                                            // Set pilihan jawaban dengan informasi jumlah pemilih
                                            const pilihanList = document.getElementById('pilihanJawaban');
                                            if (pilihanList) {
                                                pilihanList.innerHTML = `
            <li class="list-group-item ${soal.jawaban_benar === 'A' ? 'list-group-item-success' : ''}">
                A. ${soal.jawaban_a} 
                <span class="badge bg-secondary float-end">${soal.jawab_a || 0} siswa</span>
            </li>
            <li class="list-group-item ${soal.jawaban_benar === 'B' ? 'list-group-item-success' : ''}">
                B. ${soal.jawaban_b}
                <span class="badge bg-secondary float-end">${soal.jawab_b || 0} siswa</span>
            </li>
            <li class="list-group-item ${soal.jawaban_benar === 'C' ? 'list-group-item-success' : ''}">
                C. ${soal.jawaban_c}
                <span class="badge bg-secondary float-end">${soal.jawab_c || 0} siswa</span>
            </li>
            <li class="list-group-item ${soal.jawaban_benar === 'D' ? 'list-group-item-success' : ''}">
                D. ${soal.jawaban_d}
                <span class="badge bg-secondary float-end">${soal.jawab_d || 0} siswa</span>
            </li>
        `;
                                            }

                                            // Perbarui chart setelah modal ditampilkan
                                            modalElement.addEventListener('shown.bs.modal', function() {
                                                updateJawabanChart(soal);
                                            });

                                            // Tampilkan modal
                                            modal.show();
                                        }

                                        function updateJawabanChart(soal) {
                                            const chartCanvas = document.getElementById('jawabanChart');
                                            if (!chartCanvas) {
                                                console.error('jawabanChart canvas not found!');
                                                return;
                                            }

                                            // Destroy chart lama jika ada
                                            if (jawabanChart instanceof Chart) {
                                                jawabanChart.destroy();
                                            }

                                            const ctx = chartCanvas.getContext('2d');
                                            jawabanChart = new Chart(ctx, {
                                                type: 'bar',
                                                data: {
                                                    labels: ['A', 'B', 'C', 'D'],
                                                    datasets: [{
                                                        label: 'Jumlah Siswa',
                                                        data: [
                                                            parseInt(soal.jawab_a) || 0,
                                                            parseInt(soal.jawab_b) || 0,
                                                            parseInt(soal.jawab_c) || 0,
                                                            parseInt(soal.jawab_d) || 0
                                                        ],
                                                        backgroundColor: [
                                                            'rgba(218, 119, 86, 0.9)',
                                                            'rgba(218, 119, 86, 0.7)',
                                                            'rgba(218, 119, 86, 0.5)',
                                                            'rgba(218, 119, 86, 0.3)'
                                                        ]
                                                    }]
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    plugins: {
                                                        legend: {
                                                            display: false // Sembunyikan legend karena tidak terlalu diperlukan
                                                        }
                                                    },
                                                    scales: {
                                                        y: {
                                                            beginAtZero: true,
                                                            ticks: {
                                                                stepSize: 1
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                    </script>
                                    <div class="table-responsive">
                                        <style>
                                            .ios-table {
                                                border-radius: 16px;
                                                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                                                background: white;
                                                overflow: hidden;
                                            }

                                            .ios-table thead th {
                                                background: #f8f9fa;
                                                font-weight: 600;
                                                font-size: 0.9rem;
                                                padding: 16px;
                                                border: none;
                                            }

                                            .ios-table tbody td {
                                                padding: 16px;
                                                border-bottom: 1px solid #f1f1f1;
                                                vertical-align: middle;
                                            }

                                            .ios-table tbody tr:last-child td {
                                                border-bottom: none;
                                            }

                                            .ios-badge {
                                                padding: 6px 12px;
                                                border-radius: 20px;
                                                font-size: 0.8rem;
                                                font-weight: 500;
                                            }

                                            .ios-btn {
                                                padding: 8px 16px;
                                                border-radius: 15px;
                                                border: none;
                                                font-size: 0.9rem;
                                                font-weight: 500;
                                                transition: all 0.2s;
                                            }

                                            .ios-btn:active {
                                                transform: scale(0.95);
                                            }

                                            .profile-pic {
                                                width: 40px;
                                                height: 40px;
                                                border-radius: 50%;
                                                object-fit: cover;
                                                margin-right: 12px;
                                            }

                                            .student-info {
                                                display: flex;
                                                align-items: center;
                                            }
                                        </style>

                                        <div class="ios-table">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Siswa</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Benar</th>
                                                        <th class="text-center">Salah</th>
                                                        <th class="text-center">Belum</th>
                                                        <th class="text-center">Nilai</th>
                                                        <th class="text-center">Detail</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $result_peserta = mysqli_query($koneksi, $query_peserta);
                                                    while ($peserta = mysqli_fetch_assoc($result_peserta)):
                                                        $unattempted = $total_questions - ($peserta['correct_answers'] + $peserta['wrong_answers']);
                                                        $nilai = ($peserta['correct_answers'] / $total_questions) * 100;
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <div class="student-info">
                                                                    <img src="<?php
                                                                                if (!empty($peserta['photo_type'])) {
                                                                                    if ($peserta['photo_type'] === 'avatar') {
                                                                                        echo $peserta['photo_url'];
                                                                                    } else if ($peserta['photo_type'] === 'upload') {
                                                                                        echo 'uploads/profil/' . $peserta['foto_profil'];
                                                                                    }
                                                                                } else {
                                                                                    echo 'assets/pp.png';
                                                                                }
                                                                                ?>"
                                                                        class="profile-pic rounded-circle border shadow-sm"
                                                                        alt="Profile"
                                                                        style="object-fit: cover;">
                                                                    <span class="fw-medium"><?php echo htmlspecialchars($peserta['nama']); ?></span>
                                                                </div>
                                                            </td>

                                                            <td class="text-center">
                                                                <?php if ($peserta['attempted_questions'] > 0): ?>
                                                                    <span class="ios-badge" style="background-color: #e8f5e9; color: #2e7d32;">Selesai</span>
                                                                <?php else: ?>
                                                                    <span class="ios-badge" style="background-color: #f5f5f5; color: #757575;">Belum</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <span style="color: #2e7d32; font-weight: 600;"><?php echo $peserta['correct_answers']; ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span style="color: #d32f2f; font-weight: 600;"><?php echo $peserta['wrong_answers']; ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span style="color: #757575; font-weight: 600;"><?php echo $unattempted; ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="ios-badge" style="background-color: #fff3e0; color: rgb(218, 119, 86);">
                                                                    <?php echo number_format($nilai, 1); ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <button class="ios-btn" style="background-color: rgb(218, 119, 86); color: white;"
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