<?php
session_start();
require '../config.php';
if (!isset($_SESSION['admin'])) header("Location: login.php");

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Categories</h3>
    <a href="category_create.php" class="btn btn-primary">+ Add Category</a>
  </div>

  <table class="table table-bordered bg-white">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
        <tr>
          <td><?= $cat['id'] ?></td>
          <td><?= htmlspecialchars($cat['name']) ?></td>
          <td>
            <a href="category_delete.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
