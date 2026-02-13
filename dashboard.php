<?php
session_start();
include("connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die("❌ Connection not found for $db_name");
}
$conn = $connections[$db_name];

// ------------------------------------------------------------
//  FETCH REAL DATA FROM YOUR TABLES – FIXED GROUP BY ISSUES
// ------------------------------------------------------------

// --- 1. EMPLOYEE COUNTS & TURNOVER ---
$emp_total   = $conn->query("SELECT COUNT(*) as cnt FROM employees")->fetch_assoc()['cnt'] ?? 0;
$emp_active  = $conn->query("SELECT COUNT(*) as cnt FROM employees WHERE employment_status = 'active'")->fetch_assoc()['cnt'] ?? 0;
$emp_sep     = $conn->query("SELECT COUNT(*) as cnt FROM employees WHERE employment_status = 'separated'")->fetch_assoc()['cnt'] ?? 0;
$turnover_rate = $emp_total > 0 ? round(($emp_sep / $emp_total) * 100, 1) : 0;

// --- 2. DEPARTMENT DISTRIBUTION ---
$dept_dist = ['labels' => [], 'data' => []];
$dept_query = "SELECT d.name, COUNT(e.id) as emp_count 
               FROM departments d 
               LEFT JOIN employees e ON d.id = e.department_id 
               WHERE d.active = 1 
               GROUP BY d.id";
$result = $conn->query($dept_query);
while ($row = $result->fetch_assoc()) {
    $dept_dist['labels'][] = $row['name'];
    $dept_dist['data'][]   = (int)$row['emp_count'];
}

// --- 3. LABOR COST BY DEPARTMENT (latest payroll period) ---
$labor_cost = ['labels' => [], 'data' => []];
$labor_query = "SELECT d.name, SUM(p.net_pay) as cost
                FROM payroll p
                JOIN employees e ON p.employee_id = e.id
                JOIN departments d ON e.department_id = d.id
                WHERE p.period = (SELECT MAX(period) FROM payroll)
                GROUP BY d.id";
$result = $conn->query($labor_query);
while ($row = $result->fetch_assoc()) {
    $labor_cost['labels'][] = $row['name'];
    $labor_cost['data'][]   = round($row['cost'] / 1000, 1);
}

// --- 4. OVERTIME TRENDS (last 12 months) ---
$overtime_trend = ['labels' => [], 'data' => []];
$ot_query = "SELECT 
                DATE_FORMAT(MAX(period), '%b') as month,
                SUM(overtime_hours) as hours
             FROM payroll
             WHERE period >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY YEAR(period), MONTH(period)
             ORDER BY YEAR(period), MONTH(period)
             LIMIT 12";
$result = $conn->query($ot_query);
while ($row = $result->fetch_assoc()) {
    $overtime_trend['labels'][] = $row['month'];
    $overtime_trend['data'][]   = (int)$row['hours'];
}

// --- 5. SALARY GRADES (min/max vs actual average salary) ---
$salary_grade = ['labels' => [], 'min' => [], 'avg' => [], 'max' => []];
$sg_query = "SELECT sg.grade_name, sg.min_salary, sg.max_salary, 
                    AVG(e.salary) as avg_salary
             FROM salary_grades sg
             LEFT JOIN employees e ON e.job = sg.position
             GROUP BY sg.id";
$result = $conn->query($sg_query);
while ($row = $result->fetch_assoc()) {
    $salary_grade['labels'][] = $row['grade_name'];
    $salary_grade['min'][]    = (int)$row['min_salary'];
    $salary_grade['max'][]    = (int)$row['max_salary'];
    $salary_grade['avg'][]    = (int)$row['avg_salary'];
}

// --- 6. BONUS DISTRIBUTION (by bonus type) ---
$bonus_dist = ['labels' => [], 'data' => []];
$bonus_query = "SELECT bonus_type, COUNT(*) as count 
                FROM bonus_plans 
                WHERE status = 'active' 
                GROUP BY bonus_type";
