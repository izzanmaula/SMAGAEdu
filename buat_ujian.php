<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

$userid = $_SESSION['userid'];
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);

// Ambil daftar kelas
$query_kelas = "SELECT * FROM kelas WHERE guru_id = '$userid'";
$result_kelas = mysqli_query($koneksi, $query_kelas);
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

    <title>Buat Ujian - SMAGAEdu</title>
    <style>
        body {
            font-family: merriweather;
        }

        .color-web {
            background-color: rgb(218, 119, 86);
        }

        .color-web:hover {
            background-color: rgb(218, 119, 86);
        }

        .btn {
            transition: background-color 0.3s ease;
            border: 0;
            border-radius: 5px;
        }


        .menu-samping {
            position: fixed;
            width: 13rem;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .menu-samping {
                display: none;
            }

            .col-utama {
                margin-left: 0 !important;
            }
        }

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

            <!-- Settings Modal -->
            <?php include 'includes/settings_modal.php'; ?>


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

    <!-- Validation Modal -->
    <div class="modal fade" id="validationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: rgb(218, 119, 86);"></i>
                    <h5 class="mt-3 fw-bold">Data Belum Lengkap</h5>
                    <p class="mb-4" id="validationMessage">Mohon lengkapi semua field yang diperlukan.</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn color-web flex-fill text-white px-4" data-bs-dismiss="modal" style="border-radius: 12px;">Ok, Saya Mengerti</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col p-4 col-utama">
        <!-- <h3 class="mb-4 fw-bold">Buat Ujian Baru</h3> -->
        <div class="row">
            <!-- Form Section (Left) -->
            <div class="col-md-6">
                <form action="proses_buat_ujian.php" method="POST">
                    <div class="card-container">
                        <!-- Card 1 -->
                        <div class="question-card" data-step="1">
                            <div class="card border-0 p-4 mb-3">
                                <!-- nama judul -->
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div>
                                        <h5 class="card-title mb-1 fw-bold" style="font-size: 1.5rem;">Berikan Identitas Ujian Anda</h5>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Apa judul ujian Anda?</label>
                                    <input type="text" class="form-control shadow-sm" name="judul"
                                        onchange="updatePreview(this)" data-preview="judul-preview" placeholder="Cth : Ujian Harian Bahasa Indonesia" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Apa deskripsi ujian Anda?</label>
                                    <textarea class="form-control shadow-sm" name="deskripsi" rows="3"
                                        onchange="updatePreview(this)" data-preview="deskripsi-preview" placeholder="Cth : Evaluasi pemahaman tentang Teks Eksposisi dan Struktur Paragraf, Bab 1-3"></textarea>
                                </div>

                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="question-card d-none" data-step="2">
                            <div class="card border-0 p-4 mb-3">
                                <!-- nama judul -->
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div>
                                        <h5 class="card-title mb-1 fw-bold" style="font-size: 1.5rem;">Kelas apa yang ingin Anda ujikan?</h5>
                                    </div>
                                </div>
                                <label for="kelas" class="form-label fw-semibold">Pilih Kelas</label>
                                <select class="form-select shadow-sm" id="kelas" name="kelas_id" required onchange="updatePreviewSelect(this)">
                                    <option value="">Pilih kelas</option>
                                    <?php while ($kelas = mysqli_fetch_assoc($result_kelas)) { ?>
                                        <option value="<?php echo $kelas['id']; ?>">
                                            <?php echo $kelas['tingkat'] . ' - ' . $kelas['mata_pelajaran']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <!-- // Tambahkan di dalam form -->
                                <input type="hidden" id="background_image" name="background_image" value="">
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="question-card d-none" data-step="3">
                            <div class="card rounded-3 border-0">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <span class="bi bi-stars" style="font-size: 70px; color:rgb(218, 119, 86)"></span>
                                        <div>
                                            <h5 class="card-title mb-1 fw-bold" style="font-size:1.5rem">Membuat Soal Tidak Pernah Semudah Ini</h5>
                                            <p style="font-size: 12px;" class="pb-0 mb-0">Anda dapat memberikan topik materi yang akan di ujikan, dengan ini SAGA akan membaca dan membantu Anda dalam membuat soal.</p>
                                        </div>
                                    </div>

                                    <div id="materi-container">
                                        <div class="materi-item mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">1</span>
                                                <input type="text" class="form-control" name="materi[]"
                                                    placeholder="Masukkan materi ujian" required>
                                                <button type="button" class="btn btn-outline-danger" onclick="hapusMateri(this)">
                                                    <span class="bi bi-trash"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-outline-secondary w-100 mt-3" onclick="tambahMateri()">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-plus-circle"></i>
                                            <span>Tambah Materi</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div class="question-card d-none" data-step="4">
                            <div class="row g-3 mb-4 p-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div>
                                        <h5 class="card-title mb-1 fw-bold" style="font-size: 1.5rem;">Tentukan waktu ujian Anda</h5>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="tanggal_mulai" class="form-label">Tanggal & Waktu Mulai</label>
                                    <input type="datetime-local" class="form-control shadow-sm"
                                        id="tanggal_mulai" name="tanggal_mulai" required
                                        onchange="calculateDuration()">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="tanggal_selesai" class="form-label">Tanggal & Waktu Selesai</label>
                                    <input type="datetime-local" class="form-control shadow-sm"
                                        id="tanggal_selesai" name="tanggal_selesai" required
                                        onchange="calculateDuration()">
                                </div>
                            </div>
                        </div>



                        <!-- Navigation Buttons -->
                        <div class="navigation-buttons d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary rounded-5" onclick="previousStep()">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" class="btn color-web rounded-5 text-white" onclick="nextStep()">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Preview Section (Right) -->
            <div class="col-md-6 d-none d-md-block">
                <div class="card border-0 p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-eye-fill text-muted"></i>
                        <h5 class="mb-0">Pratinjau Ujian</h5>
                    </div>

                    <style>
                        .class-card {
                            border-radius: 12px;
                            overflow: hidden;
                            background: white;
                            opacity: 0;
                            transform: translateY(20px);
                            animation: fadeInUp 0.5s ease forwards;
                            will-change: transform;
                        }

                        .class-card:hover {
                            transform: translateY(-5px);
                            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

                        .class-banner {
                            height: 160px;
                            background-size: cover;
                            background-position: center;
                            position: relative;
                            background-image: url('assets/bg.jpg');
                        }

                        .profile-circle {
                            width: 70px;
                            height: 70px;
                            border-radius: 50%;
                            border: 3px solid #fff;
                            position: absolute;
                            bottom: -35px;
                            right: 20px;
                            object-fit: cover;
                            background: #fff;
                        }

                        .class-content {
                            padding: 20px;
                        }

                        .class-title {
                            font-size: 18px;
                            font-weight: 600;
                        }

                        .btn-enter {
                            background: rgb(218, 119, 86);
                            color: #fff;
                            padding: 8px 20px;
                            border-radius: 6px;
                            display: inline-flex;
                            align-items: center;
                            transition: background 0.3s;
                        }

                        .btn-enter:hover {
                            background: rgb(198, 99, 66);
                            color: #fff;
                        }
                    </style>

                    <div class="class-card border">
                        <div class="class-banner">
                            <img src="<?php echo ($is_guru || $is_admin) ?
                                            (!empty($guru['foto_profil']) ? 'uploads/profil/' . $guru['foto_profil'] : 'assets/pp.png') : (!empty($siswa['photo_url']) ? $siswa['photo_url'] : 'assets/pp.png'); ?>"
                                 class="profile-circle">
                        </div>
                        <div class="class-content">
                            <h4 class="class-title mb-3" id="judul-preview">-</h4>

                            <div class="class-meta" style="font-size: 12px;">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-text me-2 text-muted"></i>
                                            <span class="text-secondary" id="deskripsi-preview">-</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-book me-2 text-muted"></i>
                                            <span class="text-dark" id="kelas-preview">-</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-list-check me-2 text-muted"></i>
                                            <span class="text-secondary" id="materi-preview">-</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-event me-2 text-muted"></i>
                                            <span class="text-secondary" id="waktu-preview">-</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock me-2 text-muted"></i>
                                            <span class="text-secondary" id="duration-info">
                                                Waktu ujian akan muncul setelah Anda memilih waktu mulai dan selesai
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="btn px-3 py-2 w-100" style="background: rgb(218, 119, 86); border: none; color: white;">
                                    <i class="bi bi-play-circle me-1"></i> Mulai Ujian
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .preview-section {
            position: relative;
        }

        .preview-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            font-weight: 600;
            display: block;
        }

        .preview-value {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            min-height: 45px;
            color: #495057;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .preview-value:empty::before {
            content: '-';
            color: #adb5bd;
        }

        .preview-value.highlight {
            background-color: #fff;
            border-color: rgb(218, 119, 86);
            box-shadow: 0 0 0 3px rgba(218, 119, 86, 0.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-container {
            position: relative;
            min-height: 500px;
            display: flex;
            flex-direction: column;
        }

        .question-card {
            flex: 1;
        }

        .navigation-buttons {
            position: sticky;
            bottom: 0;
            background-color: white;
            padding: 1rem 0;
            margin-top: auto;
            z-index: 100;
        }
    </style>

    <script>
        // script ambil waktu 
        function calculateDuration() {
            const start = new Date(document.getElementById('tanggal_mulai').value);
            const end = new Date(document.getElementById('tanggal_selesai').value);

            if (start && end) {
                const diffInMinutes = Math.floor((end - start) / (1000 * 60));
                if (diffInMinutes > 0) {
                    document.getElementById('duration-info').innerHTML =
                        `Durasi ujian selama ${diffInMinutes} menit`;
                } else {
                    document.getElementById('duration-info').innerHTML =
                        'Waktu selesai harus lebih besar dari waktu mulai';
                }
            }
        }




        let currentStep = 1;
        const totalSteps = document.querySelectorAll('.question-card').length;

        function updatePreview(element) {
            const previewId = element.dataset.preview;
            const previewElement = document.getElementById(previewId);
            const oldValue = previewElement.textContent;
            const newValue = element.value || '-';

            previewElement.textContent = newValue;

            if (oldValue !== newValue) {
                previewElement.style.animation = 'fadeIn 0.3s ease';
                previewElement.classList.add('highlight');

                setTimeout(() => {
                    previewElement.style.animation = '';
                    previewElement.classList.remove('highlight');
                }, 1500);
            }
        }

        function updateMateriPreview() {
            const materiInputs = document.querySelectorAll('input[name="materi[]"]');
            const materiValues = Array.from(materiInputs)
                .map((input, index) => input.value ? `${index + 1}. ${input.value}` : null)
                .filter(value => value);

            const previewElement = document.getElementById('materi-preview');
            previewElement.innerHTML = materiValues.length ? materiValues.join('<br>') : '-';

            previewElement.style.animation = 'fadeIn 0.3s ease';
            previewElement.classList.add('highlight');
            setTimeout(() => {
                previewElement.style.animation = '';
                previewElement.classList.remove('highlight');
            }, 1500);
        }

        function updateWaktuPreview() {
            const tanggalMulai = document.getElementById('tanggal_mulai').value;
            const tanggalSelesai = document.getElementById('tanggal_selesai').value;

            const formatDate = (date) => {
                return date ? new Date(date).toLocaleString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }) : '-';
            };

            const previewElement = document.getElementById('waktu-preview');
            previewElement.innerHTML = `
        Mulai: ${formatDate(tanggalMulai)}<br>
        Selesai: ${formatDate(tanggalSelesai)}
    `;

            previewElement.style.animation = 'fadeIn 0.3s ease';
            previewElement.classList.add('highlight');
            setTimeout(() => {
                previewElement.style.animation = '';
                previewElement.classList.remove('highlight');
            }, 1500);
        }

        // Add event listeners
        document.getElementById('tanggal_mulai').addEventListener('change', updateWaktuPreview);
        document.getElementById('tanggal_selesai').addEventListener('change', updateWaktuPreview);

        function showStep(step) {
            const cards = document.querySelectorAll('.question-card');
            const nextButton = document.querySelector('.navigation-buttons .color-web');

            cards.forEach(card => {
                card.style.opacity = '0';
                card.classList.add('d-none');
            });

            const currentCard = document.querySelector(`[data-step="${step}"]`);
            currentCard.classList.remove('d-none');

            // Change button text and type on last step
            if (step === totalSteps) {
                nextButton.textContent = '<i class="bi bi-send"></i>';
                nextButton.type = 'submit';
                nextButton.innerHTML = '<i class="bi bi-send"></i>'
            } else {
                nextButton.textContent = '<i class="bi bi-chevron-right"></i>';
                nextButton.type = 'button';
                nextButton.innerHTML = '<i class="bi bi-chevron-right"></i>';
            }

            setTimeout(() => {
                currentCard.style.opacity = '1';
                currentCard.style.transition = 'opacity 0.3s ease';
            }, 50);
        }

        function nextStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }

        // Initialize first step
        showStep(1);
    </script>

    <script>
