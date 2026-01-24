<?php
require_once '../DB.php';
header('Content-Type: application/json');

$id = $_GET['id'];

if ($id === null) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'not found'
    ]);
    exit;
}

$eb = Database::fetchAll(
    'SELECT
    eb.id AS enrollment_id,

    e.id AS employee_id, 
    e.first_name,
    e.last_name,
    d.name AS department_name,
    e.email,
    e.employee_code,

    be.id AS benefit_enrollment_id,
    be.start_date,
    be.end_date,
    be.status,
    be.coverage_type,
    be.payroll_frequency,
    be.payroll_deductible,
    be.updated_at,

    b.id AS benefit_id,
    b.benefit_name,
    b.description,
    b.benefit_type,
    b.value,
    b.is_taxable

    FROM employee_benefits eb
    LEFT JOIN benefit_enrollment be 
            ON be.id = eb.benefit_enrollment_id
    JOIN employees e ON e.id = eb.employee_id
    LEFT JOIN departments d ON e.department_id = d.id
    JOIN benefits b ON b.id = eb.benefit_id
    WHERE e.id = :id
',
    [':id' => $id]
);


$employee_benefits = [];

foreach ($eb as $row) {

    $empId = $row->employee_id;

    if (!isset($employee_benefits[$empId])) {
        $employee_benefits[$empId] = [
            'employee_id' => $empId,
            'first_name' => $row->first_name,
            'last_name' => $row->last_name,
            'department' => $row->department_name ?? null,
            'email' => $row->email,
            'employee_code' => $row->employee_code,
            'benefit_enrollment_id' => $row->benefit_enrollment_id,
            'start_date' => $row->start_date,
            'end_date' => $row->end_date,
            'status' => $row->status,
            'payroll_frequency' => $row->payroll_frequency,
            'payroll_deductible' => $row->payroll_deductible,
            'updated_at' => $row->updated_at,
            'benefits' => []
        ];
    }

    $employee_benefits[$empId]['benefits'][] = [
        'benefit_id' => $row->benefit_id,
        'benefit_name' => $row->benefit_name,
        'type' => $row->benefit_type,
        'value' => $row->value,
        'is_taxable' => $row->is_taxable,
        'status' => $row->status,
        'enrollment_id' => $row->enrollment_id
    ];
}

if (empty($employee_benefits)) {
    echo json_encode([
        'success' => false,
        'message' => 'Employee not found'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => array_values($employee_benefits)[0]
]);
exit;
