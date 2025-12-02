<?php
// logout.php
header('Content-Type: application/json');
session_start();
session_destroy();
echo json_encode(['status' => true, 'message' => 'Logged out successfully']);
