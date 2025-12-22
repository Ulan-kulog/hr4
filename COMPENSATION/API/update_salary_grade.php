<?php
session_start();
include '../../connection.php';

$db_name = 'HR_4';
if (!isset($connections[$db_name])) {
    // if no connection, redirect back with error
    header('Location: ../core_compensation.php?updated=0&error=conn');
    exit;
}
$conn = $connections[$db_name];

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../core_compensation.php');
    exit;
}

$id = $_POST['id'] ?? '';
$grade = $_POST['grade'] ?? '';
$position = $_POST['position'] ?? '';
$min = $_POST['min_salary'] ?? null;
$max = $_POST['max_salary'] ?? null;
$department = $_POST['department'] ?? '';
$status = $_POST['status'] ?? '';

// Basic validation
if ($id === '') {
    header('Location: ../core_compensation.php?updated=0&error=missing_id');
    exit;
}

// Normalize empty numeric fields to NULL
$min = ($min === '') ? null : $min;
$max = ($max === '') ? null : $max;

$isNumericId = is_numeric($id);

if ($isNumericId) {
    $sql = "UPDATE salary_grades SET grade_name = ?, position = ?, min_salary = ?, max_salary = ?, status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) mysqli_stmt_bind_param($stmt, 'sssssi', $grade, $position, $min, $max, $status, $id);
} else {
    $sql = "UPDATE salary_grades SET grade_name = ?, position = ?, min_salary = ?, max_salary = ?, status = ? WHERE grade_name = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) mysqli_stmt_bind_param($stmt, 'ssssss', $grade, $position, $min, $max, $status, $id);
}

if (!$stmt) {
    $err = mysqli_error($conn);
    header('Location: ../core_compensation.php?updated=0&error=prepare&msg=' . urlencode($err));
    exit;
}

$ok = mysqli_stmt_execute($stmt);
if (!$ok) {
    $err = mysqli_stmt_error($stmt) ?: mysqli_error($conn);
    mysqli_stmt_close($stmt);
    header('Location: ../core_compensation.php?updated=0&error=exec&msg=' . urlencode($err));
    exit;
}

mysqli_stmt_close($stmt);
header('Location: ../core_compensation.php?updated=1');
exit;
