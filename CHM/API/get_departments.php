<?php
header('Content-Type: application/json');
require_once '../../connection.php';

if (!isset($connections['hr4_hr_4'])) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

$conn = $connections['hr4_hr_4'];

$sql = "SELECT * FROM departments ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([]);
    if ($conn) mysqli_close($conn);
    exit;
}

$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

mysqli_free_result($result);
if ($conn) mysqli_close($conn);

echo json_encode($rows);

