<?php
header('Content-Type: application/json; charset=utf-8');

// Load DB connection (relative to this file)
require_once __DIR__ . '/../../connection.php';

$db_name = 'hr4_hr_4';
if (!isset($connections[$db_name])) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection not available']);
    exit;
}
$conn = $connections[$db_name];

$out = [];

// 1) Department-wise salary distribution
$salary_by_dept = [];
$sql_dept_salary = "SELECT 
    COALESCE(d.name, 'Not Assigned') AS department,
    COUNT(e.id) AS employee_count,
    AVG(e.basic_salary) AS avg_salary
    FROM employees e
    LEFT JOIN departments d ON e.department_id = d.id
    WHERE e.work_status = 'Active'
    GROUP BY d.name
    HAVING COUNT(e.id) > 0
    ORDER BY avg_salary DESC";
$res = mysqli_query($conn, $sql_dept_salary);
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $salary_by_dept[] = $r;
    }
}

$out['salary_by_dept'] = $salary_by_dept;

// 2) Compensation mix (reuse queries similar to core page)
$compensation_mix = [];

// Base salary (annual)
$sql_base_salary = "SELECT COALESCE(SUM(basic_salary * 12), 0) as value FROM employees WHERE work_status = 'Active'";
$res = mysqli_query($conn, $sql_base_salary);
$base = $res ? mysqli_fetch_assoc($res) : null;
$compensation_mix[] = ['type' => 'Base Salary', 'value' => floatval($base['value'] ?? 0)];

// Bonuses (fixed amounts)
$sql_bonuses = "SELECT 
    COALESCE(SUM(
        CASE 
            WHEN amount_or_percentage REGEXP '^[0-9]+(\\.[0-9]+)?$' 
            OR amount_or_percentage LIKE '%₱%'
            OR (amount_or_percentage NOT LIKE '%\\%' AND amount_or_percentage NOT LIKE '%percent%')
            THEN CAST(REPLACE(REPLACE(REPLACE(amount_or_percentage, '₱', ''), ',', ''), ' ', '') AS DECIMAL(10,2))
            ELSE 0 
        END
    ), 0) as value FROM bonus_plans WHERE status = 'active'";
$res = mysqli_query($conn, $sql_bonuses);
$bon = $res ? mysqli_fetch_assoc($res) : null;
$compensation_mix[] = ['type' => 'Bonuses', 'value' => floatval($bon['value'] ?? 0)];

// Allowances (annual total)
$sql_allowances = "SELECT COALESCE(SUM(
    amount * 
    CASE LOWER(frequency)
        WHEN 'daily' THEN 365
        WHEN 'weekly' THEN 52
        WHEN 'monthly' THEN 12
        WHEN 'quarterly' THEN 4
        WHEN 'annual' THEN 1
        ELSE 12
    END
), 0) as value FROM allowances WHERE status = 'active'";
$res = mysqli_query($conn, $sql_allowances);
$alw = $res ? mysqli_fetch_assoc($res) : null;
$compensation_mix[] = ['type' => 'Allowances', 'value' => floatval($alw['value'] ?? 0)];

// Benefits (if table exists)
$benefits_value = 0;
$table_check = "SHOW TABLES LIKE 'benefits'";
$table_result = mysqli_query($conn, $table_check);
if ($table_result && mysqli_num_rows($table_result) > 0) {
    $sql_benefits = "SELECT COALESCE(SUM(amount * 12), 0) as value FROM benefits WHERE status = 'active'";
    $res = mysqli_query($conn, $sql_benefits);
    if ($res) {
        $b = mysqli_fetch_assoc($res);
        $benefits_value = floatval($b['value'] ?? 0);
    }
}
$compensation_mix[] = ['type' => 'Benefits', 'value' => $benefits_value];

// Filter zeros
$filtered = array_values(array_filter($compensation_mix, function ($i) {
    return floatval($i['value']) > 0;
}));
if (!empty($filtered)) {
    $compensation_mix = $filtered;
} else {
    if (floatval($compensation_mix[0]['value']) == 0) $compensation_mix[0]['value'] = 1;
}

$out['compensation_mix'] = $compensation_mix;

echo json_encode($out);
exit;
