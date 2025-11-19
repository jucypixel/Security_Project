// Disable browser back button
function disableBack() {
    window.history.forward();
    window.onunload = () => null;
}

// Run when page is ready
document.addEventListener('DOMContentLoaded', function() {
    disableBack();
    const sec = Number(lockoutSeconds);

    if (!isNaN(sec) && sec > 0) {
        disableButtons(sec);
    }
});

document.getElementById("forgotLink").addEventListener("click", () => {
    alert("CLICKED!");
});

// Disable login, register, and nav links during lockout
function disableButtons(seconds) {
    const loginBtn = document.querySelector('.btn');
    const registerLink = document.getElementById('signUpLink');
    const navLinks = document.querySelectorAll('nav a');
    const forgotLink = document.getElementById('forgotLink');

    if (!loginBtn) return;

    // Disable login button
    loginBtn.disabled = true;

    // Disable Sign Up link
    if (registerLink) {
        registerLink.style.pointerEvents = 'none';
        registerLink.style.opacity = '0.5';
        registerLink.style.cursor = 'not-allowed';
        registerLink.style.textDecoration = 'line-through';
    }

    // Disable nav links EXCEPT forgot password
    navLinks.forEach(link => {
        link.style.pointerEvents = 'none';
        link.style.opacity = '0.5';
        link.style.cursor = 'not-allowed';
    });

    // ❗Ensure Forgot Password link stays CLICKABLE
    if (forgotLink) {
        forgotLink.style.pointerEvents = 'auto';
        forgotLink.style.opacity = '1';
        forgotLink.style.cursor = 'pointer';
        forgotLink.style.textDecoration = 'none';
    }

    const originalSignUpText = registerLink ? registerLink.textContent : "";

    const interval = setInterval(() => {
        seconds--;

        loginBtn.textContent = `Please wait ${seconds}s`;

        if (registerLink) {
            registerLink.textContent = `Sign Up (${seconds}s)`;
        }

        if (seconds <= 0) {
            clearInterval(interval);
            loginBtn.textContent = 'Login';
            loginBtn.disabled = false;

            // Re-enable Sign Up
            if (registerLink) {
                registerLink.textContent = originalSignUpText;
                registerLink.style.pointerEvents = 'auto';
                registerLink.style.opacity = '1';
                registerLink.style.cursor = 'pointer';
                registerLink.style.textDecoration = 'none';
            }

            // Re-enable nav links
            navLinks.forEach(link => {
                link.style.pointerEvents = 'auto';
                link.style.opacity = '1';
                link.style.cursor = 'pointer';
            });
        }
    }, 1000);
}

// Toggle show/hide password
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const type = passwordField.type === 'password' ? 'text' : 'password';
    passwordField.type = type;

    if (type === 'password') {
        this.classList.remove('bx-show');
        this.classList.add('bx-hide');
    } else {
        this.classList.remove('bx-hide');
        this.classList.add('bx-show');
    }
});

// Allow Forgot Password redirect ALWAYS
const forgot = document.getElementById("forgotLink");
if (forgot) {
    forgot.addEventListener("click", function(e) {
        e.stopPropagation(); // stop interference
        window.location.href = "forgot.php"; // always redirect
    });
}
