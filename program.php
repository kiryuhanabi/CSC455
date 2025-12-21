<?php
// program.php
require_once "connect.php";

$errors = [];
$success = "";
$programId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $programId > 0;

$title = "";
$owner = "";
$duration = "";
$description = "";

// If editing, load existing program
if ($editing) {
    $stmt = $mysqli->prepare("SELECT id, title, owner, duration, description FROM programs WHERE id = ?");
    $stmt->bind_param("i", $programId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $title = $row['title'];
        $owner = $row['owner'];
        $duration = $row['duration'];
        $description = $row['description'];
    } else {
        $errors[] = "Program not found.";
        $editing = false; // fall back to add mode
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["pTitle"] ?? "");
    $owner = trim($_POST["pOwner"] ?? "");
    $duration = trim($_POST["pDuration"] ?? "");
    $description = trim($_POST["pDescription"] ?? "");

    if ($title === "") {
        $errors[] = "Program title is required.";
    }
    if ($description === "") {
        $errors[] = "Detailed description is required.";
    }

    if (empty($errors)) {
        if ($editing) {
            // Update existing program
            $stmt = $mysqli->prepare(
                "UPDATE programs
                 SET title = ?, owner = ?, duration = ?, description = ?
                 WHERE id = ?"
            );
            $stmt->bind_param("ssssi", $title, $owner, $duration, $description, $programId);
            if ($stmt->execute()) {
                header("Location: admin.php");
                exit;
            } else {
                $errors[] = "Error updating program: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Insert new program
            $stmt = $mysqli->prepare(
                "INSERT INTO programs (title, owner, duration, description)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $title, $owner, $duration, $description);
            if ($stmt->execute()) {
                header("Location: admin.php");
                exit;
            } else {
                $errors[] = "Error saving program: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mentorship Program | CareerGuide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="program.css">
</head>
<body>
  <div class="update-wrapper">
    <form class="update-card" id="programForm" method="post" action="">
      <h1 class="update-title" id="programTitleHeading">
        <?php echo $editing ? "Edit mentorship program" : "Add mentorship program"; ?>
      </h1>
      <p class="update-subtitle">
        Describe the program so clients can understand who it is for and what they'll get.
      </p>

      <?php if (!empty($errors)): ?>
        <div class="error-message" style="margin-bottom:8px;">
          <?php foreach ($errors as $e): ?>
            <div><?php echo htmlspecialchars($e); ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label class="field">
        <span>Program title</span>
        <input
          type="text"
          name="pTitle"
          id="pTitle"
          placeholder="e.g. Data Science Job Readiness Bootcamp"
          value="<?php echo htmlspecialchars($title); ?>"
        >
      </label>

      <label class="field">
        <span>Program owner / mentor</span>
        <input
          type="text"
          name="pOwner"
          id="pOwner"
          placeholder="Name of lead mentor or team"
          value="<?php echo htmlspecialchars($owner); ?>"
        >
      </label>

      <label class="field">
        <span>Duration</span>
        <input
          type="text"
          name="pDuration"
          id="pDuration"
          placeholder="e.g. 8 weeks, 3 months"
          value="<?php echo htmlspecialchars($duration); ?>"
        >
      </label>

      <label class="field field-textarea">
        <span>Detailed description</span>
        <textarea
          name="pDescription"
          id="pDescription"
          placeholder="Outline structure, expectations, outcomes, and who this program is best for."
        ><?php echo htmlspecialchars($description); ?></textarea>
      </label>

      <button type="submit" class="primary-btn">Save program</button>
    </form>
  </div>
</body>
</html>
