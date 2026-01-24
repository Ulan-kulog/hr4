<?php

session_start();

require_once '../DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // sanitize and validate inputs
    $employee_id = isset($_POST['employee_id']) && $_POST['employee_id'] !== '' ? (int)$_POST['employee_id'] : null;
    $coverage_type = trim($_POST['coverage_type'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $payroll_deductible = isset($_POST['payroll_deductible']) ? 1 : 0;

    // required fields check
    if (empty($employee_id) || $coverage_type === '') {
        $_SESSION['message'] = 'Employee and coverage type are required.';
        header('Location: ../benefits_enrollment.php');
        exit();
    }

    $enrollment = [
        'employee_id' => $employee_id,
        'coverage_type' => $coverage_type,
        'start_date' => $start_date ?: null,
        'end_date' => $end_date ?: null,
        'payroll_deductible' => $payroll_deductible,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    // ensure benefit list is an array
    $benefit = $_POST['benefit_id'] ?? [];
    if (!is_array($benefit)) {
        if ($benefit === '' || $benefit === null) {
            $benefit = [];
        } else {
            $benefit = [$benefit];
        }
    }

    try {
        $result = Database::insertInto('benefit_enrollment', $enrollment);
    } catch (Exception $e) {
        $_SESSION['message'] = 'Failed to create enrollment: ' . $e->getMessage();
        http_response_code(500);
        header('Location: ../benefits_enrollment.php');
        exit();
    }

    if ($result) {
        foreach ($benefit as $b) {
            // skip empty values
            if ($b === '' || $b === null) continue;
            Database::insertInto('employee_benefits', [
                'benefit_enrollment_id' => $result,
                'benefit_id' => $b,
                'employee_id' => $employee_id
            ]);
        }
        $_SESSION['message'] = 'Employee benefit enrolled.';
        header('Location: ../benefits_enrollment.php');
        exit();
    } else {
        $_SESSION['message'] = 'Failed to create enrollment.';
        http_response_code(500);
        header('Location: ../benefits_enrollment.php');
        exit();
    }
} else {
    http_response_code(405);
    echo 'Method Not Allowed';
}