// Simpan data background kelas dalam objek JavaScript
const kelasBackgrounds = <?php
    mysqli_data_seek($result_kelas, 0); // Reset pointer hasil query
    $backgrounds = array();
    while ($kelas = mysqli_fetch_assoc($result_kelas)) {
        $backgrounds[$kelas['id']] = $kelas['background_image'];
    }
    echo json_encode($backgrounds);
?>;


// Modifikasi fungsi updatePreviewSelect
// Modifikasi bagian perubahan background di fungsi updatePreviewSelect
function updatePreviewSelect(element) {
    const previewElement = document.getElementById('kelas-preview');
    const selectedOption = element.options[element.selectedIndex];
    const newValue = selectedOption.text || '-';

    previewElement.textContent = newValue;

    previewElement.style.animation = 'fadeIn 0.3s ease';
    previewElement.classList.add('highlight');

    // Update background berdasarkan kelas yang dipilih
    const kelasId = element.value;
    let backgroundUrl = 'assets/bg.jpg'; // Default background

    if (kelasId && kelasBackgrounds[kelasId] && kelasBackgrounds[kelasId] !== '') {
        backgroundUrl = kelasBackgrounds[kelasId];
    }

    // Set background image
    document.querySelector('.class-banner').style.backgroundImage = `url('${backgroundUrl}')`;
    
    // Update hidden input untuk background
    if (document.getElementById('background_image')) {
        document.getElementById('background_image').value = backgroundUrl;
    } else {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.id = 'background_image';
        hiddenInput.name = 'background_image';
        hiddenInput.value = backgroundUrl;
        document.querySelector('form').appendChild(hiddenInput);
    }

    setTimeout(() => {
        previewElement.style.animation = '';
        previewElement.classList.remove('highlight');
    }, 1500);
}
    </script>

    <!-- materi ujian -->
    <script>
        function tambahMateri() {
            const container = document.getElementById('materi-container');
            const materiCount = container.getElementsByClassName('materi-item').length + 1;

            const newMateri = document.createElement('div');
            newMateri.className = 'materi-item mb-2';
            newMateri.innerHTML = `
            <div class="input-group">
                <span class="input-group-text bg-light">${materiCount}</span>
                <input type="text" class="form-control" name="materi[]" placeholder="Masukkan materi ujian" required>
                <button type="button" class="btn btn-outline-danger" onclick="hapusMateri(this)">
                    <span class="bi bi-trash"></span>
                </button>
            </div>
        `;
            container.appendChild(newMateri);
            updateMateriNumbers();

            // Add event listener to new input
            newMateri.querySelector('input').addEventListener('input', updateMateriPreview);
        }

        document.querySelector('input[name="materi[]"]').addEventListener('input', updateMateriPreview);

        function hapusMateri(btn) {
            const materiItems = document.getElementsByClassName('materi-item');
            if (materiItems.length > 1) {
                btn.closest('.materi-item').remove();
                updateMateriNumbers();
                updateMateriPreview();
            } else {
                // Show modal instead of alert
                const modalHtml = `
                <div class="modal fade" id="materiMinimumModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 16px;">
                            <div class="modal-body text-center p-4">
                                <i class="bi bi-exclamation-circle" style="font-size: 3rem; color:red;"></i>
                                <h5 class="mt-3 fw-bold">Peringatan</h5>
                                <p class="mb-4">Harus ada minimal satu materi</p>
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn color-web flex-fill text-white px-4" data-bs-dismiss="modal" style="border-radius: 12px;">Ok</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

                // Append modal to body if it doesn't exist
                if (!document.getElementById('materiMinimumModal')) {
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                }

                // Show the modal
                const minimumModal = new bootstrap.Modal(document.getElementById('materiMinimumModal'));
                minimumModal.show();
            }
        }

        function updateMateriNumbers() {
            const materiItems = document.getElementsByClassName('materi-item');
            Array.from(materiItems).forEach((item, index) => {
                const numberSpan = item.querySelector('.input-group-text');
                numberSpan.textContent = index + 1;
            });
        }
    </script>

    <style>
        .materi-item .input-group-text {
            min-width: 45px;
            justify-content: center;
            font-weight: 500;
        }

        .materi-item .input-group {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
        }

        .materi-item .input-group:focus-within {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .materi-item .form-control {
            border-left: none;
        }

        .materi-item .form-control:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .btn-outline-danger {
            border: none;
        }

        .btn-outline-danger:hover {
            background-color: #ffe5e5;
        }

        .btn-outline-secondary {
            border: 1px dashed #6c757d;
            background-color: #f8f9fa;
        }

        .btn-outline-secondary:hover {
            background-color: #e9ecef;
            border: 1px dashed #6c757d;
            color: #6c757d;
        }
    </style>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const tanggalMulai = new Date(document.getElementById('tanggal_mulai').value);
            const tanggalSelesai = new Date(document.getElementById('tanggal_selesai').value);

            if (tanggalSelesai <= tanggalMulai) {
                e.preventDefault();
                alert('Tanggal & waktu selesai harus lebih besar dari tanggal & waktu mulai!');
                return;
            }

            const durasi = document.getElementById('durasi').value;
            const durasiMenit = (tanggalSelesai - tanggalMulai) / (1000 * 60);

            if (durasi > durasiMenit) {
                e.preventDefault();
                alert('Durasi ujian tidak boleh lebih besar dari rentang waktu ujian!');
            }
        });
    </script>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const form = e.target;
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                const invalidElements = form.querySelectorAll(':invalid');
                if (invalidElements.length > 0) {
                    const firstInvalidElement = invalidElements[0];
                    firstInvalidElement.focus();
                    const validationMessage = firstInvalidElement.validationMessage || 'Data Belum Lengkap';
                    document.getElementById('validationMessage').textContent = validationMessage;
                    const validationModal = new bootstrap.Modal(document.getElementById('validationModal'));
                    validationModal.show();
                }
            }
        });
    </script>
</body>

</html>