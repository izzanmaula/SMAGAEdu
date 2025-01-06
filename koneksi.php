<?php
// Koneksi ke database smagaedu
$koneksi = mysqli_connect("localhost", "root", "", "smagaedu");

// Koneksi ke database absensi
$koneksi_absensi = mysqli_connect("localhost", "root", "", "absensi");

// Cek koneksi
if (mysqli_connect_errno()){
    echo "Koneksi database gagal : " . mysqli_connect_error();
}
?>