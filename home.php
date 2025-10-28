<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | KickZone</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <nav>
    <div class="logo">KickZone</div>

    <div class="nav-links">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="welcome">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
            <a href="home.php">Home</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="registration.php">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

    <main class="main-content">
        <div class="card">
            <div class="nav">
                <div class="logo">Nike</div>
                <ul>
                    <li>Jordan</li>
                    <li>Lifestyle</li>
                    <li>Basketball</li>
                    <li>Running</li>
                    <li>Football</li>
                    <li>Golf</li>
                </ul>
            </div>

            <div class="content">
                <h1>NIKE JORDAN</h1>
                <div class="product-row">
                    <img src="image/shoes.png" alt="Air Jordan" class="shoe">
                    <div class="product-info">
                        <h2>Jordan 4</h2>
                        <h3>Purple Thunder</h3>
                        <button class="buy-btn">BUY NOW</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <h2>&copy; 2025 KickZone. All rights reserved.</h2>
    </footer>
</body>
</html>