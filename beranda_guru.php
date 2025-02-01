<?php
session_start();
require "koneksi.php";
if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}
// Tambahkan debug info di sini
// echo "Debug seluruh session:<br>";
// var_dump($_SESSION);
// echo "<br><br>";

// Ambil userid dari session
$userid = $_SESSION['userid'];

// Ambil data guru
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);

?>

<?php if(isset($_SESSION['show_siswa_modal']) && $_SESSION['show_siswa_modal']): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    tampilkanModalPilihSiswa(<?php echo $_SESSION['temp_kelas_id']; ?>, '<?php echo $_SESSION['temp_tingkat']; ?>');
    
    // Hapus session setelah modal ditampilkan
    <?php 
    unset($_SESSION['show_siswa_modal']);
    unset($_SESSION['temp_kelas_id']);
    unset($_SESSION['temp_tingkat']);
    ?>
});

// Tambahkan event listener untuk modal
document.getElementById('modal_pilih_siswa').addEventListener('hidden.bs.modal', function () {
    // Ketika modal ditutup (baik dengan tombol close atau backdrop)
    window.location.href = 'beranda_guru.php';
});
</script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <title>Beranda - SMAGAEdu</title>
</head>
<style>
        .custom-card {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);        
        }

        @media (max-width: 768px) {
            .menu-samping {
                display: none;
            }
            body {
                padding-top: 60px;
            }
            .custom-card {
                max-width: 100%;
            }
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
        body{ 
            font-family: merriweather;
        }
        .color-web {
            background-color: rgb(218, 119, 86);
            transition: background-color 0.3s ease;
        }

        .color-web:hover{
            background-color: rgb(206, 100, 65);
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
            <div class="col p-4 col-utama mt-1 mt-md-0">
                <style>
                    .col-utama {
                        margin-left: 0;
                    }
                    @media (min-width: 768px) {
                        .col-utama {
                            margin-left: 13rem;
                        }
                    }
                    
                    /* Modern card styling */
                    .class-card {
                        background: white;
                        border-radius: 16px;
                        overflow: hidden;
                        transition: all 0.3s ease;
                        border: 1px solid #eee;
                    }
                    
                    .class-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
                    }

                    .class-banner {
                        height: 120px;
                        background-size: cover;
                        background-position: center;
                        position: relative;
                    }

                    .class-content {
                        padding: 1.5rem;
                    }

                    .profile-circle {
                        width: 48px;
                        height: 48px;
                        border-radius: 50%;
                        border: 3px solid white;
                        position: absolute;
                        bottom: -24px;
                        right: 20px;
                        background: white;
                        object-fit: cover;
                    }

                    .class-title {
                        font-size: 1.1rem;
                        font-weight: 600;
                        color: #2c3e50;
                        margin-bottom: 0.3rem;
                    }

                    .class-meta {
                        font-size: 0.85rem;
                        color: #7f8c8d;
                    }

                    .action-buttons {
                        display: flex;
                        gap: 0.5rem;
                        margin-top: 1rem;
                    }

                    .btn-enter {
                        flex: 1;
                        padding: 0.6rem;
                        border-radius: 8px;
                        border: none;
                        background: #da7756;
                        color: white;
                        font-weight: 500;
                        transition: background 0.3s ease;
                    }

                    .btn-enter:hover {
                        background: #c56647;
                    }

                    .btn-more {
                        width: 42px;
                        border-radius: 8px;
                        border: 1px solid #eee;
                        background: white;
                        color: #666;
                    }
                </style>

                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold m-0">Kelas Saya</h3>
                    <div class="d-none d-md-flex gap-2">
                        <button class="btn btn-light px-3 py-2" data-bs-toggle="modal" data-bs-target="#modal_tambah_kelas">
                            <i class="bi bi-plus-lg me-2"></i>Buat Kelas
                        </button>
                        <button class="btn btn-light px-3 py-2" data-bs-toggle="modal" data-bs-target="#modal_arsip_kelas">
                            <i class="bi bi-archive me-2"></i>Arsip
                        </button>
                    </div>
                </div>


                <!-- Floating Action Button -->
                <div class="floating-action-button d-block d-md-none">
                    <!-- Main FAB -->
                    <button class="btn btn-lg main-fab rounded-circle shadow" id="mainFab">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    
                    <!-- Mini FABs -->
                    <div class="mini-fabs">
                        <!-- Buat Kelas Button -->
                        <button class="btn mini-fab rounded-circle shadow" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modal_tambah_kelas"
                            title="Buat Kelas">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        
                        <!-- Arsip Button -->
                        <button class="btn mini-fab rounded-circle shadow"
                                data-bs-toggle="modal" 
                                data-bs-target="#modal_arsip_kelas"
                                title="Arsip">
                            <i class="bi bi-archive"></i>
                        </button>
                    </div>
                </div>

                <style>
                /* Floating Action Button Styling */
                .floating-action-button {
                    position: fixed;
                    bottom: 80px;
                    right: 20px;
                    z-index: 1050;
                }

                .main-fab {
                    width: 56px;
                    height: 56px;
                    background: #da7756;
                    color: white;
                    transition: transform 0.3s;
                }

                .main-fab:hover {
                    background: #c56647;
                    color: white;
                }

                .main-fab.active {
                    transform: rotate(45deg);
                }

                .mini-fabs {
                    position: absolute;
                    bottom: 70px;
                    right: 7px;
                    display: flex;
                    flex-direction: column;
                    gap: 16px;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s;
                }

                .mini-fabs.show {
                    opacity: 1;
                    visibility: visible;
                }

                .mini-fab {
                    width: 42px;
                    height: 42px;
                    background: white;
                    color: #666;
                    transform: scale(0);
                    transition: transform 0.3s;
                }

                .mini-fabs.show .mini-fab {
                    transform: scale(1);
                }

                .mini-fab:hover {
                    background: #f8f9fa;
                    color: #da7756;
                }
                </style>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const mainFab = document.getElementById('mainFab');
                    const miniFabs = document.querySelector('.mini-fabs');
                    let isOpen = false;

                    mainFab.addEventListener('click', function(e) {
                        e.stopPropagation();
                        isOpen = !isOpen;
                        mainFab.classList.toggle('active');
                        miniFabs.classList.toggle('show');
                    });

                    // Close menu when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!mainFab.contains(e.target) && !miniFabs.contains(e.target) && isOpen) {
                            isOpen = false;
                            mainFab.classList.remove('active');
                            miniFabs.classList.remove('show');
                        }
                    });

                    // Prevent menu from closing when clicking menu items
                    miniFabs.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
                </script>
                <!-- Classes Grid -->
                <div class="row g-4">
                    <?php
                    $query_kelas = "SELECT k.*, COUNT(ks.siswa_id) as jumlah_siswa 
                                    FROM kelas k 
                                    LEFT JOIN kelas_siswa ks ON k.id = ks.kelas_id 
                                    WHERE k.guru_id = '$userid' AND k.is_archived = 0
                                    GROUP BY k.id";
                    $result_kelas = mysqli_query($koneksi, $query_kelas);

                    if(mysqli_num_rows($result_kelas) > 0):
                        while($kelas = mysqli_fetch_assoc($result_kelas)): 
                        $bg_image = !empty($kelas['background_image']) ? $kelas['background_image'] : 'assets/bg.jpg';
                    ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="class-card">
                                <div class="class-banner" style="background-image: url('<?php echo $bg_image; ?>')">
                                    <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                         class="profile-circle">
                                </div>
                                <div class="class-content">
                                    <h4 class="class-title"><?php echo htmlspecialchars($kelas['mata_pelajaran']); ?></h4>
                                    <div class="class-meta">Kelas <?php echo htmlspecialchars($kelas['tingkat']); ?></div>
                                    
                                    <div class="action-buttons">
                                        <a href="kelas_guru.php?id=<?php echo $kelas['id']; ?>" 
                                           class="btn-enter text-decoration-none d-flex align-items-center justify-content-center">
                                           Masuk
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn-more d-flex align-items-center justify-content-center" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="archive_kelas.php?id=<?php echo $kelas['id']; ?>">
                                                        <i class="bi bi-archive me-2"></i>Arsipkan
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center text-danger" href="hapus_kelas.php?id=<?php echo $kelas['id']; ?>">
                                                        <i class="bi bi-trash me-2"></i>Hapus
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <style>
                                    .action-buttons {
                                        display: flex;
                                        gap: 0.5rem;
                                        margin-top: 1rem;
                                        height: 38px;
                                    }
                                    
                                    .btn-enter {
                                        flex: 1;
                                        border-radius: 8px;
                                        border: none;
                                        background: #da7756;
                                        color: white;
                                        font-weight: 500;
                                        transition: background 0.3s ease;
                                        height: 100%;
                                    }
                                    
                                    .btn-more {
                                        width: 38px;
                                        border-radius: 8px;
                                        border: 1px solid #eee;
                                        background: white;
                                        color: #666;
                                        height: 100%;
                                    }

                                    .dropdown-item {
                                        padding: 8px 16px;
                                    }
                                    </style>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div class="col-12 text-center py-5">
                            <img src="assets/empty-class.svg" alt="No Classes" style="width: 200px; opacity: 0.5; margin-bottom: 1rem;">
                            <p class="text-muted">Belum ada kelas yang dibuat</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


        <!-- modal untuk buat kelas -->
