<?php
session_start();
include("../../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die(json_encode(['success' => false, 'error' => 'Database connection not found']));
}
$conn = $connections[$db_name];

// Initialize response array
$response = ['success' => false, 'error' => ''];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['error'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Get and validate ID
$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    $response['error'] = 'Invalid bonus plan ID';
    echo json_encode($response);
    exit;
}

// Check if record exists
$check_sql = "SELECT id FROM bonus_plans WHERE id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, 'i', $id);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) === 0) {
    $response['error'] = 'Bonus plan not found';
    mysqli_stmt_close($check_stmt);
    echo json_encode($response);
    exit;
}
mysqli_stmt_close($check_stmt);

// Prepare and execute delete statement
$sql = "DELETE FROM bonus_plans WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Failed to execute query: ' . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    $response['error'] = 'Failed to prepare statement: ' . mysqli_error($conn);
}

// Redirect back to the page with success/error message
if ($response['success']) {
    header('Location: ../core_compensation.php?bonus_deleted=1');
} else {
    header('Location: ../core_compensation.php?bonus_deleted=0&error=' . urlencode($response['error']));
}
exit;
