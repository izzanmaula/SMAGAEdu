<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_message = $data['message'] ?? '';
    $user_id = $_SESSION['userid'];

    if (empty($user_message)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Pesan tidak boleh kosong']);
        exit();
    }

    // Konfigurasi API Gemini
    $api_key = 'AIzaSyAm6yuSvkKYnjmlqor8HjciqFiFAwahUgM'; // Ganti dengan API key Anda
    $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    // Persiapkan data untuk dikirim ke API
    $request_data = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $user_message
                    ]
                ]
            ]
        ]
    ];

    // Inisialisasi cURL
    $ch = curl_init($api_url . '?key=' . $api_key);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // Kirim request ke API
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $result = json_decode($response, true);
        $ai_response = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, terjadi kesalahan dalam memproses respons.';

        // Simpan history chat ke database
        $query = "INSERT INTO ai_chat_history (user_id, pesan, respons) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "sss", $user_id, $user_message, $ai_response);
        mysqli_stmt_execute($stmt);

        echo json_encode([
            'status' => 'success',
            'response' => $ai_response
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mendapatkan respons dari AI'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>