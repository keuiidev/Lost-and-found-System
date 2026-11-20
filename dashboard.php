<?php
// admin/dashboard.php
// ADMIN DASHBOARD (SELECT): full list with Edit / Claimed / Delete actions.
include "../db_connect.php";
include "auth.php";

// ----- Get search / filter values from the URL -----
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';

// ----- Build the SELECT query -----
$sql    = "SELECT * FROM items WHERE 1=1";
$types  = "";
$params = [];

if ($search !== "") {
    $sql .= " AND (item_name LIKE ? OR location LIKE ? OR description LIKE ? OR found_by LIKE ?)";
    $like = "%" . $search . "%";
    $types .= "ssss";
    array_push($params, $like, $like, $like, $like);
}
if ($status !== "") {
    $sql .= " AND status = ?";
    $types .= "s";
    $params[] = $status;
}
$sql .= " ORDER BY date_reported DESC, id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($types !== "") {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ----- Count items per status for the summary cards -----
$counts = ['Lost' => 0, 'Found' => 0, 'Claimed' => 0];
$count_result = mysqli_query($conn, "SELECT status, COUNT(*) AS total FROM items GROUP BY status");
while ($c = mysqli_fetch_assoc($count_result)) {
    $counts[$c['status']] = $c['total'];
}

// Colors for the status badges
$badge = ['Lost' => 'danger', 'Found' => 'success', 'Claimed' => 'secondary'];

$base = "../";
include "../header.php";
?>

<!-- Message after edit / claim / delete -->
<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo htmlspecialchars($_GET['msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h3 class="mb-0">Admin Dashboard</h3>
        <small class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></small>
    </div>
    <a href="change_password.php" class="btn btn-outline-secondary btn-sm">Change Password</a>
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

<!-- Search and filter form -->
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="text" name="search" class="form-control"
               placeholder="Search item, location, description, or finder..."
               value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <?php foreach (['Lost', 'Found', 'Claimed'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php if ($status === $s) echo "selected"; ?>>
                    <?php echo $s; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-grid d-md-flex gap-2">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="dashboard.php" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<!-- Items table -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Manage Items</strong>
        <a href="../add.php" class="btn btn-success btn-sm">+ Add Item</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Contact</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($row['photo'])): ?>
                                    <a href="../uploads/<?php echo htmlspecialchars($row['photo']); ?>" target="_blank">
                                        <img src="../uploads/<?php echo htmlspecialchars($row['photo']); ?>"
                                             alt="Item photo" class="rounded"
                                             style="width:56px;height:56px;object-fit:cover;">
                                    </a>
                                <?php else: ?>
                                    <div class="rounded bg-secondary-subtle text-muted d-flex align-items-center justify-content-center"
                                         style="width:56px;height:56px;font-size:11px;">No photo</div>
                                <?php endif; ?>
                                <div>
                                    <strong><?php echo htmlspecialchars($row['item_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['description'] ?? ''); ?></small>
                                    <?php if (!empty($row['found_by'])): ?>
                                        <br><small class="text-success">Found by: <?php echo htmlspecialchars($row['found_by']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo date("M d, Y", strtotime($row['date_reported'])); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $badge[$row['status']]; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['contact_name']); ?><br>
                            <small class="text-muted"><?php echo htmlspecialchars($row['contact_number']); ?></small>
                        </td>
                        <td class="text-end text-nowrap">
                            <?php if ($row['status'] !== 'Claimed'): ?>
                                <a href="claim.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success"
                                   onclick="return confirm('Mark this item as claimed?');">Claimed</a>
                            <?php endif; ?>
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No items found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../footer.php"; ?>
