<?php
session_start();
require "koneksi.php";

// Cek autentikasi
if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $ujian_id = $_POST['ujian_id'];
    $jenis_soal = $_POST['jenis_soal'];
    $pertanyaan = $_POST['pertanyaan'];

    try {
        // Cek apakah ujian ini milik guru yang sedang login
        $userid = $_SESSION['userid'];
        $query_check = "SELECT id FROM ujian WHERE id = ? AND guru_id = ?";
        $stmt_check = mysqli_prepare($koneksi, $query_check);
        mysqli_stmt_bind_param($stmt_check, "is", $ujian_id, $userid);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) == 0) {
            throw new Exception('Anda tidak memiliki akses ke ujian ini');
        }

        // Mulai transaksi
        mysqli_begin_transaction($koneksi);

        if ($jenis_soal == 'pilihan_ganda') {
            // Query untuk soal pilihan ganda
            $query = "INSERT INTO bank_soal (ujian_id, jenis_soal, pertanyaan, jawaban_a, jawaban_b, 
                     jawaban_c, jawaban_d, jawaban_benar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "isssssss", 
                $ujian_id,
                $jenis_soal,
                $pertanyaan,
                $_POST['jawaban_a'],
                $_POST['jawaban_b'],
                $_POST['jawaban_c'],
                $_POST['jawaban_d'],
                $_POST['jawaban_benar']
            );
        } else {
            // Query untuk soal uraian
            $query = "INSERT INTO bank_soal (ujian_id, jenis_soal, pertanyaan) VALUES (?, ?, ?)";
            
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "iss", 
                $ujian_id,
                $jenis_soal,
                $pertanyaan
            );
        }

        // Eksekusi query
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($koneksi));
        }

        // Ambil ID soal yang baru dibuat
        $soal_id = mysqli_insert_id($koneksi);

        // Commit transaksi
        mysqli_commit($koneksi);

        // Kirim response sukses
        echo json_encode([
            'status' => 'success',
            'message' => 'Soal berhasil disimpan',
            'soal_id' => $soal_id
        ]);

    } catch (Exception $e) {
        // Rollback jika terjadi error
        mysqli_rollback($koneksi);
        
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