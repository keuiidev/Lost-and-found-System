<?php
// add.php
// INSERT: public form to report a lost or found item (with optional photo).
include "db_connect.php";
include "upload_helper.php";

$error = "";

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

    // The public form can only report Lost or Found items
    if ($status !== 'Lost' && $status !== 'Found') {
        $status = 'Lost';
    }

    // Basic validation
    if ($item_name === "" || $location === "" || $contact_name === "" || $contact_number === "" || $date_reported === "") {
        $error = "Please fill in all required fields.";
    } else {
        // Save the photo first (null = no photo, false = failed)
        $photo = save_photo($_FILES['photo'] ?? null, $error);

        if ($photo !== false) {
            // Empty "found by" is stored as NULL
            if ($found_by === "") {
                $found_by = null;
            }

            // Prepared statement (protects against SQL injection)
            $sql = "INSERT INTO items (item_name, category, description, location, date_reported, status, contact_name, contact_number, found_by, photo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssssssss", $item_name, $category, $description, $location, $date_reported, $status, $contact_name, $contact_number, $found_by, $photo);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: items.php?msg=" . urlencode("Item reported successfully!"));
                exit;
            } else {
                delete_photo($photo); // remove the uploaded file if saving failed
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

include "header.php";
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white"><strong>Report an Item</strong></div>
            <div class="card-body">

                <?php if ($error !== ""): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- enctype is required for file uploads -->
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Item Name *</label>
                            <input type="text" name="item_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category" class="form-select" required>
                                <option>Bag</option>
                                <option>Phone</option>
                                <option>ID</option>
                                <option>Keys</option>
                                <option>Wallet</option>
                                <option>Clothing</option>
                                <option>Bottle</option>
                                <option>Others</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Color, brand, special marks..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location *</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date *</label>
                            <input type="date" name="date_reported" class="form-control"
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status *</label>
                            <select name="status" class="form-select">
                                <option>Lost</option>
                                <option>Found</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Your Name *</label>
                            <input type="text" name="contact_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number *</label>
                            <input type="text" name="contact_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Found By <small class="text-muted">(optional)</small></label>
                            <input type="text" name="found_by" class="form-control"
                                   placeholder="Name of the person who found it">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Item Photo <small class="text-muted">(optional, max 5 MB)</small></label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-success">Save</button>
                        <a href="items.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
