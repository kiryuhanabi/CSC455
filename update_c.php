<?php
// update_c.php
require_once "connect.php";
session_start();

// Only logged-in clients
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$errors = [];

// Load existing profile to prefill form
$university = $major = $minor = $cgpa = $expected_grad = "";
$skills = $achievements = $certs = "";

$stmt = $mysqli->prepare(
    "SELECT university, major, minor, cgpa, expected_grad,
            technical_skills, achievements, certifications
     FROM client_profiles
     WHERE user_id = ?"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $university    = $row['university'] ?? "";
    $major         = $row['major'] ?? "";
    $minor         = $row['minor'] ?? "";
    $cgpa          = $row['cgpa'] ?? "";
    $expected_grad = $row['expected_grad'] ?? "";
    $skills        = $row['technical_skills'] ?? "";
    $achievements  = $row['achievements'] ?? "";
    $certs         = $row['certifications'] ?? "";
}
$stmt->close();

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $university    = trim($_POST["university"] ?? "");
    $major         = trim($_POST["major"] ?? "");
    $minor         = trim($_POST["minor"] ?? "");
    $cgpa          = trim($_POST["cgpa"] ?? "");
    $expected_grad = $_POST["gradDate"] ?? "";
    $skills        = trim($_POST["skills"] ?? "");
    $achievements  = trim($_POST["achievements"] ?? "");
    $certs         = trim($_POST["certs"] ?? "");

    if ($university === "" || $major === "") {
        $errors[] = "Please fill in at least university and major.";
    }

    if (empty($errors)) {
        // Check if profile row exists
        $stmt = $mysqli->prepare("SELECT id FROM client_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        if ($exists) {
            // Update
            $stmt = $mysqli->prepare(
                "UPDATE client_profiles
                 SET university = ?, major = ?, minor = ?, cgpa = ?, expected_grad = ?,
                     technical_skills = ?, achievements = ?, certifications = ?
                 WHERE user_id = ?"
            );
            $stmt->bind_param(
                "ssssssssi",
                $university,
                $major,
                $minor,
                $cgpa,
                $expected_grad,
                $skills,
                $achievements,
                $certs,
                $userId
            );
        } else {
            // Insert
            $stmt = $mysqli->prepare(
                "INSERT INTO client_profiles
                 (user_id, university, major, minor, cgpa, expected_grad,
                  technical_skills, achievements, certifications)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "issssssss",
                $userId,
                $university,
                $major,
                $minor,
                $cgpa,
                $expected_grad,
                $skills,
                $achievements,
                $certs
            );
        }

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: profile_c.php");
            exit;
        } else {
            $errors[] = "Error saving profile: " . $stmt->error;
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update client profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="update_c.css">
</head>
<body>
  <div class="update-wrapper">
    <form class="update-card" id="updateForm" method="post" action="update_c.php">
      <h1 class="update-title">Update profile</h1>
      <p class="update-subtitle">Add academic information and skills to complete your profile.</p>

      <?php if (!empty($errors)): ?>
        <p class="error-message" id="errorMessage">
          <?php foreach ($errors as $e): ?>
            <div><?php echo htmlspecialchars($e); ?></div>
          <?php endforeach; ?>
        </p>
      <?php endif; ?>

      <label class="field">
        <span>University</span>
        <input
          type="text"
          name="university"
          id="university"
          placeholder="Your university name"
          value="<?php echo htmlspecialchars($university); ?>"
        >
      </label>

      <label class="field">
        <span>Major</span>
        <input
          type="text"
          name="major"
          id="major"
          placeholder="Primary field of study"
          value="<?php echo htmlspecialchars($major); ?>"
        >
      </label>

      <label class="field">
        <span>Minor</span>
        <input
          type="text"
          name="minor"
          id="minor"
          placeholder="Secondary field of study (optional)"
          value="<?php echo htmlspecialchars($minor); ?>"
        >
      </label>

      <label class="field">
        <span>CGPA</span>
        <input
          type="text"
          name="cgpa"
          id="cgpa"
          placeholder="e.g. 3.65"
          value="<?php echo htmlspecialchars($cgpa); ?>"
        >
      </label>

      <label class="field">
        <span>Expected graduation date</span>
        <input
          type="text"
          name="gradDate"
          id="gradDate"
          placeholder="e.g. June 2026"
          value="<?php echo htmlspecialchars($expected_grad); ?>"
        >
      </label>


      <label class="field field-textarea">
        <span>Technical skills</span>
        <textarea
          name="skills"
          id="skills"
          placeholder="List your technical skills, separated by commas"
        ><?php echo htmlspecialchars($skills); ?></textarea>
      </label>

      <label class="field field-textarea">
        <span>Achievements / awards</span>
        <textarea
          name="achievements"
          id="achievements"
          placeholder="Notable achievements, awards, competitions"
        ><?php echo htmlspecialchars($achievements); ?></textarea>
      </label>

      <label class="field field-textarea">
        <span>Certifications</span>
        <textarea
          name="certs"
          id="certs"
          placeholder="Relevant certificates and courses"
        ><?php echo htmlspecialchars($certs); ?></textarea>
      </label>

      <button type="submit" class="primary-btn">Save changes</button>
    </form>
  </div>
</body>
</html>
