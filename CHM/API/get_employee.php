<?php
// CHM/API/get_employee.php
// Returns a single employee record as JSON. Query param: id (integer)

// Prevent connection.php from echoing HTML errors into JSON
ob_start();
require_once __DIR__ . '../../../connection.php';
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (!isset($connections) || !is_array($connections) || empty($connections)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No database connections available.']);
    exit;
}

$conn = $connections['hr4_hr_4'] ?? reset($connections);

$id = $_GET['id'] ?? null;
if ($id === null || !ctype_digit((string)$id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing or invalid `id` parameter.']);
    exit;
}

$id = (int)$id;

$stmt = mysqli_prepare($conn, 'SELECT * FROM employees WHERE id = ? LIMIT 1');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$row) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Employee not found.']);
    exit;
}

echo json_encode($row);
