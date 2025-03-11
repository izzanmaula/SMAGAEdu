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
        <h3 class="mb-4 fw-bold">Buat Ujian Baru</h3>
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
                                    <span class="bi bi-person-lines-fill" style="font-size: 70px; color:rgb(218, 119, 86)"></span>
                                    <div>
                                        <h5 class="card-title mb-1">Berikan Identitas Ujian Anda</h5>
                                        <p style="font-size: 12px;" class="pb-0 mb-0">Berikan judul dan deskripsi singkat untuk mempermudah Anda dalam mengkategorikan berkas ujian Anda.</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Apa judul ujian Anda?</label>
                                    <input type="text" class="form-control shadow-sm" name="judul"
                                        onchange="updatePreview(this)" data-preview="judul-preview" placeholder="Cth : Ujian Harian Bahasa Indonesia" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Apa deskripsi ujian Anda?</label>
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
                                    <span class="bi bi-people-fill" style="font-size: 70px; color:rgb(218, 119, 86)"></span>
                                    <div>
                                        <h5 class="card-title mb-1">Kelas apa yang ingin Anda ujikan?</h5>
                                        <p style="font-size: 12px;" class="pb-0 mb-0">Anda hanya dapat memberikan berkas ujian kepada kelas yang telah Anda buat dan diikuti oleh siswa.</p>
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
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="question-card d-none" data-step="3">
                            <div class="card rounded-3 border-0">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <span class="bi bi-stars" style="font-size: 70px; color:rgb(218, 119, 86)"></span>
                                        <div>
                                            <h5 class="card-title mb-1">SAGA siap membantu Anda</h5>
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
                                    <span class="bi bi-calendar-week-fill" style="font-size: 70px; color:rgb(218, 119, 86)"></span>
                                    <div>
                                        <h5 class="card-title mb-1">Tentukan waktu ujian Anda</h5>
                                        <p style="font-size: 12px;" class="pb-0 mb-0">Berikan jadwal soal Anda di ujikan, kami akan memblokir siswa mengerjakan soal Anda sampai waktu ujian yang telah Anda tentukan.</p>
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
                <div class="card border rounded-4 p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-eye-fill text-muted"></i>
                        <h5 class="mb-0">Pratinjau Ujian</h5>
                    </div>

                    <div class="preview-content">
                        <!-- Judul Section -->
                        <div class="preview-section mb-2">
                            <label class="preview-label">Judul Ujian</label>
                            <div class="preview-value" id="judul-preview">-</div>
                        </div>

                        <!-- Deskripsi Section -->
                        <div class="preview-section mb-2">
                            <label class="preview-label">Deskripsi</label>
                            <div class="preview-value" id="deskripsi-preview">-</div>
                        </div>

                        <!-- Kelas Section -->
                        <div class="preview-section mb-2">
                            <label class="preview-label">Kelas</label>
                            <div class="preview-value" id="kelas-preview">-</div>
                        </div>

                        <!-- Materi Section -->
                        <div class="preview-section mb-2">
                            <label class="preview-label">Materi Ujian</label>
                            <div class="preview-value" id="materi-preview">-</div>
                        </div>

                        <!-- Waktu Section -->
                        <div class="preview-section mb-2">
                            <label class="preview-label">Waktu Pelaksanaan</label>
                            <div class="preview-value" id="waktu-preview">-</div>
                        </div>

                        <!-- Durasi ujian -->
                        <div class="preview-section mb-2">
                            <div id="duration-info" class="text-muted" style="font-size: 12px;">
                                Waktu ujian akan muncul setelah Anda memilih waktu mulai dan selesai
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

        function updatePreviewSelect(element) {
            const previewElement = document.getElementById('kelas-preview');
            const selectedOption = element.options[element.selectedIndex];
            const newValue = selectedOption.text || '-';

            previewElement.textContent = newValue;

            previewElement.style.animation = 'fadeIn 0.3s ease';
            previewElement.classList.add('highlight');

            setTimeout(() => {
                previewElement.style.animation = '';
                previewElement.classList.remove('highlight');
            }, 1500);
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