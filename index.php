<?php
// Start session
session_start();

// Include necessary files
require_once __DIR__ . '/includes/autoloader.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/functions.php';

// Check if logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "You must be logged in to access that page.";
    header("Location: auth.php");
    exit;
}

// Retrieves chat history & reverses to display correctly
$history = getMessages($_SESSION['user_id']);
$history = array_reverse($history);

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
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="chat-container">
    <div class="chat-messages" id="chatMessages">
        <div class="message bot-message">
            <div class="message-content">
                <?php echo htmlspecialchars($welcome_message); ?>
            </div>
        </div>
        
        <?php
            // Prints chat history inside the chat container
            foreach ($history as $chat) {
                // Display user message
                echo '<div class="message user-message">';
                echo '<div class="message-content">' . htmlspecialchars($chat['user_input']) . '</div>';
                echo '</div>';

                // Display bot reply
                echo '<div class="message bot-message">';
                echo '<div class="message-content">' . htmlspecialchars($chat['bot_reply']) . '</div>';
                echo '</div>';
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

<script src="assets/js/script.js"></script>

<?php
    require_once __DIR__ . '/includes/footer.php';
?>