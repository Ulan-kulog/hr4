<?php
session_start();
include("../connection.php");

$db_name = "hr4_hr_4";
$conn = $connections[$db_name] ?? die("❌ Connection not found for $db_name");

// Pagination variables
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search and filter variables
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$department_filter = isset($_GET['department']) ? $_GET['department'] : '';

// Build query conditions
$conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $conditions[] = "(e.employee_id LIKE ? OR e.first_name LIKE ? OR e.last_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

if (!empty($status_filter)) {
    $conditions[] = "e.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($department_filter)) {
    $conditions[] = "d.id = ?";
    $params[] = $department_filter;
    $types .= 'i';
}

$where_clause = '';
if (!empty($conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $conditions);
}

// Fetch departments for dropdown
$departments = [];
$dept_query = "SELECT id, name FROM departments ORDER BY name";
$dept_result = $conn->query($dept_query);
if ($dept_result) {
    while ($row = $dept_result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Fetch employees data with pagination and filters
$employees = [];
$count_query = "SELECT COUNT(*) as total FROM employees e 
                LEFT JOIN departments d ON e.department_id = d.id 
                $where_clause";

if (!empty($params)) {
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $count_result = $stmt->get_result();
} else {
    $count_result = $conn->query($count_query);
}

if ($count_result) {
    $row = $count_result->fetch_assoc();
    $total_records = $row['total'];
    $total_pages = ceil($total_records / $limit);
}

// Fetch employees data
$query = "SELECT e.*, d.name as department_name, 
          CONCAT(e.first_name, ' ', e.last_name) as full_name,
          DATE_FORMAT(e.hire_date, '%M %d, %Y') as formatted_hire_date
          FROM employees e 
          LEFT JOIN departments d ON e.department_id = d.id 
          $where_clause 
          ORDER BY e.hire_date DESC 
          LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Fetch stats data
$stats = [
    'total_employees' => 0,
    'new_hires' => 0,
    'departments' => 0,
    'on_leave' => 0
];

// Total employees
$stats_query = "SELECT COUNT(*) as total FROM employees";
$stats_result = $conn->query($stats_query);
if ($stats_result) {
    $row = $stats_result->fetch_assoc();
    $stats['total_employees'] = $row['total'];
}

// New hires this month
$new_hires_query = "SELECT COUNT(*) as total FROM employees 
                   WHERE MONTH(hire_date) = MONTH(CURRENT_DATE()) 
                   AND YEAR(hire_date) = YEAR(CURRENT_DATE())";
$new_hires_result = $conn->query($new_hires_query);
if ($new_hires_result) {
    $row = $new_hires_result->fetch_assoc();
    $stats['new_hires'] = $row['total'];
}

// Total departments
$dept_count_query = "SELECT COUNT(*) as total FROM departments";
$dept_count_result = $conn->query($dept_count_query);
if ($dept_count_result) {
    $row = $dept_count_result->fetch_assoc();
    $stats['departments'] = $row['total'];
}

// Employees on leave
$leave_query = "SELECT COUNT(*) as total FROM employees WHERE work_status = 'on_leave'";
$leave_result = $conn->query($leave_query);
if ($leave_result) {
    $row = $leave_result->fetch_assoc();
    $stats['on_leave'] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Human Capital | HR Management System</title>
    <?php include '../INCLUDES/header.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        :root {
            --primary-color: #001f54;
            --accent-color: #F7B32B;
        }
        
        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .avatar.online::before,
        .avatar.offline::before,
        .avatar.away::before {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .avatar.online::before {
            background-color: #10B981;
        }
        
        .avatar.offline::before {
            background-color: #EF4444;
        }
        
        .avatar.away::before {
            background-color: #F59E0B;
        }
    </style>
</head>
<body class="min-h-screen bg-base-100">
    <!-- Drawer for mobile -->
    <div class="drawer lg:drawer-open">
        <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content flex flex-col">
            <!-- Navbar -->
            <?php include '../INCLUDES/navbar.php'; ?>
            
            <!-- Main Content -->
            <main class="p-4 md:p-6 flex-1">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Employees -->
                    <div class="stats-card card bg-base-100 shadow-lg border">
                        <div class="card-body">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="card-title text-3xl font-bold"><?php echo $stats['total_employees']; ?></h3>
                                    <p class="text-sm opacity-70">Total Employees</p>
                                </div>
                                <div class="p-3 rounded-full bg-primary/10 text-primary">
                                    <i data-lucide="users" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Active: <?php echo $stats['total_employees'] - $stats['on_leave']; ?></span>
                                    <span><?php echo $stats['total_employees']; ?> total</span>
                                </div>
                                <progress class="progress progress-primary w-full" 
                                          value="<?php echo $stats['total_employees'] - $stats['on_leave']; ?>" 
                                          max="<?php echo $stats['total_employees']; ?>"></progress>
                            </div>
                        </div>
                    </div>

                    <!-- New Hires -->
                    <div class="stats-card card bg-base-100 shadow-lg border">
                        <div class="card-body">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="card-title text-3xl font-bold"><?php echo $stats['new_hires']; ?></h3>
                                    <p class="text-sm opacity-70">New Hires This Month</p>
                                </div>
                                <div class="p-3 rounded-full bg-success/10 text-success">
                                    <i data-lucide="user-plus" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>This month</span>
                                    <span><?php echo $stats['new_hires']; ?> hires</span>
                                </div>
                                <progress class="progress progress-success w-full" 
                                          value="<?php echo $stats['new_hires']; ?>" 
                                          max="50"></progress>
                            </div>
                        </div>
                    </div>

                    <!-- Departments -->
                    <div class="stats-card card bg-base-100 shadow-lg border">
                        <div class="card-body">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="card-title text-3xl font-bold"><?php echo $stats['departments']; ?></h3>
                                    <p class="text-sm opacity-70">Active Departments</p>
                                </div>
                                <div class="p-3 rounded-full bg-info/10 text-info">
                                    <i data-lucide="building" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Active departments</span>
                                    <span><?php echo $stats['departments']; ?> units</span>
                                </div>
                                <progress class="progress progress-info w-full" 
                                          value="<?php echo $stats['departments']; ?>" 
                                          max="20"></progress>
                            </div>
                        </div>
                    </div>

                    <!-- On Leave -->
                    <div class="stats-card card bg-base-100 shadow-lg border">
                        <div class="card-body">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="card-title text-3xl font-bold"><?php echo $stats['on_leave']; ?></h3>
                                    <p class="text-sm opacity-70">Currently On Leave</p>
                                </div>
                                <div class="p-3 rounded-full bg-warning/10 text-warning">
                                    <i data-lucide="umbrella" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>On leave</span>
                                    <span><?php echo $stats['on_leave']; ?> employees</span>
                                </div>
                                <progress class="progress progress-warning w-full" 
                                          value="<?php echo $stats['on_leave']; ?>" 
                                          max="<?php echo $stats['total_employees']; ?>"></progress>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons and Filters -->
                <div class="card bg-base-100 shadow-lg border mb-6">
                    <div class="card-body">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <button onclick="addEmployeeModal.showModal()" class="btn btn-primary">
                                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                                    Add New Employee
                                </button>
                                <button class="btn btn-outline">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    Export Report
                                </button>
                            </div>
                            
                            <!-- Search and Filters -->
                            <form method="GET" class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                                <div class="join">
                                    <div>
                                        <div>
                                            <input class="input input-bordered join-item" 
                                                   type="text" 
                                                   name="search" 
                                                   placeholder="Search Employee ID or Name"
                                                   value="<?php echo htmlspecialchars($search); ?>">
                                        </div>
                                    </div>
                                    <select class="select select-bordered join-item" name="status">
                                        <option value="">All Status</option>
                                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="on_leave" <?php echo $status_filter === 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
                                        <option value="terminated" <?php echo $status_filter === 'terminated' ? 'selected' : ''; ?>>Terminated</option>
                                    </select>
                                    <select class="select select-bordered join-item" name="department">
                                        <option value="">All Departments</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo $dept['id']; ?>" 
                                                <?php echo $department_filter == $dept['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($dept['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="indicator">
                                        <button type="submit" class="btn btn-primary join-item">
                                            <i data-lucide="filter" class="w-4 h-4"></i>
                                            Filter
                                        </button>
                                        <?php if (!empty($search) || !empty($status_filter) || !empty($department_filter)): ?>
                                            <a href="?" class="btn btn-ghost btn-sm absolute -top-2 -right-2">
                                                <i data-lucide="x" class="w-3 h-3"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Employees Table -->
                <div class="card bg-base-100 shadow-lg border">
                    <div class="card-body p-0">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr class="bg-base-200">
                                        <th>Employee</th>
                                        <th>Employee ID</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Hire Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($employees)): ?>
                                        <?php foreach ($employees as $employee): ?>
                                            <tr class="hover">
                                                <td>
                                                    <div class="flex items-center gap-3">
                                                        <div class="avatar <?php echo $employee['work_status'] === 'active' ? 'online' : ($employee['work_status'] === 'on_leave' ? 'away' : 'offline'); ?>">
                                                            <div class="w-10 h-10 rounded-full">
                                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($employee['full_name']); ?>&background=001f54&color=fff" 
                                                                     alt="<?php echo htmlspecialchars($employee['full_name']); ?>" />
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold"><?php echo htmlspecialchars($employee['full_name']); ?></div>
                                                            <div class="text-sm opacity-50"><?php echo htmlspecialchars($employee['email']); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="badge badge-outline"><?php echo htmlspecialchars($employee['id']); ?></div>
                                                </td>
                                                <td><?php echo htmlspecialchars($employee['position'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($employee['department_name'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php if ($employee['work_status'] === 'active'): ?>
                                                        <span class="badge badge-success gap-1">
                                                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                            Active
                                                        </span>
                                                    <?php elseif ($employee['work_status'] === 'on_leave'): ?>
                                                        <span class="badge badge-warning gap-1">
                                                            <i data-lucide="umbrella" class="w-3 h-3"></i>
                                                            On Leave
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-error gap-1">
                                                            <i data-lucide="x-circle" class="w-3 h-3"></i>
                                                            Terminated
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $employee['formatted_hire_date'] ?? 'N/A'; ?></td>
                                                <td>
                                                    <div class="flex gap-2">
                                                        <button class="btn btn-ghost btn-xs" onclick="viewEmployee(<?php echo $employee['id']; ?>)">
                                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                                        </button>
                                                        <button class="btn btn-ghost btn-xs" onclick="editEmployee(<?php echo $employee['id']; ?>)">
                                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-8">
                                                <div class="flex flex-col items-center gap-2">
                                                    <i data-lucide="users" class="w-12 h-12 opacity-20"></i>
                                                    <p class="text-lg opacity-70">No employees found</p>
                                                    <p class="text-sm opacity-50">Try adjusting your filters or add new employees</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="flex justify-between items-center p-4 border-t">
                                <div class="text-sm opacity-70">
                                    Showing <span class="font-bold"><?php echo (($page - 1) * $limit) + 1; ?></span> 
                                    to <span class="font-bold"><?php echo min($page * $limit, $total_records); ?></span> 
                                    of <span class="font-bold"><?php echo $total_records; ?></span> results
                                </div>
                                <div class="join">
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)])); ?>" 
                                       class="btn btn-outline join-item <?php echo $page <= 1 ? 'btn-disabled' : ''; ?>">
                                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                    </a>
                                    
                                    <?php for ($i = 1; $i <= min(5, $total_pages); $i++): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                           class="btn join-item <?php echo $page == $i ? 'btn-active' : 'btn-outline'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($total_pages > 5): ?>
                                        <button class="btn join-item btn-outline btn-disabled">...</button>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>" 
                                           class="btn join-item btn-outline">
                                            <?php echo $total_pages; ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => min($total_pages, $page + 1)])); ?>" 
                                       class="btn btn-outline join-item <?php echo $page >= $total_pages ? 'btn-disabled' : ''; ?>">
                                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Sidebar for desktop -->
        <div class="drawer-side">
            <label for="my-drawer-2" aria-label="close sidebar" class="drawer-overlay"></label>
            <?php include '../INCLUDES/sidebar.php'; ?>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <dialog id="addEmployeeModal" class="modal">
        <div class="modal-box max-w-5xl rounded-lg">


         <form method="dialog">
    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
