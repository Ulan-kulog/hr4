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
//  HANDLE AI INSIGHTS GENERATION (AJAX)
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_insights'])) {
    header('Content-Type: application/json');

    // Fetch necessary data for insights
    $dept_salary = ['labels' => [], 'data' => []];
    $ds_query = "SELECT d.name, COALESCE(AVG(NULLIF(e.salary, 0)), 0) AS avg_sal
                 FROM departments d
                 LEFT JOIN employees e ON e.department_id = d.id
                 GROUP BY d.id";
    $result = $conn->query($ds_query);
    while ($row = $result->fetch_assoc()) {
        $dept_salary['labels'][] = $row['name'];
        $dept_salary['data'][] = round($row['avg_sal'], 0);
    }

    $total_employees = $conn->query("SELECT COUNT(*) as cnt FROM employees")->fetch_assoc()['cnt'] ?? 0;
    $active_employees = $conn->query("SELECT COUNT(*) as cnt FROM employees WHERE employment_status = 'active'")->fetch_assoc()['cnt'] ?? 0;
    $separated_employees = $conn->query("SELECT COUNT(*) as cnt FROM employees WHERE employment_status = 'separated'")->fetch_assoc()['cnt'] ?? 0;

    // Generate insights array
    $insights = [];

    // 1. Overall workforce
    $insights[] = "Total employees: $total_employees (Active: $active_employees, Separated: $separated_employees).";

    // 2. Department with highest/lowest average salary
    if (!empty($dept_salary['data'])) {
        $max_avg = max($dept_salary['data']);
        $max_dept = $dept_salary['labels'][array_search($max_avg, $dept_salary['data'])];
        $insights[] = "Highest average salary is in '$max_dept' department: ₱" . number_format($max_avg) . ".";

        $min_avg = min($dept_salary['data']);
        $min_dept = $dept_salary['labels'][array_search($min_avg, $dept_salary['data'])];
        $insights[] = "Lowest average salary is in '$min_dept' department: ₱" . number_format($min_avg) . ".";
    }

    // 3. Gender distribution
    $gender_counts = [];
    $gender_res = $conn->query("SELECT gender, COUNT(*) as cnt FROM employees GROUP BY gender");
    while ($row = $gender_res->fetch_assoc()) {
        $gender_counts[$row['gender'] ?: 'Not Specified'] = $row['cnt'];
    }
    if (!empty($gender_counts)) {
        $insights[] = "Gender breakdown: " . implode(', ', array_map(function($k, $v) { return "$k: $v"; }, array_keys($gender_counts), $gender_counts));
    }

    // 4. Age distribution
    $age_parts = [];
    $age_res = $conn->query("SELECT 
        CASE 
            WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 25 THEN '<25'
            WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 25 AND 34 THEN '25-34'
            WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 35 AND 44 THEN '35-44'
            WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 45 AND 54 THEN '45-54'
            ELSE '55+'
        END as age_group, COUNT(*) as count
        FROM employees WHERE date_of_birth IS NOT NULL
        GROUP BY age_group");
    while ($row = $age_res->fetch_assoc()) {
        $age_parts[] = $row['age_group'] . ': ' . $row['count'];
    }
    if (!empty($age_parts)) {
        $insights[] = "Age distribution: " . implode(', ', $age_parts);
    }

    // 5. Overtime trend
    $ot_res = $conn->query("SELECT SUM(overtime_hours) as total_ot FROM payroll WHERE period >= DATE_SUB(NOW(), INTERVAL 12 MONTH)");
    $total_ot = $ot_res->fetch_assoc()['total_ot'] ?? 0;
    if ($total_ot > 0) {
        $insights[] = "Total overtime hours in the last 12 months: " . number_format($total_ot) . " hours.";
    } else {
        $insights[] = "No overtime data recorded in the last 12 months.";
    }

    // 6. Allowance types
    $allowance_count = $conn->query("SELECT COUNT(DISTINCT allowance_type) as cnt FROM allowances WHERE status = 'active'")->fetch_assoc()['cnt'] ?? 0;
    $insights[] = "Active allowance types: $allowance_count.";

    // 7. Bonus plans
    $bonus_count = $conn->query("SELECT COUNT(DISTINCT bonus_type) as cnt FROM bonus_plans WHERE status = 'active'")->fetch_assoc()['cnt'] ?? 0;
    $insights[] = "Active bonus plan types: $bonus_count.";

    // Return insights as JSON (no database storage)
    echo json_encode(['success' => true, 'insights' => $insights]);
    exit;
}

