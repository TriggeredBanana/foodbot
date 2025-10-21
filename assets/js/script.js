// DOM elements
const chatMessages = document.getElementById('chatMessages');
const userInput = document.getElementById('userInput');
const sendButton = document.getElementById('sendButton');

// Simple responses for demonstration (will be replaced with API calls later)
const simpleResponses = {
    'chicken': '🍗 Great choice! Here are some ideas with chicken:\n• Chicken stir-fry with vegetables\n• Grilled chicken breast\n• Chicken soup\n• Chicken curry',
    'rice': '🍚 Rice is versatile! You could make:\n• Fried rice\n• Rice bowls\n• Risotto\n• Rice pudding',
    'vegetarian': '🥗 Here are some vegetarian options:\n• Vegetable stir-fry\n• Pasta with marinara\n• Bean salad\n• Veggie burgers',
    'quick': '⚡ Quick meal ideas:\n• Sandwiches\n• Instant noodles with vegetables\n• Scrambled eggs\n• Smoothie bowls',
    'breakfast': '🥞 Breakfast suggestions:\n• Pancakes\n• Oatmeal with fruits\n• Scrambled eggs and toast\n• Yogurt parfait'
};

// Add message to chat
function addMessage(content, isUser = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.textContent = content;
    
    messageDiv.appendChild(contentDiv);
    chatMessages.appendChild(messageDiv);
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Get bot response (simple keyword matching for now)
function getBotResponse(userMessage) {
    const lowercaseMessage = userMessage.toLowerCase();
    
    // Check for keywords in the message
    for (const [keyword, response] of Object.entries(simpleResponses)) {
        if (lowercaseMessage.includes(keyword)) {
            return response;
        }
    }
    
    // Default response if no keywords match
    return "🤔 I'd love to help you cook! Try telling me about specific ingredients you have (like chicken, rice, vegetables) or what type of meal you're looking for (vegetarian, quick meals, breakfast, etc.).";
}

// Send message function
function sendMessage() {
    const message = userInput.value.trim();
    if (message === '') return;

    // Add user message
    addMessage(message, true);

    // Clear input
    userInput.value = '';

    // Send message to backend (PHP)
    fetch("includes/chatHandler.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: message }),
    })
    .then(response => response.json())
    .then(data => {
        // Add bot reply from PHP (server)
        addMessage(data.reply, false);
    })
    .catch(error => {
        console.error("Error:", error);
        addMessage("⚠️ Something went wrong with the connection.", false);
    });

    // (Optional) Keep your local simulation for fallback or testing
    /*
    setTimeout(() => {
        const botResponse = getBotResponse(message);
        addMessage(botResponse, false);
    }, 500);
    */
}

// Event listeners
sendButton.addEventListener('click', sendMessage);

userInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

// Suggestion button function
function askQuestion(question) {
    userInput.value = question;
    sendMessage();
}

// Welcome message on load
document.addEventListener('DOMContentLoaded', function() {
    // Focus on input field
    userInput.focus();
});