<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])) {
    $ujian_id = $_GET['id'];
    $guru_id = $_SESSION['userid'];
    
    // Cek apakah ujian milik guru yang sedang login
    $query_check = "SELECT id FROM ujian WHERE id = ? AND guru_id = ?";
    $stmt = mysqli_prepare($koneksi, $query_check);
    mysqli_stmt_bind_param($stmt, "is", $ujian_id, $guru_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) > 0) {
        // Hapus soal-soal terkait terlebih dahulu
        $query_delete_soal = "DELETE FROM bank_soal WHERE ujian_id = ?";
        $stmt_soal = mysqli_prepare($koneksi, $query_delete_soal);
        mysqli_stmt_bind_param($stmt_soal, "i", $ujian_id);
        mysqli_stmt_execute($stmt_soal);
        
        // Hapus ujian
        $query_delete_ujian = "DELETE FROM ujian WHERE id = ?";
        $stmt_ujian = mysqli_prepare($koneksi, $query_delete_ujian);
        mysqli_stmt_bind_param($stmt_ujian, "i", $ujian_id);
        
        if(mysqli_stmt_execute($stmt_ujian)) {
            header("Location: ujian_guru.php?pesan=hapus_berhasil");
        } else {
            header("Location: ujian_guru.php?pesan=hapus_gagal");
        }
    } else {
        header("Location: ujian_guru.php?pesan=tidak_ditemukan");
    }
} else {
    header("Location: ujian_guru.php");
}
exit();
?>