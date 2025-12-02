<?php
require 'config.php';

$city_id = $_GET['city_id'] ?? '';
$category_id = $_GET['category_id'] ?? '';

$query = "SELECT * FROM locations WHERE 1";

$params = [];
if (!empty($city_id)) {
    $query .= " AND city_id = ?";
    $params[] = $city_id;
}
if (!empty($category_id)) {
    $query .= " AND category_id = ?";
    $params[] = $category_id;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => true,
    'locations' => $locations
]);
