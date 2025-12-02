<?php
session_start();
require '../config.php';
if (!isset($_SESSION['admin'])) header("Location: login.php");

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM locations WHERE id=?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

$cities = $pdo->query("SELECT * FROM cities")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $city_id = $_POST['city_id'];
  $type = $_POST['type'];
  $name = $_POST['name'];
  $short_desc = $_POST['short_desc'];
  $full_desc = $_POST['full_desc'];
  $open_hours = $_POST['open_hours'];
  $map_link = $_POST['map_link'];
  $image = $item['image'];

  if (!empty($_FILES['image']['name'])) {
    $filename = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$filename");
    $image = "$base_url/uploads/$filename";
  }

  $stmt = $pdo->prepare("UPDATE locations SET city_id=?, type=?, name=?, short_desc=?, full_desc=?, open_hours=?, map_link=?, image=? WHERE id=?");
  $stmt->execute([$city_id, $type, $name, $short_desc, $full_desc, $open_hours, $map_link, $image, $id]);

  header("Location: attractions_list.php");
  exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Attraction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-black">
<div class="container mt-5">
  <div class="card">
    <div class="card-header bg-primary text-white">Edit Attraction</div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label>City</label>
          <select name="city_id" class="form-select">
            <?php foreach($cities as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $c['id']==$item['city_id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3"><label>Type</label><input name="type" class="form-control" value="<?= htmlspecialchars($item['type']) ?>"></div>
        <div class="mb-3"><label>Name</label><input name="name" class="form-control" value="<?= htmlspecialchars($item['name']) ?>"></div>
        <div class="mb-3"><label>Short Description</label><textarea name="short_desc" class="form-control"><?= htmlspecialchars($item['short_desc']) ?></textarea></div>
        <div class="mb-3"><label>Full Description</label><textarea name="full_desc" class="form-control"><?= htmlspecialchars($item['full_desc']) ?></textarea></div>
        <div class="mb-3"><label>Open Hours</label><input name="open_hours" class="form-control" value="<?= htmlspecialchars($item['open_hours']) ?>"></div>
        <div class="mb-3"><label>Map Link</label><input name="map_link" class="form-control" value="<?= htmlspecialchars($item['map_link']) ?>"></div>
        <div class="mb-3">
          <label>Image</label><br>
          <?php if (!empty($item['image'])): ?>
            <img src="<?= $item['image'] ?>" width="100" height="100" class="mb-2 rounded"><br>
          <?php endif; ?>
          <input type="file" name="image" class="form-control">
        </div>
        <button class="btn btn-primary w-100">Update</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
