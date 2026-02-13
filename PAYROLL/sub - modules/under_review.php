<?php
session_start();
include("../../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die("❌ Connection not found for $db_name");
}
$conn = $connections[$db_name];

// Fetch department names for the dropdown if needed
$dept_query = "SELECT id, name FROM departments";
$dept_result = $conn->query($dept_query);
$departments = [];
if ($dept_result && $dept_result->num_rows > 0) {
    while ($dept = $dept_result->fetch_assoc()) {
        $departments[$dept['id']] = $dept['name'];
    }
}

// Fetch ONLY employees under review (status = 'Under Review') with department name
$review_query = "SELECT e.*, d.name as department_name 
                 FROM employees e
                 LEFT JOIN departments d ON e.department_id = d.id 
                 WHERE e.salary_status = 'Under Review' 
                 ORDER BY e.last_name";
$review_result = $conn->query($review_query);

// Calculate statistics for under review employees only
$stats_query = "SELECT 
                COUNT(*) as total_review,
                SUM(CASE WHEN department_id IS NULL THEN 1 ELSE 0 END) as no_department,
                SUM(basic_salary) as total_salary,
                AVG(basic_salary) as avg_salary
                FROM employees 
                WHERE salary_status = 'Under Review'";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hire Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php 
                                if($review_result->num_rows > 0):
                                    while($row = $review_result->fetch_assoc()): 
                                        // Build full name
                                        $full_name = trim($row['first_name'] . ' ' . ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . $row['last_name']);
                                        $employee_id = $row['employee_code'] ?? 'N/A';
                                        $department_name = $row['department_name'] ?? 'No Department';
                                        $position = $row['job'] ?? 'N/A';
                                        $basic_salary = $row['basic_salary'] ?? 0;
                                        $hire_date = !empty($row['hire_date']) ? date('M d, Y', strtotime($row['hire_date'])) : 'N/A';
                                ?>
                                <tr class="review-row" data-id="<?php echo $row['id']; ?>">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-yellow-600 text-sm font-medium">
                                                    <?php echo strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1)); ?>
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($full_name); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($employee_id); ?></p>
                                                <span class="text-xs text-yellow-600">Under Review</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if($row['department_name']): ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($row['department_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                No Department
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($position); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱<?php echo number_format($basic_salary, 2); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-500"><?php echo $hire_date; ?></td>
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
        
        <!-- Action Buttons Footer -->
        <div class="border-t border-gray-200 pt-4 mt-6">
            <!-- Employee Actions (only shown for under review employees) -->
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
    let currentEmployeeStatus = null;
    let currentActionType = null;

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
                currentEmployeeStatus = employee.salary_status;
                
                document.getElementById('detailsTitle').textContent = 'Employee Details';
                
                // Check if employee is still under review
                const isUnderReview = (employee.salary_status === 'Under Review');
                
                // Show employee actions only if under review
                if (isUnderReview) {
                    document.getElementById('employeeActions').classList.remove('hidden');
                    document.getElementById('statusWarning').classList.add('hidden');
                } else {
                    document.getElementById('employeeActions').classList.add('hidden');
                    document.getElementById('statusWarning').classList.remove('hidden');
                }
                
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
                                    <span class="font-medium">${employee.full_name || ''}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Employee Code:</span>
                                    <span class="font-medium">${employee.employee_code || 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">${employee.email || 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Position:</span>
                                    <span class="font-medium">${employee.job || 'N/A'}</span>
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
                                    <span class="font-medium">${employee.department_name || 'No Department'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="px-2 py-1 text-xs rounded-full ${employee.salary_status === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : (employee.salary_status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')}">
                                        ${employee.salary_status}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Hire Date:</span>
                                    <span class="font-medium">${employee.hire_date ? new Date(employee.hire_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Basic Salary:</span>
                                    <span class="font-medium">₱${parseFloat(employee.basic_salary || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
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
        currentEmployeeStatus = null;
        // Hide all action buttons
        document.getElementById('employeeActions').classList.add('hidden');
        document.getElementById('statusWarning').classList.add('hidden');
    }

    // Employee Actions
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
            const response = await fetch('../API/employee_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_status&employee_id=${currentEmployeeId}&type=${currentActionType}&notes=${encodeURIComponent(notes)}`
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
        let csv = 'Employee Code,Full Name,Department,Position,Basic Salary,Hire Date,Status\n';
        
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 6) {
                    const employeeCode = cells[0].querySelector('.text-gray-500')?.textContent || '';
                    const fullName = cells[0].querySelector('.font-medium')?.textContent || '';
                    const department = cells[1].textContent.trim();
                    const position = cells[2].textContent.trim();
                    const salary = cells[3].textContent.trim();
                    const hireDate = cells[4].textContent.trim();
                    const status = 'Under Review';
                    
                    csv += `"${employeeCode}","${fullName}","${department}","${position}","${salary}","${hireDate}","${status}"\n`;
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