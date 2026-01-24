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
function getStringValue($value, $default = '') {
    if (is_array($value)) {
        if (is_string($value[0] ?? null)) {
            return $value[0];
        }
        return implode(' ', $value);
    }
    return (string) $value ?: (string) $default;
}

// Helper function to build full name
function buildFullName($first_name, $middle_name, $last_name) {
    $name = trim($first_name ?? '');
    if (!empty($middle_name)) {
        $name .= ' ' . $middle_name;
    }
    if (!empty($last_name)) {
        $name .= ' ' . $last_name;
    }
    return trim($name);
}

// Fetch ALL employees from API and save to database
function fetchAndSaveEmployees($conn) {
    $api_url = "https://hr1.soliera-hotel-restaurant.com/api/employees";
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $data = json_decode($response, true);
        if (is_array($data)) {
            foreach ($data as $employee) {
                // Save or update employee in database
                saveEmployeeToDB($conn, $employee);
            }
            return $data;
        }
    } else {
        error_log("Failed to fetch employees from API. HTTP Code: $http_code");
    }
    return [];
}

// Save employee to database
function saveEmployeeToDB($conn, $api_employee) {
    // Safely extract values from API
    $employee_code = getStringValue($api_employee['employee_code'] ?? $api_employee['id'] ?? '');
    
    // Build name components
    $first_name = $conn->real_escape_string(getStringValue($api_employee['first_name'] ?? ''));
    $middle_name = $conn->real_escape_string(getStringValue($api_employee['middle_name'] ?? ''));
    $last_name = $conn->real_escape_string(getStringValue($api_employee['last_name'] ?? ''));
    
    // If API provides full_name, try to parse it
    if (empty($first_name) && isset($api_employee['full_name'])) {
        $full_name = getStringValue($api_employee['full_name']);
        $name_parts = explode(' ', $full_name, 3);
        $first_name = $conn->real_escape_string($name_parts[0] ?? '');
        if (isset($name_parts[1])) {
            if (isset($name_parts[2])) {
                $middle_name = $conn->real_escape_string($name_parts[1]);
                $last_name = $conn->real_escape_string($name_parts[2]);
            } else {
                $last_name = $conn->real_escape_string($name_parts[1]);
            }
        }
    }
    
    $email = $conn->real_escape_string(getStringValue($api_employee['email'] ?? ''));
    $phone_number = $conn->real_escape_string(getStringValue($api_employee['phone'] ?? $api_employee['phone_number'] ?? ''));
    $job = $conn->real_escape_string(getStringValue($api_employee['job'] ?? $api_employee['position'] ?? 'N/A'));
    
    // Handle department - your table uses department_id, not department name
    $department_id = $api_employee['department_id'] ?? null;
    
    // Handle salary (ensure it's numeric)
    $salary_value = $api_employee['expected_salary'] ?? $api_employee['basic_salary'] ?? $api_employee['salary'] ?? 0;
    $salary = is_numeric($salary_value) ? $salary_value : 0;
    $basic_salary = $api_employee['basic_salary'] ?? $salary;
    
    $work_status = $conn->real_escape_string(getStringValue($api_employee['work_status'] ?? 'Active'));
    $employment_status = $conn->real_escape_string(getStringValue($api_employee['employment_status'] ?? 'Active'));
    
    // Check if employee exists by employee_code
    $check_sql = "SELECT id, employee_code FROM employees WHERE employee_code = '$employee_code'";
    $result = $conn->query($check_sql);
    
    if ($result && $result->num_rows > 0) {
        // Update existing employee
        $row = $result->fetch_assoc();
        $employee_id = $row['id'];
        
        $update_sql = "UPDATE employees SET 
            first_name = '$first_name',
            middle_name = '$middle_name',
            last_name = '$last_name',
            email = '$email',
            phone_number = '$phone_number',
            job = '$job',
            salary = '$salary',
            basic_salary = '$basic_salary',
            work_status = '$work_status',
            employment_status = '$employment_status',
            updated_at = CURRENT_TIMESTAMP";
        
        if ($department_id !== null) {
            $update_sql .= ", department_id = '$department_id'";
        }
        
        $update_sql .= " WHERE employee_code = '$employee_code'";
        
        $conn->query($update_sql);
        
        // Update salary_status to 'Under review' if not already set
        $conn->query("UPDATE employees SET salary_status = 'Under review' WHERE employee_code = '$employee_code' AND (salary_status IS NULL OR salary_status = '')");
        
        return $employee_id;
    } else {
        // Insert new employee
        $insert_sql = "INSERT INTO employees (
            employee_code,
            first_name,
            middle_name,
            last_name,
            email,
            phone_number,
            job,
            salary,
            basic_salary,
            work_status,
            employment_status,
            salary_status
        ) VALUES (
            '$employee_code',
            '$first_name',
            '$middle_name',
            '$last_name',
            '$email',
            '$phone_number',
            '$job',
            '$salary',
            '$basic_salary',
            '$work_status',
            '$employment_status',
            'Under review'
        )";
        
        if ($conn->query($insert_sql)) {
            return $conn->insert_id;
        }
    }
    return null;
}

