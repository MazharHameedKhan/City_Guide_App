<?php
require 'config.php';
require 'utils.php';

$data = getPostedJson();
$user_id = $data['user_id'] ?? null;
$name = trim($data['name'] ?? '');
$city = trim($data['city'] ?? '');
$bio  = trim($data['bio'] ?? '');

if (!$user_id) {
    json_response(['status'=>false,'message'=>'user_id is required']);
}

try {
    $stmt = $pdo->prepare("UPDATE users SET name=?, city=?, bio=? WHERE id=?");
    $stmt->execute([$name, $city, $bio, $user_id]);

    // Fetch updated user record
    $userStmt = $pdo->prepare("SELECT id, name, email, city, bio, profile_pic FROM users WHERE id=?");
    $userStmt->execute([$user_id]);
    $updatedUser = $userStmt->fetch(PDO::FETCH_ASSOC);

    json_response([
        'status' => true,
        'message' => 'Profile updated successfully',
        'user' => $updatedUser
    ]);
} catch (PDOException $e) {
    json_response([
        'status' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
