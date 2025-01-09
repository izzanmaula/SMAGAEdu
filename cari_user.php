<?php
require "koneksi.php";

if(isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);
    
    // Query untuk mencari guru
    $query_guru = "SELECT username, namaLengkap, jabatan FROM guru 
                  WHERE namaLengkap LIKE '%$keyword%' OR username LIKE '%$keyword%'";
    $result_guru = mysqli_query($koneksi, $query_guru);
    
    // Query untuk mencari siswa
    $query_siswa = "SELECT username, nama FROM siswa 
                   WHERE nama LIKE '%$keyword%' OR username LIKE '%$keyword%'";
    $result_siswa = mysqli_query($koneksi, $query_siswa);
    
    $results = array('guru' => array(), 'siswa' => array());
    
    // Mengumpulkan hasil pencarian guru
    while($row = mysqli_fetch_assoc($result_guru)) {
        $results['guru'][] = $row;
    }
    
    // Mengumpulkan hasil pencarian siswa
    while($row = mysqli_fetch_assoc($result_siswa)) {
        $results['siswa'][] = $row;
    }
    
    echo json_encode($results);
}
?>