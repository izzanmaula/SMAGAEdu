<?php
session_start();
require "koneksi.php";

header('Content-Type: application/json');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['siswa_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing student ID']);
    exit;
}

$siswa_id = $_POST['siswa_id'];
$upload_dir = 'uploads/profil/';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle cropped image
if (isset($_FILES['croppedImage'])) {
    $file = $_FILES['croppedImage'];
    $fileName = time() . '_' . uniqid() . '.jpg';
    $filePath = $upload_dir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Update database
        $query = "UPDATE siswa SET foto_profil = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "si", $fileName, $siswa_id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'filename' => $fileName]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Upload failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No image provided']);
}
?>