<?php
require_once "connect.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$applyMessage = "";

// Handle application submit
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["program_id"])) {
    $programId = (int)$_POST["program_id"];

    // Insert application (unique per client+program)
    $stmt = $mysqli->prepare(
        "INSERT IGNORE INTO program_applications (client_id, program_id)
         VALUES (?, ?)"
    );
    $stmt->bind_param("ii", $userId, $programId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $applyMessage = "You have successfully applied to this program.";
        } else {
            $applyMessage = "You already applied to this program.";
        }
    } else {
        $applyMessage = "Error applying to program: " . $stmt->error;
    }
    $stmt->close();
}

// Load programs
$programs = [];
$sql = "SELECT id, title, owner, duration, description, created_at
        FROM programs
        ORDER BY created_at DESC";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $programs[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mentorship Programs | CareerGuide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="mentorship.css">
</head>
<body>
  <header class="header">
    <div class="logo">CareerGuide</div>
    <nav class="nav">
      <a href="home.php">Home</a>
      <a href="profile_c.php">Profile</a>
      <a href="assessment_c.php">Assessment</a>
      <a href="mentorship.php" class="active">Mentorship program</a>
      <a href="login.php" id="logoutLink">Logout</a>
    </nav>
  </header>

  <main class="page">
    <section class="card">
      <header class="page-header">
        <h1 class="page-title">Mentorship programs</h1>
        <p class="page-subtitle">
          Explore programs curated by the admin and apply to the ones that fit your goals.
        </p>
        <?php if (!empty($applyMessage)): ?>
          <p class="info-message"><?php echo htmlspecialchars($applyMessage); ?></p>
        <?php endif; ?>
      </header>

      <div id="programsList" class="programs-list">
        <?php if (empty($programs)): ?>
          <p>No mentorship programs are available at the moment.</p>
        <?php else: ?>
          <?php foreach ($programs as $program): ?>
            <article class="program-card">
              <h2 class="program-title">
                <?php echo htmlspecialchars($program['title']); ?>
              </h2>

              <p class="program-meta">
                <span class="tag">Owner: <?php echo htmlspecialchars($program['owner']); ?></span>
                <span class="tag">Duration: <?php echo htmlspecialchars($program['duration']); ?></span>
              </p>

              <p class="program-description">
                <?php echo nl2br(htmlspecialchars($program['description'])); ?>
              </p>

              <form method="post" action="mentorship.php">
                <input type="hidden" name="program_id"
                       value="<?php echo (int)$program['id']; ?>">
                <button class="primary-btn small" type="submit">
                  Apply to this program
                </button>
              </form>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>
</body>
</html>
