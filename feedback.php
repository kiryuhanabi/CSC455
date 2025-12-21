<?php
// feedback.php
require_once "connect.php";
session_start();

// Only specialists
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'specialist') {
    header("Location: login.php");
    exit;
}

$specialistId = (int)$_SESSION['user_id'];
$errorMessage = "";

// Get query id from GET or POST
$queryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["query_id"])) {
    $queryId = (int)$_POST["query_id"];
}

// If no id, go back
if ($queryId <= 0) {
    header("Location: assessment_s.php");
    exit;
}

// ----- Handle POST: save feedback -----
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $readiness = trim($_POST["readiness"] ?? "");
    $detail    = trim($_POST["detail"] ?? "");

    if ($readiness === "") {
        $errorMessage = "Please select a readiness level.";
    } else {
        $stmt = $mysqli->prepare(
            "UPDATE queries
             SET readiness = ?, detailed_feedback = ?, feedback_by = ?
             WHERE id = ?"
        );
        $stmt->bind_param("ssii", $readiness, $detail, $specialistId, $queryId);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: assessment_s.php");
            exit;
        } else {
            $errorMessage = "Error saving feedback: " . $stmt->error;
            $stmt->close();
        }
    }
}

// ----- Load query text & existing feedback -----
$stmt = $mysqli->prepare(
    "SELECT q.text, q.readiness, q.detailed_feedback, u.full_name AS specialist_name
     FROM queries q
     LEFT JOIN users u ON q.feedback_by = u.id
     WHERE q.id = ?"
);
$stmt->bind_param("i", $queryId);
$stmt->execute();
$res = $stmt->get_result();
$query = $res->fetch_assoc();
$stmt->close();

if (!$query) {
    header("Location: assessment_s.php");
    exit;
}

$queryText   = $query['text'];
$currentRead = $query['readiness'] ?: "Not assessed";
$currentDet  = $query['detailed_feedback'] ?? "";
$fbByName    = $query['specialist_name'] ?? ""; // optional display
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Provide Feedback | CareerGuide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="feedback.css">
</head>
<body>
  <div class="update-wrapper">
    <form class="update-card" id="feedbackForm" method="post" action="feedback.php">
      <h1 class="update-title">Provide feedback</h1>
      <p class="update-subtitle">
        Review the client query and share your readiness rating and detailed feedback.
      </p>

      <input type="hidden" name="query_id" value="<?php echo (int)$queryId; ?>">

      <div class="field read-only">
        <span>Query</span>
        <p id="fbQueryText" class="readonly-box">
          <?php echo nl2br(htmlspecialchars($queryText)); ?>
        </p>
      </div>

      <label class="field">
        <span>Feedback by</span>
        <input type="text"
               id="fbBy"
               value="<?php echo htmlspecialchars($fbByName); ?>"
               placeholder="Your name (specialist)"
               readonly>
      </label>

      <label class="field">
        <span>Readiness</span>
        <select id="fbReadiness" name="readiness">
          <?php
            $options = ["Not assessed", "Not ready", "Almost ready", "Ready"];
            foreach ($options as $opt):
          ?>
            <option value="<?php echo $opt; ?>"
              <?php if ($currentRead === $opt) echo "selected"; ?>>
              <?php echo $opt; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="field field-textarea">
        <span>Detailed feedback</span>
        <textarea
          id="fbDetail"
          name="detail"
          placeholder="Explain what the client is doing well and what to improve."
        ><?php echo htmlspecialchars($currentDet); ?></textarea>
      </label>

      <button type="submit" class="primary-btn">Save feedback</button>
      <?php if (!empty($errorMessage)): ?>
        <p class="error-message" id="errorMessage">
          <?php echo htmlspecialchars($errorMessage); ?>
        </p>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
