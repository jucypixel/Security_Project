<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Casablanca</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <!-- ✅ NAVBAR -->
    <nav>
        <div class="logo">Casablanca</div>

        <div class="nav-links">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="welcome">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                <a href="home.php" class="active">Home</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="personal.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- ✅ MAIN CONTENT -->
    <main class="main-content">
        <div class="card">
            <div class="overlay"></div>
            <div class="nav">
                <div class="logo"></div>
                <ul>
                    <li>Training</li>
                    <li>Programs</li>
                    <li>Schedule</li>
                    <li>Community</li>
                    <li>About</li>
                    <li>Contact</li>
                </ul>
            </div>

            <div class="content">
                <h1>JIU-JITSU</h1>
                <div class="product-row">
                    <div class="product-info">
                        <h2>For Everyone</h2>
                        <h3>Train. Learn. Grow.</h3>
                        <button class="buy-btn">BOOK YOUR CLASS</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ✅ FOOTER -->
    <footer>
        <h2>&copy; 2025 Casablanca. All rights reserved.</h2>
    </footer>
</body>
</html>
