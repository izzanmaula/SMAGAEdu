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

function cleanOptionText($text)
{
    // Hapus format seperti "**A. " atau simbol bullet lainnya
    $text = preg_replace('/\*+[A-D]\.\s*/i', '', $text);
    $text = preg_replace('/^[A-D]\.\s*/i', '', $text);
    return trim($text);
}

function extractCellText($cell)
{
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
    // Dekode entitas HTML sebelum mengembalikan teks
    return html_entity_decode(trim($text));
}

// Fungsi untuk menyimpan gambar
function saveImageFromElement($imageElement, $questionNumber, $optionLetter = null, $imageCount = 1)
{
    try {
        // Buat direktori jika belum ada
        $uploadDir = 'uploads/question_images/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Buat nama file unik
        $identifier = $questionNumber;
        if ($optionLetter) {
            $identifier .= "_option_" . $optionLetter;
        }
        $filename = 'question_' . $identifier . '_img_' . $imageCount . '.png';
        $fullPath = $uploadDir . $filename;

        // Ambil dan simpan data gambar
        if (method_exists($imageElement, 'getImageString')) {
            $imageData = $imageElement->getImageString();
        } else if (method_exists($imageElement, 'getContent')) {
            $imageData = $imageElement->getContent();
        } else {
            // Gunakan refleksi untuk mengakses properti private
            $reflection = new ReflectionObject($imageElement);
            $property = $reflection->getProperty('imageString');
            $property->setAccessible(true);
            $imageData = $property->getValue($imageElement);
        }

        file_put_contents($fullPath, $imageData);

        return $fullPath;
    } catch (Exception $e) {
        error_log("Error saving image: " . $e->getMessage());
        return false;
    }
}

// Fungsi untuk mengekstrak gambar dari sel
function extractCellImages($cell, $questionNumber, $optionLetter = null)
{
    $images = [];
    $imageCount = 0;

    foreach ($cell->getElements() as $element) {
        // Cek gambar langsung dalam sel
        if ($element instanceof \PhpOffice\PhpWord\Element\Image) {
            $imageCount++;
            $filename = saveImageFromElement($element, $questionNumber, $optionLetter, $imageCount);
            if ($filename) {
                $images[] = $filename;
            }
        }
        // Cek gambar dalam TextRun
        else if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $textElement) {
                if ($textElement instanceof \PhpOffice\PhpWord\Element\Image) {
                    $imageCount++;
                    $filename = saveImageFromElement($textElement, $questionNumber, $optionLetter, $imageCount);
                    if ($filename) {
                        $images[] = $filename;
                    }
                }
            }
        }
    }

    return $images;
}

