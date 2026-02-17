<?php
session_start();
include("../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die("❌ Connection not found for $db_name");
}
$conn = $connections[$db_name];

// Helper function to safely get string value
function getStringValue($value, $default = '')
{
    if (is_array($value)) {
        if (is_string($value[0] ?? null)) {
            return $value[0];
        }
        return implode(' ', $value);
    }
    return (string) $value ?: (string) $default;
}

// Helper function to build full name
function buildFullName($first_name, $middle_name, $last_name)
{
    $name = trim($first_name ?? '');
    if (!empty($middle_name)) {
        $name .= ' ' . $middle_name;
    }
    if (!empty($last_name)) {
        $name .= ' ' . $last_name;
    }
    return trim($name);
}

// ---------------------------------------------
// FETCH ONLY ACTIVE & REGULAR EMPLOYEES
// ---------------------------------------------
$current_month = date('Y-m');
$local_query = "SELECT 
                e.id,
                e.employee_code,
                e.first_name,
                e.middle_name,
                e.last_name,
                e.email,
                e.phone_number,
                e.job,
                e.salary,
                e.basic_salary,
                e.work_status,
                e.employment_status,
                e.salary_status,
                e.salary_reason,
                e.department_id,
                e.date_of_birth,
                e.hire_date,
                p.id as payroll_id,
                p.basic_salary as payroll_basic,
                p.overtime_hours,
                p.overtime_rate,
                p.overtime_pay,
                p.allowances,
                p.deductions,
                p.net_pay,
                p.period,
                p.status as payroll_status,
                p.notes as payroll_notes
                FROM employees e 
                LEFT JOIN payroll p ON e.id = p.employee_id AND p.period = '$current_month'
                WHERE e.work_status = 'Active' AND e.employment_status = 'Regular'
                ORDER BY e.first_name, e.last_name";

$local_result = $conn->query($local_query);

// Store employee data
$employees_data = [];
$payroll_data = [];
while ($row = $local_result->fetch_assoc()) {
    $employees_data[$row['id']] = $row;
    if ($row['payroll_id']) {
        $payroll_data[$row['id']] = $row;
    }
}

// ---------------------------------------------
// CALCULATE STATISTICS (BASED ON FILTERED EMPLOYEES)
// ---------------------------------------------
$stats = [
    'total_employees' => count($employees_data),
    'under_review' => 0,
    'approved' => 0,
    'denied' => 0,
    'for_compliance' => 0,
    'active' => 0,
    'inactive' => 0
];

foreach ($employees_data as $employee) {
    $status = $employee['salary_status'] ?? 'Under review';
    switch ($status) {
        case 'Under review':
            $stats['under_review']++;
            break;
        case 'For financing':
            $stats['approved']++;
            break;
        case 'Denied financing':
            $stats['denied']++;
            break;
        case 'For compliance':
            $stats['for_compliance']++;
            break;
    }

    if ($employee['work_status'] == 'Active') {
        $stats['active']++;
    } else {
        $stats['inactive']++;
    }
}

