<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 0);

    ob_start();

    require_once 'Db.php';
    require_once 'ChatHistory.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET');
    header('Access-Control-Allow-Headers: Content-Type');

    $response = [
        'status' => 'error',
        'message' => 'No action specified',
        'data' => null,
    ];

    define('PROXY_URL', 'https://openrouter.ai/api/v1/chat/completions');
    define('MODEL_NAME', 'openai/gpt-4o-mini');
    define('API_KEY', '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');
    $input = json_decode(file_get_contents("php://input"), true);


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($input['getChatHistory'])) {
            $chatHistory = new ChatHistory();
            $response = $chatHistory->getChatHistory();
        }

        if (isset($input['addChatHistory'])) {
            $chatHistory = new ChatHistory();
            $response = $chatHistory->addChatHistory($input);
        }

        if (isset($input['sendMessage'])) {
            $chatHistory = new ChatHistory();
            $response = $chatHistory->sendMessage($input);
        }
    }

    ob_clean();
    echo json_encode($response);
    exit();
?>