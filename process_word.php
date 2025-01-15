<?php
// Debug mode
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log semua yang diterima
error_log("Files received: " . print_r($_FILES, true));
error_log("POST data: " . print_r($_POST, true));

require 'koneksi.php';
require 'vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;

// Inisialisasi variabel
$soalArray = [];
$kunciJawaban = [];

// Pengecekan file yang lebih spesifik
if (empty($_FILES['fileSoal']['tmp_name']) || empty($_FILES['fileJawaban']['tmp_name'])) {
    error_log("File soal atau jawaban tidak ditemukan");
    echo json_encode([
        'status' => 'error',
        'message' => 'File soal dan jawaban harus diupload'
    ]);
    exit;
}

try {
    // Baca file soal
    $fileSoal = $_FILES['fileSoal']['tmp_name'];
    $phpWord = IOFactory::load($fileSoal);
    
    // Array untuk menyimpan soal
    $soalArray = [];
    $currentSoal = null;
    $currentPilihan = [];
    $soalCounter = 0;
    
    foreach ($phpWord->getSections() as $section) {
        error_log("Memproses section baru");
        
        // Kumpulkan semua text elements
        $elements = [];
        foreach ($section->getElements() as $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                $text = '';
                foreach ($element->getElements() as $textElement) {
                    if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                        $text .= $textElement->getText();
                    }
                }
                $text = trim($text);
                if (!empty($text)) {
                    $elements[] = $text;
                    error_log("Raw element: [$text]");
                }
            }
        }
        
        // Proses setiap elemen
        for ($i = 0; $i < count($elements); $i++) {
            $text = trim($elements[$i]);
            
            // Debug log
            error_log("Processing element: [$text]");
            
            // Deteksi awal soal baru (angka di awal baris)
            if (preg_match('/^\d+/', $text)) {
                error_log("Potential new question detected: $text");
                
                // Simpan soal sebelumnya jika ada
                if ($currentSoal !== null && !empty($currentPilihan)) {
                    $soalArray[] = [
                        'pertanyaan' => trim($currentSoal),
                        'pilihan' => $currentPilihan
                    ];
                    error_log("Saved previous question: " . json_encode([
                        'pertanyaan' => trim($currentSoal),
                        'pilihan' => $currentPilihan
                    ]));
                }
                
                // Mulai soal baru
                $currentSoal = preg_replace('/^\d+\.?\s*/', '', $text);
                $currentPilihan = [];
                $soalCounter++;
                error_log("Started new question #$soalCounter: $currentSoal");
            }
            // Deteksi pilihan jawaban (a. b. c. d.)
            elseif (preg_match('/^[a-d][\.\)]?\s+(.+)/i', $text, $matches)) {
                $huruf = strtoupper(substr($text, 0, 1));
                $pilihan = trim($matches[1]);
                $currentPilihan[$huruf] = $pilihan;
                error_log("Added option $huruf: $pilihan");
            }
            // Jika bukan keduanya dan ada soal aktif, anggap sebagai lanjutan soal
            elseif ($currentSoal !== null) {
                $currentSoal .= ' ' . $text;
                error_log("Appended to current question: $text");
            }
        }
    }
    
    // Jangan lupa simpan soal terakhir
    if ($currentSoal !== null && !empty($currentPilihan)) {
        $soalArray[] = [
            'pertanyaan' => trim($currentSoal),
            'pilihan' => $currentPilihan
        ];
        error_log("Saved last question: " . json_encode([
            'pertanyaan' => trim($currentSoal),
            'pilihan' => $currentPilihan
        ]));
    }
    
    error_log("Total soal terparsing: " . count($soalArray));
    error_log("Detail soal: " . json_encode($soalArray, JSON_PRETTY_PRINT));

    // Parsing kunci jawaban
    $fileJawaban = $_FILES['fileJawaban']['tmp_name'];
    $phpWord = IOFactory::load($fileJawaban);
    $kunciJawaban = [];
    
    foreach ($phpWord->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                $text = '';
                foreach ($element->getElements() as $textElement) {
                    if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                        $text .= $textElement->getText();
                    }
                }
                $text = trim($text);
                
                // Perbaikan regex untuk kunci jawaban
                if (!empty($text)) {
                    error_log("Processing answer text: [$text]");
                    
                    // Cek jika ada huruf A-D dalam text
                    if (preg_match('/[A-D]/i', $text)) {
                        $jawaban = strtoupper(preg_replace('/[^A-Da-d]/', '', $text));
                        if (!empty($jawaban)) {
                            $kunciJawaban[] = $jawaban;
                            error_log("Added answer: $jawaban");
                        }
                    }
                }
            }
        }
    }
    
    error_log("Total jawaban terparsing: " . count($kunciJawaban));
    error_log("Detail jawaban: " . json_encode($kunciJawaban));    
    
    // // Debug: Print arrays
    // echo json_encode([
    //     'soal' => $soalArray,
    //     'kunci' => $kunciJawaban
    // ]);
    // exit;

    // Simpan ke database
    $ujian_id = $_POST['ujian_id'];
    $success = true;
    mysqli_begin_transaction($koneksi);

    for ($i = 0; $i < count($soalArray); $i++) {
        if (isset($kunciJawaban[$i])) {
            $soal = $soalArray[$i];
            $pertanyaan = mysqli_real_escape_string($koneksi, $soal['pertanyaan']);
            $jawaban_a = mysqli_real_escape_string($koneksi, $soal['pilihan']['A'] ?? '');
            $jawaban_b = mysqli_real_escape_string($koneksi, $soal['pilihan']['B'] ?? '');
            $jawaban_c = mysqli_real_escape_string($koneksi, $soal['pilihan']['C'] ?? '');
            $jawaban_d = mysqli_real_escape_string($koneksi, $soal['pilihan']['D'] ?? '');
            $jawaban_benar = $kunciJawaban[$i];

            $query = "INSERT INTO bank_soal (ujian_id, jenis_soal, pertanyaan, jawaban_a, jawaban_b, jawaban_c, jawaban_d, jawaban_benar) 
                     VALUES ('$ujian_id', 'pilihan_ganda', '$pertanyaan', '$jawaban_a', '$jawaban_b', '$jawaban_c', '$jawaban_d', '$jawaban_benar')";
            
            if (!mysqli_query($koneksi, $query)) {
                $success = false;
                break;
            }
        }
    }

    if ($success) {
        mysqli_commit($koneksi);
        echo json_encode([
            'status' => 'success',
            'message' => 'Soal berhasil diimport',
            'data' => [
                'total_soal' => count($soalArray)
            ]
        ]);
    } else {
        mysqli_rollback($koneksi);
        throw new Exception('Gagal menyimpan soal ke database');
    }

} catch (Exception $e) {
    if (isset($koneksi)) {
        mysqli_rollback($koneksi);
    }
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>