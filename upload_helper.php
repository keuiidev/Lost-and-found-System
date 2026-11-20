<?php
// upload_helper.php
// Small helper functions for saving and deleting item photos.

// Saves an uploaded photo into the uploads/ folder.
// Returns: null   = no file was chosen
//          false  = upload failed (the reason is placed in $error)
//          string = the new file name to store in the database
function save_photo($file, &$error)
{
    // No file chosen (photo is optional)
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Photo upload failed. Please try a smaller image.";
        return false;
    }
    // Maximum size: 5 MB
    if ($file['size'] > 5 * 1024 * 1024) {
        $error = "Photo must be 5 MB or less.";
        return false;
    }

    // Check that the file is really an image (not just renamed)
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];
    $info = @getimagesize($file['tmp_name']);
    if ($info === false || !isset($allowed[$info['mime']])) {
        $error = "Only JPG, PNG, GIF, or WEBP images are allowed.";
        return false;
    }

    // Random file name so files never overwrite each other
    $name = "item_" . bin2hex(random_bytes(8)) . "." . $allowed[$info['mime']];
    $dir  = __DIR__ . "/uploads/";
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
        $error = "Could not save the photo. Check the uploads folder.";
        return false;
    }
    return $name;
}

// Deletes a photo file from the uploads/ folder (if it exists).
function delete_photo($name)
{
    if ($name) {
        $path = __DIR__ . "/uploads/" . basename($name);
        if (is_file($path)) {
            unlink($path);
        }
    }
}
?>