// ---------- Pass all employee data to JavaScript ----------
$js_employees = [];
foreach ($employees_data as $id => $emp) {
    $js_employees[$id] = [
        'id'               => $emp['id'],
        'employee_code'    => $emp['employee_code'],
        'first_name'       => $emp['first_name'],
        'middle_name'      => $emp['middle_name'],
        'last_name'        => $emp['last_name'],
        'email'            => $emp['email'],
        'phone_number'     => $emp['phone_number'],
        'job'              => $emp['job'],
        'salary'           => $emp['salary'],
        'basic_salary'     => $emp['basic_salary'] ?? $emp['salary'],
        'work_status'      => $emp['work_status'],
        'employment_status'=> $emp['employment_status'],
        'salary_status'    => $emp['salary_status'] ?? 'Under review',
        'salary_reason'    => $emp['salary_reason'],
        'department_id'    => $emp['department_id'],
        'date_of_birth'    => $emp['date_of_birth'],
        'hire_date'        => $emp['hire_date'],
        'payroll'          => isset($payroll_data[$id]) ? [
            'id'            => $payroll_data[$id]['payroll_id'],
            'status'        => $payroll_data[$id]['payroll_status'],
            'period'        => $payroll_data[$id]['period'],
            'net_pay'       => $payroll_data[$id]['net_pay'],
            'overtime_pay'  => $payroll_data[$id]['overtime_pay'],
            'notes'         => $payroll_data[$id]['payroll_notes']
        ] : null
    ];
}
// --------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Payroll Management</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.20/dist/full.min.css" rel="stylesheet" type="text/css" />
    <!-- html2pdf for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        /* Ensure Swal appears above everything */
        .swal2-container { z-index: 9999999 !important; }
        /* Modal backdrop should not block Swal */
        .modal::backdrop { z-index: 99998; }
        .modal { z-index: 99999; }
        .modal-box { max-height: 90vh; overflow-y: auto; }
        
        .table th { background-color: #f9fafb; font-weight: 600; color: #374151; padding: 1rem 0.75rem; }
        .table td { padding: 1rem 0.75rem; vertical-align: middle; }
        .avatar.placeholder>div { display: flex; align-items: center; justify-content: center; }
        .glass-effect { backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }

        /* Hide financials by default */
        .salary-value, .netpay-value { display: none; }
        .salary-hidden, .netpay-hidden { display: inline; }
        body.show-financials .salary-value,
        body.show-financials .netpay-value { display: inline; }
        body.show-financials .salary-hidden,
        body.show-financials .netpay-hidden { display: none; }

        /* Modal-specific salary toggle */
        .modal.show-financials .salary-value,
        .modal.show-financials .netpay-value { display: inline; }
        .modal.show-financials .salary-hidden,
        .modal.show-financials .netpay-hidden { display: none; }

        /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .pagination button {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 0.375rem;
            cursor: pointer;
        }
        .pagination button.active {
            background: #001f54;
            color: white;
            border-color: #001f54;
        }
        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="bg-base-100 bg-white min-h-screen">
    <?php // Inject the employee data object ?>
    <script>window.employeesData = <?php echo json_encode($js_employees); ?>;</script>

    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../INCLUDES/sidebar.php'; ?>

        <!-- Content Area -->
        <div class="flex flex-col flex-1 overflow-auto">
            <!-- Navbar -->
            <?php include '../INCLUDES/navbar.php'; ?>

            <!-- Main Content -->
            <main class="flex-1 p-4 md:p-6 overflow-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="mb-2 font-bold text-gray-800 text-3xl">Payroll Analytics Dashboard</h1>
                    <p class="text-gray-600">Comprehensive payroll management and employee analytics</p>
                </div>

                <!-- Payroll Analytics Dashboard -->
                <div class="bg-white/70 shadow-sm backdrop-blur-sm mb-8 p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center gap-4 mb-6">
                        <h2 class="flex items-center font-bold text-gray-800 text-2xl">
                            <span class="bg-indigo-100/50 mr-3 p-2 rounded-lg text-indigo-600">
                                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            </span>
                            Payroll Analytics Dashboard
                        </h2>
                        <div class="flex gap-2">
                            <select class="bg-white px-4 py-2 border border-gray-300 rounded-lg text-gray-700">
                                <option>Last 30 Days</option>
                                <option>Last Quarter</option>
                                <option>Year to Date</option>
                                <option>Last Year</option>
                            </select>
                        </div>
                    </div>

                    <!-- Key Payroll Metrics - 3 columns grid -->
                    <div class="gap-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 mb-8">
                        <!-- Total Employees -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Total Employees</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['total_employees']; ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">All employees</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="users" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                        <!-- Under Review -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Under Review</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['under_review']; ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">Awaiting approval</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="clock" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                        <!-- Under review for financing (previously For Financing) -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Under review for financing</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['approved']; ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">Ready for financing</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="check-circle" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                        <!-- For compliance financing (previously For Compliance) -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">For compliance financing</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['for_compliance']; ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">Requires compliance review</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="shield" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                        <!-- Active Employees -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Active Employees</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['active']; ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">Currently working</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="user-check" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                        <!-- Denied review (previously Denied Financing) -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Denied review</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['denied']; ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">Financing requests rejected</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="x-circle" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Controls & Employee List -->
                <div class="bg-white shadow-lg mb-6 card">
                    <div class="card-body">
                        <div class="flex md:flex-row flex-col justify-between items-start md:items-center gap-4">
                            <div>
                                <h2 class="font-semibold text-xl">Employee List</h2>
                                <p class="text-gray-500">Manage salary status and payroll</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <!-- Search by employee code -->
                                <input type="text" id="searchEmployeeCode" placeholder="Search by Employee Code" class="input input-bordered input-sm w-48">
                                
                                <!-- Toggle Salary/Net Pay visibility -->
                                <button id="toggleFinancialsBtn" onclick="toggleFinancials()" class="btn-outline btn">
                                    <i data-lucide="eye-off" id="toggleIcon" class="mr-2 w-4 h-4"></i>
                                    <span id="toggleText">Show Salary/Net Pay</span>
                                </button>
                                
                                <!-- Export Selected PDF button -->
                                <button onclick="exportSelectedPDF()" class="btn-outline btn">
                                    <i data-lucide="file-text" class="mr-2 w-4 h-4"></i> Export Selected PDF
                                </button>
                                
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button" class="btn-outline btn">
                                        <i data-lucide="filter" class="mr-2 w-4 h-4"></i> Filter
                                    </div>
                                    <ul tabindex="0" class="z-[1] bg-base-100 shadow p-2 rounded-box w-52 dropdown-content menu">
                                        <li><a onclick="filterByStatus('all')">All Employees</a></li>
                                        <li><a onclick="filterByStatus('Under review')">Under review</a></li>
                                        <li><a onclick="filterByStatus('For financing')">Under review for financing</a></li>
                                        <li><a onclick="filterByStatus('Denied financing')">Denied review</a></li>
                                        <li><a onclick="filterByStatus('For compliance')">For compliance financing</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Table - RENDERED BY JAVASCRIPT -->
                <div class="bg-white shadow-lg card">
                    <div class="p-0 card-body">
                        <div class="overflow-x-auto">
                            <table class="table w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 w-10">
                                            <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" class="checkbox checkbox-sm">
                                        </th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Employee</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Employee Code</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Position</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Salary</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Employment Status</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Salary Status</th>
                                        <!-- Payroll Status column removed -->
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employeeTableBody" class="divide-y divide-gray-200">
                                    <!-- Filled by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination controls -->
                        <div id="pagination" class="pagination"></div>
                    </div>
                </div>

                <!-- View Employee Modal (redesigned organized list) -->
                <dialog id="viewModal" class="modal">
                    <div class="bg-white max-w-4xl text-black modal-box">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-lg">Employee Details</h3>
                            <button onclick="toggleModalFinancials()" class="btn-outline btn btn-sm">
                                <i data-lucide="eye" id="modalToggleIcon" class="mr-2 w-4 h-4"></i>
                                <span id="modalToggleText">Hide Salary</span>
                            </button>
                        </div>
                        <div id="employeeDetails" class="space-y-6">
                            <!-- Content loaded via JavaScript -->
                        </div>
                        <div class="mt-8 pt-6 border-t modal-action">
                            <!-- CRUD Actions in Modal Footer -->
                            <div class="flex flex-col gap-4 w-full">
                                <!-- Action Buttons -->
                                <div class="flex flex-wrap justify-between gap-2" id="salaryActionButtons">
                                    <div class="flex flex-wrap gap-2">
                                        <button id="approveSalaryBtn" class="btn btn-success btn-sm" onclick="showSalaryActionForm('approve')">
                                            <i data-lucide="check-circle" class="mr-2 w-4 h-4"></i> Approve for Financing
                                        </button>
                                        <button id="denySalaryBtn" class="btn btn-error btn-sm" onclick="showSalaryActionForm('deny')">
                                            <i data-lucide="x-circle" class="mr-2 w-4 h-4"></i> Deny Financing
                                        </button>
                                        <button id="forComplianceBtn" class="btn btn-warning btn-sm" onclick="showSalaryActionForm('compliance')">
                                            <i data-lucide="shield" class="mr-2 w-4 h-4"></i> For Compliance
                                        </button>
                                        <button onclick="viewPayrollHistory(currentEmployeeId, currentEmployeeData?.first_name + ' ' + currentEmployeeData?.last_name)" class="btn btn-info btn-sm">
                                            <i data-lucide="history" class="mr-2 w-4 h-4"></i> Payroll History
                                        </button>
                                        <button onclick="exportSingleEmployeePDF(currentEmployeeId)" class="btn btn-secondary btn-sm">
                                            <i data-lucide="file-text" class="mr-2 w-4 h-4"></i> Export as PDF
                                        </button>
                                    </div>
                                    <button class="btn btn-ghost btn-sm" onclick="viewModal.close()">Close</button>
                                </div>

                                <!-- Action Form (hidden by default) -->
                                <div id="salaryActionForm" class="hidden w-full">
                                    <div class="bg-white shadow card">
                                        <div class="p-4 card-body">
                                            <h4 class="card-title" id="salaryActionTitle">Action</h4>
                                            <input type="hidden" id="currentEmployeeId">
                                            <input type="hidden" id="currentActionType">

                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text" id="salaryActionLabel">Reason/Comments</span>
                                                    <span class="label-text-alt text-red-500">*Required</span>
                                                </label>
                                                <textarea id="salaryActionComment" class="h-24 textarea textarea-bordered"
                                                    placeholder="Enter reason for this action..." required></textarea>
                                                <div class="label">
                                                    <span class="label-text-alt text-gray-500" id="salaryActionHint">
                                                        Please provide detailed comments for this action
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex justify-end gap-2 mt-4">
                                                <button type="button" class="btn btn-ghost" onclick="hideSalaryActionForm()">Cancel</button>
                                                <button type="button" class="btn btn-primary" onclick="submitSalaryAction()">Submit Action</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </dialog>

                <!-- Payroll History Modal -->
                <dialog id="payrollHistoryModal" class="modal">
                    <div class="bg-white max-w-6xl text-black modal-box">
                        <h3 class="mb-4 font-bold text-lg" id="payrollHistoryTitle">Payroll History</h3>
                        <div class="overflow-x-auto">
                            <table class="table w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Period</th>
                                        <th class="px-4 py-2 text-left">Basic Salary</th>
                                        <th class="px-4 py-2 text-left">Overtime</th>
                                        <th class="px-4 py-2 text-left">Allowances</th>
                                        <th class="px-4 py-2 text-left">Deductions</th>
                                        <th class="px-4 py-2 text-left">Net Pay</th>
                                        <th class="px-4 py-2 text-left">Status</th>
                                        <th class="px-4 py-2 text-left">Processed On</th>
                                        <th class="px-4 py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="payrollHistoryBody"></tbody>
                            </table>
                        </div>
                        <div class="modal-action">
                            <button class="btn btn-ghost" onclick="payrollHistoryModal.close()">Close</button>
                        </div>
                    </div>
                </dialog>

                <!-- Create/Edit Payroll Modal -->
                <dialog id="payrollModal" class="modal">
                    <div class="bg-white max-w-2xl text-black modal-box">
                        <h3 class="mb-4 font-bold text-lg" id="payrollModalTitle">Create Payroll</h3>
                        <form id="payrollForm">
                            <input type="hidden" id="payrollEmployeeId">
                            <input type="hidden" id="payrollId">

                            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Basic Salary</span></label>
                                    <input type="number" id="basicSalary" class="input input-bordered" required step="0.01">
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Overtime Hours</span></label>
                                    <input type="number" id="overtimeHours" class="input input-bordered" step="0.5" value="0">
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Overtime Rate/Hour</span></label>
                                    <input type="number" id="overtimeRate" class="input input-bordered" step="0.01" value="100">
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Allowances</span></label>
                                    <input type="number" id="allowances" class="input input-bordered" step="0.01" value="0">
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Deductions</span></label>
                                    <input type="number" id="deductions" class="input input-bordered" step="0.01" value="0">
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Period</span></label>
                                    <input type="month" id="period" class="input input-bordered" value="<?php echo date('Y-m'); ?>">
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Status</span></label>
                                    <select id="payrollStatus" class="select-bordered select">
                                        <option value="Draft">Draft</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Paid">Paid</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2 form-control">
                                    <label class="label"><span class="label-text">Notes (Optional)</span></label>
                                    <textarea id="payrollNotes" class="textarea textarea-bordered" placeholder="Any notes about this payroll..."></textarea>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="bg-white mt-6 card">
                                <div class="card-body">
                                    <h4 class="card-title">Payroll Summary</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between"><span>Basic Salary:</span><span id="summaryBasic" class="font-semibold">₱0.00</span></div>
                                        <div class="flex justify-between"><span>Overtime Pay:</span><span id="summaryOvertime" class="font-semibold">₱0.00</span></div>
                                        <div class="flex justify-between"><span>Allowances:</span><span id="summaryAllowances" class="font-semibold">₱0.00</span></div>
                                        <div class="flex justify-between"><span>Deductions:</span><span id="summaryDeductions" class="font-semibold">₱0.00</span></div>
                                        <div class="divider"></div>
                                        <div class="flex justify-between text-lg"><span>Net Pay:</span><span id="summaryNetPay" class="font-bold text-primary">₱0.00</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-action">
                                <button type="button" onclick="closePayrollModal()" class="btn btn-ghost">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="payrollSubmitBtn">Create Payroll</button>
                            </div>
                        </form>
                    </div>
                </dialog>

            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Dialog element references
        const viewModal = document.getElementById('viewModal');
        const payrollModal = document.getElementById('payrollModal');
        const payrollHistoryModal = document.getElementById('payrollHistoryModal');

        let currentAction = null;
        let currentEmployeeId = null;
        let currentEmployeeName = null;
        let currentEmployeeData = null;

        // Data and pagination
        let allEmployees = Object.values(window.employeesData);
        let filteredEmployees = allEmployees;
        let currentPage = 1;
        const rowsPerPage = 10;
        let currentFilter = 'all'; // track current filter

        // DOM elements
        const tbody = document.getElementById('employeeTableBody');
        const paginationDiv = document.getElementById('pagination');
        const searchInput = document.getElementById('searchEmployeeCode');

        // Helper function to format salary status for display
        function formatSalaryStatus(status) {
            const map = {
                'Under review': 'Under review',
                'For financing': 'Under review for financing',
                'Denied financing': 'Denied review',
                'For compliance': 'For compliance financing'
            };
            return map[status] || status;
        }

        // ========== RENDER TABLE ==========
        function renderTable() {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedEmployees = filteredEmployees.slice(start, end);

            tbody.innerHTML = '';
            paginatedEmployees.forEach(emp => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 transition-colors';
                row.dataset.employeeId = emp.id;
                row.dataset.salaryStatus = emp.salary_status;
                row.dataset.workStatus = emp.work_status;

                const fullName = emp.first_name + (emp.middle_name ? ' ' + emp.middle_name : '') + (emp.last_name ? ' ' + emp.last_name : '');
                
                // Employment status badge styling
                const employmentStatusClass = {
                    'Regular': 'bg-green-100 text-green-800',
                    'Probationary': 'bg-yellow-100 text-yellow-800',
                    'Trainee': 'bg-blue-100 text-blue-800',
                    'Contractual': 'bg-purple-100 text-purple-800'
                }[emp.employment_status] || 'bg-gray-100 text-gray-800';

                const salaryStatusClass = {
                    'Under review': 'bg-yellow-100 text-yellow-800',
                    'For financing': 'bg-green-100 text-green-800',
                    'Denied financing': 'bg-red-100 text-red-800',
                    'For compliance': 'bg-purple-100 text-purple-800'
                }[emp.salary_status] || 'bg-gray-100 text-gray-800';

                const payroll = emp.payroll;

                row.innerHTML = `
                    <td class="px-4 py-3">
                        <input type="checkbox" class="row-checkbox checkbox checkbox-sm" value="${emp.id}">
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10">
                                <div class="flex justify-center items-center bg-blue-100 rounded-full w-10 h-10 font-semibold text-blue-600">
                                    ${emp.first_name.charAt(0)}${emp.last_name ? emp.last_name.charAt(0) : ''}
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900 text-sm">${fullName}</div>
                                <div class="text-gray-500 text-sm">${emp.email || ''}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center bg-gray-100 px-2.5 py-0.5 rounded-full font-medium text-gray-800 text-xs">
                            ${emp.employee_code}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-900 text-sm whitespace-nowrap">${emp.job || ''}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900 text-sm whitespace-nowrap">
                        <span class="salary-hidden">********</span>
                        <span class="salary-value">₱${parseFloat(emp.salary).toLocaleString('en-US', {minimumFractionDigits:2})}</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${employmentStatusClass}">
                            ${emp.employment_status}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${salaryStatusClass}">
                            ${formatSalaryStatus(emp.salary_status)}
                        </span>
                    </td>
                    <!-- Payroll Status column removed -->
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <button onclick="viewEmployee(${emp.id})"
                                class="inline-flex items-center bg-blue-600 hover:bg-blue-700 px-3 py-1.5 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium text-white text-xs">
                                <i data-lucide="eye" class="mr-1 w-3 h-3"></i> View
                            </button>
                            <div class="relative">
                                <button type="button" class="inline-flex items-center bg-white hover:bg-gray-50 px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium text-gray-700 text-xs dropdown-toggle">
                                    <i data-lucide="more-vertical" class="w-3 h-3"></i>
                                </button>
                                <div class="hidden right-0 absolute bg-white ring-opacity-5 shadow-lg mt-2 rounded-md ring-1 ring-black w-48 dropdown-menu">
                                    <div class="py-1" role="menu">
                                        <a href="#" onclick="approveSalary(${emp.id}, '${fullName.replace(/'/g, "\\'")}')" class="block hover:bg-gray-100 px-4 py-2 text-gray-700 text-sm">Approve for Financing</a>
                                        <a href="#" onclick="denySalary(${emp.id}, '${fullName.replace(/'/g, "\\'")}')" class="block hover:bg-gray-100 px-4 py-2 text-gray-700 text-sm">Deny Financing</a>
                                        <a href="#" onclick="forCompliance(${emp.id}, '${fullName.replace(/'/g, "\\'")}')" class="block hover:bg-gray-100 px-4 py-2 text-gray-700 text-sm">For Compliance</a>
                                        <div class="border-gray-100 border-t"></div>
                                        ${!payroll ? 
                                            `<a href="#" onclick="createPayroll(${emp.id}, '${fullName.replace(/'/g, "\\'")}', ${emp.salary})" class="block hover:bg-gray-100 px-4 py-2 text-gray-700 text-sm">Create Payroll</a>` : 
                                            `<a href="#" onclick="editPayroll(${emp.id}, '${fullName.replace(/'/g, "\\'")}')" class="block hover:bg-gray-100 px-4 py-2 text-gray-700 text-sm">Edit Payroll</a>`
                                        }
                                        <a href="#" onclick="viewPayrollHistory(${emp.id}, '${fullName.replace(/'/g, "\\'")}')" class="block hover:bg-gray-100 px-4 py-2 text-gray-700 text-sm">Payroll History</a>
                                        <div class="border-gray-100 border-t"></div>
                                        ${emp.work_status == 'Active' ? 
                                            `<a href="#" onclick="markInactive(${emp.id})" class="block hover:bg-gray-100 px-4 py-2 text-gray-700 text-sm">Mark Inactive</a>` : 
                                            `<a href="#" onclick="markActive(${emp.id})" class="block hover:bg-gray-100 px-4 py-2 text-gray-700 text-sm">Mark Active</a>`
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
            lucide.createIcons();
            updatePagination();
            updateSelectAllState();
        }

        // ========== PAGINATION ==========
        function updatePagination() {
            const totalPages = Math.ceil(filteredEmployees.length / rowsPerPage);
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) return;

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => { currentPage--; renderTable(); };
            paginationDiv.appendChild(prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                btn.className = i === currentPage ? 'active' : '';
                btn.onclick = () => { currentPage = i; renderTable(); };
                paginationDiv.appendChild(btn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => { currentPage++; renderTable(); };
            paginationDiv.appendChild(nextBtn);
        }

        // ========== FILTER & SEARCH ==========
        function filterByStatus(status) {
            currentFilter = status;
            applyFilterAndSearch();
        }

        function applyFilterAndSearch() {
            // Start with all employees
            let filtered = allEmployees;

            // Apply filter (using raw status values for comparison)
            if (currentFilter === 'Active') {
                filtered = filtered.filter(e => e.work_status === 'Active');
            } else if (currentFilter !== 'all') {
                filtered = filtered.filter(e => e.salary_status === currentFilter);
            }

            // Apply search
            const term = searchInput.value.trim().toLowerCase();
            if (term) {
                filtered = filtered.filter(e => e.employee_code.toLowerCase().includes(term));
            }

            filteredEmployees = filtered;
            currentPage = 1;
            renderTable();
        }

        searchInput.addEventListener('input', applyFilterAndSearch);

        // ========== CHECKBOX SELECTION ==========
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }

        function updateSelectAllState() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            if (checkboxes.length === 0) {
                selectAll.checked = false;
                return;
            }
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            selectAll.checked = allChecked;
            selectAll.indeterminate = !allChecked && Array.from(checkboxes).some(cb => cb.checked);
        }

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                updateSelectAllState();
            }
        });

        // ========== EXPORT PDF (REVISED WITH CLEAN HTML TEMPLATE) ==========
        function exportSelectedPDF() {
            const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                Swal.fire({
                    title: 'No selection',
                    text: 'Please select at least one employee to export.',
                    icon: 'warning',
                    target: document.body
                });
                return;
            }
            const employeesToExport = allEmployees.filter(e => selectedIds.includes(e.id.toString()));

            // Clean HTML template for PDF (no oklch, no complex CSS)
            let html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Employee Payroll Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #001f54; text-align: center; }
                        p { text-align: center; margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; border: 1px solid #333; }
                        th { background: #001f54; color: white; padding: 8px; border: 1px solid #001f54; }
                        td { padding: 8px; border: 1px solid #666; }
                        tr:nth-child(even) { background: #f2f2f2; }
                    </style>
                </head>
                <body>
                    <h1>Employee Payroll Report</h1>
                    <p>Generated on: ${new Date().toLocaleDateString()}</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Employment Status</th>
                                <th>Salary Status</th>
                                <th>Net Pay</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            employeesToExport.forEach(emp => {
                const fullName = emp.first_name + (emp.middle_name ? ' ' + emp.middle_name : '') + (emp.last_name ? ' ' + emp.last_name : '');
                html += `
                    <tr>
                        <td>${emp.employee_code}</td>
                        <td>${fullName}</td>
                        <td>${emp.job || ''}</td>
                        <td>${emp.employment_status}</td>
                        <td>${formatSalaryStatus(emp.salary_status)}</td>
                        <td>₱${emp.payroll ? parseFloat(emp.payroll.net_pay).toFixed(2) : '0.00'}</td>
                    </tr>
                `;
            });
            html += `</tbody></table></body></html>`;

            const element = document.createElement('div');
            element.innerHTML = html;
            document.body.appendChild(element);
            
            // Show loading
            Swal.fire({
                title: 'Generating PDF...',
                allowOutsideClick: false,
                target: document.body,
                didOpen: () => Swal.showLoading()
            });

            html2pdf().from(element.querySelector('body')).set({
                margin: 0.5,
                filename: `employees_export_${Date.now()}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, logging: true },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
            }).save().then(() => {
                Swal.close();
                document.body.removeChild(element);
            }).catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'PDF generation failed: ' + error,
                    icon: 'error',
                    target: document.body
                });
                document.body.removeChild(element);
            });
        }

        function exportSingleEmployeePDF(employeeId) {
            const emp = allEmployees.find(e => e.id == employeeId);
            if (!emp) return;

            const fullName = emp.first_name + (emp.middle_name ? ' ' + emp.middle_name : '') + (emp.last_name ? ' ' + emp.last_name : '');
            let html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Employee Details</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #001f54; text-align: center; }
                        p { text-align: center; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        td { padding: 8px; border-bottom: 1px solid #666; }
                        td:first-child { font-weight: bold; width: 30%; }
                    </style>
                </head>
                <body>
                    <h1>Employee Details</h1>
                    <p>Generated on: ${new Date().toLocaleDateString()}</p>
                    <table>
                        <tr><td>Employee Code:</td><td>${emp.employee_code}</td></tr>
                        <tr><td>Name:</td><td>${fullName}</td></tr>
                        <tr><td>Email:</td><td>${emp.email || ''}</td></tr>
                        <tr><td>Phone:</td><td>${emp.phone_number || ''}</td></tr>
                        <tr><td>Position:</td><td>${emp.job || ''}</td></tr>
                        <tr><td>Employment Status:</td><td>${emp.employment_status}</td></tr>
                        <tr><td>Salary Status:</td><td>${formatSalaryStatus(emp.salary_status)}</td></tr>
                        <tr><td>Salary:</td><td>₱${parseFloat(emp.salary).toFixed(2)}</td></tr>
                        ${emp.payroll ? `
                        <tr><td>Payroll Status:</td><td>${emp.payroll.status}</td></tr>
                        <tr><td>Net Pay:</td><td>₱${parseFloat(emp.payroll.net_pay).toFixed(2)}</td></tr>
                        ` : ''}
                    </table>
                </body>
                </html>
            `;
            const element = document.createElement('div');
            element.innerHTML = html;
            document.body.appendChild(element);
            
            Swal.fire({
                title: 'Generating PDF...',
                allowOutsideClick: false,
                target: document.body,
                didOpen: () => Swal.showLoading()
            });

            html2pdf().from(element.querySelector('body')).set({
                margin: 0.5,
                filename: `employee_${emp.employee_code}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            }).save().then(() => {
                Swal.close();
                document.body.removeChild(element);
            }).catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'PDF generation failed: ' + error,
                    icon: 'error',
                    target: document.body
                });
                document.body.removeChild(element);
            });
        }

        // ========== INITIAL RENDER ==========
        document.addEventListener('DOMContentLoaded', function() {
            renderTable();
            // Initialize dropdowns
            document.querySelectorAll('.dropdown-toggle').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const menu = this.nextElementSibling;
                    menu.classList.toggle('hidden');
                });
            });
            document.addEventListener('click', function() {
                document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
            });
        });

        // ========== TOGGLE FINANCIALS (global) ==========
        let showFinancials = false;
        function toggleFinancials() {
            showFinancials = !showFinancials;
            const body = document.body;
            const icon = document.getElementById('toggleIcon');
            const text = document.getElementById('toggleText');

            if (showFinancials) {
                body.classList.add('show-financials');
                icon.setAttribute('data-lucide', 'eye');
                text.innerText = 'Hide Salary/Net Pay';
            } else {
                body.classList.remove('show-financials');
                icon.setAttribute('data-lucide', 'eye-off');
                text.innerText = 'Show Salary/Net Pay';
            }
            lucide.createIcons();
        }

        // ========== TOGGLE FINANCIALS (modal only) ==========
        let modalShowFinancials = false;
        function toggleModalFinancials() {
            modalShowFinancials = !modalShowFinancials;
            const modal = document.getElementById('viewModal');
            const icon = document.getElementById('modalToggleIcon');
            const text = document.getElementById('modalToggleText');

            if (modalShowFinancials) {
                modal.classList.add('show-financials');
                icon.setAttribute('data-lucide', 'eye');
                text.innerText = 'Hide Salary';
            } else {
                modal.classList.remove('show-financials');
                icon.setAttribute('data-lucide', 'eye-off');
                text.innerText = 'Show Salary';
            }
            lucide.createIcons();
        }

        // ========== VIEW EMPLOYEE DETAILS (organized list) ==========
        function viewEmployee(employeeId) {
            const employee = window.employeesData[employeeId];
            if (!employee) {
                Swal.fire({
                    title: 'Error',
                    text: 'Employee not found',
                    icon: 'error',
                    target: document.body
                });
                return;
            }
            currentEmployeeData = employee;
            currentEmployeeId = employeeId;
            displayEmployeeDetails(employee);
            updateActionButtons(employee.salary_status);
            viewModal.showModal();
        }

        function updateActionButtons(currentStatus) {
            const approveBtn = document.getElementById('approveSalaryBtn');
            const denyBtn = document.getElementById('denySalaryBtn');
            const complianceBtn = document.getElementById('forComplianceBtn');

            if (currentStatus === 'For financing') {
                approveBtn.disabled = true;
                approveBtn.classList.add('btn-disabled');
                approveBtn.innerHTML = '<i data-lucide="check-circle" class="mr-2 w-4 h-4"></i>Already Approved';
            } else {
                approveBtn.disabled = false;
                approveBtn.classList.remove('btn-disabled');
                approveBtn.innerHTML = '<i data-lucide="check-circle" class="mr-2 w-4 h-4"></i>Approve for Financing';
            }

            if (currentStatus === 'Denied financing') {
                denyBtn.disabled = true;
                denyBtn.classList.add('btn-disabled');
                denyBtn.innerHTML = '<i data-lucide="x-circle" class="mr-2 w-4 h-4"></i>Already Denied';
            } else {
                denyBtn.disabled = false;
                denyBtn.classList.remove('btn-disabled');
                denyBtn.innerHTML = '<i data-lucide="x-circle" class="mr-2 w-4 h-4"></i>Deny Financing';
            }

            if (currentStatus === 'For compliance') {
                complianceBtn.disabled = true;
                complianceBtn.classList.add('btn-disabled');
                complianceBtn.innerHTML = '<i data-lucide="shield" class="mr-2 w-4 h-4"></i>Already in Compliance';
            } else {
                complianceBtn.disabled = false;
                complianceBtn.classList.remove('btn-disabled');
                complianceBtn.innerHTML = '<i data-lucide="shield" class="mr-2 w-4 h-4"></i>For Compliance';
            }
            lucide.createIcons();
        }

        // Generate random attendance data
        function generateDummyAttendance(employee) {
            const daysInMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getDate();
            const workDays = Math.floor(daysInMonth * 0.8); // assume 80% working days
            const present = Math.floor(Math.random() * (workDays - 5)) + 5; // between 5 and workDays
            const late = Math.floor(Math.random() * 5);
            const absent = workDays - present;
            const overtimeHours = Math.floor(Math.random() * 20);
            return { present, late, absent, overtimeHours };
        }

        function displayEmployeeDetails(data) {
            const full_name = data.first_name + (data.middle_name ? ' ' + data.middle_name : '') + (data.last_name ? ' ' + data.last_name : '');
            const attendance = generateDummyAttendance(data);

            const details = `
            <div class="space-y-4">
                <!-- Personal Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-lg mb-3 flex items-center">
                        <i data-lucide="user" class="mr-2 w-5 h-5 text-blue-600"></i> Personal Information
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div><span class="font-medium text-gray-600">Full Name:</span> <span class="ml-2">${full_name}</span></div>
                        <div><span class="font-medium text-gray-600">Employee Code:</span> <span class="ml-2">${data.employee_code}</span></div>
                        <div><span class="font-medium text-gray-600">Email:</span> <span class="ml-2">${data.email || 'N/A'}</span></div>
                        <div><span class="font-medium text-gray-600">Phone:</span> <span class="ml-2">${data.phone_number || 'N/A'}</span></div>
                        <div><span class="font-medium text-gray-600">Date of Birth:</span> <span class="ml-2">${data.date_of_birth || 'N/A'}</span></div>
                    </div>
                </div>

                <!-- Job Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-lg mb-3 flex items-center">
                        <i data-lucide="briefcase" class="mr-2 w-5 h-5 text-blue-600"></i> Job Information
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div><span class="font-medium text-gray-600">Job/Position:</span> <span class="ml-2">${data.job}</span></div>
                        <div><span class="font-medium text-gray-600">Department ID:</span> <span class="ml-2">${data.department_id || 'N/A'}</span></div>
                        <div><span class="font-medium text-gray-600">Work Status:</span> <span class="ml-2 badge ${data.work_status === 'Active' ? 'badge-success' : (data.work_status === 'Inactive' ? 'badge-error' : 'badge-warning')}">${data.work_status}</span></div>
                        <div><span class="font-medium text-gray-600">Employment Status:</span> <span class="ml-2">${data.employment_status}</span></div>
                        <div><span class="font-medium text-gray-600">Hire Date:</span> <span class="ml-2">${data.hire_date || 'N/A'}</span></div>
                    </div>
                </div>

                <!-- Salary Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-lg mb-3 flex items-center">
                        <i data-lucide="dollar-sign" class="mr-2 w-5 h-5 text-blue-600"></i> Salary Information
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div><span class="font-medium text-gray-600">Salary:</span> 
                            <span class="ml-2">
                                <span class="salary-hidden">********</span>
                                <span class="salary-value">₱${parseFloat(data.salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                            </span>
                        </div>
                        <div><span class="font-medium text-gray-600">Basic Salary:</span> 
                            <span class="ml-2">
                                <span class="salary-hidden">********</span>
                                <span class="salary-value">₱${parseFloat(data.basic_salary || data.salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                            </span>
                        </div>
                        <div><span class="font-medium text-gray-600">Salary Status:</span> 
                            <span class="ml-2 badge ${getSalaryStatusClass(data.salary_status)}">
                                ${formatSalaryStatus(data.salary_status)}
                            </span>
                        </div>
                        ${data.salary_reason ? `
                        <div class="col-span-2"><span class="font-medium text-gray-600">Reason:</span> 
                            <p class="bg-white mt-1 p-2 rounded border">${data.salary_reason}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>

                <!-- Payroll Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-lg mb-3 flex items-center">
                        <i data-lucide="file-text" class="mr-2 w-5 h-5 text-blue-600"></i> Payroll Information
                    </h4>
                    ${data.payroll ? `
                        <div class="grid grid-cols-2 gap-3">
                            <div><span class="font-medium text-gray-600">Payroll Status:</span> 
                                <span class="ml-2 badge ${data.payroll.status === 'Approved' ? 'badge-success' : (data.payroll.status === 'Paid' ? 'badge-primary' : 'badge-warning')}">
                                    ${data.payroll.status}
                                </span>
                            </div>
                            <div><span class="font-medium text-gray-600">Period:</span> <span class="ml-2">${data.payroll.period}</span></div>
                            <div><span class="font-medium text-gray-600">Net Pay:</span> 
                                <span class="ml-2">
                                    <span class="netpay-hidden">********</span>
                                    <span class="netpay-value">₱${parseFloat(data.payroll.net_pay).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                </span>
                            </div>
                            <div><span class="font-medium text-gray-600">Overtime Pay:</span> <span class="ml-2">₱${parseFloat(data.payroll.overtime_pay).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>
                            ${data.payroll.notes ? `
                            <div class="col-span-2"><span class="font-medium text-gray-600">Notes:</span> 
                                <p class="bg-white mt-1 p-2 rounded border">${data.payroll.notes}</p>
                            </div>
                            ` : ''}
                        </div>
                    ` : '<p class="text-gray-500 text-center">No payroll record found</p>'}
                </div>

                <!-- Dummy Attendance Data -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-lg mb-3 flex items-center">
                        <i data-lucide="calendar" class="mr-2 w-5 h-5 text-blue-600"></i> Attendance Summary (Current Month - Dummy Data)
                    </h4>
                    <div class="grid grid-cols-4 gap-4 text-center">
                        <div class="bg-green-50 p-3 rounded-lg">
                            <span class="block text-2xl font-bold text-green-600">${attendance.present}</span>
                            <span class="text-sm text-gray-600">Days Present</span>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <span class="block text-2xl font-bold text-yellow-600">${attendance.late}</span>
                            <span class="text-sm text-gray-600">Late</span>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg">
                            <span class="block text-2xl font-bold text-red-600">${attendance.absent}</span>
                            <span class="text-sm text-gray-600">Absent</span>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <span class="block text-2xl font-bold text-blue-600">${attendance.overtimeHours}</span>
                            <span class="text-sm text-gray-600">Overtime Hours</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">*Randomly generated for demonstration</p>
                </div>
            </div>
        `;

            document.getElementById('employeeDetails').innerHTML = details;
            lucide.createIcons();
        }

        function getSalaryStatusClass(status) {
            const classes = {
                'Under review': 'badge-warning',
                'For financing': 'badge-success',
                'Denied financing': 'badge-error',
                'For compliance': 'badge-info'
            };
            return classes[status] || 'badge-outline';
        }

        // ---------- Salary action forms ----------
        function showSalaryActionForm(actionType) {
            currentAction = actionType;
            document.getElementById('currentEmployeeId').value = currentEmployeeId;
            document.getElementById('currentActionType').value = actionType;

            const actionTitle = document.getElementById('salaryActionTitle');
            const actionLabel = document.getElementById('salaryActionLabel');
            const actionHint = document.getElementById('salaryActionHint');
            const actionComment = document.getElementById('salaryActionComment');

            switch (actionType) {
                case 'approve':
                    actionTitle.textContent = 'Approve for Financing';
                    actionLabel.textContent = 'Approval Comments (Optional)';
                    actionHint.textContent = 'Optional comments explaining why this salary is approved for financing';
                    actionComment.placeholder = 'Enter approval comments (optional)...';
                    break;
                case 'deny':
                    actionTitle.textContent = 'Deny Financing';
                    actionLabel.textContent = 'Denial Reason (Required)';
                    actionHint.textContent = 'Required: Please provide detailed reason for denying this financing request';
                    actionComment.placeholder = 'Enter detailed reason for denial...';
                    break;
                case 'compliance':
                    actionTitle.textContent = 'Send for Compliance Review';
                    actionLabel.textContent = 'Compliance Notes (Required)';
                    actionHint.textContent = 'Required: Please specify what compliance issues need to be addressed';
                    actionComment.placeholder = 'Enter compliance review notes...';
                    break;
            }

            document.getElementById('salaryActionButtons').classList.add('hidden');
            document.getElementById('salaryActionForm').classList.remove('hidden');
            setTimeout(() => actionComment.focus(), 100);
        }

        function hideSalaryActionForm() {
            document.getElementById('salaryActionForm').classList.add('hidden');
            document.getElementById('salaryActionButtons').classList.remove('hidden');
            document.getElementById('salaryActionComment').value = '';
            currentAction = null;
        }

        async function submitSalaryAction() {
            const employeeId = document.getElementById('currentEmployeeId').value;
            const actionType = document.getElementById('currentActionType').value;
            const comment = document.getElementById('salaryActionComment').value.trim();

            if ((actionType === 'deny' || actionType === 'compliance') && !comment) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Comments Required',
                    text: 'Please provide detailed comments for this action',
                    target: document.body
                });
                return;
            }

            let status = '';
            switch (actionType) {
                case 'approve': status = 'For financing'; break;
                case 'deny':    status = 'Denied financing'; break;
                case 'compliance': status = 'For compliance'; break;
            }

            try {
                const response = await fetch('API/payroll_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=update_salary_status&id=${employeeId}&status=${status}&reason=${encodeURIComponent(comment)}`
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        target: document.body
                    }).then(() => { hideSalaryActionForm(); viewModal.close(); location.reload(); });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        target: document.body
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error!',
                    text: 'Failed to update salary status',
                    target: document.body
                });
            }
        }

        // ---------- Payroll History (unchanged) ----------
        async function viewPayrollHistory(employeeId, employeeName) {
            try {
                const response = await fetch('API/payroll_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=get_payroll_history&employee_id=${employeeId}`
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('payrollHistoryTitle').textContent = `Payroll History - ${employeeName}`;
                    displayPayrollHistory(data.history);
                    payrollHistoryModal.showModal();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to load payroll history',
                        target: document.body
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error!',
                    text: 'Failed to load payroll history',
                    target: document.body
                });
            }
        }

        function displayPayrollHistory(history) {
            const tbody = document.getElementById('payrollHistoryBody');
            tbody.innerHTML = '';
            if (history.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="px-4 py-8 text-gray-500 text-center">No payroll history found</td></tr>';
                return;
            }
            history.forEach(payroll => {
                const statusClass = {
                    'Draft': 'bg-gray-100 text-gray-800',
                    'Pending': 'bg-yellow-100 text-yellow-800',
                    'Approved': 'bg-green-100 text-green-800',
                    'Paid': 'bg-blue-100 text-blue-800',
                    'Cancelled': 'bg-red-100 text-red-800'
                };
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                <td class="px-4 py-3 whitespace-nowrap">${payroll.period}</td>
                <td class="px-4 py-3 whitespace-nowrap">₱${parseFloat(payroll.basic_salary).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                <td class="px-4 py-3 whitespace-nowrap">₱${parseFloat(payroll.overtime_pay).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                <td class="px-4 py-3 whitespace-nowrap">₱${parseFloat(payroll.allowances).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                <td class="px-4 py-3 whitespace-nowrap">₱${parseFloat(payroll.deductions).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                <td class="px-4 py-3 font-bold whitespace-nowrap">₱${parseFloat(payroll.net_pay).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass[payroll.status] || 'bg-gray-100 text-gray-800'}">
                        ${payroll.status}
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">${new Date(payroll.processed_at).toLocaleDateString()}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <button onclick="viewPayrollDetails(${payroll.id})" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </button>
                </td>
            `;
                tbody.appendChild(row);
            });
            lucide.createIcons();
        }

        async function viewPayrollDetails(payrollId) {
            try {
                const response = await fetch('API/payroll_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=get_payroll_details&payroll_id=${payrollId}`
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        title: 'Payroll Details',
                        html: `<div class="text-left">${Object.entries(data.payroll).map(([k,v])=>`<p><strong>${k}:</strong> ${v}</p>`).join('')}</div>`,
                        icon: 'info',
                        confirmButtonText: 'Close',
                        target: document.body
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        target: document.body
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error!',
                    text: 'Failed to load payroll details',
                    target: document.body
                });
            }
        }

        // ---------- CRUD functions (using Tailwind-styled Swal) ----------
        function approveSalary(employeeId, employeeName) {
            currentAction = 'approve';
            currentEmployeeId = employeeId;
            currentEmployeeName = employeeName;

            Swal.fire({
                title: 'Approve for Financing',
                html: `Approve financing for: <strong>${employeeName}</strong>`,
                input: 'textarea',
                inputLabel: 'Comments (Optional)',
                inputPlaceholder: 'Enter optional comments for approval...',
                showCancelButton: true,
                confirmButtonText: 'Approve',
                cancelButtonText: 'Cancel',
                target: document.body,
                customClass: {
                    confirmButton: 'bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-lg',
                    cancelButton: 'bg-gray-300 text-gray-800 hover:bg-gray-400 px-4 py-2 rounded-lg'
                },
                preConfirm: (comment) => { return comment === '' ? null : comment; }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const comment = result.value || '';
                    try {
                        const response = await fetch('API/payroll_api.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `action=update_salary_status&id=${employeeId}&status=For financing&reason=${encodeURIComponent(comment)}`
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Approved!',
                                text: `Financing for ${employeeName} has been approved`,
                                timer: 2000,
                                showConfirmButton: false,
                                target: document.body
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message,
                                target: document.body
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error!',
                            text: 'Failed to approve financing',
                            target: document.body
                        });
                    }
                }
            });
        }

        function denySalary(employeeId, employeeName) {
            currentAction = 'deny';
            currentEmployeeId = employeeId;
            currentEmployeeName = employeeName;

            Swal.fire({
                title: 'Deny Financing Request',
                html: `Deny financing request for: <strong>${employeeName}</strong>`,
                input: 'textarea',
                inputLabel: 'Reason (Required)',
                inputPlaceholder: 'Enter detailed reason for denial...',
                inputValidator: (value) => { if (!value) return 'You need to provide a reason!'; },
                showCancelButton: true,
                confirmButtonText: 'Deny',
                cancelButtonText: 'Cancel',
                target: document.body,
                customClass: {
                    confirmButton: 'bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-lg',
                    cancelButton: 'bg-gray-300 text-gray-800 hover:bg-gray-400 px-4 py-2 rounded-lg'
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const comment = result.value;
                    try {
                        const response = await fetch('API/payroll_api.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `action=update_salary_status&id=${employeeId}&status=Denied financing&reason=${encodeURIComponent(comment)}`
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Denied!',
                                text: `Financing for ${employeeName} has been denied`,
                                timer: 2000,
                                showConfirmButton: false,
                                target: document.body
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message,
                                target: document.body
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error!',
                            text: 'Failed to deny financing',
                            target: document.body
                        });
                    }
                }
            });
        }

        function forCompliance(employeeId, employeeName) {
            currentAction = 'compliance';
            currentEmployeeId = employeeId;
            currentEmployeeName = employeeName;

            Swal.fire({
                title: 'Send for Compliance Review',
                html: `Send salary for compliance review: <strong>${employeeName}</strong>`,
                input: 'textarea',
                inputLabel: 'Compliance Notes (Required)',
                inputPlaceholder: 'Enter compliance review notes...',
                inputValidator: (value) => { if (!value) return 'You need to provide compliance notes!'; },
                showCancelButton: true,
                confirmButtonText: 'Send for Compliance',
                cancelButtonText: 'Cancel',
                target: document.body,
                customClass: {
                    confirmButton: 'bg-yellow-600 text-white hover:bg-yellow-700 px-4 py-2 rounded-lg',
                    cancelButton: 'bg-gray-300 text-gray-800 hover:bg-gray-400 px-4 py-2 rounded-lg'
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const comment = result.value;
                    try {
                        const response = await fetch('API/payroll_api.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `action=update_salary_status&id=${employeeId}&status=For compliance&reason=${encodeURIComponent(comment)}`
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sent for Compliance!',
                                text: `Salary for ${employeeName} has been sent for compliance review`,
                                timer: 2000,
                                showConfirmButton: false,
                                target: document.body
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message,
                                target: document.body
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error!',
                            text: 'Failed to send for compliance',
                            target: document.body
                        });
                    }
                }
            });
        }

        function markActive(employeeId) {
            updateWorkStatus(employeeId, 'Active');
        }

        function markInactive(employeeId) {
            Swal.fire({
                title: 'Mark as Inactive?',
                text: "This employee will be marked as inactive.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, mark inactive',
                cancelButtonText: 'Cancel',
                target: document.body,
                customClass: {
                    confirmButton: 'bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-lg',
                    cancelButton: 'bg-gray-300 text-gray-800 hover:bg-gray-400 px-4 py-2 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateWorkStatus(employeeId, 'Inactive');
                }
            });
        }

        async function updateWorkStatus(employeeId, status) {
            try {
                const response = await fetch('API/payroll_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=update_work_status&id=${employeeId}&status=${status}`
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        target: document.body
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        target: document.body
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error!',
                    text: 'Failed to update work status',
                    target: document.body
                });
            }
        }

        function createPayroll(employeeId, employeeName, currentSalary) {
            currentEmployeeId = employeeId;
            document.getElementById('payrollEmployeeId').value = employeeId;
            document.getElementById('payrollId').value = '';
            document.getElementById('payrollModalTitle').textContent = `Create Payroll for ${employeeName}`;
            document.getElementById('payrollSubmitBtn').textContent = 'Create Payroll';

            document.getElementById('payrollForm').reset();
            document.getElementById('period').value = new Date().toISOString().slice(0, 7);
            document.getElementById('basicSalary').value = currentSalary;
            document.getElementById('payrollStatus').value = 'Draft';

            calculatePayrollSummary();
            payrollModal.showModal();
        }

        async function editPayroll(employeeId, employeeName) {
            try {
                const response = await fetch('API/payroll_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=get_current_payroll&employee_id=${employeeId}`
                });
                const data = await response.json();
                if (data.success && data.payroll) {
                    currentEmployeeId = employeeId;
                    document.getElementById('payrollEmployeeId').value = employeeId;
                    document.getElementById('payrollId').value = data.payroll.id;
                    document.getElementById('payrollModalTitle').textContent = `Edit Payroll for ${employeeName}`;
                    document.getElementById('payrollSubmitBtn').textContent = 'Update Payroll';

                    document.getElementById('basicSalary').value = data.payroll.basic_salary;
                    document.getElementById('overtimeHours').value = data.payroll.overtime_hours;
                    document.getElementById('overtimeRate').value = data.payroll.overtime_rate;
                    document.getElementById('allowances').value = data.payroll.allowances;
                    document.getElementById('deductions').value = data.payroll.deductions;
                    document.getElementById('period').value = data.payroll.period;
                    document.getElementById('payrollStatus').value = data.payroll.status;
                    document.getElementById('payrollNotes').value = data.payroll.notes || '';

                    calculatePayrollSummary();
                    payrollModal.showModal();
                } else {
                    createPayroll(employeeId, employeeName, 0);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error!',
                    text: 'Failed to load payroll data',
                    target: document.body
                });
            }
        }

        function closePayrollModal() {
            payrollModal.close();
            document.getElementById('payrollForm').reset();
            currentEmployeeId = null;
        }

        function calculatePayrollSummary() {
            const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
            const overtimeHours = parseFloat(document.getElementById('overtimeHours').value) || 0;
            const overtimeRate = parseFloat(document.getElementById('overtimeRate').value) || 100;
            const allowances = parseFloat(document.getElementById('allowances').value) || 0;
            const deductions = parseFloat(document.getElementById('deductions').value) || 0;

            const overtimePay = overtimeHours * overtimeRate;
            const netPay = basicSalary + overtimePay + allowances - deductions;

            document.getElementById('summaryBasic').textContent = '₱' + basicSalary.toLocaleString('en-US', { minimumFractionDigits: 2 });
            document.getElementById('summaryOvertime').textContent = '₱' + overtimePay.toLocaleString('en-US', { minimumFractionDigits: 2 });
            document.getElementById('summaryAllowances').textContent = '₱' + allowances.toLocaleString('en-US', { minimumFractionDigits: 2 });
            document.getElementById('summaryDeductions').textContent = '₱' + deductions.toLocaleString('en-US', { minimumFractionDigits: 2 });
            document.getElementById('summaryNetPay').textContent = '₱' + netPay.toLocaleString('en-US', { minimumFractionDigits: 2 });
        }

        document.getElementById('basicSalary')?.addEventListener('input', calculatePayrollSummary);
        document.getElementById('overtimeHours')?.addEventListener('input', calculatePayrollSummary);
        document.getElementById('overtimeRate')?.addEventListener('input', calculatePayrollSummary);
        document.getElementById('allowances')?.addEventListener('input', calculatePayrollSummary);
        document.getElementById('deductions')?.addEventListener('input', calculatePayrollSummary);

        document.getElementById('payrollForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('payrollSubmitBtn');
            submitBtn.disabled = true;
            const previousLabel = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';

            try {
                const employeeId = document.getElementById('payrollEmployeeId').value;
                const payrollId = document.getElementById('payrollId').value;
                const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
                const overtimeHours = parseFloat(document.getElementById('overtimeHours').value) || 0;
                const overtimeRate = parseFloat(document.getElementById('overtimeRate').value) || 0;
                const allowances = parseFloat(document.getElementById('allowances').value) || 0;
                const deductions = parseFloat(document.getElementById('deductions').value) || 0;
                const period = document.getElementById('period').value;
                const status = document.getElementById('payrollStatus').value;
                const notes = document.getElementById('payrollNotes').value || '';

                if (basicSalary < 0) throw new Error('Basic salary must be 0 or greater');

                const overtimePay = overtimeHours * overtimeRate;
                const netPay = basicSalary + overtimePay + allowances - deductions;

                if (status === 'Paid') {
                    const confirmPaid = await Swal.fire({
                        title: 'Mark as Paid?',
                        text: `This will mark net pay ₱${netPay.toLocaleString('en-US', { minimumFractionDigits: 2 })} as paid. Continue?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, mark Paid',
                        cancelButtonText: 'Cancel',
                        target: document.body,
                        customClass: {
                            confirmButton: 'bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg',
                            cancelButton: 'bg-gray-300 text-gray-800 hover:bg-gray-400 px-4 py-2 rounded-lg'
                        }
                    });
                    if (!confirmPaid.isConfirmed) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = previousLabel;
                        return;
                    }
                }

                const action = payrollId ? 'update_payroll' : 'create_payroll';

                const params = new URLSearchParams();
                params.append('action', action);
                if (payrollId) params.append('payroll_id', payrollId);
                else params.append('employee_id', employeeId);
                params.append('basic_salary', basicSalary);
                params.append('overtime_hours', overtimeHours);
                params.append('overtime_rate', overtimeRate);
                params.append('overtime_pay', overtimePay);
                params.append('allowances', allowances);
                params.append('deductions', deductions);
                params.append('net_pay', netPay);
                params.append('period', period);
                params.append('status', status);
                params.append('notes', notes);

                const response = await fetch('API/payroll_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: params.toString()
                });

                if (!response.ok) throw new Error('Server error');

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        target: document.body
                    }).then(() => { closePayrollModal(); location.reload(); });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to save payroll',
                        target: document.body
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to save payroll',
                    target: document.body
                });
            } finally {
                const submitBtnFinal = document.getElementById('payrollSubmitBtn');
                submitBtnFinal.disabled = false;
                submitBtnFinal.textContent = previousLabel;
            }
        });
    </script>
</body>

</html>