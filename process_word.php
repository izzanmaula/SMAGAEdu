<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'koneksi.php';
require 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

// Cek apakah class PhpWord tersedia
if (!class_exists('PhpOffice\PhpWord\IOFactory')) {
    error_log("CLASS PhpOffice\PhpWord\IOFactory TIDAK DITEMUKAN");
    echo json_encode(['status' => 'error', 'message' => 'PhpWord library not loaded properly']);
    exit;
}

if (!isset($_FILES['fileSoal']) || !isset($_FILES['fileJawaban'])) {
    echo json_encode(['status' => 'error', 'message' => 'Files not uploaded']);
    exit;
}

if ($_FILES['fileSoal']['error'] !== 0 || $_FILES['fileJawaban']['error'] !== 0) {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
    exit;
}

function extractCellText($cell) {
    $text = '';
    foreach ($cell->getElements() as $element) {
        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $textElement) {
                if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                    $text .= $textElement->getText();
                }
            }
        }
    }
    return trim($text);
}

function parseSoal($phpWord) {
    $soalArray = [];
    $currentSoal = null;
    
    foreach ($phpWord->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                foreach ($element->getRows() as $row) {
                    $cells = $row->getCells();
                    if (count($cells) < 2) continue;
                    
                    $firstCell = extractCellText($cells[0]);
                    $secondCell = extractCellText($cells[1]);

                    $firstCell = trim(preg_replace('/[^A-Z0-9]/i', '', $firstCell));
                    
                    if (empty($firstCell)) continue;
                    
                    if (is_numeric($firstCell)) {
                        if ($currentSoal !== null) {
                            $soalArray[] = $currentSoal;
                        }
                        $currentSoal = ['no' => $firstCell, 'soal' => $secondCell, 'pilihan' => []];
                    } elseif ($currentSoal !== null && preg_match('/^[A-D]$/i', $firstCell)) {
                        $currentSoal['pilihan'][strtoupper($firstCell)] = $secondCell;
                    }
                }
            }
        }
    }
    
    if ($currentSoal !== null) {
        $soalArray[] = $currentSoal;
    }
    
    return $soalArray;
}

function parseKunciJawaban($phpWord) {
    $kunciJawaban = [];
    
    foreach ($phpWord->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                foreach ($element->getRows() as $row) {
                    $cells = $row->getCells();
                    if (count($cells) < 2) continue;
                    
                    $no = extractCellText($cells[0]);
                    $jawaban = extractCellText($cells[1]);
                    
                    if (empty($no) || strpos($no, '---') !== false) continue;
                    
                    if (is_numeric($no) && preg_match('/^[A-D]$/i', $jawaban)) {
                        $kunciJawaban[$no] = strtoupper($jawaban);
                    }
                }
            }
        }
    }
    return $kunciJawaban;
}

try {
    error_log("Starting process_word.php execution");
    
    $fileSoal = $_FILES['fileSoal']['tmp_name'];
    $fileJawaban = $_FILES['fileJawaban']['tmp_name'];

    if (empty($fileSoal) || empty($fileJawaban)) {
        throw new Exception('File soal dan jawaban harus diupload');
    }

    $phpWord = \PhpOffice\PhpWord\IOFactory::load($fileSoal);
    $soalArray = parseSoal($phpWord);

    if (empty($soalArray)) {
        throw new Exception('No questions found in document');
    }

    $phpWord = \PhpOffice\PhpWord\IOFactory::load($fileJawaban);
    $kunciJawaban = parseKunciJawaban($phpWord);

    $ujian_id = $_POST['ujian_id'];
    
    mysqli_begin_transaction($koneksi);

    foreach ($soalArray as $soal) {
        $no = $soal['no'];
        $pertanyaan = mysqli_real_escape_string($koneksi, $soal['soal']);
        $jawaban_a = mysqli_real_escape_string($koneksi, $soal['pilihan']['A'] ?? '');
        $jawaban_b = mysqli_real_escape_string($koneksi, $soal['pilihan']['B'] ?? '');
        $jawaban_c = mysqli_real_escape_string($koneksi, $soal['pilihan']['C'] ?? '');
        $jawaban_d = mysqli_real_escape_string($koneksi, $soal['pilihan']['D'] ?? '');
        $jawaban_benar = $kunciJawaban[$no] ?? '';

        $query = "INSERT INTO bank_soal (ujian_id, jenis_soal, pertanyaan, jawaban_a, jawaban_b, jawaban_c, jawaban_d, jawaban_benar) 
                 VALUES (?, 'pilihan_ganda', ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'issssss', 
            $ujian_id, $pertanyaan, 
            $jawaban_a, $jawaban_b, $jawaban_c, $jawaban_d, $jawaban_benar
        );
        mysqli_stmt_execute($stmt);
    }

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    if (isset($koneksi)) mysqli_rollback($koneksi);
    error_log("Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    throw $e;
}
?>