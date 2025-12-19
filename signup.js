document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("signupForm");
  const fullNameInput = document.getElementById("fullName");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const roleSelect = document.getElementById("role");
  const errorMessage = document.getElementById("errorMessage");

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const fullName = fullNameInput.value.trim();
    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();
    const role = roleSelect.value;

    if (!fullName || !email || !password || !role) {
      errorMessage.textContent = "Please fill in all fields and choose a role.";
      return;
    }

    if (password.length < 6) {
      errorMessage.textContent = "Password must be at least 6 characters long.";
      return;
    }

    errorMessage.textContent = "";

    // In a real app you would send data to the server here.
    // For now, just go back to login page.
    window.location.href = "login.html";
  });
});