// Fungsi untuk parse soal
function parseSoal($phpWord)
{
    $soalArray = [];
    $currentSoal = null;

    foreach ($phpWord->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                foreach ($element->getRows() as $row) {
                    $cells = $row->getCells();
                    if (count($cells) < 2) continue;

                    $firstCell = extractCellText($cells[0]);
                    $firstCell = trim(preg_replace('/[^A-Z0-9]/i', '', $firstCell));

                    if (empty($firstCell)) continue;

                    if (is_numeric($firstCell)) {
                        if ($currentSoal !== null) {
                            $soalArray[] = $currentSoal;
                        }

                        // Ekstrak teks dan gambar
                        $soalText = extractCellText($cells[1]);
                        $soalImages = extractCellImages($cells[1], $firstCell);

                        $currentSoal = [
                            'no' => $firstCell,
                            'soal' => $soalText,
                            'soal_images' => $soalImages,
                            'pilihan' => [],
                            'pilihan_images' => []
                        ];
                    } elseif ($currentSoal !== null && preg_match('/^[A-D]$/i', $firstCell)) {
                        $optionLetter = strtoupper($firstCell);
                        $optionText = extractCellText($cells[1]);
                        $optionText = cleanOptionText($optionText); // Bersihkan format
                        $optionImages = extractCellImages($cells[1], $currentSoal['no'], $optionLetter);

                        $currentSoal['pilihan'][$optionLetter] = $optionText;
                        $currentSoal['pilihan_images'][$optionLetter] = $optionImages;
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


// Tambahkan fungsi pembersihan yang lebih komprehensif
function cleanText($text)
{
    // Hapus escape character (backslash)
    $text = stripslashes($text);

    // Hapus formatting bold dari Word (**) dan tanda bintang lainnya
    $text = preg_replace('/\*+/', '', $text);

    // Hapus label pilihan (A., B., C., D.) di awal teks
    $text = preg_replace('/^[A-D]\.\s*/i', '', $text);

    // Normalisasi apostrof (berbagai jenis apostrof menjadi satu standar)
    $text = str_replace(array('\'', "'", "'", '`', '´', "'", '&apos;', '&#039;'), "'", $text);

    // Decode HTML entities
    $text = html_entity_decode($text);

    // Normalisasi spasi
    $text = preg_replace('/\s+/', ' ', $text);

    return trim($text);
}

function parseKunciJawaban($phpWord)
{
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

// Mulai bagian utama program
try {
    error_log("Starting process_word.php execution");

    if (!isset($_FILES['fileSoal']) || !isset($_FILES['fileJawaban'])) {
        echo json_encode(['status' => 'error', 'message' => 'Files not uploaded']);
        exit;
    }

    if ($_FILES['fileSoal']['error'] !== 0 || $_FILES['fileJawaban']['error'] !== 0) {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
        exit;
    }

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
        $pertanyaan = cleanText($soal['soal']);
        $pertanyaan = mysqli_real_escape_string($koneksi, $pertanyaan);
        $gambar_soal = null;

        // Simpan gambar pertanyaan jika ada
        if (!empty($soal['soal_images'])) {
            $gambar_soal = $soal['soal_images'][0]; // ambil gambar pertama
        }

        $jawaban_a = cleanText($soal['pilihan']['A'] ?? '');
        $jawaban_b = cleanText($soal['pilihan']['B'] ?? '');
        $jawaban_c = cleanText($soal['pilihan']['C'] ?? '');
        $jawaban_d = cleanText($soal['pilihan']['D'] ?? '');

        $jawaban_a = mysqli_real_escape_string($koneksi, $jawaban_a);
        $jawaban_b = mysqli_real_escape_string($koneksi, $jawaban_b);
        $jawaban_c = mysqli_real_escape_string($koneksi, $jawaban_c);
        $jawaban_d = mysqli_real_escape_string($koneksi, $jawaban_d);


        // Tambahkan tag img untuk gambar pilihan jawaban
        if (!empty($soal['pilihan_images']['A'])) {
            foreach ($soal['pilihan_images']['A'] as $imagePath) {
                $jawaban_a .= "<br><img src='{$imagePath}' class='option-image'>";
            }
        }
        if (!empty($soal['pilihan_images']['B'])) {
            foreach ($soal['pilihan_images']['B'] as $imagePath) {
                $jawaban_b .= "<br><img src='{$imagePath}' class='option-image'>";
            }
        }
        if (!empty($soal['pilihan_images']['C'])) {
            foreach ($soal['pilihan_images']['C'] as $imagePath) {
                $jawaban_c .= "<br><img src='{$imagePath}' class='option-image'>";
            }
        }
        if (!empty($soal['pilihan_images']['D'])) {
            foreach ($soal['pilihan_images']['D'] as $imagePath) {
                $jawaban_d .= "<br><img src='{$imagePath}' class='option-image'>";
            }
        }

        $jawaban_benar = $kunciJawaban[$no] ?? '';

        $query = "INSERT INTO bank_soal (ujian_id, jenis_soal, pertanyaan, gambar_soal, jawaban_a, jawaban_b, jawaban_c, jawaban_d, jawaban_benar) 
                 VALUES (?, 'pilihan_ganda', ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param(
            $stmt,
            'isssssss',
            $ujian_id,
            $pertanyaan,
            $gambar_soal,
            $jawaban_a,
            $jawaban_b,
            $jawaban_c,
            $jawaban_d,
            $jawaban_benar
        );
        mysqli_stmt_execute($stmt);
    }

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    if (isset($koneksi)) mysqli_rollback($koneksi);
    error_log("Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
