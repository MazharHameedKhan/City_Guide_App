<?php
require 'config.php';
require 'utils.php';

$data = getPostedJson();
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) json_response(['status'=>false,'message'=>'Email and password required']);

$stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) json_response(['status'=>false,'message'=>'User not found']);

if (password_verify($password, $user['password'])) {
    unset($user['password']);
    json_response(['status'=>true,'message'=>'Login success','user'=>$user]);
} else {
    json_response(['status'=>false,'message'=>'Wrong credentials']);
}
?>
