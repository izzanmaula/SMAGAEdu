<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $soal_id = $_POST['soal_id'];
    $pertanyaan = $_POST['pertanyaan'];
    
    // Handle image upload
    if(isset($_FILES['gambar_soal']) && $_FILES['gambar_soal']['error'] == 0) {
        // Get existing image
        $query = "SELECT gambar_soal FROM bank_soal WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $soal_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        // Delete old image if exists
        if($row['gambar_soal'] && file_exists($row['gambar_soal'])) {
            unlink($row['gambar_soal']);
        }
        
        // Upload new image
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar_soal']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = 'uploads/soal/' . $new_filename;
            
            if(move_uploaded_file($_FILES['gambar_soal']['tmp_name'], $upload_path)) {
                $gambar_soal = $upload_path;
            }
        }
    }

    if($_POST['jenis_soal'] == 'pilihan_ganda') {
        $query = isset($gambar_soal) ? 
            "UPDATE bank_soal SET pertanyaan=?, gambar_soal=?, jawaban_a=?, jawaban_b=?, jawaban_c=?, jawaban_d=?, jawaban_benar=? WHERE id=?" :
            "UPDATE bank_soal SET pertanyaan=?, jawaban_a=?, jawaban_b=?, jawaban_c=?, jawaban_d=?, jawaban_benar=? WHERE id=?";
        
        $stmt = mysqli_prepare($koneksi, $query);
        if(isset($gambar_soal)) {
            mysqli_stmt_bind_param($stmt, "sssssssi", $pertanyaan, $gambar_soal, $_POST['jawaban_a'], $_POST['jawaban_b'], $_POST['jawaban_c'], $_POST['jawaban_d'], $_POST['jawaban_benar'], $soal_id);
        } else {
            mysqli_stmt_bind_param($stmt, "ssssssi", $pertanyaan, $_POST['jawaban_a'], $_POST['jawaban_b'], $_POST['jawaban_c'], $_POST['jawaban_d'], $_POST['jawaban_benar'], $soal_id);
        }
    } else {
        $query = isset($gambar_soal) ? 
            "UPDATE bank_soal SET pertanyaan=?, gambar_soal=? WHERE id=?" :
            "UPDATE bank_soal SET pertanyaan=? WHERE id=?";
            
        $stmt = mysqli_prepare($koneksi, $query);
        if(isset($gambar_soal)) {
            mysqli_stmt_bind_param($stmt, "ssi", $pertanyaan, $gambar_soal, $soal_id);
        } else {
            mysqli_stmt_bind_param($stmt, "si", $pertanyaan, $soal_id);
        }
    }
    
    if(mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($koneksi)]);
    }
}
?>