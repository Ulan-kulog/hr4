<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees - Core Human Capital</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-base-100 min-h-screen bg-white">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <?php include '../INCLUDES/sidebar.php'; ?>

    <!-- Content Area -->
    <div class="flex flex-col flex-1 overflow-auto">
        <!-- Navbar -->
        <?php include '../INCLUDES/navbar.php'; ?>
    <!-- Manage Employees Section -->
    <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <span class="p-2 mr-3 rounded-lg bg-blue-100/50 text-blue-600">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </span>
                Employee Management
            </h2>
            <!-- Department Filter Dropdown -->
            <div class="relative">
                <select id="departmentFilter" class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Departments</option>
                    <option value="hotel">Hotel Department</option>
                    <option value="restaurant">Restaurant Department</option>
                    <option value="hr">HR Department</option>
                    <option value="logistic">Logistic Department</option>
                    <option value="administrative">Administrative Department</option>
                    <option value="financial">Financial Department</option>
                </select>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4 mb-8">
            <!-- New Hires -->
            <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">New Hires</p>
                        <h3 class="text-2xl font-bold mt-1">15</h3>
                        <p class="text-xs text-gray-500 mt-1">This month</p>
                    </div>
                    <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                        <i data-lucide="user-plus" class="w-5 h-5 text-[#F7B32B]"></i>
                    </div>
                </div>
            </div>

            <!-- Under Review -->
            <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Under Review</p>
                        <h3 class="text-2xl font-bold mt-1">8</h3>
                        <p class="text-xs text-gray-500 mt-1">Performance review</p>
                    </div>
                    <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-[#F7B32B]"></i>
                    </div>
                </div>
            </div>

            <!-- Notice Period -->
            <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Notice Period</p>
                        <h3 class="text-2xl font-bold mt-1">6</h3>
                        <p class="text-xs text-gray-500 mt-1">Serving notice</p>
                    </div>
                    <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                        <i data-lucide="clock" class="w-5 h-5 text-[#F7B32B]"></i>
                    </div>
                </div>
            </div>

            <!-- Active Employees -->
            <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Active Employees</p>
                        <h3 class="text-2xl font-bold mt-1">247</h3>
                        <p class="text-xs text-gray-500 mt-1">Currently working</p>
                    </div>
                    <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                        <i data-lucide="user-check" class="w-5 h-5 text-[#F7B32B]"></i>
                    </div>
                </div>
            </div>

            <!-- AWOL -->
            <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">AWOL</p>
                        <h3 class="text-2xl font-bold mt-1">3</h3>
                        <p class="text-xs text-gray-500 mt-1">Absent without leave</p>
                    </div>
                    <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                        <i data-lucide="user-x" class="w-5 h-5 text-[#F7B32B]"></i>
                    </div>
                </div>
            </div>

            <!-- Floating Employees -->
            <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Floating</p>
                        <h3 class="text-2xl font-bold mt-1">12</h3>
                        <p class="text-xs text-gray-500 mt-1">No assigned department</p>
                    </div>
                    <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                        <i data-lucide="users" class="w-5 h-5 text-[#F7B32B]"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="font-semibold text-gray-800">Employee List</h3>
                    <div class="flex gap-2">
                        <input type="text" placeholder="Search employees..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-64">
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Hotel Department Employees -->
                        <tr class="department-hotel">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-600 text-sm font-medium">JD</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">John Doe</p>
                                        <p class="text-sm text-gray-500">EMP001</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Hotel
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">Front Desk Manager</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">2023-01-15</td>
                            <td class="px-4 py-3">
                                <button class="action-btn text-red-600 hover:text-red-800 p-1 rounded transition-colors" data-action="terminate" data-employee="John Doe">
                                    <i data-lucide="user-x" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                        
                        <tr class="department-hotel">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-green-600 text-sm font-medium">SJ</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Sarah Johnson</p>
                                        <p class="text-sm text-gray-500">EMP002</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Hotel
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">Housekeeping Supervisor</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Under Review
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">2024-03-01</td>
                            <td class="px-4 py-3">
                                <button class="action-btn text-orange-600 hover:text-orange-800 p-1 rounded transition-colors" data-action="warning" data-employee="Sarah Johnson">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Restaurant Department Employees -->
                        <tr class="department-restaurant">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-purple-600 text-sm font-medium">MJ</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Mike Johnson</p>
                                        <p class="text-sm text-gray-500">EMP003</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Restaurant
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">Head Chef</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">2022-08-20</td>
                            <td class="px-4 py-3">
                                <button class="action-btn text-red-600 hover:text-red-800 p-1 rounded transition-colors" data-action="terminate" data-employee="Mike Johnson">
                                    <i data-lucide="user-x" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- HR Department Employees -->
                        <tr class="department-hr">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-pink-600 text-sm font-medium">EW</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Emma Wilson</p>
                                        <p class="text-sm text-gray-500">EMP004</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    HR
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">HR Manager</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Notice Period
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">2021-05-10</td>
                            <td class="px-4 py-3">
                                <button class="action-btn text-blue-600 hover:text-blue-800 p-1 rounded transition-colors" data-action="pending" data-employee="Emma Wilson">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- AWOL Employee -->
                        <tr class="department-logistic">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-red-600 text-sm font-medium">TB</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Tom Brown</p>
                                        <p class="text-sm text-gray-500">EMP005</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    Logistic
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">Delivery Driver</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    AWOL
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">2023-11-05</td>
                            <td class="px-4 py-3">
                                <button class="action-btn text-red-600 hover:text-red-800 p-1 rounded transition-colors" data-action="terminate" data-employee="Tom Brown">
                                    <i data-lucide="user-x" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Floating Employee -->
                        <tr class="department-floating">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-gray-600 text-sm font-medium">LS</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Lisa Smith</p>
                                        <p class="text-sm text-gray-500">EMP006</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Floating
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">Trainee</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">2024-02-15</td>
                            <td class="px-4 py-3">
                                <button class="action-btn text-orange-600 hover:text-orange-800 p-1 rounded transition-colors" data-action="warning" data-employee="Lisa Smith">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
        lucide.createIcons();

        // Department Filter Functionality
        const departmentFilter = document.getElementById('departmentFilter');
        const tableRows = document.querySelectorAll('tbody tr');

        departmentFilter.addEventListener('change', function() {
            const selectedDept = this.value;
            
            tableRows.forEach(row => {
                if (selectedDept === 'all') {
                    row.style.display = '';
                } else {
                    if (row.classList.contains(`department-${selectedDept}`)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });

        // Modal Functionality
        const actionModal = document.getElementById('actionModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const closeModal = document.getElementById('closeModal');
        const cancelAction = document.getElementById('cancelAction');
        const confirmAction = document.getElementById('confirmAction');

        let currentAction = '';
        let currentEmployee = '';

        // Action button click handlers
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', function() {
                currentAction = this.getAttribute('data-action');
                currentEmployee = this.getAttribute('data-employee');
                
                switch(currentAction) {
                    case 'terminate':
                        modalTitle.textContent = 'Terminate Employee';
                        modalMessage.textContent = `Are you sure you want to terminate ${currentEmployee}? This action cannot be undone.`;
                        confirmAction.textContent = 'Terminate';
                        confirmAction.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors';
                        break;
                    case 'warning':
                        modalTitle.textContent = 'Issue Warning';
                        modalMessage.textContent = `Issue a formal warning to ${currentEmployee}?`;
                        confirmAction.textContent = 'Issue Warning';
                        confirmAction.className = 'px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors';
                        break;
                    case 'pending':
                        modalTitle.textContent = 'Pending Action';
                        modalMessage.textContent = `Mark ${currentEmployee} as pending for further review?`;
                        confirmAction.textContent = 'Mark Pending';
                        confirmAction.className = 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors';
                        break;
                }
                
                actionModal.classList.remove('hidden');
            });
        });

        // Close modal handlers
        closeModal.addEventListener('click', () => actionModal.classList.add('hidden'));
        cancelAction.addEventListener('click', () => actionModal.classList.add('hidden'));

        // Confirm action handler
        confirmAction.addEventListener('click', () => {
            // Here you would typically make an API call to perform the action
            console.log(`Action: ${currentAction} for employee: ${currentEmployee}`);
            alert(`Action "${currentAction}" confirmed for ${currentEmployee}`);
            actionModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        actionModal.addEventListener('click', (e) => {
            if (e.target === actionModal) {
                actionModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>