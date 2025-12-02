<?php
include 'config.php';

$sql = "SELECT id, city_id, name, description, category, map_link,
        CONCAT('http://', '{$_SERVER['HTTP_HOST']}', '/CITYGUIDE_API/', image) AS image
        FROM attractions";

$result = mysqli_query($conn, $sql);
$locations = [];

while ($row = mysqli_fetch_assoc($result)) {
    $locations[] = $row;
}

echo json_encode($locations);
?>
