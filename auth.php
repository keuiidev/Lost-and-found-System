<?php
// admin/auth.php
// Include this at the top of every admin page.
// If the visitor is not logged in, send them to the login page.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
