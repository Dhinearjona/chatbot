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

    define('PROXY_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent');
    define('MODEL_NAME', 'gemini-2.0-flash');
    define('API_KEY', 'AIzaSyAPIzaHVW7SymO5OxM2X82ouT75BE6bCrk');
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