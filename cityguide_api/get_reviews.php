<?php
// get_reviews.php
// Returns reviews for a location: GET ?location_id=1

ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json');

require 'config.php';
require 'utils.php';

$location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : 0;
if ($location_id <= 0) {
    echo json_encode(['status' => false, 'message' => 'location_id required']);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT r.id, r.rating, r.comment, r.created_at, u.id as user_id, u.name as user_name, u.profile_pic
         FROM reviews r
         LEFT JOIN users u ON r.user_id = u.id
         WHERE r.location_id = ?
         ORDER BY r.created_at DESC"
    );
    $stmt->execute([$location_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => true, 'reviews' => $rows]);
    exit;
} catch (Exception $e) {
    error_log("get_reviews.php error: " . $e->getMessage());
    echo json_encode(['status' => false, 'message' => 'Server error while fetching reviews']);
    exit;
}
?>