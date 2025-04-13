<?php
session_start();
require "koneksi.php";

// Terima data dari POST atau raw JSON (untuk beacon)
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($contentType === 'application/json') {
    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);
    
    $ujian_id = $decoded['ujian_id'];
    $siswa_id = $decoded['siswa_id'];
} else {
    $ujian_id = $_POST['ujian_id'];
    $siswa_id = $_POST['siswa_id'];
}

// Hapus jawaban dari database
$query = "DELETE FROM jawaban_ujian WHERE ujian_id = ? AND siswa_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("ii", $ujian_id, $siswa_id);
$stmt->execute();

// Return success response
echo json_encode(['success' => true]);