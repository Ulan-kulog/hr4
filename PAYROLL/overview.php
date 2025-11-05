<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Overview - Payroll Management</title>
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
            <!-- Payroll Overview Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-blue-100/50 text-blue-600">
                            <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                        </span>
                        Payroll Overview
                    </h2>
                    <div class="flex gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>This Month</option>
                            <option>Last Month</option>
                            <option>This Quarter</option>
                            <option>This Year</option>
                        </select>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Payroll Cost -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Total Payroll Cost</p>
                                <h3 class="text-3xl font-bold mt-1">₱2.4M</h3>
                                <p class="text-xs text-gray-500 mt-1">This month</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="dollar-sign" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Average Salary -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Average Salary</p>
                                <h3 class="text-3xl font-bold mt-1">₱45K</h3>
                                <p class="text-xs text-gray-500 mt-1">Per employee</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Withholding -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Tax Withholding</p>
                                <h3 class="text-3xl font-bold mt-1">₱486K</h3>
                                <p class="text-xs text-gray-500 mt-1">Monthly</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="file-text" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits Cost -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Benefits Cost</p>
                                <h3 class="text-3xl font-bold mt-1">₱300K</h3>
                                <p class="text-xs text-gray-500 mt-1">Monthly benefits</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="heart" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Distribution -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Payroll Components -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Payroll Components Distribution</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Basic Salary</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">65%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Overtime Pay</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 15%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">15%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Benefits & Allowances</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 12%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">12%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Bonuses</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: 8%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">8%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Department-wise Payroll -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Payroll by Department</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Hotel Department</span>
                                <span class="text-sm font-medium text-gray-700">₱850K</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Restaurant Department</span>
                                <span class="text-sm font-medium text-gray-700">₱760K</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">HR Department</span>
                                <span class="text-sm font-medium text-gray-700">₱240K</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Logistic Department</span>
                                <span class="text-sm font-medium text-gray-700">₱560K</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Administrative</span>
                                <span class="text-sm font-medium text-gray-700">₱160K</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Financials</span>
                                <span class="text-sm font-medium text-gray-700">₱280K</span>
                            </div>
                        </div>
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