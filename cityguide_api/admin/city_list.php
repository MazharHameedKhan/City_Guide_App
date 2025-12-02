<?php
session_start();
require '../config.php';
if (!isset($_SESSION['admin'])) header("Location: login.php");

$cities = $pdo->query("SELECT * FROM cities")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Cities</title>
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
      <h4 class="mb-0"><i class="bi bi-building"></i> City List</h4>
      <a href="city_create.php" class="btn btn-light btn-sm">
        <i class="bi bi-plus-circle"></i> Add City
      </a>
    </div>
    <div class="card-body">
      <?php if (count($cities) > 0): ?>
      <table class="table table-striped table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th scope="col">#</th>
            <th scope="col">City Name</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($cities as $c): ?>
          <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['name']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p class="text-center text-muted mb-0">No cities found.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
