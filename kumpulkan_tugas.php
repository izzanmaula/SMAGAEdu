<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $siswa_id = $_SESSION['userid'];
    $tugas_id = mysqli_real_escape_string($koneksi, $_POST['tugas_id']);
    
    // Validasi tugas dan batas waktu
    $query_tugas = "SELECT t.*, p.kelas_id 
                    FROM tugas t 
                    JOIN postingan_kelas p ON t.postingan_id = p.id 
                    WHERE t.id = '$tugas_id'";
    $result_tugas = mysqli_query($koneksi, $query_tugas);
    
    if (mysqli_num_rows($result_tugas) == 0) {
        header("Location: kelas_siswa.php?error=tugas_tidak_ditemukan");
        exit();
    }

    $data_tugas = mysqli_fetch_assoc($result_tugas);
    $kelas_id = $data_tugas['kelas_id'];

    // Validasi apakah siswa terdaftar di kelas ini
    $query_kelas = "SELECT * FROM kelas_siswa WHERE siswa_id = (SELECT id FROM siswa WHERE username = '$siswa_id') AND kelas_id = '$kelas_id'";
    $result_kelas = mysqli_query($koneksi, $query_kelas);
    if (mysqli_num_rows($result_kelas) == 0) {
        header("Location: beranda.php?error=akses_ditolak");
        exit();
    }

    // Cek apakah sudah melewati batas waktu
    $batas_waktu = new DateTime($data_tugas['batas_waktu']);
    $sekarang = new DateTime();
    if ($sekarang > $batas_waktu) {
        header("Location: kelas_siswa.php?id=$kelas_id&error=tugas_sudah_ditutup");
        exit();
    }

    // Cek apakah sudah pernah mengumpulkan
    $query_cek_pengumpulan = "SELECT * FROM pengumpulan_tugas WHERE tugas_id = '$tugas_id' AND siswa_id = '$siswa_id'";
    $result_cek = mysqli_query($koneksi, $query_cek_pengumpulan);
    if (mysqli_num_rows($result_cek) > 0) {
        header("Location: kelas_siswa.php?id=$kelas_id&error=sudah_mengumpulkan");
        exit();
    }
    
    // Validasi file
    if (!isset($_FILES['file_tugas']) || $_FILES['file_tugas']['error'] != 0) {
        header("Location: kelas_siswa.php?id=$kelas_id&error=file_tidak_valid");
        exit();
    }

    $file_name = $_FILES['file_tugas']['name'];
    $file_tmp = $_FILES['file_tugas']['tmp_name'];
    $file_type = $_FILES['file_tugas']['type'];
    $file_size = $_FILES['file_tugas']['size'];
    
    // Validasi ukuran file (maksimal 10MB)
    $max_size = 10 * 1024 * 1024; // 10MB dalam bytes
    if ($file_size > $max_size) {
        header("Location: kelas_siswa.php?id=$kelas_id&error=file_terlalu_besar");
        exit();
    }

    // Validasi tipe file
    $allowed_types = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png'
    ];
    if (!in_array($file_type, $allowed_types)) {
        header("Location: kelas_siswa.php?id=$kelas_id&error=tipe_file_tidak_didukung");
        exit();
    }
    
    $upload_dir = 'uploads/pengumpulan_tugas/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name_new = "tugas_{$tugas_id}_siswa_{$siswa_id}_" . uniqid() . "_" . $file_name;
    $file_path = $upload_dir . $file_name_new;
    
    if (move_uploaded_file($file_tmp, $file_path)) {

        $pesan_siswa = isset($_POST['pesan_siswa']) ? mysqli_real_escape_string($koneksi, $_POST['pesan_siswa']) : null;

        $query_simpan = "INSERT INTO pengumpulan_tugas 
               (tugas_id, siswa_id, file_path, nama_file, tipe_file, ukuran_file, waktu_pengumpulan, pesan_siswa) 
               VALUES ('$tugas_id', '$siswa_id', '$file_path', '$file_name', '$file_type', '$file_size', NOW(), '$pesan_siswa')";
        
        if (mysqli_query($koneksi, $query_simpan)) {
            header("Location: kelas.php?id=$kelas_id&success=tugas_berhasil_dikumpulkan");
        } else {
            header("Location: kelas.php?id=$kelas_id&error=gagal_menyimpan_data");
        }
    } else {
        header("Location: kelas.php?id=$kelas_id&error=gagal_upload_file");
    }
    exit();
}

// Jika bukan POST request
header("Location: beranda.php");
exit();
?>