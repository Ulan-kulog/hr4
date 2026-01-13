<?php
session_start();
include '../../connection.php';

$db_name = 'hr4_hr_4';
if (!isset($connections[$db_name])) {
    header('Location: ../core_compensation.php?deleted=0&error=conn');
    exit;
}
$conn = $connections[$db_name];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../core_compensation.php');
    exit;
}

$id = $_POST['id'] ?? '';
if ($id === '') {
    header('Location: ../core_compensation.php?deleted=0&error=missing_id');
    exit;
}

$isNumericId = is_numeric($id);

if ($isNumericId) {
    $sql = "DELETE FROM salary_grades WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) mysqli_stmt_bind_param($stmt, 'i', $id);
} else {
    $sql = "DELETE FROM salary_grades WHERE grade_name = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) mysqli_stmt_bind_param($stmt, 's', $id);
}

if (!$stmt) {
    $err = mysqli_error($conn);
    header('Location: ../core_compensation.php?deleted=0&error=prepare&msg=' . urlencode($err));
    exit;
}

$ok = mysqli_stmt_execute($stmt);
if (!$ok) {
    $err = mysqli_stmt_error($stmt) ?: mysqli_error($conn);
    mysqli_stmt_close($stmt);
    header('Location: ../core_compensation.php?deleted=0&error=exec&msg=' . urlencode($err));
    exit;
}

mysqli_stmt_close($stmt);
header('Location: ../core_compensation.php?deleted=1');
exit;
