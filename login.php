<?php
// login.php
require_once "connect.php";
session_start(); // start session at the very top

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";

    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if ($password === "") {
        $errors[] = "Password is required.";
    }
    if (!in_array($role, ["client","specialist","admin"], true)) {
        $errors[] = "Please select a valid role.";
    }

    if (empty($errors)) {
        // Plain-text password check (for assignment only)
        $stmt = $mysqli->prepare(
            "SELECT id, full_name, role FROM users WHERE email = ? AND password = ? AND role = ?"
        );
        $stmt->bind_param("sss", $email, $password, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // store user info in session
            $_SESSION['user_id'] = (int)$row['id'];
            $_SESSION['role']    = $row['role'];

            // Redirect based on role
            if ($role === "client") {
                header("Location: profile_c.php");
            } elseif ($role === "specialist") {
                header("Location: profile_s.php");
            } else { // admin
                header("Location: admin.php");
            }
            exit;
        } else {
            $errors[] = "Invalid email, password, or role.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="login-wrapper">
    <form class="login-card" id="loginForm" method="post" action="login.php">
      <h1 class="login-title">Welcome back</h1>
      <p class="login-subtitle">Log in to continue to your dashboard.</p>

      <?php if (!empty($errors)): ?>
        <div class="error-message" style="margin-bottom:8px;">
          <?php foreach ($errors as $e): ?>
            <div><?php echo htmlspecialchars($e); ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

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
          placeholder="••••••••"
          required
        >
      </label>

      <label class="field">
        <span>User role</span>
        <select name="role" id="role" required>
          <option value="" disabled <?php echo empty($role) ? 'selected' : ''; ?>>Select role</option>
          <option value="client" <?php echo (isset($role) && $role === 'client') ? 'selected' : ''; ?>>Client</option>
          <option value="specialist" <?php echo (isset($role) && $role === 'specialist') ? 'selected' : ''; ?>>Specialist</option>
          <option value="admin" <?php echo (isset($role) && $role === 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>
      </label>

      <button type="submit" class="login-button">Log in</button>

      <p class="signup-text">
        Don't have an account?
        <a href="signup.php">Sign up here</a>
      </p>
    </form>
  </div>
</body>
</html>
