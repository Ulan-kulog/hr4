<?php
session_start();
include("../connection.php");

// Database connection
$db_name = "HR_4";
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

if (!function_exists('status_class')) {
    function status_class($status)
    {
        $s = strtolower((string)$status);
        if ($s === 'active') return 'bg-green-100 text-green-800';
        if (strpos($s, 'pending') !== false) return 'bg-yellow-100 text-yellow-800';
        return 'bg-gray-100 text-gray-800';
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
$allowances = [];
$sql_alw = "SELECT * FROM allowances ORDER BY id DESC";
if ($res_alw = mysqli_query($conn, $sql_alw)) {
    while ($ralw = mysqli_fetch_assoc($res_alw)) {
        $allowances[] = $ralw;
    }
}

// Helper function for allowance frequency display
if (!function_exists('format_frequency')) {
    function format_frequency($freq)
    {
        $map = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'annual' => 'Annual'
        ];
        return $map[strtolower($freq)] ?? ucfirst($freq);
    }
}
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
                                    <h3 class="mt-1 font-bold text-3xl">₱18.2M</h3>
                                    <p class="mt-1 text-gray-500 text-xs">Annual budget</p>
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
                                    <h3 class="mt-1 font-bold text-3xl">₱25.4K</h3>
                                    <p class="mt-1 text-gray-500 text-xs">Per month</p>
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
                                    <h3 class="mt-1 font-bold text-3xl">₱2.4M</h3>
                                    <p class="mt-1 text-gray-500 text-xs">This year</p>
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
                                    <h3 class="mt-1 font-bold text-3xl">₱1.8M</h3>
                                    <p class="mt-1 text-gray-500 text-xs">Annual allocation</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="package" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
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
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Min Salary</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Max Salary</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Employees</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Actions</th>
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
                                            $minRaw = $row['min_salary'] ?? $row['min'] ?? '';
                                            $maxRaw = $row['max_salary'] ?? $row['max'] ?? '';
                                            $minSalary = format_currency($minRaw);
                                            $maxSalary = format_currency($maxRaw);
                                            $status = htmlspecialchars($row['status'] ?? 'Active');
                                            $employees = isset($row['employees']) ? (int)$row['employees'] : (isset($row['employees_count']) ? (int)$row['employees_count'] : '-');
                                            $dept = htmlspecialchars($row['department'] ?? $row['dept'] ?? '');
                                            $statusClass = status_class($status);
                                        ?>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-gray-900 text-sm"><?= $grade ?></td>
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $position ?></td>
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $minSalary ?></td>
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $maxSalary ?></td>
                                                <td class="px-4 py-3 text-gray-900 text-sm"><?= $employees ?></td>
                                                <td class="px-4 py-3"><span class="inline-flex items-center <?= $statusClass ?> px-2.5 py-0.5 rounded-full font-medium text-xs"><?= $status ?></span></td>
                                                <td class="px-4 py-3">
                                                    <div class="flex gap-2">
                                                        <a href="#" class="font-medium text-blue-600 hover:text-blue-800 text-sm edit-salary-btn" data-id="<?= $id ?>" data-grade="<?= $grade ?>" data-position="<?= $position ?>" data-min="<?= htmlspecialchars($minRaw) ?>" data-max="<?= htmlspecialchars($maxRaw) ?>" data-status="<?= $status ?>">Edit</a>
                                                        <form method="POST" action="API/delete_salary_grade.php" class="inline delete-form">
                                                            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
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

                    <!-- Bonus & Incentives Section -->
                    <div class="bg-white shadow-sm mb-6 p-6 border border-gray-100 rounded-xl">
                        <h3 class="mb-4 font-semibold text-gray-800">Bonus & Incentive Plans</h3>
                        <div class="gap-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <?php if (empty($bonus_plans)): ?>
                                <div class="p-4 text-gray-500">No bonus or incentive plans found.</div>
                            <?php else: ?>
                                <?php foreach ($bonus_plans as $plan):
                                    $bp_id = htmlspecialchars($plan['id'] ?? '');
                                    $bp_name = htmlspecialchars($plan['plan_name'] ?? $plan['title'] ?? 'Untitled Plan');
                                    $bp_dept = htmlspecialchars($plan['department'] ?? 'All Departments');
                                    $bp_desc = htmlspecialchars($plan['description'] ?? $plan['details'] ?? '');
                                    $bp_type = htmlspecialchars($plan['type'] ?? '');
                                    $bp_amount_raw = $plan['amount_or_percentage'] ?? $plan['value'] ?? '';
                                    // display percentage or formatted currency
                                    if ($bp_amount_raw === '' || $bp_amount_raw === null) {
                                        $bp_amount = '-';
                                    } elseif (strpos((string)$bp_amount_raw, '%') !== false) {
                                        $bp_amount = htmlspecialchars($bp_amount_raw);
                                    } else {
                                        $bp_amount = format_currency($bp_amount_raw);
                                    }
                                ?>
                                    <div class="p-4 border border-gray-200 rounded-lg">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-semibold text-gray-800"><?= $bp_name ?></h4>
                                            <span class="bg-green-100 p-2 rounded-lg text-green-600">
                                                <i data-lucide="gift" class="w-4 h-4"></i>
                                            </span>
                                        </div>
                                        <p class="mb-2 text-gray-600 text-sm">Department: <?= $bp_dept ?></p>
                                        <?php if ($bp_desc !== ''): ?><p class="mb-3 text-gray-600 text-sm"><?= $bp_desc ?></p><?php endif; ?>
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium text-gray-700 text-sm">Amount: <?= $bp_amount ?></span>
                                            <div class="flex gap-1">
                                                <button class="text-blue-600 hover:text-blue-800 text-sm edit-bonus-btn" data-id="<?= $bp_id ?>">Edit</button>
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
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100" id="allowanceTableBody">
                                    <?php if (empty($allowances)): ?>
                                        <tr>
                                            <td colspan="7" class="px-4 py-3 text-gray-500 text-sm text-center">No allowances found. <button class="text-blue-600 hover:text-blue-800" onclick="document.getElementById('allowanceBtn').click()">Create one</button></td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($allowances as $allowance):
                                            $alw_id = htmlspecialchars($allowance['id']);
                                            $alw_type = htmlspecialchars($allowance['allowance_type']);
                                            $alw_dept = htmlspecialchars($allowance['department'] ?? 'All');
                                            $alw_amount = format_currency($allowance['amount']);
                                            $alw_freq = format_frequency($allowance['frequency']);
                                            $alw_criteria = htmlspecialchars($allowance['eligibility_criteria'] ?? '');
                                            $alw_status = htmlspecialchars($allowance['status']);
                                            $statusClass = status_class($alw_status);
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
                                                    <span class="inline-flex items-center <?= $statusClass ?> px-2.5 py-0.5 rounded-full font-medium text-xs">
                                                        <?= ucfirst($alw_status) ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex gap-2">
                                                        <button class="font-medium text-blue-600 hover:text-blue-800 text-sm edit-allowance-btn"
                                                            data-id="<?= $alw_id ?>"
                                                            data-type="<?= $alw_type ?>"
                                                            data-dept="<?= $alw_dept ?>"
                                                            data-amount="<?= htmlspecialchars($allowance['amount']) ?>"
                                                            data-frequency="<?= $allowance['frequency'] ?>"
                                                            data-criteria="<?= htmlspecialchars($allowance['eligibility_criteria'] ?? '') ?>"
                                                            data-status="<?= $alw_status ?>">
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

        <!-- Salary Structure Modal -->
        <div id="salaryStructureModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
            <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Create Salary Structure</h3>
                    <button id="closeSalaryModal" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form id="createSalaryForm" class="space-y-4 create-form" method="POST" action="API/create_salary_grade.php">
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Grade Level</label>
                            <input name="grade_name" type="text" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., G1">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Position Title</label>
                            <input name="position" type="text" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., Junior Staff">
                        </div>
                    </div>
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Minimum Salary</label>
                            <input name="min_salary" type="number" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="15000">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Maximum Salary</label>
                            <input name="max_salary" type="number" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="25000">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Department</label>
                        <select name="department" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                            <option value="">Select Department</option>
                            <option value="hotel">Hotel Department</option>
                            <option value="restaurant">Restaurant Department</option>
                            <option value="hr">HR Department</option>
                            <option value="logistic">Logistic Department</option>
                            <option value="all">All Departments</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancelSalary" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition-colors">
                            Create Structure
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Salary Structure Modal -->
        <div id="editSalaryModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
            <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Edit Salary Structure</h3>
                    <button id="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form id="editSalaryForm" class="space-y-4" method="POST" action="API/update_salary_grade.php">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Grade Level</label>
                            <input id="edit_grade" name="grade" type="text" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full" placeholder="e.g., G1">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Position Title</label>
                            <input id="edit_position" name="position" type="text" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full" placeholder="e.g., Junior Staff">
                        </div>
                    </div>
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Minimum Salary</label>
                            <input id="edit_min" name="min_salary" type="number" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full" placeholder="15000">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Maximum Salary</label>
                            <input id="edit_max" name="max_salary" type="number" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full" placeholder="25000">
                        </div>
                    </div>
                    <!-- Department removed from edit modal per request -->
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Status</label>
                        <select id="edit_status" name="status" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                            <option value="Active">Active</option>
                            <option value="Pending Review">Pending Review</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancelEdit" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700">Cancel</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bonus & Incentives Modal -->
        <div id="bonusModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
            <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Create Bonus & Incentive Plan</h3>
                    <button id="closeBonusModal" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Plan Name</label>
                        <input type="text" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., Sales Commission Plan">
                    </div>
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Bonus Type</label>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="commission">Commission</option>
                                <option value="performance">Performance Bonus</option>
                                <option value="referral">Referral Bonus</option>
                                <option value="seasonal">Seasonal Incentive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Amount/Percentage</label>
                            <input type="text" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., 5% or ₱2,000">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Eligibility Criteria</label>
                        <textarea class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full h-20" placeholder="Describe eligibility requirements..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancelBonus" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors">
                            Create Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Allowance Modal -->
        <!-- Allowance Modal -->
        <div id="allowanceModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
            <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Create Allowance</h3>
                    <button id="closeAllowanceModal" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form id="createAllowanceForm" class="space-y-4 create-form" method="POST" action="API/create_allowance.php">
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Allowance Type *</label>
                        <input name="allowance_type" type="text" required class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., Transportation, Meal, Uniform">
                    </div>

                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Department</label>
                            <select name="department" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="All">All Departments</option>
                                <option value="hotel">Hotel Department</option>
                                <option value="restaurant">Restaurant Department</option>
                                <option value="hr">HR Department</option>
                                <option value="logistic">Logistic Department</option>
                                <option value="administrative">Administrative</option>
                                <option value="financial">Financial</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Frequency *</label>
                            <select name="frequency" required class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly" selected>Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Amount (₱) *</label>
                        <input name="amount" type="number" step="0.01" min="0" required class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="2000">
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Eligibility Criteria</label>
                        <textarea name="eligibility_criteria" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full h-20" placeholder="Describe eligibility requirements (optional)"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancelAllowance" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white transition-colors">
                            Create Allowance
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Allowance Modal -->
        <div id="editAllowanceModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
            <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Edit Allowance</h3>
                    <button id="closeEditAllowanceModal" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form id="editAllowanceForm" class="space-y-4" method="POST" action="API/update_allowance.php">
                    <input type="hidden" id="edit_alw_id" name="id">

                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Allowance Type *</label>
                        <input id="edit_alw_type" name="allowance_type" type="text" required class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                    </div>

                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Department</label>
                            <select id="edit_alw_dept" name="department" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                                <option value="All">All Departments</option>
                                <option value="hotel">Hotel Department</option>
                                <option value="restaurant">Restaurant Department</option>
                                <option value="hr">HR Department</option>
                                <option value="logistic">Logistic Department</option>
                                <option value="administrative">Administrative</option>
                                <option value="financial">Financial</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Frequency *</label>
                            <select id="edit_alw_freq" name="frequency" required class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Amount (₱) *</label>
                        <input id="edit_alw_amount" name="amount" type="number" step="0.01" min="0" required class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Eligibility Criteria</label>
                        <textarea id="edit_alw_criteria" name="eligibility_criteria" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full h-20"></textarea>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Status</label>
                        <select id="edit_alw_status" name="status" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancelEditAllowance" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700">Cancel</button>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            lucide.createIcons();

            // Initialize Charts
            const salaryCtx = document.getElementById('salaryChart').getContext('2d');
            const salaryChart = new Chart(salaryCtx, {
                type: 'bar',
                data: {
                    labels: ['Hotel', 'Restaurant', 'HR', 'Logistic', 'Admin', 'Financial'],
                    datasets: [{
                        label: 'Average Salary (₱)',
                        data: [28000, 22000, 35000, 25000, 32000, 40000],
                        backgroundColor: [
                            '#3B82F6',
                            '#10B981',
                            '#8B5CF6',
                            '#F59E0B',
                            '#EF4444',
                            '#06B6D4'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            const mixCtx = document.getElementById('compensationMixChart').getContext('2d');
            const compensationMixChart = new Chart(mixCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Base Salary', 'Bonuses', 'Allowances', 'Benefits'],
                    datasets: [{
                        data: [65, 15, 12, 8],
                        backgroundColor: [
                            '#3B82F6',
                            '#10B981',
                            '#8B5CF6',
                            '#F59E0B'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Modal Functionality
            const salaryStructureModal = document.getElementById('salaryStructureModal');
            const bonusModal = document.getElementById('bonusModal');
            const allowanceModal = document.getElementById('allowanceModal');
            const editSalaryModal = document.getElementById('editSalaryModal');

            // Open modals
            document.getElementById('salaryStructureBtn').addEventListener('click', () => {
                salaryStructureModal.classList.remove('hidden');
            });

            document.getElementById('bonusIncentivesBtn').addEventListener('click', () => {
                bonusModal.classList.remove('hidden');
            });

            document.getElementById('allowanceBtn').addEventListener('click', () => {
                allowanceModal.classList.remove('hidden');
            });

            // Close modals
            document.getElementById('closeSalaryModal').addEventListener('click', () => salaryStructureModal.classList.add('hidden'));
            document.getElementById('closeBonusModal').addEventListener('click', () => bonusModal.classList.add('hidden'));
            document.getElementById('closeAllowanceModal').addEventListener('click', () => allowanceModal.classList.add('hidden'));
            document.getElementById('closeEditModal').addEventListener('click', () => editSalaryModal.classList.add('hidden'));

            document.getElementById('cancelSalary').addEventListener('click', () => salaryStructureModal.classList.add('hidden'));
            document.getElementById('cancelBonus').addEventListener('click', () => bonusModal.classList.add('hidden'));
            document.getElementById('cancelAllowance').addEventListener('click', () => allowanceModal.classList.add('hidden'));
            document.getElementById('cancelEdit').addEventListener('click', () => editSalaryModal.classList.add('hidden'));

            // Close modals when clicking outside
            salaryStructureModal.addEventListener('click', (e) => {
                if (e.target === salaryStructureModal) salaryStructureModal.classList.add('hidden');
            });
            bonusModal.addEventListener('click', (e) => {
                if (e.target === bonusModal) bonusModal.classList.add('hidden');
            });
            allowanceModal.addEventListener('click', (e) => {
                if (e.target === allowanceModal) allowanceModal.classList.add('hidden');
            });
            editSalaryModal.addEventListener('click', (e) => {
                if (e.target === editSalaryModal) editSalaryModal.classList.add('hidden');
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
                    const status = btn.getAttribute('data-status');

                    document.getElementById('edit_id').value = id || '';
                    document.getElementById('edit_grade').value = grade || '';
                    document.getElementById('edit_position').value = position || '';
                    document.getElementById('edit_min').value = min || '';
                    document.getElementById('edit_max').value = max || '';
                    // Set status select with case-insensitive matching to handle DB value variations
                    const statusEl = document.getElementById('edit_status');
                    if (status) {
                        // try direct match first
                        if (Array.from(statusEl.options).some(o => o.value === status)) {
                            statusEl.value = status;
                        } else {
                            // case-insensitive fallback
                            const match = Array.from(statusEl.options).find(o => o.value.toLowerCase() === status.toLowerCase());
                            statusEl.value = match ? match.value : 'Active';
                        }
                    } else {
                        statusEl.value = 'Active';
                    }

                    editSalaryModal.classList.remove('hidden');
                });
            });

            // Prevent default submit on non-edit forms (allow edit and create forms to POST)
            document.querySelectorAll('form:not(#editSalaryForm)').forEach(form => {
                form.addEventListener('submit', (e) => {
                    // For delete forms show SweetAlert confirmation then submit
                    if (form.classList.contains('delete-form')) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Delete this salary grade?',
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
                        return;
                    }

                    // Allow create form to submit to server normally
                    if (form.classList.contains('create-form')) {
                        return;
                    }

                    e.preventDefault();
                    // Use SweetAlert toast for non-edit form submit feedback
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Submitted',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    salaryStructureModal.classList.add('hidden');
                    bonusModal.classList.add('hidden');
                    allowanceModal.classList.add('hidden');
                    editSalaryModal.classList.add('hidden');
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
            (function() {
                const params = new URLSearchParams(window.location.search);
                const deleted = params.get('deleted');
                if (!deleted) return;
                if (deleted === '1') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Salary grade was deleted successfully.',
                        showConfirmButton: false,
                        timer: 1600
                    });
                } else {
                    const err = params.get('error') || 'failed';
                    const msg = params.get('msg') ? decodeURIComponent(params.get('msg')) : '';
                    const text = msg ? `${err} — ${msg}` : err;
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete failed',
                        text: text,
                        confirmButtonText: 'OK'
                    });
                }
            })();

            // Edit Allowance Modal functionality
            const editAllowanceModal = document.getElementById('editAllowanceModal');

            // Open edit allowance modal
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('edit-allowance-btn')) {
                    e.preventDefault();
                    const btn = e.target;

                    document.getElementById('edit_alw_id').value = btn.getAttribute('data-id');
                    document.getElementById('edit_alw_type').value = btn.getAttribute('data-type');
                    document.getElementById('edit_alw_dept').value = btn.getAttribute('data-dept');
                    document.getElementById('edit_alw_amount').value = btn.getAttribute('data-amount');
                    document.getElementById('edit_alw_freq').value = btn.getAttribute('data-frequency');
                    document.getElementById('edit_alw_criteria').value = btn.getAttribute('data-criteria');
                    document.getElementById('edit_alw_status').value = btn.getAttribute('data-status');

                    editAllowanceModal.classList.remove('hidden');
                }
            });

            // Close edit allowance modal
            document.getElementById('closeEditAllowanceModal').addEventListener('click', () => {
                editAllowanceModal.classList.add('hidden');
            });
            document.getElementById('cancelEditAllowance').addEventListener('click', () => {
                editAllowanceModal.classList.add('hidden');
            });
            editAllowanceModal.addEventListener('click', (e) => {
                if (e.target === editAllowanceModal) editAllowanceModal.classList.add('hidden');
            });

            // Allowance search functionality
            const searchAllowancesInput = document.getElementById('searchAllowances');
            if (searchAllowancesInput) {
                searchAllowancesInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#allowanceTableBody tr');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // SweetAlert for allowance delete success/failure
            (function() {
                const params = new URLSearchParams(window.location.search);
                const allowanceDeleted = params.get('allowance_deleted');
                if (!allowanceDeleted) return;

                if (allowanceDeleted === '1') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Allowance was deleted successfully.',
                        showConfirmButton: false,
                        timer: 1600
                    });
                } else {
                    const err = params.get('error') || 'failed';
                    const msg = params.get('msg') ? decodeURIComponent(params.get('msg')) : '';
                    const text = msg ? `${err} — ${msg}` : err;
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete failed',
                        text: text,
                        confirmButtonText: 'OK'
                    });
                }
            })();
        </script>
</body>

</html>