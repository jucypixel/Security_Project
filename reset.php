<?php
session_start();
include("connection.php");

// Prevent direct access without security verification
if (!isset($_SESSION['reset_user_id'])) {
    echo "<script>
            alert('Unauthorized access! Please answer security questions first.');
            window.location = 'forgot.php';
          </script>";
    exit;
}

$user_id = $_SESSION['reset_user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT id_number, username FROM users_account WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>
            alert('User not found!');
            window.location = 'forgot.php';
          </script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (empty($new_password) || empty($confirm_password)) {
        echo "<script>alert('Please fill in all fields.');</script>";
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE users_account SET password = ? WHERE user_id = ?");
    $update->bind_param("si", $hashed_password, $user_id);

    if ($update->execute()) {

        unset($_SESSION['reset_user_id']);

        echo "<script>
                alert('Password reset successful!');
                window.location = 'login.php';
              </script>";
        exit;

    } else {
        echo "<script>alert('Error updating password. Please try again.');</script>";
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Casablanca</title>
    <link rel="stylesheet" href="css/reset.css">
    <script src="javascript/reset.js" defer></script>
</head>
<body>

    <!-- NAVBAR -->
    <nav>
        <div class="logo">CASABLANCA</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="login.php" class="active">Login</a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container">

            <h1 class="title">Reset Password</h1>
            <p class="subtitle">Your account details are shown below. Set your new password.</p>

            <form method="POST">

                <!-- DISPLAY ID NUMBER (READ ONLY) -->
                <div class="input-group">
                    <label>ID Number</label>
                    <input type="text" value="<?php echo $user['id_number']; ?>" readonly>
                </div>

                <!-- DISPLAY USERNAME (READ ONLY) -->
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" value="<?php echo $user['username']; ?>" readonly>
                </div>

                <!-- NEW PASSWORD -->
                <div class="input-group">
                    <label for="new_password">New Password <span style="color:red">*</span></label>
                    <input type="password" id="new_password" name="new_password"
                           placeholder="Enter new password" required>
                    <div class="strength" id="strength"></div>
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="input-group">
                    <label for="confirm_password">Confirm Password <span style="color:red">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           placeholder="Re-enter password" required>
                </div>

                <button type="submit" class="btn">Change Password</button>
            </form>

        </div>
    </div>

    <!-- FOOTER -->
    <footer>&copy; 2025 Casablanca. All rights reserved.</footer>

</body>
</html>