$result = $conn->query($bonus_query);
while ($row = $result->fetch_assoc()) {
    $bonus_dist['labels'][] = $row['bonus_type'];
    $bonus_dist['data'][]   = (int)$row['count'];
}

// --- 7. ALLOWANCE TYPES ---
$allowance_dist = ['labels' => [], 'data' => []];
$al_query = "SELECT allowance_type, COUNT(*) as cnt 
             FROM allowances 
             WHERE status = 'active' 
             GROUP BY allowance_type";
$result = $conn->query($al_query);
while ($row = $result->fetch_assoc()) {
    $allowance_dist['labels'][] = $row['allowance_type'];
    $allowance_dist['data'][]   = (int)$row['cnt'];
}

// --- 8. BENEFITS CATEGORY DISTRIBUTION ---
$benefit_dist = ['labels' => [], 'data' => []];
$ben_query = "SELECT benefit_type, COUNT(*) as cnt 
              FROM benefits 
              WHERE status = 'active' 
              GROUP BY benefit_type";
$result = $conn->query($ben_query);
while ($row = $result->fetch_assoc()) {
    $benefit_dist['labels'][] = $row['benefit_type'];
    $benefit_dist['data'][]   = (int)$row['cnt'];
}

// --- 9. NET PAY HISTOGRAM ---
$netpay_histo = ['labels' => [], 'data' => []];
$histo_query = "SELECT 
                  CASE 
                    WHEN net_pay < 15000 THEN '<15K'
                    WHEN net_pay BETWEEN 15000 AND 19999 THEN '15K-20K'
                    WHEN net_pay BETWEEN 20000 AND 24999 THEN '20K-25K'
                    WHEN net_pay BETWEEN 25000 AND 29999 THEN '25K-30K'
                    ELSE '30K+'
                  END as salary_range,
                  COUNT(*) as count
                FROM payroll
                GROUP BY salary_range
                ORDER BY MIN(net_pay)";
$result = $conn->query($histo_query);
while ($row = $result->fetch_assoc()) {
    $netpay_histo['labels'][] = $row['salary_range'];
    $netpay_histo['data'][]   = (int)$row['count'];
}

// --- 10. TABLE HISTORY – MONTHLY RECORD CREATION ---
$history_raw = [];
$history_query = "
    (SELECT 'Employees' as source, DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as cnt 
     FROM employees 
     GROUP BY DATE_FORMAT(created_at, '%Y-%m'))
    UNION ALL
    (SELECT 'Payroll', DATE_FORMAT(created_at, '%Y-%m'), COUNT(*) 
     FROM payroll 
     GROUP BY DATE_FORMAT(created_at, '%Y-%m'))
    UNION ALL
    (SELECT 'Allowances', DATE_FORMAT(created_at, '%Y-%m'), COUNT(*) 
     FROM allowances 
     GROUP BY DATE_FORMAT(created_at, '%Y-%m'))
    ORDER BY month DESC 
    LIMIT 12";
$result = $conn->query($history_query);
$history_by_month = [];
while ($row = $result->fetch_assoc()) {
    $month = $row['month'];
    $source = $row['source'];
    $cnt = (int)$row['cnt'];
    if (!isset($history_by_month[$month])) {
        $history_by_month[$month] = ['Employees' => 0, 'Payroll' => 0, 'Allowances' => 0];
    }
    $history_by_month[$month][$source] = $cnt;
}
ksort($history_by_month);
$history_months = array_keys($history_by_month);
$history_employees = array_column($history_by_month, 'Employees');
$history_payroll   = array_column($history_by_month, 'Payroll');
$history_allowances = array_column($history_by_month, 'Allowances');

