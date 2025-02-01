<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

// Cek apakah ada ujian_id
if(!isset($_GET['ujian_id'])) {
    header("Location: ujian_guru.php");
    exit();
}

$ujian_id = $_GET['ujian_id'];
$userid = $_SESSION['userid'];

// Ambil data guru
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);

// Ambil data ujian beserta informasi kelas
// Perbaiki query untuk mengambil data yang benar
$query_ujian = "SELECT u.*, k.tingkat 
                FROM ujian u 
                INNER JOIN kelas k ON u.kelas_id = k.id 
                WHERE u.id = '$ujian_id' AND u.guru_id = '$userid'";
$result_ujian = mysqli_query($koneksi, $query_ujian);

// Cek apakah ujian ditemukan dan milik guru tersebut 
if(mysqli_num_rows($result_ujian) == 0) {
    header("Location: ujian_guru.php");
    exit();
}

$ujian = mysqli_fetch_assoc($result_ujian);


// Ambil jumlah soal yang sudah dibuat
$query_soal = "SELECT COUNT(*) as total_soal FROM bank_soal WHERE ujian_id = '$ujian_id'";
$result_soal = mysqli_query($koneksi, $query_soal);
$total_soal = mysqli_fetch_assoc($result_soal)['total_soal'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <scriptsrc="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    
    
    <title>Buat Soal - SMAGAEdu</title>
    <style>
        /* Style yang sama dengan sebelumnya */
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
        .btn:hover {
            background-color: rgb(219, 106, 68);
        }
        .menu-samping {
            position: fixed;
            width: 13rem;
            z-index: 1000;
        }
        .col-utama {
            margin-left: 13rem;
        }
        @media (max-width: 768px) {
            .menu-samping {
                display: none;
            }
            .col-utama {
                margin-left: 0 !important;
            }
        }
        .soal-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.1);
        }
        .ai-button {
        position: relative;
        width: 45px;
        height: 45px;
    }
    
    .ai-loader {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(218, 119, 86, 0.9);
        border-radius: 12px;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .ai-loader.show {
        display: flex;
    }

    .ai-loader::after {
        content: '';
        width: 20px;
        height: 20px;
        border: 3px solid #fff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spinner 0.6s linear infinite;
    }

    @keyframes spinner {
        to {
            transform: rotate(360deg);
        }
    }

    .generate-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        
    }

    .generate-message {
        color: white;
        text-align: center;
        padding: 20px;
        border-radius: 12px;
    }

    .generate-message i {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .generate-overlay.fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
        }
        .generate-overlay.fade-out {
            animation: fadeOut 0.5s ease-in-out forwards;
    }
        @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }


    .pulse {
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
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


            <!-- Main Content -->
            <div class="col p-4 col-utama">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Buat Soal</h3>
                    <div class="d-flex gap-1">
                        <!-- Import dari Word button -->
                        <button type="button" 
                                class="rounded-4 btn color-web text-white d-flex align-items-center justify-content-center gap-2 w-100 w-md-auto" 
                                data-bs-toggle="modal" 
                                data-bs-target="#uploadSoalModal">
                            <i class="bi bi-file-earmark-word"></i>
                            <span class="d-none d-md-inline" style="font-size: 12px;">Import dari Word</span>
                        </button>

                        <!-- Buat Soal dengan AI button -->
                        <button type="button" 
                                class="rounded-4 btn color-web text-white d-flex align-items-center justify-content-center gap-2 w-100 w-md-auto" 
                                data-bs-toggle="modal" 
                                data-bs-target="#aiSoalModal">
                            <i class="bi bi-stars"></i>
                            <span class="d-none d-md-inline" style="font-size: 12px;">Bantuan SMAGA AI</span>
                        </button>
                        
                        <!-- Tambah Soal Manual button -->
                        <button type="button" 
                                class="rounded-4 btn color-web text-white d-flex align-items-center justify-content-center gap-2 w-100 w-md-auto" 
                                data-bs-toggle="modal" 
                                onclick="pilihTipeSoal('pilihan_ganda')">
                            <i class="bi bi-plus-lg"></i>
                            <span class="d-none d-md-inline" style="font-size: 12px;">Tambah Soal</span>
                        </button>
                    </div>
                </div>

                <!-- Info Ujian -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-0 mb-md-3">
                            <h5 class="card-title m-0">
                                <i class="bi bi-journal-text me-2"></i>
                                <?php echo htmlspecialchars($ujian['judul']); ?>
                            </h5>
                            <!-- Toggle button for mobile -->
                            <button class="btn d-md-none" style="background-color: rgb(218, 119, 86);" type="button" data-bs-toggle="collapse" data-bs-target="#detailUjian" aria-expanded="false" aria-controls="detailUjian">
                                <i class="bi bi-chevron-down text-white"></i>
                            </button>
                        </div>

                        <!-- Wrap content in collapse div -->
                        <div class="collapse d-md-block" id="detailUjian">
                            <div class="row g-4">
                                <!-- Informasi Utama -->
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mt-3 mb-3">
                                        <i class="bi bi-book me-3 fs-5" style="color: rgb(218, 119, 86);"></i>
                                        <div>
                                            <p class="text-muted mb-1" style="font-size: 12px;">Mata Pelajaran</p>
                                            <p class="mb-0"><?php echo htmlspecialchars($ujian['mata_pelajaran']); ?></p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-3">
                                        <i class="bi bi-card-text me-3 fs-5" style="color: rgb(218, 119, 86);"></i>
                                        <div>
                                            <p class="text-muted mb-1" style="font-size: 12px;">Deskripsi</p>
                                            <p class="mb-0"><?php echo htmlspecialchars($ujian['deskripsi']); ?></p>
                                        </div>
                                    </div>

                                    <?php if(!empty($ujian['materi'])): ?>
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-list-check me-3 fs-5" style="color: rgb(218, 119, 86);"></i>
                                        <div>
                                            <p class="text-muted mb-2" style="font-size: 12px;">Materi Ujian</p>
                                            <ul class="list-unstyled mb-0">
                                                <?php 
                                                $materi_list = json_decode($ujian['materi'], true);
                                                if(is_array($materi_list)) {
                                                    foreach($materi_list as $materi) {
                                                        echo "<li class='mb-1'><i class='bi bi-dot me-1'></i>" . htmlspecialchars($materi) . "</li>";
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Informasi Waktu -->
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="bi bi-calendar-event me-3 fs-5" style="color: rgb(218, 119, 86) ;"></i>
                                        <div>
                                            <p class="text-muted mb-1" style="font-size: 12px;">Waktu Pelaksanaan</p>
                                            <p class="mb-1">
                                                Mulai: <?php echo date('d M Y - H:i', strtotime($ujian['tanggal_mulai'])); ?> WIB
                                            </p>
                                            <p class="mb-0">
                                                Selesai: <?php echo date('d M Y - H:i', strtotime($ujian['tanggal_selesai'])); ?> WIB
                                            </p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-3">
                                        <i class="bi bi-hourglass-split me-3 fs-5" style="color: rgb(218, 119, 86) ;"></i>
                                        <div>
                                            <p class="text-muted mb-1" style="font-size: 12px;">Durasi</p>
                                            <p class="mb-0"><?php echo $ujian['durasi']; ?> menit</p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-question-circle me-3 fs-5" style="color: rgb(218, 119, 86) ;"></i>
                                        <div>
                                            <p class="text-muted mb-1" style="font-size: 12px;">Total Soal</p>
                                            <p class="mb-0"><?php echo $total_soal; ?> soal</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // Toggle icon when collapse is shown/hidden
                    document.getElementById('detailUjian').addEventListener('show.bs.collapse', function () {
                        document.querySelector('[data-bs-target="#detailUjian"] i').classList.replace('bi-chevron-down', 'bi-chevron-up');
                    });
                    
                    document.getElementById('detailUjian').addEventListener('hide.bs.collapse', function () {
                        document.querySelector('[data-bs-target="#detailUjian"] i').classList.replace('bi-chevron-up', 'bi-chevron-down');
                    });
                </script>

<!-- Daftar Soal -->
<div id="daftarSoal" class="row">
    <?php
    $query_soal_list = "SELECT * FROM bank_soal WHERE ujian_id = '$ujian_id' ORDER BY id ASC";
    $result_soal_list = mysqli_query($koneksi, $query_soal_list);
    $no = 1;
    while($soal = mysqli_fetch_assoc($result_soal_list)) {
    ?>
    <div class="col-12">
        <div class="soal-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h5>Soal <?php echo $no++; ?></h5>
                <div>
                    <button class="btn btn-sm me-2" style="background-color: rgb(218, 119, 86);" onclick="editSoal(<?php echo $soal['id']; ?>)">
                        <i class="bi bi-pencil" style="color: white;"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" style="background-color: rgb(218, 119, 86);" onclick="hapusSoal(<?php echo $soal['id']; ?>)">
                        <i class="bi bi-trash" style="color: white;"></i>
                    </button>
                </div>
            </div>

            <?php if(!empty($soal['gambar_soal'])): ?>
            <div class="mb-3">
                <img src="<?php echo htmlspecialchars($soal['gambar_soal']); ?>" class="img-fluid rounded" style="max-height: 200px">
            </div>
            <?php endif; ?>

            <p><?php echo htmlspecialchars($soal['pertanyaan']); ?></p>
            

            <?php if($soal['jenis_soal'] == 'pilihan_ganda'): ?>
                <div class="ms-3">
                    <div>A. <span class="<?php echo $soal['jawaban_benar'] == 'A' ? 'bg-success text-white fw-bold px-1 rounded' : ''; ?>"><?php echo htmlspecialchars($soal['jawaban_a']); ?></span></div>
                    <div>B. <span class="<?php echo $soal['jawaban_benar'] == 'B' ? 'bg-success text-white fw-bold px-1 rounded' : ''; ?>"><?php echo htmlspecialchars($soal['jawaban_b']); ?></span></div>
                    <div>C. <span class="<?php echo $soal['jawaban_benar'] == 'C' ? 'bg-success text-white fw-bold px-1 rounded' : ''; ?>"><?php echo htmlspecialchars($soal['jawaban_c']); ?></span></div>
                    <div>D. <span class="<?php echo $soal['jawaban_benar'] == 'D' ? 'bg-success text-white fw-bold px-1 rounded' : ''; ?>"><?php echo htmlspecialchars($soal['jawaban_d']); ?></span></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php } ?>
</div>

                <!-- Modal Pilih Tipe Soal -->
                <div class="modal fade" id="pilihTipeModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Pilih Tipe Soal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body d-flex gap-2">
                                <button class="btn color-web text-white flex-grow-1" onclick="pilihTipeSoal('pilihan_ganda')">
                                    Pilihan Ganda
                                </button>
                                <button class="btn btn-secondary flex-grow-1" onclick="pilihTipeSoal('uraian')">
                                    Uraian
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Form Soal -->
                <div class="modal fade" id="formSoalModal" data-bs-backdrop="static" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <!-- Isi modal akan diload melalui JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadSoalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Soal Word</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            <form id="formUploadSoal" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="fileSoal" class="form-label">Upload File Soal (.docx)</label>
                    <input type="file" class="form-control" id="fileSoal" name="fileSoal" accept=".docx" required>
                </div>
                <div class="mb-3">
                    <label for="fileJawaban" class="form-label">Upload File Kunci Jawaban (.docx)</label>
                    <input type="file" class="form-control" id="fileJawaban" name="fileJawaban" accept=".docx" required>
                </div>
                <input type="hidden" name="ujian_id" value="<?php echo $ujian_id; ?>">
            </form>                        
            <div class="alert alert-info">
                    <small>
                        <i class="bi bi-info-circle"></i>
                        Format File:<br>
                        <ul class="mb-0">
                            <li>File Soal: Berisi soal dan pilihan jawaban</li>
                            <li>File Jawaban: Berisi kunci jawaban (misal: 1. A, 2. B, dll)</li>
                        </ul>
                    </small>
                </div>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn color-web text-white" id="uploadButton">
                    <span>Upload</span>
                    <div class="spinner-border spinner-border-sm ms-2 d-none" role="status"></div>
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Tambahkan modal untuk Buat Soal dengan AI -->
    <div class="modal fade" id="aiSoalModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Soal dengan AI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAiSoal">
                        <div class="mb-3">
                            <label class="form-label">Jumlah Soal</label>
                            <input type="number" class="form-control" name="jumlah_soal" min="1" max="100" value="10">
                            <div class="form-text" style="font-size: 12px;">Semakin banyak soal semakin banyak waktu pembuatan soal, tentukan dengan bijak.</div>
                        </div>          
                        
                        <div class="mb-3">
                            <label class="form-label">Kesulitan</label>
                            <select class="form-select" name="kesulitan">
                                <option value="pilihan_ganda">Mudah</option>
                                <option value="pilihan_ganda">Sedang</option>
                                <option value="pilihan_ganda">Sulit</option>
                                <option value="pilihan_ganda">Sangat Sulit</option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Tipe Soal</label>
                            <select class="form-select" name="tipe_soal">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer btn-group">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn color-web text-white" onclick="generateMultipleSoal()">
                        <span>Generate Soal</span>
                        <div class="spinner-border spinner-border-sm ms-2 d-none" role="status"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- overlay untuk generate soal -->
<div id="generateOverlay" class="generate-overlay">
    <div class="generate-message">
        <img src="assets/ai.gif" style="width:100px;" alt="Deskripsi gambar">
        <h5 class="mb-1">Sedang Membuat Soal</h5>
        <p class="mb-0">Mohon tunggu sebentar</p>
    </div>
</div>

<style>

</style>
    <!-- script untuk generate multiple soal -->
    <script>
    async function generateMultipleSoal() {
        const form = document.getElementById('formAiSoal');
        const formData = new FormData(form);
        const button = form.closest('.modal').querySelector('.modal-footer .btn.color-web');
        const spinner = button.querySelector('.spinner-border');
        const buttonText = button.querySelector('span');
        const overlay = document.getElementById('generateOverlay');

        try {
            // Update button state
            button.disabled = true;
            spinner.classList.remove('d-none');
            buttonText.textContent = 'Generating...';

            // overlay muncul
            overlay.classList.add('fade-in');
            overlay.style.display = 'flex';

            const response = await fetch('generate_multiple_soal.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    jumlah_soal: formData.get('jumlah_soal'),
                    tipe_soal: formData.get('tipe_soal'),
                    kesulitan: formData.get('kesulitan'),
                    ujian_id: <?php echo $ujian_id; ?>,
                    mata_pelajaran: "<?php echo addslashes($ujian['mata_pelajaran']); ?>",
                    tingkat: "<?php echo $ujian['tingkat']; ?>"
                }).toString()
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Response from server:', result);
            
            if(result.status === 'success') {
                console.log('Soal berhasil di-generate:', result.data);
                location.reload();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            
            // Sembunyikan overlay jika terjadi error
            overlay.classList.remove('fade-in');
            overlay.classList.add('fade-out');
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.remove('fade-out');
            }, 500);
            
            alert('Gagal generate soal: ' + error.message);
        } finally {
            // Reset button state
            button.disabled = false;
            spinner.classList.add('d-none');
            buttonText.textContent = 'Generate Soal';

            // Sembunyikan overlay dengan animasi fade out
            overlay.classList.remove('fade-in');
            overlay.classList.add('fade-out');
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.remove('fade-out');
            }, 500);
            
            // Tutup modal
            const modal = document.getElementById('aiSoalModal');
            const modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
        }
    }
