<?php
session_start();
include("connection.php");

// -------------------------------------------
//  ID NUMBER FROM PERSONAL STEP
// -------------------------------------------
$id_number = $_SESSION['generated_id_number'] ?? null;
// (your existing account.php logic here ...)

// Make the logged names available to JS for auto username generation.
// If the session doesn't have them, keep them empty.
$registered_fname = $_SESSION['firstname'] ?? '';
$registered_lname = $_SESSION['lastname']  ?? '';

if (!$id_number) {
    die("Error: Missing ID number. Please complete Personal Info first.");
}

// -------------------------------------------
//  IF FORM SUBMITTED
// -------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Address Fields
    $street    = trim($_POST['street']);
    $barangay  = trim($_POST['barangay']);
    $city      = trim($_POST['city']);
    $province  = trim($_POST['province']);
    $country   = trim($_POST['country']);
    $zipcode   = trim($_POST['zipcode']);

    // Login Fields
    $email     = trim($_POST['email']);
    $username  = trim($_POST['username']);
    $password  = trim($_POST['password']);
    $confirm   = trim($_POST['password_confirm']);

    // -----------------------------
    // VALIDATIONS
    // -----------------------------
    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match!'); history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); history.back();</script>";
        exit;
    }

    // Username Check
    $checkUser = $conn->prepare("SELECT username FROM users_account WHERE username = ? LIMIT 1");
    $checkUser->bind_param("s", $username);
    $checkUser->execute();
    $resultUser = $checkUser->get_result();

    if ($resultUser->num_rows > 0) {
        echo "<script>alert('Username already taken!'); history.back();</script>";
        exit;
    }

    // Email Check
    $checkEmail = $conn->prepare("SELECT email FROM users_account WHERE email = ? LIMIT 1");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $resultEmail = $checkEmail->get_result();

    if ($resultEmail->num_rows > 0) {
        echo "<script>alert('Email already used!'); history.back();</script>";
        exit;
    }

    // Password Hash
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // -----------------------------
    // INSERT INTO DATABASE
    // -----------------------------
    $insert = $conn->prepare("
        INSERT INTO users_account 
        (id_number, street, barangay, city, province, country, zipcode, email, username, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insert->bind_param(
        "ssssssssss",
        $id_number,
        $street,
        $barangay,
        $city,
        $province,
        $country,
        $zipcode,
        $email,
        $username,
        $hashedPassword
    );

    if ($insert->execute()) {
        // Save primary key and names for next steps
        $_SESSION['user_id'] = $conn->insert_id;

        // Use the session variables that already exist
        $_SESSION['firstname'] = $registered_fname;
        $_SESSION['lastname']  = $registered_lname;

        header("Location: security_question.php");
        exit;
    } else {
        echo "Database Error: " . $insert->error;
    }


}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information | Registration</title>
    <link rel="stylesheet" href="css/account.css">
    <script src="javascript/test.js" defer></script>
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
                <div class="step active">
                    <div class="step-number">2</div>
                    <div class="step-label">Address & Login</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">Questions</div>
                </div>
            </div>

            <form class="form1" id="accountInfoForm" action="account.php" method="POST">
                <div class="form-section">
                    <h2>Address & Login Details</h2>

                    <div class="two-column-form">
                        
                        <!-- LEFT COLUMN -->
                        <div class="form-column">

                            <div class="form-group">
                                <label for="street">Purok: <span class="required">*</span></label>
                                <input type="text" id="street" name="street" required>
                            </div>

                            <div class="form-group">
                                <label for="city">City/Municipality: <span class="required">*</span></label>
                                <input type="text" id="city" name="city" required>
                            </div>

                            <div class="form-group">
                                <label for="country">Country: <span class="required">*</span></label>
                                <input type="text" id="country" name="country" required>
                            </div>

                            <div class="form-group">
                                <label>Email: <span class="required">*</span></label>
                                <span class="error-message" id="email-error"></span>
                                <input type="email" id="email" name="email" placeholder="Email" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password: <span class="required">*</span></label>
                                <div class="password-wrapper">
                                    <input type="password" id="password" name="password" required>
                                    <div id="passwordStrength"></div>
                                </div>
                            </div>

                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="form-column">

                            <div class="form-group">
                                <label for="barangay">Barangay: <span class="required">*</span></label>
                                <input type="text" id="barangay" name="barangay" required>
                            </div>

                            <div class="form-group">
                                <label for="province">Province: <span class="required">*</span></label>
                                <input type="text" id="province" name="province" required>
                            </div>

                            <div class="form-group">
                                <label for="zipcode">Zip Code: <span class="required">*</span></label>
                                <input type="text" id="zipcode" name="zipcode" pattern="[0-9]{4}" required>
                            </div>

                            <div class="form-group">
                                <label for="username">Username: <span class="required">*</span></label>
                                <input type="text" id="username" name="username" required>
                            </div>

                            <div class="form-group">
                                <label for="password_confirm">Re-enter Password: <span class="required">*</span></label>
                                <div class="password-wrapper">
                                    <input type="password" id="password_confirm" name="password_confirm" required>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="button-container">
                        <button type="button" onclick="window.location.href='personal.php'" class="back-btn">BACK</button>
                        <button type="submit" name="submit" class="next-btn">NEXT</button>
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
