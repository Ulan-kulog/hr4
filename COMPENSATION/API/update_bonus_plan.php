<?php
session_start();
include("../../connection.php");

// Database connection
$db_name = "HR_4";
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

// Get and sanitize form data
$id = intval($_POST['id'] ?? 0);
$plan_name = mysqli_real_escape_string($conn, $_POST['plan_name'] ?? '');
$bonus_type = mysqli_real_escape_string($conn, $_POST['bonus_type'] ?? '');
$amount_or_percentage = mysqli_real_escape_string($conn, $_POST['amount_or_percentage'] ?? '');
$department = mysqli_real_escape_string($conn, $_POST['department'] ?? 'All');
$eligibility_criteria = mysqli_real_escape_string($conn, $_POST['eligibility_criteria'] ?? '');
$start_date = mysqli_real_escape_string($conn, $_POST['start_date'] ?? '');
$end_date = mysqli_real_escape_string($conn, $_POST['end_date'] ?? '');
$status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'active');

// Validate required fields and ID
if ($id <= 0) {
    $response['error'] = 'Invalid bonus plan ID';
    echo json_encode($response);
    exit;
}

if (empty($plan_name) || empty($bonus_type) || empty($amount_or_percentage)) {
    $response['error'] = 'Plan name, bonus type, and amount are required';
    echo json_encode($response);
    exit;
}

// Convert empty dates to NULL for database
$start_date = empty($start_date) ? NULL : $start_date;
$end_date = empty($end_date) ? NULL : $end_date;

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

// Prepare and execute update statement
$sql = "UPDATE bonus_plans SET 
        plan_name = ?, 
        bonus_type = ?, 
        amount_or_percentage = ?, 
        department = ?, 
        eligibility_criteria = ?, 
        start_date = ?, 
        end_date = ?, 
        status = ?,
        updated_at = CURRENT_TIMESTAMP
        WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param(
        $stmt,
        'ssssssssi',
        $plan_name,
        $bonus_type,
        $amount_or_percentage,
        $department,
        $eligibility_criteria,
        $start_date,
        $end_date,
        $status,
        $id
    );

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
    header('Location: ../core_compensation.php?bonus_updated=1');
} else {
    header('Location: ../core_compensation.php?bonus_updated=0&error=' . urlencode($response['error']));
}
exit;
