<?php
// profile_s.php
require_once "connect.php";
session_start();

// Only logged-in specialists
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'specialist') {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];

// ---- Basic user info ----
$user = null;
$stmt = $mysqli->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $user = $row;
}
$stmt->close();

// ---- Specialist profile ----
$profile = [
    'bio' => 'Not set',
    'professional_title' => 'Not set',
    'degrees' => 'Not set',
    'experience' => 'Not set',
    'skills' => 'Not set',
];

$stmt = $mysqli->prepare(
    "SELECT bio, professional_title, degrees, experience, skills
     FROM specialist_profiles
     WHERE user_id = ?"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $profile['bio'] = $row['bio'] ?: 'Not set';
    $profile['professional_title'] = $row['professional_title'] ?: 'Not set';
    $profile['degrees'] = $row['degrees'] ?: 'Not set';
    $profile['experience'] = $row['experience'] ?: 'Not set';
    $profile['skills'] = $row['skills'] ?: 'Not set';
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Specialist Profile | CareerGuide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="profile_s.css">
</head>
<body>
  <!-- Dashboard header -->
  <header class="header">
    <div class="logo">CareerGuide</div>
    <nav class="nav">
      <a href="home_s.php">Home</a>
      <a href="assessment_s.php">Queries</a>
      <a href="profile_s.php" class="active">Specialist profile</a>
      <a href="login.php" id="logoutLink">Logout</a>
    </nav>
  </header>

  <div class="profile-wrapper">
    <header class="profile-header">
      <h1 class="profile-title">Specialist Profile</h1>
      <p class="profile-subtitle">Present your expertise and get ready to assess clients.</p>
    </header>

    <!-- Basic info -->
    <section class="profile-card">
      <h2 class="section-title">Basic information</h2>
      <div class="profile-row">
        <span class="label">Full name</span>
        <span class="value">
          <?php echo htmlspecialchars($user['full_name'] ?? ''); ?>
        </span>
      </div>
      <div class="profile-row">
        <span class="label">Email</span>
        <span class="value">
          <?php echo htmlspecialchars($user['email'] ?? ''); ?>
        </span>
      </div>
    </section>

    <!-- Professional info -->
    <section class="profile-card">
      <div class="profile-card-header">
        <h2 class="section-title">Professional details</h2>
        <button id="updateProfileBtn" class="primary-btn"
          onclick="window.location.href='update_s.php';">
          Update profile
        </button>
      </div>

      <div class="profile-row profile-row-multiline">
        <span class="label">Short bio</span>
        <span class="value" id="sBio">
          <?php echo nl2br(htmlspecialchars($profile['bio'])); ?>
        </span>
      </div>
      <div class="profile-row">
        <span class="label">Professional title</span>
        <span class="value" id="sTitle">
          <?php echo htmlspecialchars($profile['professional_title']); ?>
        </span>
      </div>
      <div class="profile-row profile-row-multiline">
        <span class="label">Degrees / certifications</span>
        <span class="value" id="sDegrees">
          <?php echo nl2br(htmlspecialchars($profile['degrees'])); ?>
        </span>
      </div>
      <div class="profile-row profile-row-multiline">
        <span class="label">Experience</span>
        <span class="value" id="sExperience">
          <?php echo nl2br(htmlspecialchars($profile['experience'])); ?>
        </span>
      </div>
      <div class="profile-row profile-row-multiline">
        <span class="label">Skills</span>
        <span class="value" id="sSkills">
          <?php echo nl2br(htmlspecialchars($profile['skills'])); ?>
        </span>
      </div>
    </section>

    <!-- Start assessing -->
    <section class="profile-card profile-card-center">
      <h2 class="section-title">Start assessing clients</h2>
      <p class="profile-subtitle small">
        When you are ready, move to the assessment workspace to review client questions.
      </p>
      <button id="startAssessingBtn" class="primary-btn large"
        onclick="window.location.href='assessment_s.php';">
        Start assessing
      </button>
    </section>
  </div>
</body>
</html>