// ------------------------------------------------------------
//  FETCH REAL DATA FROM YOUR TABLES (same as before)
// ------------------------------------------------------------
$emp_total   = $conn->query("SELECT COUNT(*) as cnt FROM employees")->fetch_assoc()['cnt'] ?? 0;
$emp_active  = $conn->query("SELECT COUNT(*) as cnt FROM employees WHERE employment_status = 'active'")->fetch_assoc()['cnt'] ?? 0;
$emp_sep     = $conn->query("SELECT COUNT(*) as cnt FROM employees WHERE employment_status = 'separated'")->fetch_assoc()['cnt'] ?? 0;

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

$gender_dist = ['labels' => [], 'data' => []];
$gender_query = "SELECT COALESCE(gender, 'Not Specified') as gender, COUNT(*) as cnt 
                 FROM employees 
                 GROUP BY gender";
$result = $conn->query($gender_query);
while ($row = $result->fetch_assoc()) {
    $gender_dist['labels'][] = $row['gender'];
    $gender_dist['data'][]   = (int)$row['cnt'];
}

$age_dist = ['labels' => [], 'data' => []];
$age_query = "SELECT 
  CASE 
    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 25 THEN '<25'
    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 25 AND 34 THEN '25-34'
    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 35 AND 44 THEN '35-44'
    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 45 AND 54 THEN '45-54'
    ELSE '55+'
  END as age_group,
  COUNT(*) as count
FROM employees
WHERE date_of_birth IS NOT NULL
GROUP BY age_group
ORDER BY MIN(TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()))";
$result = $conn->query($age_query);
while ($row = $result->fetch_assoc()) {
    $age_dist['labels'][] = $row['age_group'];
    $age_dist['data'][]   = (int)$row['count'];
}

