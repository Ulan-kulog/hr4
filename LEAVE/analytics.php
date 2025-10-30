<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Analytics - Leave Management</title>
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
            <!-- Leave Analytics Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-purple-100/50 text-purple-600">
                            <i data-lucide="trending-up" class="w-5 h-5"></i>
                        </span>
                        Leave Analytics
                    </h2>
                    <div class="flex gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Last 6 Months</option>
                            <option>Last Year</option>
                            <option>Last 2 Years</option>
                            <option>All Time</option>
                        </select>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Monthly Average -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Monthly Average</p>
                                <h3 class="text-3xl font-bold mt-1">74</h3>
                                <p class="text-xs text-gray-500 mt-1">Leave days per month</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="calendar" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Rate -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Approval Rate</p>
                                <h3 class="text-3xl font-bold mt-1">92%</h3>
                                <p class="text-xs text-gray-500 mt-1">Of all requests</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="check-circle" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Peak Leave Season -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Peak Season</p>
                                <h3 class="text-3xl font-bold mt-1">Dec</h3>
                                <p class="text-xs text-gray-500 mt-1">Most leaves taken</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="activity" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Impact -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Cost Impact</p>
                                <h3 class="text-3xl font-bold mt-1">₱284K</h3>
                                <p class="text-xs text-gray-500 mt-1">This quarter</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="dollar-sign" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Monthly Trend -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Monthly Leave Trend</h3>
                        <div class="h-64 flex items-end justify-between gap-2">
                            <!-- Sample chart bars -->
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 80%"></div>
                                <span class="text-xs text-gray-500 mt-1">Jan</span>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 65%"></div>
                                <span class="text-xs text-gray-500 mt-1">Feb</span>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 70%"></div>
                                <span class="text-xs text-gray-500 mt-1">Mar</span>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 55%"></div>
                                <span class="text-xs text-gray-500 mt-1">Apr</span>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 60%"></div>
                                <span class="text-xs text-gray-500 mt-1">May</span>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 75%"></div>
                                <span class="text-xs text-gray-500 mt-1">Jun</span>
                            </div>
                        </div>
                    </div>

                    <!-- Department Comparison -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Department Comparison</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Hotel Department</span>
                                    <span class="font-medium">42 days</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Restaurant Department</span>
                                    <span class="font-medium">38 days</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 76%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Logistic Department</span>
                                    <span class="font-medium">28 days</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-600 h-2 rounded-full" style="width: 56%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Financials Department</span>
                                    <span class="font-medium">14 days</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: 28%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Key Performance Indicators</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 mb-1">2.1</div>
                            <div class="text-sm text-gray-600">Avg. Leaves per Employee</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 mb-1">24h</div>
                            <div class="text-sm text-gray-600">Avg. Approval Time</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 mb-1">88%</div>
                            <div class="text-sm text-gray-600">On-time Return Rate</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600 mb-1">12%</div>
                            <div class="text-sm text-gray-600">Leave Conflict Rate</div>
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