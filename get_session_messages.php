<?php
session_start();
require "koneksi.php";

// Pastikan user sudah login
if (!isset($_SESSION['userid'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Ambil session_id dari parameter
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : null;

if (!$session_id) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Session ID not provided']);
    exit();
}

// Query untuk mengambil pesan dari session tertentu, menggunakan tabel yang benar
$query = "SELECT pesan, respons, created_at 
          FROM ai_chat_history 
          WHERE session_id = ? 
          ORDER BY created_at ASC";

try {
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($messages);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$koneksi->close();
?>