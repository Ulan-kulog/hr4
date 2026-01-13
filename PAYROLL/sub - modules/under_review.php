<?php
session_start();
include("../../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die("❌ Connection not found for $db_name");
}
$conn = $connections[$db_name];

// Fetch ONLY employees under review (status = 'Under Review')
$review_query = "SELECT * FROM employees WHERE status = 'Under Review' ORDER BY full_name";
$review_result = $conn->query($review_query);

// Calculate statistics for under review employees only
$stats_query = "SELECT 
                COUNT(*) as total_review,
                SUM(CASE WHEN department IS NULL THEN 1 ELSE 0 END) as no_department,
                SUM(basic_salary) as total_salary,
                AVG(basic_salary) as avg_salary
                FROM employees WHERE status = 'Under Review'";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Fetch payroll data for employees under review ONLY
$payroll_query = "SELECT p.*, e.full_name, e.employee_id, e.department, e.position, e.status as emp_status
                  FROM payroll p 
                  JOIN employees e ON p.employee_id = e.id 
                  WHERE e.status = 'Under Review' 
                  AND p.period = DATE_FORMAT(NOW(), '%Y-%m')
                  ORDER BY p.status ASC";
$payroll_result = $conn->query($payroll_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Review Employees - HR Management</title>
    <?php include '../../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-base-100 min-h-screen bg-white">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <?php include '../../INCLUDES/sidebar.php'; ?>

    <!-- Content Area -->
    <div class="flex flex-col flex-1 overflow-auto">
        <!-- Navbar -->
        <?php include '../../INCLUDES/navbar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Under Review Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-yellow-100/50 text-yellow-600">
                            <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                        </span>
                        Employees Under Review
                        <span class="ml-2 text-sm bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                            Only "Under Review" status shown
                        </span>
                    </h2>
                    <div class="flex gap-2">
                        <button onclick="exportToExcel()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                            Export
                        </button>
                        <button onclick="refreshData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                            Refresh
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Under Review -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Total Under Review</p>
                                <h3 class="text-3xl font-bold mt-1"><?php echo $stats['total_review'] ?? 0; ?></h3>
                                <p class="text-xs text-gray-500 mt-1">Employees</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="users" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- No Department -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">No Department</p>
                                <h3 class="text-3xl font-bold mt-1"><?php echo $stats['no_department'] ?? 0; ?></h3>
                                <p class="text-xs text-gray-500 mt-1">Floating employees</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="user-x" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Salary -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Total Salary</p>
                                <h3 class="text-3xl font-bold mt-1">₱<?php echo number_format($stats['total_salary'] ?? 0, 2); ?></h3>
                                <p class="text-xs text-gray-500 mt-1">Monthly cost</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="dollar-sign" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Average Salary -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Average Salary</p>
                                <h3 class="text-3xl font-bold mt-1">₱<?php echo number_format($stats['avg_salary'] ?? 0, 2); ?></h3>
                                <p class="text-xs text-gray-500 mt-1">Per employee</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Status for Under Review Employees ONLY -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="font-semibold text-gray-800">Payroll Status - <?php echo date('F Y'); ?></h3>
                            <span class="text-sm text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full">
                                Only employees with "Under Review" status
                            </span>
                            <div class="flex gap-2">
                                <input type="text" id="searchPayroll" placeholder="Search payroll..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-64">
                                <select id="payrollStatusFilter" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">All Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Hold">Hold</option>
                                    <option value="Declined">Declined</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payroll Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php 
                                if($payroll_result->num_rows > 0):
                                    while($payroll = $payroll_result->fetch_assoc()): 
                                        // Check if employee is still under review (for payroll entries)
                                        $isEmployeeUnderReview = ($payroll['emp_status'] === 'Under Review');
                                ?>
                                <tr class="payroll-row" data-id="<?php echo $payroll['id']; ?>" data-status="<?php echo $payroll['status']; ?>" data-emp-status="<?php echo $payroll['emp_status']; ?>">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-yellow-600 text-sm font-medium">
                                                    <?php echo substr($payroll['full_name'], 0, 2); ?>
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($payroll['full_name']); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($payroll['employee_id']); ?></p>
                                                <?php if(!$isEmployeeUnderReview): ?>
                                                <span class="text-xs text-red-500">Employee status changed</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if($payroll['department']): ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($payroll['department']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                No Department
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱<?php echo number_format($payroll['basic_salary'], 2); ?></td>
                                    <td class="px-4 py-3 text-sm text-green-600">₱<?php echo number_format($payroll['overtime_pay'], 2); ?></td>
                                    <td class="px-4 py-3 text-sm text-red-600">₱<?php echo number_format($payroll['deductions'], 2); ?></td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱<?php echo number_format($payroll['net_pay'], 2); ?></td>
                                    <td class="px-4 py-3">
                                        <?php 
                                        $status_class = [
                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                            'Approved' => 'bg-green-100 text-green-800',
                                            'Hold' => 'bg-orange-100 text-orange-800',
                                            'Declined' => 'bg-red-100 text-red-800',
                                            'Paid' => 'bg-blue-100 text-blue-800'
                                        ];
                                        ?>
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $status_class[$payroll['status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $payroll['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button onclick="viewPayrollDetails(<?php echo $payroll['id']; ?>, '<?php echo $payroll['emp_status']; ?>')" 
                                                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else: ?>
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        No payroll records found for employees under review
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Under Review Employees Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="font-semibold text-gray-800">Employees Under Review</h3>
                            <div class="flex gap-2">
                                <input type="text" id="searchReview" placeholder="Search employees..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-64">
                                <button class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <i data-lucide="filter" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hire Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php 
                                if($review_result->num_rows > 0):
                                    while($row = $review_result->fetch_assoc()): 
                                ?>
                                <tr class="review-row" data-id="<?php echo $row['id']; ?>" data-status="<?php echo $row['status']; ?>">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-yellow-600 text-sm font-medium">
                                                    <?php echo substr($row['full_name'], 0, 2); ?>
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($row['full_name']); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($row['employee_id']); ?></p>
                                                <span class="text-xs text-yellow-600">Under Review</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if($row['department']): ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($row['department']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                No Department
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($row['position']); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱<?php echo number_format($row['basic_salary'], 2); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-500"><?php echo date('M d, Y', strtotime($row['hire_date'])); ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button onclick="viewEmployeeDetails(<?php echo $row['id']; ?>)" 
                                                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else: ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        No employees are currently under review
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

<!-- View Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-800" id="detailsTitle">Employee Details</h3>
            <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div id="detailsContent" class="mb-6">
            <!-- Content will be loaded here -->
        </div>
        
        <!-- Action Buttons Footer - FIXED ALIGNMENT -->
        <div class="border-t border-gray-200 pt-4 mt-6">
            <!-- Employee Actions -->
            <div id="employeeActions" class="hidden">
                <div class="flex flex-row flex-wrap gap-3 justify-end">
                    <button onclick="requestBudget()" class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center min-w-[150px]">
                        <i data-lucide="dollar-sign" class="w-4 h-4 mr-2"></i>
                        Request Budget
                    </button>
                    <button onclick="forCompliance()" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center min-w-[150px]">
                        <i data-lucide="clipboard-check" class="w-4 h-4 mr-2"></i>
                        For Compliance
                    </button>
                    <button onclick="regularizeEmployee()" class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center min-w-[150px]">
                        <i data-lucide="user-check" class="w-4 h-4 mr-2"></i>
                        Regularize
                    </button>
                    <button onclick="rejectEmployee()" class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center min-w-[150px]">
                        <i data-lucide="user-x" class="w-4 h-4 mr-2"></i>
                        Reject
                    </button>
                </div>
            </div>
            
            <!-- Payroll Actions -->
            <div id="payrollActions" class="hidden">
                <div class="flex flex-row flex-wrap gap-3 justify-end">
                    <!-- Dynamic buttons will be inserted here -->
                </div>
            </div>
            
            <!-- Status Warning -->
            <div id="statusWarning" class="hidden">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-2"></i>
                        <p class="text-red-700 font-medium">This employee is no longer under review. Actions are disabled.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Action Notes Modal -->
    <div id="actionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2" id="actionTitle">Action Required</h3>
            <p class="text-sm text-gray-600 mb-4" id="actionDescription"></p>
            <textarea id="actionNotes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4" placeholder="Enter notes here..."></textarea>
            <div class="flex justify-end gap-3 mt-4">
                <button onclick="closeActionModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="submitAction()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Submit
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        let currentEmployeeId = null;
        let currentPayrollId = null;
        let currentActionType = null;
        let currentDetailsType = null; // 'employee' or 'payroll'
        let currentEmployeeStatus = null; // Store current employee status

        // Search functionality for payroll table
        document.getElementById('searchPayroll').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.payroll-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Payroll status filter
        document.getElementById('payrollStatusFilter').addEventListener('change', function(e) {
            const status = e.target.value;
            const rows = document.querySelectorAll('.payroll-row');
            
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (!status || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Search functionality for employees
        document.getElementById('searchReview').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.review-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // View Employee Details
        async function viewEmployeeDetails(employeeId) {
            try {
                const response = await fetch('../API/employee_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=get_employee_details&employee_id=${employeeId}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const employee = result.data;
                    currentEmployeeId = employeeId;
                    currentEmployeeStatus = employee.status;
                    currentDetailsType = 'employee';
                    
                    document.getElementById('detailsTitle').textContent = 'Employee Details';
                    
                    // Check if employee is still under review
                    const isUnderReview = (employee.status === 'Under Review');
                    
                    // Show employee actions only if under review
                    if (isUnderReview) {
                        document.getElementById('employeeActions').classList.remove('hidden');
                        document.getElementById('statusWarning').classList.add('hidden');
                    } else {
                        document.getElementById('employeeActions').classList.add('hidden');
                        document.getElementById('statusWarning').classList.remove('hidden');
                    }
                    
                    document.getElementById('payrollActions').classList.add('hidden');
                    
                    const content = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                    Basic Information
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Full Name:</span>
                                        <span class="font-medium">${employee.full_name}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Employee ID:</span>
                                        <span class="font-medium">${employee.employee_id}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="font-medium">${employee.email || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Position:</span>
                                        <span class="font-medium">${employee.position}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Department & Status -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <i data-lucide="building" class="w-4 h-4 mr-2"></i>
                                    Department & Status
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Department:</span>
                                        <span class="font-medium">${employee.department || 'No Department'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="px-2 py-1 text-xs rounded-full ${employee.status === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : (employee.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')}">
                                            ${employee.status}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Hire Date:</span>
                                        <span class="font-medium">${employee.hire_date}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Basic Salary:</span>
                                        <span class="font-medium">₱${parseFloat(employee.basic_salary).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Employee Notes -->
                            <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                    Notes & Remarks
                                </h4>
                                <div id="employeeNotesDisplay" class="text-sm text-gray-700 mb-3">
                                    ${employee.notes ? employee.notes.replace(/\n/g, '<br>') : 'No notes available'}
                                </div>
                                ${isUnderReview ? `
                                <textarea id="employeeNotesInput" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Add or update notes about this employee...">${employee.notes || ''}</textarea>
                                <div class="flex justify-end gap-3 mt-3">
                                    <button onclick="saveEmployeeNotes()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Save Notes
                                    </button>
                                </div>
                                ` : '<p class="text-gray-500 text-sm">Notes editing disabled - employee status changed</p>'}
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('detailsContent').innerHTML = content;
                    lucide.createIcons();
                    document.getElementById('detailsModal').classList.remove('hidden');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Network error occurred'
                });
            }
        }

        // View Payroll Details
async function viewPayrollDetails(payrollId, employeeStatus) {
    try {
        const response = await fetch('../API/payroll_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_payroll_details&payroll_id=${payrollId}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            const payroll = result.data;
            currentPayrollId = payrollId;
            currentEmployeeStatus = employeeStatus;
            currentDetailsType = 'payroll';
            
            document.getElementById('detailsTitle').textContent = 'Payroll Details';
            
            // Check if employee is under review
            const isUnderReview = (employeeStatus === 'Under Review');
            
            // Show payroll actions, hide employee actions
            document.getElementById('payrollActions').classList.remove('hidden');
            document.getElementById('employeeActions').classList.add('hidden');
            
            if (!isUnderReview) {
                document.getElementById('statusWarning').classList.remove('hidden');
            } else {
                document.getElementById('statusWarning').classList.add('hidden');
            }
            
            // Update action buttons based on current status and employee status
            updatePayrollActionButtons(payroll.status, isUnderReview);
            
            const content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Employee Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                            Employee Information
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Full Name:</span>
                                <span class="font-medium">${payroll.full_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Employee ID:</span>
                                <span class="font-medium">${payroll.employee_id}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Department:</span>
                                <span class="font-medium">${payroll.department || 'No Department'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Position:</span>
                                <span class="font-medium">${payroll.position}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Employee Status:</span>
                                <span class="px-2 py-1 text-xs rounded-full ${employeeStatus === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : (employeeStatus === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')}">
                                    ${employeeStatus}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payroll Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i data-lucide="dollar-sign" class="w-4 h-4 mr-2"></i>
                            Payroll Summary
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Period:</span>
                                <span class="font-medium">${payroll.period}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Basic Salary:</span>
                                <span class="font-medium">₱${parseFloat(payroll.basic_salary).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Overtime:</span>
                                <span class="font-medium text-green-600">₱${parseFloat(payroll.overtime_pay).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Deductions:</span>
                                <span class="font-medium text-red-600">₱${parseFloat(payroll.deductions).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-600 font-semibold">Net Pay:</span>
                                <span class="font-bold text-lg">₱${parseFloat(payroll.net_pay).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Breakdown -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i data-lucide="pie-chart" class="w-4 h-4 mr-2"></i>
                            Detailed Breakdown
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-3 rounded-lg border">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Earnings</h5>
                                <div class="space-y-1">
                                    <div class="flex justify-between">
                                        <span>Basic Salary:</span>
                                        <span>₱${parseFloat(payroll.basic_salary).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Overtime Pay:</span>
                                        <span>₱${parseFloat(payroll.overtime_pay).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Allowances:</span>
                                        <span>₱${parseFloat(payroll.allowances).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-1 font-medium">
                                        <span>Total Earnings:</span>
                                        <span>₱${(parseFloat(payroll.basic_salary) + parseFloat(payroll.overtime_pay) + parseFloat(payroll.allowances)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white p-3 rounded-lg border">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Deductions</h5>
                                <div class="space-y-1">
                                    <div class="flex justify-between">
                                        <span>Tax:</span>
                                        <span>₱${(parseFloat(payroll.deductions) * 0.3).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>SSS/PhilHealth:</span>
                                        <span>₱${(parseFloat(payroll.deductions) * 0.4).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Other Deductions:</span>
                                        <span>₱${(parseFloat(payroll.deductions) * 0.3).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-1 font-medium">
                                        <span>Total Deductions:</span>
                                        <span class="text-red-600">₱${parseFloat(payroll.deductions).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white p-3 rounded-lg border">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Status & Notes</h5>
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-gray-600">Current Status:</span>
                                        <span class="px-2 py-1 text-xs rounded-full ${getStatusClass(payroll.status)} ml-2">${payroll.status}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Notes:</span>
                                        <p class="text-sm text-gray-700 mt-1">${payroll.notes || 'No notes available'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detailsContent').innerHTML = content;
            lucide.createIcons();
            document.getElementById('detailsModal').classList.remove('hidden');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: result.message
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Network error occurred'
        });
    }
}

function updatePayrollActionButtons(currentStatus, isEmployeeUnderReview) {
    const payrollActionsDiv = document.querySelector('#payrollActions > div');
    
    // Clear existing buttons
    payrollActionsDiv.innerHTML = '';
    
    // Disable all actions if employee is not under review
    if (!isEmployeeUnderReview) {
        payrollActionsDiv.innerHTML = `
            <button class="px-4 py-2.5 bg-gray-400 text-white rounded-lg cursor-not-allowed flex items-center justify-center min-w-[130px]">
                <i data-lucide="lock" class="w-4 h-4 mr-2"></i>
                Actions Locked
            </button>
            <button onclick="printPayroll()" class="px-4 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                Print
            </button>
        `;
        lucide.createIcons();
        return;
    }
    
    // Create buttons based on current status (only if employee is under review)
    const buttons = [];
    
    if (currentStatus === 'Pending') {
        buttons.push(`
            <button onclick="approvePayroll()" class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                Approve
            </button>
        `);
        
        buttons.push(`
            <button onclick="holdPayroll()" class="px-4 py-2.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="pause" class="w-4 h-4 mr-2"></i>
                Hold
            </button>
        `);
        
        buttons.push(`
            <button onclick="declinePayroll()" class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                Decline
            </button>
        `);
    } 
    else if (currentStatus === 'Approved') {
        buttons.push(`
            <button onclick="markAsPaid()" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                Mark as Paid
            </button>
        `);
        
        buttons.push(`
            <button onclick="holdPayroll()" class="px-4 py-2.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="pause" class="w-4 h-4 mr-2"></i>
                Put on Hold
            </button>
        `);
    }
    else if (currentStatus === 'Hold') {
        buttons.push(`
            <button onclick="approvePayroll()" class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                Approve
            </button>
        `);
        
        buttons.push(`
            <button onclick="declinePayroll()" class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                Decline
            </button>
        `);
    }
    else if (currentStatus === 'Declined') {
        buttons.push(`
            <button onclick="approvePayroll()" class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                Re-approve
            </button>
        `);
    }
    // For Paid status, show minimal options or none
    else if (currentStatus === 'Paid') {
        buttons.push(`
            <button class="px-4 py-2.5 bg-gray-400 text-white rounded-lg cursor-not-allowed flex items-center justify-center min-w-[130px]">
                <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                Already Paid
            </button>
        `);
    }
    
    // Add edit button for all statuses except Paid (only if under review)
    if (currentStatus !== 'Paid') {
        buttons.push(`
            <button onclick="editPayroll()" class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center min-w-[130px]">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit
            </button>
        `);
    }
    
    // Add print button
    buttons.push(`
        <button onclick="printPayroll()" class="px-4 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center min-w-[130px]">
            <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
            Print
        </button>
    `);
    
    // Set the HTML and create icons
    payrollActionsDiv.innerHTML = buttons.join('\n');
    lucide.createIcons();
}

// Update the getStatusClass function
function getStatusClass(status) {
    const statusClasses = {
        'Pending': 'bg-yellow-100 text-yellow-800',
        'Approved': 'bg-green-100 text-green-800',
        'Hold': 'bg-orange-100 text-orange-800',
        'Declined': 'bg-red-100 text-red-800',
        'Paid': 'bg-blue-100 text-blue-800'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
}

// Employee rejection function
function rejectEmployee() {
    Swal.fire({
        title: 'Reject Employee?',
        text: "This will change employee status to Rejected and remove them from under review list.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, reject!',
        cancelButtonText: 'Cancel',
        input: 'textarea',
        inputLabel: 'Reason for rejection:',
        inputPlaceholder: 'Enter the reason for rejection...',
        inputAttributes: {
            'aria-label': 'Enter the reason for rejection'
        },
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Please provide a reason for rejection!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateEmployeeStatus(currentEmployeeId, 'Rejected', result.value);
        }
    });
}
        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
            currentEmployeeId = null;
            currentPayrollId = null;
            currentDetailsType = null;
            currentEmployeeStatus = null;
            // Hide all action buttons
            document.getElementById('employeeActions').classList.add('hidden');
            document.getElementById('payrollActions').classList.add('hidden');
            document.getElementById('statusWarning').classList.add('hidden');
        }

        // Employee Actions (from modal footer)
        function requestBudget() {
            currentActionType = 'budget';
            document.getElementById('actionTitle').textContent = 'Request Budget';
            document.getElementById('actionDescription').textContent = 'Please provide details for the budget request:';
            document.getElementById('actionNotes').placeholder = 'Enter budget details, amount needed, and justification...';
            document.getElementById('actionModal').classList.remove('hidden');
        }

        function forCompliance() {
            currentActionType = 'compliance';
            document.getElementById('actionTitle').textContent = 'For Compliance';
            document.getElementById('actionDescription').textContent = 'List compliance requirements:';
            document.getElementById('actionNotes').placeholder = 'Enter compliance requirements, deadlines, and notes...';
            document.getElementById('actionModal').classList.remove('hidden');
        }

        function regularizeEmployee() {
            Swal.fire({
                title: 'Regularize Employee?',
                text: "This will change employee status from Under Review to Active.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8B5CF6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, regularize!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateEmployeeStatus(currentEmployeeId, 'Active', 'Employee regularized');
                }
            });
        }

        // Payroll Actions (from modal footer)
        function approvePayroll() {
            // Check if employee is still under review
            if (currentEmployeeStatus !== 'Under Review') {
                Swal.fire({
                    icon: 'error',
                    title: 'Action Denied',
                    text: 'Cannot modify payroll for employees no longer under review'
                });
                return;
            }
            
            Swal.fire({
                title: 'Approve Payroll?',
                text: "This will approve the payroll for processing.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updatePayrollStatus(currentPayrollId, 'Approved');
                }
            });
        }

        function holdPayroll() {
            // Check if employee is still under review
            if (currentEmployeeStatus !== 'Under Review') {
                Swal.fire({
                    icon: 'error',
                    title: 'Action Denied',
                    text: 'Cannot modify payroll for employees no longer under review'
                });
                return;
            }
            
            currentActionType = 'Hold';
            document.getElementById('actionTitle').textContent = 'Hold Payroll';
            document.getElementById('actionDescription').textContent = 'Why is this payroll being put on hold?';
            document.getElementById('actionNotes').placeholder = 'Enter hold notes...';
            document.getElementById('actionModal').classList.remove('hidden');
        }

        function declinePayroll() {
            // Check if employee is still under review
            if (currentEmployeeStatus !== 'Under Review') {
                Swal.fire({
                    icon: 'error',
                    title: 'Action Denied',
                    text: 'Cannot modify payroll for employees no longer under review'
                });
                return;
            }
            
            currentActionType = 'Declined';
            document.getElementById('actionTitle').textContent = 'Decline Payroll';
            document.getElementById('actionDescription').textContent = 'Why is this payroll being declined?';
            document.getElementById('actionNotes').placeholder = 'Enter decline reason...';
            document.getElementById('actionModal').classList.remove('hidden');
        }

        function markAsPaid() {
            // Check if employee is still under review
            if (currentEmployeeStatus !== 'Under Review') {
                Swal.fire({
                    icon: 'error',
                    title: 'Action Denied',
                    text: 'Cannot modify payroll for employees no longer under review'
                });
                return;
            }
            
            Swal.fire({
                title: 'Mark as Paid?',
                text: "This will mark the payroll as paid.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8B5CF6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, mark as paid!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updatePayrollStatus(currentPayrollId, 'Paid');
                }
            });
        }

        function editPayroll() {
            // Check if employee is still under review
            if (currentEmployeeStatus !== 'Under Review') {
                Swal.fire({
                    icon: 'error',
                    title: 'Action Denied',
                    text: 'Cannot modify payroll for employees no longer under review'
                });
                return;
            }
            
            Swal.fire({
                title: 'Edit Payroll',
                text: "This feature allows you to edit payroll details.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#8B5CF6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Open Editor'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would typically open an edit form
                    Swal.fire({
                        title: 'Edit Form',
                        html: `
                            <div class="text-left">
                                <p class="mb-4">Payroll editor would open here with form fields.</p>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Basic Salary</label>
                                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Enter amount">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Overtime Hours</label>
                                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Enter hours">
                                    </div>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Save Changes',
                        cancelButtonText: 'Cancel',
                        preConfirm: () => {
                            // Handle form submission here
                            return Promise.resolve();
                        }
                    });
                }
            });
        }

        function printPayroll() {
            Swal.fire({
                title: 'Print Payroll',
                text: "This will generate a printable payroll slip.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#6B7280',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Generate PDF'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'PDF Generated!',
                        text: 'Payroll slip is ready for download.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('actionNotes').value = '';
            currentActionType = null;
        }

        async function submitAction() {
            const notes = document.getElementById('actionNotes').value.trim();
            if (!notes) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Notes Required',
                    text: 'Please add notes for this action.'
                });
                return;
            }

            try {
                let url = '';
                let data = new FormData();
                
                if (currentActionType === 'Hold' || currentActionType === 'Declined') {
                    // For payroll actions
                    url = '../API/payroll_api.php';
                    data.append('action', 'update_status');
                    data.append('payroll_id', currentPayrollId);
                    data.append('status', currentActionType);
                    data.append('notes', notes);
                } else if (currentActionType === 'budget' || currentActionType === 'compliance') {
                    // For employee actions
                    url = '../API/employee_api.php';
                    data.append('action', 'update_status');
                    data.append('employee_id', currentEmployeeId);
                    data.append('type', currentActionType);
                    data.append('notes', notes);
                }

                const response = await fetch(url, {
                    method: 'POST',
                    body: data
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        closeActionModal();
                        closeDetailsModal();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Network error occurred'
                });
            }
        }

        async function updatePayrollStatus(payrollId, status, notes = '') {
            try {
                const response = await fetch('../API/payroll_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_status&payroll_id=${payrollId}&status=${status}&notes=${encodeURIComponent(notes)}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        closeDetailsModal();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Network error occurred'
                });
            }
        }

        async function updateEmployeeStatus(employeeId, status, notes = '') {
            try {
                const response = await fetch('../API/employee_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_employee_status&employee_id=${employeeId}&status=${status}&notes=${encodeURIComponent(notes)}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        closeDetailsModal();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Network error occurred'
                });
            }
        }

        async function saveEmployeeNotes() {
            // Check if employee is still under review
            if (currentEmployeeStatus !== 'Under Review') {
                Swal.fire({
                    icon: 'error',
                    title: 'Action Denied',
                    text: 'Cannot edit notes for employees no longer under review'
                });
                return;
            }
            
            const notes = document.getElementById('employeeNotesInput').value.trim();
            
            try {
                const response = await fetch('../API/employee_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=save_notes&employee_id=${currentEmployeeId}&notes=${encodeURIComponent(notes)}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update the displayed notes immediately
                    document.getElementById('employeeNotesDisplay').innerHTML = notes.replace(/\n/g, '<br>') || 'No notes available';
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Notes saved successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Network error occurred'
                });
            }
        }

        function exportToExcel() {
            // Simple export to CSV
            const rows = document.querySelectorAll('.review-row');
            let csv = 'Employee ID,Full Name,Department,Position,Salary,Hire Date,Status\n';
            
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 5) {
                        const employeeId = cells[0].querySelector('.text-gray-500')?.textContent || '';
                        const fullName = cells[0].querySelector('.font-medium')?.textContent || '';
                        const department = cells[1].textContent.trim();
                        const position = cells[2].textContent.trim();
                        const salary = cells[3].textContent.trim();
                        const hireDate = cells[4].textContent.trim();
                        const status = 'Under Review';
                        
                        csv += `"${employeeId}","${fullName}","${department}","${position}","${salary}","${hireDate}","${status}"\n`;
                    }
                }
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `under_review_employees_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            
            Swal.fire({
                icon: 'success',
                title: 'Exported!',
                text: 'Data exported to CSV file.',
                timer: 1500,
                showConfirmButton: false
            });
        }

        function refreshData() {
            location.reload();
        }

        // Close modal on outside click
        document.getElementById('detailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailsModal();
            }
        });

        document.getElementById('actionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeActionModal();
            }
        });
    </script>
</body>
</html>