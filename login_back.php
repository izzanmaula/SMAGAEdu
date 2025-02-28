<?php
require "koneksi.php";
session_start();

if(isset($_POST)) {
    $userid = mysqli_real_escape_string($koneksi, $_POST['userid']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']); 
    
    // Cek apakah username fauzinugroho dengan password a
    if($userid == "fauzinugroho" && $password == "a") {
        $_SESSION['userid'] = $userid;
        $_SESSION['nama'] = "fauzinugroho";
        $_SESSION['level'] = 'admin';
        
        header("Location: beranda_admin.php");
        exit();
    }
    
    // Cek di tabel siswa
    $query_siswa = "SELECT * FROM siswa WHERE username = '$userid' AND password = '$password'";
    $result_siswa = mysqli_query($koneksi, $query_siswa);
    
    // Cek di tabel guru 
    $query_guru = "SELECT * FROM guru WHERE username = '$userid' AND password = '$password'";
    $result_guru = mysqli_query($koneksi, $query_guru);
    
    if(mysqli_num_rows($result_siswa) == 1) {
        $row = mysqli_fetch_assoc($result_siswa);
        $_SESSION['userid'] = $row['username'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['level'] = 'siswa';
        $_SESSION['foto_profil'] = $row['foto_profil'];
        $_SESSION['foto_latarbelakang'] = $row['foto_latarbelakang'];
        
        header("Location: beranda.php");
        exit();
    } 
    else if(mysqli_num_rows($result_guru) == 1) {
        $row = mysqli_fetch_assoc($result_guru);
        $_SESSION['userid'] = $row['username'];
        $_SESSION['nama'] = $row['namaLengkap'];
        $_SESSION['level'] = 'guru';
        $_SESSION['jabatan'] = $row['jabatan'];
        
        header("Location: beranda_guru.php");
        exit();
    }
    else {
        header("Location: index.php?pesan=user_tidak_ditemukan");
        exit();
    }
}
?>