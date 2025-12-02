<?php
require 'config.php';
require 'utils.php';

$data = getPostedJson();
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$city = $data['city'] ?? '';

if (!$email || !$password) json_response(['status'=>false,'message'=>'Email and password required']);

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (name,email,password,city) VALUES (?,?,?,?)");
try {
    $stmt->execute([$name,$email,$hashed,$city]);
    $id = $pdo->lastInsertId();
    json_response(['status'=>true,'message'=>'Registered','user_id'=>$id]);
} catch (Exception $e) {
    json_response(['status'=>false,'message'=>'Email may be already used']);
}
?>
