<?php
// add_review.php
// Expects JSON POST or form-data: user_id, location_id, rating, comment

// No whitespace before <?php
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json');

require 'config.php';
require 'utils.php';

$data = getPostedJson();

$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
$location_id = isset($data['location_id']) ? intval($data['location_id']) : 0;
$rating = isset($data['rating']) ? intval($data['rating']) : 0;
$comment = isset($data['comment']) ? trim($data['comment']) : '';

if ($user_id <= 0 || $location_id <= 0 || $rating <= 0) {
    json_response(['status' => false, 'message' => 'user_id, location_id and rating are required']);
}

try {
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, location_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $location_id, $rating, $comment]);

    // update average rating (optional)
    $avg = $pdo->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE location_id = ?");
    $avg->execute([$location_id]);
    $r = $avg->fetch(PDO::FETCH_ASSOC);
    if ($r && isset($r['avg_rating'])) {
        $pdo->prepare("UPDATE locations SET rating = ? WHERE id = ?")
            ->execute([round($r['avg_rating'],1), $location_id]);
    }

    json_response(['status' => true, 'message' => 'Review added']);
} catch (Exception $e) {
    error_log("add_review.php error: " . $e->getMessage());
    json_response(['status' => false, 'message' => 'Server error while adding review']);
}
