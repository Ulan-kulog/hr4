<?php
// Streams a CSV report for compensation analytics
// Disable display of PHP errors and buffer output to prevent accidental HTML/warnings
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(0);
session_start();
// Clear any existing output buffers to avoid corrupting CSV
while (ob_get_level()) ob_end_clean();
// API file lives in COMPENSATION/API; connection.php is two levels up
$connPath = __DIR__ . '/../../connection.php';
if (!file_exists($connPath)) {
    error_log('[export_compensation_analytics] missing connection.php at ' . $connPath);
    http_response_code(500);
    echo 'Connection file missing';
    exit;
}
include($connPath);
error_log('[export_compensation_analytics] called');

$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    http_response_code(500);
    echo 'Connection error';
    exit;
}
$conn = $connections[$db_name];

// Gather stats (reuse queries from stats endpoint)
$stats = [];
$q = "SELECT COALESCE(SUM(basic_salary * 12),0) as total_budget FROM employees WHERE work_status = 'Active'";
$r = mysqli_query($conn, $q);
$stats['total_budget'] = ($r && ($row = mysqli_fetch_assoc($r))) ? $row['total_budget'] : 0;

$q = "SELECT COALESCE(AVG(basic_salary),0) as avg_monthly FROM employees WHERE work_status = 'Active'";
$r = mysqli_query($conn, $q);
$stats['avg_monthly'] = ($r && ($row = mysqli_fetch_assoc($r))) ? $row['avg_monthly'] : 0;

$q = "SELECT 
    COALESCE(SUM(
        CASE 
            WHEN amount_or_percentage NOT LIKE '%\\%' 
            THEN CAST(REPLACE(REPLACE(amount_or_percentage, '₱', ''), ',', '') AS DECIMAL(10,2))
            ELSE 0 
        END
    ), 0) as bonus_pool 
    FROM bonus_plans 
    WHERE status = 'active'";
$r = mysqli_query($conn, $q);
$stats['bonus_pool'] = ($r && ($row = mysqli_fetch_assoc($r))) ? $row['bonus_pool'] : 0;

$q = "SELECT COALESCE(SUM(amount * CASE LOWER(frequency) WHEN 'daily' THEN 365 WHEN 'weekly' THEN 52 WHEN 'monthly' THEN 12 WHEN 'quarterly' THEN 4 WHEN 'annual' THEN 1 ELSE 12 END),0) as allowance_budget FROM allowances WHERE status = 'active'";
$r = mysqli_query($conn, $q);
$stats['allowance_budget'] = ($r && ($row = mysqli_fetch_assoc($r))) ? $row['allowance_budget'] : 0;

$q = "SELECT COUNT(*) as total_employees FROM employees WHERE work_status = 'Active'";
$r = mysqli_query($conn, $q);
$stats['total_employees'] = ($r && ($row = mysqli_fetch_assoc($r))) ? $row['total_employees'] : 0;

// Salary by department
$salary_by_dept = [];
$q = "SELECT COALESCE(d.name,'Not Assigned') as department, COUNT(e.id) as employee_count, AVG(e.basic_salary) as avg_salary FROM employees e LEFT JOIN departments d ON e.department_id = d.id WHERE e.work_status = 'Active' GROUP BY d.name HAVING COUNT(e.id) > 0 ORDER BY avg_salary DESC";
$r = mysqli_query($conn, $q);
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) $salary_by_dept[] = $row;
}

// Compensation mix
$compensation_mix = [];
$q = "SELECT COALESCE(SUM(basic_salary * 12),0) as value FROM employees WHERE work_status = 'Active'";
$r = mysqli_query($conn, $q);
$compensation_mix[] = ['type' => 'Base Salary', 'value' => ($r && ($row = mysqli_fetch_assoc($r))) ? $row['value'] : 0];

$q = "SELECT COALESCE(SUM(CASE WHEN amount_or_percentage REGEXP '^[0-9]+(\\.[0-9]+)?$' OR amount_or_percentage LIKE '%₱%' OR (amount_or_percentage NOT LIKE '%\\%' AND amount_or_percentage NOT LIKE '%percent%') THEN CAST(REPLACE(REPLACE(REPLACE(amount_or_percentage, '₱', ''), ',', ''), ' ', '') AS DECIMAL(10,2)) ELSE 0 END),0) as value FROM bonus_plans WHERE status = 'active'";
$r = mysqli_query($conn, $q);
$compensation_mix[] = ['type' => 'Bonuses', 'value' => ($r && ($row = mysqli_fetch_assoc($r))) ? $row['value'] : 0];

$q = "SELECT COALESCE(SUM(amount * CASE LOWER(frequency) WHEN 'daily' THEN 365 WHEN 'weekly' THEN 52 WHEN 'monthly' THEN 12 WHEN 'quarterly' THEN 4 WHEN 'annual' THEN 1 ELSE 12 END),0) as value FROM allowances WHERE status = 'active'";
$r = mysqli_query($conn, $q);
$compensation_mix[] = ['type' => 'Allowances', 'value' => ($r && ($row = mysqli_fetch_assoc($r))) ? $row['value'] : 0];

$benefits_value = 0;
$q = "SHOW TABLES LIKE 'benefits'";
$r = mysqli_query($conn, $q);
if ($r && mysqli_num_rows($r) > 0) {
    $q = "SELECT COALESCE(SUM(amount * 12),0) as value FROM benefits WHERE status = 'active'";
    $r2 = mysqli_query($conn, $q);
    if ($r2 && ($row = mysqli_fetch_assoc($r2))) $benefits_value = $row['value'];
}
$compensation_mix[] = ['type' => 'Benefits', 'value' => $benefits_value];

// Prepare CSV
// Clear any buffered output to ensure headers and CSV are clean
// Ensure no previous output remains
while (ob_get_level()) ob_end_clean();

$filename = 'compensation_analytics_' . date('Ymd') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
if ($out === false) {
    error_log('[export_compensation_analytics] failed to open php://output');
    http_response_code(500);
    echo 'Failed to open output stream';
    exit;
}
if (!$out) exit;

// BOM for Excel compatibility
fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Report header
fputcsv($out, ['Compensation Analytics Report']);
fputcsv($out, ['Generated At', date('Y-m-d H:i:s')]);
fputcsv($out, []);

// Summary
fputcsv($out, ['Summary']);
fputcsv($out, ['Metric', 'Value']);
fputcsv($out, ['Total Salary Budget (annual)', $stats['total_budget']]);
fputcsv($out, ['Average Monthly Salary', $stats['avg_monthly']]);
fputcsv($out, ['Bonus Pool', $stats['bonus_pool']]);
fputcsv($out, ['Allowance Budget (annual)', $stats['allowance_budget']]);
fputcsv($out, ['Active Employees', $stats['total_employees']]);
fputcsv($out, []);

// Salary by Department
fputcsv($out, ['Salary Distribution by Department']);
fputcsv($out, ['Department', 'Employee Count', 'Avg Salary (monthly)']);
foreach ($salary_by_dept as $r) {
    fputcsv($out, [$r['department'], $r['employee_count'], $r['avg_salary']]);
}
fputcsv($out, []);

// Compensation Mix
fputcsv($out, ['Compensation Mix (annual amounts)']);
fputcsv($out, ['Type', 'Value']);
foreach ($compensation_mix as $m) {
    fputcsv($out, [$m['type'], $m['value']]);
}

fflush($out);
fclose($out);
error_log('[export_compensation_analytics] completed');
// ensure script terminates cleanly
exit(0);
