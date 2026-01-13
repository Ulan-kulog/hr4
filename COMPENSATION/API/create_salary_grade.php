<?php
session_start();
include '../../connection.php';

$db_name = 'hr4_hr_4';
if (!isset($connections[$db_name])) {
    header('Location: ../core_compensation.php?created=0&error=conn');
    exit;
}
$conn = $connections[$db_name];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../core_compensation.php');
    exit;
}

$grade = trim($_POST['grade_name'] ?? '');
$position = trim($_POST['position'] ?? '');
$min = $_POST['min_salary'] ?? null;
$max = $_POST['max_salary'] ?? null;
$department = trim($_POST['department'] ?? '');
$status = $_POST['status'] ?? 'Active';

if ($grade === '') {
    header('Location: ../core_compensation.php?created=0&error=missing_grade');
    exit;
}

// Normalize empty numeric fields to NULL
$min = ($min === '') ? null : $min;
$max = ($max === '') ? null : $max;

$sql = "INSERT INTO salary_grades (grade_name, position, min_salary, max_salary, department, status) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    $err = mysqli_error($conn);
    header('Location: ../core_compensation.php?created=0&error=prepare&msg=' . urlencode($err));
    exit;
}

mysqli_stmt_bind_param($stmt, 'ssssss', $grade, $position, $min, $max, $department, $status);
$ok = mysqli_stmt_execute($stmt);
if (!$ok) {
    $err = mysqli_stmt_error($stmt) ?: mysqli_error($conn);
    mysqli_stmt_close($stmt);
    header('Location: ../core_compensation.php?updated=0&error=DB Error&msg=' . urlencode($err));
    exit;
}

mysqli_stmt_close($stmt);
header('Location: ../core_compensation.php?updated=1&action=salary_created');
exit;
