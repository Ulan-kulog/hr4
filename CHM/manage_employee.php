<?php
session_start();
include("../connection.php");

$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => "Connection not found for $db_name"]));
}
$conn = $connections[$db_name];

// No archive functionality – all employees are shown

// Handle API requests – must exit before any HTML output
if (isset($_GET['action'])) {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);

    header('Content-Type: application/json');
    ob_start();

    $action = $_GET['action'];

    // Fetch distinct department values from the employees table for filters
    if ($action === 'departments') {
        // Get distinct department names from the employees table (direct column)
        $deptQuery = "SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != '' ORDER BY department";
        $deptResult = $conn->query($deptQuery);
        if (!$deptResult) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Department query failed: ' . $conn->error]);
            exit;
        }
        $departments = [];
        while ($row = $deptResult->fetch_assoc()) {
            $departments[] = $row['department'];
        }

        // Sub-departments still come from the sub_departments table via relationship
        $subQuery = "SELECT DISTINCT sd.name AS sub_department FROM employees e 
                     LEFT JOIN sub_departments sd ON e.sub_department_id = sd.id 
                     WHERE sd.name IS NOT NULL ORDER BY sd.name";
        $subResult = $conn->query($subQuery);
        if (!$subResult) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Sub-department query failed: ' . $conn->error]);
            exit;
        }
        $subDepartments = [];
        while ($row = $subResult->fetch_assoc()) {
            $subDepartments[] = $row['sub_department'];
        }

        ob_clean();
        echo json_encode([
            'success' => true,
            'data' => [
                'departments' => $departments,
                'sub_departments' => $subDepartments
            ]
        ]);
        exit;
    }

    // Fetch stats for the four cards
    if ($action === 'get_stats') {
        $totalEmp = $conn->query("SELECT COUNT(*) AS count FROM employees");
        if (!$totalEmp) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Count employees failed: ' . $conn->error]);
            exit;
        }
        $totalEmp = $totalEmp->fetch_assoc()['count'];

        $jobPos = $conn->query("SELECT COUNT(DISTINCT job) AS count FROM employees WHERE job IS NOT NULL AND job != ''");
        if (!$jobPos) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Count job positions failed: ' . $conn->error]);
            exit;
        }
        $jobPos = $jobPos->fetch_assoc()['count'];

        $dept = $conn->query("SELECT COUNT(*) AS count FROM departments");
        if (!$dept) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Count departments failed: ' . $conn->error]);
            exit;
        }
        $dept = $dept->fetch_assoc()['count'];

        $subDept = $conn->query("SELECT COUNT(*) AS count FROM sub_departments");
        if (!$subDept) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Count sub-departments failed: ' . $conn->error]);
            exit;
        }
        $subDept = $subDept->fetch_assoc()['count'];

        ob_clean();
        echo json_encode([
            'success' => true,
            'data' => [
                'total_employees' => $totalEmp,
                'total_job_positions' => $jobPos,
                'total_departments' => $dept,
                'total_sub_departments' => $subDept
            ]
        ]);
        exit;
    }

    // Fetch a single employee by ID for the view modal
    if ($action === 'get_employee') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            exit;
        }

        // Use the direct department column, keep sub-department join
        $stmt = $conn->prepare("
            SELECT e.*, e.department AS department, sd.name AS sub_department 
            FROM employees e
            LEFT JOIN sub_departments sd ON e.sub_department_id = sd.id
            WHERE e.id = ?
        ");
        if (!$stmt) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();

        if ($employee) {
            $employee['full_name'] = trim($employee['first_name'] . ' ' . $employee['middle_name'] . ' ' . $employee['last_name']);
            $employee['hire_date_formatted'] = $employee['hire_date'] ? date('M d, Y', strtotime($employee['hire_date'])) : 'N/A';
            $employee['date_of_birth_formatted'] = $employee['date_of_birth'] ? date('M d, Y', strtotime($employee['date_of_birth'])) : 'N/A';
            ob_clean();
            echo json_encode(['success' => true, 'data' => $employee]);
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Employee not found']);
        }
        exit;
    }

    // Fetch employees (with optional filters and pagination) – default limit 10
    if ($action === 'fetch_employees') {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Fixed to 10
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];
        $types = "";

        // Filters – now use e.department instead of d.name
        if (!empty($_GET['department'])) {
            $where[] = "e.department = ?";
            $params[] = $_GET['department'];
            $types .= "s";
        }
        if (!empty($_GET['sub_department'])) {
            $where[] = "sd.name = ?";
            $params[] = $_GET['sub_department'];
            $types .= "s";
        }
        if (!empty($_GET['employee_code'])) {
            $where[] = "e.employee_code = ?";
            $params[] = $_GET['employee_code'];
            $types .= "s";
        }
        if (!empty($_GET['employment_status'])) {
            $where[] = "e.employment_status = ?";
            $params[] = $_GET['employment_status'];
            $types .= "s";
        }
        if (!empty($_GET['search'])) {
            $where[] = "(e.first_name LIKE ? OR e.last_name LIKE ? OR e.email LIKE ?)";
            $searchTerm = "%" . $_GET['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        $whereClause = empty($where) ? "1=1" : implode(" AND ", $where);

        // Select the direct department column, keep sub-department join
        $sql = "SELECT e.*, e.department AS department, sd.name AS sub_department 
                FROM employees e
                LEFT JOIN sub_departments sd ON e.sub_department_id = sd.id
                WHERE $whereClause
                ORDER BY e.id DESC
                LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }

        if (!empty($params)) {
            $types .= "ii";
            $bindParams = [];
            $bindParams[] = $types;
            foreach ($params as $key => $value) {
                $bindParams[] = &$params[$key];
            }
            $bindParams[] = &$limit;
            $bindParams[] = &$offset;
            $stmt->bind_param(...$bindParams);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $employees = [];
        while ($row = $result->fetch_assoc()) {
            $row['full_name'] = trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
            $row['hire_date_formatted'] = $row['hire_date'] ? date('M d, Y', strtotime($row['hire_date'])) : 'N/A';
            $employees[] = $row;
        }

        // Count query (same conditions)
        $countSql = "SELECT COUNT(*) as total FROM employees e 
                     LEFT JOIN sub_departments sd ON e.sub_department_id = sd.id
                     WHERE $whereClause";

        $countStmt = $conn->prepare($countSql);
        if (!$countStmt) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Count prepare failed: ' . $conn->error]);
            exit;
        }

        if (!empty($params)) {
            $countTypes = substr($types, 0, -2);
            $countBindParams = [];
            $countBindParams[] = $countTypes;
            foreach ($params as $key => $value) {
                $countBindParams[] = &$params[$key];
            }
            $countStmt->bind_param(...$countBindParams);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalRecords = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRecords / $limit);

        ob_clean();
        echo json_encode([
            'success' => true,
            'data' => $employees,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'limit' => $limit,
                'offset' => $offset
            ]
        ]);
        exit;
    }

    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// If no action, continue to HTML output
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees - Core Human Capital</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .avatar-placeholder {
            width: 32px;
            height: 32px;
            background-color: #3b82f6;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .pagination-btn {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: white;
            color: #374151;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .pagination-btn.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .pagination-btn:hover:not(:disabled) {
            background-color: #f3f4f6;
        }

        .pagination-btn.active:hover {
            background-color: #2563eb;
        }

        /* Printable template hidden by default */
        #printTemplate, #printSelectedTemplate {
            display: none;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printTemplate, #printTemplate *,
            #printSelectedTemplate, #printSelectedTemplate * {
                visibility: visible;
            }
            #printTemplate, #printSelectedTemplate {
                display: block;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
                padding: 20px;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../INCLUDES/sidebar.php'; ?>

        <!-- Content Area -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Navbar -->
            <?php include '../INCLUDES/navbar.php'; ?>

            <!-- Main Content -->
            <div class="flex-1 p-6 overflow-auto">
                <!-- Header Section -->
                <div class="mb-6">
                    <h1 class="flex items-center gap-2 font-bold text-gray-800 text-2xl">
                        <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                        Employee Management
                    </h1>
                    <p class="mt-1 text-gray-600">Manage and view all employee information</p>
                </div>

                <!-- Stats Cards (4 cards) – redesigned with navy/yellow theme -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-4 mb-6">
                    <!-- Total Employees -->
                    <div class="bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">TOTAL EMPLOYEE</p>
                                <h3 id="totalEmployees" class="text-2xl font-bold mt-1">0</h3>
                                <p class="text-xs text-gray-500 mt-1">Employees</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="users" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Job Positions -->
                    <div class="bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">TOTAL JOB POSITION</p>
                                <h3 id="totalJobPositions" class="text-2xl font-bold mt-1">0</h3>
                                <p class="text-xs text-gray-500 mt-1">Positions</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="briefcase" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Departments -->
                    <div class="bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">TOTAL DEPARTMENT</p>
                                <h3 id="totalDepartments" class="text-2xl font-bold mt-1">0</h3>
                                <p class="text-xs text-gray-500 mt-1">Departments</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="building-2" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Sub-Departments -->
                    <div class="bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">TOTAL SUB DEPARTMENT</p>
                                <h3 id="totalSubDepartments" class="text-2xl font-bold mt-1">0</h3>
                                <p class="text-xs text-gray-500 mt-1">Sub Depts</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="layers" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compact Filter Section (updated: Employee ID → Employee Code) -->
                <div class="bg-white shadow-sm mb-6 p-3 border border-gray-200 rounded-xl">
                    <!-- First row: main filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Department</label>
                            <select id="departmentFilter" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full">
                                <option value="">All Departments</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Sub-Department</label>
                            <select id="subDepartmentFilter" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full">
                                <option value="">All Sub-Departments</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Employee Code</label>
                            <div class="flex gap-1">
                                <input type="text" id="employeeCodeSearch" placeholder="Enter Code"
                                    class="flex-1 bg-white px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <button id="searchByCodeBtn" class="bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded text-white text-sm transition-colors">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-end gap-1">
                            <button id="clearFilters" class="hover:bg-gray-50 px-3 py-1.5 border border-gray-300 rounded text-sm text-gray-700 transition-colors">
                                Clear
                            </button>
                            <button id="refreshData" class="bg-gray-800 hover:bg-gray-900 px-3 py-1.5 rounded text-sm text-white transition-colors">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Second row: employment status and search -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Employment Status</label>
                            <select id="employmentStatusFilter" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full">
                                <option value="">All Status</option>
                                <option value="probationary">Probationary</option>
                                <option value="regular">Regular</option>
                                <option value="suspended">Suspended</option>
                                <option value="AWOL">AWOL</option>
                                <option value="terminated">Terminated</option>
                                <option value="contractual">Contractual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Search (Name/Email)</label>
                            <input type="text" id="generalSearch" placeholder="Search..."
                                class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full">
                        </div>
                    </div>
                </div>

                <!-- Employee Table with Checkboxes and Export -->
                <div class="bg-white shadow-sm border border-gray-200 rounded-xl">
                    <div class="p-4 border-gray-200 border-b">
                        <div class="flex md:flex-row flex-col justify-between items-start md:items-center gap-4">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Employee List</h3>
                                <p class="text-gray-600 text-sm" id="tableSummary">Showing 0 employees</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button id="exportSelectedBtn" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded text-white text-sm transition-colors flex items-center gap-1">
                                    <i data-lucide="download" class="w-4 h-4"></i> Export CSV
                                </button>
                                <button id="printSelectedBtn" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-white text-sm transition-colors flex items-center gap-1">
                                    <i data-lucide="printer" class="w-4 h-4"></i> Print Selected
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 w-10">
                                        <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300">
                                    </th>
                                    <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Employee Code</th>
                                    <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Full Name</th>
                                    <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Sub-Department</th>
                                    <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Position</th>
                                    <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Hire Date</th>
                                    <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="employeesTableBody" class="divide-y divide-gray-200">
                                <tr id="loadingRow">
                                    <td colspan="9" class="px-4 py-8 text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="border-blue-600 border-b-2 rounded-full w-8 h-8 animate-spin"></div>
                                            <p class="mt-2 text-gray-600">Loading employees...</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination (limit fixed to 10) -->
                    <div class="p-4 border-gray-200 border-t">
                        <div class="flex md:flex-row flex-col justify-between items-center gap-4">
                            <div class="text-gray-600 text-sm" id="paginationInfo">
                                Showing 0 to 0 of 0 entries
                            </div>
                            <div class="flex items-center gap-2" id="paginationControls">
                                <!-- Pagination buttons -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Employee Modal (organized list, salary toggle) -->
    <div id="viewModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-xl">
            <div class="sticky top-0 bg-white p-6 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 text-xl" id="viewModalTitle">Employee Details</h3>
                <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-6" id="viewModalContent">
                <!-- Organized list will be injected here -->
            </div>
            <div class="sticky bottom-0 bg-gray-50 p-4 border-t flex justify-end gap-2">
                <button id="toggleSalaryBtn" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors flex items-center gap-1">
                    <i data-lucide="eye" class="w-4 h-4"></i> Show Salary
                </button>
                <button id="printEmployeeBtn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-1">
                    <i data-lucide="printer" class="w-4 h-4"></i> Print / Export PDF
                </button>
                <button id="closeViewBtn" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">Close</button>
            </div>
        </div>
    </div>

    <!-- Hidden Printable Templates -->
    <div id="printTemplate"></div>
    <div id="printSelectedTemplate"></div>

    <script>
        lucide.createIcons();

        // Global variables
        let currentPage = 1;
        let totalPages = 1;
        let currentFilters = {};
        let totalRecords = 0;
        let currentEmployees = []; // store the currently displayed employees for export
        let showSalary = false; // toggle for salary visibility
        let lastEmployee = null; // store last viewed employee for toggle

        // DOM Elements
        const departmentFilter = document.getElementById('departmentFilter');
        const subDepartmentFilter = document.getElementById('subDepartmentFilter');
        const employeeCodeSearch = document.getElementById('employeeCodeSearch');
        const searchByCodeBtn = document.getElementById('searchByCodeBtn');
        const employmentStatusFilter = document.getElementById('employmentStatusFilter');
        const generalSearch = document.getElementById('generalSearch');
        const clearFilters = document.getElementById('clearFilters');
        const refreshData = document.getElementById('refreshData');
        const employeesTableBody = document.getElementById('employeesTableBody');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const totalEmployees = document.getElementById('totalEmployees');
        const totalJobPositions = document.getElementById('totalJobPositions');
        const totalDepartments = document.getElementById('totalDepartments');
        const totalSubDepartments = document.getElementById('totalSubDepartments');
        const tableSummary = document.getElementById('tableSummary');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const exportSelectedBtn = document.getElementById('exportSelectedBtn');
        const printSelectedBtn = document.getElementById('printSelectedBtn');

        // Modal elements
        const viewModal = document.getElementById('viewModal');
        const viewModalContent = document.getElementById('viewModalContent');
        const closeViewModal = document.getElementById('closeViewModal');
        const closeViewBtn = document.getElementById('closeViewBtn');
        const printEmployeeBtn = document.getElementById('printEmployeeBtn');
        const toggleSalaryBtn = document.getElementById('toggleSalaryBtn');
        const printTemplate = document.getElementById('printTemplate');
        const printSelectedTemplate = document.getElementById('printSelectedTemplate');

        // Load stats
        async function loadStats() {
            try {
                const response = await fetch('?action=get_stats');
                const data = await response.json();
                if (data.success) {
                    totalEmployees.textContent = data.data.total_employees;
                    totalJobPositions.textContent = data.data.total_job_positions;
                    totalDepartments.textContent = data.data.total_departments;
                    totalSubDepartments.textContent = data.data.total_sub_departments;
                } else {
                    console.error('Stats error:', data.message);
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Load departments (from direct department column)
        async function loadDepartments() {
            try {
                const response = await fetch('?action=departments');
                const data = await response.json();
                if (data.success) {
                    departmentFilter.innerHTML = '<option value="">All Departments</option>';
                    data.data.departments.forEach(dept => {
                        if (dept) {
                            const option = document.createElement('option');
                            option.value = dept;
                            option.textContent = dept;
                            departmentFilter.appendChild(option);
                        }
                    });
                    subDepartmentFilter.innerHTML = '<option value="">All Sub-Departments</option>';
                    data.data.sub_departments.forEach(subDept => {
                        if (subDept) {
                            const option = document.createElement('option');
                            option.value = subDept;
                            option.textContent = subDept;
                            subDepartmentFilter.appendChild(option);
                        }
                    });
                } else {
                    console.error('Departments error:', data.message);
                }
            } catch (error) {
                console.error('Error loading departments:', error);
            }
        }

        // Load employees
        async function loadEmployees(page = 1) {
            currentPage = page;
            employeesTableBody.innerHTML = `
                <tr id="loadingRow">
                    <td colspan="9" class="px-4 py-8 text-center">
                        <div class="flex flex-col justify-center items-center">
                            <div class="border-blue-600 border-b-2 rounded-full w-8 h-8 animate-spin"></div>
                            <p class="mt-2 text-gray-600">Loading employees...</p>
                        </div>
                    </td>
                </tr>
            `;

            const params = new URLSearchParams({
                action: 'fetch_employees',
                page: currentPage
                // limit is fixed to 10 on server
            });

            if (currentFilters.department) params.append('department', currentFilters.department);
            if (currentFilters.sub_department) params.append('sub_department', currentFilters.sub_department);
            if (currentFilters.employee_code) params.append('employee_code', currentFilters.employee_code);
            if (currentFilters.employment_status) params.append('employment_status', currentFilters.employment_status);
            if (currentFilters.search) params.append('search', currentFilters.search);

            try {
                const response = await fetch(`?${params.toString()}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                if (data.success) {
                    currentEmployees = data.data; // store for export
                    renderEmployeesTable(data.data);
                    updatePagination(data.pagination);
                    const start = data.pagination.offset + 1;
                    const end = Math.min(data.pagination.offset + data.pagination.limit, data.pagination.total_records);
                    tableSummary.textContent = `Showing ${start} - ${end} of ${data.pagination.total_records} employees`;
                    totalRecords = data.pagination.total_records;
                } else {
                    showError('Failed to load employees: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error loading employees:', error);
                showError('Failed to load employees. Please check your connection. ' + error.message);
            }
        }

        // Badge class
        function getEmploymentBadgeClass(status) {
            if (!status) return 'bg-gray-100 text-gray-800';
            const s = status.toLowerCase();
            switch (s) {
                case 'probationary': return 'bg-blue-100 text-blue-800';
                case 'regular': return 'bg-green-100 text-green-800';
                case 'suspended': return 'bg-orange-100 text-orange-800';
                case 'awol': return 'bg-red-50 text-red-800';
                case 'terminated': return 'bg-red-100 text-red-800';
                case 'contractual': return 'bg-purple-100 text-purple-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        // Render table with checkboxes
        function renderEmployeesTable(employees) {
            if (employees.length === 0) {
                employeesTableBody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center">
                            <div class="flex flex-col justify-center items-center">
                                <i data-lucide="users" class="mb-2 w-12 h-12 text-gray-300"></i>
                                <p class="text-gray-600">No employees found</p>
                                <p class="mt-1 text-gray-500 text-sm">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                `;
                lucide.createIcons();
                return;
            }

            let tableHTML = '';
            employees.forEach(employee => {
                let statusText = employee.employment_status ? 
                    employee.employment_status.charAt(0).toUpperCase() + employee.employment_status.slice(1) : 'Unknown';
                if (employee.employment_status && employee.employment_status.toLowerCase() === 'awol') {
                    statusText = 'AWOL';
                }
                const statusClass = getEmploymentBadgeClass(employee.employment_status);
                const initials = `${employee.first_name ? employee.first_name.charAt(0) : ''}${employee.last_name ? employee.last_name.charAt(0) : ''}`;
                const hireDateDisplay = employee.hire_date_formatted || 'N/A';
                const employeeCode = employee.employee_code || 'N/A';

                tableHTML += `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="row-checkbox rounded border-gray-300" value="${employee.id}">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="font-medium text-gray-900">${employeeCode}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="avatar-placeholder">${initials || '?'}</div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">${employee.full_name || 'N/A'}</div>
                                    <div class="text-gray-500 text-sm">${employee.email || 'No email'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3"><span class="text-gray-900">${employee.department || 'N/A'}</span></td>
                        <td class="px-4 py-3"><span class="text-gray-900">${employee.sub_department || 'N/A'}</span></td>
                        <td class="px-4 py-3"><span class="text-gray-900">${employee.job || 'N/A'}</span></td>
                        <td class="px-4 py-3"><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="text-gray-900">${hireDateDisplay}</span></td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button onclick="viewEmployee('${employee.id}')" 
                                        class="p-1 text-blue-600 hover:text-blue-800 transition-colors"
                                        title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            employeesTableBody.innerHTML = tableHTML;
            lucide.createIcons();

            // Re-attach select all functionality
            setupSelectAll();
        }

        // Handle select all checkbox
        function setupSelectAll() {
            selectAllCheckbox.addEventListener('change', function(e) {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(cb => cb.checked = e.target.checked);
            });

            // Update select all when individual checkboxes change
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = document.querySelectorAll('.row-checkbox:checked').length === document.querySelectorAll('.row-checkbox').length;
                    selectAllCheckbox.checked = allChecked;
                });
            });
        }

        // Export selected as CSV
        function exportSelected() {
            const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                alert('Please select at least one employee to export.');
                return;
            }
            const selectedEmployees = currentEmployees.filter(emp => selectedIds.includes(emp.id.toString()));
            if (selectedEmployees.length === 0) return;

            const headers = ['Employee Code', 'Full Name', 'Email', 'Department', 'Sub-Department', 'Position', 'Status', 'Hire Date'];
            const rows = selectedEmployees.map(emp => [
                emp.employee_code || '',
                emp.full_name || '',
                emp.email || '',
                emp.department || '',
                emp.sub_department || '',
                emp.job || '',
                emp.employment_status || '',
                emp.hire_date_formatted || ''
            ]);

            let csvContent = headers.join(',') + '\n' + rows.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'selected_employees.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Print selected employees
        function printSelected() {
            const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                alert('Please select at least one employee to print.');
                return;
            }
            const selectedEmployees = currentEmployees.filter(emp => selectedIds.includes(emp.id.toString()));
            if (selectedEmployees.length === 0) return;

            let template = `
                <div style="font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px;">
                    <h1 style="color: #001f54; border-bottom: 2px solid #F7B32B; padding-bottom: 10px;">Selected Employees Report</h1>
                    <p style="margin-bottom: 20px;">Generated on ${new Date().toLocaleDateString()}</p>
            `;

            selectedEmployees.forEach(emp => {
                template += `
                    <div style="margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                        <h2 style="color: #001f54; margin-top: 0;">${emp.full_name} (${emp.employee_code || 'N/A'})</h2>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr><td style="padding: 5px; font-weight: bold;">Department:</td><td>${emp.department || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Sub-Department:</td><td>${emp.sub_department || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Position:</td><td>${emp.job || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Employment Status:</td><td>${emp.employment_status || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Work Status:</td><td>${emp.work_status || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Hire Date:</td><td>${emp.hire_date_formatted}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Date of Birth:</td><td>${emp.date_of_birth_formatted || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Email:</td><td>${emp.email || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Phone:</td><td>${emp.phone_number || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Gender:</td><td>${emp.gender || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Address:</td><td>${emp.address || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Emergency Contact:</td><td>${emp.emergency_contact_name ? `${emp.emergency_contact_name} (${emp.emergency_contact_relationship}) - ${emp.emergency_contact_number}` : 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Mentors:</td><td>${emp.mentors || 'N/A'}</td></tr>
                            <tr><td style="padding: 5px; font-weight: bold;">Salary:</td><td>${emp.salary ? '₱' + Number(emp.salary).toLocaleString() : 'N/A'}</td></tr>
                        </table>
                    </div>
                `;
            });

            template += '</div>';
            printSelectedTemplate.innerHTML = template;
            window.print();
        }

        exportSelectedBtn.addEventListener('click', exportSelected);
        printSelectedBtn.addEventListener('click', printSelected);

        // Render employee modal (with salary toggle)
        function renderEmployeeModal(emp) {
            const salaryDisplay = showSalary ? (emp.salary ? '₱' + Number(emp.salary).toLocaleString() : 'N/A') : '•••••';
            const toggleIcon = showSalary ? 'eye-off' : 'eye';
            toggleSalaryBtn.innerHTML = `<i data-lucide="${toggleIcon}" class="w-4 h-4"></i> ${showSalary ? 'Hide Salary' : 'Show Salary'}`;

            const content = `
                <div class="space-y-6">
                    <!-- Personal Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-[#001f54] border-b border-[#F7B32B] pb-2 mb-3">Personal Information</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Employee Code:</dt><dd class="flex-1 text-gray-900">${emp.employee_code || 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Full Name:</dt><dd class="flex-1 text-gray-900">${emp.full_name}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Gender:</dt><dd class="flex-1 text-gray-900">${emp.gender || 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Date of Birth:</dt><dd class="flex-1 text-gray-900">${emp.date_of_birth_formatted || 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Email:</dt><dd class="flex-1 text-gray-900">${emp.email || 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Phone:</dt><dd class="flex-1 text-gray-900">${emp.phone_number || 'N/A'}</dd></div>
                            <div class="flex md:col-span-2"><dt class="w-32 font-medium text-gray-600">Address:</dt><dd class="flex-1 text-gray-900">${emp.address || 'N/A'}</dd></div>
                        </dl>
                    </div>

                    <!-- Employment Details -->
                    <div>
                        <h4 class="text-lg font-semibold text-[#001f54] border-b border-[#F7B32B] pb-2 mb-3">Employment Details</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Department:</dt><dd class="flex-1 text-gray-900">${emp.department || 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Sub-Department:</dt><dd class="flex-1 text-gray-900">${emp.sub_department || 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Job Position:</dt><dd class="flex-1 text-gray-900">${emp.job || 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Employment Status:</dt><dd class="flex-1 text-gray-900">${emp.employment_status ? (emp.employment_status.toLowerCase() === 'awol' ? 'AWOL' : emp.employment_status.charAt(0).toUpperCase() + emp.employment_status.slice(1)) : 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Work Status:</dt><dd class="flex-1 text-gray-900">${emp.work_status || 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Hire Date:</dt><dd class="flex-1 text-gray-900">${emp.hire_date_formatted}</dd></div>
                        </dl>
                    </div>

                    <!-- Other Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-[#001f54] border-b border-[#F7B32B] pb-2 mb-3">Other Information</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Mentors:</dt><dd class="flex-1 text-gray-900">${emp.mentors || 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Emergency Contact:</dt><dd class="flex-1 text-gray-900">${emp.emergency_contact_name ? `${emp.emergency_contact_name} (${emp.emergency_contact_relationship}) - ${emp.emergency_contact_number}` : 'N/A'}</dd></div>
                            <div class="flex"><dt class="w-32 font-medium text-gray-600">Salary:</dt><dd class="flex-1 text-gray-900 salary-value">${salaryDisplay}</dd></div>
                        </dl>
                    </div>
                </div>
            `;
            viewModalContent.innerHTML = content;
            lucide.createIcons(); // ensure icons in the new content are rendered
        }

        // View employee details (single source of truth)
        async function viewEmployee(id) {
            try {
                const response = await fetch(`?action=get_employee&id=${id}`);
                const data = await response.json();
                if (data.success) {
                    lastEmployee = data.data;
                    renderEmployeeModal(lastEmployee);
                    viewModal.classList.remove('hidden');
                    lucide.createIcons();

                    // Set print button for this employee
                    printEmployeeBtn.onclick = () => printEmployee(lastEmployee);
                } else {
                    alert('Failed to load employee details: ' + data.message);
                }
            } catch (error) {
                console.error('Error fetching employee:', error);
                alert('Error loading employee details');
            }
        }

        // Print single employee
        function printEmployee(emp) {
            const template = `
                <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
                    <h1 style="color: #001f54; border-bottom: 2px solid #F7B32B; padding-bottom: 10px;">Employee Details</h1>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <tr><td style="padding: 8px; font-weight: bold;">Employee Code:</td><td>${emp.employee_code || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Full Name:</td><td>${emp.full_name}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Department:</td><td>${emp.department || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Sub-Department:</td><td>${emp.sub_department || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Job Position:</td><td>${emp.job || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Employment Status:</td><td>${emp.employment_status || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Work Status:</td><td>${emp.work_status || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Hire Date:</td><td>${emp.hire_date_formatted}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Date of Birth:</td><td>${emp.date_of_birth_formatted || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Email:</td><td>${emp.email || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Phone:</td><td>${emp.phone_number || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Gender:</td><td>${emp.gender || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Address:</td><td>${emp.address || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Emergency Contact:</td><td>${emp.emergency_contact_name ? `${emp.emergency_contact_name} (${emp.emergency_contact_relationship}) - ${emp.emergency_contact_number}` : 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Mentors:</td><td>${emp.mentors || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Salary:</td><td>${emp.salary ? '₱' + Number(emp.salary).toLocaleString() : 'N/A'}</td></tr>
                    </table>
                </div>
            `;
            printTemplate.innerHTML = template;
            window.print();
        }

        // Toggle salary visibility
        toggleSalaryBtn.addEventListener('click', () => {
            if (!lastEmployee) return;
            showSalary = !showSalary;
            renderEmployeeModal(lastEmployee);
            lucide.createIcons(); // update the toggle button icon
        });

        // Close modal
        closeViewModal.addEventListener('click', () => {
            viewModal.classList.add('hidden');
            lastEmployee = null;
        });
        closeViewBtn.addEventListener('click', () => {
            viewModal.classList.add('hidden');
            lastEmployee = null;
        });

        // Pagination
        function updatePagination(pagination) {
            totalPages = pagination.total_pages;
            currentPage = pagination.current_page;

            const start = pagination.offset + 1;
            const end = Math.min(pagination.offset + pagination.limit, pagination.total_records);
            paginationInfo.textContent = `Showing ${start} to ${end} of ${pagination.total_records} entries`;

            let paginationHTML = '';

            paginationHTML += `
                <button onclick="changePage(${currentPage - 1})" 
                        ${currentPage === 1 ? 'disabled' : ''}
                        class="pagination-btn ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'}">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
            `;

            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationHTML += `
                    <button onclick="changePage(${i})" 
                            class="pagination-btn ${i === currentPage ? 'active' : ''}">
                        ${i}
                    </button>
                `;
            }

            paginationHTML += `
                <button onclick="changePage(${currentPage + 1})" 
                        ${currentPage === totalPages ? 'disabled' : ''}
                        class="pagination-btn ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'}">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            `;

            paginationControls.innerHTML = paginationHTML;
            lucide.createIcons();
        }

        function changePage(page) {
            if (page < 1 || page > totalPages || page === currentPage) return;
            loadEmployees(page);
        }

        // Update filters
        function updateFilters() {
            currentFilters = {
                department: departmentFilter.value || null,
                sub_department: subDepartmentFilter.value || null,
                employee_code: employeeCodeSearch.value || null,
                employment_status: employmentStatusFilter.value || null,
                search: generalSearch.value || null
            };
            currentPage = 1;
            loadEmployees(1);
        }

        function showError(message) {
            employeesTableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center">
                        <div class="flex flex-col justify-center items-center">
                            <i data-lucide="alert-circle" class="mb-2 w-12 h-12 text-red-300"></i>
                            <p class="text-red-600">${message}</p>
                        </div>
                    </td>
                </tr>
            `;
            lucide.createIcons();
        }

        // Event listeners
        departmentFilter.addEventListener('change', updateFilters);
        subDepartmentFilter.addEventListener('change', updateFilters);
        employmentStatusFilter.addEventListener('change', updateFilters);

        searchByCodeBtn.addEventListener('click', () => {
            if (employeeCodeSearch.value.trim()) updateFilters();
        });

        employeeCodeSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') updateFilters();
        });

        generalSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') updateFilters();
        });

        let searchTimeout;
        generalSearch.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(updateFilters, 500);
        });

        clearFilters.addEventListener('click', () => {
            departmentFilter.value = '';
            subDepartmentFilter.value = '';
            employeeCodeSearch.value = '';
            employmentStatusFilter.value = '';
            generalSearch.value = '';
            currentFilters = {};
            loadEmployees(1);
        });

        refreshData.addEventListener('click', () => {
            loadEmployees(currentPage);
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadStats();
            loadDepartments();
            loadEmployees(1);
        });
    </script>
</body>

</html>