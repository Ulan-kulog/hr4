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
// $turnover_rate = $emp_total > 0 ? round(($emp_sep / $emp_total) * 100, 1) : 0;

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

// --- 7.  TYPES ---
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

// Benefits category distribution removed per request

// Net pay histogram removed per request


// Tenure distribution removed per request

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

// --- AGE DISTRIBUTION ---
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
$dept_salary = ['labels' => [], 'data' => [], 'emp_count' => [], 'missing' => []];
// Use LEFT JOIN from departments so departments with no employees still appear.
// Treat salary = 0 or NULL as missing for the purpose of the average.
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

// --- 15. ADDITIONAL STATS FOR 20 CARDS ---
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

// Helper to format currency
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

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }
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
                    <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center gap-4 mb-6">
                        <h2 class="flex items-center font-bold text-gray-800 text-2xl">
                            <span class="bg-indigo-100/50 mr-3 p-2 rounded-lg text-indigo-600">
                                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            </span>
                            HR Analytics Dashboard
                        </h2>
                    </div>

                    <!-- 4 STAT CARDS -->
                    <div class="gap-6 grid grid-cols-2 mb-8">
                        <!-- 1. Total Employees -->
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
                        <!-- 10. Departments -->
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
                        <div class="gap-6 grid grid-cols-1 lg:grid-cols-2">
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
                                <div style="max-width:800px; width:100%;">
                                    <canvas id="genderPieChart" style="width:100%; height:320px;"></canvas>
                                </div>
                            </div>
                            <!-- Age Distribution -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Age Distribution</h4>
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
                            <!-- Tenure Distribution removed -->
                            <!-- Employment Status -->
                            <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-800">Employment Status</h4>
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
                            <!-- Net Pay Distribution removed -->
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
                            <!-- Benefit Categories removed -->
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
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // --- AGE DISTRIBUTION ---
        charts.ageDistributionChart = new Chart(document.getElementById('ageDistributionChart'), {
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
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
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
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // --- Tenure chart removed ---

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
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '₱ Thousands'
                        }
                    }
                }
            }
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
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hours'
                        }
                    }
                }
            }
        });

        // --- Net Pay histogram removed ---

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
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // --- Benefit pie removed ---

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
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // --- 10. SALARY BENCHMARK ---
        charts.salaryBenchmarkChart = new Chart(document.getElementById('salaryBenchmarkChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($salary_grade['labels'] ?? []) ?>,
                datasets: [{
                        label: 'Min Salary',
                        data: <?= json_encode($salary_grade['min'] ?? []) ?>,
                        backgroundColor: '#9ca3af'
                    },
                    {
                        label: 'Avg Salary',
                        data: <?= json_encode($salary_grade['avg'] ?? []) ?>,
                        backgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Max Salary',
                        data: <?= json_encode($salary_grade['max'] ?? []) ?>,
                        backgroundColor: '#10b981'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Salary (₱)'
                        }
                    }
                }
            }
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
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
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
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
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