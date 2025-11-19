document.addEventListener("DOMContentLoaded", () => {

    // ==========================
    // Utility Functions
    // ==========================

    const showError = (input, message) => {
    clearError(input);

    const group = input.closest(".form-group");
    const label = group.querySelector("label");

    const error = document.createElement("div");
    error.classList.add("error-message");
    error.innerText = message;
    error.style.color = "red";
    error.style.margin = "3px 0";

    // Insert error BELOW the label, ABOVE the input
    label.insertAdjacentElement("afterend", error);
    };

    const clearError = (input) => {
        const group = input.closest(".form-group");
        const exists = group.querySelector(".error-message");
        if (exists) exists.remove();
    };

    const hasSpecialChars = (str) => /[^A-Za-z\s]/.test(str);
    const hasNumbers = (str) => /[0-9]/.test(str);
    const hasDoubleSpace = (str) => /\s{2,}/.test(str);
    const isAllCaps = (str) => str === str.toUpperCase();
    const hasTripleLetters = (str) => /(.)\1\1/i.test(str);

    const capitalizeFormat = (str) =>
        str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();


    // =================================================
    // ADDRESS VALIDATION (ALL TEXT FIELDS)
    // =================================================
    const addressFields = ["street", "barangay", "city", "province", "country"];
    addressFields.forEach(id => {
        const input = document.getElementById(id);

        input.addEventListener("input", () => {
            let val = input.value.trim();

            clearError(input);

            if (!val) {
                showError(input, "This field is required.");
                return;
            }

            if (hasSpecialChars(val)) {
                showError(input, "Special characters are not allowed.");
                return;
            }

            if (hasNumbers(val)) {
                showError(input, "Numbers are not allowed.");
                return;
            }

            if (hasDoubleSpace(val)) {
                showError(input, "Double spaces are not allowed.");
                return;
            }

            if (hasTripleLetters(val)) {
                showError(input, "Three consecutive letters are not allowed.");
                return;
            }

            if (isAllCaps(val)) {
                showError(input, "All capital letters are not allowed.");
                return;
            }

            if (val[0] !== val[0].toUpperCase()) {
                showError(input, "First letter must be capital.");
                return;
            }

            // Apply formatting
            input.value = capitalizeFormat(val);
        });
    });

    // ==========================================
    // PUROK (P-, Purok-, numbers, optional '-' and text after)
    // Examples: P-6, Purok-6, Purok - 6 Paradise, P-6-2 Area 5
    // ==========================================
    const purok = document.getElementById("street");

    purok.addEventListener("input", () => {

        clearError(purok);
        let val = purok.value.trim();

        if (!val) {
            showError(purok, "Purok is required.");
            return;
        }

        // VALID PATTERN:
        // ^(P|Purok)\s*-\s*\d+(?:-\d+)*(?:\s+[A-Za-z ]+)?$
        const purokPattern = /^(P|Purok)\s*-\s*\d+(?:-\d+)*(?:\s+[A-Za-z ]+)?$/i;

        if (!purokPattern.test(val)) {
            showError(
                purok,
                "Format examples: P-6, Purok-6, P-6 Paradise, Purok - 6 Area 5"
            );
            return;
        }

        // no double dash
        if (/--/.test(val)) {
            showError(purok, "Double dash '--' is not allowed.");
            return;
        }

    });



    // ==========================================
    // ZIP CODE VALIDATION
    // ==========================================
    const zipcode = document.getElementById("zipcode");
    zipcode.addEventListener("input", () => {
        clearError(zipcode);

        if (!/^\d{4}$/.test(zipcode.value)) {
            showError(zipcode, "ZIP code must be exactly 4 digits.");
        }
    });


    // ==========================================
    // AUTO-GENERATE USERNAME + STRICT PATTERN
    // Pattern example: firstname.lastname
    // ==========================================

    const fnameInput = document.getElementById("firstname");
    const lnameInput = document.getElementById("lastname");
    const username = document.getElementById("username");

    // Generate username automatically
    function generateUsername() {
        let fname = firstnameInput.value.trim().toLowerCase().replace(/[^a-z]/g, "");
        let lname = lastnameInput.value.trim().toLowerCase().replace(/[^a-z]/g, "");

        if (fname && lname) {
            username.value = `${fname}.${lname}`;
            username.dispatchEvent(new Event("input")); // trigger validation
        }
    }

    firstnameInput.addEventListener("input", generateUsername);
    lastnameInput.addEventListener("input", generateUsername);

    // Username validation
    username.addEventListener("input", () => {
        let val = username.value.trim();
        clearError(username);

        if (!val) {
            showError(username, "Username is required.");
            return;
        }

        // STRICT PATTERN firstname.lastname
        const pattern = /^[a-z]{2,}\.[a-z]{2,}$/;

        if (!pattern.test(val)) {
            showError(username, "Username must be in the format: firstname.lastname");
            return;
        }

        // AJAX CHECK (same as before)
        fetch("check_username.php?username=" + encodeURIComponent(val))
            .then(res => res.text())
            .then(data => {
                if (data === "exists") {
                    showError(username, "Username already exists.");
                }
            });
    });



    // ==========================================
    // ID NUMBER VALIDATION + DB CHECK
    // ==========================================
    const idnum = document.getElementById("id_number");
    idnum.addEventListener("input", () => {
        clearError(idnum);

        if (!/^[0-9-]+$/.test(idnum.value)) {
            showError(idnum, "ID number must contain digits only.");
            return;
        }

        fetch("check_id.php?id=" + encodeURIComponent(idnum.value))
        .then(res => res.text())
        .then(data => {
            if (data === "exists") {
                showError(idnum, "ID number already exists.");
            }
        });
    });


    // ==========================================
    // PASSWORD VALIDATION
    // ==========================================
    const password = document.getElementById("password");
    const confirmPass = document.getElementById("password_confirm");

    password.addEventListener("input", () => {
    const val = password.value.trim();
    const strengthBox = document.getElementById("passwordStrength");

    clearError(password);
    strengthBox.innerHTML = ""; // reset meter

    // ============================
    // VALIDATION RULES
    // ============================

    // MIN 8
    if (val.length < 8) {
        showError(password, "Password must be at least 8 characters.");
        strengthBox.innerHTML = `<span style="color:red">Password Strength: Weak</span>`;
        return;
    }

    // MAX 16
    if (val.length > 16) {
        showError(password, "Password must not exceed 16 characters.");
        strengthBox.innerHTML = `<span style="color:red">Password Strength: Weak</span>`;
        return;
    }

    // UPPERCASE
    if (!/[A-Z]/.test(val)) {
        showError(password, "Password must contain at least 1 uppercase letter.");
        strengthBox.innerHTML = `<span style="color:red">Password Strength: Weak</span>`;
        return;
    }

    // LOWERCASE
    if (!/[a-z]/.test(val)) {
        showError(password, "Password must contain at least 1 lowercase letter.");
        strengthBox.innerHTML = `<span style="color:red">Password Strength: Weak</span>`;
        return;
    }

    // NUMBER
    if (!/\d/.test(val)) {
        showError(password, "Password must contain at least 1 number.");
        strengthBox.innerHTML = `<span style="color:red">Password Strength: Weak</span>`;
        return;
    }

    // MUST CONTAIN SPECIAL CHARACTER
    if (!/[^A-Za-z0-9\s]/.test(val)) {
        showError(password, "Password must contain at least 1 special character.");
        strengthBox.innerHTML = `<span style="color:red">Password Strength: Weak</span>`;
        return;
    }

    // DISALLOW DOUBLE SPACES
    if (hasDoubleSpace(val)) {
        showError(password, "Double spaces are not allowed.");
        return;
    }

    // DISALLOW TRIPLE REPEATED CHARACTERS
    if (hasTripleLetters(val)) {
        showError(password, "Too many repeating characters.");
        return;
    }

    // ============================
    // STRENGTH METER
    //============================

    let strength = "Weak";
    let color = "red";

    // Medium
    if (
        val.length >= 8 &&
        /[A-Z]/.test(val) &&
        /[a-z]/.test(val) &&
        /\d/.test(val) &&
        /[^A-Za-z0-9]/.test(val)
    ) {
        strength = "Medium";
        color = "gold";
    }

    // Strong
    if (
        val.length >= 10 &&
        /[A-Z]/.test(val) &&
        /[a-z]/.test(val) &&
        /\d/.test(val) &&
        /[^A-Za-z0-9]/.test(val)
    ) {
        strength = "Strong";
        color = "limegreen";
    }

    strengthBox.innerHTML = `Password Strength: <b style="color:${color}">${strength}</b>`;
    });





    // ==========================================
    // CONFIRM PASSWORD CHECK
    // ==========================================
    confirmPass.addEventListener("input", () => {
        clearError(confirmPass);

        if (confirmPass.value !== password.value) {
            showError(confirmPass, "Passwords do not match.");
        }
    });

    // ==========================================
    // EMAIL VALIDATION
    // ==========================================
    const email = document.getElementById("email");

    email.addEventListener("input", () => {
        clearError(email);
        let val = email.value.trim();

        if (!val) {
            showError(email, "Email is required.");
            return;
        }

        if (/\s/.test(val)) {
            showError(email, "Email cannot contain spaces.");
            return;
        }

        // Strict Email Pattern
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;

        if (!emailPattern.test(val)) {
            showError(email, "Please enter a valid email format.");
            return;
        }

        // Allow only valid domains (customize here)
        const allowedDomains = [
            "gmail.com",
            "yahoo.com",
            "outlook.com",
            "hotmail.com"
        ];

        const domain = val.split("@")[1].toLowerCase();

        if (!allowedDomains.includes(domain)) {
            showError(email, "Email domain must be Gmail, Yahoo, Outlook, or Hotmail.");
            return;
        }

        // AJAX CHECK (already exists?)
        fetch("check_email.php?email=" + encodeURIComponent(val))
            .then(res => res.text())
            .then(data => {
                if (data === "exists") {
                    showError(email, "Email is already registered.");
                }
            });
    });




    // ===============================
    // FINAL VALIDATION ON SUBMIT
    // ===============================
    document.getElementById("accountInfoForm").addEventListener("submit", function(e) {

    // Only count REAL error messages
    let errors = [...document.querySelectorAll(".error-message")]
        .filter(err => err.textContent.trim() !== "");

    if (errors.length > 0) {
        e.preventDefault();
        alert("Please fix all highlighted errors before proceeding.");
        return false;
    }

    return true;
    });


});
