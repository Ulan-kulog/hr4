<?php
session_start();
include("../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die("❌ Connection not found for $db_name");
}
$conn = $connections[$db_name];

// Small helpers for rendering
if (!function_exists('format_currency')) {
    function format_currency($val)
    {
        if ($val === '' || $val === null) {
            return '-';
        }
        return '₱' . number_format((float)$val, 0);
    }
}

if (!function_exists('work_status_class')) {
    function work_status_class($work_status)
    {
        $s = strtolower((string)$work_status);
        if ($s === 'active') return 'bg-green-100 text-green-800';
        if (strpos($s, 'pending') !== false) return 'bg-yellow-100 text-yellow-800';
        return 'bg-gray-100 text-gray-800';
    }
}

// Helper function for allowance frequency display
if (!function_exists('format_frequency')) {
    function format_frequency($freq)
    {
        if ($freq === null || $freq === '') {
            return 'Not Specified';
        }

        $freq = strtolower(trim($freq));
        $map = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Annual',
            'annual' => 'Annual',
            'annually' => 'Annual'
        ];

        return $map[$freq] ?? ucfirst($freq);
    }
}

// Load `salary_grades` for display in the salary structure table
$salary_grades = [];
$grades = "SELECT * FROM salary_grades ORDER BY id ASC";
if ($res_dbg = mysqli_query($conn, $grades)) {
    while ($rdbg = mysqli_fetch_assoc($res_dbg)) {
        $salary_grades[] = $rdbg;
    }
}

// Load `bonus_plans` for Bonus & Incentive Plans section
$bonus_plans = [];
$sql_bp = "SELECT * FROM bonus_plans ORDER BY id DESC";
if ($res_bp = mysqli_query($conn, $sql_bp)) {
    while ($rbp = mysqli_fetch_assoc($res_bp)) {
        $bonus_plans[] = $rbp;
    }
}

// Load allowances for Allowance Management section
$allowances_tbl = [];
$sql_alw = "SELECT * FROM allowances ORDER BY id DESC";
if ($res_alw = mysqli_query($conn, $sql_alw)) {
    while ($ralw = mysqli_fetch_assoc($res_alw)) {
        $allowances_tbl[] = $ralw;
    }
}

// Simplified version assuming basic_salary is monthly
$statistics = [];

// 1. Total Salary Budget (Annual total, assuming basic_salary is monthly)
$sql_total_budget = "SELECT SUM(basic_salary * 12) as total_budget FROM employees WHERE work_status = 'Active'";
$result = mysqli_query($conn, $sql_total_budget);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $statistics['total_budget'] = $row['total_budget'] ?? 0;
} else {
    $statistics['total_budget'] = 0;
}

// 2. Average Monthly Salary
$sql_avg_salary = "SELECT AVG(basic_salary) as avg_monthly FROM employees WHERE work_status = 'Active'";
$result = mysqli_query($conn, $sql_avg_salary);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $statistics['avg_monthly'] = $row['avg_monthly'] ?? 0;
} else {
    $statistics['avg_monthly'] = 0;
}

// 3. Bonus Pool
$sql_bonus_pool = "SELECT 
    SUM(
        CASE 
            WHEN amount_or_percentage NOT LIKE '%\\%' 
            THEN CAST(REPLACE(REPLACE(amount_or_percentage, '₱', ''), ',', '') AS DECIMAL(10,2))
            ELSE 0 
        END
    ) as bonus_pool 
    FROM bonus_plans 
    WHERE status = 'active'";
$result = mysqli_query($conn, $sql_bonus_pool);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $statistics['bonus_pool'] = $row['bonus_pool'] ?? 0;
} else {
    $statistics['bonus_pool'] = 0;
}

// 4. Allowance Budget
$sql_allowance_budget = "SELECT 
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
    ) as allowance_budget 
    FROM allowances WHERE status = 'active'";
$result = mysqli_query($conn, $sql_allowance_budget);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $statistics['allowance_budget'] = $row['allowance_budget'] ?? 0;
} else {
    $statistics['allowance_budget'] = 0;
}

// 5. Department-wise salary distribution
$salary_by_dept = [];
$sql_dept_salary = "SELECT 
    COALESCE(e.department, 'Not Assigned') as department,
    COUNT(e.id) as employee_count,
    AVG(e.basic_salary) as avg_salary
    FROM employees e
    LEFT JOIN departments d ON e.department_id = d.id
    WHERE e.work_status = 'Active'
    GROUP BY e.department
    HAVING COUNT(e.id) > 0
    ORDER BY avg_salary DESC";
$result = mysqli_query($conn, $sql_dept_salary);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $salary_by_dept[] = $row;
    }
} else {
    // leave $salary_by_dept empty on query failure
}

// Fetch compensation mix data - FIXED VERSION
$compensation_mix = [];

// 1. Base Salary (Annual total from employees)
$sql_base_salary = "SELECT 
    COALESCE(SUM(basic_salary * 12), 0) as value 
    FROM employees 
    WHERE work_status = 'Active'";
$result = mysqli_query($conn, $sql_base_salary);
$base_salary = mysqli_fetch_assoc($result);
$compensation_mix[] = ['type' => 'Base Salary', 'value' => $base_salary['value'] ?? 0];

// 2. Bonuses (Only fixed amounts, not percentages)
$sql_bonuses = "SELECT 
    COALESCE(SUM(
        CASE 
            WHEN amount_or_percentage REGEXP '^[0-9]+(\.[0-9]+)?$' 
            OR amount_or_percentage LIKE '%₱%'
            OR (amount_or_percentage NOT LIKE '%\%' AND amount_or_percentage NOT LIKE '%percent%')
            THEN CAST(REPLACE(REPLACE(REPLACE(amount_or_percentage, '₱', ''), ',', ''), ' ', '') AS DECIMAL(10,2))
            ELSE 0 
        END
    ), 0) as value 
    FROM bonus_plans 
    WHERE status = 'active'";
$result = mysqli_query($conn, $sql_bonuses);
$bonuses = mysqli_fetch_assoc($result);
$compensation_mix[] = ['type' => 'Bonuses', 'value' => $bonuses['value'] ?? 0];

// 3. Allowances (Annual total)
$sql_allowances = "SELECT 
    COALESCE(SUM(
        amount * 
        CASE LOWER(frequency)
            WHEN 'daily' THEN 365 
            WHEN 'weekly' THEN 52 
            WHEN 'monthly' THEN 12 
            WHEN 'quarterly' THEN 4 
            WHEN 'annual' THEN 1 
            ELSE 12  -- Default to monthly if unknown
        END
    ), 0) as value 
    FROM allowances 
    WHERE status = 'active'";
$result = mysqli_query($conn, $sql_allowances);
$allowances = mysqli_fetch_assoc($result);
$compensation_mix[] = ['type' => 'Allowances', 'value' => $allowances['value'] ?? 0];