<!-- Modal Buat Kelas dan Pilih Siswa -->
<div class="modal fade" id="modal_tambah_kelas" tabindex="-1" aria-labelledby="label_tambah_kelas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-5 fw-bold" id="label_tambah_kelas">Buat Kelas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <form action="tambah_kelas.php" method="POST">
                    <div class="row g-4">
                        <!-- Form Kelas -->
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label small mb-2">Mata Pelajaran</label>
                                <select class="form-select form-select-lg shadow-sm" name="mata_pelajaran" id="mata_pelajaran" required>
                                    <option value="">Pilih mata pelajaran</option>
                                    <option value="">Pilih salah satu</option>
                                    <option value="Matematika">Matematika</option>
                                    <option value="Ilmu Pengetahuan Alam">Ilmu Pengetahuan Alam</option>
                                    <option value="Informatika">Informatika</option>
                                    <option value="Akidah AKhlak">Akidah Akhlak</option>
                                    <option value="Quran Hadist">Quran Hadist</option>
                                    <option value="Fikih">Fikih</option>
                                    <option value="Bahasa Arab">Bahasa Arab</option>
                                    <option value="Kemuhammadiyahan Tarikh">Kemuhammadiyahan Tarikh</option>
                                    <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                    <option value="Bahasa Inggris">Bahasa Inggris</option>
                                    <option value="Ilmu Pengetahuan Sosial">Ilmu Pengetahuan Sosial</option>
                                    <option value="TIK">TIK</option>
                                    <option value="Bahasa Jawa">Bahasa Jawa</option>
                                    <option value="Seni Budaya">Seni Budaya</option>
                                    <option value="PPkn">PPkn</option>
                                    <option value="PJOK">PJOK</option>
                                    <option value="Project">Project</option>
                                    <option value="Bimbingan Konseling">Bimbingan Konseling</option>
                                    <option value="Mentoring">Mentoring</option>
                                    <option value="Praktik Ibadah">Praktik Ibadah</option>
                                    <option value="Geografi">Geografi</option>
                                    <option value="Matematika Tingkat Lanjut SMA">Matematika Tingkat Lanjut SMA</option>
                                    <option value="Kemuhammadiyahan">Kemuhammadiyahan</option>
                                    <option value="PKN">PKN</option>
                                    <option value="PKWU">PKWU</option>
                                    <option value="Sosiologi">Sosiologi</option>
                                    <option value="Biologi">Biologi</option>
                                    <option value="Pendidikan Jasmani">Pendidikan Jasmani</option>
                                    <option value="Kimia">Kimia</option>
                                    <option value="Ekonomi">Ekonomi</option>
                                    <option value="Ibadah">Ibadah</option>
                                    <option value="Sejarah">Sejarah</option>
                                    <option value="Seni">Seni</option>
                                    <option value="Akutansi">Akutansi</option>

                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label small mb-2">Tingkat Kelas</label>
                                <select class="form-select form-select-lg shadow-sm" name="tingkat" id="tingkat" onchange="loadSiswa(this.value)" required>
                                    <option value="">Pilih tingkat kelas</option>
                                    <option value="7">SMP Kelas 7</option>
                                    <option value="8">SMP Kelas 8</option>
                                    <option value="9">SMP Kelas 9</option>
                                    <option value="E">SMA Fase E</option>
                                    <option value="F">SMA Fase F</option>
                                    <option value="12">SMA Kelas 12</option>
                                </select>
                            </div>
                        </div>

                        <!-- Daftar Siswa -->
                        <div class="col-12 col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label small mb-0">Daftar Siswa</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pilih_semua">
                                        <label class="form-check-label small">Pilih Semua</label>
                                    </div>
                                </div>
                                
                                <div id="daftar_siswa" class="overflow-auto" style="max-height: 300px;">
                                    <div class="text-center py-4 text-muted small">
                                        Pilih tingkat kelas terlebih dahulu
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-0 pt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="submit" class="btn color-web text-white px-4">Buat Kelas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal styling */
.modal-content {
    border: none;
    border-radius: 12px;
}

