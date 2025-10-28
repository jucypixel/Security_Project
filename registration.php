<?php
include 'connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $id_number = trim($_POST['id_number']);
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $extension = trim($_POST['extension']);
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $address = trim($_POST['address']);
    $zipcode = trim($_POST['zipcode']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // + Password confirmation
    if ($password !== $confirm_password) {
        echo "<script>
            alert('Passwords do not match!');
            window.history.back();
        </script>";
        exit;
    }

    // Check if ID number already exists
    $check_id = $conn->prepare("SELECT * FROM users WHERE id_number = ?");
    $check_id->bind_param("s", $id_number);
    $check_id->execute();
    $result_id = $check_id->get_result();
    if ($result_id->num_rows > 0) {
        echo "<script>
            alert('⚠️ ID Number already registered!');
            window.history.back();
        </script>";
        exit;
    }
    $check_id->close();

    //  Check if username already exists
    $check_user = $conn->prepare("SELECT * FROM accounts WHERE username = ?");
    $check_user->bind_param("s", $username);
    $check_user->execute();
    $result = $check_user->get_result();
    if ($result->num_rows > 0) {
        echo "<script>
            alert('⚠️ Username already taken!');
            window.history.back();
        </script>";
        exit;
    }
    $check_user->close();

    //  Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //  Insert into users table
    $sql_users = "INSERT INTO users 
        (id_number, firstname, middlename, lastname, extension, birthdate, age, sex, address, zipcode)
        VALUES (?,?,?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql_users);
    $stmt->bind_param("ssssssisss", $id_number, $firstname, $middlename, $lastname, $extension, $birthdate, $age, $sex, $address, $zipcode);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id; 

        //  Insert into accounts table
        $sql_accounts = "INSERT INTO accounts (user_id, username, password) VALUES (?,?,?)";
        $stmt2 = $conn->prepare($sql_accounts);
        $stmt2->bind_param("iss", $user_id, $username, $hashed_password);

        if ($stmt2->execute()) {
            $_SESSION['user_id'] = $user_id;

            echo "<script>
                alert('✅ Registration successful! Please set up your authentication questions next.');
                window.location.href='authentication.php';
            </script>";
            exit;
        } else {
            echo "<script>
                alert('Error in account creation. Please try again.');
                window.history.back();
            </script>";
            exit;
        }
        $stmt2->close();
    } else {
        echo "<script>
            alert('Error in user registration. Please try again.');
            window.history.back();
        </script>";
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="registration.css">
</head>
<body>
    <nav>
        <div class="logo">KickZone</div>
        <div class="nav-links">
          <a href="home.php">Home</a>
          <a href="login.php">Login</a>
          <a href="registration.php">Sign Up</a>
        </div>
    </nav>

   <main class="main-content">
  <div class="container">
    <h1 class="title">Registration</h1>
    <div class="form-container">
      <form class="form1" id="registrationForm" action="registration.php" method="POST">
        <div class="form-section">
          <h2>Personal Information</h2>

          <div class="form-group-half">
            <label><span class="label-text">ID Number</span><span class="required">*</span></label>
            <input type="text" name="id_number" pattern="[0-9\-]+" placeholder="ID NO: xxxx-xxxx" required>

            <label><span class="label-text">First Name</span><span class="required">*</span></label>
            <input type="text" name="firstname" placeholder="First Name" required>
          </div>

          <div class="form-group-half">
            <label><span class="label-text">Middle Name</span><span class="required">*</span></label>
            <input type="text" name="middlename" placeholder="Middle Name" required>

            <label><span class="label-text">Last Name</span><span class="required">*</span></label>
            <input type="text" name="lastname" placeholder="Last Name" required>
          </div>

          <div class="form-group-half">
            <label><span class="label-text">Extension</span></label>
            <input type="text" name="extension" placeholder="Extension Name">

            <label><span class="label-text">Birthdate</span><span class="required">*</span></label>
            <input type="date" name="birthdate" id="birthdate" required>
          </div>

          <div class="form-group-half">
            <label><span class="label-text">Age</span><span class="required">*</span></label>
            <input type="number" id="age" name="age" placeholder="Age" readonly required>

            <label><span class="label-text">Sex</span><span class="required">*</span></label>
            <select name="sex" id="sex" required>
              <option value="">Select</option>
              <option value="M">Male</option>
              <option value="F">Female</option>
            </select>
          </div>

          <div class="form-group-half">
            <label><span class="label-text">Address</span><span class="required">*</span></label>
            <input type="text" name="address" placeholder="Address" required>

            <label><span class="label-text">Zipcode</span><span class="required">*</span></label>
            <input type="text" name="zipcode" placeholder="Zipcode" required>
          </div>

          <div class="account">
            <h2>Account</h2>

            <div class="form-group-half">
              <label><span class="label-text">Username</span><span class="required">*</span></label>
              <input type="text" name="username" placeholder="Username" required>

              <label><span class="label-text">Password</span><span class="required">*</span></label>
              <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="form-group">
              <label><span class="label-text">Confirm Password</span><span class="required">*</span></label>
              <input type="password" name="confirm_password" placeholder="Re-Password" required>
            </div>
          </div>

          <button type="submit" name="submit">Register</button>
        </div>
      </form>
    </div>
  </div>
</main>

    
    <footer>
        <h2>&copy; 2025 KickZone. All rights reserved.</h2>
    </footer>
</body>
</html>

<script>
   document.addEventListener("DOMContentLoaded", () => {
  const inputs = document.querySelectorAll("#registrationForm input, #registrationForm select");

  inputs.forEach(input => {
    let placeholder = input.getAttribute("placeholder") || "";

    // Prevent duplicate marks
    if (placeholder.includes("(optional)")) return;

    if (!input.hasAttribute("required")) {
      input.setAttribute("placeholder", placeholder + " (optional)");
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registrationForm");

  form.addEventListener("submit", (e) => {
    const nameFields = ["firstname", "middlename", "lastname", "extension"];
    let valid = true;
    let errorMessage = "";

    // === NAME VALIDATION ===
    nameFields.forEach(field => {
      const input = document.querySelector(`input[name='${field}']`);
      const value = input.value.trim();

      // 1. No special characters (letters and spaces only)
      if (!/^[A-Za-z\s]+$/.test(value) && value !== "") {
        valid = false;
        errorMessage += `❌ ${field} should not contain special characters.\n`;
      }

      // 2. No numbers followed by letters (e.g., 12John)
      if (/\d+[A-Za-z]/.test(value)) {
        valid = false;
        errorMessage += `❌ ${field} should not have numbers followed by letters.\n`;
      }

      // 3. No double spaces
      if (/\s{2,}/.test(value)) {
        valid = false;
        errorMessage += `❌ ${field} should not contain double spaces.\n`;
      }

      // 4. Not all uppercase
      if (value === value.toUpperCase() && value !== "") {
        valid = false;
        errorMessage += `❌ ${field} should not be all capital letters.\n`;
      }

      // 5. No 3 consecutive identical letters
      if (/(.)\1\1/i.test(value)) {
        valid = false;
        errorMessage += `❌ ${field} should not have three consecutive identical letters.\n`;
      }

      // 6. Each word must start with a capital letter, rest lowercase
      if (value !== "") {
        const words = value.split(" ");
        const incorrectFormat = words.some(word => !/^[A-Z][a-z]*$/.test(word));
        if (incorrectFormat) {
          valid = false;
          errorMessage += `❌ ${field} must start with a capital letter and the rest must be lowercase (e.g., Juan Carlo).\n`;
        }
      }
    });

    // === PASSWORD VALIDATION ===
    const password = document.querySelector("input[name='password']").value.trim();
    const confirm_password = document.querySelector("input[name='repassword']").value.trim();

    if (password === "") {
      valid = false;
      errorMessage += "❌ Password cannot be empty.\n";
    } else {
      // Password Strength
      let strength = "";
      if (password.length < 6) {
        strength = "Weak";
      } else if (
        password.length >= 6 &&
        /[A-Za-z]/.test(password) &&
        /\d/.test(password)
      ) {
        strength = "Medium";
      } else if (
        password.length >= 8 &&
        /[A-Z]/.test(password) &&
        /[a-z]/.test(password) &&
        /\d/.test(password) &&
        /[!@#$%^&*(),.?":{}|<>]/.test(password)
      ) {
        strength = "Strong";
      } else {
        strength = "Weak";
      }

      // Show strength info
      alert(`🔒 Password Strength: ${strength}`);

      // Check match with re-entered password
      if (password !== confirmPassword) {
        valid = false;
        errorMessage += "❌ Password and Re-enter Password must match.\n";
      }
    }

    // === SHOW ERRORS IF ANY ===
    if (!valid) {
      e.preventDefault();
      alert(errorMessage);
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const birthdateInput = document.getElementById("birthdate");
  const ageInput = document.getElementById("age");

  // Disable future dates
  const today = new Date().toISOString().split("T")[0];
  birthdateInput.setAttribute("max", today);

  birthdateInput.addEventListener("change", function () {
    const birthdateValue = new Date(this.value);
    const now = new Date();

    if (!isNaN(birthdateValue)) {
      let age = now.getFullYear() - birthdateValue.getFullYear();
      const m = now.getMonth() - birthdateValue.getMonth();
      if (m < 0 || (m === 0 && now.getDate() < birthdateValue.getDate())) {
        age--;
      }

      // Prevent negative ages
      if (age >= 0) {
        ageInput.value = age;
      } else {
        ageInput.value = "";
        alert("Please select a valid birthdate.");
      }
    }
  });
});

  </script>