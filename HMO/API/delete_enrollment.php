<?php
require_once '../DB.php';
session_start();

// Set header for JSON response
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$benefit_enrollment_id = $_POST['benefit_enrollment_id'] ?? null;
$employee_id = $_POST['employee_id'] ?? null;

if (!$benefit_enrollment_id || !$employee_id) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    // Get employee name for message
    $employee = Database::fetch("SELECT first_name, last_name FROM employees WHERE id = ?", [$employee_id]);
    $employee_name = $employee ? $employee->first_name . ' ' . $employee->last_name : 'Unknown Employee';

    // 1. First, delete from employee_benefits table (one-to-many relation)
    $sql1 = "DELETE FROM employee_benefits WHERE benefit_enrollment_id = ? AND employee_id = ?";
    $benefitsDeleted = Database::execute($sql1, [$benefit_enrollment_id, $employee_id]);

    // 2. Then delete from benefit_enrollment table (main enrollment record)
    $sql2 = "DELETE FROM benefit_enrollment WHERE id = ? AND employee_id = ?";
    $enrollmentDeleted = Database::execute($sql2, [$benefit_enrollment_id, $employee_id]);

    if ($enrollmentDeleted > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Successfully deleted enrollment for {$employee_name}.",
            'benefits_deleted' => $benefitsDeleted,
            'enrollment_deleted' => $enrollmentDeleted
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Enrollment not found or already deleted'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while deleting the enrollment'
    ]);
}
