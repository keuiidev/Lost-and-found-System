<?php
// admin/edit.php
// UPDATE (admin only): edit an existing record, including found by and photo.
include "../db_connect.php";
include "auth.php";
include "../upload_helper.php";

// Get the record id from the URL
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$error = "";

// Load the existing record
$stmt = mysqli_prepare($conn, "SELECT * FROM items WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// If the record does not exist, go back to the dashboard
if (!$item) {
    header("Location: dashboard.php?msg=" . urlencode("Record not found."));
    exit;
}

$old_photo = $item['photo']; // current photo file name (may be empty)

// Runs only when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name      = trim($_POST['item_name']);
    $category       = trim($_POST['category']);
    $description    = trim($_POST['description']);
    $location       = trim($_POST['location']);
    $date_reported  = $_POST['date_reported'];
    $status         = $_POST['status'];
    $contact_name   = trim($_POST['contact_name']);
    $contact_number = trim($_POST['contact_number']);
    $found_by       = trim($_POST['found_by']);

    if ($item_name === "" || $location === "" || $contact_name === "" || $contact_number === "" || $date_reported === "") {
        $error = "Please fill in all required fields.";
    } else {
        // Try to save a new photo (null = keep the old one)
        $new_photo = save_photo($_FILES['photo'] ?? null, $error);

        if ($new_photo !== false) {
            $photo = ($new_photo === null) ? $old_photo : $new_photo;
            if ($found_by === "") {
                $found_by = null;
            }

            $sql = "UPDATE items SET item_name=?, category=?, description=?, location=?, date_reported=?,
                    status=?, contact_name=?, contact_number=?, found_by=?, photo=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssssssssi", $item_name, $category, $description, $location, $date_reported, $status, $contact_name, $contact_number, $found_by, $photo, $id);

            if (mysqli_stmt_execute($stmt)) {
                // A new photo replaced the old one, so delete the old file
                if ($new_photo !== null) {
                    delete_photo($old_photo);
                }
                header("Location: dashboard.php?msg=" . urlencode("Item updated successfully!"));
                exit;
            } else {
                if ($new_photo !== null) {
                    delete_photo($new_photo);
                }
                $error = "Something went wrong. Please try again.";
            }
        }
    }
    // Keep the typed values in the form if there was an error
    $item = $_POST;
    $item['photo'] = $old_photo;
}

$categories = ['Bag', 'Phone', 'ID', 'Keys', 'Wallet', 'Clothing', 'Bottle', 'Others'];
$statuses   = ['Lost', 'Found', 'Claimed'];

$base = "../";
include "../header.php";
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning"><strong>Edit Item #<?php echo $id; ?></strong></div>
            <div class="card-body">

                <?php if ($error !== ""): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Item Name *</label>
                            <input type="text" name="item_name" class="form-control" required
                                   value="<?php echo htmlspecialchars($item['item_name']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category" class="form-select">
                                <?php foreach ($categories as $c): ?>
                                    <option <?php if ($item['category'] === $c) echo "selected"; ?>><?php echo $c; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location *</label>
                            <input type="text" name="location" class="form-control" required
                                   value="<?php echo htmlspecialchars($item['location']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date *</label>
                            <input type="date" name="date_reported" class="form-control" required
                                   value="<?php echo htmlspecialchars($item['date_reported']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status *</label>
                            <select name="status" class="form-select">
                                <?php foreach ($statuses as $s): ?>
                                    <option <?php if ($item['status'] === $s) echo "selected"; ?>><?php echo $s; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Name *</label>
                            <input type="text" name="contact_name" class="form-control" required
                                   value="<?php echo htmlspecialchars($item['contact_name']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number *</label>
                            <input type="text" name="contact_number" class="form-control" required
                                   value="<?php echo htmlspecialchars($item['contact_number']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Found By <small class="text-muted">(optional)</small></label>
                            <input type="text" name="found_by" class="form-control"
                                   value="<?php echo htmlspecialchars($item['found_by'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Item Photo <small class="text-muted">(choose a file to replace)</small></label>
                            <?php if (!empty($item['photo'])): ?>
                                <div class="mb-2">
                                    <img src="../uploads/<?php echo htmlspecialchars($item['photo']); ?>"
                                         alt="Current photo" class="img-thumbnail" style="max-height:100px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-warning">Update</button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "../footer.php"; ?>
