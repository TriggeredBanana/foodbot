<?php
    // Starter session som lagrer data i "$_SESSION"
    session_start();

    // Displays errors or info if any & removes them afterwards
    function print_message_helper($key, $class) {
        if (!empty($_SESSION[$key])) {
            echo '<div class="' . $class . '">' . htmlspecialchars($_SESSION[$key]) . '</div>';
            unset($_SESSION[$key]);
        }
    }
    
    // Connect to database
    $db_host = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "foodbot_db";

    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Register user
    if (isset($_POST['register'])) {
        $new_username = $_POST['new_username'];
        $new_password = $_POST['new_password'];

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Check if username already exists & add to database
        $get_username = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $get_username->bind_param("s", $new_username);
        $get_username->execute();
        $get_username->store_result();

        if ($get_username->num_rows > 0) {
            $_SESSION['username_taken'] = "Username is taken.";
        }
        else {
            $create_user = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $create_user->bind_param("ss", $new_username, $hashed_password);
            if ($create_user->execute()) {
                $_SESSION['user_registered'] = "User registered!";
            }
            else {
                $_SESSION['error_creating_user'] ="Something went wrong.";
            }
            $create_user->close();
        }
        $get_username->close();
    }

    // User login
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check failed login last hour
        $check_attempts = $conn->prepare("SELECT COUNT(*) FROM login_attempts WHERE username = ? AND attempt_time > (NOW() - INTERVAL 1 HOUR)");
        $check_attempts->bind_param("s", $username);
        $check_attempts->execute();
        $check_attempts->bind_result($attempts);
        $check_attempts->fetch();
        $check_attempts->close();

        if ($attempts >= 3) {
            $_SESSION['failed_attempts'] = "Too many failed attempts. Try again in an hour.";
        }
        else {
            // Get user data from database & verify against login information
            $get_user = $conn->prepare("SELECT id, username, password, usertype FROM users WHERE username = ?");
            $get_user->bind_param("s", $username);
            $get_user->execute();
            $get_user->store_result();

            if ($get_user->num_rows() === 1) {
                $get_user->bind_result($id, $db_username, $db_password, $usertype);
                $get_user->fetch();

                if (password_verify($password, $db_password)) {
                    session_regenerate_id(true); // Regenerate session ID (for security against hijackers)
                    $_SESSION['username'] = $db_username;
                    $_SESSION['usertype'] = $usertype;
                    $_SESSION['user_id'] = $id;

                    header("Location: index.php");

                    // Commented out -> To be used for testing
                    // $_SESSION['login_message'] = "Logged in as $db_username ($usertype)";
                }
                else {
                    // Register failed attempts
                    $insert_attempt = $conn->prepare("INSERT INTO login_attempts (username) VALUES (?)");
                    $insert_attempt->bind_param("s", $username);
                    $insert_attempt->execute();
                    $insert_attempt->close();

                    $_SESSION['invalid_login'] = "Username or password is incorrect.";
                }
            }
            else {
                $_SESSION['invalid_login'] = "Username or password is incorrect.";
            }
            $get_user->close();
        }
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <center>
        <nav class="navbar">
            <div class="navbar-left">
            <a href="index.php">FoodBot</a>
        </div>
        </nav>
    </center>
    <br><br><br><br>
    <center>
    <form action="#" method="POST">
        <h2>Register</h2>
        <div class="register">
            <label>Username</label>
            <input type="text" name="new_username" required>
        </div>

        <div class="register">
            <label>Password</label>
            <input type="password" name="new_password" required>
        </div>

        <div>
            <input type="submit" name="register" value="Register">
        </div>
    </form>
    </center>
    <br><br><br><br><br>
    <center>
    <form action="#" method="POST">
        <h2>Login</h2>
        <div class="login">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="login">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div>
            <input type="submit" name="login" value="Login">
        </div>
    </form>
    </center>

    <!-- Print out errors or informational messages -->
    <?php print_message_helper('error', 'error-message') ?>
    <?php print_message_helper('logout', 'info-message') ?>
    <?php print_message_helper('username_taken', 'error-message') ?>
    <?php print_message_helper('user_registered', 'info-message') ?>
    <?php print_message_helper('error_creating_user', 'error-message') ?>
    <?php print_message_helper('failed_attempts', 'error-message') ?>
    <?php print_message_helper('login_message', 'info-message') ?>
    <?php print_message_helper('invalid_login', 'error-message') ?>

</body>
</html>