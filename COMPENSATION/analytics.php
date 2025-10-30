<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Compensation Planning</title>
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
            <!-- Analytics Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-indigo-100/50 text-indigo-600">
                            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        </span>
                        Analytics & Reporting
                    </h2>
                    <div class="flex gap-2">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                            Export Report
                        </button>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700">
                            <option>Last 30 Days</option>
                            <option>Last Quarter</option>
                            <option>Year to Date</option>
                            <option>Last Year</option>
                        </select>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Compensation Ratio -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Comp Ratio</p>
                                <h3 class="text-3xl font-bold mt-1">1.08</h3>
                                <p class="text-xs text-gray-500 mt-1">Industry: 1.12</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="scale" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Per Employee -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Cost/Employee</p>
                                <h3 class="text-3xl font-bold mt-1">₱385K</h3>
                                <p class="text-xs text-gray-500 mt-1">Annual average</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="user" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Turnover Cost -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Turnover Cost</p>
                                <h3 class="text-3xl font-bold mt-1">₱2.1M</h3>
                                <p class="text-xs text-gray-500 mt-1">YTD impact</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="repeat" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- ROI on Compensation -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Compensation ROI</p>
                                <h3 class="text-3xl font-bold mt-1">3.2x</h3>
                                <p class="text-xs text-gray-500 mt-1">Return on investment</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Dashboard -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Department Cost Analysis</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Hotel Operations</span>
                                    <span class="font-medium">₱8.2M</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Restaurant & F&B</span>
                                    <span class="font-medium">₱6.8M</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 37%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Support Departments</span>
                                    <span class="font-medium">₱3.2M</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: 18%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Compensation Trends</h3>
                        <div class="h-48 flex items-end justify-between gap-2">
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 60%"></div>
                                <span class="text-xs text-gray-500 mt-1">Q1</span>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 75%"></div>
                                <span class="text-xs text-gray-500 mt-1">Q2</span>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 85%"></div>
                                <span class="text-xs text-gray-500 mt-1">Q3</span>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="bg-blue-500 w-full rounded-t" style="height: 92%"></div>
                                <span class="text-xs text-gray-500 mt-1">Q4</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Compensation Metrics</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 mb-1">18%</div>
                            <div class="text-sm text-gray-600">Variable Pay Ratio</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 mb-1">12.5%</div>
                            <div class="text-sm text-gray-600">Benefits Cost Ratio</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 mb-1">4.2%</div>
                            <div class="text-sm text-gray-600">Merit Increase Avg</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600 mb-1">88%</div>
                            <div class="text-sm text-gray-600">Comp Satisfaction</div>
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