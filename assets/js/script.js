// DOM elements
const chatMessages = document.getElementById('chatMessages');
const userInput = document.getElementById('userInput');
const sendButton = document.getElementById('sendButton');

let typingMessage = null;

// Show "FoodBot is thinking..." bubble
function showTypingIndicator() {
    // If it already exists, do nothing
    if (typingMessage !== null) return;

    typingMessage = document.createElement('div');
    typingMessage.className = 'message bot-message typing-indicator';

    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.textContent = 'FoodBot is thinking…';

    typingMessage.appendChild(contentDiv);
    chatMessages.appendChild(typingMessage);

    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Remove the typing bubble
function hideTypingIndicator() {
    if (typingMessage !== null) {
        chatMessages.removeChild(typingMessage);
        typingMessage = null;
    }
}


// Add message to chat
function addMessage(content, isUser = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    if (isUser) {
    contentDiv.textContent = content;
    } else {
        contentDiv.innerHTML = content;
    }
    
    messageDiv.appendChild(contentDiv);
    chatMessages.appendChild(messageDiv);
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Send message function
function sendMessage() {
    const message = userInput.value.trim();
    if (message === '') return;

    // Add user message
    addMessage(message, true);

    // Clear input
    userInput.value = '';

    // Show typing indicator while waiting for backend
    showTypingIndicator();

    // Send message to backend (PHP)
    fetch("includes/chatHandler.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: message }),
    })
    .then(response => response.json())
    .then(data => {
        if (typingMessage !== null) {
            const contentDiv = typingMessage.querySelector('.message-content');
            
            if (data.error) {
                contentDiv.textContent = "⚠️ " + data.error; // Show an error to the user
            }
            else {
                contentDiv.innerHTML = data.reply; // show recipe
            }
            typingMessage.classList.remove('typing-indicator');
            typingMessage = null;  // no longer a typing bubble
        }
    })
    
    .catch(error => {
        console.error("Error:", error);
        addMessage("⚠️ Something went wrong with the connection.", false);
    });

    // (Optional) Keep local simulation for fallback or testing
    /*
    setTimeout(() => {
        const botResponse = getBotResponse(message);
        addMessage(botResponse, false);
    }, 500);
    */
}

// Send a predefined suggestion:
// displayText = what the user sees in the chat window
// ingredients  = what we actually send to the backend / Spoonacular
function sendPreset(displayText, ingredients) {
    const message = ingredients.trim();
    if (message === '') return;

    // Show the nice label in the user bubble
    addMessage(displayText, true);

    userInput.value = '';
    showTypingIndicator();

    fetch("includes/chatHandler.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            message: message,          // used for Spoonacular
            displayText: displayText   // used for chat history
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (typingMessage !== null) {
            const contentDiv = typingMessage.querySelector('.message-content');
            contentDiv.innerHTML = data.reply;
            typingMessage.classList.remove('typing-indicator');
            typingMessage = null;
        }
    })
    .catch(error => {
        console.error("Error:", error);
        if (typingMessage !== null) {
            const contentDiv = typingMessage.querySelector('.message-content');
            contentDiv.textContent = "⚠️ Something went wrong with the connection.";
            typingMessage.classList.remove('typing-indicator');
            typingMessage = null;
        }
    });
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