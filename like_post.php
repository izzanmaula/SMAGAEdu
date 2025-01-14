<?php
session_start();
require 'koneksi.php';

if(!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false]);
    exit;
}

$postingan_id = $_POST['postingan_id'];
$user_id = $_SESSION['userid'];

// Cek apakah sudah like
$query_cek = "SELECT * FROM likes_postingan WHERE postingan_id = ? AND user_id = ?";
$stmt = mysqli_prepare($koneksi, $query_cek);
mysqli_stmt_bind_param($stmt, "is", $postingan_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0) {
    // Sudah like, hapus like
    $query = "DELETE FROM likes_postingan WHERE postingan_id = ? AND user_id = ?";
} else {
    // Belum like, tambah like
    $query = "INSERT INTO likes_postingan (postingan_id, user_id) VALUES (?, ?)";
}

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "is", $postingan_id, $user_id);
$success = mysqli_stmt_execute($stmt);

echo json_encode(['success' => $success]);