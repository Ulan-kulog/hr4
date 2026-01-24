<?php
session_start();
include("../../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die("Database connection not found");
}
$conn = $connections[$db_name];

// Helper function to build full name
function buildFullName($first_name, $middle_name, $last_name) {
    $name = trim($first_name ?? '');
    if (!empty($middle_name)) {
        $name .= ' ' . $middle_name;
    }
    if (!empty($last_name)) {
        $name .= ' ' . $last_name;
    }
    return trim($name);
}

// Fetch employees with payroll info
$sql = "SELECT 
        e.employee_code,
        e.first_name,
        e.middle_name,
        e.last_name,
        e.email,
        e.phone_number,
        e.job,
        e.salary,
        e.basic_salary,
        e.work_status,
        e.employment_status,
        e.salary_status,
        e.salary_reason,
        p.net_pay,
        p.period,
        p.status as payroll_status
        FROM employees e 
        LEFT JOIN payroll p ON e.id = p.employee_id 
        ORDER BY e.first_name, e.last_name";
$result = $conn->query($sql);

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="employee_payroll_' . date('Y-m-d') . '.xls"');

// Create Excel content with UTF-8 BOM for proper encoding
echo "\xEF\xBB\xBF"; // UTF-8 BOM

// Create header
echo "Employee Code\tFull Name\tEmail\tPhone\tJob/Position\tSalary\tBasic Salary\tWork Status\tEmployment Status\tSalary Status\tSalary Reason\tPayroll Period\tPayroll Status\tNet Pay\n";

while ($row = $result->fetch_assoc()) {
    $full_name = buildFullName($row['first_name'], $row['middle_name'], $row['last_name']);
    
    echo $row['employee_code'] . "\t";
    echo $full_name . "\t";
    echo $row['email'] . "\t";
    echo $row['phone_number'] . "\t";
    echo $row['job'] . "\t";
    echo $row['salary'] . "\t";
    echo $row['basic_salary'] . "\t";
    echo $row['work_status'] . "\t";
    echo $row['employment_status'] . "\t";
    echo $row['salary_status'] . "\t";
    echo str_replace(["\t", "\n", "\r"], " ", $row['salary_reason'] ?? '') . "\t";
    echo ($row['period'] ?? 'N/A') . "\t";
    echo ($row['payroll_status'] ?? 'No Payroll') . "\t";
    echo ($row['net_pay'] ?? '0.00') . "\n";
}