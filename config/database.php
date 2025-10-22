<?php

// Database connection using PDO
$host = "localhost";
$dbname = "foodbot_db";
$username = "root"; // default user in XAMPP
$password = "";     // usually empty for local projects

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: uncomment this line to confirm it works
    // echo "Database connected successfully!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
