<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliance - Compensation Planning</title>
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
            <!-- Compliance Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-orange-100/50 text-orange-600">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </span>
                        Compliance & Administration
                    </h2>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                        New Policy
                    </button>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Compliance Score -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Compliance Score</p>
                                <h3 class="text-3xl font-bold mt-1">94%</h3>
                                <p class="text-xs text-gray-500 mt-1">Overall rating</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="shield" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Policies -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Active Policies</p>
                                <h3 class="text-3xl font-bold mt-1">18</h3>
                                <p class="text-xs text-gray-500 mt-1">Compensation policies</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="file-text" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Findings -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Audit Findings</p>
                                <h3 class="text-3xl font-bold mt-1">3</h3>
                                <p class="text-xs text-gray-500 mt-1">Open items</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="clipboard-check" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Policy Updates -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Updates Needed</p>
                                <h3 class="text-3xl font-bold mt-1">5</h3>
                                <p class="text-xs text-gray-500 mt-1">Policies due for review</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="refresh-cw" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compliance Dashboard -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Policy Compliance Status</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <span class="text-sm font-medium text-green-800">Minimum Wage Compliance</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Compliant</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                <span class="text-sm font-medium text-yellow-800">Overtime Regulations</span>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Review Needed</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <span class="text-sm font-medium text-green-800">Equal Pay Act</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Compliant</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <span class="text-sm font-medium text-red-800">Bonus Taxation</span>
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Action Required</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Recent Policy Updates</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Service Charge Policy</p>
                                    <p class="text-xs text-gray-500">Updated: Dec 15, 2024</p>
                                </div>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Active</span>
                            </div>
                            <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Commission Structure</p>
                                    <p class="text-xs text-gray-500">Updated: Nov 30, 2024</p>
                                </div>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Active</span>
                            </div>
                            <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Shift Differential</p>
                                    <p class="text-xs text-gray-500">Review due: Jan 15, 2025</p>
                                </div>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Pending</span>
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