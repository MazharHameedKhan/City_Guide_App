<?php
function json_response($arr) {
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

function getPostedJson() {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    if ($data === null && !empty($raw)) {
        return $_POST;
    }
    return $data ?: $_POST;
}
?>