// --- 11. TENURE DISTRIBUTION ---
$tenure = ['labels' => [], 'data' => []];
$tenure_query = "SELECT 
                  CASE 
                    WHEN TIMESTAMPDIFF(YEAR, hire_date, CURDATE()) < 1 THEN '<1 year'
                    WHEN TIMESTAMPDIFF(YEAR, hire_date, CURDATE()) BETWEEN 1 AND 2 THEN '1-2 years'
                    WHEN TIMESTAMPDIFF(YEAR, hire_date, CURDATE()) BETWEEN 3 AND 5 THEN '3-5 years'
                    ELSE '5+ years'
                  END as tenure_range,
                  COUNT(*) as count
                FROM employees
                WHERE hire_date IS NOT NULL
                GROUP BY tenure_range";
$result = $conn->query($tenure_query);
while ($row = $result->fetch_assoc()) {
    $tenure['labels'][] = $row['tenure_range'];
    $tenure['data'][]   = (int)$row['count'];
}

// --- 12. GENDER DISTRIBUTION ---
$gender_dist = ['labels' => [], 'data' => []];
$gender_query = "SELECT COALESCE(gender, 'Not Specified') as gender, COUNT(*) as cnt 
                 FROM employees 
                 GROUP BY gender";
$result = $conn->query($gender_query);
while ($row = $result->fetch_assoc()) {
    $gender_dist['labels'][] = $row['gender'];
    $gender_dist['data'][]   = (int)$row['cnt'];
}

