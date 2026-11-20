<?php
// admin/claim.php
// UPDATE (admin only, quick action): marks an item as "Claimed" with one click.
include "../db_connect.php";
include "auth.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "UPDATE items SET status = 'Claimed' WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location: dashboard.php?msg=" . urlencode("Item marked as claimed."));
exit;
?>
