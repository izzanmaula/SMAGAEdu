<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ujian_id = $_POST['ujian_id'];
    $jenis_soal = $_POST['jenis_soal'];
    $pertanyaan = $_POST['pertanyaan'];
    
    // Handle image upload
    $gambar_soal = null;
    if(isset($_FILES['gambar_soal']) && $_FILES['gambar_soal']['error'] == 0) {
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
    
    if($jenis_soal == 'pilihan_ganda') {
        $jawaban_a = $_POST['jawaban_a'];
        $jawaban_b = $_POST['jawaban_b'];
        $jawaban_c = $_POST['jawaban_c'];
        $jawaban_d = $_POST['jawaban_d'];
        $jawaban_benar = $_POST['jawaban_benar'];
        
        $query = "INSERT INTO bank_soal (ujian_id, jenis_soal, pertanyaan, gambar_soal, jawaban_a, jawaban_b, jawaban_c, jawaban_d, jawaban_benar) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "issssssss", $ujian_id, $jenis_soal, $pertanyaan, $gambar_soal, $jawaban_a, $jawaban_b, $jawaban_c, $jawaban_d, $jawaban_benar);
    } else {
        $query = "INSERT INTO bank_soal (ujian_id, jenis_soal, pertanyaan, gambar_soal) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "isss", $ujian_id, $jenis_soal, $pertanyaan, $gambar_soal);
    }
    
    if(mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($koneksi)]);
    }
}
?>