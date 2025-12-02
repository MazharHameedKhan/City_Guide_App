<?php
require 'config.php';
require 'utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['status'=>false,'message'=>'POST required']);
}

$user_id = $_POST['user_id'] ?? null;
if (!$user_id) json_response(['status'=>false,'message'=>'user_id required']);

if (!isset($_FILES['profile'])) json_response(['status'=>false,'message'=>'profile file missing']);

$uploaddir = __DIR__ . '/uploads/';
if (!is_dir($uploaddir)) mkdir($uploaddir, 0755, true);

$fname = basename($_FILES['profile']['name']);
$ext = pathinfo($fname, PATHINFO_EXTENSION);
$newname = 'profile_' . time() . '_' . rand(1000,9999) . '.' . $ext;
$target = $uploaddir . $newname;

if (move_uploaded_file($_FILES['profile']['tmp_name'], $target)) {
    $urlPath = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/uploads/' . $newname;
    $stmt = $pdo->prepare("UPDATE users SET profile_pic=? WHERE id=?");
    $stmt->execute([$urlPath, $user_id]);
    json_response(['status'=>true,'message'=>'Uploaded','profile_pic'=>$urlPath]);
} else {
    json_response(['status'=>false,'message'=>'Upload failed']);
}
?>
