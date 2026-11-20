<?php
// admin/logout.php
// Ends the admin session and returns to the home page.
session_start();
$_SESSION = [];
session_destroy();
header("Location: ../index.php");
exit;
?>
