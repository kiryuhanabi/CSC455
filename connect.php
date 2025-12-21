<?php
// connect.php
$host = "localhost";
$db   = "careerdb";
$user = "root";   // change for your setup
$pass = "";       // change for your setup

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}
?>
