<?php
require_once 'Db.php';
require_once 'Helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ChatHistory
{
    private Db $db;

    public function __construct()
    {
        $this->db = new Db();
    }

    public function getChatHistory()
    {
        $sql = 'SELECT role, message FROM chat_history ORDER BY id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $message = [];
        foreach ($result as $row) {
            $message[] = [
                'role' => $row['role'],
                'content' => $row['message'],
            ];
        }
        return response('success', 'Chat history fetched successfully', $message);
    }

    public function addChatHistory($data)
    {
        $sql = 'INSERT INTO chat_history (role, message) VALUES (:role, :message)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':message', $data['message']);
        $stmt->execute();
        return response('success', 'Chat history added successfully', ['id' => $this->db->lastInsertId()]);
    }

    public function sendMessage($data)
    {
        $message = trim($data['message']);

        if (empty($message)) {
            return response('error', 'Message is required', null);
        }

        $this->addChatHistory([
            'role' => 'user',
            'message' => $message,
        ]);

        $history = $this->getChatHistory()['data'];

        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => "I am Sir Dhine, a cheerful and patient K-12 teacher in the Philippines. You use Tagalog and simple English. Answer based on the student's question in a friendly and engaging manner, always asking 'Naiintindihan mo ba?' at the end."
        ];

        foreach ($history as $row) {
            $messages[] = [
                'role' => $row['role'],
                'content' => $row['content']
            ];
        }

        $ch = curl_init(PROXY_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'model' => MODEL_NAME,
                'messages' => $messages,
                'temperature' => 0.6,
                'max_tokens' => 300
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization' => 'Bearer ' . API_KEY
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $reply = 'Sorry, I cannot answer that question right now.';
        $json = json_decode($response, true);

        if (isset($json['choices'][0]['message']['content'])) {
            $reply = trim($json['choices'][0]['message']['content']);
        }

        $this->addChatHistory([
            'role' => 'assistant',
            'message' => $reply,
        ]);

        return response('success', 'Sir Dhine replied', [
            'reply' => $reply,
        ]);
    }
}
?>