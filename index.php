<?php
// Start session
session_start();

// Include necessary files
require_once __DIR__ . '/includes/autoloader.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/functions.php';

// Initialize chatbot session if it does not exists
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Define page variables
$page_title = 'FoodBot - Your Cooking Assistant';
$welcome_message = "👋 Hello! I'm FoodBot, your personal cooking assistant. What ingredients do you have in your kitchen today?";

// Quick suggestions array
$suggestions = [
    [
        'icon' => '🍗',
        'label' => 'Chicken & Rice',
        'question' => 'What can I make with chicken and rice?'
    ],
    [
        'icon' => '🥗',
        'label' => 'Vegetarian',
        'question' => 'Vegetarian recipes please'
    ],
    [
        'icon' => '⚡',
        'label' => 'Quick Meals',
        'question' => 'Quick 15-minute meals'
    ],
    [
        'icon' => '🥞',
        'label' => 'Breakfast',
        'question' => 'Healthy breakfast ideas'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>FoodBot</h1>
            <p>Tell me what ingredients you have, and I'll help you cook something delicious!</p>
        </header>

        <!-- Prints out welcome message -->
        <div class="chat-container">
            <div class="chat-messages" id="chatMessages">
                <div class="message bot-message">
                    <div class="message-content">
                        <?php echo htmlspecialchars($welcome_message); ?>
                    </div>
                </div>
                
                <?php
                // Display previous chat history if exists
                // For each message in chat history, it will display them correctly (using correct user or chatbot class)
                if (!empty($_SESSION['chat_history'])) {
                    foreach ($_SESSION['chat_history'] as $chat) {
                        $message_class = $chat['type'] === 'user' ? 'user-message' : 'bot-message';
                        echo '<div class="message ' . $message_class . '">';
                        echo '<div class="message-content">' . htmlspecialchars($chat['message']) . '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            
            <!-- User input -->
            <div class="input-container">
                <input type="text" id="userInput" placeholder="Type your ingredients here... (e.g., chicken, rice, tomatoes)" />
                <button id="sendButton">Send</button>
            </div>
        </div>

        <!-- Displays a pre-defined set of suggestions -->
        <div class="suggestions">
            <h3>Quick suggestions:</h3>
            <div class="suggestion-buttons">
                <!-- For each pre-defined suggestion it creates a button that "onClick" sends a question to the chatbot -->
                <?php foreach ($suggestions as $suggestion): ?>
                    <button class="suggestion-btn" onclick="askQuestion('<?php echo htmlspecialchars($suggestion['question'], ENT_QUOTES); ?>')">
                        <?php echo $suggestion['icon'] . ' ' . htmlspecialchars($suggestion['label']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>