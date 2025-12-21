<?php
// index.php — always start at login page

// Show errors temporarily while debugging (optional; remove later)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Location: login.php");
exit;
