<?php
    //header.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodBot</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <center>
        <nav class="navbar">
            <div class="navbar-left">
                <a href="index.php">FoodBot</a>
            </div>
            <div class="navbar-right">
                <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'admin'): ?>
                <a href="admin_users.php" style="margin-right: 30px;">Admin</a>
                <?php endif; ?>

                <a href="logout.php">Logout</a>
            </div>

        </nav>
    </center>
    <div class="container">
        <header>
            <h1>FoodBot</h1>
            <p>What’s cooking? Whatever FoodBot says of course!</p>
        </header>