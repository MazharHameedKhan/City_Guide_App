<?php
session_start();
require '../config.php';
if (!isset($_SESSION['admin'])) header("Location: login.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
        header("Location: category_list.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Category</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="card mx-auto" style="max-width: 500px;">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Add New Category</h5>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Category Name</label>
          <input type="text" name="name" class="form-control" placeholder="e.g., Restaurant" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Save Category</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
