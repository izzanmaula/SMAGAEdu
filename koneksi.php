<?php
// Koneksi ke database smagaedu
$koneksi = mysqli_connect("localhost", "root", "", "smagaedu");


// Cek koneksi
if (mysqli_connect_errno()){
    echo "Koneksi database gagal : " . mysqli_connect_error();
}
?>