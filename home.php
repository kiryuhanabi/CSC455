<?php
// index.php – minimal safe homepage

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;

if ($isLoggedIn) {
    if ($role === 'client') {
        $assessmentLink = 'assessment_c.php';
        $profileLink    = 'profile_c.php';
        $homeLink       = 'home_c.php';
    } elseif ($role === 'specialist') {
        $assessmentLink = 'assessment_s.php';
        $profileLink    = 'profile_s.php';
        $homeLink       = 'home_s.php';
    } else {
        $assessmentLink = '#';
        $profileLink    = '#';
        $homeLink       = 'index.php';
    }
    $authLink = 'logout.php';
    $authText = 'Logout';
} else {
    $assessmentLink = 'login.php';
    $profileLink    = 'login.php';
    $homeLink       = 'index.php';
    $authLink       = 'login.php';
    $authText       = 'Login';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CareerGuide</title>
  <link rel="stylesheet" href="home.css">
</head>
<body>
  <header class="header">
    <div class="logo">CareerGuide</div>
    <nav class="nav">
      <a href="<?php echo htmlspecialchars($homeLink); ?>">Home</a>
      <a href="<?php echo htmlspecialchars($assessmentLink); ?>">Assessment</a>
      <a href="mentorship.php">Mentorship program</a>
      <a href="<?php echo htmlspecialchars($profileLink); ?>">Profile</a>
      <a href="<?php echo htmlspecialchars($authLink); ?>">
        <?php echo htmlspecialchars($authText); ?>
      </a>
    </nav>
  </header>

  <main class="hero">
    <section>
      <h1 class="hero-title">Shape your career future with clarity.</h1>
      <p class="hero-subtitle">
        A focused platform for students and graduates to assess readiness,
        calibrate skills, and connect to specialists who understand real-world hiring.
      </p>
      <div class="hero-cta">
        <button class="btn-primary"
                onclick="window.location.href='<?php echo htmlspecialchars($assessmentLink); ?>';">
          Start assessment
        </button>
        <button class="btn-secondary"
                onclick="window.location.href='<?php echo htmlspecialchars($profileLink); ?>';">
          View profile
        </button>
      </div>
      <p class="hero-meta">No credit card. No spam. Just structured guidance.</p>

      <div class="steps">
        <div class="step">
          <div class="step-number">01 · Profile</div>
          <div class="step-title">Profile calibration</div>
          <p class="step-text">
            Capture your academic year, skills, and experience to build a realistic baseline.
          </p>
        </div>
        <div class="step">
          <div class="step-number">02 · Assessment</div>
          <div class="step-title">Readiness check</div>
          <p class="step-text">
            Take curated questions to benchmark your domain knowledge and soft skills.
          </p>
        </div>
        <div class="step">
          <div class="step-number">03 · Feedback</div>
          <div class="step-title">Specialist review</div>
          <p class="step-text">
            Receive targeted feedback and next steps from verified industry specialists.
          </p>
        </div>
      </div>
    </section>

    <aside class="hero-panel">
      <h2 class="hero-panel-title">Live readiness snapshot</h2>
      <p class="hero-panel-text">
        Track performance across communication, problem solving, and technical depth
        as you complete assessments and apply expert feedback.
      </p>
    </aside>
  </main>

  <footer class="footer">
    <div>© 2025 CareerGuide. All rights reserved.</div>
    <div class="footer-links">
      <a href="#">Privacy</a>
      <a href="#">Terms</a>
      <a href="#">Support</a>
    </div>
  </footer>
  <script src="script.js"></script>
</body>
</html>
