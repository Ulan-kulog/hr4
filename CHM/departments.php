<?php
session_start();
include("../connection.php");

$db_name = "hr4_hr_4";
$conn = $connections[$db_name] ?? die("❌ Connection not found for $db_name");

// --- Helper function to assign icon/color based on department name ---
function getDepartmentStyle($deptName) {
    $name = strtolower($deptName);
    $icon = 'users';
    $colorClass = 'bg-pink-100 text-pink-600';
    if (strpos($name, 'logistic') !== false) {
        $icon = 'truck';
        $colorClass = 'bg-orange-100 text-orange-600';
    } elseif (strpos($name, 'administrative') !== false) {
        $icon = 'clipboard-list';
        $colorClass = 'bg-purple-100 text-purple-600';
    } elseif (strpos($name, 'financial') !== false) {
        $icon = 'dollar-sign';
        $colorClass = 'bg-green-100 text-green-600';
    } elseif (strpos($name, 'hotel') !== false) {
        $icon = 'home';
        $colorClass = 'bg-blue-100 text-blue-600';
    } elseif (strpos($name, 'restaurant') !== false) {
        $icon = 'utensils';
        $colorClass = 'bg-red-100 text-red-600';
    } elseif (strpos($name, 'human resource') !== false || strpos($name, 'hr') !== false) {
        $icon = 'users';
        $colorClass = 'bg-pink-100 text-pink-600';
    }
    return ['icon' => $icon, 'colorClass' => $colorClass];
}

