<?php
session_start();
include("connection.php");

$allowed = ['login.php', 'forgot.php', 'personal.php', 'home.php'];

if (!in_array(basename($_SERVER['PHP_SELF']), $allowed)) {
    http_response_code(404);
    die("Page not Found");
}


// Clear old session if logged in
if (isset($_SESSION['username'])) {
    session_unset();
    session_destroy();
    session_start();
}

// Initialize attempts and lockout
if (!isset($_SESSION['attempts'])) $_SESSION['attempts'] = 0;
if (!isset($_SESSION['lockout_time'])) $_SESSION['lockout_time'] = 0;

$error = "";
$lockout_durations = [15, 30, 60]; // seconds

// Check if still locked
if (time() < $_SESSION['lockout_time']) {
    $remaining = $_SESSION['lockout_time'] - time();
    $error = " Access denied for $remaining seconds. Please wait.";
} elseif (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!preg_match("/^[A-Za-z0-9_]+$/", $username)) {
        $error = " Username can only contain letters, numbers, or underscores.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users_account WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['attempts'] = 0;
                $_SESSION['lockout_time'] = 0;
                header("Location: home.php");
                exit();
            } else {
                $error = " Invalid username or password.";
            }
        } else {
            $error = " Invalid username or password.";
        }

        $stmt->close();
        $conn->close();
    }

    // Lockout handling
    if (!empty($error)) {
        $_SESSION['attempts']++;
        if ($_SESSION['attempts'] % 3 == 0) {
            $index = min(intval($_SESSION['attempts'] / 3) - 1, 2);
            $_SESSION['lockout_time'] = time() + $lockout_durations[$index];
            $error = " Too many failed attempts. Try again after " . $lockout_durations[$index] . " seconds.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Casablanca</title>
<link rel="stylesheet" href="css/login.css">
<script src="/javascript/login.js" defer></script>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<script>
    // Pass lockout seconds to JS
    const lockoutSeconds = <?= (time() < $_SESSION['lockout_time']) ? $_SESSION['lockout_time'] - time() : 0 ?>;
</script>

</head>

<body>
    <nav>
        <div class="logo">Casablanca</div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="personal.php" class="signup">Sign Up</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <h1 class="title">Login</h1>

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
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="bx bx-hide toggle-password" id="togglePassword"></i>
                </div>


                <button type="submit" name="login" class="btn">Login</button>

                <div class="links">
                    <?php if ($_SESSION['attempts'] >= 2): ?>
                        <a href="forgot.php" id="forgotLink">Forgot Password?</a>
                    <?php endif; ?>
                    <a href="personal.php" id="signUpLink">Sign Up</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <h2>&copy; 2025 Casablanca. All rights reserved.</h2>
    </footer>
</body>
</html>
