<?php

session_start();
include("connection.php");

//  LOGOUT logic: if user is already logged in, log them out first
if (isset($_SESSION['username'])) {
    session_unset();   // remove session variables
    session_destroy(); // end session
    session_start();   // start fresh session after logout
}

// initialize counters if not set
if (!isset($_SESSION['attempts'])) $_SESSION['attempts'] = 0;
if (!isset($_SESSION['lockout_time'])) $_SESSION['lockout_time'] = 0;

$error = "";
$lockout_durations = [15, 30, 60]; // lock times in seconds

// Check if still locked out
if (time() < $_SESSION['lockout_time']) {
    $remaining = $_SESSION['lockout_time'] - time();
    $error = "Access denied for $remaining seconds. Please wait.";
} elseif (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input (letters/numbers only for username)
    if (!preg_match("/^[A-Za-z0-9_]+$/", $username)) {
        $error = "Username can only contain letters and numbers.";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM accounts WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['attempts'] = 0; // reset on success
                header("Location: home.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }

        $stmt->close();
        $conn->close();
    }

    // Handle failed login attempts
    if (!empty($error)) {
        $_SESSION['attempts']++;

        if ($_SESSION['attempts'] % 3 == 0) {
            // lockout stages: 15, 30, 60 seconds
            $index = min(intval($_SESSION['attempts'] / 3) - 1, 2);
            $_SESSION['lockout_time'] = time() + $lockout_durations[$index];
            $error = "Too many failed attempts. Try again after " . $lockout_durations[$index] . " seconds.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | KickZone</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script>
        // Disable back button
        function disableBack() {
            window.history.forward();
            window.onunload = () => null;
        }

        document.addEventListener('DOMContentLoaded', function () {
            disableBack();

            // Handle lockout (disable buttons)
            const lockoutSeconds = <?= (time() < $_SESSION['lockout_time']) ? $_SESSION['lockout_time'] - time() : 0 ?>;
            if (lockoutSeconds > 0) disableButtons(lockoutSeconds);
        });

        function disableButtons(seconds) {
            const loginBtn = document.querySelector('.btn');
            const registerLink = document.querySelector('.links a[href="registration.php"]');
            loginBtn.disabled = true;
            registerLink.style.pointerEvents = 'none';
            registerLink.style.opacity = '0.5';

            const interval = setInterval(() => {
                seconds--;
                loginBtn.textContent = `Please wait ${seconds}s`;
                if (seconds <= 0) {
                    clearInterval(interval);
                    loginBtn.textContent = 'Login';
                    loginBtn.disabled = false;
                    registerLink.style.pointerEvents = 'auto';
                    registerLink.style.opacity = '1';
                }
            }, 1000);
        }
    </script>
</head>
<body>
    <nav>
        <div class="logo">KickZone</div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="login.php" class="active">Login</a>
            <a href="registration.php">Sign Up</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <h1 class="title">Login</h1>

            <!-- Display error message -->
            <?php if (!empty($error)) : ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="input-group">
                    <i class="bx bx-user"></i>
                    <input type="text" name="username" placeholder="Username" required pattern="[A-Za-z0-9_]+">
                </div>

                <div class="input-group">
                    <i class="bx bx-lock-alt"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" name="login" class="btn">Login</button>

                <div class="links">
                    <?php if ($_SESSION['attempts'] >= 2): ?>
                        <a href="forgot.php" onclick="goToForgot()" id="forgotLink">Forgot Password? Reset here</a>
                        <script>
  function goToForgot() {
    window.location.href = "forgot.php";
  }
  </script>
                    <?php else: ?>
                        <a href="forgot.php" style="display:none;" id="forgotLink">Forgot Password? Reset here</a>
                    <?php endif; ?>
                    <a href="registration.php">Sign Up</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <h2>&copy; 2025 KickZone. All rights reserved.</h2>
    </footer>
</body>
</html>