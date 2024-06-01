// JavaScript for real-time password confirmation
const passwordField = document.getElementById("password");
const confirmPasswordField = document.getElementById("confirmPassword");
const passwordError = document.getElementById("passwordError");
const submitButton = document.getElementById("submitButton");

confirmPasswordField.addEventListener("input", function () {
  if (confirmPasswordField.value !== passwordField.value) {
    passwordError.style.display = "block";
    submitButton.disabled = true;
  } else {
    passwordError.style.display = "none";
    submitButton.disabled = false;
  }
});

document.getElementById("password").addEventListener("input", function () {
  var passwordInput = document.getElementById("password").value.trim();
  var passwordError = document.getElementById("passwordError1");
  var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
  if (!passwordRegex.test(passwordInput)) {
    passwordError.style.display = "block";
    submitButton.disabled = true;
  } else {
    passwordError.style.display = "none";
    submitButton.disabled = false;
  }
});
