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
