<?php
class AIUserAnalysis {
    private $koneksi;
    private $userid;
    private $userLevel;
    
    public function __construct($koneksi, $userid, $userLevel) {
        $this->koneksi = $koneksi;
        $this->userid = $userid;
        $this->userLevel = $userLevel;
    }

    public function getUserProfile() {
        $table = $this->userLevel == 'guru' ? 'guru' : 'siswa';
        $query = "SELECT * FROM $table WHERE username = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("s", $this->userid);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getConversationAnalysis() {
        $query = "SELECT pesan, respons, created_at, character_traits 
                 FROM ai_chat_history 
                 WHERE user_id = ? 
                 ORDER BY created_at DESC 
                 LIMIT 50";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("s", $this->userid);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function saveCharacterAnalysis($traits) {
        $query = "INSERT INTO user_character_analysis 
                 (user_id, kerjasama, analitis, detail, inisiatif, komunikatif) 
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE 
                 kerjasama = VALUES(kerjasama),
                 analitis = VALUES(analitis),
                 detail = VALUES(detail),
                 inisiatif = VALUES(inisiatif),
                 komunikatif = VALUES(komunikatif)";
        
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("sddddd", 
            $this->userid,
            $traits['kerjasama'],
            $traits['analitis'],
            $traits['detail'],
            $traits['inisiatif'],
            $traits['komunikatif']
        );
        $stmt->execute();
    }

    private function saveTopics($topics) {
        foreach ($topics as $topic => $count) {
            $query = "INSERT INTO user_topics (user_id, topic, frequency)
                     VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE 
                     frequency = frequency + VALUES(frequency)";
            
            $stmt = $this->koneksi->prepare($query);
            $stmt->bind_param("ssi", $this->userid, $topic, $count);
            $stmt->execute();
        }
    }

    private function analyzeTopics($conversations) {
        $topics = [];
        foreach ($conversations as $conv) {
            $words = explode(' ', strtolower($conv['pesan']));
            foreach ($words as $word) {
                if (strlen($word) > 3 && !in_array($word, ['yang', 'dengan', 'untuk', 'dari', 'pada', 'dalam', 'akan', 'saya', 'anda'])) {
                    if (!isset($topics[$word])) {
                        $topics[$word] = 0;
                    }
                    $topics[$word]++;
                }
            }
        }
        
        arsort($topics);
        return array_slice($topics, 0, 5);
    }

    private function analyzeCharacterTraits($conversations) {
        $traits = [
            'kerjasama' => 0,
            'analitis' => 0,
            'detail' => 0,
            'inisiatif' => 0,
            'komunikatif' => 0
        ];
        
        foreach ($conversations as $conv) {
            if (stripos($conv['pesan'], 'tolong') !== false || 
                stripos($conv['pesan'], 'bantu') !== false) {
                $traits['kerjasama'] += 0.1;
            }
            if (stripos($conv['pesan'], 'mengapa') !== false || 
                stripos($conv['pesan'], 'bagaimana') !== false) {
                $traits['analitis'] += 0.1;
            }
            if (stripos($conv['pesan'], 'detail') !== false || 
                stripos($conv['pesan'], 'rinci') !== false) {
                $traits['detail'] += 0.1;
            }
            if (stripos($conv['pesan'], 'ide') !== false || 
                stripos($conv['pesan'], 'saran') !== false) {
                $traits['inisiatif'] += 0.1;
            }
            if (substr_count($conv['pesan'], '?') > 0 || 
                stripos($conv['pesan'], 'jelaskan') !== false) {
                $traits['komunikatif'] += 0.1;
            }
        }
        
        array_walk($traits, function(&$value) {
            $value = min($value, 1.0);
        });
        
        return $traits;
    }


    public function generateReport() {
        $profile = $this->getUserProfile();
        $conversations = $this->getConversationAnalysis();
        
        $topics = $this->analyzeTopics($conversations);
        $interactionPatterns = $this->analyzeInteractionPatterns($conversations);
        $characterTraits = $this->analyzeCharacterTraits($conversations);
        
        $this->saveCharacterAnalysis($characterTraits);
        $this->saveTopics($topics);
        
        return [
            'user_info' => [
                'name' => $profile['namaLengkap'],
                'level' => $this->userLevel,
                'total_conversations' => count($conversations)
            ],
            'conversation_analysis' => [
                'common_topics' => $topics,
                'interaction_patterns' => $interactionPatterns,
                'character_traits' => $characterTraits,
                'last_interaction' => !empty($conversations) ? $conversations[0]['created_at'] : null
            ]
        ];
    }


    private function analyzeInteractionPatterns($conversations) {
        $patterns = [
            'avg_message_length' => 0,
            'question_frequency' => 0,
            'command_frequency' => 0
        ];

        if (empty($conversations)) return $patterns;

        $total_length = 0;
        foreach ($conversations as $conv) {
            $total_length += strlen($conv['pesan']);
            if (substr($conv['pesan'], -1) == '?') {
                $patterns['question_frequency']++;
            }
            if (strpos($conv['pesan'], 'tolong') !== false || 
                strpos($conv['pesan'], 'bisa') !== false) {
                $patterns['command_frequency']++;
            }
        }

        $patterns['avg_message_length'] = round($total_length / count($conversations));
        return $patterns;
    }
}

function generateAIReport($koneksi, $userid, $userLevel) {
    $analyzer = new AIUserAnalysis($koneksi, $userid, $userLevel);
    return $analyzer->generateReport();
}