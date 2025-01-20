<?php
session_start();
require "koneksi.php";

// Ambil data dari POST
$ujian_id = $_POST['ujian_id'] ?? null;
$answers = json_decode($_POST['answers'], true);

if(!$ujian_id || !$answers) {
    echo json_encode(['success' => false, 'error' => 'Data tidak lengkap']);
    exit();
}

// Ambil ID siswa dari username
$query_siswa = "SELECT id FROM siswa WHERE username = ?";
$stmt_siswa = $koneksi->prepare($query_siswa);
$stmt_siswa->bind_param("s", $_SESSION['userid']);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();
$siswa = $result_siswa->fetch_assoc();
$siswa_id = $siswa['id'];

// Ambil soal-soal untuk ujian ini
$query_soal = "SELECT id FROM bank_soal WHERE ujian_id = ?";
$stmt_soal = $koneksi->prepare($query_soal);
$stmt_soal->bind_param("i", $ujian_id);
$stmt_soal->execute();
$result_soal = $stmt_soal->get_result();
$soal_ids = $result_soal->fetch_all(MYSQLI_ASSOC);

try {
    // Bersihkan jawaban sebelumnya jika ada
    $query_delete = "DELETE FROM jawaban_ujian WHERE ujian_id = ? AND siswa_id = ?";
    $stmt_delete = $koneksi->prepare($query_delete);
    $stmt_delete->bind_param("ii", $ujian_id, $siswa_id);
    $stmt_delete->execute();

    foreach($soal_ids as $index => $soal) {
        $jawaban = $answers[$index] ?? null;
        
        if ($jawaban !== null) {
            $query = "INSERT INTO jawaban_ujian (ujian_id, siswa_id, soal_id, jawaban) 
                     VALUES (?, ?, ?, ?)";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("iiss", $ujian_id, $siswa_id, $soal['id'], $jawaban);
            $stmt->execute();
        }
    }

    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>