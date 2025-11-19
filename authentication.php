<?php
session_start();
include("connection.php");

// Skip authentication check for login page to prevent redirect loops
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page === 'login.php' || $current_page === 'personal.php') {
    return; // Exit the authentication check for these pages
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication | Casablanca</title>
    <link rel="stylesheet" href="css/authentication.css">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">Casablanca</div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="login.php" class="active">Login</a></li>
                <li><a href="personal.php">Sign Up</a></li>
            </ul>
        </nav>
    </header>   

    <!-- Background -->
    <div class="background">
        <div class="overlay"></div>

        <div class="auth-container">
            <h1>AUTHENTICATION</h1>
            <p>Answer the questions below for verification</p>

            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?= $error_message ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <?php
                $count = 1;
                foreach ($questions as $q):
                    if (!empty($q)):
                ?>
                <div class="input-group">
                    <label><?= $count . ". " . htmlspecialchars($q) ?></label>
                    <input type="text" name="answer<?= $count ?>" placeholder="Enter your answer" required>
                </div>
                <?php
                    $count++;
                    endif;
                endforeach;
                ?>

                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </div>

    <footer>
        <p>© 2025 Casablanca. All rights reserved.</p>
    </footer>
</body>
</html>