// --- Handle API Requests ---
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // --- Get distinct departments with employee counts ---
    if ($action === 'get_departments') {
        header('Content-Type: application/json');
        $query = "SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != '' ORDER BY department";
        $result = $conn->query($query);
        $departments = [];
        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $deptName = $row['department'];
            $countQuery = "SELECT COUNT(*) as total FROM employees WHERE department = ?";
            $countStmt = $conn->prepare($countQuery);
            $countStmt->bind_param("s", $deptName);
            $countStmt->execute();
            $countRes = $countStmt->get_result();
            $count = $countRes->fetch_assoc()['total'];

            $style = getDepartmentStyle($deptName);
            $departments[] = [
                'name' => $deptName,
                'employee_count' => $count,
                'icon' => $style['icon'],
                'colorClass' => $style['colorClass'],
                'description' => ''
            ];
            $stats[] = [
                'name' => $deptName,
                'count' => $count
            ];
        }
        echo json_encode(['departments' => $departments, 'stats' => $stats]);
        exit;
    }

    // --- Get paginated employees for a department (by name) ---
    if ($action === 'get_employees') {
        header('Content-Type: application/json');
        $deptName = isset($_GET['department']) ? trim($_GET['department']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $offset = ($page - 1) * $limit;

        if (empty($deptName)) {
            echo json_encode(['error' => 'Department name required']);
            exit;
        }

        $search_condition = '';
        $params = [];
        $types = '';

        if (!empty($search)) {
            $search_condition = "AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR employee_code LIKE ?)";
            $search_term = "%$search%";
            $params = [$search_term, $search_term, $search_term, $search_term];
            $types = 'ssss';
        }

        $count_query = "SELECT COUNT(*) as total FROM employees WHERE department = ? $search_condition";
        $count_stmt = $conn->prepare($count_query);
        $count_params = array_merge([$deptName], $params);
        $count_types = "s" . $types;
        $count_stmt->bind_param($count_types, ...$count_params);
        $count_stmt->execute();
        $total = $count_stmt->get_result()->fetch_assoc()['total'];

        $query = "SELECT id, employee_code, first_name, middle_name, last_name, 
                         job, email, hire_date, employment_status
                  FROM employees
                  WHERE department = ? $search_condition
                  ORDER BY last_name, first_name
                  LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($query);
        $fetch_params = array_merge([$deptName], $params, [$limit, $offset]);
        $fetch_types = "s" . $types . "ii";
        $stmt->bind_param($fetch_types, ...$fetch_params);
        $stmt->execute();
        $result = $stmt->get_result();

        $employees = [];
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }

        echo json_encode([
            'employees' => $employees,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
        exit;
    }

    // --- Export department as printable HTML ---
    if ($action === 'export') {
        $deptName = isset($_GET['department']) ? trim($_GET['department']) : '';
        if (empty($deptName)) {
            die("Invalid department");
        }

        $stmt = $conn->prepare("SELECT employee_code, first_name, middle_name, last_name, job, email, hire_date, employment_status
                                FROM employees WHERE department = ? ORDER BY last_name, first_name");
        $stmt->bind_param("s", $deptName);
        $stmt->execute();
        $employees = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $total = count($employees);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($deptName); ?> Employees Export</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7fa; margin: 20px; color: #333; }
                .export-container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); padding: 30px; }
                .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #001f54; padding-bottom: 15px; margin-bottom: 25px; }
                .header h1 { color: #001f54; font-size: 28px; font-weight: 600; }
                .header p { color: #666; font-size: 14px; }
                .department-info { background: #f0f4f8; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; display: flex; gap: 30px; flex-wrap: wrap; }
                .info-item { display: flex; flex-direction: column; }
                .info-label { font-size: 12px; color: #666; text-transform: uppercase; }
                .info-value { font-size: 24px; font-weight: 700; color: #001f54; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
                th { background-color: #001f54; color: white; padding: 12px 10px; text-align: left; font-weight: 500; }
                td { padding: 10px; border-bottom: 1px solid #e0e0e0; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                tr:hover { background-color: #f1f5f9; }
                .status-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; text-transform: capitalize; }
                .status-regular { background: #e3fcef; color: #0e6245; }
                .status-probationary { background: #fff3cd; color: #856404; }
                .status-for_compliance { background: #d1ecf1; color: #0c5460; }
                .status-suspended, .status-terminated { background: #f8d7da; color: #721c24; }
                .footer { margin-top: 30px; font-size: 12px; color: #888; text-align: center; border-top: 1px solid #ddd; padding-top: 20px; }
                .no-print { margin-bottom: 20px; display: flex; gap: 10px; }
                .btn { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: 0.2s; }
                .btn-print { background: #001f54; color: white; }
                .btn-print:hover { background: #002b70; }
                .btn-close { background: #6c757d; color: white; }
                .btn-close:hover { background: #5a6268; }
                @media print { .no-print { display: none; } .export-container { box-shadow: none; padding: 10px; } th { background-color: #001f54 !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
            </style>
        </head>
        <body>
            <div class="export-container">
                <div class="no-print">
                    <button class="btn btn-print" onclick="window.print()">🖨️ Print</button>
                    <button class="btn btn-close" onclick="window.close()">✖️ Close</button>
                </div>
                <div class="header">
                    <h1><?php echo htmlspecialchars($deptName); ?> Department</h1>
                    <p>Generated: <?php echo date('F j, Y, g:i a'); ?></p>
                </div>
                <div class="department-info">
                    <div class="info-item"><span class="info-label">Total Employees</span><span class="info-value"><?php echo $total; ?></span></div>
                    <div class="info-item"><span class="info-label">Export Date</span><span class="info-value"><?php echo date('Y-m-d'); ?></span></div>
                </div>
                <?php if ($total > 0): ?>
                <table>
                    <thead><tr><th>#</th><th>Employee Code</th><th>Full Name</th><th>Position</th><th>Email</th><th>Hire Date</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php $counter = 1; foreach ($employees as $emp): 
                            $fullName = trim($emp['first_name'] . ' ' . ($emp['middle_name'] ?? '') . ' ' . $emp['last_name']);
                            $status = $emp['employment_status'] ?? 'N/A';
                            $statusClass = 'status-';
                            if ($status === 'regular') $statusClass .= 'regular';
                            elseif ($status === 'probationary') $statusClass .= 'probationary';
                            elseif ($status === 'for_compliance') $statusClass .= 'for_compliance';
                            elseif (in_array($status, ['suspended', 'terminated'])) $statusClass .= 'suspended';
                            else $statusClass = '';
                        ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><strong><?php echo htmlspecialchars($emp['employee_code'] ?? ''); ?></strong></td>
                            <td><?php echo htmlspecialchars($fullName); ?></td>
                            <td><?php echo htmlspecialchars($emp['job'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($emp['email'] ?? 'N/A'); ?></td>
                            <td><?php echo $emp['hire_date'] ? date('M d, Y', strtotime($emp['hire_date'])) : 'N/A'; ?></td>
                            <td><?php if ($statusClass): ?><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span><?php else: echo htmlspecialchars($status); endif; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p style="text-align: center; padding: 40px; color: #999;">No employees found in this department.</p>
                <?php endif; ?>
                <div class="footer"><p>Generated by Core Human Capital System | Confidential</p></div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// --- If no action, serve the HTML UI ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments - Core Human Capital</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal-box {
            max-width: 90vw;
            width: 90%;
            background-color: white !important;
        }
        @media (min-width: 768px) {
            .modal-box {
                max-width: 80vw;
            }
        }
        /* Modal table styling to match export */
        .modal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .modal-table th {
            background-color: #001f54;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: 500;
        }
        .modal-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        .modal-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .modal-table tbody tr:hover {
            background-color: #f1f5f9;
        }
        .status-badge-modal {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-regular { background: #e3fcef; color: #0e6245; }
        .status-probationary { background: #fff3cd; color: #856404; }
        .status-for_compliance { background: #d1ecf1; color: #0c5460; }
        .status-suspended, .status-terminated { background: #f8d7da; color: #721c24; }
        .dept-summary {
            background: #f0f4f8;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 30px;
        }
        .summary-item {
            display: flex;
            flex-direction: column;
        }
        .summary-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 20px;
            font-weight: 700;
            color: #001f54;
        }
    </style>
</head>
<body class="bg-base-100 min-h-screen bg-white">
  <div class="flex h-screen">
    <?php include '../INCLUDES/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-auto">
        <?php include '../INCLUDES/navbar.php'; ?>
        <main class="flex-1 p-6">
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-green-100/50 text-green-600">
                            <i data-lucide="building" class="w-5 h-5"></i>
                        </span>
                        Manage Departments
                    </h2>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-3 gap-4 mb-8" id="stats-container"></div>

                <!-- Department Management Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Department List</h3>
                        <div class="flex gap-2 mt-2 sm:mt-0">
                            <input type="text" id="searchDepartments" placeholder="Search departments..." class="bg-white px-3 py-2 border border-gray-200 rounded-lg text-sm w-64">
                            <button id="filterBtn" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50"><i data-lucide="filter" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                    <div id="department-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                    <div id="loading" class="text-center py-8 hidden">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                        <p class="mt-2 text-gray-600">Loading departments...</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Employee List Modal (Organized like export template) -->
    <dialog id="employeeModal" class="modal">
        <div class="modal-box max-w-6xl w-11/12 bg-white p-6">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-2xl font-bold text-[#001f54] mb-2" id="modalDepartmentName">Employees</h3>
            
            <!-- Department Summary -->
            <div class="dept-summary" id="deptSummary">
                <div class="summary-item">
                    <span class="summary-label">Total Employees</span>
                    <span class="summary-value" id="totalEmployees">0</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Viewing</span>
                    <span class="summary-value" id="viewingRange">0-0</span>
                </div>
            </div>
            
            <!-- Export and Search Bar -->
            <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
                <button id="exportBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1">
                    <i data-lucide="printer" class="w-4 h-4"></i> Export / Print
                </button>
                <input type="text" id="modalSearch" placeholder="Search employees..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-64">
            </div>
            
            <!-- Employee Table (styled like export) -->
            <div class="overflow-x-auto">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee Code</th>
                            <th>Full Name</th>
                            <th>Position</th>
                            <th>Email</th>
                            <th>Hire Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="modalEmployeeBody">
                        <tr><td colspan="7" class="text-center py-8">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div id="modalPagination" class="flex justify-between items-center mt-4"></div>
            
            <div class="modal-action">
                <button class="btn" onclick="document.getElementById('employeeModal').close()">Close</button>
            </div>
        </div>
    </dialog>

    <script>
        lucide.createIcons();

        let departments = [];
        let currentDepartmentName = '';
        let currentPage = 1;
        let totalPages = 1;
        let modalSearchTerm = '';
        let currentTotal = 0;
        let currentLimit = 15;

        function loadDepartments() {
            document.getElementById('loading').classList.remove('hidden');
            fetch('departments.php?action=get_departments')
                .then(res => res.json())
                .then(data => {
                    departments = data.departments;
                    renderStatsCards(data.stats);
                    renderDepartmentCards(departments);
                    document.getElementById('loading').classList.add('hidden');
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('loading').classList.add('hidden');
                });
        }

        function renderStatsCards(stats) {
            const container = document.getElementById('stats-container');
            if (!stats || stats.length === 0) {
                container.innerHTML = '<p class="col-span-full text-center text-gray-500">No departments found</p>';
                return;
            }
            let html = '';
            stats.forEach(dept => {
                html += `
                    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">${dept.name}</p>
                                <h3 class="text-2xl font-bold mt-1">${dept.count}</h3>
                                <p class="text-xs text-gray-500 mt-1">Employees</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="users" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
            lucide.createIcons();
        }

        function renderDepartmentCards(depts) {
            const grid = document.getElementById('department-grid');
            if (depts.length === 0) {
                grid.innerHTML = '<p class="col-span-full text-center text-gray-500">No departments found</p>';
                return;
            }
            let html = '';
            depts.forEach(dept => {
                html += `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-800">${dept.name}</h3>
                            <span class="p-2 rounded-lg ${dept.colorClass}"><i data-lucide="${dept.icon}" class="w-4 h-4"></i></span>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">${dept.description || ''}</p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-lg font-bold text-gray-800">${dept.employee_count} Employees</span>
                            <span class="text-sm text-green-600 font-medium">Active</span>
                        </div>
                        <div class="flex gap-2">
                            <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1" onclick="openEmployeeModal('${dept.name}')">
                                <i data-lucide="eye" class="w-4 h-4"></i> View
                            </button>
                            <button class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1" onclick="exportDepartment('${dept.name}')">
                                <i data-lucide="download" class="w-4 h-4"></i> Export
                            </button>
                        </div>
                    </div>
                `;
            });
            grid.innerHTML = html;
            lucide.createIcons();
        }

        function openEmployeeModal(deptName) {
            currentDepartmentName = deptName;
            currentPage = 1;
            modalSearchTerm = '';
            document.getElementById('modalDepartmentName').textContent = deptName + ' Department';
            document.getElementById('modalSearch').value = '';
            loadDepartmentEmployees(deptName, 1, '');
            document.getElementById('employeeModal').showModal();
        }

        function loadDepartmentEmployees(deptName, page, search) {
            const tbody = document.getElementById('modalEmployeeBody');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8">Loading...</td></tr>';
            
            const params = new URLSearchParams({
                action: 'get_employees',
                department: deptName,
                page: page,
                limit: 15,
                search: search
            });
            
            fetch(`departments.php?${params}`)
                .then(res => res.json())
                .then(data => {
                    currentTotal = data.total;
                    renderEmployeeTable(data.employees, page, data.limit);
                    renderModalPagination(data.total, data.page, data.limit);
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-error">Error loading employees</td></tr>';
                });
        }

        function renderEmployeeTable(employees, page, limit) {
            const tbody = document.getElementById('modalEmployeeBody');
            if (!employees || employees.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8">No employees found</td></tr>';
                document.getElementById('totalEmployees').textContent = currentTotal;
                document.getElementById('viewingRange').textContent = '0-0';
                return;
            }

            const start = (page - 1) * limit + 1;
            const end = Math.min(page * limit, currentTotal);
            document.getElementById('totalEmployees').textContent = currentTotal;
            document.getElementById('viewingRange').textContent = `${start}-${end}`;

            let html = '';
            let counter = start;
            employees.forEach(emp => {
                const fullName = `${emp.first_name} ${emp.last_name}`;
                const status = emp.employment_status || 'N/A';
                let statusClass = '';
                if (status === 'regular') statusClass = 'status-regular';
                else if (status === 'probationary') statusClass = 'status-probationary';
                else if (status === 'for_compliance') statusClass = 'status-for_compliance';
                else if (status === 'suspended' || status === 'terminated') statusClass = 'status-suspended';
                
                html += `
                    <tr>
                        <td>${counter++}</td>
                        <td><strong>${emp.employee_code || emp.id}</strong></td>
                        <td>${fullName}</td>
                        <td>${emp.job || 'N/A'}</td>
                        <td>${emp.email || 'N/A'}</td>
                        <td>${emp.hire_date ? new Date(emp.hire_date).toLocaleDateString() : 'N/A'}</td>
                        <td>${statusClass ? `<span class="status-badge-modal ${statusClass}">${status}</span>` : status}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function renderModalPagination(total, currentPage, limit) {
            const container = document.getElementById('modalPagination');
            const totalPages = Math.ceil(total / limit);
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let html = `<div class="text-sm">Page ${currentPage} of ${totalPages}</div><div class="join">`;
            html += `<button class="btn btn-outline join-item btn-sm ${currentPage <= 1 ? 'btn-disabled' : ''}" onclick="changeModalPage(${currentPage-1})"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>`;
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage-2 && i <= currentPage+2)) {
                    html += `<button class="btn join-item btn-sm ${currentPage === i ? 'btn-active' : 'btn-outline'}" onclick="changeModalPage(${i})">${i}</button>`;
                } else if (i === currentPage-3 || i === currentPage+3) {
                    html += `<button class="btn join-item btn-sm btn-disabled">...</button>`;
                }
            }
            html += `<button class="btn btn-outline join-item btn-sm ${currentPage >= totalPages ? 'btn-disabled' : ''}" onclick="changeModalPage(${currentPage+1})"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>`;
            html += '</div>';
            container.innerHTML = html;
            lucide.createIcons();
        }

        function changeModalPage(page) {
            if (page < 1 || page > Math.ceil(currentTotal / 15)) return;
            currentPage = page;
            loadDepartmentEmployees(currentDepartmentName, page, modalSearchTerm);
        }

        function exportDepartment(deptName) {
            window.open(`departments.php?action=export&department=${encodeURIComponent(deptName)}`, '_blank');
        }

        document.getElementById('searchDepartments').addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            const filtered = departments.filter(dept => dept.name.toLowerCase().includes(term));
            renderDepartmentCards(filtered);
        });

        document.getElementById('filterBtn').addEventListener('click', function() {
            const term = document.getElementById('searchDepartments').value.toLowerCase();
            const filtered = departments.filter(dept => dept.name.toLowerCase().includes(term));
            renderDepartmentCards(filtered);
        });

        document.getElementById('modalSearch').addEventListener('keyup', function(e) {
            modalSearchTerm = e.target.value;
            currentPage = 1;
            loadDepartmentEmployees(currentDepartmentName, 1, modalSearchTerm);
        });

        document.getElementById('exportBtn').addEventListener('click', function() {
            if (currentDepartmentName) exportDepartment(currentDepartmentName);
        });

        loadDepartments();
    </script>
</body>
</html>