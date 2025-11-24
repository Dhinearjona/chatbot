CREATE DATABASE IF NOT EXISTS k12_chatbot;
USE k12_chatbot;

CREATE TABLE IF NOT EXISTS chat_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('user', 'assistant') NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);