<?php
// compensation_management.php - Employee Compensation System
require_once 'DB.php';

// Check user permissions/session
session_start();
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../login.php');
//     exit();
// }

// Get user role and ID from session
$user_id = $_SESSION['user_id'] ?? 0;
$user_role = $_SESSION['user_role'] ?? 'employee'; // admin, manager, hr, finance, payroll, employee
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Compensation Management System</title>
    <?php include '../INCLUDES/header.php'; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../INCLUDES/sidebar.php'; ?>

        <!-- Content Area -->
        <div class="flex flex-col flex-1 overflow-auto">
            <!-- Navbar -->
            <?php include '../INCLUDES/navbar.php'; ?>

            <!-- Main Content Area -->
            <div class="space-y-6 p-6">
                <!-- Header Section -->
                <div class="bg-white shadow-sm p-6 rounded-lg">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="font-bold text-gray-800 text-2xl">Employee Compensation Management</h1>
                            <p class="mt-1 text-gray-600">Manage employee compensation, bonuses, allowances, and benefits</p>
                        </div>
                        <?php if (in_array($user_role, ['admin', 'hr', 'payroll'])): ?>
                            <button id="newCompensationBtn" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white">
                                <i data-lucide="plus-circle"></i>
                                Add Compensation
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Compensation Overview Stats -->
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-4 mb-6">
                        <!-- Total Compensation -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-blue-600 text-sm">Total Compensation</p>
                                    <p class="font-bold text-gray-800 text-2xl">
                                        <?php
                                        $sql = "SELECT COALESCE(SUM(base_salary + allowance_amount + bonus_amount), 0) as total 
                                                FROM employee_compensations 
                                                WHERE status = 'active'";
                                        if ($user_role === 'employee') {
                                            $sql .= " AND employee_id = ?";
                                            $total = Database::fetchColumn($sql, [$user_id]);
                                        } else {
                                            $total = Database::fetchColumn($sql);
                                        }
                                        echo '₱' . number_format($total, 2);
                                        ?>
                                    </p>
                                </div>
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <i data-lucide="dollar-sign" class="w-6 h-6 text-blue-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Payroll -->
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-green-600 text-sm">Monthly Payroll</p>
                                    <p class="font-bold text-gray-800 text-2xl">
                                        <?php
                                        $sql = "SELECT COALESCE(SUM(base_salary/12), 0) as monthly 
                                                FROM employee_compensations 
                                                WHERE status = 'active'";
                                        if ($user_role === 'employee') {
                                            $sql .= " AND employee_id = ?";
                                            $monthly = Database::fetchColumn($sql, [$user_id]);
                                        } else {
                                            $monthly = Database::fetchColumn($sql);
                                        }
                                        echo '₱' . number_format($monthly, 2);
                                        ?>
                                    </p>
                                </div>
                                <div class="bg-green-100 p-2 rounded-full">
                                    <i data-lucide="calendar" class="w-6 h-6 text-green-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Average Salary -->
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-purple-600 text-sm">Average Salary</p>
                                    <p class="font-bold text-gray-800 text-2xl">
                                        <?php
                                        $sql = "SELECT COALESCE(AVG(base_salary), 0) as avg_salary 
                                                FROM employee_compensations 
                                                WHERE status = 'active'";
                                        if ($user_role === 'employee') {
                                            $sql .= " AND employee_id = ?";
                                            $avg = Database::fetchColumn($sql, [$user_id]);
                                        } else {
                                            $avg = Database::fetchColumn($sql);
                                        }
                                        echo '₱' . number_format($avg, 2);
                                        ?>
                                    </p>
                                </div>
                                <div class="bg-purple-100 p-2 rounded-full">
                                    <i data-lucide="trending-up" class="w-6 h-6 text-purple-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Active Employees -->
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-orange-600 text-sm">Active Employees</p>
                                    <p class="font-bold text-gray-800 text-2xl">
                                        <?php
                                        $sql = "SELECT COUNT(DISTINCT employee_id) 
                                                FROM employee_compensations 
                                                WHERE status = 'active'";
                                        $active = Database::fetchColumn($sql);
                                        echo $active;
                                        ?>
                                    </p>
                                </div>
                                <div class="bg-orange-100 p-2 rounded-full">
                                    <i data-lucide="users" class="w-6 h-6 text-orange-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Compensation Summary (for HR/Admin) -->
                    <?php if (in_array($user_role, ['admin', 'hr', 'payroll'])): ?>
                        <div class="mb-6">
                            <h3 class="mb-3 font-bold text-gray-700">Compensation Summary by Department</h3>
                            <div class="overflow-x-auto">
                                <table class="bg-white border border-gray-200 min-w-full">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-4 py-2 border-b text-left">Department</th>
                                            <th class="px-4 py-2 border-b text-left">Avg Base Salary</th>
                                            <th class="px-4 py-2 border-b text-left">Avg Total Comp</th>
                                            <th class="px-4 py-2 border-b text-left">Employee Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $dept_sql = "SELECT 
                                                e.department,
                                                AVG(ec.base_salary) as avg_base,
                                                AVG(ec.base_salary + ec.allowance_amount + ec.bonus_amount) as avg_total,
                                                COUNT(DISTINCT ec.employee_id) as emp_count
                                            FROM employee_compensations ec
                                            JOIN employees e ON ec.employee_id = e.id
                                            WHERE ec.status = 'active'
                                            GROUP BY e.department
                                            ORDER BY avg_total DESC";
                                        $dept_summary = Database::fetchAll($dept_sql);
                                        foreach ($dept_summary as $dept): ?>
                                            <tr>
                                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($dept->department) ?></td>
                                                <td class="px-4 py-2 border-b">₱<?= number_format($dept->avg_base, 2) ?></td>
                                                <td class="px-4 py-2 border-b">₱<?= number_format($dept->avg_total, 2) ?></td>
                                                <td class="px-4 py-2 border-b"><?= $dept->emp_count ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Compensation Structure Info -->
                <div class="bg-white shadow-sm p-6 rounded-lg">
                    <h3 class="mb-4 font-bold text-gray-700">Compensation Structure Information</h3>
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-3">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="mb-2 font-semibold text-blue-700">Base Salary Structure</h4>
                            <ul class="space-y-1 text-gray-600 text-sm">
                                <li>• Monthly base salary</li>
                                <li>• Paid on 15th and 30th</li>
                                <li>• Annual review cycle</li>
                                <li>• Performance-based increments</li>
                            </ul>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="mb-2 font-semibold text-green-700">Allowances & Benefits</h4>
                            <ul class="space-y-1 text-gray-600 text-sm">
                                <li>• Transportation allowance</li>
                                <li>• Meal allowance</li>
                                <li>• Medical benefits</li>
                                <li>• Housing allowance</li>
                                <li>• Communication allowance</li>
                            </ul>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="mb-2 font-semibold text-purple-700">Bonuses & Incentives</h4>
                            <ul class="space-y-1 text-gray-600 text-sm">
                                <li>• Performance bonus (up to 20%)</li>
                                <li>• 13th month pay</li>
                                <li>• Year-end bonus</li>
                                <li>• Project completion bonus</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white shadow-sm p-4 rounded-lg">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Employee</label>
                            <select id="employeeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="">All Employees</option>
                                <?php
                                $employees = Database::fetchAll("SELECT id, first_name, last_name FROM employees ORDER BY last_name");
                                foreach ($employees as $emp): ?>
                                    <option value="<?= $emp->id ?>"><?= htmlspecialchars($emp->last_name . ', ' . $emp->first_name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="flex-1 min-w-[200px]">
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Compensation Type</label>
                            <select id="typeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="">All Types</option>
                                <option value="salary">Base Salary</option>
                                <option value="allowance">Allowance</option>
                                <option value="bonus">Bonus</option>
                                <option value="incentive">Incentive</option>
                            </select>
                        </div>

                        <div class="flex-1 min-w-[200px]">
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Status</label>
                            <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                                <option value="under_review">Under Review</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button id="applyFilters" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white">
                                Apply Filters
                            </button>
                            <button id="clearFilters" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg text-gray-800">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Compensation Table -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table id="compensationsTable" class="divide-y divide-gray-200 min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Employee</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Position</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Base Salary</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Allowances</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Bonuses</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Total Comp</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Effective Date</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                    // Build query based on user role
                                    $sql = "SELECT 
                                                ec.*,
                                                e.first_name,
                                                e.last_name,
                                                e.employee_code,
                                                e.department,
                                                e.position,
                                                e.hire_date,
                                                (ec.base_salary + ec.allowance_amount + ec.bonus_amount) as total_compensation
                                            FROM employee_compensations ec
                                            JOIN employees e ON ec.employee_id = e.id";

                                    $params = [];

                                    if ($user_role === 'employee') {
                                        $sql .= " WHERE ec.employee_id = ?";
                                        $params = [$user_id];
                                    }

                                    $sql .= " ORDER BY ec.effective_date DESC";

                                    $compensations = Database::fetchAll($sql, $params);

                                    foreach ($compensations as $row) {
                                        // Get status color
                                        $statusColor = 'bg-gray-100 text-gray-800';
                                        switch ($row->status) {
                                            case 'active':
                                                $statusColor = 'bg-green-100 text-green-800';
                                                break;
                                            case 'pending':
                                                $statusColor = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'inactive':
                                                $statusColor = 'bg-red-100 text-red-800';
                                                break;
                                            case 'under_review':
                                                $statusColor = 'bg-blue-100 text-blue-800';
                                                break;
                                        }
                                    ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div class="font-medium text-gray-900">
                                                        <?= htmlspecialchars($row->first_name . ' ' . $row->last_name) ?>
                                                    </div>
                                                    <div class="text-gray-500 text-sm">
                                                        <?= htmlspecialchars($row->employee_code) ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                                <?= htmlspecialchars($row->position) ?>
                                                <div class="text-gray-500 text-sm">
                                                    <?= htmlspecialchars($row->department) ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                ₱<?= number_format($row->base_salary, 2) ?>
                                                <div class="text-gray-500 text-xs">
                                                    <?= $row->pay_frequency ? ucfirst($row->pay_frequency) : 'Monthly' ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-medium text-green-600 whitespace-nowrap">
                                                ₱<?= number_format($row->allowance_amount, 2) ?>
                                                <div class="text-gray-500 text-xs">
                                                    <?= $row->allowance_type ? ucfirst(str_replace('_', ' ', $row->allowance_type)) : 'N/A' ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-medium text-purple-600 whitespace-nowrap">
                                                ₱<?= number_format($row->bonus_amount, 2) ?>
                                                <div class="text-gray-500 text-xs">
                                                    <?= $row->bonus_type ? ucfirst($row->bonus_type) : 'N/A' ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-bold text-blue-600 whitespace-nowrap">
                                                ₱<?= number_format($row->total_compensation, 2) ?>
                                                <div class="text-gray-500 text-xs">
                                                    Annual
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                                <?= date('M d, Y', strtotime($row->effective_date)) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= $statusColor ?>">
                                                    <?= ucfirst(str_replace('_', ' ', htmlspecialchars($row->status))) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <button onclick="viewCompensation(<?= $row->id ?>)"
                                                        class="flex items-center gap-1 text-blue-600 hover:text-blue-900"
                                                        title="View Details">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                        View
                                                    </button>

                                                    <?php if (in_array($user_role, ['admin', 'hr', 'payroll'])): ?>
                                                        <button onclick="editCompensation(<?= $row->id ?>)"
                                                            class="flex items-center gap-1 text-green-600 hover:text-green-900"
                                                            title="Edit Compensation">
                                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                                            Edit
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if ($user_role === 'admin' && $row->status === 'pending'): ?>
                                                        <button onclick="approveCompensation(<?= $row->id ?>)"
                                                            class="flex items-center gap-1 text-indigo-600 hover:text-indigo-900"
                                                            title="Approve Compensation">
                                                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Compensation Modal -->
            <div id="newCompensationModal" class="hidden z-50 fixed inset-0 bg-gray-600 bg-opacity-50 w-full h-full overflow-y-auto">
                <div class="top-20 relative bg-white shadow-lg mx-auto p-5 border rounded-lg w-full max-w-4xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800 text-xl">New Compensation Entry</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form id="compensationForm" class="space-y-4">
                        <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                            <!-- Employee Selection -->
                            <div>
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Employee *</label>
                                <select name="employee_id" id="employee_id" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                    <option value="">Select Employee</option>
                                    <?php foreach ($employees as $emp): ?>
                                        <option value="<?= $emp->id ?>"><?= htmlspecialchars($emp->last_name . ', ' . $emp->first_name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Compensation Type -->
                            <div>
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Compensation Type *</label>
                                <select name="compensation_type" id="compensation_type" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                    <option value="">Select Type</option>
                                    <option value="base_salary">Base Salary</option>
                                    <option value="allowance">Allowance</option>
                                    <option value="bonus">Bonus</option>
                                    <option value="incentive">Incentive</option>
                                    <option value="adjustment">Adjustment</option>
                                </select>
                            </div>

                            <!-- Base Salary -->
                            <div id="baseSalaryField">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Base Salary (Annual) *</label>
                                <input type="number" step="0.01" name="base_salary" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                                    placeholder="0.00">
                            </div>

                            <!-- Pay Frequency -->
                            <div>
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Pay Frequency *</label>
                                <select name="pay_frequency" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                    <option value="monthly">Monthly</option>
                                    <option value="bi-weekly">Bi-weekly</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="semi-monthly">Semi-monthly</option>
                                </select>
                            </div>

                            <!-- Allowance Amount -->
                            <div id="allowanceField">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Allowance Amount</label>
                                <input type="number" step="0.01" name="allowance_amount"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                                    placeholder="0.00">
                            </div>

                            <!-- Allowance Type -->
                            <div id="allowanceTypeField">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Allowance Type</label>
                                <select name="allowance_type"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                    <option value="">Select Type</option>
                                    <option value="transportation">Transportation</option>
                                    <option value="meal">Meal</option>
                                    <option value="housing">Housing</option>
                                    <option value="communication">Communication</option>
                                    <option value="medical">Medical</option>
                                    <option value="uniform">Uniform</option>
                                </select>
                            </div>

                            <!-- Bonus Amount -->
                            <div id="bonusField">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Bonus Amount</label>
                                <input type="number" step="0.01" name="bonus_amount"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                                    placeholder="0.00">
                            </div>

                            <!-- Bonus Type -->
                            <div id="bonusTypeField">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Bonus Type</label>
                                <select name="bonus_type"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                    <option value="">Select Type</option>
                                    <option value="performance">Performance</option>
                                    <option value="year_end">Year-end</option>
                                    <option value="13th_month">13th Month</option>
                                    <option value="signing">Signing</option>
                                    <option value="retention">Retention</option>
                                </select>
                            </div>

                            <!-- Effective Date -->
                            <div>
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Effective Date *</label>
                                <input type="date" name="effective_date" value="<?= date('Y-m-d') ?>" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Status *</label>
                                <select name="status" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="under_review">Under Review</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Description *</label>
                                <textarea name="description" rows="3" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                                    placeholder="Enter compensation details, reasons for adjustment, or additional information..."></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t">
                            <button type="button" onclick="closeModal()"
                                class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white">
                                Save Compensation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#compensationsTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [
                    [6, 'desc']
                ] // Sort by effective date descending
            });

            lucide.createIcons();
        });

        // Modal Functions
        document.getElementById('newCompensationBtn')?.addEventListener('click', function() {
            document.getElementById('newCompensationModal').classList.remove('hidden');
            document.getElementById('compensationForm').reset();
        });

        function closeModal() {
            document.getElementById('newCompensationModal').classList.add('hidden');
        }

        // Form Submission
        document.getElementById('compensationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'create_compensation');

            // Submit via AJAX
            $.ajax({
                url: 'API/submit_compensation.php',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to save compensation. Please try again.',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });

        // Action Functions
        function viewCompensation(id) {
            window.location.href = 'view_compensation.php?id=' + id;
        }

        function editCompensation(id) {
            window.location.href = 'edit_compensation.php?id=' + id;
        }

        function approveCompensation(id) {
            Swal.fire({
                title: 'Approve Compensation',
                text: "Are you sure you want to approve this compensation?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'API/update_compensation.php',
                        method: 'POST',
                        data: {
                            id: id,
                            status: 'active',
                            action: 'approve'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Approved!',
                                'Compensation has been approved.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to approve compensation.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }

        // Filter Functions
        document.getElementById('applyFilters').addEventListener('click', function() {
            const employee = document.getElementById('employeeFilter').value;
            const type = document.getElementById('typeFilter').value;
            const status = document.getElementById('statusFilter').value;

            const table = $('#compensationsTable').DataTable();
            table.columns().search('').draw();

            if (employee) {
                // Assuming employee name is in first column
                table.column(0).search(employee, true, false).draw();
            }
            if (status) {
                table.column(7).search(status, true, false).draw();
            }
        });

        document.getElementById('clearFilters').addEventListener('click', function() {
            document.getElementById('employeeFilter').value = '';
            document.getElementById('typeFilter').value = '';
            document.getElementById('statusFilter').value = '';

            const table = $('#compensationsTable').DataTable();
            table.columns().search('').draw();
            table.search('').draw();
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Compensation Type Change Handler
        document.getElementById('compensation_type').addEventListener('change', function() {
            const type = this.value;
            const baseField = document.getElementById('baseSalaryField');
            const allowanceField = document.getElementById('allowanceField');
            const allowanceTypeField = document.getElementById('allowanceTypeField');
            const bonusField = document.getElementById('bonusField');
            const bonusTypeField = document.getElementById('bonusTypeField');

            // Show/hide relevant fields based on type
            if (type === 'base_salary') {
                baseField.style.display = 'block';
                allowanceField.style.display = 'none';
                allowanceTypeField.style.display = 'none';
                bonusField.style.display = 'none';
                bonusTypeField.style.display = 'none';
            } else if (type === 'allowance') {
                baseField.style.display = 'none';
                allowanceField.style.display = 'block';
                allowanceTypeField.style.display = 'block';
                bonusField.style.display = 'none';
                bonusTypeField.style.display = 'none';
            } else if (type === 'bonus' || type === 'incentive') {
                baseField.style.display = 'none';
                allowanceField.style.display = 'none';
                allowanceTypeField.style.display = 'none';
                bonusField.style.display = 'block';
                bonusTypeField.style.display = 'block';
            } else {
                baseField.style.display = 'block';
                allowanceField.style.display = 'block';
                allowanceTypeField.style.display = 'block';
                bonusField.style.display = 'block';
                bonusTypeField.style.display = 'block';
            }
        });
    </script>
</body>

</html>