// 4. Benefits - Check if benefits table exists
$benefits_value = 0;
// First, check if benefits table exists
$table_check = "SHOW TABLES LIKE 'benefits'";
$table_result = mysqli_query($conn, $table_check);
if (mysqli_num_rows($table_result) > 0) {
    // Benefits table exists, query it
    $sql_benefits = "SELECT COALESCE(SUM(amount * 12), 0) as value FROM benefits WHERE status = 'active'";
    $result = mysqli_query($conn, $sql_benefits);
    if ($result) {
        $benefits = mysqli_fetch_assoc($result);
        $benefits_value = $benefits['value'] ?? 0;
    }
}
$compensation_mix[] = ['type' => 'Benefits', 'value' => $benefits_value];

// Filter out zero values to make chart cleaner
$non_zero_mix = array_filter($compensation_mix, function ($item) {
    return floatval($item['value']) > 0;
});

// If we have at least one non-zero value, use filtered array
// Otherwise, use the original but with a minimum value for Base Salary
if (!empty($non_zero_mix)) {
    $compensation_mix = array_values($non_zero_mix);
} else {
    // Ensure at least Base Salary has a minimum value for chart display
    if (floatval($compensation_mix[0]['value']) == 0) {
        $compensation_mix[0]['value'] = 1; // Minimum value for display
    }
}

// Helper function to format large numbers
if (!function_exists('format_large_number')) {
    function format_large_number($val)
    {
        if ($val === '' || $val === null) {
            return '-';
        }
        $val = (float)$val;
        if ($val >= 1000000) {
            return '₱' . number_format($val / 1000000, 1) . 'M';
        } elseif ($val >= 1000) {
            return '₱' . number_format($val / 1000, 1) . 'K';
        }
        return '₱' . number_format($val, 0);
    }
}
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Compensation - Compensation Planning</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Primary button - theme color */
        .swal-btn-primary {
            background-color: #011f55 !important;
            color: #ffffff !important;
            border-radius: 0.5rem !important;
            padding: 0.65rem 1.4rem !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            border: none !important;
            transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
        }

        .swal-btn-primary:hover {
            background-color: #022a73 !important;
            /* lighter shade */
            box-shadow: 0 4px 10px rgba(1, 31, 85, 0.35);
        }

        .swal-btn-primary:active {
            transform: scale(0.96);
        }

        /* Cancel / secondary button */
        .swal-btn-cancel {
            background-color: #e5e7eb !important;
            color: #011f55 !important;
            border-radius: 0.5rem !important;
            padding: 0.65rem 1.4rem !important;
            font-weight: 500 !important;
            border: 1px solid #cbd5e1 !important;
        }

        .swal-btn-cancel:hover {
            background-color: #d1d5db !important;
        }

        /* Bonus card styling */
        #bonusPlansContainer>div {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        #bonusPlansContainer>div:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        /* work_status badges */
        .bg-green-100.text-green-800 {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
        }

        .bg-yellow-100.text-yellow-800 {
            background-color: #fef3c7 !important;
            color: #92400e !important;
        }

        .bg-gray-100.text-gray-800 {
            background-color: #f3f4f6 !important;
            color: #374151 !important;
        }
    </style>
</head>

