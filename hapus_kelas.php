<?php
// hapus_kelas.php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])) {
    $kelas_id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Mulai transaction untuk memastikan semua query berhasil
    mysqli_begin_transaction($koneksi);
    
    try {
        // 1. Hapus dulu data di tabel kelas_siswa
        $query_hapus_relasi = "DELETE FROM kelas_siswa WHERE kelas_id = ?";
        $stmt = mysqli_prepare($koneksi, $query_hapus_relasi);
        mysqli_stmt_bind_param($stmt, "i", $kelas_id);
        mysqli_stmt_execute($stmt);
        
        // 2. Baru hapus data di tabel kelas
        $query_hapus_kelas = "DELETE FROM kelas WHERE id = ? AND guru_id = ?";
        $stmt = mysqli_prepare($koneksi, $query_hapus_kelas);
        mysqli_stmt_bind_param($stmt, "is", $kelas_id, $_SESSION['userid']);
        mysqli_stmt_execute($stmt);
        
        // Commit transaction
        mysqli_commit($koneksi);
        
        header("Location: beranda_guru.php?pesan=kelas_berhasil_dihapus");
        exit();
        
    } catch (Exception $e) {
        // Rollback jika terjadi error
        mysqli_rollback($koneksi);
        header("Location: beranda_guru.php?pesan=gagal_hapus_kelas");
        exit();
    }
} else {
    header("Location: beranda_guru.php");
    exit();
}
?>