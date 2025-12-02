<?php
require 'config.php';
require 'utils.php';

$data = getPostedJson();
$token = $data['token'] ?? '';
$new_password = $data['new_password'] ?? '';

if (!$token || !$new_password)
    json_response(['status'=>false,'message'=>'Token and new password required']);

$stmt = $pdo->prepare("SELECT id, reset_expire FROM users WHERE reset_token=?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) json_response(['status'=>false,'message'=>'Invalid token']);
if (strtotime($user['reset_expire']) < time())
    json_response(['status'=>false,'message'=>'Token expired']);

$hash = password_hash($new_password, PASSWORD_DEFAULT);
$pdo->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expire=NULL WHERE id=?")
    ->execute([$hash, $user['id']]);

json_response(['status'=>true,'message'=>'Password reset successfully']);
