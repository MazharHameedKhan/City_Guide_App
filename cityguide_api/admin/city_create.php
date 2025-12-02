<?php
session_start();
require '../config.php';
if (!isset($_SESSION['admin'])) header("Location: login.php");

if ($_SERVER['REQUEST_METHOD']=='POST') {
    $name = $_POST['name'];
    $stmt = $pdo->prepare("INSERT INTO cities (name) VALUES (?)");
    $stmt->execute([$name]);
    header("Location: city_list.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create City</title>
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

<!-- Main Container -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="bi bi-building-add me-2"></i>Add New City</h4>
          <a href="city_list.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
        </div>
        <div class="card-body">
          <form method="post" class="needs-validation" novalidate>
            <div class="mb-3">
              <label for="name" class="form-label">City Name</label>
              <input type="text" id="name" name="name" class="form-control" placeholder="Enter city name" required>
              <div class="invalid-feedback">Please enter a city name.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-save me-1"></i> Add City
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validation Script -->
<script>
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

</body>
</html>
