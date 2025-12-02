<?php
session_start();
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['admin'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="col-md-4 mx-auto">
    <div class="card shadow">
      <div class="card-header bg-primary text-white text-center">
        <h4>Admin Login</h4>
      </div>
      <div class="card-body">
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-primary w-100">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
