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
        'message' => 'What can I make with chicken and rice?',
        'ingredients' => 'chicken, rice'
    ],
    [
        'icon' => '🥗',
        'label' => 'Vegetarian',
        'message' => 'Could you give me some vegetarian suggestions?',
        'ingredients' => 'tomato, zucchini, bell pepper'
    ],
    [
        'icon' => '⚡',
        'label' => 'Quick Meals',
        'message' => 'What about quick meals?',
        'ingredients' => 'egg, cheese, bread'
    ],
    [
        'icon' => '🥞',
        'label' => 'Breakfast',
        'message' => 'Any breakfast options?',
        'ingredients' => 'eggs, oats, banana'
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

                // Take raw bot reply
                $bot_reply_raw = $chat['bot_reply'];

                // Convert HTML line breaks / paragraphs to real line breaks
                $bot_reply_raw = str_ireplace(
                    ['<br>', '<br/>', '<br />'],
                    "\n",
                    $bot_reply_raw
                );
                $bot_reply_raw = str_ireplace(
                    ['</p>', '</li>'],
                    "\n\n",
                    $bot_reply_raw
                );

                // Remove any remaining HTML tags
                $bot_reply_text = strip_tags($bot_reply_raw);

                // Escape safely and keep line breaks
                $sanitized_reply = nl2br(htmlspecialchars($bot_reply_text, ENT_QUOTES, 'UTF-8'));

                // Display bot reply
                echo '<div class="message bot-message">';
                echo '<div class="message-content">' . $sanitized_reply . '</div>';
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
    <?php foreach ($suggestions as $suggestion): ?>
    <button class="suggestion-btn" onclick="sendPreset(
                '<?php echo htmlspecialchars($suggestion['message'], ENT_QUOTES); ?>',
                '<?php echo htmlspecialchars($suggestion['ingredients'], ENT_QUOTES); ?>'
            )">
        <?php echo $suggestion['icon'] . ' ' . htmlspecialchars($suggestion['label']); ?>
    </button>
    <?php endforeach; ?>
</div>

<script src="assets/js/script.js"></script>

<?php print_message_helper('error', 'error-message') ?>

<?php
    require_once __DIR__ . '/includes/footer.php';
?>