<?php
// Include database connection & constants
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';


// Validates ingredient list & checks if list is within limits
function validate_ingredients($ingredients_string) {
    $ingredients = explode(',', $ingredients_string); // Break string into array by ","
    $ingredients = array_map('trim', $ingredients); // Uses "trim" on all array elements (get rid of spaces)
    $ingredients = array_filter($ingredients); // Removes potential empty values in array

    // Check if ingredient limit is hit
    if (count($ingredients) > MAX_INGREDIENTS) {

        // Displays an error message if over limit
        return [
            'valid' => false,
            'error' => "Too many ingredients. Maximum at once is: " . MAX_INGREDIENTS,
            'ingredients' => []
        ];
    }

    // Otherwise returns valid
    return [
        'valid' => true,
        'error' => null,
        'ingredients' => $ingredients
    ];
}

// Save a chat message to the database
function saveMessage($userId, $userInput, $botReply) {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO messages (user_id, user_input, bot_reply) VALUES (:user_id, :user_input, :bot_reply)");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':user_input', $userInput);
        $stmt->bindParam(':bot_reply', $botReply);
        $stmt->execute();
    }
    catch (PDOException $e) {
        error_log("Database error when saving messages: " . $e->getMessage());
    }
    
}

// Get the latest chat messages based on user id
function getMessages($userId, $limit = CHAT_HISTORY_LIMIT) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT * FROM messages WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        error_log("Database error when getting messages: " . $e->getMessage());
        return []; // Return empty array so frontend doesn't crash
    }
    
}

    // Displays errors or info if any & removes them afterwards
    function print_message_helper($key, $class) {
        if (!empty($_SESSION[$key])) {
            echo '<div class="' . $class . '">' . htmlspecialchars($_SESSION[$key]) . '</div>';
            unset($_SESSION[$key]);
        }
    }

?>