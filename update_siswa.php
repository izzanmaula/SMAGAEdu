<?php
session_start();
require "koneksi.php";

// Pastikan yang mengakses adalah guru
if(!isset($_SESSION['level']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data yang dikirim dari form
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $pendidikan_sebelumnya = mysqli_real_escape_string($koneksi, $_POST['pendidikan_sebelumnya']);
    $kelas_saat_ini = mysqli_real_escape_string($koneksi, $_POST['kelas_saat_ini']);
    $gaya_belajar = mysqli_real_escape_string($koneksi, $_POST['gaya_belajar']);
    $hasil_iq = mysqli_real_escape_string($koneksi, $_POST['hasil_iq']);
    $kemampuan_literasi = mysqli_real_escape_string($koneksi, $_POST['kemampuan_literasi']);
    $kemampuan_berhitung = mysqli_real_escape_string($koneksi, $_POST['kemampuan_berhitung']);
    $minat = mysqli_real_escape_string($koneksi, $_POST['minat']);
    $hobi = mysqli_real_escape_string($koneksi, $_POST['hobi']);
    $kesehatan_mental = mysqli_real_escape_string($koneksi, $_POST['kesehatan_mental']);
    $pengembangan_emosional = mysqli_real_escape_string($koneksi, $_POST['pengembangan_emosional']);
    $penyakit_bawaan = mysqli_real_escape_string($koneksi, $_POST['penyakit_bawaan']);
    $kehidupan_sosial = mysqli_real_escape_string($koneksi, $_POST['kehidupan_sosial']);

    // Update data siswa
    $query = "UPDATE siswa SET 
        nama = ?, 
        pendidikan_sebelumnya = ?,
        kelas_saat_ini = ?,
        gaya_belajar = ?,
        hasil_iq = ?,
        kemampuan_literasi = ?,
        kemampuan_berhitung = ?,
        minat = ?,
        hobi = ?,
        kesehatan_mental = ?,
        pengembangan_emosional = ?,
        penyakit_bawaan = ?,
        kehidupan_sosial = ?
        WHERE username = ?";

    // Menggunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ssssssssssssss", 
        $nama,
        $pendidikan_sebelumnya,
        $kelas_saat_ini,
        $gaya_belajar,
        $hasil_iq,
        $kemampuan_literasi,
        $kemampuan_berhitung,
        $minat,
        $hobi,
        $kesehatan_mental,
        $pengembangan_emosional,
        $penyakit_bawaan,
        $kehidupan_sosial,
        $username
    );

    if(mysqli_stmt_execute($stmt)) {
        // Jika berhasil, redirect dengan pesan sukses
        $_SESSION['message'] = "Data siswa berhasil diperbarui!";
        $_SESSION['message_type'] = "success";
    } else {
        // Jika gagal, redirect dengan pesan error
        $_SESSION['message'] = "Gagal memperbarui data siswa: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "danger";
    }

    mysqli_stmt_close($stmt);

    // Redirect kembali ke halaman edit
    header("Location: view_siswa.php?username=" . $username);
    exit();
} else {
    // Jika bukan method POST, redirect ke halaman utama
    header("Location: cari.php");
    exit();
}
?>