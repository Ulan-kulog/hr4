<?php
session_start();
include("../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die("❌ Connection not found for $db_name");
}
$conn = $connections[$db_name];

// Fetch payroll data for ALL employees (regardless of status)
$current_month = date('Y-m');
$payroll_query = "SELECT p.*, e.full_name, e.employee_id, e.department, e.position, e.status as emp_status
                  FROM payroll p 
                  JOIN employees e ON p.employee_id = e.id 
                  WHERE p.period = '$current_month' 
                  ORDER BY p.status ASC";
$payroll_result = $conn->query($payroll_query);

// Calculate statistics for ALL payrolls
$stats_query = "SELECT 
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'Approved' OR status = 'Paid' THEN 1 ELSE 0 END) as processed,
                SUM(CASE WHEN status IN ('Approved', 'Paid') THEN net_pay ELSE 0 END) as total_payroll,
                SUM(overtime_pay) as total_overtime
                FROM payroll WHERE period = '$current_month'";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Processing - Payroll Management</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-base-100 min-h-screen bg-white">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <?php include '../INCLUDES/sidebar.php'; ?>

    <!-- Content Area -->
    <div class="flex flex-col flex-1 overflow-auto">
        <!-- Navbar -->
        <?php include '../INCLUDES/navbar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Payroll Processing Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-orange-100/50 text-orange-600">
                            <i data-lucide="calculator" class="w-5 h-5"></i>
                        </span>
                        Payroll Processing
                        <span class="ml-2 text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            All Employee Statuses
                        </span>
                    </h2>
                    <div class="flex gap-2">
                        <select id="departmentFilter" class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">All Departments</option>
                            <option value="Hotel">Hotel Department</option>
                            <option value="Restaurant">Restaurant Department</option>
                            <option value="HR">HR Department</option>
                            <option value="Logistic">Logistic Department</option>
                            <option value="Administrative">Administrative Department</option>
                            <option value="Financial">Financial Department</option>
                        </select>
                        <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Hold">Hold</option>
                            <option value="Declined">Declined</option>
                            <option value="Paid">Paid</option>
                        </select>
                        <button onclick="processAllPayroll()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i data-lucide="play" class="w-4 h-4 inline mr-2"></i>
                            Process All
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Pending Processing -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Pending Processing</p>
                                <h3 class="text-3xl font-bold mt-1"><?php echo $stats['pending'] ?? 0; ?></h3>
                                <p class="text-xs text-gray-500 mt-1">Awaiting calculation</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="clock" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Processed This Month -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Processed This Month</p>
                                <h3 class="text-3xl font-bold mt-1"><?php echo $stats['processed'] ?? 0; ?></h3>
                                <p class="text-xs text-gray-500 mt-1">Employee payrolls</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="check-circle" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Payroll Amount -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Total Payroll</p>
                                <h3 class="text-3xl font-bold mt-1">₱<?php echo number_format($stats['total_payroll'] ?? 0, 2); ?></h3>
                                <p class="text-xs text-gray-500 mt-1">This month</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="dollar-sign" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Overtime Payments -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Overtime Payments</p>
                                <h3 class="text-3xl font-bold mt-1">₱<?php echo number_format($stats['total_overtime'] ?? 0, 2); ?></h3>
                                <p class="text-xs text-gray-500 mt-1">This period</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="watch" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Processing Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="font-semibold text-gray-800">Payroll Processing - <?php echo date('F Y'); ?></h3>
                            <span class="text-sm text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                All employee statuses included
                            </span>
                            <div class="flex gap-2">
                                <input type="text" id="searchInput" placeholder="Search employees..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-64">
                                <button onclick="clearFilters()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i data-lucide="filter-x" class="w-4 h-4 mr-2"></i>
                                    Clear Filters
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payroll Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="payrollTable">
                                <?php while($row = $payroll_result->fetch_assoc()): ?>
                                <tr class="payroll-row" data-department="<?php echo htmlspecialchars($row['department'] ?? 'No Department'); ?>" data-status="<?php echo $row['status']; ?>" data-id="<?php echo $row['id']; ?>" data-emp-status="<?php echo $row['emp_status']; ?>">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-blue-600 text-sm font-medium">
                                                    <?php echo substr($row['full_name'], 0, 2); ?>
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($row['full_name']); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($row['employee_id']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
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
                                    <td class="px-4 py-3">
                                        <?php 
                                        $emp_status_class = [
                                            'Active' => 'bg-green-100 text-green-800',
                                            'Under Review' => 'bg-yellow-100 text-yellow-800',
                                            'Rejected' => 'bg-red-100 text-red-800',
                                            'Inactive' => 'bg-gray-100 text-gray-800'
                                        ];
                                        ?>
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $emp_status_class[$row['emp_status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $row['emp_status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱<?php echo number_format($row['basic_salary'], 2); ?></td>
                                    <td class="px-4 py-3 text-sm text-green-600">₱<?php echo number_format($row['overtime_pay'], 2); ?></td>
                                    <td class="px-4 py-3 text-sm text-red-600">₱<?php echo number_format($row['deductions'], 2); ?></td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱<?php echo number_format($row['net_pay'], 2); ?></td>
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
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $status_class[$row['status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button onclick="viewPayrollDetails(<?php echo $row['id']; ?>, '<?php echo $row['emp_status']; ?>')" 
                                                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
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
                <h3 class="text-xl font-semibold text-gray-800" id="detailsTitle">Payroll Details</h3>
                <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div id="detailsContent" class="mb-6">
                <!-- Content will be loaded here -->
            </div>
            
            <!-- Action Buttons Footer -->
            <div class="border-t border-gray-200 pt-4 mt-6">
                <!-- Employee Status Warning -->
                <div id="statusWarning" class="hidden">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-2"></i>
                            <p class="text-red-700 font-medium">This employee is no longer under review. Some actions may be restricted.</p>
                        </div>
                    </div>
                </div>
                
                <div id="payrollActions" class="hidden">
                    <div class="flex flex-row flex-wrap gap-3 justify-end">
                        <!-- Dynamic buttons will be inserted here -->
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
        
        let currentPayrollId = null;
        let currentAction = null;
        let currentEmployeeStatus = null;

        // Department Filter
        document.getElementById('departmentFilter').addEventListener('change', function() {
            filterTable();
        });

        // Status Filter
        document.getElementById('statusFilter').addEventListener('change', function() {
            filterTable();
        });

        function filterTable() {
            const department = document.getElementById('departmentFilter').value;
            const status = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('.payroll-row');
            
            rows.forEach(row => {
                const rowDept = row.getAttribute('data-department');
                const rowStatus = row.getAttribute('data-status');
                
                const showDept = department === 'all' || rowDept === department;
                const showStatus = status === 'all' || rowStatus === status;
                
                row.style.display = (showDept && showStatus) ? '' : 'none';
            });
        }

        function clearFilters() {
            document.getElementById('departmentFilter').value = 'all';
            document.getElementById('statusFilter').value = 'all';
            const rows = document.querySelectorAll('.payroll-row');
            rows.forEach(row => row.style.display = '');
        }

        // Search Functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.payroll-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // View Payroll Details
        async function viewPayrollDetails(payrollId, employeeStatus) {
            try {
                const response = await fetch('API/payroll_api.php', {
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
                    
                    document.getElementById('detailsTitle').textContent = 'Payroll Details';
                    document.getElementById('payrollActions').classList.remove('hidden');
                    
                    // Check if employee is under review
                    const isUnderReview = (employeeStatus === 'Under Review');
                    
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
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Allowances:</span>
                                        <span class="font-medium text-blue-600">₱${parseFloat(payroll.allowances).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
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
                                            <div class="mt-2">
                                                <span class="text-gray-600">Processed:</span>
                                                <p class="text-sm text-gray-700">${payroll.processed_at || 'Not processed yet'}</p>
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

        function updatePayrollActionButtons(currentStatus, isEmployeeUnderReview) {
            const payrollActionsDiv = document.querySelector('#payrollActions > div');
            
            // Clear existing buttons
            payrollActionsDiv.innerHTML = '';
            
            // If employee is not under review, restrict certain actions
            const canModify = isEmployeeUnderReview || 
                             (currentEmployeeStatus === 'Active' && currentStatus === 'Pending');
            
            // Create buttons based on current status and modification permissions
            const buttons = [];
            
            if (currentStatus === 'Pending') {
                if (canModify) {
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
                } else {
                    buttons.push(`
                        <button class="px-4 py-2.5 bg-gray-400 text-white rounded-lg cursor-not-allowed flex items-center justify-center min-w-[130px]">
                            <i data-lucide="lock" class="w-4 h-4 mr-2"></i>
                            Restricted
                        </button>
                    `);
                }
            } 
            else if (currentStatus === 'Approved') {
                if (canModify) {
                    buttons.push(`
                        <button onclick="markAsPaid()" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center min-w-[130px]">
                            <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                            Mark as Paid
                        </button>
                    `);
                    
                    if (isEmployeeUnderReview) {
                        buttons.push(`
                            <button onclick="holdPayroll()" class="px-4 py-2.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center min-w-[130px]">
                                <i data-lucide="pause" class="w-4 h-4 mr-2"></i>
                                Put on Hold
                            </button>
                        `);
                    }
                }
            }
            else if (currentStatus === 'Hold') {
                if (canModify) {
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
            }
            else if (currentStatus === 'Declined') {
                if (canModify) {
                    buttons.push(`
                        <button onclick="approvePayroll()" class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center min-w-[130px]">
                            <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                            Re-approve
                        </button>
                    `);
                }
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
            
            // Add edit button for all statuses except Paid (only if can modify)
            if (currentStatus !== 'Paid' && canModify) {
                buttons.push(`
                    <button onclick="editPayroll()" class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center min-w-[130px]">
                        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                        Edit
                    </button>
                `);
            }
            
            // Add print button for all statuses
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

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
            document.getElementById('payrollActions').classList.add('hidden');
            document.getElementById('statusWarning').classList.add('hidden');
            currentPayrollId = null;
            currentEmployeeStatus = null;
        }

        // Payroll Actions from Details Modal
        function approvePayroll() {
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
            currentAction = 'Hold';
            document.getElementById('actionTitle').textContent = 'Hold Payroll';
            document.getElementById('actionDescription').textContent = 'Why is this payroll being put on hold?';
            document.getElementById('actionNotes').placeholder = 'Enter hold notes...';
            document.getElementById('actionModal').classList.remove('hidden');
        }

        function declinePayroll() {
            currentAction = 'Declined';
            document.getElementById('actionTitle').textContent = 'Decline Payroll';
            document.getElementById('actionDescription').textContent = 'Why is this payroll being declined?';
            document.getElementById('actionNotes').placeholder = 'Enter decline reason...';
            document.getElementById('actionModal').classList.remove('hidden');
        }

        function markAsPaid() {
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
            currentAction = null;
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
            
            updatePayrollStatus(currentPayrollId, currentAction, notes);
            closeActionModal();
        }

        // Payroll API Functions
        async function updatePayrollStatus(payrollId, status, notes = '') {
            try {
                const response = await fetch('API/payroll_api.php', {
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

        function processAllPayroll() {
            Swal.fire({
                title: 'Process All Pending?',
                text: "This will attempt to process all pending payrolls.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, process all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process all payrolls.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Processed!',
                            text: 'All payrolls have been processed.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }, 1500);
                }
            });
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