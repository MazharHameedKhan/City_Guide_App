<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require '../config.php';
if (!isset($_SESSION['admin'])) header("Location: login.php");

// Fetch cities and categories
$cities = $pdo->query("SELECT * FROM cities")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC); // 👈 NEW LINE

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $city_id = $_POST['city_id'];
  $category_id = $_POST['category_id']; // 👈 NEW FIELD
  $type = $_POST['type'];
  $name = $_POST['name'];
  $short_desc = $_POST['short_desc'];
  $full_desc = $_POST['full_desc'];
  $open_hours = $_POST['open_hours'];
  $map_link = $_POST['map_link'];

  $image = '';
  if (!empty($_FILES['image']['name'])) {
    $filename = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$filename");
    $image = "$base_url/uploads/$filename";
  }

  // 👇 Updated query: now saves category_id too
  $stmt = $pdo->prepare("INSERT INTO locations (city_id, category_id, type, name, short_desc, full_desc, open_hours, map_link, image) 
                         VALUES (?,?,?,?,?,?,?,?,?)");
  $stmt->execute([$city_id, $category_id, $type, $name, $short_desc, $full_desc, $open_hours, $map_link, $image]);

  header("Location: attractions_list.php");
  exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Attraction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-black">
<div class="container mt-5">
  <div class="card">
    <div class="card-header bg-success text-white">Add New Attraction</div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <!-- City Dropdown -->
        <div class="mb-3">
          <label class="form-label">City</label>
          <select name="city_id" class="form-select" required>
            <?php foreach($cities as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- ✅ Category Dropdown -->
        <div class="mb-3">
          <label class="form-label">Category</label>
          <select name="category_id" class="form-select" required>
            <?php foreach($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3"><label>Type</label><input name="type" class="form-control" required></div>
        <div class="mb-3"><label>Name</label><input name="name" class="form-control" required></div>
        <div class="mb-3"><label>Short Description</label><textarea name="short_desc" class="form-control"></textarea></div>
        <div class="mb-3"><label>Full Description</label><textarea name="full_desc" class="form-control"></textarea></div>
        <div class="mb-3"><label>Open Hours</label><input name="open_hours" class="form-control" required></div>
        <div class="mb-3"><label>Map Link</label><input name="map_link" class="form-control"></div>
        <div class="mb-3"><label>Image</label><input type="file" name="image" class="form-control"></div>
        <button class="btn btn-success w-100">Save</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
