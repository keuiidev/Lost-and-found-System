<?php
// admin/delete.php
// DELETE (admin only): removes a record and its photo file.
include "../db_connect.php";
include "auth.php";
include "../upload_helper.php";

$id = (int)($_GET['id'] ?? 0);

// Get the photo name first so the file can be removed too
$stmt = mysqli_prepare($conn, "SELECT photo FROM items WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$stmt = mysqli_prepare($conn, "DELETE FROM items WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

if ($row) {
    delete_photo($row['photo']);
}

header("Location: dashboard.php?msg=" . urlencode("Record deleted."));
exit;
?>
