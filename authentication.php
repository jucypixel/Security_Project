<?php
include("connection.php");
session_start();

// Check if user just registered
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('No active registration found. Please register first.');
        window.location='registration.php';
    </script>";
    exit;
}

$user_id = $_SESSION['user_id'];

//  Fetch the authentication questions (should be fixed to the correct table)
$query = "SELECT question1, question2, question3 FROM authentication LIMIT 1";
$result = mysqli_query($conn, $query);

$questions = [];
if ($row = mysqli_fetch_assoc($result)) {
    $questions = [$row['question1'], $row['question2'], $row['question3']];
}

//  Default fallback questions
if (empty($questions) || empty($questions[0])) {
    $questions = [
        "What is your favorite color?",
        "What is your pet’s name?",
        "Where were you born?"
    ];
}

//  When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $answer1 = trim($_POST['answer1']);
    $answer2 = trim($_POST['answer2']);
    $answer3 = trim($_POST['answer3']);

    // Check if user already has authentication set up
  $check = $conn->prepare("SELECT * FROM user_authentication WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $result_check = $check->get_result();

    if ($result_check->num_rows > 0) {
        echo "<script>
            alert('You already set up authentication answers. Please log in instead.');
            window.location='login.php';
        </script>";
        exit;
    }

    // Save authentication answers
    $insert = $conn->prepare("INSERT INTO user_authentication (user_id, answer1, answer2, answer3) VALUES (?, ?, ?, ?)");
    $insert->bind_param("isss", $user_id, $answer1, $answer2, $answer3);

    if ($insert->execute()) {
        unset($_SESSION['user_id']); // clear the session after setup
        echo "<script>
            alert('Authentication setup complete! You can now log in.');
            window.location='login.php';
        </script>";
        exit;
    } else {
        echo "<script>alert('Error saving authentication data: " . addslashes($conn->error) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication | KickZone</title>
    <link rel="stylesheet" href="authentication.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

    <!--  NAVBAR -->
    <nav class="navbar">
        <div class="logo">KickZone</div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="forgot-container">
            <h2 class="form-title">AUTHENTICATION</h2>
            <p class="form-subtitle">Answer the questions below for verification</p>
            <hr>

            <form method="POST" action="" class="auth-form">
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

                <button type="submit" class="btn">Submit Answers</button>
            </form>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        <h2>&copy; 2025 KickZone. All rights reserved.</h2>
    </footer>

</body>
</html>