<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
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
    
    <title>Buat Ujian - SMAGAEdu</title>
    <style>
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
        }
    </style>
</head>
<body>
    <!-- row col untuk halaman utama -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 vh-100 p-4 shadow-sm menu-samping" style="background-color:rgb(238, 236, 226)">
                <div class="row gap-0">
                    <div class="ps-3 mb-3">
                        <a href="#" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
                            <img src="assets/smagaedu.png" alt="" width="30px">
                            <div>
                                <h1 class="display-5 p-0 m-0" style="font-size: 20px;">SMAGAEdu</h1>
                                <p class="p-0 m-0 text-muted" style="font-size: 12px;">LMS</p>
                            </div>
                        </a>
                    </div>  
                    <div class="col">
                        <a href="beranda_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/beranda_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Beranda</p>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="ujian_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded bg-white shadow-sm p-2">
                                <img src="assets/ujian_fill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Ujian</p>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="profil_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/profil_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Profil</p>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- Menu Gemini dan Bantuan -->
                <div class="row gap-0" style="margin-bottom: 14rem;">
                    <div class="col">
                        <a href="ai.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Gemini</p>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="bantuan.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/bantuan_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Bantuan</p>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- User Profile -->
                <div class="row dropdown">
                    <div class="btn d-flex align-items-center gap-3 p-2 rounded-3 border dropdown-toggle" 
                         style="background-color: #F8F8F7;" type="button" data-bs-toggle="dropdown">
                        <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                        <p class="p-0 m-0 text-truncate" style="font-size: 12px;"><?php echo $guru['namaLengkap']; ?></p>
                    </div>
                    <ul class="dropdown-menu" style="font-size: 12px;">
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col p-4 col-utama">
                <h3 class="mb-4">Buat Ujian Baru</h3>
                <div class="card">
                    <div class="card-body">
                        <form action="proses_buat_ujian.php" method="POST">
                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Ujian</label>
                                <input type="text" class="form-control" id="judul" name="judul" required>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Ujian</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="kelas" class="form-label">Pilih Kelas</label>
                                <select class="form-select" id="kelas" name="kelas_id" required>
                                    <option value="">Pilih Kelas</option>
                                    <?php while($kelas = mysqli_fetch_assoc($result_kelas)) { ?>
                                        <option value="<?php echo $kelas['id']; ?>">
                                            <?php echo $kelas['tingkat'] . ' - ' . $kelas['mata_pelajaran']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Materi Ujian</label>
                                <div class="card">
                                    <div class="card-body">
                                        <div id="materi-container">
                                            <div class="materi-item mb-2">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">1</span>
                                                    <input type="text" class="form-control" name="materi[]" placeholder="Masukkan materi ujian" required>
                                                    <button type="button" class="btn btn-outline-danger" onclick="hapusMateri(this)">
                                                        <img src="assets/delete.png" alt="Hapus" width="20px">
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Tombol tambah dengan style yang lebih baik -->
                                        <button type="button" class="btn btn-outline-secondary mt-3" onclick="tambahMateri()">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="assets/tambah.png" alt="Tambah" width="20px">
                                                <span>Tambah Materi</span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_mulai" class="form-label">Tanggal & Waktu Mulai</label>
                                    <input type="datetime-local" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_selesai" class="form-label">Tanggal & Waktu Selesai</label>
                                    <input type="datetime-local" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="ujian_guru.php" class="btn btn-secondary me-2">Kembali</a>
                                <button type="submit" class="btn color-web text-white">Buat Ujian & Lanjut Buat Soal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <img src="assets/delete.png" alt="Hapus" width="20px">
                </button>
            </div>
        `;
        container.appendChild(newMateri);
        updateMateriNumbers();
    }

    function hapusMateri(btn) {
        const materiItems = document.getElementsByClassName('materi-item');
        if (materiItems.length > 1) {
            btn.closest('.materi-item').remove();
            updateMateriNumbers();
        } else {
            alert('Harus ada minimal satu materi!');
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
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-radius: 6px;
}

.materi-item .input-group:focus-within {
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
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
</body>
</html>