<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $soal_id = $_POST['soal_id'];
    $jenis_soal = $_POST['jenis_soal'];
    $pertanyaan = $_POST['pertanyaan'];
    $userid = $_SESSION['userid'];

    try {
        // Perbaikan pada query check kepemilikan soal
        $query_check = "SELECT bs.id FROM bank_soal bs 
                       JOIN ujian u ON bs.ujian_id = u.id 
                       WHERE bs.id = ? AND u.guru_id = ?";
        
        $stmt_check = mysqli_prepare($koneksi, $query_check);
        mysqli_stmt_bind_param($stmt_check, "is", $soal_id, $userid);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check); // Tambahkan ini

        // Perbaikan pada pengecekan hasil query
        if(mysqli_num_rows($result_check) == 0) {
            throw new Exception('Anda tidak memiliki akses untuk mengedit soal ini');
        }

        if($jenis_soal == 'pilihan_ganda') {
            $query = "UPDATE bank_soal SET 
                     pertanyaan = ?, 
                     jawaban_a = ?, 
                     jawaban_b = ?, 
                     jawaban_c = ?, 
                     jawaban_d = ?, 
                     jawaban_benar = ? 
                     WHERE id = ?";
            
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ssssssi", 
                $pertanyaan,
                $_POST['jawaban_a'],
                $_POST['jawaban_b'],
                $_POST['jawaban_c'],
                $_POST['jawaban_d'],
                $_POST['jawaban_benar'],
                $soal_id
            );
        } else {
            $query = "UPDATE bank_soal SET pertanyaan = ? WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "si", $pertanyaan, $soal_id);
        }

        if(mysqli_stmt_execute($stmt)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Soal berhasil diperbarui'
            ]);
        } else {
            throw new Exception(mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
?>