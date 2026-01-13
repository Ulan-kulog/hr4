<?php
session_start();
include("../../connection.php");

$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die(json_encode(['success' => false, 'message' => 'Database connection not found']));
}
$conn = $connections[$db_name];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $allowance_type = mysqli_real_escape_string($conn, $_POST['allowance_type']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $amount = floatval($_POST['amount']);
    $frequency = mysqli_real_escape_string($conn, $_POST['frequency']);
    $eligibility_criteria = mysqli_real_escape_string($conn, $_POST['eligibility_criteria'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if (empty($allowance_type) || empty($amount) || empty($frequency)) {
        header("Location: ../core_compensation.php?updated=0&error=Missing required fields");
        exit;
    }

    $sql = "UPDATE allowances SET 
            allowance_type = ?,
            department = ?,
            amount = ?,
            frequency = ?,
            eligibility_criteria = ?,
            status = ?
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsssi", $allowance_type, $department, $amount, $frequency, $eligibility_criteria, $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../core_compensation.php?updated=1");
    } else {
        $error_msg = urlencode(mysqli_error($conn));
        header("Location: ../core_compensation.php?updated=0&error=DB Error&msg=$error_msg");
    }
    mysqli_stmt_close($stmt);
    exit;
}
