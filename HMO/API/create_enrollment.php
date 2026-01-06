<?php

session_start();

require_once '../DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $enrollment = [
        'employee_id' => $_POST['employee_id'],
        'coverage_type' => $_POST['coverage_type'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'payroll_deductible' => isset($_POST['payroll_deductible']) ? 1 : 0,
        'created_at' => date('Y-m-d H:i:s'),
    ];
    // dd($enrollment);

    $benefit = $_POST['benefit_id'] ?? [];

    $result = Database::insertInto('benefit_enrollment', $enrollment);

    if ($result) {
        foreach ($benefit as $b) {
            Database::insertInto('employee_benefits', [
                'benefit_enrollment_id' => $result,
                'benefit_id' => $b,
                'employee_id' => $enrollment['employee_id']
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