</script>

<!-- script upload soal pake word -->
 <script>
// Separate script section
document.getElementById('uploadButton').addEventListener('click', function(e) {
    const form = document.getElementById('formUploadSoal');
    const formData = new FormData(form);
    formData.append('ujian_id', '<?php echo $ujian_id; ?>');

    // Log form data
    for (let pair of formData.entries()) {
    console.log(pair[0], pair[1]); 
    }


    const button = this;
    const spinner = button.querySelector('.spinner-border');
    const buttonText = button.querySelector('span');
    const overlay = document.getElementById('generateOverlay');

    button.disabled = true;
    spinner.classList.remove('d-none');
    buttonText.textContent = 'Uploading...';
    overlay.style.display = 'flex';

    fetch('process_word.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            location.reload();
        } else {
            console.error('Upload error:', result);
            throw new Error(result.message || 'Gagal mengupload file');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal mengupload file: ' + error.message);
    })
    .finally(() => {
        button.disabled = false;
        spinner.classList.add('d-none');
        buttonText.textContent = 'Upload';
        overlay.style.display = 'none';
        
        const modal = document.getElementById('uploadSoalModal');
        const modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();
    });
});
</script>

    <script>
        let currentTipeSoal = '';
        
        function pilihTipeSoal(tipe) {
            currentTipeSoal = tipe;
            loadFormSoal();
            $('#pilihTipeModal').modal('hide');
            $('#formSoalModal').modal('show');
        }

        // Tambahkan function untuk edit soal
        async function editSoal(id) {
            try {
                // Ambil data soal
                const response = await fetch(`get_soal.php?id=${id}`);
                const data = await response.json();
                
                if(data.status === 'success') {
                    currentTipeSoal = data.soal.jenis_soal;
                    loadFormSoal(true); // Parameter true menandakan ini mode edit
                    
                    // Isi form dengan data yang ada
                    const form = document.getElementById('formSoal');
                    form.querySelector('[name="pertanyaan"]').value = data.soal.pertanyaan;
                    form.querySelector('[name="soal_id"]').value = id; // Hidden input untuk ID soal
                    
                    if(data.soal.jenis_soal === 'pilihan_ganda') {
                        form.querySelector('[name="jawaban_a"]').value = data.soal.jawaban_a;
                        form.querySelector('[name="jawaban_b"]').value = data.soal.jawaban_b;
                        form.querySelector('[name="jawaban_c"]').value = data.soal.jawaban_c;
                        form.querySelector('[name="jawaban_d"]').value = data.soal.jawaban_d;
                        form.querySelector('[name="jawaban_benar"]').value = data.soal.jawaban_benar;
                    }
                    
                    // Tampilkan modal
                    const formModal = new bootstrap.Modal(document.querySelector('#formSoalModal'));
                    formModal.show();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                alert('Gagal mengambil data soal: ' + error.message);
            }
        }


    function loadFormSoal(isEdit = false) {
    const modalContent = document.querySelector('#formSoalModal .modal-content');
    modalContent.style.position = 'relative'; // Tambahkan ini

    // Tambahkan overlay di awal konten modal
    const overlayHtml = `
        <div class="generate-overlay">
            <div class="generate-message">
            <img src="assets/ai.gif" style="width:100px;" alt="Deskripsi gambar">
                <h5 class="mb-1">Sedang Membuat Soal</h5>
                <p class="mb-0">Mohon tunggu sebentar</p>
            </div>
        </div>
    `;

    if(currentTipeSoal === 'pilihan_ganda') {
        modalContent.innerHTML = overlayHtml + `
            <div class="modal-header bg-white border-0">
                <h5 class="modal-title fw-bold">${isEdit ? 'Edit' : 'Buat'} Soal Pilihan Ganda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <form id="formSoal">
                    <input type="hidden" name="soal_id" value="">
                   
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Pertanyaan</label>
                        <div class="d-flex gap-2 position-relative">
                            <textarea class="form-control" name="pertanyaan" rows="3" required style="border-radius: 12px; resize: none;"></textarea>
                                <button type="button" class="btn color-web text-white ai-button" onclick="generateSoal('${currentTipeSoal}')">
                                    <i class="bi bi-stars"></i>
                                    <div class="ai-loader"></div>
                                </button>                                
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Gambar Soal (Opsional)</label>
                        <div class="d-flex gap-2">
                            <input type="file" class="form-control" name="gambar_soal" accept="image/*">
                            <div id="preview_container" class="d-none">
                                <img id="image_preview" class="img-fluid mb-2" style="max-height: 200px">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">Hapus</button>
                            </div>
                        </div>
                    </div>
 

                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Pilihan Jawaban</label>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-white fw-bold">A</span>
                                    <input type="text" class="form-control" name="jawaban_a" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-white fw-bold">B</span>
                                    <input type="text" class="form-control" name="jawaban_b" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-white fw-bold">C</span>
                                    <input type="text" class="form-control" name="jawaban_c" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-white fw-bold">D</span>
                                    <input type="text" class="form-control" name="jawaban_d" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Jawaban Benar</label>
                        <select class="form-select" name="jawaban_benar" required style="border-radius: 12px;">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                <button type="button" class="btn color-web text-white" onclick="simpanSoal()" style="border-radius: 12px;">Simpan</button>
            </div>
        `;
    } else {
    modalContent.innerHTML = overlayHtml + `
        <div class="modal-header bg-white border-0">
            <h5 class="modal-title fw-bold">${isEdit ? 'Edit' : 'Buat'} Soal Uraian</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body px-4">
            <form id="formSoal">
                <input type="hidden" name="soal_id" value="">
                <div class="mb-4">
                    <label class="form-label small fw-bold">Pertanyaan</label>
                    <div class="d-flex gap-2">
                        <textarea class="form-control" name="pertanyaan" rows="3" required style="border-radius: 12px; resize: none;"></textarea>
                        <button type="button" class="btn color-web text-white ai-button" onclick="generateSoal('uraian')" style="border-radius: 12px; height: 45px;">
                            <i class="bi bi-stars"></i>
                            <div class="ai-loader"></div>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer bg-white border-0">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
            <button type="button" class="btn color-web text-white" onclick="simpanSoal()" style="border-radius: 12px;">Simpan</button>
        </div>
    `;
}

    // Tambahkan style untuk modal
    modalContent.style.borderRadius = '16px';
    modalContent.style.overflow = 'hidden';

    // Tambahkan style ini setelah modal content
    const style = document.createElement('style');
    style.textContent = `
        .dot-pulse {
            position: relative;
            left: -9999px;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: rgb(218, 119, 86);
            color: rgb(218, 119, 86);
            box-shadow: 9999px 0 0 -5px;
            animation: dot-pulse 1.5s infinite linear;
            animation-delay: 0.25s;
        }
        .dot-pulse::before, .dot-pulse::after {
            content: '';
            display: inline-block;
            position: absolute;
            top: 0;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: rgb(218, 119, 86);
            color: rgb(218, 119, 86);
        }
        .dot-pulse::before {
            box-shadow: 9984px 0 0 -5px;
            animation: dot-pulse-before 1.5s infinite linear;
            animation-delay: 0s;
        }
        .dot-pulse::after {
            box-shadow: 10014px 0 0 -5px;
            animation: dot-pulse-after 1.5s infinite linear;
            animation-delay: 0.5s;
        }
        @keyframes dot-pulse-before {
            0% { box-shadow: 9984px 0 0 -5px; }
            30% { box-shadow: 9984px 0 0 2px; }
            60%, 100% { box-shadow: 9984px 0 0 -5px; }
        }
        @keyframes dot-pulse {
            0% { box-shadow: 9999px 0 0 -5px; }
            30% { box-shadow: 9999px 0 0 2px; }
            60%, 100% { box-shadow: 9999px 0 0 -5px; }
        }
        @keyframes dot-pulse-after {
            0% { box-shadow: 10014px 0 0 -5px; }
            30% { box-shadow: 10014px 0 0 2px; }
            60%, 100% { box-shadow: 10014px 0 0 -5px; }
        }
    `;
    document.head.appendChild(style);
}

// Add after existing loadFormSoal() code:
function previewImage(input) {
    const preview = document.getElementById('image_preview');
    const container = document.getElementById('preview_container');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const fileInput = document.querySelector('input[name="gambar_soal"]');
    const preview = document.getElementById('image_preview');
    const container = document.getElementById('preview_container');
    
    fileInput.value = '';
    preview.src = '';
    container.classList.add('d-none');
}
    




// Modifikasi fungsi generateSoal untuk menambahkan animasi
async function generateSoal(jenis) {
    try {
        // Show loading
        const button = document.querySelector('.ai-button');
        const icon = button.querySelector('.bi-stars');
        const loader = button.querySelector('.ai-loader');
        const overlay = document.getElementById('generateOverlay');
        
        button.disabled = true;
        icon.style.display = 'none';
        loader.classList.add('show');

        overlay.classList.add('fade-in');
        overlay.style.display = 'flex';


        const response = await fetch('generate_soal.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                jenis_soal: jenis,
                ujian_id: <?php echo $ujian_id; ?>,
                mata_pelajaran: "<?php echo addslashes($ujian['mata_pelajaran']); ?>",
                tingkat: "<?php echo $ujian['tingkat']; ?>"
            }).toString()
        });

        const result = await response.json();
        
        if(result.status === 'success') {
            const form = document.getElementById('formSoal');
            form.querySelector('[name="pertanyaan"]').value = result.data.pertanyaan;
            
            if(jenis === 'pilihan_ganda') {
                form.querySelector('[name="jawaban_a"]').value = result.data.jawaban_a;
                form.querySelector('[name="jawaban_b"]').value = result.data.jawaban_b;
                form.querySelector('[name="jawaban_c"]').value = result.data.jawaban_c;
                form.querySelector('[name="jawaban_d"]').value = result.data.jawaban_d;
                form.querySelector('[name="jawaban_benar"]').value = result.data.jawaban_benar.toUpperCase();
            }
            
            // Sembunyikan overlay dengan animasi fade out
            overlay.classList.remove('fade-in');
            overlay.classList.add('fade-out');
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.remove('fade-out');
            }, 500);


        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        alert('Gagal generate soal: ' + error.message);
    } finally {
        // Hide loading
        const button = document.querySelector('.ai-button');
        const icon = button.querySelector('.bi-stars');
        const loader = button.querySelector('.ai-loader');



        loader.classList.remove('show');
        icon.style.display = 'block';
        button.disabled = false;
    }
}


        // Modifikasi fungsi simpanSoal untuk mendukung edit
        async function simpanSoal() {
            const form = document.getElementById('formSoal');
            const formData = new FormData(form);
            formData.append('jenis_soal', currentTipeSoal);
            formData.append('ujian_id', <?php echo $ujian_id; ?>);

            const soalId = form.querySelector('[name="soal_id"]').value;
            const url = soalId ? 'update_soal.php' : 'simpan_soal.php';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if(result.status === 'success') {
                    location.reload(); // Refresh halaman untuk menampilkan perubahan
                } else {
                    alert('Gagal menyimpan soal: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            }
        }


        async function hapusSoal(id) {
            if(confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
                try {
                    const response = await fetch('hapus_soal.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ soal_id: id })
                    });

                    const result = await response.json();
                    if(result.status === 'success') {
                        location.reload();
                    } else {
                        alert('Gagal menghapus soal: ' + result.message);
                    }
                } catch (error) {
                    alert('Terjadi kesalahan: ' + error.message);
                }
            }
        }
    </script>
</body>
</html>