<?php
session_start();
require "koneksi.php";

$input = json_decode(file_get_contents('php://input'), true);
$session_id = $input['session_id'];
$userid = $_SESSION['userid'];

// Delete entire session and its chats
$stmt = $koneksi->prepare("DELETE h, s FROM ai_chat_history h 
    JOIN ai_chat_sessions s ON h.session_id = s.id 
    WHERE s.id = ? AND s.user_id = ?");
$stmt->bind_param("is", $session_id, $userid);
$success = $stmt->execute();

echo json_encode(['success' => $success]);
?>