<body class="bg-base-100 bg-white min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../INCLUDES/sidebar.php'; ?>

        <!-- Content Area -->
        <div class="flex flex-col flex-1 overflow-auto">
            <!-- Navbar -->
            <?php include '../INCLUDES/navbar.php'; ?>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                <?php if (isset($_GET['updated'])): ?>
                    <?php if ($_GET['updated'] === '1'): ?>
                        <div id="updateBanner" class="bg-green-50 mb-4 p-4 border border-green-200 rounded text-green-800 update-banner">Salary updated successfully.</div>
                    <?php else: ?>
                        <div id="updateBanner" class="bg-red-50 mb-4 p-4 border border-red-200 rounded text-red-800 update-banner">Update failed.</div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php
                // Flash banner for update results
                // NOTE: set $DEBUG_UPDATE = true only on local/dev environments to show
                // detailed DB error messages. Keep false in production to avoid leaking info.
                $DEBUG_UPDATE = false;
                $flash = null;
                if (isset($_GET['updated'])) {
                    if ($_GET['updated'] === '1') {
                        $flash = ['type' => 'success', 'text' => 'Salary updated successfully.'];
                    } else {
                        $err = $_GET['error'] ?? 'unknown';
                        $msg = isset($_GET['msg']) ? urldecode($_GET['msg']) : '';
                        $safe_msg = '';
                        if ($DEBUG_UPDATE) {
                            $safe_msg = $msg !== '' ? ' — ' . htmlspecialchars($msg) : '';
                        }
                        $flash = ['type' => 'error', 'text' => 'Update failed: ' . htmlspecialchars($err) . $safe_msg];
                    }
                }
                ?>
                <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center gap-4 mb-6">
                        <h2 class="flex items-center font-bold text-gray-800 text-2xl">
                            <span class="bg-blue-100/50 mr-3 p-2 rounded-lg text-blue-600">
                                <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                            </span>
                            Core Compensation Management
                        </h2>
                        <div class="flex gap-2">
                            <button id="salaryStructureBtn" class="flex items-center bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                                Salary Structure
                            </button>
                            <button id="bonusIncentivesBtn" class="flex items-center bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="gift" class="mr-2 w-4 h-4"></i>
                                Bonus & Incentives
                            </button>
                            <button id="allowanceBtn" class="flex items-center bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="credit-card" class="mr-2 w-4 h-4"></i>
                                Allowances
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="gap-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                        <!-- Total Salary Budget -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Total Salary Budget</p>
                                    <h3 class="mt-1 font-bold text-3xl">
                                        <span id="totalBudget"><?= format_large_number($statistics['total_budget']) ?></span>
                                    </h3>
                                    <p class="mt-1 text-gray-500 text-xs">
                                        <span id="totalEmployees"><?= $statistics['total_employees'] ?? 0 ?></span> active employees
                                    </p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="credit-card" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Average Salary -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Average Salary</p>
                                    <h3 class="mt-1 font-bold text-3xl">
                                        <span id="avgMonthly">₱<?= number_format($statistics['avg_monthly'] ?? 0, 0) ?></span>
                                    </h3>
                                    <p class="mt-1 text-gray-500 text-xs">Monthly average</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Bonus Pool -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Bonus Pool</p>
                                    <h3 class="mt-1 font-bold text-3xl">
                                        <span id="bonusPool"><?= format_large_number($statistics['bonus_pool']) ?></span>
                                    </h3>
                                    <p class="mt-1 text-gray-500 text-xs">
                                        <?php
                                        $sql_bonus_plans = "SELECT COUNT(*) as bonus_count FROM bonus_plans WHERE status = 'active'";
                                        $result = mysqli_query($conn, $sql_bonus_plans);
                                        $bonus_count = mysqli_fetch_assoc($result)['bonus_count'] ?? 0;
                                        echo $bonus_count . ' active plans';
                                        ?>
                                    </p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="gift" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Allowance Budget -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Allowance Budget</p>
                                    <h3 class="mt-1 font-bold text-3xl">
                                        <span id="allowanceBudget"><?= format_large_number($statistics['allowance_budget']) ?></span>
                                    </h3>
                                    <p class="mt-1 text-gray-500 text-xs">
                                        <?php
                                        $sql_allowance_count = "SELECT COUNT(*) as allowance_count FROM allowances WHERE status = 'active'";
                                        $result = mysqli_query($conn, $sql_allowance_count);
                                        $allowance_count = mysqli_fetch_assoc($result)['allowance_count'] ?? 0;
                                        echo $allowance_count . ' active allowances';
                                        ?>
                                    </p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="package" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-800 text-lg">Analytics</h3>
                        <div>
                            <button id="exportAnalyticsBtn" class="bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm">Export Reports</button>
                        </div>
                    </div>

                    <div class="gap-6 grid grid-cols-1 lg:grid-cols-2 mb-8">
                        <!-- Salary Distribution Chart -->
                        <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                            <h3 class="mb-4 font-semibold text-gray-800">Salary Distribution by Department</h3>
                            <div class="h-64">
                                <canvas id="salaryChart"></canvas>
                            </div>
                        </div>

                        <!-- Bonus vs Base Pay Chart -->
                        <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                            <h3 class="mb-4 font-semibold text-gray-800">Compensation Mix</h3>
                            <div class="h-64">
                                <canvas id="compensationMixChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Structure Management -->
                    <div class="bg-white shadow-sm mb-6 p-6 border border-gray-100 rounded-xl">
                        <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center mb-4">
                            <h3 class="font-semibold text-gray-800">Salary Structure</h3>
                            <div class="flex gap-2 mt-2 sm:mt-0">
                                <input type="text" placeholder="Search grades..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <button class="hover:bg-gray-50 p-2 border border-gray-200 rounded-lg">
                                    <i data-lucide="filter" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-gray-100 border-b">
                                    <tr>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Grade</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Position</th>
                                        <!-- <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Min Salary</th> -->
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Max Salary</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Employees</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">work_status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if (empty($salary_grades)): ?>
                                        <tr>
                                            <td colspan="7" class="px-4 py-3 text-gray-500 text-sm">No salary grades found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($salary_grades as $row):
                                            // Prefer `grade_name` when available, otherwise fall back to `id`
                                            $id = htmlspecialchars($row['grade_name'] ?? $row['id'] ?? '');
                                            $grade = htmlspecialchars($row['grade'] ?? $row['grade_name'] ?? $row['grade_level'] ?? $row['level'] ?? $id);
                                            $position = htmlspecialchars($row['position'] ?? $row['position_title'] ?? $row['title'] ?? '');
                                            $maxRaw = $row['max_salary'] ?? $row['max'] ?? '';
                                            // $minSalary = format_currency($minRaw);
                                            $maxSalary = format_currency($maxRaw);
                                            $work_status = htmlspecialchars($row['work_status'] ?? 'Active');
                                            $employees = isset($row['employees']) ? (int)$row['employees'] : (isset($row['employees_count']) ? (int)$row['employees_count'] : '-');
                                            $dept = htmlspecialchars($row['department'] ?? $row['dept'] ?? '');
                                            $work_statusClass = work_status_class($work_status);
                                        ?>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-gray-900 text-sm"><?= $grade ?></td>
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $position ?></td>
                                                <!-- <td class="px-4 py-3 text-gray-900 text-sm"><?= $minSalary ?></td> -->
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $maxSalary ?></td>
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $employees ?></td>
                                                <td class="px-4 py-3"><span class="inline-flex items-center <?= $work_statusClass ?> px-2.5 py-0.5 rounded-full font-medium text-xs"><?= $work_status ?></span></td>
                                                <td class="px-4 py-3">
                                                    <div class="flex gap-2">
                                                        <!-- <a href="#" class="font-medium text-blue-600 hover:text-blue-800 text-sm edit-salary-btn" data-id="<?= $id ?>" data-grade="<?= $grade ?>" data-position="<?= $position ?>" data-min="<?= htmlspecialchars($minRaw) ?>" data-max="<?= htmlspecialchars($maxRaw) ?>" data-work_status="<?= $work_status ?>">Edit</a> -->

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Bonus & Incentives Section -->
                    <div class="bg-white shadow-sm mb-6 p-6 border border-gray-100 rounded-xl">
                        <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center mb-4">
                            <h3 class="font-semibold text-gray-800">Bonus & Incentive Plans</h3>
                            <div class="flex gap-2 mt-2 sm:mt-0">
                                <input type="text" id="searchBonus" placeholder="Search plans..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <button class="hover:bg-gray-50 p-2 border border-gray-200 rounded-lg">
                                    <i data-lucide="filter" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <div class="gap-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3" id="bonusPlansContainer">
                            <?php if (empty($bonus_plans)): ?>
                                <div class="p-4 text-gray-500">No bonus or incentive plans found.</div>
                            <?php else: ?>
                                <?php foreach ($bonus_plans as $plan):
                                    $bp_id = htmlspecialchars($plan['id'] ?? '');
                                    $bp_name = htmlspecialchars($plan['plan_name'] ?? $plan['title'] ?? 'Untitled Plan');
                                    $bp_dept = htmlspecialchars($plan['department'] ?? 'All Departments');
                                    $bp_desc = htmlspecialchars($plan['eligibility_criteria'] ?? $plan['description'] ?? $plan['details'] ?? '');
                                    $bp_type = htmlspecialchars($plan['bonus_type'] ?? $plan['type'] ?? '');
                                    $bp_amount_raw = $plan['amount_or_percentage'] ?? $plan['value'] ?? '';
                                    $bp_work_status = htmlspecialchars($plan['work_status'] ?? 'active');
                                    $bp_start = htmlspecialchars($plan['start_date'] ?? '');
                                    $bp_end = htmlspecialchars($plan['end_date'] ?? '');

                                    // display percentage or formatted currency
                                    if ($bp_amount_raw === '' || $bp_amount_raw === null) {
                                        $bp_amount = '-';
                                    } elseif (strpos((string)$bp_amount_raw, '%') !== false || strpos((string)$bp_amount_raw, '₱') !== false) {
                                        $bp_amount = htmlspecialchars($bp_amount_raw);
                                    } else {
                                        $bp_amount = format_currency($bp_amount_raw);
                                    }

                                    $work_statusClass = work_status_class($bp_work_status);
                                ?>
                                    <div class="p-4 border border-gray-200 rounded-lg">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-semibold text-gray-800"><?= $bp_name ?></h4>
                                            <span class="bg-green-100 p-2 rounded-lg text-green-600">
                                                <i data-lucide="gift" class="w-4 h-4"></i>
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="inline-flex items-center <?= $work_statusClass ?> px-2.5 py-0.5 rounded-full font-medium text-xs mb-2">
                                                <?= ucfirst($bp_work_status) ?>
                                            </span>
                                        </div>
                                        <p class="mb-2 text-gray-600 text-sm">
                                            <strong>Type:</strong> <?= ucfirst(str_replace('_', ' ', $bp_type)) ?>
                                        </p>
                                        <p class="mb-2 text-gray-600 text-sm">
                                            <strong>Department:</strong> <?= $bp_dept ?>
                                        </p>
                                        <?php if ($bp_start || $bp_end): ?>
                                            <p class="mb-2 text-gray-600 text-sm">
                                                <strong>Period:</strong>
                                                <?= $bp_start ? date('M d, Y', strtotime($bp_start)) : 'N/A' ?>
                                                to
                                                <?= $bp_end ? date('M d, Y', strtotime($bp_end)) : 'Ongoing' ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($bp_desc !== ''): ?>
                                            <p class="mb-3 text-gray-600 text-sm">
                                                <strong>Criteria:</strong> <?= strlen($bp_desc) > 100 ? substr($bp_desc, 0, 100) . '...' : $bp_desc ?>
                                            </p>
                                        <?php endif; ?>
                                        <div class="flex justify-between items-center mt-4 pt-4 border-gray-100 border-t">
                                            <span class="font-medium text-gray-700 text-sm">Value: <?= $bp_amount ?></span>
                                            <div class="flex gap-2">
                                                <button class="text-blue-600 hover:text-blue-800 text-sm edit-bonus-btn"
                                                    data-id="<?= $bp_id ?>"
                                                    data-name="<?= $bp_name ?>"
                                                    data-type="<?= $bp_type ?>"
                                                    data-amount="<?= htmlspecialchars($plan['amount_or_percentage']) ?>"
                                                    data-dept="<?= $bp_dept ?>"
                                                    data-criteria="<?= htmlspecialchars($plan['eligibility_criteria'] ?? '') ?>"
                                                    data-start="<?= $bp_start ?>"
                                                    data-end="<?= $bp_end ?>"
                                                    data-work_status="<?= $bp_work_status ?>">
                                                    Edit
                                                </button>
                                                <form method="POST" action="API/delete_bonus_plan.php" class="inline delete-form">
                                                    <input type="hidden" name="id" value="<?= $bp_id ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Allowance Management -->
                    <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                        <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center mb-4">
                            <h3 class="font-semibold text-gray-800">Allowance Management</h3>
                            <div class="flex gap-2 mt-2 sm:mt-0">
                                <input type="text" id="searchAllowances" placeholder="Search allowances..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <button class="hover:bg-gray-50 p-2 border border-gray-200 rounded-lg">
                                    <i data-lucide="filter" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-gray-100 border-b">
                                    <tr>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Allowance Type</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Department</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Amount</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Frequency</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Eligibility Criteria</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">work_status</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100" id="allowanceTableBody">
                                    <?php if (empty($allowances_tbl)): ?>
                                        <tr>
                                            <td colspan="7" class="px-4 py-3 text-gray-500 text-sm text-center">No allowances found. <button class="text-blue-600 hover:text-blue-800" onclick="document.getElementById('allowanceBtn').click()">Create one</button></td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($allowances_tbl as $a):
                                            // Safely access array elements with null coalescing
                                            $alw_id = htmlspecialchars($a['id'] ?? '');
                                            $alw_type = htmlspecialchars($a['allowance_type'] ?? $a['type'] ?? 'Not Specified');
                                            $alw_dept = htmlspecialchars($a['department'] ?? $a['dept'] ?? 'All');
                                            $alw_amount_raw = $a['amount'] ?? $a['value'] ?? 0;
                                            $alw_amount = format_currency($alw_amount_raw);
                                            $alw_freq = format_frequency($a['frequency'] ?? '');
                                            $alw_criteria = htmlspecialchars($a['eligibility_criteria'] ?? $a['description'] ?? $a['details'] ?? '');
                                            $alw_work_status = htmlspecialchars($a['work_status'] ?? 'active');
                                            $work_statusClass = work_status_class($alw_work_status);
                                        ?>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-gray-900 text-sm"><?= $alw_type ?></td>
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $alw_dept ?></td>
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $alw_amount ?></td>
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $alw_freq ?></td>
                                                <td class="px-4 py-3 max-w-xs text-gray-900 text-sm truncate" title="<?= $alw_criteria ?>">
                                                    <?= strlen($alw_criteria) > 50 ? substr($alw_criteria, 0, 50) . '...' : $alw_criteria ?>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center <?= $work_statusClass ?> px-2.5 py-0.5 rounded-full font-medium text-xs">
                                                        <?= ucfirst($alw_work_status) ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex gap-2">
                                                        <button class="font-medium text-blue-600 hover:text-blue-800 text-sm edit-allowance-btn"
                                                            data-id="<?= $alw_id ?>"
                                                            data-type="<?= $alw_type ?>"
                                                            data-dept="<?= $alw_dept ?>"
                                                            data-amount="<?= htmlspecialchars($alw_amount_raw) ?>"
                                                            data-frequency="<?= htmlspecialchars($allowance['frequency'] ?? '') ?>"
                                                            data-criteria="<?= htmlspecialchars($alw_criteria) ?>"
                                                            data-work_status="<?= $alw_work_status ?>">
                                                            Edit
                                                        </button>
                                                        <form method="POST" action="API/delete_allowance.php" class="inline delete-form">
                                                            <input type="hidden" name="id" value="<?= $alw_id ?>">
                                                            <button type="submit" class="font-medium text-red-600 hover:text-red-800 text-sm">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <?php include 'modals/salary_structure_modal.php' ?>
        <?php include 'modals/edit_salary_modal.php' ?>
        <?php include 'modals/bonus_incentives_modal.php' ?>
        <?php include 'modals/edit_bonus_modal.php' ?>
        <?php include 'modals/allowance_modal.php' ?>
        <?php include 'modals/edit_allowance_modal.php' ?>
    </div>

    <script>
        lucide.createIcons();

        // Initialize Charts by fetching data from backend API
        (function initializeChartsFromApi() {
            const apiUrl = 'API/compensation_charts.php';

            fetch(apiUrl)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to fetch chart data');
                    return res.json();
                })
                .then(payload => {
                    // Salary distribution
                    const salaryCtx = document.getElementById('salaryChart')?.getContext('2d');
                    const salaryData = payload.salary_by_dept || [];
                    const deptLabels = salaryData.map(r => r.department || 'Unknown');
                    const avgSalaries = salaryData.map(r => parseFloat(r.avg_salary) || 0);
                    const employeeCounts = salaryData.map(r => parseInt(r.employee_count) || 0);

                    if (salaryCtx) {
                        const colors = generateChartColors(deptLabels.length);
                        new Chart(salaryCtx, {
                            type: 'bar',
                            data: {
                                labels: deptLabels.length > 0 ? deptLabels : ['No Department Data'],
                                datasets: [{
                                    label: 'Avg Monthly Salary',
                                    data: avgSalaries,
                                    backgroundColor: colors,
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {},
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    }

                    // Compensation mix
                    const mixCtx = document.getElementById('compensationMixChart')?.getContext('2d');
                    const mix = payload.compensation_mix || [];
                    const mixLabels = mix.map(m => m.type || '');
                    const mixValues = mix.map(m => parseFloat(m.value) || 0);

                    if (mixCtx) {
                        const mixColors = generateChartColors(mixLabels.length);
                        const total = mixValues.reduce((s, v) => s + v, 0);
                        new Chart(mixCtx, {
                            type: 'doughnut',
                            data: {
                                labels: mixLabels.length > 0 ? mixLabels : ['No Data'],
                                datasets: [{
                                    data: mixValues,
                                    backgroundColor: mixColors,
                                    hoverOffset: 10
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const val = parseFloat(context.raw || 0);
                                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';
                                                return `${context.label}: ₱${Number(val).toLocaleString()} (${pct}%)`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                })
                .catch(err => {
                    console.error('Chart API error', err);
                });
        })();

        // Fetch and render stats dynamically from backend API
        (function fetchCompensationStats() {
            const url = 'API/compensation_stats.php';

            function setText(id, text) {
                const el = document.getElementById(id);
                if (el) el.textContent = text;
            }

            fetch(url)
                .then(r => {
                    if (!r.ok) throw new Error('Failed to fetch stats');
                    return r.json();
                })
                .then(data => {
                    // Format numbers for display
                    const fmt = (v) => {
                        if (v === null || v === undefined) return '-';
                        const n = Number(v);
                        if (isNaN(n)) return '-';
                        if (Math.abs(n) >= 1000000) return '₱' + (n / 1000000).toFixed(1) + 'M';
                        if (Math.abs(n) >= 1000) return '₱' + (n / 1000).toFixed(1) + 'K';
                        return '₱' + n.toLocaleString();
                    };

                    setText('totalBudget', fmt(data.total_budget));
                    setText('avgMonthly', '₱' + Math.round((data.avg_monthly || 0)).toLocaleString());
                    setText('bonusPool', fmt(data.bonus_pool));
                    setText('allowanceBudget', fmt(data.allowance_budget));
                    setText('totalEmployees', (data.total_employees || 0).toString());
                })
                .catch(err => {
                    console.error('Comp stats fetch error', err);
                });

            // Optional: refresh every 5 minutes
            // setInterval(() => fetchCompensationStats(), 5 * 60 * 1000);
        })();

        // Export analytics CSV
        document.getElementById('exportAnalyticsBtn')?.addEventListener('click', function() {
            const url = 'API/export_compensation_analytics.php';
            fetch(url, {
                    method: 'GET'
                })
                .then(resp => {
                    if (!resp.ok) throw new Error('Export failed');
                    return resp.blob();
                })
                .then(blob => {
                    const link = document.createElement('a');
                    const filename = 'compensation_analytics_' + new Date().toISOString().slice(0, 10) + '.csv';
                    link.href = URL.createObjectURL(blob);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                })
                .catch(err => {
                    console.error('Export error', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Export failed',
                        text: 'Unable to generate report.'
                    });
                });
        });

        // Helper function to generate chart colors
        function generateChartColors(count) {
            const baseColors = [
                '#3B82F6', '#10B981', '#8B5CF6', '#F59E0B',
                '#EF4444', '#06B6D4', '#EC4899', '#14B8A6',
                '#F97316', '#6366F1', '#8B5CF6', '#EF4444'
            ];

            if (count <= baseColors.length) {
                return baseColors.slice(0, count);
            }

            // Generate additional colors if needed
            const colors = [...baseColors];
            for (let i = baseColors.length; i < count; i++) {
                const hue = Math.floor(Math.random() * 360);
                colors.push(`hsl(${hue}, 70%, 60%)`);
            }
            return colors;
        }

        // Modal Functionality
        const salaryStructureModal = document.getElementById('salaryStructureModal');
        const bonusModal = document.getElementById('bonusModal');
        const allowanceModal = document.getElementById('allowanceModal');
        const editSalaryModal = document.getElementById('editSalaryModal');
        const editAllowanceModal = document.getElementById('editAllowanceModal'); // Added missing declaration

        // Open modals with null checks
        document.getElementById('salaryStructureBtn')?.addEventListener('click', () => {
            salaryStructureModal?.classList.remove('hidden');
        });

        document.getElementById('bonusIncentivesBtn')?.addEventListener('click', () => {
            bonusModal?.classList.remove('hidden');
        });

        document.getElementById('allowanceBtn')?.addEventListener('click', () => {
            allowanceModal?.classList.remove('hidden');
        });

        // Close modals with null checks
        document.getElementById('closeSalaryModal')?.addEventListener('click', () => salaryStructureModal?.classList.add('hidden'));
        document.getElementById('closeBonusModal')?.addEventListener('click', () => bonusModal?.classList.add('hidden'));
        document.getElementById('closeAllowanceModal')?.addEventListener('click', () => allowanceModal?.classList.add('hidden'));
        document.getElementById('closeEditModal')?.addEventListener('click', () => editSalaryModal?.classList.add('hidden'));
        document.getElementById('closeEditAllowanceModal')?.addEventListener('click', () => editAllowanceModal?.classList.add('hidden')); // Added

        document.getElementById('cancelSalary')?.addEventListener('click', () => salaryStructureModal?.classList.add('hidden'));
        document.getElementById('cancelBonus')?.addEventListener('click', () => bonusModal?.classList.add('hidden'));
        document.getElementById('cancelAllowance')?.addEventListener('click', () => allowanceModal?.classList.add('hidden'));
        document.getElementById('cancelEdit')?.addEventListener('click', () => editSalaryModal?.classList.add('hidden'));
        document.getElementById('cancelEditAllowance')?.addEventListener('click', () => editAllowanceModal?.classList.add('hidden')); // Added

        // Close modals when clicking outside
        salaryStructureModal?.addEventListener('click', (e) => {
            if (e.target === salaryStructureModal) salaryStructureModal.classList.add('hidden');
        });
        bonusModal?.addEventListener('click', (e) => {
            if (e.target === bonusModal) bonusModal.classList.add('hidden');
        });
        allowanceModal?.addEventListener('click', (e) => {
            if (e.target === allowanceModal) allowanceModal.classList.add('hidden');
        });
        editSalaryModal?.addEventListener('click', (e) => {
            if (e.target === editSalaryModal) editSalaryModal.classList.add('hidden');
        });
        editAllowanceModal?.addEventListener('click', (e) => { // Added
            if (e.target === editAllowanceModal) editAllowanceModal.classList.add('hidden');
        });

        // Open Edit modal and populate fields
        document.querySelectorAll('.edit-salary-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.getAttribute('data-id');
                const grade = btn.getAttribute('data-grade');
                const position = btn.getAttribute('data-position');
                const min = btn.getAttribute('data-min');
                const max = btn.getAttribute('data-max');
                const work_status = btn.getAttribute('data-work_status');

                document.getElementById('edit_id').value = id || '';
                document.getElementById('edit_grade').value = grade || '';
                document.getElementById('edit_position').value = position || '';
                document.getElementById('edit_min').value = min || '';
                document.getElementById('edit_max').value = max || '';

                // Set work_status select with case-insensitive matching
                const work_statusEl = document.getElementById('edit_work_status');
                if (work_status && work_statusEl) {
                    // try direct match first
                    if (Array.from(work_statusEl.options).some(o => o.value === work_status)) {
                        work_statusEl.value = work_status;
                    } else {
                        // case-insensitive fallback
                        const match = Array.from(work_statusEl.options).find(o =>
                            o.value.toLowerCase() === work_status.toLowerCase()
                        );
                        work_statusEl.value = match ? match.value : 'Active';
                    }
                } else if (work_statusEl) {
                    work_statusEl.value = 'Active';
                }

                editSalaryModal?.classList.remove('hidden');
            });
        });

        // Function to validate if a string is a valid number (including decimals)
        function isValidNumber(value) {
            if (value === '' || value === null || value === undefined) return false;
            // Remove commas and currency symbols for validation
            const cleanValue = String(value).replace(/[₱,]/g, '').trim();
            // Check if it's a valid number (including decimals)
            return !isNaN(parseFloat(cleanValue)) && isFinite(cleanValue);
        }

        // Function to validate if a string is a valid percentage
        function isValidPercentage(value) {
            if (value === '' || value === null || value === undefined) return false;
            const cleanValue = String(value).replace('%', '').trim();
            const num = parseFloat(cleanValue);
            return !isNaN(num) && isFinite(num) && num >= 0 && num <= 100;
        }

        // Function to validate if a string is a valid amount (currency or percentage)
        function isValidAmountOrPercentage(value) {
            if (value === '' || value === null || value === undefined) return false;

            // Check if it's a percentage
            if (value.includes('%')) {
                return isValidPercentage(value);
            }

            // Check if it's a currency amount
            return isValidNumber(value);
        }

        // Function to validate date is not in the past
        function isValidFutureDate(dateString) {
            if (!dateString) return true; // Empty dates are allowed
            const inputDate = new Date(dateString);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Set to start of day
            return inputDate >= today;
        }

        // Function to validate end date is after start date
        function isValidDateRange(startDate, endDate) {
            if (!startDate || !endDate) return true; // If either is empty, validation passes
            const start = new Date(startDate);
            const end = new Date(endDate);
            return end >= start;
        }

        // Function to validate salary range (min < max)
        function isValidSalaryRange(min, max) {
            if (!min || !max) return true; // If either is empty, validation passes
            const minVal = parseFloat(min);
            const maxVal = parseFloat(max);
            return minVal < maxVal;
        }

        // Function to validate amount is positive
        function isValidPositiveNumber(value) {
            if (value === '' || value === null || value === undefined) return false;
            const num = parseFloat(value);
            return !isNaN(num) && isFinite(num) && num >= 0;
        }

        // Set up date input restrictions (min = today)
        function setupDateInputRestrictions() {
            const today = new Date().toISOString().split('T')[0];

            // Set min date for all date inputs
            document.querySelectorAll('input[type="date"]').forEach(dateInput => {
                dateInput.setAttribute('min', today);

                // Add change event to validate dates
                dateInput.addEventListener('change', function() {
                    if (this.value && !isValidFutureDate(this.value)) {
                        this.setCustomValidity('Date cannot be in the past');
                        this.reportValidity();
                    } else {
                        this.setCustomValidity('');
                    }
                });
            });
        }

        // Set up number input restrictions
        function setupNumberInputValidations() {
            // Salary inputs - min and max validation
            const salaryInputs = document.querySelectorAll(' input[name="max_salary"]');
            salaryInputs.forEach(input => {
                input.setAttribute('min', '0');
                input.setAttribute('step', '0.01');

                input.addEventListener('change', function() {
                    const maxInput = document.querySelector('input[name="max_salary"]');

                    if (minInput && maxInput && minInput.value && maxInput.value) {
                        if (!isValidSalaryRange(minInput.value, maxInput.value)) {
                            minInput.setCustomValidity('Minimum salary must be less than maximum salary');
                            minInput.reportValidity();
                            maxInput.setCustomValidity('Maximum salary must be greater than minimum salary');
                            maxInput.reportValidity();
                        } else {
                            minInput.setCustomValidity('');
                            maxInput.setCustomValidity('');
                        }
                    }
                });
            });

            // Allowance amount input
            const allowanceAmountInput = document.querySelector('input[name="amount"]');
            if (allowanceAmountInput) {
                allowanceAmountInput.setAttribute('min', '0');
                allowanceAmountInput.setAttribute('step', '0.01');

                allowanceAmountInput.addEventListener('change', function() {
                    if (this.value && !isValidPositiveNumber(this.value)) {
                        this.setCustomValidity('Amount must be a positive number');
                        this.reportValidity();
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }

            // Edit allowance amount input
            const editAllowanceAmountInput = document.querySelector('#edit_alw_amount');
            if (editAllowanceAmountInput) {
                editAllowanceAmountInput.setAttribute('min', '0');
                editAllowanceAmountInput.setAttribute('step', '0.01');

                editAllowanceAmountInput.addEventListener('change', function() {
                    if (this.value && !isValidPositiveNumber(this.value)) {
                        this.setCustomValidity('Amount must be a positive number');
                        this.reportValidity();
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        }

        // Set up bonus form validation
        function setupBonusFormValidation() {
            // Bonus amount/percentage validation
            const bonusAmountInputs = document.querySelectorAll('#edit_bonus_amount, input[name="amount_or_percentage"]');
            bonusAmountInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value && !isValidAmountOrPercentage(this.value)) {
                        this.setCustomValidity('Please enter a valid amount (e.g., 5000 or 5%)');
                        this.reportValidity();
                    } else {
                        this.setCustomValidity('');
                    }
                });
            });

            // Bonus date range validation
            const startDateInputs = document.querySelectorAll('#edit_bonus_start, input[name="start_date"]');
            const endDateInputs = document.querySelectorAll('#edit_bonus_end, input[name="end_date"]');

            startDateInputs.forEach(startInput => {
                startInput.addEventListener('change', function() {
                    const form = this.closest('form');
                    const endInput = form.querySelector('#edit_bonus_end') || form.querySelector('input[name="end_date"]');

                    if (this.value && endInput && endInput.value) {
                        if (!isValidDateRange(this.value, endInput.value)) {
                            this.setCustomValidity('Start date must be before end date');
                            this.reportValidity();
                        } else {
                            this.setCustomValidity('');
                        }
                    }
                });
            });

            endDateInputs.forEach(endInput => {
                endInput.addEventListener('change', function() {
                    const form = this.closest('form');
                    const startInput = form.querySelector('#edit_bonus_start') || form.querySelector('input[name="start_date"]');

                    if (this.value && startInput && startInput.value) {
                        if (!isValidDateRange(startInput.value, this.value)) {
                            this.setCustomValidity('End date must be after start date');
                            this.reportValidity();
                        } else {
                            this.setCustomValidity('');
                        }
                    }
                });
            });
        }

        // Create Salary Form Validation (min_salary validation removed)
        document.getElementById('createSalaryForm')?.addEventListener('submit', function(e) {
            const maxSalary = this.querySelector('[name="max_salary"]').value;
            const gradeName = this.querySelector('[name="grade_name"]').value.trim();
            const position = this.querySelector('[name="position"]').value.trim();

            let hasError = false;
            let errorMessage = '';

            // Validate required fields
            if (!gradeName || !position) {
                hasError = true;
                errorMessage = 'Grade Level and Position Title are required.';
            }

            // Validate maximum salary only (do not block on min_salary)
            // if (!isValidPositiveNumber(maxSalary)) {
            //     hasError = true;
            //     errorMessage = 'Please enter a valid maximum salary (positive number).';
            // }

            // No client-side salary range validation for min_salary

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });

        // Edit Salary Form Validation (min_salary validation removed)
        document.getElementById('editSalaryForm')?.addEventListener('submit', function(e) {
            const maxSalary = this.querySelector('[name="max_salary"]').value;
            const grade = this.querySelector('[name="grade"]').value.trim();
            const position = this.querySelector('[name="position"]').value.trim();

            let hasError = false;
            let errorMessage = '';

            // Validate required fields
            if (!grade || !position) {
                hasError = true;
                errorMessage = 'Grade Level and Position Title are required.';
            }

            // Validate maximum salary only (do not block on min_salary)
            // if (!isValidPositiveNumber(maxSalary)) {
            //     hasError = true;
            //     errorMessage = 'Please enter a valid maximum salary (positive number).';
            // }

            // No client-side salary range validation for min_salary

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });

        // Create Bonus Form Validation
        document.getElementById('createBonusForm')?.addEventListener('submit', function(e) {
            const planName = this.querySelector('[name="plan_name"]').value.trim();
            const bonusType = this.querySelector('[name="bonus_type"]').value;
            const amount = this.querySelector('[name="amount_or_percentage"]').value.trim();
            const startDate = this.querySelector('[name="start_date"]').value;
            const endDate = this.querySelector('[name="end_date"]').value;

            let hasError = false;
            let errorMessage = '';

            // Validate required fields
            if (!planName || !bonusType || !amount) {
                hasError = true;
                errorMessage = 'Plan Name, Bonus Type, and Amount/Percentage are required.';
            }

            // Validate amount/percentage
            if (amount && !isValidAmountOrPercentage(amount)) {
                hasError = true;
                errorMessage = 'Please enter a valid amount (e.g., 5000 or 5%).';
            }

            // Validate dates
            if (startDate && !isValidFutureDate(startDate)) {
                hasError = true;
                errorMessage = 'Start date cannot be in the past.';
            }

            if (endDate && !isValidFutureDate(endDate)) {
                hasError = true;
                errorMessage = 'End date cannot be in the past.';
            }

            // Validate date range
            if (startDate && endDate && !isValidDateRange(startDate, endDate)) {
                hasError = true;
                errorMessage = 'End date must be after start date.';
            }

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });

        // Edit Bonus Form Validation
        document.getElementById('editBonusForm')?.addEventListener('submit', function(e) {
            const planName = this.querySelector('[name="plan_name"]').value.trim();
            const bonusType = this.querySelector('[name="bonus_type"]').value;
            const amount = this.querySelector('[name="amount_or_percentage"]').value.trim();
            const startDate = this.querySelector('[name="start_date"]').value;
            const endDate = this.querySelector('[name="end_date"]').value;

            let hasError = false;
            let errorMessage = '';

            // Validate required fields
            if (!planName || !bonusType || !amount) {
                hasError = true;
                errorMessage = 'Plan Name, Bonus Type, and Amount/Percentage are required.';
            }

            // Validate amount/percentage
            if (amount && !isValidAmountOrPercentage(amount)) {
                hasError = true;
                errorMessage = 'Please enter a valid amount (e.g., 5000 or 5%).';
            }

            // Validate dates (for existing records, allow past dates for start date)
            if (endDate && !isValidFutureDate(endDate)) {
                hasError = true;
                errorMessage = 'End date cannot be in the past.';
            }

            // Validate date range
            if (startDate && endDate && !isValidDateRange(startDate, endDate)) {
                hasError = true;
                errorMessage = 'End date must be after start date.';
            }

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });

        // Create Allowance Form Validation
        document.getElementById('createAllowanceForm')?.addEventListener('submit', function(e) {
            const allowanceType = this.querySelector('[name="allowance_type"]').value.trim();
            const amount = this.querySelector('[name="amount"]').value;
            const frequency = this.querySelector('[name="frequency"]').value;

            let hasError = false;
            let errorMessage = '';

            // Validate required fields
            if (!allowanceType || !amount || !frequency) {
                hasError = true;
                errorMessage = 'Allowance Type, Amount, and Frequency are required.';
            }

            // Validate amount
            if (amount && !isValidPositiveNumber(amount)) {
                hasError = true;
                errorMessage = 'Please enter a valid positive amount.';
            }

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });

        // Edit Allowance Form Validation
        document.getElementById('editAllowanceForm')?.addEventListener('submit', function(e) {
            const allowanceType = this.querySelector('[name="allowance_type"]').value.trim();
            const amount = this.querySelector('[name="amount"]').value;
            const frequency = this.querySelector('[name="frequency"]').value;

            let hasError = false;
            let errorMessage = '';

            // Validate required fields
            if (!allowanceType || !amount || !frequency) {
                hasError = true;
                errorMessage = 'Allowance Type, Amount, and Frequency are required.';
            }

            // Validate amount
            if (amount && !isValidPositiveNumber(amount)) {
                hasError = true;
                errorMessage = 'Please enter a valid positive amount.';
            }

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });

        // FIXED: Prevent default submit on forms that should NOT submit to server
        // Exclude: edit forms, create forms, and delete forms (handled separately)
        document.querySelectorAll('form:not(#editSalaryForm):not(#editAllowanceForm):not(#editBonusForm):not(.create-form):not(.delete-form)').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();

                // Use SweetAlert toast for demo form submit feedback
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Submitted successfully',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });

                // Close all modals
                [salaryStructureModal, bonusModal, allowanceModal, editSalaryModal, editAllowanceModal, editBonusModal].forEach(modal => {
                    modal?.classList.add('hidden');
                });
            });
        });

        // Handle delete forms separately
        document.querySelectorAll('form.delete-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();

                // Get form type from action URL
                const action = form.getAttribute('action') || '';
                let itemName = 'record';
                if (action.includes('salary')) itemName = 'salary grade';
                if (action.includes('allowance')) itemName = 'allowance';
                if (action.includes('bonus')) itemName = 'bonus plan';

                Swal.fire({
                    title: `Delete this ${itemName}?`,
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'swal-btn-primary',
                        cancelButton: 'swal-btn-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Auto-hide update banner after 3 seconds
        (function() {
            const banner = document.getElementById('updateBanner');
            if (!banner) return;
            setTimeout(() => {
                banner.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(-8px)';
                setTimeout(() => {
                    if (banner && banner.parentNode) banner.parentNode.removeChild(banner);
                }, 450);
            }, 3000);
        })();

        // Show SweetAlert for delete result (if present in URL)
        // FIXED: Show appropriate banners/messages based on URL parameters
        (function() {
            const params = new URLSearchParams(window.location.search);

            // Remove the static banner if it exists
            const staticBanner = document.getElementById('updateBanner');
            if (staticBanner) {
                staticBanner.remove();
            }

            const updated = params.get('updated');

            if (updated === '1') {
                // Get the current URL to check which form was submitted
                const currentUrl = window.location.href;
                let message = 'Operation completed successfully.';
                let icon = 'success';

                // Check URL parameters or patterns to determine what was updated
                if (currentUrl.includes('allowance_created')) {
                    message = 'Allowance created successfully!';
                } else if (currentUrl.includes('allowance_updated')) {
                    message = 'Allowance updated successfully!';
                } else if (currentUrl.includes('salary_created')) {
                    message = 'Salary structure created successfully!';
                } else if (currentUrl.includes('salary_updated')) {
                    message = 'Salary structure updated successfully!';
                }

                // Show success notification
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: icon,
                    title: message,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                // Clean URL without reloading page (remove query parameters)
                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);

            } else if (updated === '0') {
                const error = params.get('error') || 'Operation failed';
                const msg = params.get('msg') ? decodeURIComponent(params.get('msg')) : '';

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg || error,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal-btn-primary'
                    }
                });

                // Clean URL after showing error
                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            }

            // Handle delete results
            const deleted = params.get('deleted');
            if (deleted === '1') {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Record was deleted successfully.',
                    showConfirmButton: false,
                    timer: 1600
                });

                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            } else if (deleted === '0') {
                const err = params.get('error') || 'failed';
                const msg = params.get('msg') ? decodeURIComponent(params.get('msg')) : '';

                Swal.fire({
                    icon: 'error',
                    title: 'Delete failed',
                    text: msg || err,
                    confirmButtonText: 'OK'
                });

                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            }
        })();

        // Open edit allowance modal (improved version)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-allowance-btn')) {
                e.preventDefault();
                const btn = e.target;

                // Get all data attributes
                const data = {
                    id: btn.getAttribute('data-id'),
                    type: btn.getAttribute('data-type'),
                    dept: btn.getAttribute('data-dept'),
                    amount: btn.getAttribute('data-amount'),
                    frequency: btn.getAttribute('data-frequency'),
                    criteria: btn.getAttribute('data-criteria'),
                    work_status: btn.getAttribute('data-work_status')
                };

                // Populate form fields
                const setValue = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) el.value = value || '';
                };

                setValue('edit_alw_id', data.id);
                setValue('edit_alw_type', data.type);
                setValue('edit_alw_dept', data.dept);
                setValue('edit_alw_amount', data.amount);
                setValue('edit_alw_freq', data.frequency);
                setValue('edit_alw_criteria', data.criteria);
                setValue('edit_alw_work_status', data.work_status);

                // Show modal
                editAllowanceModal?.classList.remove('hidden');
            }
        });

        // Allowance search functionality (with null check)
        const searchAllowancesInput = document.getElementById('searchAllowances');
        if (searchAllowancesInput) {
            searchAllowancesInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#allowanceTableBody tr');

                rows.forEach(row => {
                    if (row.classList.contains('no-data-row')) return;

                    const text = row.textContent.toLowerCase();
                    row.style.display = searchTerm === '' || text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Bonus Modal Functionality
        const editBonusModal = document.getElementById('editBonusModal');

        // Open edit bonus modal
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-bonus-btn')) {
                e.preventDefault();
                const btn = e.target;

                // Get all data attributes
                const data = {
                    id: btn.getAttribute('data-id'),
                    name: btn.getAttribute('data-name'),
                    type: btn.getAttribute('data-type'),
                    amount: btn.getAttribute('data-amount'),
                    dept: btn.getAttribute('data-dept'),
                    criteria: btn.getAttribute('data-criteria'),
                    start: btn.getAttribute('data-start'),
                    end: btn.getAttribute('data-end'),
                    work_status: btn.getAttribute('data-work_status')
                };

                // Populate form fields
                const setValue = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) el.value = value || '';
                };

                setValue('edit_bonus_id', data.id);
                setValue('edit_bonus_name', data.name);
                setValue('edit_bonus_type', data.type);
                setValue('edit_bonus_amount', data.amount);
                setValue('edit_bonus_dept', data.dept);
                setValue('edit_bonus_criteria', data.criteria);
                setValue('edit_bonus_start', data.start);
                setValue('edit_bonus_end', data.end);
                setValue('edit_bonus_work_status', data.work_status);

                // Show modal
                editBonusModal?.classList.remove('hidden');
            }
        });

        // Close edit bonus modal
        document.getElementById('closeEditBonusModal')?.addEventListener('click', () => editBonusModal?.classList.add('hidden'));
        document.getElementById('cancelEditBonus')?.addEventListener('click', () => editBonusModal?.classList.add('hidden'));

        // Close modal when clicking outside
        editBonusModal?.addEventListener('click', (e) => {
            if (e.target === editBonusModal) editBonusModal.classList.add('hidden');
        });

        // Bonus search functionality
        const searchBonusInput = document.getElementById('searchBonus');
        if (searchBonusInput) {
            searchBonusInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const cards = document.querySelectorAll('#bonusPlansContainer > div');

                let visibleCount = 0;

                cards.forEach(card => {
                    if (card.textContent.toLowerCase().includes(searchTerm)) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show message if no results
                const noResultsMsg = document.getElementById('noBonusResults');
                if (visibleCount === 0 && searchTerm !== '') {
                    if (!noResultsMsg) {
                        const msg = document.createElement('div');
                        msg.id = 'noBonusResults';
                        msg.className = 'col-span-3 p-4 text-gray-500 text-center';
                        msg.textContent = 'No bonus plans found matching your search.';
                        document.getElementById('bonusPlansContainer').appendChild(msg);
                    }
                } else if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            });
        }

        // Handle bonus-specific URL parameters
        (function() {
            const params = new URLSearchParams(window.location.search);

            // Handle bonus creation/update/deletion results
            const bonusCreated = params.get('bonus_created');
            const bonusUpdated = params.get('bonus_updated');
            const bonusDeleted = params.get('bonus_deleted');

            if (bonusCreated === '1') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Bonus plan created successfully!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                // Clean URL
                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            } else if (bonusCreated === '0') {
                const error = params.get('error') || 'Creation failed';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal-btn-primary'
                    }
                });

                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            }

            if (bonusUpdated === '1') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Bonus plan updated successfully!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            } else if (bonusUpdated === '0') {
                const error = params.get('error') || 'Update failed';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal-btn-primary'
                    }
                });

                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            }

            if (bonusDeleted === '1') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Bonus plan deleted successfully!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            } else if (bonusDeleted === '0') {
                const error = params.get('error') || 'Deletion failed';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal-btn-primary'
                    }
                });

                setTimeout(() => {
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            }
        })();

        // Form validation for bonus forms
        document.getElementById('createBonusForm')?.addEventListener('submit', function(e) {
            const planName = this.querySelector('[name="plan_name"]').value.trim();
            const bonusType = this.querySelector('[name="bonus_type"]').value;
            const amount = this.querySelector('[name="amount_or_percentage"]').value.trim();

            if (!planName || !bonusType || !amount) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill in all required fields (Plan Name, Bonus Type, and Amount).',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal-btn-primary'
                    }
                });
            }
        });

        document.getElementById('editBonusForm')?.addEventListener('submit', function(e) {
            const planName = this.querySelector('[name="plan_name"]').value.trim();
            const bonusType = this.querySelector('[name="bonus_type"]').value;
            const amount = this.querySelector('[name="amount_or_percentage"]').value.trim();

            if (!planName || !bonusType || !amount) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill in all required fields (Plan Name, Bonus Type, and Amount).',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal-btn-primary'
                    }
                });
            }
        });
    </script>
</body>

</html>