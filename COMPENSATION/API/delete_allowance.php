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

    // Check if allowance exists
    $check_sql = "SELECT id FROM allowances WHERE id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) == 0) {
        header("Location: ../core_compensation.php?deleted=0&error=Allowance not found");
        mysqli_stmt_close($check_stmt);
        exit;
    }
    mysqli_stmt_close($check_stmt);

    // Delete the allowance
    $sql = "DELETE FROM allowances WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../core_compensation.php?deleted=1");
    } else {
        $error_msg = urlencode(mysqli_error($conn));
        header("Location: ../core_compensation.php?deleted=0&error=DB Error&msg=$error_msg");
    }
    mysqli_stmt_close($stmt);
    exit;
}
