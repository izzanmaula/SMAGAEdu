<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postingan_id = mysqli_real_escape_string($koneksi, $_POST['postingan_id']);
    $konten = mysqli_real_escape_string($koneksi, $_POST['konten']);
    $user_id = $_SESSION['userid'];

    $query = "INSERT INTO komentar_postingan (postingan_id, user_id, konten) 
              VALUES ('$postingan_id', '$user_id', '$konten')";

    if(mysqli_query($koneksi, $query)) {
        // Ambil data komentar yang baru ditambahkan
        $komentar_id = mysqli_insert_id($koneksi);
        $query_komentar = "SELECT k.*, COALESCE(g.namaLengkap, s.nama) as nama_user 
                          FROM komentar_postingan k 
                          LEFT JOIN guru g ON k.user_id = g.username 
                          LEFT JOIN siswa s ON k.user_id = s.username 
                          WHERE k.id = '$komentar_id'";
        $result = mysqli_query($koneksi, $query_komentar);
        $komentar = mysqli_fetch_assoc($result);
        
        echo json_encode([
            'status' => 'success',
            'komentar' => $komentar
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menambahkan komentar'
        ]);
    }
}