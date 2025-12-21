<?php
// assessment_s.php
require_once "connect.php";
session_start();

// Only logged-in specialists
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'specialist') {
    header("Location: login.php");
    exit;
}

$specialistId = (int)$_SESSION['user_id'];

// Load queries + client & specialist names
$queries = [];

$sql = "
    SELECT
        q.id,
        q.client_id,
        q.text,
        q.status,
        q.readiness,
        q.detailed_feedback,
        q.feedback_by,
        c.full_name AS client_name,
        s.full_name AS specialist_name
    FROM queries q
    JOIN users c ON q.client_id = c.id
    LEFT JOIN users s ON q.feedback_by = s.id
    ORDER BY q.id ASC
";

if ($res = $mysqli->query($sql)) {
    while ($row = $res->fetch_assoc()) {
        $queries[] = $row;
    }
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Specialist Assessment | CareerGuide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assessment_s.css">
</head>
<body>
  <!-- Specialist dashboard header -->
  <header class="header">
    <div class="logo">CareerGuide</div>
    <nav class="nav">
      <a href="home_s.php">Home</a>
      <a href="profile_s.php">Profile</a>
      <a href="assessment_s.php" class="active">Assessments</a>
      <a href="logout.php" id="logoutLink">Logout</a>
    </nav>
  </header>

  <main class="page">
    <section class="card">
      <header class="page-header">
        <h1 class="page-title">Client queries</h1>
        <p class="page-subtitle">
          Review client questions, mark readiness, and leave detailed feedback.
        </p>
      </header>

      <div class="table-wrapper">
        <table class="queries-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Client</th>
              <th>Feedback by</th>
              <th>Query</th>
              <th>Readiness</th>
              <th>Detailed feedback</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="assessmentBody">
            <?php if (empty($queries)): ?>
              <tr><td colspan="7">No client queries yet.</td></tr>
            <?php else: ?>
              <?php foreach ($queries as $q): ?>
                <tr>
                  <td><?php echo htmlspecialchars($q['id']); ?></td>
                  <td><?php echo htmlspecialchars($q['client_name']); ?></td>
                  <td><?php echo htmlspecialchars($q['specialist_name'] ?: '—'); ?></td>
                  <td><?php echo nl2br(htmlspecialchars($q['text'])); ?></td>
                  <td><?php echo htmlspecialchars($q['readiness'] ?: 'Not assessed'); ?></td>
                  <td><?php echo nl2br(htmlspecialchars($q['detailed_feedback'] ?? "")); ?></td>
                  <td>
                    <a href="feedback.php?id=<?php echo (int)$q['id']; ?>"
                       class="primary-btn small">
                      Feedback
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
