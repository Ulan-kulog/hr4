<?php
session_start();
header('Content-Type: application/json');

include("../../connection.php");

$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    echo json_encode(['success' => false, 'message' => 'Database connection not found']);
    exit;
}
$conn = $connections[$db_name];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

$query = "SELECT e.*, d.name as department_name, sd.name as sub_department_name
          FROM employees e
          LEFT JOIN departments d ON e.department_id = d.id
          LEFT JOIN sub_departments sd ON e.sub_department_id = sd.id
          WHERE e.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if ($employee) {
    echo json_encode(['success' => true, 'employee' => $employee]);
} else {
    echo json_encode(['success' => false, 'message' => 'Employee not found']);
}