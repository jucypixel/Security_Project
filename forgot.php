<?php
session_start();
include("connection.php");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Verify user still exists in database and get current data
$username = $_SESSION['username'];

// Use the correct column names from your users_account table
$stmt = $conn->prepare("SELECT account_id, id_number, username FROM users_account WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    // User no longer exists or session is invalid
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$user_data = $result->fetch_assoc();

// Store user data in session for use in other pages
$_SESSION['account_id'] = $user_data['account_id']; // Primary key
$_SESSION['id_number'] = $user_data['id_number'];   // ID number
$_SESSION['username'] = $user_data['username'];

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Casablanca</title>
    <link rel="stylesheet" href="css/forgot.css">
</head>
<body>
    <nav>
        <div class="logo">Casablanca</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="login.php" class="login">Login</a>
            <a href="personal.php">Sign Up</a>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <h1 class="title">Forgot Password</h1>
            <p class="subtitle">Enter your ID Number to verify your account.</p>

            <form method="POST">
                <div class="input-group">
                    <label for="id_number">ID Number <span style="color:red">*</span></label>
                    <input type="text" id="id_number" name="id_number" placeholder="Enter your registered ID Number" required>
                </div>
                <button type="submit" class="btn">Verify ID</button>
            </form>
        </div>
    </div>

    <footer>
        <h2>2025 Casablanca. All rights reserved.</h2>
    </footer>
</body>
</html>
