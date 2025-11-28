<?php

// Database connection using PDO
$db_host = "localhost";
$db_name = "foodbot_db";
$db_username = "root"; // Default user in XAMPP
$db_password = "";     // Empty for local project

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: uncomment this line to confirm it works
    // echo "Database connected successfully!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
