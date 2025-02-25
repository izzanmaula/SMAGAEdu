<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

if(isset($_POST['submit'])) {
    mysqli_begin_transaction($koneksi);
    
    try {
        // Ambil data kelas
        $mata_pelajaran = mysqli_real_escape_string($koneksi, $_POST['mata_pelajaran']);
        $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
        $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
        $guru_id = $_SESSION['userid'];
        
        // Proses array materi
        $materi = isset($_POST['materi']) ? $_POST['materi'] : [];
        $materi_json = json_encode(array_values(array_filter($materi))); // Hapus empty values
        
        // Insert data kelas
        $query_kelas = "INSERT INTO kelas (nama_kelas, deskripsi, guru_id, mata_pelajaran, tingkat, materi) 
                      VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($koneksi, $query_kelas);
        $nama_kelas = "$mata_pelajaran Kelas $tingkat";
        mysqli_stmt_bind_param($stmt, "ssssss", $nama_kelas, $deskripsi, $guru_id, $mata_pelajaran, $tingkat, $materi_json);
        
        if(mysqli_stmt_execute($stmt)) {
            $kelas_id = mysqli_insert_id($koneksi);
            
            // Proses siswa yang dipilih seperti sebelumnya
            if(isset($_POST['siswa_ids']) && is_array($_POST['siswa_ids'])) {
                $query_siswa = "INSERT INTO kelas_siswa (kelas_id, siswa_id) VALUES (?, ?)";
                $stmt_siswa = mysqli_prepare($koneksi, $query_siswa);
                
                foreach($_POST['siswa_ids'] as $siswa_id) {
                    mysqli_stmt_bind_param($stmt_siswa, "ii", $kelas_id, $siswa_id);
                    mysqli_stmt_execute($stmt_siswa);
                }
            }
            
            mysqli_commit($koneksi);
            header("Location: kelas_guru.php?id=" . $kelas_id);
            exit();
        }
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        header("Location: beranda_guru.php?pesan=gagal_tambah_kelas");
        exit();
    }
} else {
    header("Location: beranda_guru.php");
    exit();
}
?>