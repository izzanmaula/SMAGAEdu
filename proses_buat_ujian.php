<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $kelas_id = mysqli_real_escape_string($koneksi, $_POST['kelas_id']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $tanggal_mulai = mysqli_real_escape_string($koneksi, $_POST['tanggal_mulai']);
    $tanggal_selesai = mysqli_real_escape_string($koneksi, $_POST['tanggal_selesai']);
    $guru_id = $_SESSION['userid'];
    
    // Proses array materi menjadi JSON
    $materi = isset($_POST['materi']) ? json_encode($_POST['materi']) : null;

    // Hitung durasi dalam menit
    $mulai = new DateTime($tanggal_mulai);
    $selesai = new DateTime($tanggal_selesai);
    $interval = $mulai->diff($selesai);
    $durasi = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

    // Format tanggal untuk database
    $tanggal_mulai_formatted = date('Y-m-d H:i:s', strtotime($tanggal_mulai));
    $tanggal_selesai_formatted = date('Y-m-d H:i:s', strtotime($tanggal_selesai));
    
    // Ambil mata pelajaran dari kelas
    $query_kelas = "SELECT mata_pelajaran FROM kelas WHERE id = '$kelas_id'";
    $result_kelas = mysqli_query($koneksi, $query_kelas);
    $kelas = mysqli_fetch_assoc($result_kelas);
    $mata_pelajaran = $kelas['mata_pelajaran'];

    $query = "INSERT INTO ujian (judul, deskripsi, guru_id, kelas_id, mata_pelajaran, materi,
              tanggal_mulai, tanggal_selesai, durasi, status) 
              VALUES ('$judul', '$deskripsi', '$guru_id', '$kelas_id', '$mata_pelajaran', '$materi',
              '$tanggal_mulai_formatted', '$tanggal_selesai_formatted', '$durasi', 'draft')";

    if(mysqli_query($koneksi, $query)) {
        $ujian_id = mysqli_insert_id($koneksi);
        header("Location: buat_soal.php?ujian_id=" . $ujian_id);
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>