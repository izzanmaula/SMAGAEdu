<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postingan_id = mysqli_real_escape_string($koneksi, $_POST['postingan_id']);
    $konten = mysqli_real_escape_string($koneksi, $_POST['konten']);
    $user_id = $_SESSION['userid'];

    $query = "INSERT INTO komentar_postingan (postingan_id, user_id, konten) 
              VALUES (?, ?, ?)";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "iss", $postingan_id, $user_id, $konten);
    
    if(mysqli_stmt_execute($stmt)) {
        $komentar_id = mysqli_insert_id($koneksi);
        
        $query_komentar = "SELECT k.*,
            g.namaLengkap as nama_guru, g.foto_profil as foto_guru,
            s.nama as nama_siswa, s.foto_profil as foto_siswa,
            CASE 
                WHEN g.username IS NOT NULL THEN 'guru'
                ELSE 'siswa'
            END as user_type
            FROM komentar_postingan k
            LEFT JOIN guru g ON k.user_id = g.username 
            LEFT JOIN siswa s ON k.user_id = s.username
            WHERE k.id = ?";

        $stmt = mysqli_prepare($koneksi, $query_komentar);
        mysqli_stmt_bind_param($stmt, "i", $komentar_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $komentar = mysqli_fetch_assoc($result);

        // Set foto profil berdasarkan tipe user
        $komentar['foto_profil'] = $komentar['user_type'] == 'guru' ? 
            ($komentar['foto_guru'] ? 'uploads/profil/'.$komentar['foto_guru'] : 'assets/pp.png') :
            ($komentar['foto_siswa'] ? $komentar['foto_siswa'] : 'assets/pp-siswa.png');
            
        $komentar['nama_user'] = $komentar['user_type'] == 'guru' ? 
            $komentar['nama_guru'] : $komentar['nama_siswa'];

        echo json_encode([
            'status' => 'success',
            'komentar' => $komentar
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menambahkan komentar'
        ]);
    }
}
?>