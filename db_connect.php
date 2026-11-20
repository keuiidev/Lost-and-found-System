<?php
// db_connect.php
// Connects the website to the MySQL database and starts the login session.

// Start the session (used for the admin login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- LOCAL (XAMPP) SETTINGS ----------
$host = "localhost";
$user = "root";
$pass = "";
$db   = "lost_and_found_db";

// ---------- INFINITYFREE SETTINGS ----------
//$host = "sql213.infinityfree.com";
//$user = "if0_43463745";
//$pass = "4cpK6jKzHG65R1";
//$db   = "if0_43463745_lost_and_found";

$conn = mysqli_connect($host, $user, $pass, $db);

// Stop the page if the connection failed
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
