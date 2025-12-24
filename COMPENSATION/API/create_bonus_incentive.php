<?php
session_start();
include '../../connection.php';

$db_name = 'HR_4';
if (!isset($connections[$db_name])) {
    header('Location: ../core_compensation.php?bonus_created=0&error=conn');
    exit;
}
$conn = $connections[$db_name];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../core_compensation.php');
    exit;
}

// Collect and sanitize inputs
$title = trim($_POST['title'] ?? '');
$amount = $_POST['amount'] ?? null;
$type = trim($_POST['type'] ?? ''); // e.g., 'Bonus' or 'Incentive'
$description = trim($_POST['description'] ?? '');
$employee_id = $_POST['employee_id'] ?? null;
$department = trim($_POST['department'] ?? '');
$date_awarded = trim($_POST['date_awarded'] ?? ''); // expected YYYY-MM-DD
$status = trim($_POST['status'] ?? 'Active');

if ($title === '' || $amount === null || $type === '') {
    header('Location: ../core_compensation.php?bonus_created=0&error=missing_fields');
    exit;
}

// Normalize numeric and optional fields
$amount = ($amount === '') ? null : $amount;
$employee_id = ($employee_id === '') ? null : $employee_id;
$date_awarded = ($date_awarded === '') ? null : $date_awarded;

// Insert into assumed table `bonus_incentives`. Adjust table/columns if different.
$sql = "INSERT INTO bonus_incentives (title, amount, type, description, employee_id, department, date_awarded, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    $err = mysqli_error($conn);
    header('Location: ../core_compensation.php?bonus_created=0&error=prepare&msg=' . urlencode($err));
    exit;
}

// Bind parameters as strings to avoid unintended numeric casting
mysqli_stmt_bind_param($stmt, 'ssssssss', $title, $amount, $type, $description, $employee_id, $department, $date_awarded, $status);
$ok = mysqli_stmt_execute($stmt);
if (!$ok) {
    $err = mysqli_stmt_error($stmt) ?: mysqli_error($conn);
    mysqli_stmt_close($stmt);
    header('Location: ../core_compensation.php?bonus_created=0&error=exec&msg=' . urlencode($err));
    exit;
}

mysqli_stmt_close($stmt);
header('Location: ../core_compensation.php?created=1');
exit;
