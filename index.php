<?php
// index.php at the root of your project (e.g. htdocs/careerguide)
session_start();

/*
  Assumes:
  - Login code sets $_SESSION['user_id'] and $_SESSION['role'] = 'client' or 'specialist' or 'admin'.
  - You already have: home.php (public/client entry), home_s.php, admin.php, login.php.
*/

// Not logged in: send to public home (or login if you prefer)
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

$role = $_SESSION['role'] ?? 'client';

if ($role === 'client') {
    header("Location: home_c.php");   // or profile_c.php / assessment_c.php
    exit;
}

if ($role === 'specialist') {
    header("Location: home_s.php");
    exit;
}

if ($role === 'admin') {
    header("Location: admin.php");
    exit;
}

// Fallback
header("Location: home.php");
exit;

