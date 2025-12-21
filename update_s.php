<?php
// update_s.php
require_once "connect.php";
session_start();

// Only logged-in specialists
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'specialist') {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$errors = [];

// Load existing specialist profile (if any) to pre-fill the form
$bio = $title = $degrees = $experience = $skills = "";

$stmt = $mysqli->prepare(
    "SELECT bio, professional_title, degrees, experience, skills
     FROM specialist_profiles
     WHERE user_id = ?"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $bio        = $row['bio'] ?? "";
    $title      = $row['professional_title'] ?? "";
    $degrees    = $row['degrees'] ?? "";
    $experience = $row['experience'] ?? "";
    $skills     = $row['skills'] ?? "";
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bio        = trim($_POST["bio"] ?? "");
    $title      = trim($_POST["title"] ?? "");
    $degrees    = trim($_POST["degrees"] ?? "");
    $experience = trim($_POST["experience"] ?? "");
    $skills     = trim($_POST["skills"] ?? "");

    if ($bio === "" || $title === "") {
        $errors[] = "Please fill in at least short bio and professional title.";
    }

    if (empty($errors)) {
        // Check if profile row exists
        $stmt = $mysqli->prepare("SELECT id FROM specialist_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        if ($exists) {
            // Update existing specialist profile
            $stmt = $mysqli->prepare(
                "UPDATE specialist_profiles
                 SET bio = ?, professional_title = ?, degrees = ?, experience = ?, skills = ?
                 WHERE user_id = ?"
            );
            $stmt->bind_param(
                "sssssi",
                $bio,
                $title,
                $degrees,
                $experience,
                $skills,
                $userId
            );
        } else {
            // Insert new specialist profile
            $stmt = $mysqli->prepare(
                "INSERT INTO specialist_profiles
                 (user_id, bio, professional_title, degrees, experience, skills)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "isssss",
                $userId,
                $bio,
                $title,
                $degrees,
                $experience,
                $skills
            );
        }

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: profile_s.php");
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
  <title>Update Specialist Profile | CareerGuide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="update_s.css">
</head>
<body>
  <div class="update-wrapper">
    <form class="update-card" id="updateForm" method="post" action="update_s.php">
      <h1 class="update-title">Update specialist profile</h1>
      <p class="update-subtitle">
        Add a clear summary of your expertise so clients can find the right match.
      </p>

      <?php if (!empty($errors)): ?>
        <p class="error-message" id="errorMessage">
          <?php foreach ($errors as $e): ?>
            <div><?php echo htmlspecialchars($e); ?></div>
          <?php endforeach; ?>
        </p>
      <?php endif; ?>

      <label class="field field-textarea">
        <span>Short bio</span>
        <textarea
          name="bio"
          id="bio"
          placeholder="Briefly describe your background and focus areas"
        ><?php echo htmlspecialchars($bio); ?></textarea>
      </label>

      <label class="field">
        <span>Professional title</span>
        <input
          type="text"
          name="title"
          id="title"
          placeholder="e.g. Senior Data Scientist"
          value="<?php echo htmlspecialchars($title); ?>"
        >
      </label>

      <label class="field field-textarea">
        <span>Degrees / certifications</span>
        <textarea
          name="degrees"
          id="degrees"
          placeholder="List relevant degrees and certifications"
        ><?php echo htmlspecialchars($degrees); ?></textarea>
      </label>

      <label class="field field-textarea">
        <span>Experience</span>
        <textarea
          name="experience"
          id="experience"
          placeholder="Summarize your professional experience"
        ><?php echo htmlspecialchars($experience); ?></textarea>
      </label>

      <label class="field field-textarea">
        <span>Skills</span>
        <textarea
          name="skills"
          id="skills"
          placeholder="Key skills and tools you specialize in"
        ><?php echo htmlspecialchars($skills); ?></textarea>
      </label>

      <button type="submit" class="primary-btn">Save changes</button>
    </form>
  </div>
</body>
</html>
