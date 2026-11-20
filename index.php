<?php
// index.php
// STARTING PAGE (landing page): welcome message, counts, and the latest items.
include "db_connect.php";
include "header.php";

// ----- Count items per status -----
$counts = ['Lost' => 0, 'Found' => 0, 'Claimed' => 0];
$count_result = mysqli_query($conn, "SELECT status, COUNT(*) AS total FROM items GROUP BY status");
while ($c = mysqli_fetch_assoc($count_result)) {
    $counts[$c['status']] = $c['total'];
}

// ----- Latest 3 items that are not yet claimed -----
$recent = mysqli_query($conn, "SELECT * FROM items WHERE status <> 'Claimed' ORDER BY created_at DESC, id DESC LIMIT 3");

// Colors for the status badges
$badge = ['Lost' => 'danger', 'Found' => 'success', 'Claimed' => 'secondary'];
?>

<!-- Hero section -->
<div class="bg-white rounded shadow-sm p-4 p-md-5 text-center mb-4">
    <h1 class="display-5 fw-bold">Lost something? Found something?</h1>
    <p class="lead text-muted mb-4">
        Report a lost or found item, browse what others have reported, and help belongings find their way back to their owners.
    </p>
    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
        <a href="items.php" class="btn btn-primary btn-lg px-4">Browse Items</a>
        <a href="add.php" class="btn btn-success btn-lg px-4">Report an Item</a>
    </div>
</div>

<!-- Summary cards -->
<div class="row text-center mb-4">
    <?php foreach ($counts as $label => $total): ?>
        <div class="col-4">
            <div class="card shadow-sm border-<?php echo $badge[$label]; ?>">
                <div class="card-body py-3">
                    <h3 class="mb-0 text-<?php echo $badge[$label]; ?>"><?php echo $total; ?></h3>
                    <small class="text-muted"><?php echo $label; ?></small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- How it works -->
<h4 class="mb-3">How it works</h4>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">1. Report</h5>
                <p class="card-text text-muted mb-0">Fill in the form with the item details, where and when it was lost or found, and add a photo if you have one.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">2. Search</h5>
                <p class="card-text text-muted mb-0">Browse the list or search by item name, location, or description to see if your item has been reported.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">3. Claim</h5>
                <p class="card-text text-muted mb-0">Contact the person listed on the item. Once it is returned, the admin marks it as claimed.</p>
            </div>
        </div>
    </div>
</div>

<!-- Latest items -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Latest reports</h4>
    <a href="items.php" class="btn btn-outline-primary btn-sm">View all</a>
</div>
<div class="row g-3">
    <?php if (mysqli_num_rows($recent) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($recent)): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($row['photo'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" class="card-img-top"
                             alt="Item photo" style="height:180px;object-fit:cover;">
                    <?php else: ?>
                        <div class="bg-secondary-subtle text-muted d-flex align-items-center justify-content-center"
                             style="height:180px;">No photo</div>
                    <?php endif; ?>
                    <div class="card-body">
                        <span class="badge bg-<?php echo $badge[$row['status']]; ?> mb-2"><?php echo $row['status']; ?></span>
                        <h5 class="card-title"><?php echo htmlspecialchars($row['item_name']); ?></h5>
                        <p class="card-text text-muted small mb-1">
                            <?php echo htmlspecialchars($row['location']); ?> &middot;
                            <?php echo date("M d, Y", strtotime($row['date_reported'])); ?>
                        </p>
                        <p class="card-text small mb-0">
                            Contact: <?php echo htmlspecialchars($row['contact_name']); ?>
                            (<?php echo htmlspecialchars($row['contact_number']); ?>)
                        </p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-light border text-center">No items reported yet. Be the first to report one!</div>
        </div>
    <?php endif; ?>
</div>

<?php include "footer.php"; ?>
