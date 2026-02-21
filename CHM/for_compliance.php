<?php
session_start();
include("../connection.php");

$db_name = "hr4_hr_4";
$conn = $connections[$db_name] ?? die("❌ Connection not found for $db_name");

// --- STATS CARDS: Count employees by status (using actual DB values) ---
$stats = [];
$status_list = [
    'suspended'     => 'TOTAL SUSPENDED',
    'awol'          => 'TOTAL AWOL',
    'probationary'  => 'TOTAL PROBATION',
    'terminated'    => 'TOTAL TERMINATE',
    'for_compliance'=> 'TOTAL COMPLIANCE'
];
foreach ($status_list as $db_status => $label) {
    $query = "SELECT COUNT(*) as total FROM employees WHERE employment_status = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $db_status);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats[$db_status] = $result->fetch_assoc()['total'] ?? 0;
    $stmt->close();
}
// Total employees (all)
$total_query = "SELECT COUNT(*) as total FROM employees";
$total_result = $conn->query($total_query);
$stats['total'] = $total_result->fetch_assoc()['total'] ?? 0;

// --- PAGINATION & FILTERS ---
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base conditions
$conditions = [];
$params = [];
$types = '';

// Status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'for_compliance';
if (!empty($status_filter) && $status_filter !== 'all') {
    $conditions[] = "e.employment_status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

// Other optional filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$department_filter = isset($_GET['department']) ? $_GET['department'] : '';
$sub_department_filter = isset($_GET['sub_department']) ? $_GET['sub_department'] : '';
$employee_code_filter = isset($_GET['employee_code']) ? trim($_GET['employee_code']) : '';

if (!empty($employee_code_filter)) {
    $conditions[] = "e.employee_code LIKE ?";
    $params[] = "%$employee_code_filter%";
    $types .= 's';
}
if (!empty($search)) {
    $conditions[] = "(e.first_name LIKE ? OR e.last_name LIKE ? OR e.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

// --- MODIFIED: Filter by department column (name) instead of department_id ---
if (!empty($department_filter)) {
    // Get department name from the fetched departments array (or query)
    $dept_name = null;
    // First, ensure departments are fetched (they are, but we need them now)
    // We'll fetch departments again or use already fetched? Let's fetch them early if needed.
    // But we already fetch departments later for dropdown. To avoid duplication, we'll fetch them now.
    // For simplicity, we'll requery or use a cached list. We'll requery just the name.
    $dept_query = "SELECT name FROM departments WHERE id = ?";
    $stmt_dept = $conn->prepare($dept_query);
    $stmt_dept->bind_param('i', $department_filter);
    $stmt_dept->execute();
    $dept_result = $stmt_dept->get_result();
    if ($dept_row = $dept_result->fetch_assoc()) {
        $dept_name = $dept_row['name'];
    }
    $stmt_dept->close();
    
    if ($dept_name) {
        $conditions[] = "e.department = ?";
        $params[] = $dept_name;
        $types .= 's';
    }
    // If department name not found, skip filter (or could treat as no match, but better to skip)
}

if (!empty($sub_department_filter)) {
    $conditions[] = "e.sub_department_id = ?";
    $params[] = $sub_department_filter;
    $types .= 'i';
}

$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// Fetch departments for dropdown (still needed for UI)
$departments = [];
$dept_query = "SELECT id, name FROM departments ORDER BY name";
$dept_result = $conn->query($dept_query);
if ($dept_result) {
    while ($row = $dept_result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Fetch sub-departments
$sub_departments = [];
$sub_dept_query = "SELECT id, name, department_id FROM sub_departments ORDER BY name";
$sub_dept_result = $conn->query($sub_dept_query);
if ($sub_dept_result) {
    while ($row = $sub_dept_result->fetch_assoc()) {
        $sub_departments[] = $row;
    }
}

// Count total for pagination
$count_query = "SELECT COUNT(*) as total FROM employees e $where_clause";
if (!empty($params)) {
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $count_result = $stmt->get_result();
} else {
    $count_result = $conn->query($count_query);
}
$total_records = $count_result->fetch_assoc()['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

// --- MODIFIED: Added e.department to SELECT list ---
$query = "SELECT e.id, e.department_id, e.sub_department_id, e.employee_code, 
                 e.first_name, e.middle_name, e.last_name, e.job, e.email, 
                 e.employment_status, e.work_status, e.hire_date, e.compliance_notes,
                 e.department,  -- newly added column
                 d.name as department_name, sd.name as sub_department_name,
                 CONCAT(e.first_name, ' ', e.last_name) as full_name,
                 DATE_FORMAT(e.hire_date, '%M %d, %Y') as formatted_hire_date
          FROM employees e 
          LEFT JOIN departments d ON e.department_id = d.id 
          LEFT JOIN sub_departments sd ON e.sub_department_id = sd.id
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

$employees = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Status Management | HR</title>
    <?php include '../INCLUDES/header.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
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
        .avatar.online::before { background-color: #10B981; }
        .avatar.offline::before { background-color: #EF4444; }
        .avatar.away::before { background-color: #F59E0B; }
        @media print {
            .no-print { display: none; }
            .print-only { display: block; }
        }
        .print-only { display: none; }
    </style>
</head>
<body class="bg-base-100 min-h-screen">
    <div class="drawer lg:drawer-open">
        <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
        <div class="flex flex-col drawer-content">
            <?php include '../INCLUDES/navbar.php'; ?>
            <main class="flex-1 p-4 md:p-6">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Employee Status Overview</h1>
                        <p class="opacity-70">View and manage employee statuses (Suspended, AWOL, Probationary, Terminated, For Compliance)</p>
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="exportSelected()">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export Selected
                    </button>
                </div>

                <!-- Stats Cards (unchanged) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4 mb-8">
                    <!-- Suspended -->
                    <div class="stat-card bg-white text-black shadow-xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">TOTAL SUSPENDED</p>
                                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['suspended']; ?></h3>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54]">
                                <i data-lucide="pause-circle" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                    <!-- AWOL -->
                    <div class="stat-card bg-white text-black shadow-xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">TOTAL AWOL</p>
                                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['awol']; ?></h3>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54]">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Probationary -->
                    <div class="stat-card bg-white text-black shadow-xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">TOTAL PROBATION</p>
                                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['probationary']; ?></h3>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54]">
                                <i data-lucide="clock" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Terminated -->
                    <div class="stat-card bg-white text-black shadow-xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">TOTAL TERMINATE</p>
                                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['terminated']; ?></h3>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54]">
                                <i data-lucide="user-x" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                    <!-- For Compliance -->
                    <div class="stat-card bg-white text-black shadow-xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">TOTAL COMPLIANCE</p>
                                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['for_compliance']; ?></h3>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54]">
                                <i data-lucide="file-check" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Total Employees -->
                    <div class="stat-card bg-white text-black shadow-xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">TOTAL EMPLOYEE</p>
                                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['total']; ?></h3>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54]">
                                <i data-lucide="users" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section (manual Apply button) -->
                <form method="GET" id="filterForm" class="bg-white shadow-sm mb-6 p-3 border border-gray-200 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Status</label>
                            <select name="status" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm w-full">
                                <option value="all" <?php echo ($status_filter == 'all') ? 'selected' : ''; ?>>All Statuses</option>
                                <option value="for_compliance" <?php echo ($status_filter == 'for_compliance') ? 'selected' : ''; ?>>For Compliance</option>
                                <option value="suspended" <?php echo ($status_filter == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                                <option value="awol" <?php echo ($status_filter == 'awol') ? 'selected' : ''; ?>>AWOL</option>
                                <option value="probationary" <?php echo ($status_filter == 'probationary') ? 'selected' : ''; ?>>Probationary</option>
                                <option value="terminated" <?php echo ($status_filter == 'terminated') ? 'selected' : ''; ?>>Terminated</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Department</label>
                            <select name="department" id="departmentFilter" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm w-full">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>" <?php echo ($department_filter == $dept['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Sub-Department</label>
                            <select name="sub_department" id="subDepartmentFilter" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm w-full">
                                <option value="">All Sub-Departments</option>
                                <?php foreach ($sub_departments as $sub): ?>
                                    <option value="<?php echo $sub['id']; ?>" data-dept="<?php echo $sub['department_id']; ?>" <?php echo ($sub_department_filter == $sub['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sub['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Employee Code</label>
                            <input type="text" name="employee_code" value="<?php echo htmlspecialchars($employee_code_filter); ?>" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm w-full">
                        </div>
                        <div class="flex items-end gap-1">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded text-white text-sm">Apply</button>
                            <a href="?" class="hover:bg-gray-50 px-3 py-1.5 border border-gray-300 rounded text-sm text-gray-700">Clear</a>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="block mb-1 font-medium text-gray-700 text-xs">Search (Name/Email)</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm w-full">
                    </div>
                </form>

                <!-- Employees Table with Checkboxes -->
                <div class="bg-base-100 shadow-lg border card">
                    <div class="p-0 card-body">
                        <div class="overflow-x-auto">
                            <table class="table" id="employeesTable">
                                <thead>
                                    <tr class="bg-base-200">
                                        <th class="w-10">
                                            <input type="checkbox" id="selectAllCheckbox" class="checkbox checkbox-sm">
                                        </th>
                                        <th>Employee</th>
                                        <th>Employee Code</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Employment Status</th>
                                        <th>Hire Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($employees)): ?>
                                        <?php foreach ($employees as $employee): 
                                            $details_json = htmlspecialchars(json_encode($employee), ENT_QUOTES, 'UTF-8');
                                        ?>
                                            <tr class="hover" data-employee-id="<?php echo $employee['id']; ?>" data-details='<?php echo $details_json; ?>'>
                                                <td>
                                                    <input type="checkbox" class="row-checkbox checkbox checkbox-sm" value="<?php echo $employee['id']; ?>">
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-3">
                                                        <div class="avatar <?php echo $employee['work_status'] === 'active' ? 'online' : ($employee['work_status'] === 'on_leave' ? 'away' : 'offline'); ?>">
                                                            <div class="rounded-full w-10 h-10">
                                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($employee['full_name']); ?>&background=001f54&color=fff" alt="" />
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold"><?php echo htmlspecialchars($employee['full_name']); ?></div>
                                                            <div class="opacity-50 text-sm"><?php echo htmlspecialchars($employee['email']); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><div class="badge-outline badge"><?php echo htmlspecialchars($employee['employee_code']); ?></div></td>
                                                <td><?php echo htmlspecialchars($employee['job'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($employee['department_name'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php
                                                    $status = $employee['employment_status'];
                                                    $badge_class = 'badge';
                                                    if ($status == 'for_compliance') $badge_class .= ' badge-info';
                                                    elseif ($status == 'suspended') $badge_class .= ' badge-warning';
                                                    elseif ($status == 'awol') $badge_class .= ' badge-error';
                                                    elseif ($status == 'probationary') $badge_class .= ' badge-success';
                                                    elseif ($status == 'terminated') $badge_class .= ' badge-secondary';
                                                    else $badge_class .= ' badge-ghost';
                                                    ?>
                                                    <span class="<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                                </td>
                                                <td><?php echo $employee['formatted_hire_date'] ?? 'N/A'; ?></td>
                                                <td>
                                                    <button class="btn btn-ghost btn-sm" onclick="viewEmployee(<?php echo $employee['id']; ?>)">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="py-8 text-center">No employees found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination (unchanged) -->
                        <?php if ($total_pages > 1): ?>
                            <div class="flex justify-between items-center p-4 border-t">
                                <div class="opacity-70 text-sm">
                                    Showing <span class="font-bold"><?php echo (($page - 1) * $limit) + 1; ?></span>
                                    to <span class="font-bold"><?php echo min($page * $limit, $total_records); ?></span>
                                    of <span class="font-bold"><?php echo $total_records; ?></span> results
                                </div>
                                <div class="join">
                                    <?php
                                    $query_params = $_GET;
                                    unset($query_params['page']);
                                    $base_url = '?' . http_build_query($query_params);
                                    ?>
                                    <a href="<?php echo $base_url . '&page=' . max(1, $page - 1); ?>" class="btn btn-outline join-item <?php echo $page <= 1 ? 'btn-disabled' : ''; ?>"><i data-lucide="chevron-left" class="w-4 h-4"></i></a>
                                    <?php for ($i = 1; $i <= min(5, $total_pages); $i++): ?>
                                        <a href="<?php echo $base_url . '&page=' . $i; ?>" class="btn join-item <?php echo $page == $i ? 'btn-active' : 'btn-outline'; ?>"><?php echo $i; ?></a>
                                    <?php endfor; ?>
                                    <?php if ($total_pages > 5): ?>
                                        <button class="btn-outline btn join-item btn-disabled">...</button>
                                        <a href="<?php echo $base_url . '&page=' . $total_pages; ?>" class="btn-outline btn join-item"><?php echo $total_pages; ?></a>
                                    <?php endif; ?>
                                    <a href="<?php echo $base_url . '&page=' . min($total_pages, $page + 1); ?>" class="btn btn-outline join-item <?php echo $page >= $total_pages ? 'btn-disabled' : ''; ?>"><i data-lucide="chevron-right" class="w-4 h-4"></i></a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
        <div class="drawer-side">
            <label for="my-drawer-2" class="drawer-overlay"></label>
            <?php include '../INCLUDES/sidebar.php'; ?>
        </div>
    </div>

    <!-- View Employee Modal (unchanged) -->
    <dialog id="viewEmployeeModal" class="modal">
        <div class="modal-box max-w-4xl w-11/12">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="font-bold text-lg mb-4">Employee Details</h3>
            <div id="employeeDetails" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
            
            <!-- Compliance Notes Display -->
            <div class="mt-4 p-3 bg-base-200 rounded-lg">
                <p class="font-semibold">Compliance Notes / Reason:</p>
                <p id="complianceNotesDisplay" class="text-sm italic">Loading...</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2 mt-6">
                <button class="btn btn-warning btn-sm" onclick="showActionModal('suspended')">Suspended with days</button>
                <button class="btn btn-error btn-sm" onclick="showActionModal('terminate')">Terminate</button>
                <button class="btn btn-info btn-sm" onclick="showActionModal('probationary')">Probationary with days</button>
                <button class="btn btn-secondary btn-sm" onclick="showActionModal('awol')">AWOL</button>
                <button class="btn btn-success btn-sm" onclick="showActionModal('for_compliance')">For Compliance again</button>
            </div>

            <div class="modal-action flex justify-between">
                <button class="btn btn-primary btn-sm" onclick="exportSingleEmployee()">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export as PDF
                </button>
                <button class="btn" onclick="document.getElementById('viewEmployeeModal').close()">Close</button>
            </div>
        </div>
    </dialog>

    <!-- Modal for actions that require input (unchanged) -->
    <dialog id="actionInputModal" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="font-bold text-lg" id="actionModalTitle">Enter Details</h3>
            <div class="py-4">
                <input type="hidden" id="actionEmployeeId">
                <input type="hidden" id="actionType">
                <div id="daysInputGroup" class="form-control hidden">
                    <label class="label">
                        <span class="label-text">Number of days</span>
                    </label>
                    <input type="number" id="actionDays" class="input input-bordered" min="1" value="30">
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Reason / Notes</span>
                    </label>
                    <textarea id="actionReason" class="textarea textarea-bordered" placeholder="Optional notes..."></textarea>
                </div>
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" onclick="showConfirmationModal()">Next</button>
                <button class="btn" onclick="document.getElementById('actionInputModal').close()">Cancel</button>
            </div>
        </div>
    </dialog>

    <!-- Confirmation Modal (unchanged) -->
    <dialog id="confirmationModal" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="font-bold text-lg text-warning">Confirm Action</h3>
            <div class="py-4">
                <p id="confirmationMessage" class="text-lg"></p>
            </div>
            <div class="modal-action">
                <button class="btn btn-error" onclick="executeConfirmedAction()">Yes, Proceed</button>
                <button class="btn" onclick="document.getElementById('confirmationModal').close()">Cancel</button>
            </div>
        </div>
    </dialog>

    <!-- Toast Notification (unchanged) -->
    <div id="toast" class="toast-top z-50 toast toast-end">
        <div class="hidden alert" id="toast-alert">
            <span id="toast-message"></span>
        </div>
    </div>

    <script src="../JAVASCRIPT/sidebar.js"></script>
    <script>
        lucide.createIcons();

        // Toast function (unchanged)
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-alert');
            const messageEl = document.getElementById('toast-message');
            messageEl.textContent = message;
            toast.className = `alert ${type === 'success' ? 'alert-success' : 'alert-error'} shadow-lg`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        let currentEmployeeId = null;
        let pendingAction = null;

        function viewEmployee(id) {
            currentEmployeeId = id;
            document.getElementById('viewEmployeeModal').showModal();
            fetch(`API/get_employee_details.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderEmployeeDetails(data.employee);
                        document.getElementById('complianceNotesDisplay').textContent = 
                            data.employee.compliance_notes || 'No notes provided.';
                    } else {
                        showToast('Failed to load employee details', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Error loading employee details', 'error');
                });
        }

        function renderEmployeeDetails(emp) {
            const container = document.getElementById('employeeDetails');
            container.innerHTML = `
                <div class="card bg-base-200">
                    <div class="card-body">
                        <p><span class="font-semibold">Full Name:</span> ${emp.first_name} ${emp.middle_name ? emp.middle_name + ' ' : ''}${emp.last_name}</p>
                        <p><span class="font-semibold">Employee Code:</span> ${emp.employee_code}</p>
                        <p><span class="font-semibold">Email:</span> ${emp.email}</p>
                        <p><span class="font-semibold">Phone:</span> ${emp.phone_number || 'N/A'}</p>
                        <p><span class="font-semibold">Gender:</span> ${emp.gender || 'N/A'}</p>
                    </div>
                </div>
                <div class="card bg-base-200">
                    <div class="card-body">
                        <p><span class="font-semibold">Department:</span> ${emp.department_name || 'N/A'}</p>
                        <p><span class="font-semibold">Position:</span> ${emp.job || 'N/A'}</p>
                        <p><span class="font-semibold">Hire Date:</span> ${emp.hire_date || 'N/A'}</p>
                        <p><span class="font-semibold">Employment Status:</span> ${emp.employment_status}</p>
                    </div>
                </div>
            `;
        }

        // Show input modal for action (unchanged)
        function showActionModal(action) {
            if (!currentEmployeeId) {
                showToast('No employee selected', 'error');
                return;
            }
            document.getElementById('actionEmployeeId').value = currentEmployeeId;
            document.getElementById('actionType').value = action;
            document.getElementById('actionReason').value = '';

            const titleMap = {
                suspended: 'Suspended Employee',
                terminate: 'Terminate Employee',
                probationary: 'Probationary Employee',
                awol: 'Mark as AWOL',
                for_compliance: 'Revert to For Compliance'
            };
            document.getElementById('actionModalTitle').textContent = titleMap[action] || 'Update Status';

            const daysGroup = document.getElementById('daysInputGroup');
            if (action === 'suspended' || action === 'probationary') {
                daysGroup.classList.remove('hidden');
                document.getElementById('actionDays').value = 30;
            } else {
                daysGroup.classList.add('hidden');
            }

            document.getElementById('actionInputModal').showModal();
        }

        // Show confirmation modal with appropriate message (unchanged)
        function showConfirmationModal() {
            const employeeId = document.getElementById('actionEmployeeId').value;
            const action = document.getElementById('actionType').value;
            const reason = document.getElementById('actionReason').value;
            let days = null;
            if (action === 'suspended' || action === 'probationary') {
                days = document.getElementById('actionDays').value;
                if (!days || days < 1) {
                    showToast('Please enter valid number of days', 'error');
                    return;
                }
            }

            const messages = {
                suspended: `Are you sure you want to suspend this employee for ${days} days?`,
                terminate: 'Are you sure you want to terminate this employee? This action cannot be undone.',
                probationary: `Are you sure you want to place this employee on probation for ${days} days?`,
                awol: 'Are you sure you want to mark this employee as AWOL?',
                for_compliance: 'Are you sure you want to set this employee back to "For Compliance"?'
            };
            document.getElementById('confirmationMessage').textContent = messages[action];

            pendingAction = {
                employee_id: employeeId,
                action: action,
                days: days,
                reason: reason
            };

            document.getElementById('actionInputModal').close();
            document.getElementById('confirmationModal').showModal();
        }

        function executeConfirmedAction() {
            if (!pendingAction) return;

            console.log('Sending action:', pendingAction);

            fetch('API/employment_status_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(pendingAction)
            })
            .then(async response => {
                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(`HTTP ${response.status}: ${text}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    showToast(data.message, 'success');
                    document.getElementById('confirmationModal').close();
                    document.getElementById('viewEmployeeModal').close();
                    location.reload();
                } else {
                    showToast(data.message || 'Error updating status', 'error');
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                showToast('Request failed: ' + err.message, 'error');
                document.getElementById('confirmationModal').close();
            })
            .finally(() => {
                pendingAction = null;
            });
        }

        // Checkbox "Select All" (unchanged)
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => cb.checked = this.checked);
            });
        }

        // Export functions (unchanged) ...
        function exportSelected() {
            const selectedRows = [];
            document.querySelectorAll('#employeesTable tbody tr').forEach(row => {
                const cb = row.querySelector('.row-checkbox');
                if (cb && cb.checked) {
                    const details = row.getAttribute('data-details');
                    if (details) {
                        try {
                            selectedRows.push(JSON.parse(details));
                        } catch (e) {
                            console.error('Failed to parse details', e);
                        }
                    }
                }
            });

            if (selectedRows.length === 0) {
                showToast('Please select at least one employee to export.', 'error');
                return;
            }
            generateDetailedPDF(selectedRows);
        }

        function exportSingleEmployee() {
            const container = document.getElementById('employeeDetails');
            if (!container) return;
            const paragraphs = container.querySelectorAll('p');
            const fullName = paragraphs[0]?.textContent?.replace('Full Name:', '').trim() || '';
            const empCode = paragraphs[1]?.textContent?.replace('Employee Code:', '').trim() || '';
            const email = paragraphs[2]?.textContent?.replace('Email:', '').trim() || '';
            const phone = paragraphs[3]?.textContent?.replace('Phone:', '').trim() || '';
            const gender = paragraphs[4]?.textContent?.replace('Gender:', '').trim() || '';
            const dept = paragraphs[5]?.textContent?.replace('Department:', '').trim() || '';
            const position = paragraphs[6]?.textContent?.replace('Position:', '').trim() || '';
            const hireDate = paragraphs[7]?.textContent?.replace('Hire Date:', '').trim() || '';
            const status = paragraphs[8]?.textContent?.replace('Employment Status:', '').trim() || '';
            const notes = document.getElementById('complianceNotesDisplay').textContent;

            generateDetailedPDF([{
                full_name: fullName,
                employee_code: empCode,
                email: email,
                phone_number: phone,
                gender: gender,
                department_name: dept,
                job: position,
                hire_date: hireDate,
                employment_status: status,
                compliance_notes: notes
            }]);
        }

        function generateDetailedPDF(employees) {
            let html = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Employee Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #001f54; }
                    h2 { margin-top: 30px; color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                    table { border-collapse: collapse; width: 100%; margin-top: 10px; margin-bottom: 30px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; width: 30%; }
                    .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
                    .footer { margin-top: 30px; font-size: 0.9em; color: #666; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Employee Status Report</h1>
                    <p>Generated on: ${new Date().toLocaleString()}</p>
                </div>
            `;

            employees.forEach((emp, index) => {
                html += `
                <h2>Employee ${index + 1}: ${emp.full_name || emp.name}</h2>
                <table>
                    <tr><th>Full Name</th><td>${emp.full_name || emp.name || ''}</td></tr>
                    <tr><th>Employee Code</th><td>${emp.employee_code || emp.id || ''}</td></tr>
                    <tr><th>Email</th><td>${emp.email || ''}</td></tr>
                    <tr><th>Phone</th><td>${emp.phone_number || 'N/A'}</td></tr>
                    <tr><th>Gender</th><td>${emp.gender || 'N/A'}</td></tr>
                    <tr><th>Department</th><td>${emp.department_name || emp.dept || ''}</td></tr>
                    <tr><th>Position</th><td>${emp.job || emp.position || ''}</td></tr>
                    <tr><th>Hire Date</th><td>${emp.hire_date || emp.hireDate || ''}</td></tr>
                    <tr><th>Employment Status</th><td>${emp.employment_status || emp.status || ''}</td></tr>
                    <tr><th>Compliance Notes</th><td>${emp.compliance_notes || emp.notes || 'No notes'}</td></tr>
                </table>
                `;
            });

            html += `
                <div class="footer">
                    <p>Report generated by HR System</p>
                </div>
            </body>
            </html>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(html);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }

        // Sub-department filtering based on selected department (visual only) (unchanged)
        document.getElementById('departmentFilter')?.addEventListener('change', function() {
            const deptId = this.value;
            const subSelect = document.getElementById('subDepartmentFilter');
            if (!subSelect) return;
            const options = subSelect.querySelectorAll('option');
            options.forEach(opt => {
                if (opt.value === '') return;
                if (deptId === '' || opt.getAttribute('data-dept') === deptId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
            if (subSelect.value && !Array.from(options).some(o => o.value === subSelect.value && o.style.display !== 'none')) {
                subSelect.value = '';
            }
        });

        window.addEventListener('load', function() {
            const event = new Event('change');
            document.getElementById('departmentFilter')?.dispatchEvent(event);
        });
    </script>
</body>
</html>