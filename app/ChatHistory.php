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

        return response('success', 'Chat history added successfully', [
            'id' => $this->db->lastInsertId()
        ]);
    }

    public function search($data)
    {
        $query = isset($data['q']) ? $data['q'] : '';

        if (empty($query)) {
            return response('error', 'Search query is required', null);
        }

        $postData = ['q' => $query];
        if (isset($data['location'])) {
            $postData['location'] = $data['location'];
        }
        if (isset($data['gl'])) {
            $postData['gl'] = $data['gl'];
        }
        if (isset($data['hl'])) {
            $postData['hl'] = $data['hl'];
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => SERPER_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'X-API-KEY: ' . SERPER_API_KEY,
                'Content-Type: application/json'
            ),
        ));

        $searchResponse = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode === 200) {
            return response('success', 'Search completed successfully', json_decode($searchResponse, true));
        }

        return response('error', 'Search request failed', [
            'http_code' => $httpCode,
            'response' => $searchResponse
        ]);
    }

    public function sendMessage($data)
    {
        $message = trim($data['message'] ?? '');

        if (empty($message)) {
            return response('error', 'Message is required', null);
        }

        $this->addChatHistory([
            'role' => 'user',
            'message' => $message,
        ]);

        $history = $this->getChatHistory()['data'];
        $contents = [];
        foreach ($history as $row) {
            $role = $row['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    [
                        'text' => $row['content']
                    ]
                ]
            ];
        }

        $requestBody = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [
                    [
                        'text' => "I am Sir Dhine, a cheerful and patient K-12 teacher in the Philippines. You use Tagalog and simple English. Answer based on the student's question in a friendly and engaging manner, always asking 'Naiintindihan mo ba?' at the end."
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.6,
                'maxOutputTokens' => 300
            ]
        ];

        $url = PROXY_URL . '?key=' . urlencode(API_KEY);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestBody),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $reply = 'Sorry, I cannot answer that question right now.';
        $json = json_decode($response, true);

        if ($curlError) {
            $reply = 'Error connecting to API: ' . $curlError;
        } elseif ($httpCode !== 200) {
            $errorMsg = 'API request failed';
            if (isset($json['error']['message'])) {
                $errorMsg = $json['error']['message'];
            } elseif (isset($json['error'])) {
                $errorMsg = is_string($json['error']) ? $json['error'] : json_encode($json['error']);
            }
            $reply = 'API Error (HTTP ' . $httpCode . '): ' . $errorMsg;
        } elseif (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            $reply = trim($json['candidates'][0]['content']['parts'][0]['text']);
        } elseif (isset($json['error'])) {
            $errorMsg = isset($json['error']['message']) ? $json['error']['message'] : json_encode($json['error']);
            $reply = 'API Error: ' . $errorMsg;
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