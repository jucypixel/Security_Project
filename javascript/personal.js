// javascript/conditions.js
// Live validation + auto-age for personal.php
document.addEventListener("DOMContentLoaded", () => {

  // ---------- Elements ----------
  const form = document.getElementById("personalInfoForm");
  const btnNext = form.querySelector("button[type='submit']");

  const firstname = form.querySelector('input[name="firstname"]');
  const lastname = form.querySelector('input[name="lastname"]');
  const middlename = form.querySelector('input[name="middlename"]');
  const extension = form.querySelector('input[name="extension"]');
  const birthdate = form.querySelector('input[name="birthdate"]');
  const ageInput = form.querySelector('input[name="age"]');
  const sex = form.querySelector('select[name="sex"]');
  const email = form.querySelector('input[name="email"]');

  // Error spans (should be present in your HTML, above inputs)
  const errs = {
    firstname: document.getElementById("firstname-error"),
    lastname:  document.getElementById("lastname-error"),
    middlename: document.getElementById("middlename-error"),
    extension: document.getElementById("extension-error"),
    birthdate: document.getElementById("birthdate-error"),
    age: document.getElementById("age-error"),
    sex: document.getElementById("sex-error"),
    email: document.getElementById("email-error")
  };

  // ---------- Config ----------
  const MIN_AGE = 18;
  const MAX_AGE = 150;

  // Set birthdate max to today
  const todayISO = new Date().toISOString().split("T")[0];
  if (birthdate) birthdate.setAttribute("max", todayISO);

  // Make age read-only / disabled
  if (ageInput) {
    ageInput.readOnly = true;
    ageInput.disabled = true;
  }

  // ---------- Helpers ----------
  function showError(span, message) {
    if (!span) return;
    span.textContent = message || "";
    span.style.display = message ? "block" : "none";
  }

  function clearError(span) {
    showError(span, "");
  }

  // Checks for special characters (allow letters and spaces only)
  function containsInvalidCharacters(value) {
    // allow letters (unicode letter), spaces, dots and hyphens for extension maybe;
    // But for names, we only allow letters and spaces
    return !/^[A-Za-z\s]+$/.test(value);
  }

  function hasNumberFollowedByLetter(value) {
    return /\d+[A-Za-z]/.test(value);
  }

  function hasDoubleSpace(value) {
    return /\s{2,}/.test(value);
  }

  function hasThreeConsecutiveSameLetter(value) {
    return /(.)\1\1/i.test(value);
  }

  function isAllUpperCase(value) {
    return value.length > 1 && value === value.toUpperCase();
  }

  function wordsCapitalized(value) {
    // Each word must start with uppercase then lowercase letters only
    const words = value.split(" ").filter(w => w.length > 0);
    if (words.length === 0) return false;
    return words.every(w => /^[A-Z][a-z]+$/.test(w));
  }

  function isValidEmail(v) {
    // Simple but robust email regex for validation purposes
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  }

  function calculateAgeFromDOB(dobString) {
    if (!dobString) return NaN;
    const b = new Date(dobString);
    if (isNaN(b.getTime())) return NaN;
    const now = new Date();
    let age = now.getFullYear() - b.getFullYear();
    const m = now.getMonth() - b.getMonth();
    if (m < 0 || (m === 0 && now.getDate() < b.getDate())) age--;
    return age;
  }

  // ---------- Individual field validators (return boolean) ----------
  function validateFirstname(show = true) {
    const v = (firstname.value || "").trim();
    let ok = true;
    if (!v) {
      if (show) showError(errs.firstname, "First Name* is required");
      ok = false;
    } else if (containsInvalidCharacters(v)) {
      if (show) showError(errs.firstname, "First Letter of your name must start with capital and only letters/spaces allowed.");
      ok = false;
    } else if (hasNumberFollowedByLetter(v)) {
      if (show) showError(errs.firstname, "First Name should not have numbers followed by letters.");
      ok = false;
    } else if (hasDoubleSpace(v)) {
      if (show) showError(errs.firstname, "First Name must not contain double spaces.");
      ok = false;
    } else if (isAllUpperCase(v)) {
      if (show) showError(errs.firstname, "First Name should not be all uppercase.");
      ok = false;
    } else if (hasThreeConsecutiveSameLetter(v)) {
      if (show) showError(errs.firstname, "First Name should not have three consecutive identical letters.");
      ok = false;
    } else if (!wordsCapitalized(v)) {
      if (show) showError(errs.firstname, "First Letter of each word must be CAPITAL, rest lowercase (e.g., Juan Carlo).");
      ok = false;
    } else {
      if (show) clearError(errs.firstname);
    }
    return ok;
  }

  function validateLastname(show = true) {
    const v = (lastname.value || "").trim();
    let ok = true;
    if (!v) {
      if (show) showError(errs.lastname, "Last Name* is required");
      ok = false;
    } else if (containsInvalidCharacters(v)) {
      if (show) showError(errs.lastname, "Last Name must only contain letters and spaces.");
      ok = false;
    } else if (hasNumberFollowedByLetter(v)) {
      if (show) showError(errs.lastname, "Last Name should not have numbers followed by letters.");
      ok = false;
    } else if (hasDoubleSpace(v)) {
      if (show) showError(errs.lastname, "Last Name must not contain double spaces.");
      ok = false;
    } else if (isAllUpperCase(v)) {
      if (show) showError(errs.lastname, "Last Name should not be all uppercase.");
      ok = false;
    } else if (hasThreeConsecutiveSameLetter(v)) {
      if (show) showError(errs.lastname, "Last Name should not have three consecutive identical letters.");
      ok = false;
    } else if (!wordsCapitalized(v)) {
      if (show) showError(errs.lastname, "First Letter of each word must be CAPITAL, rest lowercase (e.g., Juan Carlo).");
      ok = false;
    } else {
      if (show) clearError(errs.lastname);
    }
    return ok;
  }

  function validateMiddlename(show = true) {
    const v = (middlename.value || "").trim();
    if (!v) {
      // optional - valid when empty
      if (show) clearError(errs.middlename);
      return true;
    }
    // If provided, must be a single letter initial (A-Z) OR a short name (but you said initial)
    if (!/^[A-Za-z]$/.test(v)) {
      if (show) showError(errs.middlename, "Middle Initial must be one letter (A-Z).");
      return false;
    }
    // must be capital single-letter
    if (v !== v.toUpperCase()) {
      if (show) showError(errs.middlename, "Middle Initial must be CAPITAL letter.");
      return false;
    }
    if (show) clearError(errs.middlename);
    return true;
  }

  function validateExtension(show = true) {
    const v = (extension.value || "").trim();
    if (!v) {
      if (show) clearError(errs.extension);
      return true; // optional
    }
    // allow letters, dots and roman numerals / numbers but not special chars
    // We'll allow letters and dots and numbers: /^[A-Za-z0-9\. ]+$/
    if (!/^[A-Za-z0-9\. ]+$/.test(v)) {
      if (show) showError(errs.extension, "Suffix contains invalid characters.");
      return false;
    }
    if (v.length > 10) {
      if (show) showError(errs.extension, "Suffix too long (max 10 characters).");
      return false;
    }
    // No three consecutive identical letters
    if (hasThreeConsecutiveSameLetter(v)) {
      if (show) showError(errs.extension, "Suffix should not have three consecutive identical letters.");
      return false;
    }
    if (show) clearError(errs.extension);
    return true;
  }

  function validateBirthdateAndAge(show = true) {
    const dob = (birthdate.value || "").trim();
    if (!dob) {
      if (show) showError(errs.birthdate, "Birthdate* is required.");
      if (show) showError(errs.age, "Age is required.");
      return false;
    }
    const ageCalc = calculateAgeFromDOB(dob);
    if (isNaN(ageCalc)) {
      if (show) showError(errs.birthdate, "Invalid birthdate.");
      return false;
    }
    // set age input visually
    if (ageInput) {
      if (ageCalc < 0 || ageCalc > MAX_AGE) ageInput.value = "";
      else ageInput.value = ageCalc;
    }
    // check legal age
    if (ageCalc < MIN_AGE) {
      if (show) showError(errs.age, `You must be at least ${MIN_AGE} years old.`);
      return false;
    }
    // check reasonable max
    if (ageCalc > MAX_AGE) {
      if (show) showError(errs.age, "Please enter a valid birthdate.");
      return false;
    }
    if (show) {
      clearError(errs.birthdate);
      clearError(errs.age);
    }
    return true;
  }

  function validateEmailField(show = true) {
    const v = (email.value || "").trim();
    if (!v) {
      if (show) showError(errs.email, "Email* is required.");
      return false;
    }
    if (!isValidEmail(v)) {
      if (show) showError(errs.email, "Please enter a valid email address.");
      return false;
    }
    clearError(errs.email);
    return true;
  }

  function validateSexField(show = true) {
    const v = (sex.value || "").trim();
    if (!v) {
      if (show) showError(errs.sex, "Please select sex.");
      return false;
    }
    if (!["M", "F"].includes(v)) {
      if (show) showError(errs.sex, "Invalid sex selection.");
      return false;
    }
    clearError(errs.sex);
    return true;
  }

  // ---------- Live events (real-time) ----------
  // Debounce helper for typing
  function debounce(fn, wait = 250) {
    let t;
    return function(...args) {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), wait);
    };
  }

  firstname.addEventListener("input", debounce(() => {
    validateFirstname(true);
    updateNextState();
  }));

  lastname.addEventListener("input", debounce(() => {
    validateLastname(true);
    updateNextState();
  }));

  middlename.addEventListener("input", debounce(() => {
    validateMiddlename(true);
    updateNextState();
  }));

  extension.addEventListener("input", debounce(() => {
    validateExtension(true);
    updateNextState();
  }));

  email.addEventListener("input", debounce(() => {
    validateEmailField(true);
    updateNextState();
  }));

  sex.addEventListener("change", () => {
    validateSexField(true);
    updateNextState();
  });

  birthdate.addEventListener("change", () => {
    validateBirthdateAndAge(true);
    updateNextState();
  });

  // ---------- Overall form validity ----------
  function isFormValid() {
    // call validators with show=false to avoid flicker when checking overall state
    const a = validateFirstname(false);
    const b = validateLastname(false);
    const c = validateMiddlename(false);
    const d = validateExtension(false);
    const e = validateBirthdateAndAge(false);
    const f = validateEmailField(false);
    const g = validateSexField(false);
    return a && b && c && d && e && f && g;
  }

  function updateNextState() {
    if (!btnNext) return;
    if (isFormValid()) {
      btnNext.disabled = false;
      btnNext.classList.remove("disabled");
    } else {
      btnNext.disabled = true;
      btnNext.classList.add("disabled");
    }
  }

  // Initialize button state on load
  updateNextState();

  // ---------- Final form submit guard ----------
  form.addEventListener("submit", (e) => {
    // Run validators and show messages if invalid
    const ok1 = validateFirstname(true);
    const ok2 = validateLastname(true);
    const ok3 = validateMiddlename(true);
    const ok4 = validateExtension(true);
    const ok5 = validateBirthdateAndAge(true);
    const ok6 = validateEmailField(true);
    const ok7 = validateSexField(true);

    if (!(ok1 && ok2 && ok3 && ok4 && ok5 && ok6 && ok7)) {
      e.preventDefault();
      updateNextState();
      // focus the first invalid field
      const order = ["firstname","lastname","birthdate","sex","email"];
      for (let k of order) {
        if (errs[k] && errs[k].textContent) {
          // focus corresponding input
          switch(k) {
            case "firstname": firstname.focus(); break;
            case "lastname": lastname.focus(); break;
            case "birthdate": birthdate.focus(); break;
            case "sex": sex.focus(); break;
            case "email": email.focus(); break;
            default: break;
          }
          break;
        }
      }
    } else {
      // All client-side valid — allow submit
      // Note: server-side validation must still re-check everything.
    }
  });

  // show/hide error containers initially (in case CSS needs them visible)
  Object.values(errs).forEach(span => {
    if (span) {
      span.style.display = span.textContent ? "block" : "none";
    }
  });

}); // DOMContentLoaded end