// Fetch employees from API and save to DB
$api_employees = fetchAndSaveEmployees($conn);

// Fetch local employees from database with payroll info
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
                WHERE e.work_status IN ('Active', 'Under Review')
                ORDER BY e.first_name, e.last_name";
$local_result = $conn->query($local_query);

// Store employee data
$employees_data = [];
$payroll_data = [];
while($row = $local_result->fetch_assoc()) {
    $employees_data[$row['id']] = $row;
    if ($row['payroll_id']) {
        $payroll_data[$row['id']] = $row;
    }
}

// Calculate statistics
$stats = [
    'total_employees' => count($employees_data),
    'under_review' => 0,
    'approved' => 0,
    'denied' => 0,
    'for_compliance' => 0,
    'total_payroll' => 0,
    'active' => 0,
    'inactive' => 0
];

foreach($employees_data as $employee) {
    $status = $employee['salary_status'] ?? 'Under review';
    switch($status) {
        case 'Under review': $stats['under_review']++; break;
        case 'Approved': $stats['approved']++; break;
        case 'Denied': $stats['denied']++; break;
        case 'For compliance': $stats['for_compliance']++; break;
    }
    
    if ($employee['work_status'] == 'Active') {
        $stats['active']++;
    } else {
        $stats['inactive']++;
    }
    
    if ($employee['net_pay']) {
        $stats['total_payroll'] += $employee['net_pay'];
    }
}
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
    <style>
        .swal2-container {
            z-index: 999999 !important;
        }
        .modal {
            z-index: 99999;
        }
        .modal-box {
            max-height: 90vh;
            overflow-y: auto;
        }
        .table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            padding: 1rem 0.75rem;
        }
        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }
        .avatar.placeholder > div {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .glass-effect {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
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
        <main class="flex-1 overflow-auto p-4 md:p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Payroll Analytics Dashboard</h1>
                <p class="text-gray-600">Comprehensive payroll management and employee analytics</p>
            </div>

            <!-- Payroll Analytics Dashboard -->
            <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect mb-8">
                <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center gap-4 mb-6">
                    <h2 class="flex items-center font-bold text-gray-800 text-2xl">
                        <span class="bg-indigo-100/50 mr-3 p-2 rounded-lg text-indigo-600">
                            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        </span>
                        Payroll Analytics Dashboard
                    </h2>
                    <div class="flex gap-2">
                        <button onclick="exportToExcel()" class="flex items-center bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition-colors">
                            <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                            Export Report
                        </button>
                        <select class="bg-white px-4 py-2 border border-gray-300 rounded-lg text-gray-700">
                            <option>Last 30 Days</option>
                            <option>Last Quarter</option>
                            <option>Year to Date</option>
                            <option>Last Year</option>
                        </select>
                    </div>
                </div>

                <!-- Key Payroll Metrics -->
                <div class="gap-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <!-- Total Employees -->
                    <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Total Employees</p>
                                <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['total_employees']; ?></h3>
                                <p class="mt-1 text-gray-500 text-xs">All active employees</p>
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

                    <!-- Approved -->
                    <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Approved</p>
                                <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['approved']; ?></h3>
                                <p class="mt-1 text-gray-500 text-xs">Ready for payroll</p>
                            </div>
                            <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                <i data-lucide="check-circle" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- For Compliance -->
                    <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">For Compliance</p>
                                <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['for_compliance']; ?></h3>
                                <p class="mt-1 text-gray-500 text-xs">Requires compliance review</p>
                            </div>
                            <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                <i data-lucide="shield" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Payroll Metrics -->
                <div class="gap-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-8">
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

                    <!-- Denied -->
                    <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Denied</p>
                                <h3 class="mt-1 font-bold text-3xl"><?php echo $stats['denied']; ?></h3>
                                <p class="mt-1 text-gray-500 text-xs">Salary requests rejected</p>
                            </div>
                            <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                <i data-lucide="x-circle" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Payroll -->
                    <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Total Payroll</p>
                                <h3 class="mt-1 font-bold text-3xl">₱<?php echo number_format($stats['total_payroll'], 0); ?></h3>
                                <p class="mt-1 text-gray-500 text-xs">Current month total</p>
                            </div>
                            <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                <i data-lucide="dollar-sign" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Average Salary -->
                    <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Avg. Salary</p>
                                <?php
                                $avg_salary = $stats['total_employees'] > 0 ? array_sum(array_column($employees_data, 'salary')) / $stats['total_employees'] : 0;
                                ?>
                                <h3 class="mt-1 font-bold text-3xl">₱<?php echo number_format($avg_salary, 0); ?></h3>
                                <p class="mt-1 text-gray-500 text-xs">Per employee monthly</p>
                            </div>
                            <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                <i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls & Employee List -->
            <div class="card bg-white shadow-lg mb-6">
                <div class="card-body">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h2 class="text-xl font-semibold">Employee List</h2>
                            <p class="text-gray-500">Manage salary status and payroll</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button onclick="syncEmployees()" class="btn btn-primary">
                                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                                Sync from API
                            </button>
                            <button onclick="exportToExcel()" class="btn btn-outline">
                                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                Export Excel
                            </button>
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-outline">
                                    <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                                    Filter
                                </div>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a onclick="filterByStatus('all')">All Employees</a></li>
                                    <li><a onclick="filterByStatus('Under review')">Under Review Only</a></li>
                                    <li><a onclick="filterByStatus('Active')">Active Only</a></li>
                                    <li><a onclick="filterByStatus('Approved')">Approved Only</a></li>
                                    <li><a onclick="filterByStatus('Denied')">Denied Only</a></li>
                                    <li><a onclick="filterByStatus('For compliance')">For Compliance Only</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Table -->
            <div class="card bg-white shadow-lg">
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-auto w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Work Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payroll Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach($employees_data as $employee): 
                                    $full_name = buildFullName($employee['first_name'], $employee['middle_name'], $employee['last_name']);
                                    $work_status_class = [
                                        'Active' => 'bg-green-100 text-green-800',
                                        'Inactive' => 'bg-red-100 text-red-800',
                                        'On Leave' => 'bg-yellow-100 text-yellow-800',
                                        'Under Review' => 'bg-blue-100 text-blue-800'
                                    ];
                                    $salary_status_class = [
                                        'Under review' => 'bg-yellow-100 text-yellow-800',
                                        'Approved' => 'bg-green-100 text-green-800',
                                        'Denied' => 'bg-red-100 text-red-800',
                                        'For compliance' => 'bg-purple-100 text-purple-800'
                                    ];
                                ?>
                                <tr class="employee-row hover:bg-gray-50 transition-colors" 
                                    data-salary-status="<?php echo $employee['salary_status']; ?>"
                                    data-work-status="<?php echo $employee['work_status']; ?>">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                                    <?php echo substr($employee['first_name'], 0, 1) . substr($employee['last_name'] ?? '', 0, 1); ?>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($full_name); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($employee['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <?php echo $employee['employee_code']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($employee['job']); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">₱<?php echo number_format($employee['salary'], 2); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $work_status_class[$employee['work_status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $employee['work_status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $salary_status_class[$employee['salary_status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $employee['salary_status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?php if(isset($payroll_data[$employee['id']])): 
                                            $payroll_status_class = [
                                                'Approved' => 'bg-green-100 text-green-800',
                                                'Paid' => 'bg-blue-100 text-blue-800',
                                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                                'Draft' => 'bg-gray-100 text-gray-800'
                                            ];
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $payroll_status_class[$employee['payroll_status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $employee['payroll_status']; ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            No Payroll
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">₱<?php echo number_format($employee['net_pay'] ?? 0, 2); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="viewEmployee(<?php echo $employee['id']; ?>)" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                                View
                                            </button>
                                            <div class="relative">
                                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dropdown-toggle">
                                                    <i data-lucide="more-vertical" class="w-3 h-3"></i>
                                                </button>
                                                <div class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden dropdown-menu">
                                                    <div class="py-1" role="menu">
                                                        <a href="#" onclick="approveSalary(<?php echo $employee['id']; ?>, '<?php echo addslashes($full_name); ?>')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Approve Salary</a>
                                                        <a href="#" onclick="denySalary(<?php echo $employee['id']; ?>, '<?php echo addslashes($full_name); ?>')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Deny Salary</a>
                                                        <a href="#" onclick="forCompliance(<?php echo $employee['id']; ?>, '<?php echo addslashes($full_name); ?>')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">For Compliance</a>
                                                        <div class="border-t border-gray-100"></div>
                                                        <?php if(!isset($payroll_data[$employee['id']])): ?>
                                                        <a href="#" onclick="createPayroll(<?php echo $employee['id']; ?>, '<?php echo addslashes($full_name); ?>', <?php echo $employee['salary']; ?>)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Create Payroll</a>
                                                        <?php else: ?>
                                                        <a href="#" onclick="editPayroll(<?php echo $employee['id']; ?>, '<?php echo addslashes($full_name); ?>')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Edit Payroll</a>
                                                        <?php endif; ?>
                                                        <a href="#" onclick="viewPayrollHistory(<?php echo $employee['id']; ?>, '<?php echo addslashes($full_name); ?>')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Payroll History</a>
                                                        <div class="border-t border-gray-100"></div>
                                                        <?php if($employee['work_status'] == 'Active'): ?>
                                                        <a href="#" onclick="markInactive(<?php echo $employee['id']; ?>)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Mark Inactive</a>
                                                        <?php else: ?>
                                                        <a href="#" onclick="markActive(<?php echo $employee['id']; ?>)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Mark Active</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

             <!-- View Employee Modal with CRUD actions in footer -->
  <dialog id="viewModal" class="modal">
    <div class="modal-box max-w-4xl">
      <h3 class="font-bold text-lg mb-6">Employee Details</h3>
      <div id="employeeDetails" class="space-y-6">
        <!-- Content loaded via JavaScript -->
      </div>
      <div class="modal-action mt-8 pt-6 border-t">
        <!-- CRUD Actions in Modal Footer -->
        <div class="flex flex-col w-full gap-4">
          <!-- Action Buttons -->
          <div class="flex flex-wrap gap-2 justify-between" id="salaryActionButtons">
            <div class="flex flex-wrap gap-2">
              <button id="approveSalaryBtn" class="btn btn-success btn-sm" onclick="showSalaryActionForm('approve')">
                <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                Approve Salary
              </button>
              <button id="denySalaryBtn" class="btn btn-error btn-sm" onclick="showSalaryActionForm('deny')">
                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                Deny Salary Request
              </button>
              <button id="forComplianceBtn" class="btn btn-warning btn-sm" onclick="showSalaryActionForm('compliance')">
                <i data-lucide="shield" class="w-4 h-4 mr-2"></i>
                For Compliance
              </button>
              <button onclick="viewPayrollHistory(currentEmployeeId, currentEmployeeData?.first_name + ' ' + currentEmployeeData?.last_name)" class="btn btn-info btn-sm">
                <i data-lucide="history" class="w-4 h-4 mr-2"></i>
                Payroll History
              </button>
            </div>
            <button class="btn btn-ghost btn-sm" onclick="viewModal.close()">Close</button>
          </div>
          
          <!-- Action Form (hidden by default) -->
          <div id="salaryActionForm" class="hidden w-full">
            <div class="card bg-base-100 shadow">
              <div class="card-body p-4">
                <h4 class="card-title" id="salaryActionTitle">Action</h4>
                <input type="hidden" id="currentEmployeeId">
                <input type="hidden" id="currentActionType">
                
                <div class="form-control">
                  <label class="label">
                    <span class="label-text" id="salaryActionLabel">Reason/Comments</span>
                    <span class="label-text-alt text-red-500">*Required</span>
                  </label>
                  <textarea id="salaryActionComment" class="textarea textarea-bordered h-24" 
                            placeholder="Enter reason for this action..." required></textarea>
                  <div class="label">
                    <span class="label-text-alt text-gray-500" id="salaryActionHint">
                      Please provide detailed comments for this action
                    </span>
                  </div>
                </div>
                
                <div class="flex gap-2 justify-end mt-4">
                  <button type="button" class="btn btn-ghost" onclick="hideSalaryActionForm()">
                    Cancel
                  </button>
                  <button type="button" class="btn btn-primary" onclick="submitSalaryAction()">
                    Submit Action
                  </button>
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
    <div class="modal-box max-w-6xl">
      <h3 class="font-bold text-lg mb-4" id="payrollHistoryTitle">Payroll History</h3>
      <div class="overflow-x-auto">
        <table class="table table-auto w-full">
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
          <tbody id="payrollHistoryBody">
            <!-- Payroll history will be loaded here -->
          </tbody>
        </table>
      </div>
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="payrollHistoryModal.close()">Close</button>
      </div>
    </div>
  </dialog>

  <!-- Create/Edit Payroll Modal -->
  <dialog id="payrollModal" class="modal">
    <div class="modal-box max-w-2xl">
      <h3 class="font-bold text-lg mb-4" id="payrollModalTitle">Create Payroll</h3>
      <form id="payrollForm">
        <input type="hidden" id="payrollEmployeeId">
        <input type="hidden" id="payrollId">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="form-control">
            <label class="label">
              <span class="label-text">Basic Salary</span>
            </label>
            <input type="number" id="basicSalary" class="input input-bordered" required step="0.01">
          </div>
          
          <div class="form-control">
            <label class="label">
              <span class="label-text">Overtime Hours</span>
            </label>
            <input type="number" id="overtimeHours" class="input input-bordered" step="0.5" value="0">
          </div>
          
          <div class="form-control">
            <label class="label">
              <span class="label-text">Overtime Rate/Hour</span>
            </label>
            <input type="number" id="overtimeRate" class="input input-bordered" step="0.01" value="100">
          </div>
          
          <div class="form-control">
            <label class="label">
              <span class="label-text">Allowances</span>
            </label>
            <input type="number" id="allowances" class="input input-bordered" step="0.01" value="0">
          </div>
          
          <div class="form-control">
            <label class="label">
              <span class="label-text">Deductions</span>
            </label>
            <input type="number" id="deductions" class="input input-bordered" step="0.01" value="0">
          </div>
          
          <div class="form-control">
            <label class="label">
              <span class="label-text">Period</span>
            </label>
            <input type="month" id="period" class="input input-bordered" value="<?php echo date('Y-m'); ?>">
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text">Status</span>
            </label>
            <select id="payrollStatus" class="select select-bordered">
              <option value="Draft">Draft</option>
              <option value="Pending">Pending</option>
              <option value="Approved">Approved</option>
              <option value="Paid">Paid</option>
            </select>
          </div>
          
          <div class="form-control md:col-span-2">
            <label class="label">
              <span class="label-text">Notes (Optional)</span>
            </label>
            <textarea id="payrollNotes" class="textarea textarea-bordered" placeholder="Any notes about this payroll..."></textarea>
          </div>
        </div>
        
        <!-- Summary -->
        <div class="card bg-base-100 mt-6">
          <div class="card-body">
            <h4 class="card-title">Payroll Summary</h4>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span>Basic Salary:</span>
                <span id="summaryBasic" class="font-semibold">₱0.00</span>
              </div>
              <div class="flex justify-between">
                <span>Overtime Pay:</span>
                <span id="summaryOvertime" class="font-semibold">₱0.00</span>
              </div>
              <div class="flex justify-between">
                <span>Allowances:</span>
                <span id="summaryAllowances" class="font-semibold">₱0.00</span>
              </div>
              <div class="flex justify-between">
                <span>Deductions:</span>
                <span id="summaryDeductions" class="font-semibold">₱0.00</span>
              </div>
              <div class="divider"></div>
              <div class="flex justify-between text-lg">
                <span>Net Pay:</span>
                <span id="summaryNetPay" class="font-bold text-primary">₱0.00</span>
              </div>
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
    
    let currentAction = null;
    let currentEmployeeId = null;
    let currentEmployeeName = null;
    let currentEmployeeData = null;

    // Initialize dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize dropdown menus
        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const menu = this.nextElementSibling;
                menu.classList.toggle('hidden');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        });
    });

    // Sync employees from API
    async function syncEmployees() {
        const result = await Swal.fire({
            title: 'Sync Employees?',
            text: 'This will fetch the latest employee data from API',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, sync',
            cancelButtonText: 'Cancel'
        });
        
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Syncing...',
                text: 'Please wait while we sync employees from API',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                const response = await fetch('API/payroll_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=sync_employees'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error!',
                    text: 'Failed to sync employees'
                });
            }
        }
    }

    // View Employee Details
    async function viewEmployee(employeeId) {
        try {
            const response = await fetch('API/payroll_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_employee_details&id=${employeeId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                currentEmployeeData = data.data;
                currentEmployeeId = employeeId;
                displayEmployeeDetails(data.data);
                
                // Show action buttons based on current status
                updateActionButtons(data.data.salary_status);
                
                viewModal.showModal();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to load employee details'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Network Error!',
                text: 'Failed to load employee details'
            });
        }
    }

    function updateActionButtons(currentStatus) {
        const approveBtn = document.getElementById('approveSalaryBtn');
        const denyBtn = document.getElementById('denySalaryBtn');
        const complianceBtn = document.getElementById('forComplianceBtn');
        
        // Disable buttons based on current status
        if (currentStatus === 'Approved') {
            approveBtn.disabled = true;
            approveBtn.classList.add('btn-disabled');
            approveBtn.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Already Approved';
        } else {
            approveBtn.disabled = false;
            approveBtn.classList.remove('btn-disabled');
            approveBtn.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Approve Salary';
        }
        
        if (currentStatus === 'Denied') {
            denyBtn.disabled = true;
            denyBtn.classList.add('btn-disabled');
            denyBtn.innerHTML = '<i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>Already Denied';
        } else {
            denyBtn.disabled = false;
            denyBtn.classList.remove('btn-disabled');
            denyBtn.innerHTML = '<i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>Deny Salary Request';
        }
        
        if (currentStatus === 'For compliance') {
            complianceBtn.disabled = true;
            complianceBtn.classList.add('btn-disabled');
            complianceBtn.innerHTML = '<i data-lucide="shield" class="w-4 h-4 mr-2"></i>Already in Compliance';
        } else {
            complianceBtn.disabled = false;
            complianceBtn.classList.remove('btn-disabled');
            complianceBtn.innerHTML = '<i data-lucide="shield" class="w-4 h-4 mr-2"></i>For Compliance';
        }
    }

    function displayEmployeeDetails(data) {
        const full_name = data.first_name + (data.middle_name ? ' ' + data.middle_name : '') + (data.last_name ? ' ' + data.last_name : '');
        
        const details = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h4 class="card-title">Personal Information</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="font-medium">Full Name:</span>
                                <span>${full_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Employee Code:</span>
                                <span>${data.employee_code}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Email:</span>
                                <span>${data.email || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Phone:</span>
                                <span>${data.phone_number || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Date of Birth:</span>
                                <span>${data.date_of_birth || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Job Information -->
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h4 class="card-title">Job Information</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="font-medium">Job/Position:</span>
                                <span>${data.job}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Department ID:</span>
                                <span>${data.department_id || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Work Status:</span>
                                <span class="badge ${data.work_status === 'Active' ? 'badge-success' : (data.work_status === 'Inactive' ? 'badge-error' : 'badge-warning')}">
                                    ${data.work_status}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Employment Status:</span>
                                <span>${data.employment_status}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Hire Date:</span>
                                <span>${data.hire_date || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Salary Information -->
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h4 class="card-title">Salary Information</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="font-medium">Salary:</span>
                                <span class="font-bold">₱${parseFloat(data.salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Basic Salary:</span>
                                <span>₱${parseFloat(data.basic_salary || data.salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Salary Status:</span>
                                <span class="badge ${getSalaryStatusClass(data.salary_status)}">
                                    ${data.salary_status || 'Under review'}
                                </span>
                            </div>
                            ${data.salary_reason ? `
                            <div>
                                <span class="font-medium">Reason:</span>
                                <p class="mt-1 p-2 bg-base-200 rounded">${data.salary_reason}</p>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
                
                <!-- Payroll Information -->
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h4 class="card-title">Payroll Information</h4>
                        ${data.payroll ? `
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>Payroll Status:</span>
                                    <span class="badge ${data.payroll.status === 'Approved' ? 'badge-success' : (data.payroll.status === 'Paid' ? 'badge-primary' : 'badge-warning')}">
                                        ${data.payroll.status}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Period:</span>
                                    <span>${data.payroll.period}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Net Pay:</span>
                                    <span class="font-bold">₱${parseFloat(data.payroll.net_pay).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Overtime Pay:</span>
                                    <span>₱${parseFloat(data.payroll.overtime_pay).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                </div>
                                ${data.payroll.notes ? `
                                <div>
                                    <span class="font-medium">Notes:</span>
                                    <p class="mt-1 p-2 bg-base-200 rounded">${data.payroll.notes}</p>
                                </div>
                                ` : ''}
                            </div>
                        ` : '<p class="text-center text-gray-500">No payroll record found</p>'}
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('employeeDetails').innerHTML = details;
    }

    function getSalaryStatusClass(status) {
        const classes = {
            'Under review': 'badge-warning',
            'Approved': 'badge-success',
            'Denied': 'badge-error',
            'For compliance': 'badge-info'
        };
        return classes[status] || 'badge-outline';
    }

    // Show salary action form in modal footer
    function showSalaryActionForm(actionType) {
        currentAction = actionType;
        
        // Set current employee ID
        document.getElementById('currentEmployeeId').value = currentEmployeeId;
        document.getElementById('currentActionType').value = actionType;
        
        // Update form title and labels based on action type
        const actionTitle = document.getElementById('salaryActionTitle');
        const actionLabel = document.getElementById('salaryActionLabel');
        const actionHint = document.getElementById('salaryActionHint');
        const actionComment = document.getElementById('salaryActionComment');
        
        switch(actionType) {
            case 'approve':
                actionTitle.textContent = 'Approve Salary Request';
                actionLabel.textContent = 'Approval Comments (Optional)';
                actionHint.textContent = 'Optional comments explaining why this salary is approved';
                actionComment.placeholder = 'Enter approval comments (optional)...';
                break;
            case 'deny':
                actionTitle.textContent = 'Deny Salary Request';
                actionLabel.textContent = 'Denial Reason (Required)';
                actionHint.textContent = 'Required: Please provide detailed reason for denying this salary request';
                actionComment.placeholder = 'Enter detailed reason for denial...';
                break;
            case 'compliance':
                actionTitle.textContent = 'Send for Compliance Review';
                actionLabel.textContent = 'Compliance Notes (Required)';
                actionHint.textContent = 'Required: Please specify what compliance issues need to be addressed';
                actionComment.placeholder = 'Enter compliance review notes...';
                break;
        }
        
        // Hide action buttons and show form
        document.getElementById('salaryActionButtons').classList.add('hidden');
        document.getElementById('salaryActionForm').classList.remove('hidden');
        
        // Focus on textarea
        setTimeout(() => {
            actionComment.focus();
        }, 100);
    }

    // Hide salary action form
    function hideSalaryActionForm() {
        document.getElementById('salaryActionForm').classList.add('hidden');
        document.getElementById('salaryActionButtons').classList.remove('hidden');
        document.getElementById('salaryActionComment').value = '';
        currentAction = null;
    }

    // Submit salary action
    async function submitSalaryAction() {
        const employeeId = document.getElementById('currentEmployeeId').value;
        const actionType = document.getElementById('currentActionType').value;
        const comment = document.getElementById('salaryActionComment').value.trim();
        
        // Validation for required comments
        if ((actionType === 'deny' || actionType === 'compliance') && !comment) {
            Swal.fire({
                icon: 'warning',
                title: 'Comments Required',
                text: 'Please provide detailed comments for this action'
            });
            return;
        }
        
        // Map action type to status
        let status = '';
        switch(actionType) {
            case 'approve': status = 'Approved'; break;
            case 'deny': status = 'Denied'; break;
            case 'compliance': status = 'For compliance'; break;
        }
        
        try {
            const response = await fetch('API/payroll_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_salary_status&id=${employeeId}&status=${status}&reason=${encodeURIComponent(comment)}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    hideSalaryActionForm();
                    viewModal.close();
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Network Error!',
                text: 'Failed to update salary status'
            });
        }
    }

    // View Payroll History
    async function viewPayrollHistory(employeeId, employeeName) {
        try {
            const response = await fetch('API/payroll_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
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
                    text: data.message || 'Failed to load payroll history'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Network Error!',
                text: 'Failed to load payroll history'
            });
        }
    }

    function displayPayrollHistory(history) {
        const tbody = document.getElementById('payrollHistoryBody');
        tbody.innerHTML = '';
        
        if (history.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                        No payroll history found
                    </td>
                </tr>
            `;
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
                <td class="px-4 py-3 whitespace-nowrap">₱${parseFloat(payroll.basic_salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-3 whitespace-nowrap">₱${parseFloat(payroll.overtime_pay).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-3 whitespace-nowrap">₱${parseFloat(payroll.allowances).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-3 whitespace-nowrap">₱${parseFloat(payroll.deductions).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-3 whitespace-nowrap font-bold">₱${parseFloat(payroll.net_pay).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
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
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_payroll_details&payroll_id=${payrollId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    title: 'Payroll Details',
                    html: `
                        <div class="text-left">
                            <p><strong>Period:</strong> ${data.payroll.period}</p>
                            <p><strong>Status:</strong> ${data.payroll.status}</p>
                            <p><strong>Basic Salary:</strong> ₱${parseFloat(data.payroll.basic_salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                            <p><strong>Overtime Hours:</strong> ${data.payroll.overtime_hours}</p>
                            <p><strong>Overtime Rate:</strong> ₱${parseFloat(data.payroll.overtime_rate).toLocaleString('en-US', {minimumFractionDigits: 2})}/hr</p>
                            <p><strong>Overtime Pay:</strong> ₱${parseFloat(data.payroll.overtime_pay).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                            <p><strong>Allowances:</strong> ₱${parseFloat(data.payroll.allowances).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                            <p><strong>Deductions:</strong> ₱${parseFloat(data.payroll.deductions).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                            <p><strong>Net Pay:</strong> ₱${parseFloat(data.payroll.net_pay).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                            <p><strong>Processed By:</strong> ${data.payroll.processed_by}</p>
                            <p><strong>Processed At:</strong> ${new Date(data.payroll.processed_at).toLocaleString()}</p>
                            ${data.payroll.notes ? `<p><strong>Notes:</strong> ${data.payroll.notes}</p>` : ''}
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Network Error!',
                text: 'Failed to load payroll details'
            });
        }
    }

    // Filter functions
    function filterByStatus(status) {
        const rows = document.querySelectorAll('.employee-row');
        rows.forEach(row => {
            if (status === 'all') {
                row.style.display = '';
            } else if (status === 'Active') {
                row.style.display = row.getAttribute('data-work-status') === 'Active' ? '' : 'none';
            } else {
                row.style.display = row.getAttribute('data-salary-status') === status ? '' : 'none';
            }
        });
    }

    // CRUD Operations from table dropdown
    function approveSalary(employeeId, employeeName) {
        currentAction = 'approve';
        currentEmployeeId = employeeId;
        currentEmployeeName = employeeName;
        
        Swal.fire({
            title: 'Approve Salary',
            html: `Approve salary for: <strong>${employeeName}</strong>`,
            input: 'textarea',
            inputLabel: 'Comments (Optional)',
            inputPlaceholder: 'Enter optional comments for approval...',
            showCancelButton: true,
            confirmButtonText: 'Approve',
            cancelButtonText: 'Cancel',
            preConfirm: (comment) => {
                if (comment === '') {
                    return null; // Allow empty comments
                }
                return comment;
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                const comment = result.value || '';
                
                try {
                    const response = await fetch('API/payroll_api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=update_salary_status&id=${employeeId}&status=Approved&reason=${encodeURIComponent(comment)}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Approved!',
                            text: `Salary for ${employeeName} has been approved`,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error!',
                        text: 'Failed to approve salary'
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
            title: 'Deny Salary Request',
            html: `Deny salary request for: <strong>${employeeName}</strong>`,
            input: 'textarea',
            inputLabel: 'Reason (Required)',
            inputPlaceholder: 'Enter detailed reason for denial...',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to provide a reason!';
                }
            },
            showCancelButton: true,
            confirmButtonText: 'Deny',
            cancelButtonText: 'Cancel'
        }).then(async (result) => {
            if (result.isConfirmed) {
                const comment = result.value;
                
                try {
                    const response = await fetch('API/payroll_api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=update_salary_status&id=${employeeId}&status=Denied&reason=${encodeURIComponent(comment)}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Denied!',
                            text: `Salary for ${employeeName} has been denied`,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error!',
                        text: 'Failed to deny salary'
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
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to provide compliance notes!';
                }
            },
            showCancelButton: true,
            confirmButtonText: 'Send for Compliance',
            cancelButtonText: 'Cancel'
        }).then(async (result) => {
            if (result.isConfirmed) {
                const comment = result.value;
                
                try {
                    const response = await fetch('API/payroll_api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=update_salary_status&id=${employeeId}&status=For compliance&reason=${encodeURIComponent(comment)}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sent for Compliance!',
                            text: `Salary for ${employeeName} has been sent for compliance review`,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error!',
                        text: 'Failed to send for compliance'
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
            cancelButtonText: 'Cancel'
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
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_work_status&id=${employeeId}&status=${status}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Network Error!',
                text: 'Failed to update work status'
            });
        }
    }

    // Create/Edit Payroll
    function createPayroll(employeeId, employeeName, currentSalary) {
        currentEmployeeId = employeeId;
        document.getElementById('payrollEmployeeId').value = employeeId;
        document.getElementById('payrollId').value = '';
        document.getElementById('payrollModalTitle').textContent = `Create Payroll for ${employeeName}`;
        document.getElementById('payrollSubmitBtn').textContent = 'Create Payroll';
        
        // Reset form
        document.getElementById('payrollForm').reset();
        document.getElementById('period').value = new Date().toISOString().slice(0, 7);
        document.getElementById('basicSalary').value = currentSalary;
        document.getElementById('payrollStatus').value = 'Draft';
        
        // Calculate initial summary
        calculatePayrollSummary();
        
        payrollModal.showModal();
    }

    async function editPayroll(employeeId, employeeName) {
        try {
            const response = await fetch('API/payroll_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_current_payroll&employee_id=${employeeId}`
            });
            
            const data = await response.json();
            
            if (data.success && data.payroll) {
                currentEmployeeId = employeeId;
                document.getElementById('payrollEmployeeId').value = employeeId;
                document.getElementById('payrollId').value = data.payroll.id;
                document.getElementById('payrollModalTitle').textContent = `Edit Payroll for ${employeeName}`;
                document.getElementById('payrollSubmitBtn').textContent = 'Update Payroll';
                
                // Fill form with existing data
                document.getElementById('basicSalary').value = data.payroll.basic_salary;
                document.getElementById('overtimeHours').value = data.payroll.overtime_hours;
                document.getElementById('overtimeRate').value = data.payroll.overtime_rate;
                document.getElementById('allowances').value = data.payroll.allowances;
                document.getElementById('deductions').value = data.payroll.deductions;
                document.getElementById('period').value = data.payroll.period;
                document.getElementById('payrollStatus').value = data.payroll.status;
                document.getElementById('payrollNotes').value = data.payroll.notes || '';
                
                // Calculate initial summary
                calculatePayrollSummary();
                
                payrollModal.showModal();
            } else {
                createPayroll(employeeId, employeeName, 0);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Network Error!',
                text: 'Failed to load payroll data'
            });
        }
    }

    function closePayrollModal() {
        payrollModal.close();
        document.getElementById('payrollForm').reset();
        currentEmployeeId = null;
    }

    // Calculate payroll summary
    function calculatePayrollSummary() {
        const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
        const overtimeHours = parseFloat(document.getElementById('overtimeHours').value) || 0;
        const overtimeRate = parseFloat(document.getElementById('overtimeRate').value) || 100;
        const allowances = parseFloat(document.getElementById('allowances').value) || 0;
        const deductions = parseFloat(document.getElementById('deductions').value) || 0;
        
        const overtimePay = overtimeHours * overtimeRate;
        const netPay = basicSalary + overtimePay + allowances - deductions;
        
        document.getElementById('summaryBasic').textContent = '₱' + basicSalary.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('summaryOvertime').textContent = '₱' + overtimePay.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('summaryAllowances').textContent = '₱' + allowances.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('summaryDeductions').textContent = '₱' + deductions.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('summaryNetPay').textContent = '₱' + netPay.toLocaleString('en-US', {minimumFractionDigits: 2});
    }

    // Add event listeners for calculation
    document.getElementById('basicSalary').addEventListener('input', calculatePayrollSummary);
    document.getElementById('overtimeHours').addEventListener('input', calculatePayrollSummary);
    document.getElementById('overtimeRate').addEventListener('input', calculatePayrollSummary);
    document.getElementById('allowances').addEventListener('input', calculatePayrollSummary);
    document.getElementById('deductions').addEventListener('input', calculatePayrollSummary);

    // Handle payroll form submission
    document.getElementById('payrollForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const employeeId = document.getElementById('payrollEmployeeId').value;
        const payrollId = document.getElementById('payrollId').value;
        const basicSalary = document.getElementById('basicSalary').value;
        const overtimeHours = document.getElementById('overtimeHours').value;
        const overtimeRate = document.getElementById('overtimeRate').value;
        const allowances = document.getElementById('allowances').value;
        const deductions = document.getElementById('deductions').value;
        const period = document.getElementById('period').value;
        const status = document.getElementById('payrollStatus').value;
        const notes = document.getElementById('payrollNotes').value;
        
        const overtimePay = overtimeHours * overtimeRate;
        const netPay = parseFloat(basicSalary) + parseFloat(overtimePay) + parseFloat(allowances) - parseFloat(deductions);
        
        const action = payrollId ? 'update_payroll' : 'create_payroll';
        const bodyData = payrollId 
            ? `action=${action}&payroll_id=${payrollId}&basic_salary=${basicSalary}&overtime_hours=${overtimeHours}&overtime_rate=${overtimeRate}&overtime_pay=${overtimePay}&allowances=${allowances}&deductions=${deductions}&net_pay=${netPay}&period=${period}&status=${status}&notes=${encodeURIComponent(notes)}`
            : `action=${action}&employee_id=${employeeId}&basic_salary=${basicSalary}&overtime_hours=${overtimeHours}&overtime_rate=${overtimeRate}&overtime_pay=${overtimePay}&allowances=${allowances}&deductions=${deductions}&net_pay=${netPay}&period=${period}&status=${status}&notes=${encodeURIComponent(notes)}`;
        
        try {
            const response = await fetch('API/payroll_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: bodyData
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    closePayrollModal();
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Network Error!',
                text: 'Failed to save payroll'
            });
        }
    });

    // Export to Excel
    function exportToExcel() {
        window.open('API/export_employees.php', '_blank');
    }
  </script>
</body>
</html>