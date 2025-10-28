<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("connection.php");

 if (!isset($_SESSION['reset_user_id'])) {
     echo "<script>
         alert('No verification found. Please go through Forgot Password first.');
         window.location='forgot.php';
     </script>";
     exit;
 }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $id_number = $_SESSION['reset_user_id'];

        $update = $conn->prepare("UPDATE users SET password=? WHERE id_number=?");
        $update->bind_param("ss", $hashed, $id_number);

        if ($update->execute()) {
            echo "<script>
                alert('Password successfully changed!');
                window.location='login.php';
            </script>";
        } else {
            echo "<script>alert('Database update failed.');</script>";
        }
    } else {
        echo "<script>alert('Passwords do not match.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | KickZone</title>
    <link rel="stylesheet" href="reset.css">
</head>
<body>
<div class="main-content">
    <div class="container">
        <h1 class="title">Reset Password</h1>
        <p class="subtitle">Create a new password for your account.</p>

        <form method="POST">
            <div class="input-group">
                <label for="new_password">New Password <span style="color:red">*</span></label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                <div class="strength" id="strength"></div>
            </div>

            <div class="input-group">
                <label for="confirm_password">Confirm Password <span style="color:red">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
            </div>

            <button type="submit" class="btn">Change Password</button>
        </form>
    </div>
</div>

<script>
const passwordInput = document.getElementById("new_password");
const strengthText = document.getElementById("strength");

passwordInput.addEventListener("input", () => {
    const val = passwordInput.value;
    let strength = "Weak";
    let color = "red";

    if (val.length >= 8 && /[A-Z]/.test(val) && /[0-9]/.test(val) && /[!@#$%^&*]/.test(val)) {
        strength = "Strong";
        color = "limegreen";
    } else if (val.length >= 6) {
        strength = "Medium";
        color = "orange";
    }

    strengthText.textContent = strength;
    strengthText.style.color = color;
});
</script>
</body>
</html>
