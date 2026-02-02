<?php
header('Content-Type: application/json');
session_start();
// API file lives in COMPENSATION/API; connection.php is two levels up
$connPath = __DIR__ . '/../../connection.php';
if (!file_exists($connPath)) {
    error_log('[compensation_stats] missing connection.php at ' . $connPath);
    echo json_encode(['error' => 'Connection file missing']);
    http_response_code(500);
    exit;
}
include($connPath);

$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    echo json_encode(['error' => 'Connection not found']);
    http_response_code(500);
    exit;
}
$conn = $connections[$db_name];

$stats = [];

// Total Salary Budget (annual)
$sql_total_budget = "SELECT COALESCE(SUM(basic_salary * 12), 0) as total_budget FROM employees WHERE work_status = 'Active'";
$result = mysqli_query($conn, $sql_total_budget);
$stats['total_budget'] = ($result && ($r = mysqli_fetch_assoc($result))) ? (float)$r['total_budget'] : 0;

// Average Monthly Salary
$sql_avg_salary = "SELECT COALESCE(AVG(basic_salary), 0) as avg_monthly FROM employees WHERE work_status = 'Active'";
$result = mysqli_query($conn, $sql_avg_salary);
$stats['avg_monthly'] = ($result && ($r = mysqli_fetch_assoc($result))) ? (float)$r['avg_monthly'] : 0;

// Bonus Pool
$sql_bonus_pool = "SELECT 
    COALESCE(SUM(
        CASE 
            WHEN amount_or_percentage NOT LIKE '%\\%' 
            THEN CAST(REPLACE(REPLACE(amount_or_percentage, '₱', ''), ',', '') AS DECIMAL(10,2))
            ELSE 0 
        END
    ), 0) as bonus_pool 
    FROM bonus_plans 
    WHERE status = 'active'";
$result = mysqli_query($conn, $sql_bonus_pool);
$stats['bonus_pool'] = ($result && ($r = mysqli_fetch_assoc($result))) ? (float)$r['bonus_pool'] : 0;

// Allowance Budget (annualized)
$sql_allowance_budget = "SELECT 
    COALESCE(SUM(
        amount * 
        CASE LOWER(frequency) 
            WHEN 'daily' THEN 365 
            WHEN 'weekly' THEN 52 
            WHEN 'monthly' THEN 12 
            WHEN 'quarterly' THEN 4 
            WHEN 'annual' THEN 1 
            ELSE 12 
        END
    ), 0) as allowance_budget 
    FROM allowances WHERE status = 'active'";
$result = mysqli_query($conn, $sql_allowance_budget);
$stats['allowance_budget'] = ($result && ($r = mysqli_fetch_assoc($result))) ? (float)$r['allowance_budget'] : 0;

// Total active employees
$sql_employees = "SELECT COUNT(*) as total_employees FROM employees WHERE work_status = 'Active'";
$result = mysqli_query($conn, $sql_employees);
$stats['total_employees'] = ($result && ($r = mysqli_fetch_assoc($result))) ? (int)$r['total_employees'] : 0;

echo json_encode($stats);
