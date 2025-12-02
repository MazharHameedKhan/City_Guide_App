<?php
require 'config.php';
require 'utils.php';

$data = getPostedJson();
$email = $data['email'] ?? '';

if (!$email) json_response(['status'=>false,'message'=>'Email required']);

$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) json_response(['status'=>false,'message'=>'User not found']);

$token = bin2hex(random_bytes(16));
$expire = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

$pdo->prepare("UPDATE users SET reset_token=?, reset_expire=? WHERE id=?")
    ->execute([$token, $expire, $user['id']]);

// For testing: Return token directly (in real app, send via email)
json_response(['status'=>true,'message'=>'Reset token created','token'=>$token]);
