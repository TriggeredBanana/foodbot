<?php
// This file receives AJAX requests from the chat UI (JavaScript).
// It sends the user's message into the ChatBot and returns a
// formatted reply as JSON. This connects the front-end chat
// interface with the back-end recipe/formatter system.

ini_set('display_errors', 0);               // Avoid warnings breaking JSON output
header('Content-Type: application/json');   // Tell the browser we return JSON

require_once __DIR__ . '/autoloader.php';   // Loads all classes
require_once __DIR__ . '/../config/api_keys.php';  // Loads API keys

// Read JSON sent from fetch() in script.js
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

// Validate input
if (!isset($data['message']) || trim($data['message']) === '') {
    echo json_encode([
        "error" => "No message received."
    ]);
    exit;
}

$userMessage = trim($data['message']);

// Create chatbot system (RecipeAPI + ResponseFormatter + ChatBot)
$recipeApi   = new RecipeAPI(SPOONACULAR_API);
$formatter   = new ResponseFormatter();
$ai          = new AIProcessor(GEMINI_API);
$chatbot     = new ChatBot($recipeApi, $formatter, $ai);

try {
    // Process user message
    $reply = $chatbot->handleMessage($userMessage);

    // Return JSON response to JavaScript
    echo json_encode([
        "reply" => $reply
    ]);
}
catch (RuntimeException $e) {
    // Any API-level or logical errors
    echo json_encode([
        "error" => "ChatBot error: " . $e->getMessage()
    ]);
}
catch (Throwable $t) {
    // Backup error in case something unexpected happens
    echo json_encode([
        "error" => "Unexpected error occurred."
    ]);
}