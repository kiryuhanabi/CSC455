<?php
// signup.php
require_once "connect.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["fullName"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role     = $_POST["role"] ?? "";

    if ($fullName === "") {
        $errors[] = "Full name is required.";
    }
    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($role !== "client" && $role !== "specialist") {
        $errors[] = "Please choose a valid role.";
    }

    if (empty($errors)) {
        // Check if email exists
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "This email is already registered.";
        } else {
            // NO HASHING: store password directly (for assignment only)
            $insert = $mysqli->prepare(
                "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $insert->bind_param("ssss", $fullName, $email, $password, $role);

            if ($insert->execute()) {
                $success = "Registration successful. You can now log in.";
            } else {
                $errors[] = "Error saving user: " . $insert->error;
            }
            $insert->close();
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="signup.css">
</head>
<body>
  <div class="signup-wrapper">
    <form class="signup-card" id="signupForm" method="post" action="signup.php">
      <h1 class="signup-title">Create your account</h1>
      <p class="signup-subtitle">Join as a client or specialist to get started.</p>

      <?php if (!empty($errors)): ?>
        <div class="error-message" style="margin-bottom:8px;">
          <?php foreach ($errors as $e): ?>
            <div><?php echo htmlspecialchars($e); ?></div>
          <?php endforeach; ?>
        </div>
      <?php elseif ($success !== ""): ?>
        <div class="success-message" style="margin-bottom:8px; color:#22c55e;">
          <?php echo htmlspecialchars($success); ?>
        </div>
      <?php endif; ?>

      <label class="field">
        <span>Full name</span>
        <input
          type="text"
          name="fullName"
          id="fullName"
          placeholder="your full name"
          required
          value="<?php echo isset($fullName) ? htmlspecialchars($fullName) : ''; ?>"
        >
      </label>

      <label class="field">
        <span>Email</span>
        <input
          type="email"
          name="email"
          id="email"
          placeholder="you@example.com"
          required
          value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
        >
      </label>

      <label class="field">
        <span>Password</span>
        <input
          type="password"
          name="password"
          id="password"
          placeholder="At least 6 characters"
          required
        >
      </label>

      <label class="field">
        <span>Sign up as</span>
        <select name="role" id="role" required>
          <option value="" disabled <?php echo empty($role) ? 'selected' : ''; ?>>Select role</option>
          <option value="client" <?php echo (isset($role) && $role === 'client') ? 'selected' : ''; ?>>Client</option>
          <option value="specialist" <?php echo (isset($role) && $role === 'specialist') ? 'selected' : ''; ?>>Specialist</option>
        </select>
      </label>

      <button type="submit" class="signup-button">Sign up</button>

      <p class="login-text">
        Already have an account?
        <a href="login.php">Log in here</a>
      </p>
    </form>
  </div>
</body>
</html>
