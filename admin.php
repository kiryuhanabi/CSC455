<?php
// admin.php
require_once "connect.php";
session_start();

// Optional: only admins
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header("Location: login.php");
//     exit;
// }

// ---------- Handle deletions ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_type'], $_POST['row_id'])) {
    $type  = $_POST['delete_type'];
    $id    = (int)$_POST['row_id'];

    if ($type === 'user') {
        $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    } elseif ($type === 'query') {
        $stmt = $mysqli->prepare("DELETE FROM queries WHERE id = ?");
    } elseif ($type === 'program') {
        $stmt = $mysqli->prepare("DELETE FROM programs WHERE id = ?");
    } else {
        $stmt = null;
    }

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    // Avoid resubmission on refresh
    header("Location: admin.php");
    exit;
}

// ---------- Load users ----------
$users = [];
$usersResult = $mysqli->query("SELECT id, full_name, email, role FROM users ORDER BY id ASC");
if ($usersResult) {
    while ($row = $usersResult->fetch_assoc()) {
        $users[] = $row;
    }
    $usersResult->free();
}

// ---------- Load queries ----------
$queries = [];
$qSql = "
  SELECT
    q.id,
    q.text,
    q.readiness,
    q.detailed_feedback,
    c.full_name AS client_name,
    c.id AS client_id
  FROM queries q
  JOIN users c ON q.client_id = c.id
  ORDER BY q.id ASC
";
$qRes = $mysqli->query($qSql);
if ($qRes) {
    while ($row = $qRes->fetch_assoc()) {
        $queries[] = $row;
    }
    $qRes->free();
}

// ---------- Load programs ----------
$programs = [];
$pRes = $mysqli->query("SELECT id, title, owner, duration, description FROM programs ORDER BY id ASC");
if ($pRes) {
    while ($row = $pRes->fetch_assoc()) {
        $programs[] = $row;
    }
    $pRes->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | CareerGuide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <header class="header">
    <div class="logo">CareerGuide</div>
    <nav class="nav">
      <a href="admin.php" class="active">Admin profile</a>
      <a href="login.php" id="logoutLink">Logout</a>
    </nav>
  </header>

  <main class="page">

    <!-- Users -->
    <section class="card">
      <header class="section-header">
        <h2 class="section-title">Users</h2>
        <button id="addUserBtn" class="primary-btn small" type="button">Add user</button>
      </header>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="usersBody">
            <?php if (empty($users)): ?>
              <tr><td colspan="5">No users found.</td></tr>
            <?php else: ?>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td><?php echo htmlspecialchars($u['id']); ?></td>
                  <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($u['email']); ?></td>
                  <td><?php echo htmlspecialchars($u['role']); ?></td>
                  <td>
                    <form method="post" action="admin.php" style="display:inline;">
                      <input type="hidden" name="delete_type" value="user">
                      <input type="hidden" name="row_id" value="<?php echo (int)$u['id']; ?>">
                      <button class="secondary-btn" type="submit"
                        onclick="return confirm('Remove this user?');">
                        Remove
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Queries -->
    <section class="card">
      <header class="section-header">
        <h2 class="section-title">Queries</h2>
      </header>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Client</th>
              <th>Query</th>
              <th>Readiness</th>
              <th>Detailed feedback</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="queriesBody">
            <?php if (empty($queries)): ?>
              <tr><td colspan="6">No queries found.</td></tr>
            <?php else: ?>
              <?php foreach ($queries as $q): ?>
                <tr>
                  <td><?php echo htmlspecialchars($q['id']); ?></td>
                  <td>
                    <a href="profile_c.php?id=<?php echo (int)$q['client_id']; ?>" class="link">
                      <?php echo htmlspecialchars($q['client_name']); ?>
                    </a>
                  </td>
                  <td><?php echo nl2br(htmlspecialchars($q['text'])); ?></td>
                  <td><?php echo htmlspecialchars($q['readiness']); ?></td>
                  <td><?php echo nl2br(htmlspecialchars($q['detailed_feedback'] ?? '')); ?></td>
                  <td>
                    <form method="post" action="admin.php" style="display:inline;">
                      <input type="hidden" name="delete_type" value="query">
                      <input type="hidden" name="row_id" value="<?php echo (int)$q['id']; ?>">
                      <button class="secondary-btn" type="submit"
                        onclick="return confirm('Remove this query?');">
                        Remove
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Mentorship programs -->
    <section class="card">
      <header class="section-header">
        <h2 class="section-title">Mentorship programs</h2>
        <button id="addProgramBtn" class="primary-btn small" type="button"
          onclick="window.location.href='program.php'">
          Add program
        </button>
      </header>
      <div id="programsList" class="programs-list">
        <?php if (empty($programs)): ?>
          <p class="page-subtitle">No mentorship programs yet.</p>
        <?php else: ?>
          <?php foreach ($programs as $p): ?>
            <div class="program-card">
              <div class="program-title-row">
                <div class="program-title">
                  <?php echo htmlspecialchars($p['title']); ?>
                </div>
                <form method="post" action="admin.php" style="margin-left:auto;">
                  <input type="hidden" name="delete_type" value="program">
                  <input type="hidden" name="row_id" value="<?php echo (int)$p['id']; ?>">
                  <button class="secondary-btn small" type="submit"
                    onclick="return confirm('Remove this program?');">
                    Remove
                  </button>
                </form>
              </div>
              <?php if (!empty($p['owner'])): ?>
                <div class="program-meta"><?php echo htmlspecialchars($p['owner']); ?></div>
              <?php endif; ?>
              <?php if (!empty($p['duration'])): ?>
                <div class="program-meta">Duration: <?php echo htmlspecialchars($p['duration']); ?></div>
              <?php endif; ?>
              <div class="program-desc">
                <?php echo nl2br(htmlspecialchars($p['description'])); ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

  </main>
</body>
</html>
