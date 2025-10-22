<?php
require_once __DIR__ . '/functions.php';

// Get the input from JavaScript (sent as JSON)
$data = json_decode(file_get_contents("php://input"), true);
$userInput = trim($data['message'] ?? '');

// Simple example response (replace later with AI logic)
$botReply = "You said: " . $userInput;

// Save to database
if (!empty($userInput)) {
    saveMessage($userInput, $botReply);
}

// Return the reply as JSON
header('Content-Type: application/json');
echo json_encode(['reply' => $botReply]);
?>
