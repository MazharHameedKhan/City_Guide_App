<?php
session_start();
require '../config.php';

// Redirect if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: attractions_list.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch current attraction data
$stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
$stmt->execute([$id]);
$attraction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$attraction) {
    echo "<div class='alert alert-danger text-center mt-5'>Attraction not found!</div>";
    exit;
}

// Fetch all cities for dropdown
$cities = $pdo->query("SELECT id, name FROM cities ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city_id = $_POST['city_id'];
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);
    $imagePath = $attraction['image']; // keep old image by default

    // Image upload
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../uploads/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $fileName;
        }
    }

    // Update in DB
    $update = $pdo->prepare("UPDATE locations SET city_id=?, name=?, type=?, description=?, image=? WHERE id=?");
    $update->execute([$city_id, $name, $type, $description, $imagePath, $id]);

    header("Location: attractions_list.php?updated=1");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Attraction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Admin Panel</a>
    <div class="d-flex align-items-center">
      <span class="text-white me-3">Welcome, <strong><?= htmlspecialchars($_SESSION['admin']) ?></strong></span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
  <div class="card shadow border-0">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Attraction</h4>
    </div>
    <div class="card-body p-4">
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label fw-semibold">Attraction Name</label>
          <input type="text" name="name" class="form-control" required 
                 value="<?= htmlspecialchars($attraction['name']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">City</label>
          <select name="city_id" class="form-select" required>
            <option value="">Select City</option>
            <?php foreach ($cities as $city): ?>
              <option value="<?= $city['id'] ?>" 
                <?= $city['id'] == $attraction['city_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($city['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Type</label>
          <input type="text" name="type" class="form-control" 
                 value="<?= htmlspecialchars($attraction['type']) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Description</label>
          <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($attraction['description']) ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Current Image</label><br>
          <?php if (!empty($attraction['image'])): ?>
            <img src="<?= htmlspecialchars($base_url . '/uploads/' . $attraction['image']); ?>" 
                 width="120" height="120" class="rounded mb-2" style="object-fit:cover;">
          <?php else: ?>
            <p class="text-muted">No image available</p>
          <?php endif; ?>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold">Upload New Image</label>
          <input type="file" name="image" class="form-control">
        </div>

        <div class="text-end">
          <a href="attractions_list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Update Attraction
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
