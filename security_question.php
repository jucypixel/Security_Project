<?php
session_start();
include("connection.php");

// -------------------------------------------
// GET ID NUMBER FROM ACCOUNT STEP
// -------------------------------------------
$id_number = $_SESSION['generated_id_number'] ?? null;

if (!$id_number) {
    die("ERROR: Missing ID number. Please complete Account Information first.");
}

// -------------------------------------------
// FIXED QUESTION LIST
// -------------------------------------------
$questions = [
    "What is your mother's maiden name?",
    "What was the name of your first pet?",
    "What city were you born in?"
];

// -------------------------------------------
// FORM SUBMISSION
// -------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $answer1 = trim($_POST['answer1']);
    $answer2 = trim($_POST['answer2']);
    $answer3 = trim($_POST['answer3']);

    if (empty($answer1) || empty($answer2) || empty($answer3)) {
        echo "<script>alert('Please answer all questions.');</script>";
        exit;
    }

    // -------------------------------------------
    // INSERT SECURITY QUESTIONS (USING id_number FK)
    // -------------------------------------------
    $sql = "INSERT INTO users_security 
            (id_number, question1, answer1, question2, answer2, question3, answer3)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssss",
        $id_number,
        $questions[0], $answer1,
        $questions[1], $answer2,
        $questions[2], $answer3
    );

    if ($stmt->execute()) {

        // CLEAN UP SESSION
        unset($_SESSION['generated_id_number']);

        echo "<script>
                alert('Account Created Successfully!');
                window.location='login.php';
              </script>";
        exit;

    } else {
        echo "Database Error: " . $stmt->error;
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Questions | Registration</title>
    <link rel="stylesheet" href="css/sec_ques.css">
    
</head>
<body>
    <nav>
        <div class="logo">Casablanca</div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="login.php">Login</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <h1 class="form-title">SIGN UP</h1>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Personal Details</div>
                </div>
                <div class="step completed">
                    <div class="step-number">2</div>
                    <div class="step-label">Address & Login</div>
                </div>
                <div class="step active">
                    <div class="step-number">3</div>
                    <div class="step-label">Questions</div>
                </div>
            </div>

            <form class="form1" id="securityQuestionsForm" method="POST" action="">
                <div class="form-section">
                    <h2>Security Questions</h2>

                    <div class="single-column-form">
                        <?php
                        $count = 1;
                        foreach ($questions as $q):
                            if (!empty($q)):
                        ?>
                        <div class="form-group">
                            <label for="answer<?= $count ?>"><?= $count . ". " . htmlspecialchars($q) ?> <span class="required">*</span></label>
                            <input type="text" id="answer<?= $count ?>" name="answer<?= $count ?>" placeholder="Enter your answer" required>
                            <span class="error-message" id="answer<?= $count ?>-error"></span>
                        </div>
                        <?php
                            $count++;
                            endif;
                        endforeach;
                        ?>
                    </div>

                    <div class="button-container">
                        <button type="button" onclick="window.location.href='account.php'" class="back-btn">BACK</button>
                        <button type="submit" name="submit" class="next-btn">SUBMIT</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <h2>&copy; 2025 Casablanca. All rights reserved.</h2>
    </footer>
</body>
</html>

