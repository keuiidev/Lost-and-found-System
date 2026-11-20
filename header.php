<?php
// header.php
// Shared top part of every page: HTML head + Bootstrap navbar.
// $base is "" for normal pages and "../" for pages inside the admin folder.
$base     = $base ?? "";
$is_admin = isset($_SESSION['admin_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lost and Found System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo $base; ?>index.php">Lost &amp; Found</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>items.php">Browse Items</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>add.php">Report an Item</a></li>
                <?php if ($is_admin): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>admin/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>admin/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>admin/login.php">Admin Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