.form-select {
    border: 1px solid #dee2e6;
    padding: 0.5rem 1rem;
    font-size: 14px;
    border-radius: 8px;
}

.form-select:focus {
    border-color: #da7756;
    box-shadow: 0 0 0 0.25rem rgba(218, 119, 86, 0.25);
}

.form-check-input:checked {
    background-color: #da7756;
    border-color: #da7756;
}

/* Custom scrollbar */
#daftar_siswa::-webkit-scrollbar {
    width: 6px;
}

#daftar_siswa::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#daftar_siswa::-webkit-scrollbar-thumb {
    background: #da7756;
    border-radius: 3px;
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .modal-body {
        padding: 1rem;
    }
}
</style>

<script>
// Sort mata pelajaran options
document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('mata_pelajaran');
    var options = Array.from(select.options).slice(1);
    
    options.sort((a, b) => a.text.localeCompare(b.text));
    
    while (select.options.length > 1) {
        select.remove(1);
    }
    
    options.forEach(option => select.add(option));
});

// Handle pilih semua checkbox
document.getElementById('pilih_semua').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});
</script>

<!-- Modal Arsip Kelas -->
<div class="modal fade" id="modal_arsip_kelas" tabindex="-1" aria-labelledby="label_arsip_kelas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <div>
                    <h1 class="modal-title fs-5 fw-bold" id="label_arsip_kelas">Kelas yang Diarsipkan</h1>
                    <p class="text-muted small mb-0">Daftar kelas yang telah diarsipkan</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4">
                <?php
                // Query untuk mengambil kelas yang diarsipkan
                $query_arsip = "SELECT k.*, COUNT(ks.siswa_id) as jumlah_siswa 
                              FROM kelas k 
                              LEFT JOIN kelas_siswa ks ON k.id = ks.kelas_id 
                              WHERE k.guru_id = '$userid' AND k.is_archived = 1
                              GROUP BY k.id";
                $result_arsip = mysqli_query($koneksi, $query_arsip);

                if(mysqli_num_rows($result_arsip) > 0) {
                    ?>
                    <div class="row g-4">
                        <?php while($kelas_arsip = mysqli_fetch_assoc($result_arsip)) { ?>
                            <div class="col-12">
                                <div class="card border-1 shadow-none">
                                    <div class="row g-0">
                                        <!-- Gambar Kelas -->
                                        <div class="col-md-4">
                                            <img src="<?php echo !empty($kelas_arsip['background_image']) ? htmlspecialchars($kelas_arsip['background_image']) : 'assets/bg.jpg'; ?>" 
                                                 class="img-fluid rounded-start h-100" 
                                                 style="object-fit: cover;" 
                                                 alt="Background Image">
                                        </div>
                                        
                                        <!-- Informasi Kelas -->
                                        <div class="col-md-8">
                                            <div class="card-body shadow-none border-2">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h5 class="card-title fw-bold mb-1">
                                                            <?php echo htmlspecialchars($kelas_arsip['mata_pelajaran']); ?>
                                                        </h5>
                                                        <p class="card-text text-muted small">
                                                            Kelas <?php echo htmlspecialchars($kelas_arsip['tingkat']); ?>
                                                        </p>
                                                    </div>
                                                    <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                                         class="rounded-circle" 
                                                         width="40" 
                                                         height="40"
                                                         style="object-fit: cover;"
                                                         alt="Profile">
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="d-flex gap-2 mt-3">
                                                    <a href="unarchive_kelas.php?id=<?php echo $kelas_arsip['id']; ?>" 
                                                       class="btn color-web text-white btn-sm flex-grow-1">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i>
                                                        Keluarkan dari Arsip
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm"
                                                            onclick="if(confirm('Apakah Anda yakin ingin menghapus kelas ini?')) window.location.href='hapus_kelas.php?id=<?php echo $kelas_arsip['id']; ?>'">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="text-center py-5">
                        <i class="bi bi-archive text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mb-0">Belum ada kelas yang diarsipkan</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>




<style>
/* Modal Archive Styling */
#modal_arsip_kelas .modal-content {
    border-radius: 15px;
}

#modal_arsip_kelas .card {
    transition: transform 0.2s;
    border-radius: 12px;
}

#modal_arsip_kelas .card:hover {
    transform: translateY(-2px);
}

#modal_arsip_kelas .btn {
    border-radius: 8px;
    padding: 8px 16px;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    #modal_arsip_kelas .modal-dialog {
        margin: 1rem;
    }
    
    #modal_arsip_kelas .col-md-4 img {
        height: 150px;
        width: 100%;
        border-radius: 12px 12px 0 0 !important;
    }
    
    #modal_arsip_kelas .card-body {
        padding: 1rem;
    }
}
</style>


<script>
function loadSiswa(tingkat) {
    if(tingkat) {
        // Gunakan AJAX untuk mengambil daftar siswa
        fetch(`get_siswa.php?tingkat=${tingkat}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('daftar_siswa').innerHTML = data;
            });
    } else {
        document.getElementById('daftar_siswa').innerHTML = '<p class="text-muted text-center mt-5" style="font-size: 14px;">Pilih tingkat kelas terlebih dahulu</p>';
    }
}

// Handle checkbox "Pilih Semua"
document.getElementById('pilih_semua').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>


</body>
</html>