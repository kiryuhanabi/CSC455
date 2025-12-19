document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const roleSelect = document.getElementById("role");
  const errorMessage = document.getElementById("errorMessage");

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();
    const role = roleSelect.value;

    if (!email || !password || !role) {
      errorMessage.textContent = "Please fill in email, password, and select a role.";
      return;
    }

    if (password.length < 6) {
      errorMessage.textContent = "Password must be at least 6 characters long.";
      return;
    }

    // All validations passed: redirect to home.html
    errorMessage.textContent = "";
    window.location.href = "home.html";
  });
});
