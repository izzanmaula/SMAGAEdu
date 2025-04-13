<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if(isset($_GET['id'])) {
    $soal_id = $_GET['id'];
    $userid = $_SESSION['userid'];

    try {
        // Cek apakah soal ini milik ujian yang dibuat oleh guru yang sedang login
        $query = "SELECT bs.* FROM bank_soal bs 
                 JOIN ujian u ON bs.ujian_id = u.id 
                 WHERE bs.id = ? AND u.guru_id = ?";
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "is", $soal_id, $userid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if($row = mysqli_fetch_assoc($result)) {
            echo json_encode([
                'status' => 'success',
                'soal' => $row
            ]);
        } else {
            throw new Exception('Soal tidak ditemukan');
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'ID soal tidak diberikan'
    ]);
}
?>