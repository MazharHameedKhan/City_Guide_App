<?php
require 'config.php';

$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => true, 'categories' => $categories]);
