<?php
session_start();
include("connection.php");

// -----------------------------------------------
// Generate ID NUMBER
// -----------------------------------------------
$year = date("Y");

$result = $conn->query("SELECT id_number FROM users_personal ORDER BY user_id DESC LIMIT 1");

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Extract the numeric part after YYYY-
    $last = intval(substr($row['id_number'], 5));
    $next = $last + 1;
} else {
    $next = 1;
}

$generatedID = $year . "-" . str_pad($next, 4, "0", STR_PAD_LEFT);
$_SESSION['generated_id_number'] = $generatedID;

// -----------------------------------------------
// FORM SUBMIT
// -----------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fname = trim($_POST['firstname']);
    $mname = trim($_POST['middlename']);
    $lname = trim($_POST['lastname']);
    $suffix = trim($_POST['suffix']);
    $birthdate = trim($_POST['birthdate']);
    $sex = trim($_POST['sex']);
    $extension = trim($_POST['extension']);

    // Validation
    if (empty($fname) || empty($lname) || empty($birthdate) || empty($sex)) {
        echo "<script>alert('Please complete all required fields.');</script>";
        exit;
    }

    // Age
    $age = (new DateTime())->diff(new DateTime($birthdate))->y;

    // Insert into users_personal
    $stmt = $conn->prepare("
        INSERT INTO users_personal
        (id_number, firstname, middlename, lastname, suffix, birthdate, age, sex, extension)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssssssiss", 
        $generatedID, $fname, $mname, $lname, $suffix, 
        $birthdate, $age, $sex, $extension
    );

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        header("Location: account.php");
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
    <title>Registration</title>
    <link rel="stylesheet" href="css/personal.css">
    <script src="javascript/personal.js" defer></script>

    
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
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-label">Personal Details</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-label">Address & Login</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">Questions</div>
                </div>
            </div>

            <form class="form1" id="personalInfoForm" action="personal.php" method="POST">
            <div class="form-section">
                <h2>Personal Details</h2>

                    <!-- two-column grid for the top fields -->
                    <div class="two-column-form">

                        <!-- LEFT COLUMN -->
                        <div class="form-column">

                            <div class="form-group">
                                <label for="id_number">ID No.: <span class="required">*</span></label>
                                <input type="text" id="id_number" name="id_number" value="<?= $generatedID ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="firstname">First Name: <span class="required">*</span></label>
                                <span class="error-message" id="firstname-error"></span>
                                <input type="text" id="firstname" name="firstname" placeholder="Jaceen" required>
                            </div>

                            <div class="form-group">
                                <label for="lastname">Last Name: <span class="required">*</span></label>
                                <span class="error-message" id="lastname-error"></span>
                                <input type="text" id="lastname" name="lastname" placeholder="Jowak" required>
                            </div>

                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="form-column">

                            <div class="form-group">
                                <label for="middlename">Middle Initial: <span class="optional">(Optional)</span></label>
                                <span class="error-message" id="middlename-error"></span>
                                <input type="text" id="middlename" name="middlename" placeholder="C" maxlength="1">
                            </div>

                            <div class="form-group">
                                <label for="extension">Suffix: <span class="optional">(Optional)</span></label>
                                <span class="error-message" id="extension-error"></span>
                                <input type="text" id="extension" name="extension" placeholder="jr., Jr., III, etc.">
                            </div>

                            <div class="form-group">
                                <label for="age">Age: <span class="required">*</span></label>
                                <span class="error-message" id="age-error"></span>
                                <input type="number" name="age" id="age" placeholder="Age" disabled>
                            </div>

                        </div> <!-- end two columns -->
                    </div>

                    <!-- FULL-WIDTH ROW: birthdate + sex aligned exactly like the pairs above -->
                    <div class="form-row-wide">
                        <div class="form-group">
                            <label for="birthdate">Birthdate: <span class="required">*</span></label>
                            <input type="date" id="birthdate" name="birthdate" required>
                        </div>

                        <div class="form-group">
                            <label for="sex">Sex: <span class="required">*</span></label>
                            <select id="sex" name="sex" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="button-container">
                        <button type="submit" name="submit" class="next-btn">NEXT</button>
                    </div>
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

