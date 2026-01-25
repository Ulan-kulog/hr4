<?php
require_once 'DB.php';
session_start();

$benefits = Database::fetchAll(
    'SELECT 
    b.*,
    p.name as provider_name 
    FROM benefits b 
    LEFT JOIN providers p ON b.provider_id = p.id
    ORDER BY b.id DESC'
);

$benefits_count = count($benefits);

$providers = Database::fetchAll('SELECT * FROM providers ORDER BY name asc');

$benefit_categories = Database::fetchAll('SELECT id, name FROM benefit_categories ORDER BY name asc');

$policies = Database::fetchAll('SELECT * FROM policies ORDER BY id DESC');

$employees = Database::fetchAll('SELECT e.id, e.first_name, e.last_name, e.employee_code, e.department_id, d.name AS department_name FROM employees e LEFT JOIN departments d ON e.department_id = d.id');

$employee_benefits_query = Database::fetchAll(
    'SELECT
    eb.id AS enrollment_id,
    eb.benefit_id,
    
    e.id AS employee_id, 
    e.first_name,
    e.last_name,
    e.department_id,
    d.name AS department_name,
    e.email,
    e.employee_code,

    be.id AS benefit_enrollment_id,
    be.start_date,
    be.end_date,
    be.status AS enrollment_status,
    be.coverage_type,
    be.payroll_frequency,
    be.payroll_deductible,
    be.updated_at,

    b.id AS benefit_id,
    b.benefit_name,
    b.description,
    b.benefit_type,
    b.value,
    b.is_taxable,
    b.status

    FROM employee_benefits eb
    LEFT JOIN benefit_enrollment be ON be.id = eb.benefit_enrollment_id
    JOIN employees e ON e.id = eb.employee_id
    LEFT JOIN departments d ON e.department_id = d.id
    JOIN benefits b ON b.id = eb.benefit_id
'
);

$employee_benefits = [];

foreach ($employee_benefits_query as $eb) {
    $empId = $eb->employee_id;

    if (!isset($employee_benefits[$empId])) {
        $employee_benefits[$empId] = [
            'employee_id' => $empId,
            'first_name' => $eb->first_name,
            'last_name' => $eb->last_name,
            'department' => $eb->department_name ?? null,
            'email' => $eb->email,
            'employee_code' => $eb->employee_code,
            'benefit_enrollment_id' => $eb->benefit_enrollment_id,
            'start_date' => $eb->start_date,
            'end_date' => $eb->end_date,
            'status' => $eb->enrollment_status,
            'payroll_frequency' => $eb->payroll_frequency,
            'payroll_deductible' => $eb->payroll_deductible,
            'updated_at' => $eb->updated_at,
            'benefits' => []
        ];
    }

    $employee_benefits[$empId]['benefits'][] = [
        'benefit_id' => $eb->benefit_id,
        'benefit_name' => $eb->benefit_name,
        'type' => $eb->benefit_type,
        'value' => $eb->value,
        'is_taxable' => $eb->is_taxable,
        'status' => $eb->status,
        'enrollment_id' => $eb->enrollment_id
    ];
}

// NEW: Calculate statistics
// 1. Count distinct enrolled employees
$enrolled_employees_result = Database::fetchAll('SELECT COUNT(DISTINCT employee_id) as count FROM employee_benefits');
$enrolled_employees = $enrolled_employees_result[0]->count ?? 0;

// 2. Count active policies
$active_policies_result = Database::fetchAll("SELECT COUNT(*) as count FROM policies WHERE status = 'active'");
$active_policies = $active_policies_result[0]->count ?? 0;

// 3. Calculate coverage rate (percentage of employees with at least one benefit)
$total_employees_result = Database::fetchAll('SELECT COUNT(*) as count FROM employees');
$total_employees = $total_employees_result[0]->count ?? 1; // Avoid division by zero

$coverage_rate = $total_employees > 0 ? round(($enrolled_employees / $total_employees) * 100) : 0;

