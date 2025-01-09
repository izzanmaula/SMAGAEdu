<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postingan_id = mysqli_real_escape_string($koneksi, $_POST['postingan_id']);
    $user_id = $_SESSION['userid'];

    // Cek apakah user sudah like postingan ini
    $check_query = "SELECT id FROM likes_postingan 
                   WHERE postingan_id = '$postingan_id' AND user_id = '$user_id'";
    $check_result = mysqli_query($koneksi, $check_query);

    if(mysqli_num_rows($check_result) > 0) {
        // User sudah like, maka unlike
        $query = "DELETE FROM likes_postingan 
                 WHERE postingan_id = '$postingan_id' AND user_id = '$user_id'";
        $is_liked = false;
    } else {
        // User belum like, maka like
        $query = "INSERT INTO likes_postingan (postingan_id, user_id) 
                 VALUES ('$postingan_id', '$user_id')";
        $is_liked = true;
    }

    if(mysqli_query($koneksi, $query)) {
        // Hitung jumlah like terbaru
        $count_query = "SELECT COUNT(*) as total FROM likes_postingan 
                       WHERE postingan_id = '$postingan_id'";
        $count_result = mysqli_query($koneksi, $count_query);
        $count_data = mysqli_fetch_assoc($count_result);
        
        echo json_encode([
            'status' => 'success',
            'is_liked' => $is_liked,
            'like_count' => $count_data['total']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memproses like'
        ]);
    }
}
?>