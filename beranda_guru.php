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
<body>
    

    <!-- Navbar Mobile -->
    <nav class="navbar navbar-dark d-md-none color-web fixed-top">
        <div class="container-fluid">
            <!-- Logo dan Nama -->
            <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="#">
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
                            <div class="d-flex align-items-center rounded color-web p-2">
                                <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Beranda</p>
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
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Ujian</p>
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
                        <a href="bantuan.php" class="text-decoration-none text-black">
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
                        <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                        <p class="p-0 m-0  text-truncate" style="font-size: 12px;" aria-expanded="false"><?php echo htmlspecialchars($guru['namaLengkap']); ?></p>
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
        <div class="col-auto vh-100 p-2 p-md-4 shadow-sm menu-samping d-none d-md-block" style="background-color:rgb(238, 236, 226)">
                <style>
                    .menu-samping {
                        position: fixed;
                        width: 13rem;
                        z-index: 1000;
                    }
                </style>
                <div class="row gap-0">
                    <div class="ps-3 mb-3">
                        <a href="beranda.php" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
                            <img src="assets/smagaedu.png" alt="" width="30px" class="logo_orange">
                            <div>
                                <h1 class="display-5  p-0 m-0" style="font-size: 20px; text-decoration: none;">SMAGAEdu</h1>
                                <p class="p-0 m-0 text-muted" style="font-size: 12px;">LMS</p>
                            </div>
                        </a>
                    </div>  
                    <div class="col">
                        <a href="beranda_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
                            <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
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
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
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
                        <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                        <p class="p-0 m-0  text-truncate" style="font-size: 12px;"><?php echo htmlspecialchars($guru['namaLengkap']); ?></p>
                    </div>
                    <!-- dropdown menu option -->
                    <ul class="dropdown-menu" style="font-size: 12px;">
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                      </ul>
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
            </style>                
                <div class="row justify-content-between align-items-center mb-1">
                    <div class="col-12 col-md-auto mb-3 mb-md-0">
                        <h3 style="font-weight: bold;">Beranda</h3>
                    </div>
                    <!-- Tombol desktop -->
                    <div class="d-none d-md-block col-md-auto">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modal_tambah_kelas" 
                                class="btn d-flex align-items-center justify-content-center border p-2">
                            <img src="assets/tambah.png" alt="Tambah" width="25px" class="me-2">
                            <p class="m-0">Buat Kelas</p>
                        </button>
                    </div>

                    <!-- Floating button untuk mobile -->
                    <div class="position-fixed bottom-0 end-0 d-md-none m-4">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modal_tambah_kelas" 
                                class="btn color-web rounded-circle shadow d-flex align-items-center justify-content-center" 
                                style="width: 56px; height: 56px;">
                            <img src="assets/tambah.png" alt="Tambah" width="25px" class="m-0" style="filter: brightness(0) invert(1);">
                        </button>
                    </div>

                    <style>
                        /* Animasi hover untuk floating button */
                        .position-fixed.bottom-0.end-0 {
                                margin-right: -75% !important; /* Memastikan tidak ada margin yang mengganggu */
                            }
                    </style>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                // Kemudian query untuk kelas
                $query_kelas = "SELECT k.*, COUNT(ks.siswa_id) as jumlah_siswa 
                                FROM kelas k 
                                LEFT JOIN kelas_siswa ks ON k.id = ks.kelas_id 
                                WHERE k.guru_id = '$userid'
                                GROUP BY k.id";
                $result_kelas = mysqli_query($koneksi, $query_kelas); 
                

                if(mysqli_num_rows($result_kelas) > 0) {
                    while($kelas = mysqli_fetch_assoc($result_kelas)) {
                        ?>
                        <div class="col">
                            <div class="custom-card w-100">
                                <!-- Jika ada background image, tampilkan. Jika tidak, gunakan default -->
                                <?php if(!empty($kelas['background_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($kelas['background_image']); ?>" 
                                        alt="Background Image" 
                                        class="card-img-top">
                                <?php else: ?>
                                    <img src="assets/bg.jpg" 
                                        alt="Default Background Image" 
                                        class="card-img-top">
                                <?php endif; ?>
                                
                                <!-- Profile Image -->
                                <div class="card-body" style="text-align: right; padding-right: 30px; background-color: white;">
                                                        <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>" 
                                                            alt="Profile Image" 
                                                            class="profile-img rounded-4 border-0 bg-white">
                                                    </div>

                                <div class="ps-3">
                                    <h5 class="mt-3 p-0 mb-1" style="font-weight: bold; font-size: 20px;">
                                        <?php echo htmlspecialchars($kelas['mata_pelajaran']); ?>
                                    </h5>
                                    <p class="p-0 m-0" style="font-size: 12px;">
                                        Kelas <?php echo htmlspecialchars($kelas['tingkat']); ?>
                                    </p>
                                </div>
                                <div class="d-flex btn-group gap-2 p-3">
                                    <a href="kelas_guru.php?id=<?php echo $kelas['id']; ?>" 
                                    class="btn color-web w-45 rounded" 
                                    style="text-decoration: none; color: white;">
                                        Masuk
                                    </a>
                                    <a href="hapus_kelas.php?id=<?php echo $kelas['id']; ?>" 
                                        class="btn btn-danger w-45 rounded" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');"
                                        style="text-decoration: none; color: white;">
                                        Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                        <p class="text-muted">Anda belum memiliki kelas.</p>
                    </div>
                    <?php
                }
                ?>
                </div>
            </div>
        </div>
    </div>

        <!-- script copy kode kelas -->
        <script>
function copyKodeKelas(kode) {
    navigator.clipboard.writeText(kode).then(function() {
        // Buat alert bootstrap yang akan hilang setelah beberapa detik
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-3';
        alertDiv.innerHTML = `
            Kode kelas berhasil disalin!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(alertDiv);
        
        // Hilangkan alert setelah 3 detik
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }).catch(function(err) {
        console.error('Gagal menyalin kode: ', err);
    });
}
</script>


        <!-- modal untuk buat kelas -->
<!-- Modal Buat Kelas dan Pilih Siswa -->
<div class="modal fade" id="modal_tambah_kelas" tabindex="-1" aria-labelledby="label_tambah_kelas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Ubah ukuran modal jadi lebih besar -->
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="label_tambah_kelas" style="font-weight: bold;">Buat Kelas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="tambah_kelas.php" method="POST">
                    <div class="row">
                        <!-- Kolom Kiri: Form Kelas -->
                        <div class="col-md-6 border-end">
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 14px;">Pilih mata pelajaran</label>
                                <select class="form-select" name="mata_pelajaran" id="mata_pelajaran" required>
                                    <option value="">Pilih salah satu</option>
                                    <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                    <option value="Matematika">Matematika</option>
                                    <option value="Ilmu Pengetahuan Alam">Ilmu Pengetahuan Alam</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-size: 14px;">Pilih tingkat kelas</label>
                                <select class="form-select" name="tingkat" id="tingkat" onchange="loadSiswa(this.value)" required>
                                    <option value="">Pilih salah satu</option>
                                    <option value="7">Kelas 7</option>
                                    <option value="8">Kelas 8</option>
                                    <option value="9">Kelas 9</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-size: 14px;">Deskripsi kelas</label>
                                <textarea class="form-control" name="deskripsi" rows="3" placeholder="Kelas ini bertujuan untuk ..."></textarea>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Daftar Siswa -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label" style="font-size: 14px;">Pilih Siswa</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pilih_semua">
                                        <label class="form-check-label" style="font-size: 12px;">
                                            Pilih Semua
                                        </label>
                                    </div>
                                </div>
                                <div id="daftar_siswa" style="max-height: 300px; overflow-y: auto;">
                                    <p class="text-muted">Pilih tingkat kelas terlebih dahulu</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="submit" class="btn color-web text-white">Buat Kelas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
        document.getElementById('daftar_siswa').innerHTML = '<p class="text-muted">Pilih tingkat kelas terlebih dahulu</p>';
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