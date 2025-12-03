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
                            <h3 class="text-2xl font-bold mt-1">0</h3>
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
                            <h3 class="text-2xl font-bold mt-1">0</h3>
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
                            <h3 class="text-2xl font-bold mt-1">0</h3>
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
                            <h3 class="text-2xl font-bold mt-1">0</h3>
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
                            <h3 class="text-2xl font-bold mt-1">0</h3>
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
                            <h3 class="text-2xl font-bold mt-1">0</h3>
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
                            <!-- Employee rows will be populated here -->
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
    </div>
</body>
</html>