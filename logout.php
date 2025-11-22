<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        $_SESSION['error'] = "You must be logged in to logout.";
    }
    else {
        $_SESSION['logout'] = "You have been logged out.";
        unset($_SESSION['username']);
        unset($_SESSION['usertype']);
        unset($_SESSION['user_id']);
    }

    session_write_close(); // Saves session data and then closes

    header("Location: auth.php");
    exit;
?>