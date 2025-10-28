<?php
session_start();
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = trim($_POST['id_number']);

    // Prevent errors if connection or query fails
    if ($conn) {
        $query = "SELECT id_number FROM users WHERE id_number = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $id_number);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $_SESSION['verify_id_number'] = $id_number;
                echo "<script>
                    alert('✅ Account found! Redirecting to security questions...');
                    window.location.href = 'security_question.php';
                </script>";
                exit;
            } else {
                echo "<script>alert('❌ No account found with that ID number.');</script>";
            }
        } else {
            echo "<script>alert('⚠️ Database query failed. Check your table or query.');</script>";
        }
    } else {
        echo "<script>alert('⚠️ Database connection failed.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | KickZone</title>
    <link rel="stylesheet" href="forgot.css">
</head>
<body>
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
</body>
</html>
