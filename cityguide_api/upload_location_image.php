<?php
session_start();
require 'config.php';
require 'utils.php';


// Simple admin form to upload image for a location (must be logged in as admin first)
if (!isset($_SESSION['admin'])) {
    echo "<p style='color:red'>Not logged in as admin. Please <a href='login.php'>login</a> first.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // forward to upload_location_image.php via curl internal
    $location_id = $_POST['location_id'] ?? '';
    if (empty($location_id)) {
        $msg = "Please provide location_id";
    } elseif (!isset($_FILES['image'])) {
        $msg = "Please choose a file";
    } else {
        // Use move_uploaded_file directly to reuse same uploads folder (bypass session check)
        $uploaddir = __DIR__ . '/../uploads/';
        if (!is_dir($uploaddir)) mkdir($uploaddir, 0755, true);

        $fname = basename($_FILES['image']['name']);
        $ext = pathinfo($fname, PATHINFO_EXTENSION);
        $newname = 'loc_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = $uploaddir . $newname;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $urlPath = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/../uploads/' . $newname;
            $stmt = $pdo->prepare("UPDATE locations SET image=? WHERE id=?");
            $stmt->execute([$urlPath, $location_id]);
            $msg = "Uploaded successfully. Image URL: $urlPath";
        } else {
            $msg = "Upload failed (move_uploaded_file)";
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Upload Location Image (Admin)</title></head>
<body>
    <h2>Upload Location Image</h2>
    <?php if(!empty($msg)) echo "<p>$msg</p>"; ?>
    <form method="post" enctype="multipart/form-data">
        <label>Location ID: <input name="location_id" type="text" required></label><br/><br/>
        <label>Image: <input name="image" type="file" accept="image/*" required></label><br/><br/>
        <button type="submit">Upload</button>
    </form>
    <p>Tip: find <strong>location_id</strong> from <code>attractions_list.php</code> or PHPMyAdmin.</p>
    <p><a href="attractions_list.php">Back to attractions list</a></p>
</body>
</html>