</form>
<h3 class="font-bold text-lg mb-6">Add New Job Position</h3>

<form id="addJobPositionForm" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
            <label class="label">
                <span class="label-text">Position Title *</span>
            </label>
            <input type="text" name="title" required 
                   class="input input-bordered w-full" 
                   placeholder="e.g., Senior Software Engineer">
        </div>
        
        <div class="form-control">
            <label class="label">
                <span class="label-text">Employment Type *</span>
            </label>
            <select name="type" required class="select select-bordered w-full">
    <option value="">Select Type</option>
    <option value="full_time">Full-time</option>
    <option value="part_time">Part-time</option>
    <option value="contract">Contract</option>
    <option value="internship">Internship</option>
</select>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
            <label class="label">
                <span class="label-text">Department *</span>
            </label>
            <select name="department_id" id="departmentSelect" required class="select select-bordered w-full">
                <option value="">Select Department</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo $dept['id']; ?>">
                        <?php echo htmlspecialchars($dept['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-control">
            <label class="label">
                <span class="label-text">Status *</span>
            </label>
            <select name="status" required class="select select-bordered w-full">
                <option value="draft">Draft</option>
                <option value="open" selected>Open</option>
                <option value="closed">Closed</option>
            </select>
            <label class="label">
                <span class="label-text-alt text-gray-500">Draft: Not visible, Open: Accepting applications, Closed: No longer accepting</span>
            </label>
        </div>
    </div>
    
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
            <label class="label">
                <span class="label-text">Salary Range *</span>
            </label>
            <div class="flex items-center gap-2">
                <span class="text-gray-600">₱</span>
                <input type="number" name="salary_min" required min="0" step="0.01"
                       class="input input-bordered w-full" 
                       placeholder="Minimum salary">
            </div>
            <label class="label">
                <span class="label-text-alt text-gray-500">Minimum salary in pesos</span>
            </label>
        </div>
        
        <div class="form-control">
            <label class="label">
                <span class="label-text">&nbsp;</span>
            </label>
            <div class="flex items-center gap-2">
                <span class="text-gray-600">₱</span>
                <input type="number" name="salary_max" required min="0" step="0.01"
                       class="input input-bordered w-full" 
                       placeholder="Maximum salary">
            </div>
            <label class="label">
                <span class="label-text-alt text-gray-500">Maximum salary in pesos</span>
            </label>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
            <label class="label">
                <span class="label-text">Number of Vacancies *</span>
            </label>
            <input type="number" name="vacancies" required min="1" value="1"
                   class="input input-bordered w-full">
        </div>
        
        <div class="form-control">
            <label class="label">
                <span class="label-text">Job Posting Duration *</span>
            </label>
            <div class="flex items-center gap-2">
                <input type="number" name="job_period_days" required min="1" value="30"
                       class="input input-bordered w-full">
                <span class="text-gray-600">Days</span>
            </div>
            <label class="label">
                <span class="label-text-alt text-gray-500">How many days the job will be posted</span>
            </label>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
            <label class="label cursor-pointer">
                <span class="label-text">Exam Required</span>
                <input type="checkbox" name="exam_required" class="toggle toggle-primary">
            </label>
            <label class="label">
                <span class="label-text-alt text-gray-500">Check if exam is required for this position</span>
            </label>
        </div>
    </div>
    
    <div class="modal-action">
        <button type="button" onclick="addEmployeeModal.close()" class="btn btn-ghost">Cancel</button>
        <button type="submit" class="btn btn-primary">
            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
            Save Job Position
        </button>
    </div>
</form>
        </div>
    </dialog>

    <!-- Toast Notification -->
    <div id="toast" class="toast toast-top toast-end z-50">
        <div class="alert hidden" id="toast-alert">
            <span id="toast-message"></span>
        </div>
    </div>

    <script src="../JAVASCRIPT/sidebar.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Toast function
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-alert');
            const messageEl = document.getElementById('toast-message');
            
            // Set message and type
            messageEl.textContent = message;
            toast.className = `alert ${type === 'success' ? 'alert-success' : 'alert-error'} shadow-lg`;
            
            // Show toast
            toast.classList.remove('hidden');
            
            // Hide after 3 seconds
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }
        
        // View employee function
        function viewEmployee(id) {
            // Implement view functionality
            showToast(`Viewing employee ID: ${id}`, 'info');
        }
        
        // Edit employee function
        function editEmployee(id) {
            // Implement edit functionality
            showToast(`Editing employee ID: ${id}`, 'info');
        }
        
        // Form submission for adding employee
        document.getElementById('addEmployeeForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Saving...';
            submitBtn.disabled = true;
            
            try {
                const formData = new FormData(form);
                
                // In a real application, you would send this to your API
                // For now, we'll simulate a successful submission
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                showToast('Employee added successfully!', 'success');
                
                // Close modal
                addEmployeeModal.close();
                form.reset();
                
                // Reload page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
            } catch (error) {
                console.error('Error:', error);
                showToast('Failed to add employee. Please try again.', 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                lucide.createIcons();
            }
        });
        
        // Mobile drawer toggle
        document.getElementById('menu-toggle')?.addEventListener('click', function() {
            document.getElementById('my-drawer-2').checked = true;
        });

        // Department change handler for sub-department dropdown
document.getElementById('departmentSelect')?.addEventListener('change', function(e) {
    const departmentId = e.target.value;
    const subDepartmentSelect = document.getElementById('subDepartmentSelect');
    
    if (departmentId) {
        // Enable sub-department dropdown
        subDepartmentSelect.disabled = false;
        subDepartmentSelect.innerHTML = '<option value="">Loading sub-departments...</option>';
        
        // In a real application, you would fetch sub-departments from API
        // For now, we'll simulate with sample data
        setTimeout(() => {
            const sampleSubDepartments = [
                { id: 1, name: 'Web Development' },
                { id: 2, name: 'Mobile Development' },
                { id: 3, name: 'DevOps' },
                { id: 4, name: 'Quality Assurance' },
                { id: 5, name: 'UI/UX Design' }
            ];
            
            subDepartmentSelect.innerHTML = '<option value="">Select Sub-Department (Optional)</option>';
            sampleSubDepartments.forEach(subDept => {
                const option = document.createElement('option');
                option.value = subDept.id;
                option.textContent = subDept.name;
                subDepartmentSelect.appendChild(option);
            });
        }, 500);
    } else {
        // Disable and reset sub-department dropdown
        subDepartmentSelect.disabled = true;
        subDepartmentSelect.innerHTML = '<option value="">Select Sub-Department (Optional)</option>';
    }
});

// Form submission for adding job position with confirmation
document.getElementById('addJobPositionForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    
    // First show confirmation dialog
    const confirmResult = await Swal.fire({
        title: 'Add Job Position?',
        text: 'Are you sure you want to add this job position?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#001f54',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Add It!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    });
    
    // If user cancels, stop here
    if (!confirmResult.isConfirmed) {
        return;
    }
    
    // Proceed with validation and submission
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Validate salary range
    const salaryMin = parseFloat(form.salary_min.value);
    const salaryMax = parseFloat(form.salary_max.value);
    
    if (salaryMin > salaryMax) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: `
                <div class="text-left">
                    <p class="mb-2"><strong>Salary Range Error</strong></p>
                    <p class="text-sm">Minimum salary (₱${salaryMin.toLocaleString()}) cannot be greater than maximum salary (₱${salaryMax.toLocaleString()})</p>
                </div>
            `,
            confirmButtonColor: '#001f54',
        });
        return;
    }
    
    // Validate job period
    const jobPeriodDays = parseInt(form.job_period_days.value);
    if (jobPeriodDays < 1) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Job posting duration must be at least 1 day',
            confirmButtonColor: '#001f54',
        });
        return;
    }
    
    // Validate vacancies
    const vacancies = parseInt(form.vacancies.value);
    if (vacancies < 1) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Number of vacancies must be at least 1',
            confirmButtonColor: '#001f54',
        });
        return;
    }
    
    // Show loading SweetAlert
    Swal.fire({
        title: 'Processing...',
        text: 'Please wait while we save the job position',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const formData = new FormData(form);
        
        // Send to API
        const response = await fetch('API/save_employee.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        // Close loading SweetAlert
        Swal.close();
        
        if (result.success) {
            // Success SweetAlert with auto-close and reload
            await Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: `
                    <div class="text-left">
                        <p class="mb-2"><strong>Job Position Added Successfully</strong></p>
                        <div class="space-y-1 text-sm">
                            <p><span class="font-medium">Title:</span> ${result.data?.title || 'N/A'}</p>
                            <p><span class="font-medium">Type:</span> ${result.data?.type || 'N/A'}</p>
                            <p><span class="font-medium">Status:</span> ${result.data?.status || 'N/A'}</p>
                            <p><span class="font-medium">Salary Range:</span> ${result.data?.salary_range || 'N/A'}</p>
                            <p><span class="font-medium">Vacancies:</span> ${result.data?.vacancies || 'N/A'}</p>
                        </div>
                    </div>
                `,
                confirmButtonColor: '#001f54',
                confirmButtonText: 'OK',
                willClose: () => {
                    // Close modal
                    addEmployeeModal.close();
                    form.reset();
                    
                    // Reload page to show new data
                    window.location.reload();
                }
            });
        } else {
            // Error SweetAlert
            Swal.fire({
                icon: 'error',
                title: 'Failed to Save',
                html: `
                    <div class="text-left">
                        <p class="mb-2"><strong>Error Details:</strong></p>
                        <p class="text-sm">${result.message || 'An error occurred while saving the job position'}</p>
                    </div>
                `,
                confirmButtonColor: '#001f54',
            });
        }
    } catch (error) {
        console.error('Error:', error);
        // Close loading SweetAlert
        Swal.close();
        
        // Network/Server Error SweetAlert
        Swal.fire({
            icon: 'error',
            title: 'Connection Error',
            html: `
                <div class="text-left">
                    <p class="mb-2"><strong>Network Error</strong></p>
                    <p class="text-sm">Failed to connect to server. Please check your connection and try again.</p>
                    <p class="text-xs mt-2 text-gray-500">Error: ${error.message}</p>
                </div>
            `,
            confirmButtonColor: '#001f54',
        });
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        lucide.createIcons();
    }
});
    </script>
</body>
</html>