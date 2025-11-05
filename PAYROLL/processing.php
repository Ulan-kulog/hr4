<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Processing - Payroll Management</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
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
        
        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Payroll Processing Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-orange-100/50 text-orange-600">
                            <i data-lucide="calculator" class="w-5 h-5"></i>
                        </span>
                        Payroll Processing
                    </h2>
                    <div class="flex gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>All Departments</option>
                            <option>Hotel Department</option>
                            <option>Restaurant Department</option>
                            <option>HR Department</option>
                            <option>Logistic Department</option>
                            <option>Administrative Department</option>
                            <option>Financials Department</option>
                        </select>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Pending Processing -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Pending Processing</p>
                                <h3 class="text-3xl font-bold mt-1">18</h3>
                                <p class="text-xs text-gray-500 mt-1">Awaiting calculation</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="clock" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Processed This Month -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Processed This Month</p>
                                <h3 class="text-3xl font-bold mt-1">142</h3>
                                <p class="text-xs text-gray-500 mt-1">Employee payrolls</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="check-circle" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Payroll Amount -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Total Payroll</p>
                                <h3 class="text-3xl font-bold mt-1">₱2.4M</h3>
                                <p class="text-xs text-gray-500 mt-1">This month</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="dollar-sign" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Overtime Payments -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Overtime Payments</p>
                                <h3 class="text-3xl font-bold mt-1">₱84K</h3>
                                <p class="text-xs text-gray-500 mt-1">This period</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="watch" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Processing Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="font-semibold text-gray-800">Pending Payroll Calculations</h3>
                            <div class="flex gap-2">
                                <input type="text" placeholder="Search employees..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <!-- Sample data rows -->
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-blue-600 text-sm font-medium">JD</span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">John Doe</p>
                                                <p class="text-sm text-gray-500">EMP-001</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Hotel</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱35,000</td>
                                    <td class="px-4 py-3 text-sm text-green-600">₱5,200</td>
                                    <td class="px-4 py-3 text-sm text-red-600">₱3,800</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱36,400</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="p-1 text-green-600 hover:text-green-800 rounded transition-colors">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                            <button class="p-1 text-blue-600 hover:text-blue-800 rounded transition-colors">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                            <button class="p-1 text-red-600 hover:text-red-800 rounded transition-colors">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>