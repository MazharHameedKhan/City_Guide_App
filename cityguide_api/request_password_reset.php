<?php
require 'config.php';
require 'utils.php';

$data = getPostedJson();
$email = $data['email'] ?? '';
if (!$email) json_response(['status'=>false,'message'=>'email required']);

$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) json_response(['status'=>false,'message'=>'User not found']);

$token = bin2hex(random_bytes(16));
$expire = date('Y-m-d H:i:s', time() + 60*60); // 1 hour

$pdo->prepare("UPDATE users SET reset_token=?, reset_expire=? WHERE id=?")->execute([$token, $expire, $user['id']]);

// In real app: send $token by email. Here return token so you can use it in reset screen.
json_response(['status'=>true,'message'=>'Reset token created','token'=>$token,'expire'=>$expire]);
?>
