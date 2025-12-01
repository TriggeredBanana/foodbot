# FoodBot 🤖🍳

FoodBot is an intelligent cooking assistant that helps you decide what to cook based on the ingredients you have at home. It combines the power of the **Spoonacular API** for recipe data and **Google Gemini AI** for natural language understanding and personalized cooking tips.

## ✨ Features

-   **Ingredient-Based Search:** Simply tell FoodBot what ingredients you have (e.g., "chicken, rice, tomatoes"), and it will find matching recipes.
-   **AI-Powered Suggestions:** If no exact recipes are found, the AI generates creative meal ideas and cooking tips.
-   **Smart Parsing:** The bot intelligently extracts ingredients from your natural language messages.
-   **User Accounts:** Secure registration and login system to keep your chat history private.
-   **Chat History:** Your past conversations and recipe suggestions are saved and retrieved automatically.
-   **Quick Suggestions:** One-click buttons for common requests like "Quick Meals", "Vegetarian", or "Breakfast".

## 🛠️ Tech Stack

-   **Backend:** PHP (Vanilla)
-   **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
-   **Database:** MySQL
-   **APIs:**
    -   [Spoonacular API](https://spoonacular.com/food-api) (Recipe Data)
    -   [Google Gemini API](https://ai.google.dev/) (AI Processing)

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/triggeredbanana/foodbot.git
cd foodbot
```

### 2. Database Setup
1.  Create a MySQL database named `foodbot_db`.
2.  Run the following SQL commands to create the necessary tables:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    usertype VARCHAR(20) DEFAULT 'user'
);

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_input TEXT NOT NULL,
    bot_reply TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 3. Configuration
1.  **Database:** Open `config/database.php` and update your database credentials if they differ from the defaults (root/empty password).
2.  **API Keys:**
    -   Rename `config/api_keys_example.php` to `config/api_keys.php`.
    -   Add your **Spoonacular API Key** and **Google Gemini API Key** in the file.

### 4. Run the Application
-   Host the project using a local server like **XAMPP**, **WAMP**, or the PHP built-in server:
    ```bash
    php -S localhost:8000
    ```
-   Open your browser and navigate to `http://localhost:8000`.

## 📂 Project Structure

```
foodbot/
├── assets/             # CSS and JavaScript files
├── classes/            # Core PHP classes (ChatBot, AIProcessor, RecipeAPI)
├── config/             # Configuration files (Database, API keys)
├── includes/           # Helper functions and UI partials (header, footer)
├── index.php           # Main chat interface
├── auth.php            # Login and Registration page
├── admin_users.php     # Admin user management
└── README.md           # Project documentation
```

## 📝 Usage

1.  **Register** a new account on the login page.
2.  **Log in** to access the chat interface.
3.  **Type** your ingredients (e.g., "I have eggs and milk") or click a **Quick Suggestion** button.
4.  **View** the recipes and AI tips returned by FoodBot!
