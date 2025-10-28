<?php
session_start();
include("connection.php");

// ✅ Step 1: Verify session
if (!isset($_SESSION['verify_id_number'])) {
    echo "<script>
        alert('No verification found. Please go through Forgot Password first.');
        window.location='forgot.php';
    </script>";
    exit;
}

$id_number = $_SESSION['verify_id_number'];

// ✅ Step 2: Get user_id using id_number
$getUserIdQuery = "SELECT user_id FROM users WHERE id_number = ?";
$stmt = $conn->prepare($getUserIdQuery);
$stmt->bind_param("s", $id_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('User not found.'); window.location='forgot.php';</script>";
    exit;
}

$user = $result->fetch_assoc();
$user_id = $user['user_id'];

// ✅ Step 3: Fetch the security questions linked to this user
// (Make sure your 'authentication' table uses user_id, not id_number)
$qQuery = "SELECT question1, question2, question3 FROM authentication WHERE user_id = ?";
$qStmt = $conn->prepare($qQuery);
$qStmt->bind_param("i", $user_id);
$qStmt->execute();
$qResult = $qStmt->get_result();

if ($qResult->num_rows === 1) {
    $questions = $qResult->fetch_assoc();
} else {
    echo "<script>
        alert('No security questions found for this account.');
        window.location='forgot.php';
    </script>";
    exit;
}

// ✅ Step 4: Verify the user's answers
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $answer1 = strtolower(trim($_POST['answer1']));
    $answer2 = strtolower(trim($_POST['answer2']));
    $answer3 = strtolower(trim($_POST['answer3']));

    // Fetch the stored answers
    $ansQuery = "SELECT answer1, answer2, answer3 FROM user_authentication WHERE user_id = ?";
    $ansStmt = $conn->prepare($ansQuery);
    $ansStmt->bind_param("i", $user_id);
    $ansStmt->execute();
    $ansResult = $ansStmt->get_result();

    if ($ansResult->num_rows === 1) {
        $correct = $ansResult->fetch_assoc();

        if (
            $answer1 === strtolower($correct['answer1']) &&
            $answer2 === strtolower($correct['answer2']) &&
            $answer3 === strtolower($correct['answer3'])
        ) {
            // ✅ All correct — allow reset
            $_SESSION['reset_user_id'] = $user_id;
            echo "<script>
                alert('Verification successful! You may now reset your password.');
                window.location='reset.php';
            </script>";
            exit;
        } else {
            echo "<script>alert('One or more answers are incorrect. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Answer records not found for this user. Please set up your answers first.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Security Verification | KickZone</title>
    <link rel="stylesheet" href="forgot.css">
</head>
<body>
<div class="container">
    <h1>Security Verification</h1>
    <p>Answer the following security questions to verify your account.</p>

    <form method="POST">
        <div class="input-group">
            <label><?= htmlspecialchars($questions['question1']); ?></label>
            <input type="text" name="answer1" required>
        </div>
        <div class="input-group">
            <label><?= htmlspecialchars($questions['question2']); ?></label>
            <input type="text" name="answer2" required>
        </div>
        <div class="input-group">
            <label><?= htmlspecialchars($questions['question3']); ?></label>
            <input type="text" name="answer3" required>
        </div>

        <button type="submit" class="btn">Submit Answers</button>
    </form>
</div>
</body>
</html>
