<?php
require "koneksi.php";
session_start();

// Cek apakah user sudah login dan adalah guru
if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

if(isset($_GET['id']) && isset($_GET['kelas_id'])) {
    $postingan_id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $kelas_id = mysqli_real_escape_string($koneksi, $_GET['kelas_id']);
    
    // Cek apakah postingan ini milik guru yang sedang login
    $query_cek = "SELECT p.* FROM postingan_kelas p 
                 WHERE p.id = '$postingan_id' 
                 AND p.user_id = '{$_SESSION['userid']}'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if(mysqli_num_rows($result_cek) > 0) {
        // Hapus lampiran terlebih dahulu
        $query_lampiran = "SELECT path_file FROM lampiran_postingan WHERE postingan_id = '$postingan_id'";
        $result_lampiran = mysqli_query($koneksi, $query_lampiran);
        
        while($lampiran = mysqli_fetch_assoc($result_lampiran)) {
            // Hapus file fisik jika ada
            if(file_exists($lampiran['path_file'])) {
                unlink($lampiran['path_file']);
            }
        }
        
        // Hapus data lampiran dari database
        mysqli_query($koneksi, "DELETE FROM lampiran_postingan WHERE postingan_id = '$postingan_id'");
        
        // Hapus postingan
        mysqli_query($koneksi, "DELETE FROM postingan_kelas WHERE id = '$postingan_id'");
        
        header("Location: kelas_guru.php?id=$kelas_id&pesan=berhasil_hapus");
    } else {
        header("Location: kelas_guru.php?id=$kelas_id&pesan=tidak_berhak");
    }
} else {
    header("Location: beranda_guru.php");
}
?>