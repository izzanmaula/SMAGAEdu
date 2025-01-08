<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

if(isset($_POST['submit'])) {
    $mata_pelajaran = mysqli_real_escape_string($koneksi, $_POST['mata_pelajaran']);
    $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $guru_id = $_SESSION['userid'];

    $query = "INSERT INTO kelas (nama_kelas, deskripsi, guru_id, mata_pelajaran, tingkat) 
              VALUES ('$mata_pelajaran Kelas $tingkat', '$deskripsi', '$guru_id', '$mata_pelajaran', '$tingkat')";
    
    if(mysqli_query($koneksi, $query)) {
        $kelas_id = mysqli_insert_id($koneksi);
        header("Location: kelas_guru.php?id=" . $kelas_id);
        exit();
    } else {
        header("Location: beranda_guru.php?pesan=gagal_tambah_kelas");
        exit();
    }
}
?>