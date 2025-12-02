<?php
require 'config.php';
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$city_id = $_GET['city_id'] ?? null;

if ($q === '') {
    echo json_encode(['status'=>false,'message'=>'q parameter required']);
    exit;
}

$sql = "SELECT * FROM locations WHERE (name LIKE :q OR short_desc LIKE :q OR full_desc LIKE :q)";
$params = [':q'=>"%$q%"];
if (!empty($city_id)) {
    $sql .= " AND city_id = :city_id";
    $params[':city_id'] = $city_id;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status'=>true,'results'=>$rows]);
?>
