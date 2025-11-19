const passwordInput = document.getElementById("new_password");
const confirmInput = document.getElementById("confirm_password");
const strengthText = document.getElementById("strength");

// Create error text container
let errorText = document.createElement("div");
errorText.style.color = "red";
errorText.style.fontSize = "10px";
errorText.style.margin = "3px 0";
errorText.id = "generated_password_error";

// Insert ABOVE the password input (between label and input)
passwordInput.parentNode.insertBefore(errorText, passwordInput);

passwordInput.addEventListener("input", () => {
    const val = passwordInput.value;

    const hasUpper = /[A-Z]/.test(val);
    const hasNumber = /[0-9]/.test(val);
    const hasSpecial = /[!@#$%^&*()_\-+=<>?/{}~]/.test(val);
    const minLength = val.length >= 8;
    const maxLength = val.length <= 16;

    let errorMessage = "";

    // Show ONE error at a time (sequential)
    if (!minLength) {
        errorMessage = "Minimum 8 characters required.";
    } else if (!maxLength) {
        errorMessage = "Maximum 16 characters only.";
    } else if (!hasUpper) {
        errorMessage = "Must contain at least 1 uppercase letter.";
    } else if (!hasNumber) {
        errorMessage = "Must contain at least 1 number.";
    } else if (!hasSpecial) {
        errorMessage = "Must contain at least 1 special character.";
    }

    errorText.textContent = errorMessage;

    // Strength logic
    let strength = "Weak";
    let color = "red";

    if (minLength && hasUpper && hasNumber && hasSpecial && maxLength) {
        strength = "Strong";
        color = "limegreen";
    } else if (minLength) {
        strength = "Medium";
        color = "orange";
    }

    strengthText.textContent = strength;
    strengthText.style.color = color;
});

// Confirm password validation
confirmInput.addEventListener("input", () => {
    if (confirmInput.value !== passwordInput.value) {
        confirmInput.style.border = "2px solid red";
    } else {
        confirmInput.style.border = "2px solid limegreen";
    }
});






