<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: login.php");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-primary">
  <div class="container-fluid">
    <a href="index.php" class="navbar-brand">CityGuide Admin</a>
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
    <a href="category_list.php" class="btn btn-secondary">Manage Categories</a>

  </div>
</nav>

<div class="container mt-5">
  <div class="card">
    <div class="card-header bg-primary text-white">Dashboard</div>
    <div class="card-body">
      <a href="attractions_list.php" class="btn btn-success">Manage Attractions</a>
    </div>
    <div class="card-body">
      <a href="city_create.php" class="btn btn-success">City Create</a>
    </div>
  </div>
</div>
</body>
</html>
