<?php
header('Content-Type: application/json');
require "koneksi.php";

try {
    // Determine input method
    $input = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($input, 'application/json') !== false) {
        // JSON input
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        
        if (!$data) {
            throw new Exception('Invalid JSON data');
        }
        
        $userId = mysqli_real_escape_string($koneksi, $data['user_id'] ?? '');
        $pesan = mysqli_real_escape_string($koneksi, $data['pesan'] ?? '');
        $respons = mysqli_real_escape_string($koneksi, $data['respons'] ?? '');
    } else {
        // Form data input
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method');
        }
        
        $userId = mysqli_real_escape_string($koneksi, $_POST['user_id'] ?? '');
        $pesan = mysqli_real_escape_string($koneksi, $_POST['pesan'] ?? '');
        $respons = mysqli_real_escape_string($koneksi, $_POST['respons'] ?? '');
    }
    
    // Validate required fields
    if (empty($userId) || empty($pesan) || empty($respons)) {
        throw new Exception('Missing required fields');
    }

    // Get or create session
    $result = $koneksi->query("SELECT id FROM ai_chat_sessions 
        WHERE user_id = '$userId' 
        AND DATE(created_at) = CURDATE() 
        AND closed_at IS NULL 
        ORDER BY id DESC LIMIT 1");

    if ($result->num_rows == 0) {
        // Create new session
        $topic = getTopicSummary([['pesan' => $pesan, 'respons' => $respons]]);
        $stmt = $koneksi->prepare("INSERT INTO ai_chat_sessions (user_id, title) VALUES (?, ?)");
        $stmt->bind_param("ss", $userId, $topic);
        $stmt->execute();
        $session_id = $koneksi->insert_id;
    } else {
        // Update existing session
        $row = $result->fetch_assoc();
        $session_id = $row['id'];
        
        $convResult = $koneksi->query("SELECT pesan, respons FROM ai_chat_history WHERE session_id = $session_id");
        $conversation = [];
        while ($chatRow = $convResult->fetch_assoc()) {
            $conversation[] = $chatRow;
        }
        $conversation[] = ['pesan' => $pesan, 'respons' => $respons];
        
        $topic = getTopicSummary($conversation);
        
        $stmt = $koneksi->prepare("UPDATE ai_chat_sessions SET title = ? WHERE id = ?");
        $stmt->bind_param("si", $topic, $session_id);
        $stmt->execute();
    }

    // Save message
    $stmt = $koneksi->prepare("INSERT INTO ai_chat_history (user_id, session_id, pesan, respons) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $userId, $session_id, $pesan, $respons);
    $stmt->execute();

    echo json_encode(['success' => true, 'session_id' => $session_id, 'topic' => $topic]);

} catch (JsonException $e) {
    error_log('JSON Parse Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid JSON: ' . $e->getMessage(),
        'raw_input' => $rawInput
    ]);
} catch (Exception $e) {
    error_log('Save chat error: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'raw_input' => $rawInput
    ]);
}


function getTopicSummary($conversation) {
    $apiKey = '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';
    $apiEndpoint = 'https://api.groq.com/openai/v1/chat/completions';

    // Error handling for empty conversation
    if (empty($conversation)) {
        return "Percakapan Baru";
    }

    $messages = [
        ["role" => "system", "content" => "Berikan komentar mengenai percakapan berikut:"],
    ];

    // Add error handling for message construction
    try {
        foreach ($conversation as $chat) {
            if (!isset($chat['pesan']) || !isset($chat['respons'])) {
                continue;
            }
            $messages[] = ["role" => "user", "content" => $chat['pesan']];
            $messages[] = ["role" => "assistant", "content" => $chat['respons']];
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json'
                ],
                'content' => json_encode([
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => $messages,
                    'temperature' => 0.3,
                    'max_tokens' => 30,
                    'top_p' => 1
                ]),
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($apiEndpoint, false, $context);
        
        if ($response === false) {
            throw new Exception('Failed to get topic from API');
        }
        
        $result = json_decode($response, true);
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception('Invalid API response format');
        }
        
        return trim($result['choices'][0]['message']['content']);

    } catch (Exception $e) {
        error_log('Topic generation error: ' . $e->getMessage());
        return substr($conversation[0]['pesan'] ?? "Percakapan Baru", 0, 50);
    }
}
?>