// Additional useful stats you could add:
// Count active benefits
$active_benefits_result = Database::fetchAll("SELECT COUNT(*) as count FROM benefits WHERE status = 'active'");
$active_benefits = $active_benefits_result[0]->count ?? 0;

// Count pending enrollments
$pending_enrollments_result = Database::fetchAll("SELECT COUNT(*) as count FROM benefit_enrollment WHERE status = 'pending'");
$pending_enrollments = $pending_enrollments_result[0]->count ?? 0;

// Count upcoming renewals (within next 30 days)
$upcoming_renewals_result = Database::fetchAll("SELECT COUNT(*) as count FROM benefit_enrollment WHERE end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND status = 'active'");
$upcoming_renewals = $upcoming_renewals_result[0]->count ?? 0;

// NEW: Chart Data Queries

// 1. Plan Distribution by Benefit Type
$plan_distribution = Database::fetchAll('
    SELECT 
        benefit_type,
        COUNT(*) as count
    FROM benefits 
    WHERE status = "active"
    GROUP BY benefit_type
    ORDER BY count DESC
');

// Prepare plan distribution data for chart
$plan_labels = [];
$plan_data = [];
$plan_colors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#8b5cf6'];

foreach ($plan_distribution as $plan) {
    $plan_labels[] = ucfirst($plan->benefit_type);
    $plan_data[] = $plan->count;
}

// If no data, use defaults
if (empty($plan_labels)) {
    $plan_labels = ['Health Insurance', 'Retirement', 'Wellness', 'Other'];
    $plan_data = [0, 0, 0, 0];
}

// 2. Enrollment Status by Benefit
$enrollment_status = Database::fetchAll('
    SELECT 
        b.benefit_name,
        COUNT(DISTINCT eb.employee_id) as enrolled_count,
        (SELECT COUNT(*) FROM employees) as total_employees
    FROM benefits b
    LEFT JOIN employee_benefits eb ON b.id = eb.benefit_id
    WHERE b.status = "active"
    GROUP BY b.id, b.benefit_name
    ORDER BY enrolled_count DESC
    LIMIT 5
');

// Prepare enrollment status data for chart
$enrollment_labels = [];
$enrollment_data = [];

foreach ($enrollment_status as $status) {
    $enrollment_labels[] = $status->benefit_name;
    $rate = ($status->total_employees > 0) ? round(($status->enrolled_count / $status->total_employees) * 100) : 0;
    $enrollment_data[] = $rate;
}

// If no data, use defaults
if (empty($enrollment_labels)) {
    $enrollment_labels = ['Health', 'Dental', 'Vision', '401(k)', 'Wellness'];
    $enrollment_data = [0, 0, 0, 0, 0];
}

// 3. Additional Chart: Monthly Enrollment Trend
$monthly_enrollments = Database::fetchAll('
    SELECT 
        DATE_FORMAT(be.created_at, "%Y-%m") as month,
        COUNT(DISTINCT be.id) as enrollment_count
    FROM benefit_enrollment be
    WHERE be.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(be.created_at, "%Y-%m")
    ORDER BY month
');

// 4. Department-wise Enrollment
$department_enrollment = Database::fetchAll('
    SELECT 
        COALESCE(d.name, "No Department") AS department_name,
        COUNT(DISTINCT eb.employee_id) as enrolled_count,
        COUNT(DISTINCT e.id) as total_employees
    FROM employees e
    LEFT JOIN employee_benefits eb ON e.id = eb.employee_id
    LEFT JOIN departments d ON e.department_id = d.id
    GROUP BY d.name
    ORDER BY enrolled_count DESC
    LIMIT 5
');

// Simple dd() helper for debugging: dumps variable and exits
if (!function_exists('dd')) {
    function dd($var)
    {
        echo '<pre style="text-align:left;">';
        var_dump($var);
        echo '</pre>';
        exit;
    }
}
