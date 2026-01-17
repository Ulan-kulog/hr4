<?php
// Set the API URL
$api_url = 'API/employees_api.php';
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
            <div class="flex-1 overflow-auto p-6">
                <!-- Header Section -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                        Employee Management
                    </h1>
                    <p class="text-gray-600 mt-1">Manage and view all employee information</p>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Employees -->
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Employees</p>
                                <h3 id="totalCount" class="text-2xl font-bold text-gray-800">0</h3>
                            </div>
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Employees -->
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Active Employees</p>
                                <h3 id="activeCount" class="text-2xl font-bold text-gray-800">0</h3>
                            </div>
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i data-lucide="user-check" class="w-6 h-6 text-green-600"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- New Hires (This Month) -->
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">New Hires (This Month)</p>
                                <h3 id="newHiresCount" class="text-2xl font-bold text-gray-800">0</h3>
                            </div>
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <i data-lucide="user-plus" class="w-6 h-6 text-purple-600"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Employees -->
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Floating Employees</p>
                                <h3 id="floatingCount" class="text-2xl font-bold text-gray-800">0</h3>
                            </div>
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <i data-lucide="user-cog" class="w-6 h-6 text-yellow-600"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 mb-6">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <!-- Department Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <select id="departmentFilter" class="bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Departments</option>
                            </select>
                        </div>
                        
                        <!-- Sub-Department Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sub-Department</label>
                            <select id="subDepartmentFilter" class="bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Sub-Departments</option>
                            </select>
                        </div>
                        
                        <!-- Employee ID Search -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID Search</label>
                            <div class="flex gap-2">
                                <input type="text" id="employeeIdSearch" placeholder="Enter Employee ID" 
                                       class="bg-white flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button id="searchByIdBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-end gap-2">
                            <button id="clearFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Clear Filters
                            </button>
                            <button id="refreshData" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Advanced Filters -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Work Status Filter -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Work Status</label>
                                <select id="workStatusFilter" class="bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Work Status</option>
                                    <option value="regular">Regular</option>
                                    <option value="probationary">Probationary</option>
                                    <option value="contractual">Contractual</option>
                                    <option value="floating">Floating</option>
                                    <option value="trainee">Trainee</option>
                                </select>
                            </div>
                            
                            <!-- Employment Status Filter -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                                <select id="employmentStatusFilter" class="bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="terminated">Terminated</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="awol">AWOL</option>
                                </select>
                            </div>
                            
                            <!-- General Search -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search (Name/Email)</label>
                                <input type="text" id="generalSearch" placeholder="Search by name or email..." 
                                       class="bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Employee Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Employee List</h3>
                                <p class="text-sm text-gray-600" id="tableSummary">Showing 0 employees</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">Show:</span>
                                <select id="pageLimit" class="px-3 py-1 border border-gray-300 rounded text-sm">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub-Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hire Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="employeesTableBody" class="divide-y divide-gray-200">
                                <tr id="loadingRow">
                                    <td colspan="8" class="px-4 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                            <p class="mt-2 text-gray-600">Loading employees...</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-4 border-t border-gray-200">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-600" id="paginationInfo">
                                Showing 0 to 0 of 0 entries
                            </div>
                            <div class="flex items-center gap-2" id="paginationControls">
                                <!-- Pagination buttons will be generated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Modal -->
    <div id="actionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Action Required</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <p class="text-gray-600 mb-4" id="modalMessage">Are you sure you want to perform this action?</p>
            <div class="flex justify-end gap-3">
                <button id="cancelAction" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button id="confirmAction" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Confirm
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Global variables
        let currentPage = 1;
        let totalPages = 1;
        let currentFilters = {};
        let totalRecords = 0;
        
        // DOM Elements
        const departmentFilter = document.getElementById('departmentFilter');
        const subDepartmentFilter = document.getElementById('subDepartmentFilter');
        const employeeIdSearch = document.getElementById('employeeIdSearch');
        const searchByIdBtn = document.getElementById('searchByIdBtn');
        const employmentStatusFilter = document.getElementById('employmentStatusFilter');
        const workStatusFilter = document.getElementById('workStatusFilter');
        const generalSearch = document.getElementById('generalSearch');
        const clearFilters = document.getElementById('clearFilters');
        const refreshData = document.getElementById('refreshData');
        const pageLimit = document.getElementById('pageLimit');
        const employeesTableBody = document.getElementById('employeesTableBody');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const activeCount = document.getElementById('activeCount');
        const totalCount = document.getElementById('totalCount');
        const newHiresCount = document.getElementById('newHiresCount');
        const floatingCount = document.getElementById('floatingCount');
        const tableSummary = document.getElementById('tableSummary');
        
        // Load departments and sub-departments
        async function loadDepartments() {
            try {
                const response = await fetch('<?php echo $api_url; ?>?action=departments');
                const data = await response.json();
                
                if (data.success) {
                    // Populate department filter
                    departmentFilter.innerHTML = '<option value="">All Departments</option>';
                    data.data.departments.forEach(dept => {
                        if (dept) {
                            const option = document.createElement('option');
                            option.value = dept;
                            option.textContent = dept;
                            departmentFilter.appendChild(option);
                        }
                    });
                    
                    // Populate sub-department filter
                    subDepartmentFilter.innerHTML = '<option value="">All Sub-Departments</option>';
                    data.data.sub_departments.forEach(subDept => {
                        if (subDept) {
                            const option = document.createElement('option');
                            option.value = subDept;
                            option.textContent = subDept;
                            subDepartmentFilter.appendChild(option);
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading departments:', error);
            }
        }
        
        // Load employees data
        async function loadEmployees(page = 1) {
            currentPage = page;
            
            // Show loading state
            employeesTableBody.innerHTML = `
                <tr id="loadingRow">
                    <td colspan="8" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <p class="mt-2 text-gray-600">Loading employees...</p>
                        </div>
                    </td>
                </tr>
            `;
            
            // Build query parameters
            const params = new URLSearchParams({
                page: currentPage,
                limit: pageLimit.value
            });
            
            // Add filters
            if (currentFilters.department) params.append('department', currentFilters.department);
            if (currentFilters.sub_department) params.append('sub_department', currentFilters.sub_department);
            if (currentFilters.id) params.append('id', currentFilters.id);
            if (currentFilters.employment_status) params.append('employment_status', currentFilters.employment_status);
            if (currentFilters.work_status) params.append('work_status', currentFilters.work_status);
            if (currentFilters.search) params.append('search', currentFilters.search);
            
            try {
                const response = await fetch(`<?php echo $api_url; ?>?${params.toString()}`);
                const data = await response.json();
                
                if (data.success) {
                    // Update stats
                    updateStats(data.data);
                    
                    // Update table
                    renderEmployeesTable(data.data);
                    
                    // Update pagination
                    updatePagination(data.pagination);
                    
                    // Update summary
                    tableSummary.textContent = `Showing ${data.pagination.total_records} employees`;
                    totalRecords = data.pagination.total_records;
                } else {
                    showError('Failed to load employees: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error loading employees:', error);
                showError('Failed to load employees. Please check your connection.');
            }
        }
        
        // Update stats
        function updateStats(employees) {
            const activeEmployees = employees.filter(emp => emp.employment_status === 'active').length;
            const totalEmployees = employees.length;
            const floatingEmployees = employees.filter(emp => emp.work_status === 'floating').length;
            
            // Calculate new hires this month
            const currentMonth = new Date().getMonth();
            const currentYear = new Date().getFullYear();
            const newHires = employees.filter(emp => {
                if (!emp.hire_date) return false;
                const hireDate = new Date(emp.hire_date);
                return hireDate.getMonth() === currentMonth && hireDate.getFullYear() === currentYear;
            }).length;
            
            activeCount.textContent = activeEmployees;
            totalCount.textContent = totalEmployees;
            newHiresCount.textContent = newHires;
            floatingCount.textContent = floatingEmployees;
        }
        
        // Render employees table
        function renderEmployeesTable(employees) {
            if (employees.length === 0) {
                employeesTableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="users" class="w-12 h-12 text-gray-300 mb-2"></i>
                                <p class="text-gray-600">No employees found</p>
                                <p class="text-sm text-gray-500 mt-1">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                `;
                lucide.createIcons();
                return;
            }
            
            let tableHTML = '';
            employees.forEach(employee => {
                const statusClass = employee.status_class || 'bg-gray-100 text-gray-800';
                const statusText = employee.employment_status ? 
                    employee.employment_status.charAt(0).toUpperCase() + employee.employment_status.slice(1) : 
                    'Unknown';
                
                const initials = `${employee.first_name ? employee.first_name.charAt(0) : ''}${employee.last_name ? employee.last_name.charAt(0) : ''}`;
                
                tableHTML += `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-medium text-gray-900">${employee.id || 'N/A'}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="avatar-placeholder">
                                    ${initials || '?'}
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">${employee.full_name || 'N/A'}</div>
                                    <div class="text-sm text-gray-500">${employee.email || 'No email'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-900">${employee.department || 'N/A'}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-900">${employee.sub_department || 'N/A'}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-900">${employee.job || 'N/A'}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="status-badge ${statusClass}">${statusText}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-gray-900">${employee.hire_date_formatted || 'N/A'}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button onclick="viewEmployee('${employee.id}')" 
                                        class="p-1 text-blue-600 hover:text-blue-800 transition-colors"
                                        title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button onclick="editEmployee('${employee.id}')" 
                                        class="p-1 text-green-600 hover:text-green-800 transition-colors"
                                        title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteEmployee('${employee.id}', '${employee.full_name}')" 
                                        class="p-1 text-red-600 hover:text-red-800 transition-colors"
                                        title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            employeesTableBody.innerHTML = tableHTML;
            lucide.createIcons();
        }
        
        // Update pagination controls
        function updatePagination(pagination) {
            totalPages = pagination.total_pages;
            currentPage = pagination.current_page;
            
            // Update pagination info
            const start = pagination.offset + 1;
            const end = Math.min(pagination.offset + pagination.limit, pagination.total_records);
            paginationInfo.textContent = `Showing ${start} to ${end} of ${pagination.total_records} entries`;
            
            // Generate pagination buttons
            let paginationHTML = '';
            
            // Previous button
            paginationHTML += `
                <button onclick="changePage(${currentPage - 1})" 
                        ${currentPage === 1 ? 'disabled' : ''}
                        class="pagination-btn ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'}">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
            `;
            
            // Page numbers
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
            
            // Next button
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
        
        // Change page
        function changePage(page) {
            if (page < 1 || page > totalPages || page === currentPage) return;
            loadEmployees(page);
        }
        
        // Update filters
        function updateFilters() {
            currentFilters = {
                department: departmentFilter.value || null,
                sub_department: subDepartmentFilter.value || null,
                id: employeeIdSearch.value || null,
                employment_status: employmentStatusFilter.value || null,
                work_status: workStatusFilter.value || null,
                search: generalSearch.value || null
            };
            
            // Reset to page 1 when filters change
            currentPage = 1;
            loadEmployees(1);
        }
        
        // Show error message
        function showError(message) {
            employeesTableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="alert-circle" class="w-12 h-12 text-red-300 mb-2"></i>
                            <p class="text-red-600">${message}</p>
                        </div>
                    </td>
                </tr>
            `;
            lucide.createIcons();
        }
        
        // Employee actions
        function viewEmployee(id) {
            alert(`View employee with ID: ${id}`);
            // Implement view functionality
        }
        
        function editEmployee(id) {
            alert(`Edit employee with ID: ${id}`);
            // Implement edit functionality
        }
        
        function deleteEmployee(id, name) {
            if (confirm(`Are you sure you want to delete ${name}? This action cannot be undone.`)) {
                alert(`Delete employee with ID: ${id}`);
                // Implement delete functionality
                loadEmployees(currentPage); // Refresh data
            }
        }
        
        // Event Listeners
        departmentFilter.addEventListener('change', updateFilters);
        subDepartmentFilter.addEventListener('change', updateFilters);
        employmentStatusFilter.addEventListener('change', updateFilters);
        workStatusFilter.addEventListener('change', updateFilters);
        pageLimit.addEventListener('change', () => {
            currentPage = 1;
            updateFilters();
        });
        
        searchByIdBtn.addEventListener('click', () => {
            if (employeeIdSearch.value.trim()) {
                updateFilters();
            }
        });
        
        employeeIdSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                updateFilters();
            }
        });
        
        generalSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                updateFilters();
            }
        });
        
        // Debounced search for general search
        let searchTimeout;
        generalSearch.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(updateFilters, 500);
        });
        
        clearFilters.addEventListener('click', () => {
            departmentFilter.value = '';
            subDepartmentFilter.value = '';
            employeeIdSearch.value = '';
            employmentStatusFilter.value = '';
            workStatusFilter.value = '';
            generalSearch.value = '';
            currentFilters = {};
            loadEmployees(1);
        });
        
        refreshData.addEventListener('click', () => {
            loadEmployees(currentPage);
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadDepartments();
            loadEmployees(1);
        });
    </script>
</body>
</html>