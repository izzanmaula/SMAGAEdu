<?php
require "koneksi.php";
session_start();

if(isset($_POST)) {
    $userid = mysqli_real_escape_string($koneksi, $_POST['userid']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']); 
    
    // Cek di database smagaedu untuk siswa
    $query_siswa = "SELECT * FROM users WHERE username = '$userid' AND password = '$password'";
    $result_siswa = mysqli_query($koneksi, $query_siswa);
    
    // Cek di database absensi untuk guru
    $query_guru = "SELECT * FROM users WHERE username = '$userid' AND password = '$password'";
    $result_guru = mysqli_query($koneksi_absensi, $query_guru);
    
    if(mysqli_num_rows($result_siswa) == 1) {
        // Jika user ditemukan di database siswa
        $row = mysqli_fetch_assoc($result_siswa);
        // Set session untuk siswa
        $_SESSION['userid'] = $row['username'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['level'] = 'siswa';
        
        header("Location: beranda.php");
        exit();
    } 
    else if(mysqli_num_rows($result_guru) == 1) {
        // Jika user ditemukan di database guru
        $row = mysqli_fetch_assoc($result_guru);
        // Set session untuk guru
        $_SESSION['userid'] = $row['username'];
        $_SESSION['nama'] = $row['namaLengkap'];
        $_SESSION['level'] = 'guru';
        
        header("Location: beranda_guru.php");
        exit();
    }
    else {
        // Jika user tidak ditemukan di kedua database
        header("Location: index.php?pesan=user_tidak_ditemukan");
        exit();
    }
}
?>