// --- 13. EMPLOYMENT STATUS ---
$status_counts = ['labels' => [], 'data' => []];
$status_res = $conn->query("SELECT COALESCE(employment_status, 'Unknown') as status, COUNT(*) as cnt 
                            FROM employees 
                            GROUP BY employment_status");
while ($row = $status_res->fetch_assoc()) {
    $status_counts['labels'][] = $row['status'];
    $status_counts['data'][]   = (int)$row['cnt'];
}

// --- 14. AVERAGE SALARY PER DEPARTMENT ---
$dept_salary = ['labels' => [], 'data' => []];
$ds_query = "SELECT d.name, AVG(e.salary) as avg_sal 
             FROM employees e 
             JOIN departments d ON e.department_id = d.id 
             GROUP BY d.id";
$ds_res = $conn->query($ds_query);
while ($row = $ds_res->fetch_assoc()) {
    $dept_salary['labels'][] = $row['name'];
    $dept_salary['data'][]   = round($row['avg_sal'], 0);
}

// --- 15. ADDITIONAL STATS FOR 20 CARDS ---
$payroll_latest   = $conn->query("SELECT SUM(net_pay) as total, AVG(net_pay) as avg, MAX(net_pay) as max, MIN(net_pay) as min, SUM(overtime_hours) as ot FROM payroll WHERE period = (SELECT MAX(period) FROM payroll)")->fetch_assoc();
$total_payroll    = $payroll_latest['total'] ?? 0;
$avg_net_pay      = $payroll_latest['avg'] ?? 0;
$max_net_pay      = $payroll_latest['max'] ?? 0;
$min_net_pay      = $payroll_latest['min'] ?? 0;
$total_overtime   = $payroll_latest['ot'] ?? 0;

$dept_count       = $conn->query("SELECT COUNT(*) as c FROM departments WHERE active = 1")->fetch_assoc()['c'] ?? 0;
$salary_grade_cnt = $conn->query("SELECT COUNT(*) as c FROM salary_grades")->fetch_assoc()['c'] ?? 0;
$active_bonus     = $conn->query("SELECT COUNT(*) as c FROM bonus_plans WHERE status = 'active'")->fetch_assoc()['c'] ?? 0;
$active_allow     = $conn->query("SELECT COUNT(*) as c FROM allowances WHERE status = 'active'")->fetch_assoc()['c'] ?? 0;
$active_benefits  = $conn->query("SELECT COUNT(*) as c FROM benefits WHERE status = 'active'")->fetch_assoc()['c'] ?? 0;

$new_hires_30     = $conn->query("SELECT COUNT(*) as c FROM employees WHERE hire_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch_assoc()['c'] ?? 0;
$separations_30   = $conn->query("SELECT COUNT(*) as c FROM employees WHERE employment_status = 'separated' AND updated_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch_assoc()['c'] ?? 0;

$avg_tenure_years = $conn->query("SELECT AVG(TIMESTAMPDIFF(YEAR, hire_date, CURDATE())) as avg_tenure FROM employees WHERE hire_date IS NOT NULL")->fetch_assoc()['avg_tenure'] ?? 0;

$male_count       = $conn->query("SELECT COUNT(*) as c FROM employees WHERE gender = 'Male'")->fetch_assoc()['c'] ?? 0;
$female_count     = $conn->query("SELECT COUNT(*) as c FROM employees WHERE gender = 'Female'")->fetch_assoc()['c'] ?? 0;

$with_contract    = $conn->query("SELECT COUNT(*) as c FROM employees WHERE has_contract = 1")->fetch_assoc()['c'] ?? 0;
$without_contract = $emp_total - $with_contract;

$bonus_pool_total = $conn->query("SELECT 
    SUM(
        CASE 
            WHEN amount_or_percentage NOT LIKE '%\\%' 
            THEN CAST(REPLACE(REPLACE(amount_or_percentage, '₱', ''), ',', '') AS DECIMAL(10,2))
            ELSE 0 
        END
    ) as total 
    FROM bonus_plans 
    WHERE status = 'active'")->fetch_assoc()['total'] ?? 0;

$allowance_budget = $conn->query("SELECT 
    SUM(
        amount * 
        CASE frequency 
            WHEN 'daily' THEN 365 
            WHEN 'weekly' THEN 52 
            WHEN 'monthly' THEN 12 
            WHEN 'quarterly' THEN 4 
            WHEN 'annual' THEN 1 
            ELSE 12 
        END
    ) as total 
    FROM allowances 
    WHERE status = 'active'")->fetch_assoc()['total'] ?? 0;

// Helper to format currency
function format_currency($val) {
    if ($val === '' || $val === null || $val == 0) return '₱0';
    return '₱' . number_format((float)$val, 0);
}
function format_number($val) {
    return number_format((float)$val, 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Analytics Dashboard (Live Data) - 20 Stats + Export</title>
    <?php include 'INCLUDES/header.php'; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .chart-toolbar {
            display: flex;
            gap: 0.5rem;
            margin-left: auto;
        }
        .chart-toolbar button {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .chart-toolbar button:hover {
            background: rgba(0,0,0,0.05);
        }
        [data-tooltip] {
            position: relative;
            cursor: help;
        }
        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 130%;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 10;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }
        [data-tooltip]:hover:before {
            opacity: 1;
        }
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-base-100 bg-white min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar (no-print) -->
        <?php include 'INCLUDES/sidebar.php'; ?>

        <div class="flex flex-col flex-1 overflow-auto">
            <!-- Navbar (no-print) -->
            <?php include 'INCLUDES/navbar.php'; ?>

            <main class="flex-1 p-6">
                <!-- GLOBAL PRINT BUTTON (no-print) -->
                <div class="mb-4 no-print flex justify-end">
                    <button onclick="window.print()" class="flex items-center bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-lg text-white transition-colors shadow-lg">
                        <i data-lucide="printer" class="mr-2 w-4 h-4"></i>
                        Print / Save as PDF
                    </button>
                </div>

                <!-- HR Analytics Dashboard -->
                <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center gap-4 mb-6">
                        <h2 class="flex items-center font-bold text-gray-800 text-2xl">
                            <span class="bg-indigo-100/50 mr-3 p-2 rounded-lg text-indigo-600">
                                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            </span>
                            HR Analytics Dashboard – 20 Live Metrics
                        </h2>
                        <select class="bg-white px-4 py-2 border border-gray-300 rounded-lg text-gray-700 no-print">
                            <option>Last 30 Days</option>
                            <option>Last Quarter</option>
                            <option>Year to Date</option>
                            <option>Last Year</option>
                        </select>
                    </div>

                    <!-- 20 STAT CARDS (4x5) -->
                    <div class="gap-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                        <!-- 1. Total Employees -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Total Employees</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $emp_total ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs"><?= $emp_active ?> active</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="users" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 2. Turnover Rate -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Turnover Rate</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $turnover_rate ?>%</h3>
                                        <p class="mt-1 text-gray-500 text-xs"><?= $emp_sep ?> separated</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="repeat" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 3. Payroll Periods -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Payroll Periods</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $payroll_periods ?? 0 ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">processed</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="clock" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 4. Active Benefits -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Active Benefits</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $active_benefits ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">+<?= $active_allow ?> allowances</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="award" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 5. Total Payroll (Latest) -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Payroll Cost (latest)</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= format_currency($total_payroll) ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">net pay</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="credit-card" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 6. Average Net Pay -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Avg Net Pay</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= format_currency($avg_net_pay) ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">per employee</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 7. Highest Net Pay -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Highest Net Pay</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= format_currency($max_net_pay) ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">latest period</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="arrow-up" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 8. Lowest Net Pay -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Lowest Net Pay</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= format_currency($min_net_pay) ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">latest period</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="arrow-down" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 9. Overtime Hours -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Overtime (latest)</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= format_number($total_overtime) ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">total hours</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="clock" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 10. Departments -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Departments</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $dept_count ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">active</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="building-2" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 11. Salary Grades -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Salary Grades</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $salary_grade_cnt ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">defined</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="bar-chart" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 12. Active Bonus Plans -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Active Bonus Plans</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $active_bonus ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">bonus plans</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="gift" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 13. Active Allowances -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Active Allowances</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $active_allow ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">allowance types</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="credit-card" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 14. New Hires (30d) -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">New Hires (30d)</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $new_hires_30 ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">joined</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="user-plus" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 15. Separations (30d) -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Separations (30d)</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $separations_30 ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">left</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="user-minus" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 16. Avg Tenure -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Avg Tenure</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= round($avg_tenure_years, 1) ?> yrs</h3>
                                        <p class="mt-1 text-gray-500 text-xs">employees</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="calendar" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 17. Male Employees -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Male</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $male_count ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">employees</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="user" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 18. Female Employees -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Female</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $female_count ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">employees</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="user" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 19. Bonus Pool -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Bonus Pool</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= format_currency($bonus_pool_total) ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">fixed amount</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="package" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- 20. Allowance Budget -->
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Allowance Budget</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= format_currency($allowance_budget) ?></h3>
                                        <p class="mt-1 text-gray-500 text-xs">annual</p>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="wallet" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 1: EMPLOYEE DEMOGRAPHICS -->
                    <div class="mb-8">
                        <h3 class="flex items-center mb-4 font-bold text-gray-800 text-xl">
                            <span class="bg-blue-100 mr-2 p-2 rounded-lg text-blue-600"><i data-lucide="users" class="w-5 h-5"></i></span>
                            Employee Demographics
                        </h3>
                        <div class="gap-6 grid grid-cols-1 lg:grid-cols-3">
                            <?php
                            $chart_descriptions = [
                                'genderPieChart' => 'Distribution of employees by gender (from employees table)',
                                'deptPieChart' => 'Number of employees per department',
                                'tenureHistogram' => 'Years of service grouped into ranges',
                                'laborCostChart' => 'Total net pay per department (latest period)',
                                'overtimeTrendsChart' => 'Monthly overtime hours over the last 12 months',
                                'netPayHistogram' => 'How many employees fall into each net pay bracket',
                                'allowancePieChart' => 'Active allowances grouped by type',
                                'benefitPieChart' => 'Active benefits grouped by category',
                                'bonusPieChart' => 'Active bonus plans by bonus type',
                                'salaryBenchmarkChart' => 'Min, actual average and max salary per grade',
                                'historyTrendChart' => 'Record creation over time (Employees, Payroll, Allowances)',
                                'tenurePieChart' => 'Same as histogram, displayed as pie',
                                'employmentStatusChart' => 'Active vs separated vs other statuses',
                                'departmentSalaryChart' => 'Average monthly salary per department'
                            ];
                            ?>
                            <!-- Gender Pie -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Gender Distribution</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['genderPieChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('genderPieChart')" title="Download PNG"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('genderPieChart', 'Gender Distribution')" title="Print Chart"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="genderPieChart"></canvas>
                            </div>
                            <!-- Department Pie -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Department Distribution</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['deptPieChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('deptPieChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('deptPieChart', 'Department Distribution')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="deptPieChart"></canvas>
                            </div>
                            <!-- Tenure Histogram -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Tenure Histogram</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['tenureHistogram'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('tenureHistogram')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('tenureHistogram', 'Tenure Histogram')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="tenureHistogram"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: PAYROLL ANALYTICS -->
                    <div class="mb-8">
                        <h3 class="flex items-center mb-4 font-bold text-gray-800 text-xl">
                            <span class="bg-green-100 mr-2 p-2 rounded-lg text-green-600"><i data-lucide="credit-card" class="w-5 h-5"></i></span>
                            Payroll Management
                        </h3>
                        <div class="gap-6 grid grid-cols-1 lg:grid-cols-2">
                            <!-- Labor Cost -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Labor Cost by Department</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['laborCostChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('laborCostChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('laborCostChart', 'Labor Cost by Department')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="laborCostChart"></canvas>
                            </div>
                            <!-- Overtime Trends -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Overtime Trends (12 months)</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['overtimeTrendsChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('overtimeTrendsChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('overtimeTrendsChart', 'Overtime Trends')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="overtimeTrendsChart"></canvas>
                            </div>
                            <!-- Net Pay Histogram (full width) -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl lg:col-span-2">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Net Pay Distribution</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['netPayHistogram'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('netPayHistogram')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('netPayHistogram', 'Net Pay Distribution')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="netPayHistogram"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: COMPENSATION & BENEFITS -->
                    <div class="mb-8">
                        <h3 class="flex items-center mb-4 font-bold text-gray-800 text-xl">
                            <span class="bg-purple-100 mr-2 p-2 rounded-lg text-purple-600"><i data-lucide="gift" class="w-5 h-5"></i></span>
                            Compensation & Benefits
                        </h3>
                        <div class="gap-6 grid grid-cols-1 lg:grid-cols-3">
                            <!-- Allowance Pie -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Allowance Types</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['allowancePieChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('allowancePieChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('allowancePieChart', 'Allowance Types')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="allowancePieChart"></canvas>
                            </div>
                            <!-- Benefit Pie -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Benefit Categories</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['benefitPieChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('benefitPieChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('benefitPieChart', 'Benefit Categories')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="benefitPieChart"></canvas>
                            </div>
                            <!-- Bonus Pie -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Bonus Plans by Type</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['bonusPieChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('bonusPieChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('bonusPieChart', 'Bonus Plans by Type')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="bonusPieChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: SALARY STRUCTURE -->
                    <div class="mb-8">
                        <h3 class="flex items-center mb-4 font-bold text-gray-800 text-xl">
                            <span class="bg-yellow-100 mr-2 p-2 rounded-lg text-yellow-600"><i data-lucide="trending-up" class="w-5 h-5"></i></span>
                            Salary Grades & Benchmark
                        </h3>
                        <div class="gap-6 grid grid-cols-1 lg:grid-cols-2">
                            <!-- Salary Benchmark -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Salary Ranges vs Actual</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['salaryBenchmarkChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('salaryBenchmarkChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('salaryBenchmarkChart', 'Salary Benchmark')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="salaryBenchmarkChart"></canvas>
                            </div>
                            <!-- Dept Avg Salary -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Avg Salary by Department</h4>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['departmentSalaryChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('departmentSalaryChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('departmentSalaryChart', 'Avg Salary by Dept')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="departmentSalaryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 5: TABLE HISTORY -->
                    <div class="mb-8">
                        <h3 class="flex items-center mb-4 font-bold text-gray-800 text-xl">
                            <span class="bg-red-100 mr-2 p-2 rounded-lg text-red-600"><i data-lucide="database" class="w-5 h-5"></i></span>
                            Record Creation Trends
                        </h3>
                        <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-semibold text-gray-800">Monthly Records Created</h4>
                                <div class="flex items-center gap-2">
                                    <span data-tooltip="<?= $chart_descriptions['historyTrendChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                    <div class="chart-toolbar no-print">
                                        <button onclick="downloadChart('historyTrendChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                        <button onclick="printChart('historyTrendChart', 'Record Creation Trends')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                    </div>
                                </div>
                            </div>
                            <canvas id="historyTrendChart"></canvas>
                        </div>
                    </div>

                    <!-- SECTION 6: ADDITIONAL INSIGHTS -->
                    <div class="gap-6 grid grid-cols-1 lg:grid-cols-2 mb-6">
                        <!-- Tenure Pie -->
                        <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-semibold text-gray-800">Tenure Distribution</h4>
                                <div class="flex items-center gap-2">
                                    <span data-tooltip="<?= $chart_descriptions['tenurePieChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                    <div class="chart-toolbar no-print">
                                        <button onclick="downloadChart('tenurePieChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                        <button onclick="printChart('tenurePieChart', 'Tenure Distribution')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                    </div>
                                </div>
                            </div>
                            <canvas id="tenurePieChart"></canvas>
                        </div>
                        <!-- Employment Status -->
                        <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-semibold text-gray-800">Employment Status</h4>
                                <div class="flex items-center gap-2">
                                    <span data-tooltip="<?= $chart_descriptions['employmentStatusChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                    <div class="chart-toolbar no-print">
                                        <button onclick="downloadChart('employmentStatusChart')"><i data-lucide="download" class="w-4 h-4"></i></button>
                                        <button onclick="printChart('employmentStatusChart', 'Employment Status')"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                    </div>
                                </div>
                            </div>
                            <canvas id="employmentStatusChart"></canvas>
                        </div>
                    </div>

                </div> <!-- end dashboard -->
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // ------------------------------------------------------------
        // CHART INITIALIZATION (store instances for export)
        // ------------------------------------------------------------
        window.charts = {};

        // --- 1. GENDER PIE ---
        charts.genderPieChart = new Chart(document.getElementById('genderPieChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode($gender_dist['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($gender_dist['data'] ?? []) ?>,
                    backgroundColor: ['#3b82f6', '#f472b6', '#9ca3af'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // --- 2. DEPARTMENT PIE ---
        charts.deptPieChart = new Chart(document.getElementById('deptPieChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($dept_dist['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($dept_dist['data'] ?? []) ?>,
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#6b7280'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // --- 3. TENURE HISTOGRAM ---
        charts.tenureHistogram = new Chart(document.getElementById('tenureHistogram'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($tenure['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Employees',
                    data: <?= json_encode($tenure['data'] ?? []) ?>,
                    backgroundColor: '#8b5cf6',
                    borderRadius: 4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // --- 4. LABOR COST ---
        charts.laborCostChart = new Chart(document.getElementById('laborCostChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($labor_cost['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Labor Cost (₱K)',
                    data: <?= json_encode($labor_cost['data'] ?? []) ?>,
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: '₱ Thousands' } } } }
        });

        // --- 5. OVERTIME TRENDS ---
        charts.overtimeTrendsChart = new Chart(document.getElementById('overtimeTrendsChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode($overtime_trend['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Overtime Hours',
                    data: <?= json_encode($overtime_trend['data'] ?? []) ?>,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'Hours' } } } }
        });

        // --- 6. NET PAY HISTOGRAM ---
        charts.netPayHistogram = new Chart(document.getElementById('netPayHistogram'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($netpay_histo['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Number of Employees',
                    data: <?= json_encode($netpay_histo['data'] ?? []) ?>,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // --- 7. ALLOWANCE PIE ---
        charts.allowancePieChart = new Chart(document.getElementById('allowancePieChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode($allowance_dist['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($allowance_dist['data'] ?? []) ?>,
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // --- 8. BENEFIT PIE ---
        charts.benefitPieChart = new Chart(document.getElementById('benefitPieChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode($benefit_dist['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($benefit_dist['data'] ?? []) ?>,
                    backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#10b981'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // --- 9. BONUS PIE ---
        charts.bonusPieChart = new Chart(document.getElementById('bonusPieChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode($bonus_dist['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($bonus_dist['data'] ?? []) ?>,
                    backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // --- 10. SALARY BENCHMARK ---
        charts.salaryBenchmarkChart = new Chart(document.getElementById('salaryBenchmarkChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($salary_grade['labels'] ?? []) ?>,
                datasets: [
                    { label: 'Min Salary', data: <?= json_encode($salary_grade['min'] ?? []) ?>, backgroundColor: '#9ca3af' },
                    { label: 'Avg Salary', data: <?= json_encode($salary_grade['avg'] ?? []) ?>, backgroundColor: '#3b82f6' },
                    { label: 'Max Salary', data: <?= json_encode($salary_grade['max'] ?? []) ?>, backgroundColor: '#10b981' }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'Salary (₱)' } } } }
        });

        // --- 11. HISTORY TREND ---
        charts.historyTrendChart = new Chart(document.getElementById('historyTrendChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode($history_months) ?>,
                datasets: [
                    { label: 'Employees', data: <?= json_encode($history_employees) ?>, borderColor: '#3b82f6', tension: 0.3 },
                    { label: 'Payroll', data: <?= json_encode($history_payroll) ?>, borderColor: '#10b981', tension: 0.3 },
                    { label: 'Allowances', data: <?= json_encode($history_allowances) ?>, borderColor: '#f59e0b', tension: 0.3 }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'Records Created' } } } }
        });

        // --- 12. TENURE PIE ---
        charts.tenurePieChart = new Chart(document.getElementById('tenurePieChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode($tenure['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($tenure['data'] ?? []) ?>,
                    backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // --- 13. EMPLOYMENT STATUS ---
        charts.employmentStatusChart = new Chart(document.getElementById('employmentStatusChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($status_counts['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($status_counts['data'] ?? []) ?>,
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#9ca3af'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // --- 14. DEPARTMENT AVG SALARY ---
        charts.departmentSalaryChart = new Chart(document.getElementById('departmentSalaryChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($dept_salary['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Average Salary (₱)',
                    data: <?= json_encode($dept_salary['data'] ?? []) ?>,
                    backgroundColor: '#8b5cf6',
                    borderRadius: 4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // ------------------------------------------------------------
        // EXPORT FUNCTIONS
        // ------------------------------------------------------------
        window.downloadChart = function(chartId) {
            const chart = charts[chartId];
            if (!chart) return;
            const link = document.createElement('a');
            link.download = chartId + '.png';
            link.href = chart.toBase64Image();
            link.click();
        };

        window.printChart = function(chartId, title) {
            const chart = charts[chartId];
            if (!chart) return;
            const win = window.open('');
            win.document.write('<img src="' + chart.toBase64Image() + '" style="max-width:100%;">');
            win.document.title = title;
            win.print();
        };

        // Re-run lucide for dynamic icons
        lucide.createIcons();
    </script>
</body>
</html>