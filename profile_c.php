<?php
// profile_c.php
require_once "connect.php";
session_start();

// Basic access control: only logged-in clients
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];

// ---- Load basic user info ----
$user = null;
$stmt = $mysqli->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $user = $row;
}
$stmt->close();

// ---- Load academic profile (if exists) ----
$profile = [
    'university' => 'Not set',
    'major' => 'Not set',
    'minor' => 'Not set',
    'cgpa' => 'Not set',
    'expected_grad' => 'Not set',
    'technical_skills' => 'Not set',
    'achievements' => 'Not set',
    'certifications' => 'Not set',
];

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
    $profile['university'] = $row['university'] ?: 'Not set';
    $profile['major'] = $row['major'] ?: 'Not set';
    $profile['minor'] = $row['minor'] ?: 'Not set';
    $profile['cgpa'] = $row['cgpa'] ?: 'Not set';
    $profile['expected_grad'] = $row['expected_grad'] ?: 'Not set';
    $profile['technical_skills'] = $row['technical_skills'] ?: 'Not set';
    $profile['achievements'] = $row['achievements'] ?: 'Not set';
    $profile['certifications'] = $row['certifications'] ?: 'Not set';
}
$stmt->close();

// ---- Load this client's queries ----
$queries = [];
$stmt = $mysqli->prepare(
    "SELECT id, text, readiness, detailed_feedback, feedback_by
     FROM queries
     WHERE client_id = ?
     ORDER BY id ASC"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $queries[] = $row;
}
$stmt->close();

// Map specialist IDs to names (for feedback_by)
$specialistNames = [];
if (!empty($queries)) {
    $ids = [];
    foreach ($queries as $q) {
        if (!empty($q['feedback_by'])) {
            $ids[] = (int)$q['feedback_by'];
        }
    }
    $ids = array_unique($ids);
    if (!empty($ids)) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $sql = "SELECT id, full_name FROM users WHERE id IN ($in)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $specialistNames[(int)$row['id']] = $row['full_name'];
        }
        $stmt->close();
    }
}

// ---- Load applied mentorship programs ----
// ---- Load mentorship programs directly from programs table ----
// ---- Load applied mentorship programs for this client ----
$appliedPrograms = [];

$sql = "SELECT p.title, p.owner, p.duration
        FROM program_applications pa
        JOIN programs p ON pa.program_id = p.id
        WHERE pa.client_id = ?
        ORDER BY pa.applied_at DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $appliedPrograms[] = $row;
}
$stmt->close();


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Client Profile | CareerGuide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="profile_c.css">
</head>
<body>
  <!-- Dashboard header -->
  <header class="header">
    <div class="logo">CareerGuide</div>
    <nav class="nav">
      <a href="home.php">Home</a>
      <a href="assessment_c.php">Assessment</a>
      <a href="mentorship.php">Mentorship program</a>
      <a href="profile_c.php" class="active">Profile</a>
      <a href="login.php" id="logoutLink">Logout</a>
    </nav>
  </header>

  <div class="profile-wrapper">
    <header class="profile-header">
      <h1 class="profile-title">Client Profile</h1>
      <p class="profile-subtitle">View your details and question readiness.</p>
    </header>

    <!-- Basic information -->
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

    <!-- Academic & skills -->
    <section class="profile-card">
      <div class="profile-card-header">
        <h2 class="section-title">Academic & skills</h2>
        <button id="updateProfileBtn" class="primary-btn"
          onclick="window.location.href='update_c.php';">
          Update profile
        </button>
      </div>

      <div class="profile-row">
        <span class="label">University</span>
        <span class="value"><?php echo htmlspecialchars($profile['university']); ?></span>
      </div>
      <div class="profile-row">
        <span class="label">Major</span>
        <span class="value"><?php echo htmlspecialchars($profile['major']); ?></span>
      </div>
      <div class="profile-row">
        <span class="label">Minor</span>
        <span class="value"><?php echo htmlspecialchars($profile['minor']); ?></span>
      </div>
      <div class="profile-row">
        <span class="label">CGPA</span>
        <span class="value"><?php echo htmlspecialchars($profile['cgpa']); ?></span>
      </div>
      <div class="profile-row">
        <span class="label">Expected graduation</span>
        <span class="value">
          <?php echo htmlspecialchars($profile['expected_grad']); ?>
        </span>
      </div>
      <div class="profile-row profile-row-multiline">
        <span class="label">Technical skills</span>
        <span class="value">
          <?php echo nl2br(htmlspecialchars($profile['technical_skills'])); ?>
        </span>
      </div>
      <div class="profile-row profile-row-multiline">
        <span class="label">Achievements / awards</span>
        <span class="value">
          <?php echo nl2br(htmlspecialchars($profile['achievements'])); ?>
        </span>
      </div>
      <div class="profile-row profile-row-multiline">
        <span class="label">Certifications</span>
        <span class="value">
          <?php echo nl2br(htmlspecialchars($profile['certifications'])); ?>
        </span>
      </div>
    </section>

    <!-- Questions table -->
    <section class="profile-card">
      <h2 class="section-title">Your questions</h2>
      <div class="table-wrapper">
        <table class="questions-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Query</th>
              <th>Readiness</th>
              <th>Feedback by</th>
              <th>Detailed feedback</th>
            </tr>
          </thead>
          <tbody id="questionsBody">
            <?php if (empty($queries)): ?>
              <tr><td colspan="5">You have not submitted any queries yet.</td></tr>
            <?php else: ?>
              <?php foreach ($queries as $q): ?>
                <tr>
                  <td><?php echo htmlspecialchars($q['id']); ?></td>
                  <td><?php echo nl2br(htmlspecialchars($q['text'])); ?></td>
                  <td><?php echo htmlspecialchars($q['readiness']); ?></td>
                  <td>
                    <?php
                      if (!empty($q['feedback_by']) && isset($specialistNames[(int)$q['feedback_by']])) {
                        echo htmlspecialchars($specialistNames[(int)$q['feedback_by']]);
                      } else {
                        echo "—";
                      }
                    ?>
                  </td>
                  <td><?php echo nl2br(htmlspecialchars($q['detailed_feedback'] ?? "")); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Applied mentorship programs -->
    <section class="profile-card">
      <h2 class="section-title">Applied mentorship programs</h2>
      <div class="table-wrapper">
        <table class="questions-table">
          <thead>
            <tr>
              <th>Program</th>
              <th>Mentor / owner</th>
              <th>Duration</th>
            </tr>
          </thead>
          <tbody id="appliedProgramsBody">
            <?php if (empty($appliedPrograms)): ?>
              <tr><td colspan="3">No mentorship programs available yet.</td></tr>
            <?php else: ?>
              <?php foreach ($appliedPrograms as $p): ?>
                <tr>
                  <td><?php echo htmlspecialchars($p['title']); ?></td>
                  <td><?php echo htmlspecialchars($p['owner'] ?? "—"); ?></td>
                  <td><?php echo htmlspecialchars($p['duration'] ?? "—"); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</body>
</html>
