<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Tambahkan ini untuk mengatasi CORS
error_reporting(E_ALL);
ini_set('display_errors', 0);

require "koneksi.php";

try {
    if (!isset($_GET['id'])) {
        throw new Exception('No ID provided');
    }

    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    $query = "SELECT 
        p.id,
        p.siswa_id,
        p.file_path, 
        p.tipe_file,
        p.ukuran_file,
        p.waktu_pengumpulan,
        p.nilai,
        p.komentar_guru,
        p.pesan_siswa,
        s.nama as nama_siswa
    FROM pengumpulan_tugas p
    JOIN siswa s ON p.siswa_id = s.username
    WHERE p.id = '$id'";

    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        throw new Exception(mysqli_error($koneksi));
    }

// Di dalam blok if yang ada
if ($data = mysqli_fetch_assoc($result)) {
    $file_name = basename($data['file_path']);
    
    // Cek apakah file_path adalah Google Drive URL
    if (strpos($data['file_path'], 'drive.google.com') !== false) {
        $file_url = $data['file_path']; // Gunakan URL Google Drive langsung
    } else {
        // Buat URL lengkap untuk file lokal
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $file_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $data['file_path'];
    }
    
    $response = [
        'success' => true,
        'nilai' => $data['nilai'],
        'komentar' => $data['komentar_guru'],
        'waktu_pengumpulan' => $data['waktu_pengumpulan'],
        'pesan' => $data['pesan_siswa'],
        'files' => [
            [
                'name' => $file_name,
                'url' => $file_url,
                'type' => $data['tipe_file'],
                'size' => $data['ukuran_file']
            ]
        ]
    ];
    
    echo json_encode($response);
} else {
        throw new Exception('No data found for ID: ' . $id);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>