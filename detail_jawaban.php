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
    <div class="col p-4 col-utama bg-white">
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

        <div class="container">
            <div class="card border rounded-4">
                <div class="card-header p-4 pb-0 bg-white rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center pb-3">
                        <div>
                            <h3 class="mb-0"> <?php echo ucwords(strtolower(htmlspecialchars($siswa['nama']))); ?></h3>
                            <p class="text-muted mb-0">
                                <?php echo htmlspecialchars($ujian['judul']); ?> |<?php echo htmlspecialchars($ujian['mata_pelajaran']); ?>  
                            </p>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="detail_hasil_ujian.php?ujian_id=<?php echo $ujian_id; ?>"
                                class="btn btn-light btn-sm rounded-pill">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <button onclick="window.print()" class="btn btn-sm rounded-pill"
                                style="background-color: rgb(218, 119, 86); color: white;">
                                <i class="bi bi-printer me-1"></i>Cetak
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $no = 1;
                    while ($jawaban = mysqli_fetch_assoc($result_jawaban)) {
                        $is_correct = $jawaban['jawaban_siswa'] === $jawaban['jawaban_benar'];
                        $status_class = $jawaban['jawaban_siswa'] ? ($is_correct ? 'correct-answer' : 'wrong-answer') : '';
                    ?>
                        <div class="answer-box rounded-4 shadow-sm <?php echo $status_class; ?>">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0" style="color: rgb(218, 119, 86);">Soal <?php echo $no; ?></h5>
                                <?php if ($jawaban['jawaban_siswa']): ?>
                                    <span class="badge rounded-pill <?php echo $is_correct ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $is_correct ? 'Benar' : 'Salah'; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-secondary">Tidak Dijawab</span>
                                <?php endif; ?>
                            </div>

                            <p class="mb-3"><?php echo htmlspecialchars($jawaban['pertanyaan']); ?></p>

                            <?php
                            $options = ['A', 'B', 'C', 'D'];
                            foreach ($options as $option) {
                                $option_key = 'jawaban_' . strtolower($option);
                                $is_selected = $jawaban['jawaban_siswa'] === $option;
                                $is_correct_option = $jawaban['jawaban_benar'] === $option;

                                $option_class = '';
                                if ($is_selected && $is_correct) $option_class = 'selected-correct';
                                else if ($is_selected) $option_class = 'selected-wrong';
                                else if ($is_correct_option) $option_class = 'correct-option';
                            ?>
                                <div class="option rounded-3 <?php echo $option_class; ?>">
                                    <?php echo $option . '. ' . htmlspecialchars($jawaban[$option_key]); ?>
                                    <?php if ($is_selected): ?>
                                        <i class="bi bi-check-circle-fill"></i>
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
            <style>
                .answer-box {
                    padding: 20px;
                    margin-bottom: 20px;
                    background: #fff;
                    border: 1px solid rgba(0, 0, 0, 0.1);
                }

                .correct-answer {
                    background-color: rgba(52, 199, 89, 0.1);
                    border-color: rgba(52, 199, 89, 0.2);
                }

                .wrong-answer {
                    background-color: rgba(255, 59, 48, 0.1);
                    border-color: rgba(255, 59, 48, 0.2);
                }

                .option {
                    padding: 12px;
                    margin: 8px 0;
                    background: #f8f9fa;
                    border: 1px solid rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease;
                }

                .option:hover {
                    transform: translateX(5px);
                }

                .selected-correct {
                    background-color: rgba(52, 199, 89, 0.1);
                    border-color: rgba(52, 199, 89, 0.2);
                }

                .selected-wrong {
                    background-color: rgba(255, 59, 48, 0.1);
                    border-color: rgba(255, 59, 48, 0.2);
                }

                .correct-option {
                    color: rgb(52, 199, 89);
                }
            </style>
        </div>
        <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</body>

</html>