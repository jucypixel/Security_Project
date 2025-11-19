document.addEventListener("DOMContentLoaded", () => {

    // small helpers
    const q = sel => document.querySelector(sel);
    const showError = (input, message) => {
        if (!input) return;
        clearError(input);
        const group = input.closest(".form-group");
        if (!group) return;
        const label = group.querySelector("label") || group;
        const error = document.createElement("div");
        error.className = "error-message";
        error.innerText = message;
        error.style.color = "red";
        error.style.margin = "3px 0";
        label.insertAdjacentElement("afterend", error);
    };
    const clearError = (input) => {
        if (!input) return;
        const group = input.closest(".form-group");
        if (!group) return;
        const exists = group.querySelector(".error-message");
        if (exists) exists.remove();
    };

    const hasDoubleSpace = (s) => /\s{2,}/.test(s);
    const hasTripleLetters = (s) => /(.)\1\1/i.test(s);

    // Grab elements (guard if missing)
    const street = q("#street");
    const barangay = q("#barangay");
    const city = q("#city");
    const province = q("#province");
    const country = q("#country");
    const zipcode = q("#zipcode");
    const email = q("#email");
    const username = q("#username");
    const password = q("#password");
    const confirmPass = q("#password_confirm");
    const passwordStrength = q("#passwordStrength");
    const form = q("#accountInfoForm");

    // =========================
    // Address validation (simple)
    // =========================
    const textAddressFields = [street, barangay, city, province, country].filter(Boolean);
    textAddressFields.forEach(input => {
        input.addEventListener("input", () => {
            clearError(input);
            const val = input.value.trim();
            if (!val) { showError(input, "This field is required."); return; }
            if (/[^A-Za-z\s\-]/.test(val)) { showError(input, "Invalid characters."); return; }
            if (hasDoubleSpace(val)) { showError(input, "Double spaces are not allowed."); return; }
            if (hasTripleLetters(val)) { showError(input, "Too many repeated letters."); return; }
            // capitalise first letter
            input.value = val.charAt(0).toUpperCase() + val.slice(1);
        });
    });

    // Purok (street) strict pattern (if you want)
    if (street) {
        street.addEventListener("input", () => {
            clearError(street);
            const val = street.value.trim();
            if (!val) { showError(street, "Purok is required."); return; }
            const purokPattern = /^(P|Purok)\s*-\s*\d+(?:-\d+)*(?:\s+[A-Za-z ]+)?$/i;
            if (!purokPattern.test(val)) {
                showError(street, "Format examples: P-6, Purok-6, P-6 Paradise");
                return;
            }
        });
    }

    // ZIP
    if (zipcode) {
        zipcode.addEventListener("input", () => {
            clearError(zipcode);
            if (!/^\d{4}$/.test(zipcode.value)) {
                showError(zipcode, "ZIP code must be exactly 4 digits.");
            }
        });
    }

    // =========================
    // USERNAME validation
    // pattern: firstname.lastname  (two alpha groups separated by a dot)
    // =========================
    if (username) {
        username.addEventListener("input", () => {
            clearError(username);
            const v = username.value.trim();
            if (!v) { showError(username, "Username is required."); return; }
            const pattern = /^[a-z]{2,}\.[a-z]{2,}$/;
            if (!pattern.test(v)) { showError(username, "Username must be firstname.lastname (lowercase)."); return; }

            // AJAX check (server must return plain "exists" when taken)
            fetch("check_username.php?username=" + encodeURIComponent(v))
                .then(res => res.text())
                .then(txt => { if (txt.trim() === "exists") showError(username, "Username already exists."); })
                .catch(()=>{/*ignore network errors here*/});
        });
    }

    // =========================
    // EMAIL validation
    // =========================
    if (email) {
        email.addEventListener("input", () => {
            clearError(email);
            const v = email.value.trim();
            if (!v) { showError(email, "Email is required."); return; }
            if (/\s/.test(v)) { showError(email, "Email cannot contain spaces."); return; }
            const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!pattern.test(v)) { showError(email, "Please enter a valid email."); return; }

            // optional domain whitelist (remove if you want any domain)
            const allowed = ["gmail.com","yahoo.com","outlook.com","hotmail.com"];
            const domain = v.split("@")[1]?.toLowerCase() ?? "";
            if (!allowed.includes(domain)) { showError(email, "Allowed domains: gmail, yahoo, outlook, hotmail."); return; }

            // AJAX check
            fetch("check_email.php?email=" + encodeURIComponent(v))
                .then(res => res.text())
                .then(txt => { if (txt.trim() === "exists") showError(email, "Email already registered."); })
                .catch(()=>{});
        });
    }

    // =========================
    // PASSWORD validation + strength
    // =========================
    if (password) {
        password.addEventListener("input", () => {
            clearError(password);
            if (passwordStrength) passwordStrength.innerHTML = "";

            const v = password.value;

            if (v.length < 8) { showError(password, "Password must be at least 8 characters."); if (passwordStrength) passwordStrength.innerHTML = `<span style="color:red">Weak</span>`; return; }
            if (v.length > 16) { showError(password, "Password must not exceed 16 characters."); if (passwordStrength) passwordStrength.innerHTML = `<span style="color:red">Weak</span>`; return; }
            if (!/[A-Z]/.test(v)) { showError(password, "Password must contain at least 1 uppercase."); if (passwordStrength) passwordStrength.innerHTML = `<span style="color:red">Weak</span>`; return; }
            if (!/[a-z]/.test(v)) { showError(password, "Password must contain at least 1 lowercase."); if (passwordStrength) passwordStrength.innerHTML = `<span style="color:red">Weak</span>`; return; }
            if (!/\d/.test(v)) { showError(password, "Password must contain at least 1 number."); if (passwordStrength) passwordStrength.innerHTML = `<span style="color:red">Weak</span>`; return; }
            if (!/[^A-Za-z0-9\s]/.test(v)) { showError(password, "Password must contain at least 1 special character."); if (passwordStrength) passwordStrength.innerHTML = `<span style="color:red">Weak</span>`; return; }
            if (hasDoubleSpace(v)) { showError(password, "Double spaces are not allowed."); return; }
            if (hasTripleLetters(v)) { showError(password, "Too many repeating characters."); return; }

            // strength score
            let score = 0;
            if (v.length >= 8) score++;
            if (/[A-Z]/.test(v)) score++;
            if (/[a-z]/.test(v)) score++;
            if (/\d/.test(v)) score++;
            if (/[^A-Za-z0-9]/.test(v)) score++;

            if (passwordStrength) {
                if (score <= 2) passwordStrength.innerHTML = `<b style="color:red">Weak</b>`;
                else if (score === 3) passwordStrength.innerHTML = `<b style="color:gold">Medium</b>`;
                else passwordStrength.innerHTML = `<b style="color:limegreen">Strong</b>`;
            }
        });
    }

    // confirm password
    if (confirmPass && password) {
        confirmPass.addEventListener("input", () => {
            clearError(confirmPass);
            if (confirmPass.value !== password.value) showError(confirmPass, "Passwords do not match.");
        });
    }

    // final on-submit validation
    if (form) {
        form.addEventListener("submit", (e) => {
            // collect any visible errors
            const errors = [...document.querySelectorAll(".error-message")].filter(el => el.textContent.trim() !== "");
            if (errors.length > 0) {
                e.preventDefault();
                alert("Please fix all highlighted errors before proceeding.");
                return false;
            }
            // extra guard: ensure password fields match
            if (password && confirmPass && password.value !== confirmPass.value) {
                e.preventDefault();
                alert("Passwords do not match.");
                return false;
            }
            return true;
        });
    }

});
