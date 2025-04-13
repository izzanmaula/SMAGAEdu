<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

// Di awal file tambah_postingan.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kelas_id = mysqli_real_escape_string($koneksi, $_POST['kelas_id']);
    $konten = mysqli_real_escape_string($koneksi, $_POST['konten']);
    $user_id = $_SESSION['userid'];
    
    // Insert postingan
    $query = "INSERT INTO postingan_kelas (kelas_id, user_id, konten) VALUES ('$kelas_id', '$user_id', '$konten')";
    
    if(mysqli_query($koneksi, $query)) {
        $post_id = mysqli_insert_id($koneksi);
        
        // Handle file uploads
        if(isset($_FILES['lampiran'])) {
            $allowed_types = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'application/pdf',
                'application/msword',                // .doc
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                'application/vnd.ms-excel',          // .xls
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                'application/vnd.ms-powerpoint',     // .ppt
                'application/vnd.openxmlformats-officedocument.presentationml.presentation' // .pptx
            ];
            
            $upload_path = 'uploads/';
            
            // Buat direktori jika belum ada
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            foreach($_FILES['lampiran']['tmp_name'] as $key => $tmp_name) {
                if($_FILES['lampiran']['error'][$key] === 0) {
                    $file_type = $_FILES['lampiran']['type'][$key];
                    $file_name = $_FILES['lampiran']['name'][$key];
                    $file_size = $_FILES['lampiran']['size'][$key];
                    
                    // Validasi tipe file
                    if(in_array($file_type, $allowed_types)) {
                        // Generate nama unik untuk file
                        $new_file_name = uniqid() . '_' . $file_name;
                        $file_path = $upload_path . $new_file_name;
                        
                        if(move_uploaded_file($tmp_name, $file_path)) {
                            $query_lampiran = "INSERT INTO lampiran_postingan (postingan_id, tipe_file, nama_file, path_file) 
                                             VALUES ('$post_id', '$file_type', '$file_name', '$file_path')";
                            mysqli_query($koneksi, $query_lampiran);
                        }
                    }
                }
            }
        }
        
        header("Location: kelas_guru.php?id=$kelas_id&status=success");
    } else {
        header("Location: kelas_guru.php?id=$kelas_id&error=gagal_posting");
    }
}
?>