<?php
require 'config.php';

$stmt = $pdo->query("SELECT id, name FROM cities ORDER BY name ASC");
$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => true, 'cities' => $cities]);