$status_counts = ['labels' => [], 'data' => []];
$status_res = $conn->query("SELECT COALESCE(employment_status, 'Unknown') as status, COUNT(*) as cnt 
                            FROM employees 
                            GROUP BY employment_status");
while ($row = $status_res->fetch_assoc()) {
    $status_counts['labels'][] = $row['status'];
    $status_counts['data'][]   = (int)$row['cnt'];
}

$dept_salary = ['labels' => [], 'data' => [], 'emp_count' => [], 'missing' => []];
$ds_query = "SELECT d.name,
       COALESCE(AVG(NULLIF(e.salary, 0)), 0) AS avg_sal,
       COUNT(e.id) AS emp_count,
       SUM(e.salary IS NULL OR e.salary = 0) AS missing_salaries
    FROM departments d
    LEFT JOIN employees e ON e.department_id = d.id
    GROUP BY d.id
    ORDER BY d.name";
$ds_res = $conn->query($ds_query);
if ($ds_res) {
    while ($row = $ds_res->fetch_assoc()) {
        $dept_salary['labels'][] = $row['name'];
        $dept_salary['data'][]   = round((float)$row['avg_sal'], 0);
        $dept_salary['emp_count'][] = (int)$row['emp_count'];
        $dept_salary['missing'][] = (int)$row['missing_salaries'];
    }
} else {
    error_log('Dept salary query failed: ' . $conn->error);
}

$dept_count       = $conn->query("SELECT COUNT(*) as c FROM departments WHERE active = 1")->fetch_assoc()['c'] ?? 0;
$salary_grade_cnt = $conn->query("SELECT COUNT(*) as c FROM salary_grades")->fetch_assoc()['c'] ?? 0;
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

// ------------------------------------------------------------
//  CHART DESCRIPTIONS
// ------------------------------------------------------------
$chart_descriptions = [
    'genderPieChart'           => 'Distribution of employees by gender.',
    'ageDistributionChart'     => 'Number of employees in each age group.',
    'employmentStatusChart'    => 'Current employment status breakdown.',
    'deptPieChart'             => 'Employee count per department.',
    'laborCostChart'           => 'Total labor cost (in thousands) by department for the latest payroll period.',
    'overtimeTrendsChart'      => 'Monthly overtime hours over the last 12 months.',
    'allowancePieChart'        => 'Active allowances grouped by type.',
    'bonusPieChart'            => 'Active bonus plans by type.',
    'salaryBenchmarkChart'     => 'Salary grade ranges (min, max) vs actual average salary.',
    'departmentSalaryChart'    => 'Average salary per department (employees with salary > 0).'
];

// Helper functions
function format_currency($val)
{
    if ($val === '' || $val === null || $val == 0) return '₱0';
    return '₱' . number_format((float)$val, 0);
}
function format_number($val)
{
    return number_format((float)$val, 0);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Analytics Dashboard - AI + Export</title>
    <?php include 'INCLUDES/header.php'; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            align-items: center;
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
            background: rgba(0, 0, 0, 0.05);
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
        .export-checkbox {
            margin-right: 0.5rem;
            accent-color: #3b82f6;
        }
        /* Styles for insights list */
        .insights-list {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin: 1rem 0;
        }
        .insights-list li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }
        /* Pulse animation for generate button */
        .btn-pulse {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
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

                <!-- HR Analytics Dashboard -->
                <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <!-- Header with AI Controls -->
                    <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center gap-4 mb-6">
                        <h2 class="flex items-center font-bold text-gray-800 text-2xl">
                            <span class="bg-indigo-100/50 mr-3 p-2 rounded-lg text-indigo-600">
                                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            </span>
                            HR Analytics Dashboard
                        </h2>
                        <div class="flex items-center gap-3 no-print">
                            <button id="aiGenerateBtn" onclick="generateAI()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg font-medium text-white transition">
                                <i data-lucide="bot" class="w-4 h-4"></i>
                                Generate AI Insights
                            </button>
                            <button onclick="exportSelectedPDF()" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg font-medium text-white transition">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Export Selected as PDF
                            </button>
                        </div>
                    </div>

                    <!-- 4 STAT CARDS (unchanged) -->
                    <div class="gap-6 grid grid-cols-2 mb-8">
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Total Employees</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $emp_total ?></h3>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="users" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="bg-white hover:bg-gray-50 shadow-2xl p-5 rounded-xl text-black">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-[#001f54] text-sm">Departments</p>
                                        <h3 class="mt-1 font-bold text-3xl"><?= $dept_count ?></h3>
                                    </div>
                                    <div class="bg-[#001f54] p-3 rounded-lg"><i data-lucide="building-2" class="w-6 h-6 text-[#F7B32B]"></i></div>
                                </div>
                            </div>
                        </div>
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
                        <div class="gap-6 grid grid-cols-1 lg:grid-cols-2">
                            <!-- Gender Pie -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-genderPieChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="genderPieChart" checked>
                                        <h4 class="font-semibold text-gray-800">Gender Distribution</h4>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['genderPieChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('genderPieChart')" title="Download PNG"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('genderPieChart', 'Gender Distribution')" title="Print Chart"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div style="max-width:800px; width:100%;">
                                    <canvas id="genderPieChart" style="width:100%; height:320px;"></canvas>
                                </div>
                            </div>
                            <!-- Age Distribution -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-ageDistributionChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="ageDistributionChart" checked>
                                        <h4 class="font-semibold text-gray-800">Age Distribution</h4>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['ageDistributionChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('ageDistributionChart')" title="Download PNG"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('ageDistributionChart', 'Age Distribution')" title="Print Chart"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="ageDistributionChart"></canvas>
                            </div>
                            <!-- Department Distribution -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-deptPieChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="deptPieChart" checked>
                                        <h4 class="font-semibold text-gray-800">Department Distribution</h4>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['deptPieChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('deptPieChart')" title="Download PNG"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('deptPieChart', 'Department Distribution')" title="Print Chart"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="deptPieChart"></canvas>
                            </div>
                            <!-- Employment Status -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-employmentStatusChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="employmentStatusChart" checked>
                                        <h4 class="font-semibold text-gray-800">Employment Status</h4>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span data-tooltip="<?= $chart_descriptions['employmentStatusChart'] ?>" class="text-gray-400 hover:text-gray-600"><i data-lucide="info" class="w-4 h-4"></i></span>
                                        <div class="chart-toolbar no-print">
                                            <button onclick="downloadChart('employmentStatusChart')" title="Download PNG"><i data-lucide="download" class="w-4 h-4"></i></button>
                                            <button onclick="printChart('employmentStatusChart', 'Employment Status')" title="Print Chart"><i data-lucide="printer" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div style="max-width:800px; width:100%;">
                                    <canvas id="employmentStatusChart" style="width:100%; height:320px;"></canvas>
                                </div>
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
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-laborCostChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="laborCostChart" checked>
                                        <h4 class="font-semibold text-gray-800">Labor Cost by Department</h4>
                                    </div>
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
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-overtimeTrendsChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="overtimeTrendsChart" checked>
                                        <h4 class="font-semibold text-gray-800">Overtime Trends (12 months)</h4>
                                    </div>
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
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-allowancePieChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="allowancePieChart" checked>
                                        <h4 class="font-semibold text-gray-800">Allowance Types</h4>
                                    </div>
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
                            <!-- Bonus Pie -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-bonusPieChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="bonusPieChart" checked>
                                        <h4 class="font-semibold text-gray-800">Bonus Plans by Type</h4>
                                    </div>
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
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-salaryBenchmarkChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="salaryBenchmarkChart" checked>
                                        <h4 class="font-semibold text-gray-800">Salary Ranges vs Actual</h4>
                                    </div>
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
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl" id="card-departmentSalaryChart">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="export-checkbox" value="departmentSalaryChart" checked>
                                        <h4 class="font-semibold text-gray-800">Avg Salary by Department</h4>
                                    </div>
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

                </div> <!-- end dashboard -->
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Chart instances storage
        window.charts = {};

        // Helper to safely create chart
        function createChart(id, config) {
            const canvas = document.getElementById(id);
            if (!canvas) {
                console.warn(`Canvas with id '${id}' not found.`);
                return null;
            }
            try {
                return new Chart(canvas, config);
            } catch (e) {
                console.error(`Failed to create chart '${id}':`, e);
                return null;
            }
        }

        // ------------------------------------------------------------
        // Initialize all charts
        // ------------------------------------------------------------
        // Gender Pie
        charts.genderPieChart = createChart('genderPieChart', {
            type: 'pie',
            data: {
                labels: <?= json_encode($gender_dist['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($gender_dist['data'] ?? []) ?>,
                    backgroundColor: ['#3b82f6', '#f472b6', '#9ca3af'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Age Distribution
        charts.ageDistributionChart = createChart('ageDistributionChart', {
            type: 'bar',
            data: {
                labels: <?= json_encode($age_dist['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Employees',
                    data: <?= json_encode($age_dist['data'] ?? []) ?>,
                    backgroundColor: '#f59e0b',
                    borderRadius: 4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Department Pie
        charts.deptPieChart = createChart('deptPieChart', {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($dept_dist['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($dept_dist['data'] ?? []) ?>,
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#6b7280'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Labor Cost
        charts.laborCostChart = createChart('laborCostChart', {
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

        // Overtime Trends
        charts.overtimeTrendsChart = createChart('overtimeTrendsChart', {
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

        // Allowance Pie
        charts.allowancePieChart = createChart('allowancePieChart', {
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

        // Bonus Pie
        charts.bonusPieChart = createChart('bonusPieChart', {
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

        // Salary Benchmark
        charts.salaryBenchmarkChart = createChart('salaryBenchmarkChart', {
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

        // Employment Status
        charts.employmentStatusChart = createChart('employmentStatusChart', {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($status_counts['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($status_counts['data'] ?? []) ?>,
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#9ca3af'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });

        // Department Salary
        charts.departmentSalaryChart = createChart('departmentSalaryChart', {
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
        // DOWNLOAD / PRINT INDIVIDUAL CHARTS
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

        // ------------------------------------------------------------
        // AI GENERATION with 10-second animated progress
        // ------------------------------------------------------------
        function generateAI() {
            const btn = document.getElementById('aiGenerateBtn');
            btn.classList.add('btn-pulse'); // Add pulse animation

            // Show SweetAlert with progress bar that fills over 10 seconds
            let progress = 0;
            Swal.fire({
                title: 'Generating AI Insights',
                html: `
                    <div style="text-align: left; margin-bottom: 15px;">
                        <p>AI is analyzing your HR data. This will take about 10 seconds.</p>
                        <ul style="list-style: disc; padding-left: 20px; margin-top: 10px;">
                            <li>Examining employee demographics</li>
                            <li>Calculating salary trends</li>
                            <li>Identifying overtime patterns</li>
                            <li>Generating actionable insights</li>
                        </ul>
                    </div>
                    <div class="progress-bar-container" style="width: 100%; background-color: #f3f3f3; border-radius: 5px; margin: 20px 0;">
                        <div id="swal-progress-bar" style="width: 0%; height: 10px; background-color: #4f46e5; border-radius: 5px; transition: width 0.3s;"></div>
                    </div>
                    <p id="swal-progress-text" style="font-size: 14px; color: #666;">0% complete</p>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    // Start progress bar animation
                    const interval = setInterval(() => {
                        progress += 10;
                        const progressBar = document.getElementById('swal-progress-bar');
                        const progressText = document.getElementById('swal-progress-text');
                        if (progressBar && progressText) {
                            progressBar.style.width = progress + '%';
                            progressText.innerText = progress + '% complete';
                        }
                        if (progress >= 100) {
                            clearInterval(interval);
                        }
                    }, 1000);

                    // Make AJAX request to generate insights (runs in background)
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'generate_insights=1'
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Wait until progress reaches 100% (or close enough) before showing results
                        const checkProgress = setInterval(() => {
                            if (progress >= 100) {
                                clearInterval(checkProgress);
                                btn.classList.remove('btn-pulse');
                                if (data.success && data.insights) {
                                    Swal.close();
                                    // Display insights
                                    const insightsList = data.insights.map(text => `<li class="mb-2">${text}</li>`).join('');
                                    const htmlContent = `
                                        <div class="text-left">
                                            <h3 class="text-lg font-bold mb-3">AI-Generated Insights</h3>
                                            <ul class="insights-list">${insightsList}</ul>
                                            <div class="mt-6 flex justify-end gap-3">
                                                <button onclick="printInsights()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                                                    <i data-lucide="printer" class="w-4 h-4"></i> Print as PDF
                                                </button>
                                                <button onclick="Swal.close()" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Close</button>
                                            </div>
                                        </div>
                                    `;
                                    Swal.fire({
                                        html: htmlContent,
                                        showConfirmButton: false,
                                        showCloseButton: true,
                                        width: '42rem',
                                        didOpen: () => lucide.createIcons()
                                    });
                                    window.lastInsights = data.insights;
                                } else {
                                    Swal.fire('Error', 'Failed to generate insights', 'error');
                                }
                            }
                        }, 100);
                    })
                    .catch(error => {
                        btn.classList.remove('btn-pulse');
                        console.error('Error:', error);
                        Swal.fire('Error', 'An error occurred while generating insights', 'error');
                    });
                }
            });
        }

        // Print insights as PDF
        window.printInsights = function() {
            if (!window.lastInsights || !window.lastInsights.length) return;

            const insightsList = window.lastInsights.map(text => `<li style="margin-bottom: 8px;">${text}</li>`).join('');
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>HR AI Insights</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 2rem; }
                        h1 { color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; }
                        ul { list-style-type: disc; padding-left: 1.5rem; }
                        li { margin-bottom: 8px; line-height: 1.5; }
                        .footer { margin-top: 2rem; font-size: 0.8rem; color: #6b7280; text-align: center; }
                    </style>
                </head>
                <body>
                    <h1>HR AI Insights</h1>
                    <p>Generated on: ${new Date().toLocaleString()}</p>
                    <ul>${insightsList}</ul>
                    <div class="footer">This report was generated by the HR Analytics Dashboard.</div>
                </body>
                </html>
            `;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        };

        // ------------------------------------------------------------
        // EXPORT SELECTED CHARTS AS PDF
        // ------------------------------------------------------------
        window.exportSelectedPDF = function() {
            const checkboxes = document.querySelectorAll('.export-checkbox:checked');
            if (checkboxes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Charts Selected',
                    text: 'Select at least one chart to export.',
                });
                return;
            }

            const chartIds = Array.from(checkboxes).map(cb => cb.value);
            const images = [];

            chartIds.forEach(id => {
                if (charts[id]) {
                    images.push({
                        id: id,
                        src: charts[id].toBase64Image(),
                        title: id.replace(/([A-Z])/g, ' $1').trim()
                    });
                }
            });

            let htmlContent = `
            <!DOCTYPE html>
            <html>
            <head><title>HR Analytics Export</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .chart-page { page-break-after: always; text-align: center; }
                img { max-width: 100%; height: auto; }
                h2 { color: #333; }
                .description { color: #666; font-size: 14px; margin-bottom: 20px; }
            </style>
            </head>
            <body>
                <div class="description">
                    <p>This document contains HR analytics charts generated from live data. Each chart represents key metrics about employees, payroll, and compensation. Use these insights for reporting and decision-making.</p>
                </div>
            `;

            images.forEach(img => {
                htmlContent += `
                <div class="chart-page">
                    <h2>${img.title}</h2>
                    <img src="${img.src}" style="max-width:100%;">
                </div>
                `;
            });

            htmlContent += `</body></html>`;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(htmlContent);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        };

        lucide.createIcons();
    </script>
</body>

</html>