<?php
require_once '../DB.php';
session_start();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['employee_id'], $input['benefit_enrollment_id'], $input['start_date'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Prepare SQL
    $sql = "UPDATE benefit_enrollment 
            SET start_date = ?,
                end_date = ?,
                status = ?,
                payroll_frequency = ?,
                payroll_deductible = ?,
                updated_at = NOW()
            WHERE id = ? AND employee_id = ?";

    $params = [
        $input['start_date'],
        $input['end_date'] ?: null,
        $input['status'],
        $input['payroll_frequency'],
        $input['payroll_deductible'] ? 1 : 0,
        $input['benefit_enrollment_id'],
        $input['employee_id']
    ];

    $updated = Database::execute($sql, $params);

    // If no rows were affected, try updating by enrollment id only (some schemas may not store employee_id on the enrollment row)
    if ($updated === 0) {
        $sql2 = "UPDATE benefit_enrollment 
            SET start_date = ?,
                end_date = ?,
                status = ?,
                payroll_frequency = ?,
                payroll_deductible = ?,
                updated_at = NOW()
            WHERE id = ?";

        $params2 = [
            $input['start_date'],
            $input['end_date'] ?: null,
            $input['status'],
            $input['payroll_frequency'],
            $input['payroll_deductible'] ? 1 : 0,
            $input['benefit_enrollment_id']
        ];

        $updated = Database::execute($sql2, $params2);
    }

    if ($updated > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Enrollment updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No changes were made or record not found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
