<?
include 'connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>404 - Page Not Found</title>
  <link rel="stylesheet" href="css/registration.css">
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f8f8f8;
    }
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      text-align: center;
      padding: 40px;
    }
    h1 {
      font-size: 120px;
      margin: 0;
      color: #e74c3c;
    }
    h2 {
      font-size: 36px;
      color: #333;
      margin-bottom: 10px;
    }
    p {
      color: #777;
      font-size: 18px;
      margin-bottom: 30px;
    }
    a {
      background-color: #333;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      transition: background 0.3s;
    }
    a:hover {
      background-color: #555;
    }
  </style>
</head>
<body>
  <!-- KickZone Nav -->
  <nav>
    <div class="logo">KickZone</div>
    <div class="nav-links">
      <a href="home.php">Home</a>
      <a href="login.php">Login</a>
      <a href="registration.php">Sign Up</a>
    </div>
  </nav>

  <main>
    <h1>404</h1>
    <h2>Page Not Found</h2>
    <p>Sorry, the page you are looking for doesn’t exist or has been moved.</p>
    <a href="home.php">← Go Back to Home</a>
  </main>

  <footer>
    <h2>&copy; 2025 Casablanca. All rights reserved.</h2>
  </footer>
</body>
</html>
