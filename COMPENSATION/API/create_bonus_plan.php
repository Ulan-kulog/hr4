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

// Get and sanitize form data
$plan_name = mysqli_real_escape_string($conn, $_POST['plan_name'] ?? '');
$bonus_type = mysqli_real_escape_string($conn, $_POST['bonus_type'] ?? '');
$amount_or_percentage = mysqli_real_escape_string($conn, $_POST['amount_or_percentage'] ?? '');
$department = mysqli_real_escape_string($conn, $_POST['department'] ?? 'All');
$eligibility_criteria = mysqli_real_escape_string($conn, $_POST['eligibility_criteria'] ?? '');
$start_date = mysqli_real_escape_string($conn, $_POST['start_date'] ?? '');
$end_date = mysqli_real_escape_string($conn, $_POST['end_date'] ?? '');
$status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'active');

// Validate required fields
if (empty($plan_name) || empty($bonus_type) || empty($amount_or_percentage)) {
    $response['error'] = 'Plan name, bonus type, and amount are required';
    echo json_encode($response);
    exit;
}

// Convert empty dates to NULL for database
$start_date = empty($start_date) ? NULL : $start_date;
$end_date = empty($end_date) ? NULL : $end_date;

// Prepare and execute insert statement
$sql = "INSERT INTO bonus_plans (plan_name, bonus_type, amount_or_percentage, department, eligibility_criteria, start_date, end_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param(
        $stmt,
        'ssssssss',
        $plan_name,
        $bonus_type,
        $amount_or_percentage,
        $department,
        $eligibility_criteria,
        $start_date,
        $end_date,
        $status
    );

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['id'] = mysqli_insert_id($conn);
    } else {
        $response['error'] = 'Failed to execute query: ' . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    $response['error'] = 'Failed to prepare statement: ' . mysqli_error($conn);
}

// Redirect back to the page with success/error message
if ($response['success']) {
    header('Location: ../core_compensation.php?bonus_created=1');
} else {
    header('Location: ../core_compensation.php?bonus_created=0&error=' . urlencode($response['error']));
}
exit;
