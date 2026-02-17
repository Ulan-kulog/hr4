<?php
session_start();
include("../connection.php");

$db_name = "hr4_hr_4";
$conn = $connections[$db_name] ?? die("❌ Connection not found for $db_name");

// If AJAX request for stats (returns counts for the six statuses)
if (isset($_GET['action']) && $_GET['action'] === 'get_stats') {
    header('Content-Type: application/json');
    $statuses = ['regular', 'probationary', 'for_compliance', 'suspended', 'AWOL', 'terminated'];
    $stats = [];
    foreach ($statuses as $status) {
        $q = "SELECT COUNT(*) as total FROM employees WHERE employment_status = ?";
        $stmt = $conn->prepare($q);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stats[$status] = $row['total'] ?? 0;
    }
    echo json_encode($stats);
    exit;
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

// Fetch sub-departments for dropdown
$sub_departments = [];
$sub_dept_query = "SELECT id, name, department_id FROM sub_departments ORDER BY name";
$sub_dept_result = $conn->query($sub_dept_query);
if ($sub_dept_result) {
    while ($row = $sub_dept_result->fetch_assoc()) {
        $sub_departments[] = $row;
    }
}

// Initial stats for page load (six statuses)
$statuses = ['regular', 'probationary', 'for_compliance', 'suspended', 'AWOL', 'terminated'];
$initial_stats = [];
foreach ($statuses as $status) {
    $q = "SELECT COUNT(*) as total FROM employees WHERE employment_status = ?";
    $stmt = $conn->prepare($q);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $initial_stats[$status] = $row['total'] ?? 0;
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
        .avatar.online::before { background-color: #10B981; }
        .avatar.offline::before { background-color: #EF4444; }
        .avatar.away::before { background-color: #F59E0B; }
    </style>
</head>

<body class="bg-base-100 min-h-screen">
    <div class="drawer lg:drawer-open">
        <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
        <div class="flex flex-col drawer-content">
            <!-- Navbar -->
            <?php include '../INCLUDES/navbar.php'; ?>

            <!-- Main Content -->
            <main class="flex-1 p-4 md:p-6">
                <!-- Status Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-3 gap-4 mb-8" id="stats-container">

    <!-- Regular -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50"
         data-status="regular">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">
                    Regular
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    <?php echo $initial_stats['regular']; ?>
                </h3>
                <p class="text-xs text-gray-500 mt-1">Employees</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="badge-check" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- Probationary -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50"
         data-status="probationary">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">
                    Probationary
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    <?php echo $initial_stats['probationary']; ?>
                </h3>
                <p class="text-xs text-gray-500 mt-1">Employees</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="clock" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- For Compliance -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50"
         data-status="for_compliance">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">
                    For Compliance
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    <?php echo $initial_stats['for_compliance']; ?>
                </h3>
                <p class="text-xs text-gray-500 mt-1">Employees</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="file-text" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- Suspended -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50"
         data-status="suspended">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">
                    Suspended
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    <?php echo $initial_stats['suspended']; ?>
                </h3>
                <p class="text-xs text-gray-500 mt-1">Employees</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="alert-circle" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- AWOL -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50"
         data-status="AWOL">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">
                    AWOL
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    <?php echo $initial_stats['AWOL']; ?>
                </h3>
                <p class="text-xs text-gray-500 mt-1">Employees</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="user-x" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- Terminated -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50"
         data-status="terminated">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">
                    Terminated
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    <?php echo $initial_stats['terminated']; ?>
                </h3>
                <p class="text-xs text-gray-500 mt-1">Employees</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="user-minus" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

</div>

                <!-- Filter Section -->
                <div class="bg-white shadow-sm mb-6 p-3 border border-gray-200 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Department</label>
                            <select id="departmentFilter" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Sub-Department</label>
                            <select id="subDepartmentFilter" class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full">
                                <option value="">All Sub-Departments</option>
                                <?php foreach ($sub_departments as $sub): ?>
                                    <option value="<?php echo $sub['id']; ?>" data-dept="<?php echo $sub['department_id']; ?>"><?php echo htmlspecialchars($sub['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Employee Code</label>
                            <div class="flex gap-1">
                                <input type="text" id="employeeIdSearch" placeholder="Enter Code" class="flex-1 bg-white px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <button id="searchByIdBtn" class="bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded text-white text-sm transition-colors"><i data-lucide="search" class="w-4 h-4"></i></button>
                            </div>
                        </div>
                        <div class="flex items-end gap-1">
                            <button id="clearFilters" class="hover:bg-gray-50 px-3 py-1.5 border border-gray-300 rounded text-sm text-gray-700 transition-colors">Clear</button>
                            <button id="refreshData" class="bg-gray-800 hover:bg-gray-900 px-3 py-1.5 rounded text-sm text-white transition-colors"><i data-lucide="refresh-cw" class="w-4 h-4"></i></button>
                        </div>
                    </div>
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
                                <option value="for_compliance">For Compliance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-xs">Search (Name/Email)</label>
                            <input type="text" id="generalSearch" placeholder="Search..." class="bg-white px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full">
                        </div>
                    </div>
                </div>

                <!-- Employees Table -->
                <div class="bg-base-100 shadow-lg border card">
                    <div class="p-0 card-body">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr class="bg-base-200">
                                        <th>Employee</th>
                                        <th>Employee Code</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Employment Status</th>
                                        <th>Hire Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employee-table-body">
                                    <!-- Filled by AJAX -->
                                    <tr><td colspan="7" class="text-center py-8">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div id="pagination-container" class="flex justify-between items-center p-4 border-t"></div>
                    </div>
                </div>
            </main>
        </div>

        <div class="drawer-side">
            <label for="my-drawer-2" aria-label="close sidebar" class="drawer-overlay"></label>
            <?php include '../INCLUDES/sidebar.php'; ?>
        </div>
    </div>

    <!-- View Employee Modal -->
    <dialog id="viewEmployeeModal" class="modal">
        <div class="modal-box max-w-4xl w-11/12">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button></form>
            <h3 class="font-bold text-lg mb-4">Employee Details</h3>
            <div id="employeeDetails" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
            <!-- Compliance Notes Section -->
            <div class="mt-4 p-4 bg-base-200 rounded-lg">
                <h4 class="font-semibold mb-2">Compliance Notes</h4>
                <div id="complianceNotesDisplay" class="text-sm"></div>
            </div>
            <div class="modal-action flex gap-2 mt-4">
                <button class="btn btn-secondary" onclick="openForComplianceModal()">Set For Compliance</button>
                <button class="btn" onclick="viewEmployeeModal.close()">Close</button>
            </div>
        </div>
    </dialog>

    <!-- For Compliance Modal -->
    <dialog id="forComplianceModal" class="modal">
        <div class="modal-box">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button></form>
            <h3 class="font-bold text-lg mb-4">Set For Compliance</h3>
            <form id="forComplianceForm" onsubmit="event.preventDefault(); submitForCompliance();">
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Reason / Comment <span class="text-error">*</span></span></label>
                    <textarea id="complianceComment" class="textarea textarea-bordered" placeholder="Enter reason" required></textarea>
                </div>
                <div class="modal-action">
                    <button type="submit" class="btn btn-primary">Confirm</button>
                    <button type="button" class="btn" onclick="document.getElementById('forComplianceModal').close()">Cancel</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Toast Notification -->
    <div id="toast" class="toast-top z-50 toast toast-end">
        <div class="hidden alert" id="toast-alert"><span id="toast-message"></span></div>
    </div>

    <script src="../JAVASCRIPT/sidebar.js"></script>
    <script>
        lucide.createIcons();

        // Toast function
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-alert');
            const messageEl = document.getElementById('toast-message');
            messageEl.textContent = message;
            toast.className = `alert ${type === 'success' ? 'alert-success' : 'alert-error'} shadow-lg`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        // State
        let currentFilters = {
            department: '',
            sub_department: '',
            employee_id: '',
            emp_status: '',
            search: '',
            page: 1
        };
        let currentEmployeeId = null;

        // Load employees via AJAX
        function loadEmployees(page = 1) {
            currentFilters.page = page;
            const params = new URLSearchParams(currentFilters);
            fetch(`API/get_employee.php?${params}`)
                .then(res => res.json())
                .then(data => {
                    renderTable(data.employees);
                    renderPagination(data.total, data.page, data.limit);
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('employee-table-body').innerHTML = '<tr><td colspan="7" class="text-center py-8">Error loading data</td></tr>';
                });
        }

        // Render table rows
        function renderTable(employees) {
            const tbody = document.getElementById('employee-table-body');
            if (!employees.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="py-8 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <i data-lucide="users" class="opacity-20 w-12 h-12"></i>
                        <p class="opacity-70 text-lg">No employees found</p>
                        <p class="opacity-50 text-sm">Try adjusting your filters</p>
                    </div>
                </td></tr>`;
                lucide.createIcons();
                return;
            }

            let html = '';
            employees.forEach(emp => {
                const fullName = `${emp.first_name} ${emp.last_name}`;
                const status = emp.employment_status || 'N/A';
                let badgeClass = 'badge-ghost';
                if (status === 'regular') badgeClass = 'badge-success';
                else if (status === 'probationary') badgeClass = 'badge-warning';
                else if (status === 'for_compliance') badgeClass = 'badge-info';
                else if (status === 'suspended') badgeClass = 'badge-error';
                else if (status === 'AWOL') badgeClass = 'badge-neutral';
                else if (status === 'terminated') badgeClass = 'badge-error';

                html += `<tr class="hover" data-employee-id="${emp.id}">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar ${emp.work_status === 'active' ? 'online' : (emp.work_status === 'on_leave' ? 'away' : 'offline')}">
                                <div class="rounded-full w-10 h-10">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=001f54&color=fff" alt="${fullName}" />
                                </div>
                            </div>
                            <div>
                                <div class="font-bold">${fullName}</div>
                                <div class="opacity-50 text-sm">${emp.email || ''}</div>
                            </div>
                        </div>
                    </td>
                    <td><div class="badge-outline badge">${emp.employee_code || emp.id}</div></td>
                    <td>${emp.job || 'N/A'}</td>
                    <td>${emp.department_name || 'N/A'}</td>
                    <td class="status-badge"><span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span></td>
                    <td>${emp.formatted_hire_date || 'N/A'}</td>
                    <td><button class="btn btn-ghost btn-sm" onclick="viewEmployee(${emp.id})"><i data-lucide="eye" class="w-4 h-4"></i></button></td>
                </tr>`;
            });
            tbody.innerHTML = html;
            lucide.createIcons();
        }

        // Render pagination
        function renderPagination(total, currentPage, limit) {
            const container = document.getElementById('pagination-container');
            const totalPages = Math.ceil(total / limit);
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = `<div class="opacity-70 text-sm">
                Showing <span class="font-bold">${(currentPage-1)*limit+1}</span> to <span class="font-bold">${Math.min(currentPage*limit, total)}</span> of <span class="font-bold">${total}</span> results
            </div><div class="join">`;

            // Previous
            html += `<button class="btn btn-outline join-item ${currentPage <= 1 ? 'btn-disabled' : ''}" onclick="changePage(${currentPage-1})"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>`;

            // Page numbers (simplified)
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage-2 && i <= currentPage+2)) {
                    html += `<button class="btn join-item ${currentPage === i ? 'btn-active' : 'btn-outline'}" onclick="changePage(${i})">${i}</button>`;
                } else if (i === currentPage-3 || i === currentPage+3) {
                    html += `<button class="btn join-item btn-disabled">...</button>`;
                }
            }

            // Next
            html += `<button class="btn btn-outline join-item ${currentPage >= totalPages ? 'btn-disabled' : ''}" onclick="changePage(${currentPage+1})"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>`;
            html += '</div>';
            container.innerHTML = html;
            lucide.createIcons();
        }

        function changePage(page) {
            loadEmployees(page);
        }

        // Function to update stats cards from stats object
        function updateStatsCards(stats) {
            document.querySelector('[data-status="regular"] .card-title').textContent = stats.regular || 0;
            document.querySelector('[data-status="probationary"] .card-title').textContent = stats.probationary || 0;
            document.querySelector('[data-status="for_compliance"] .card-title').textContent = stats.for_compliance || 0;
            document.querySelector('[data-status="suspended"] .card-title').textContent = stats.suspended || 0;
            document.querySelector('[data-status="AWOL"] .card-title').textContent = stats.AWOL || 0;
            document.querySelector('[data-status="terminated"] .card-title').textContent = stats.terminated || 0;
        }

        // Load stats via AJAX (using same file with action=get_stats)
        function loadStats() {
            fetch('employees.php?action=get_stats')
                .then(res => res.json())
                .then(stats => updateStatsCards(stats))
                .catch(err => console.error('Failed to load stats', err));
        }

        // Filter change handlers
        function updateFilterAndLoad() {
            currentFilters = {
                department: document.getElementById('departmentFilter').value,
                sub_department: document.getElementById('subDepartmentFilter').value,
                employee_id: document.getElementById('employeeIdSearch').value.trim(),
                emp_status: document.getElementById('employmentStatusFilter').value,
                search: document.getElementById('generalSearch').value.trim(),
                page: 1
            };
            loadEmployees(1);
        }

        document.getElementById('departmentFilter').addEventListener('change', updateFilterAndLoad);
        document.getElementById('subDepartmentFilter').addEventListener('change', updateFilterAndLoad);
        document.getElementById('employmentStatusFilter').addEventListener('change', updateFilterAndLoad);
        document.getElementById('generalSearch').addEventListener('keyup', debounce(updateFilterAndLoad, 500));
        document.getElementById('employeeIdSearch').addEventListener('keyup', debounce(updateFilterAndLoad, 500));
        document.getElementById('searchByIdBtn').addEventListener('click', updateFilterAndLoad);

        document.getElementById('clearFilters').addEventListener('click', function() {
            document.getElementById('departmentFilter').value = '';
            document.getElementById('subDepartmentFilter').value = '';
            document.getElementById('employeeIdSearch').value = '';
            document.getElementById('employmentStatusFilter').value = '';
            document.getElementById('generalSearch').value = '';
            updateFilterAndLoad();
        });

        document.getElementById('refreshData').addEventListener('click', function() {
            loadEmployees(currentFilters.page);
            loadStats();
        });

        // Debounce helper
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Sub-department filtering based on department
        document.getElementById('departmentFilter').addEventListener('change', function() {
            const deptId = this.value;
            const subSelect = document.getElementById('subDepartmentFilter');
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

        // Employee view and compliance
        function viewEmployee(id) {
            currentEmployeeId = id;
            document.getElementById('viewEmployeeModal').showModal();
            fetch(`API/get_employee_details.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderEmployeeDetails(data.employee);
                        // Display compliance notes
                        const notesDiv = document.getElementById('complianceNotesDisplay');
                        if (data.employee.compliance_notes) {
                            notesDiv.innerHTML = `<p>${data.employee.compliance_notes}</p>`;
                        } else {
                            notesDiv.innerHTML = '<p class="opacity-50">No compliance notes</p>';
                        }
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
                        <p><span class="font-semibold">Employee Code:</span> ${emp.employee_code || emp.id}</p>
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

        function openForComplianceModal() {
            if (!currentEmployeeId) return;
            document.getElementById('forComplianceModal').showModal();
        }

        function submitForCompliance() {
            const comment = document.getElementById('complianceComment').value;
            if (!comment.trim()) {
                showToast('Comment is required', 'error');
                return;
            }

            fetch('API/update_employment_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    employee_id: currentEmployeeId,
                    employment_status: 'for_compliance',
                    comment: comment
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Employee marked as For Compliance');
                    document.getElementById('forComplianceModal').close();
                    document.getElementById('viewEmployeeModal').close();

                    // Update the table row status badge
                    const row = document.querySelector(`tr[data-employee-id="${currentEmployeeId}"]`);
                    if (row) {
                        const statusCell = row.querySelector('.status-badge');
                        if (statusCell) {
                            statusCell.innerHTML = `<span class="badge badge-info">For Compliance</span>`;
                        }
                    }

                    // Refresh stats
                    loadStats();
                } else {
                    showToast('Update failed: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error updating status', 'error');
            });
        }

        // Initial load
        loadEmployees(1);
        loadStats();

        // Trigger sub-department filtering on load
        window.addEventListener('load', function() {
            const event = new Event('change');
            document.getElementById('departmentFilter').dispatchEvent(event);
        });

        document.getElementById('menu-toggle')?.addEventListener('click', function() {
            document.getElementById('my-drawer-2').checked = true;
        });
    </script>
</body>
</html>