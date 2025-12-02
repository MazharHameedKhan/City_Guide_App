<?php
session_start();
require '../config.php';

// Redirect if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Delete attraction if requested
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM locations WHERE id=?");
    $stmt->execute([$id]);
    header("Location: attractions_list.php");
    exit;
}

// Fetch attractions with city name
$rows = [];
try {
    $stmt = $pdo->query("
        SELECT l.*, c.name AS city_name 
        FROM locations l 
        JOIN cities c ON l.city_id = c.id
        ORDER BY l.id DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Attractions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-black">

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
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Manage Attractions</h4>
      <a href="attraction_create.php" class="btn btn-light btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Create New
      </a>
    </div>

    <div class="card-body table-responsive">
      <table class="table table-hover align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>City</th>
            <th>Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= $r['id']; ?></td>
                <td>
                  <?php if (!empty($r['image'])): ?>
                    <img src="<?= htmlspecialchars($base_url . '/uploads/' . basename($r['image'])); ?>" 
                         class="rounded" width="60" height="60" style="object-fit:cover;">
                  <?php else: ?>
                    <span class="text-muted">No image</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['name']); ?></td>
                <td><?= htmlspecialchars($r['city_name']); ?></td>
                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($r['type']); ?></span></td>
                <td>
                  <a href="attraction_edit.php?id=<?= $r['id']; ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil-square"></i> Edit
                  </a>
                  <a href="?delete=<?= $r['id']; ?>" 
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this attraction?')">
                    <i class="bi bi-trash3"></i> Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-muted py-4">No attractions found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
