<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strategic Planning - Compensation Planning</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <!-- Strategic Planning Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-green-100/50 text-green-600">
                            <i data-lucide="trending-up" class="w-5 h-5"></i>
                        </span>
                        Strategic Planning & Analysis
                    </h2>
                    <div class="flex gap-2">
                        <button id="marketSalaryBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="bar-chart-3" class="w-4 h-4 mr-2"></i>
                            Market Salary
                        </button>
                        <button id="compensationBudgetBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="dollar-sign" class="w-4 h-4 mr-2"></i>
                            Budget Planning
                        </button>
                        <button id="payEquityBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="scale" class="w-4 h-4 mr-2"></i>
                            Pay Equity
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Market Competitiveness -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Market Position</p>
                                <h3 class="text-3xl font-bold mt-1">75%</h3>
                                <p class="text-xs text-gray-500 mt-1">Vs competitors</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="target" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Budget Utilization -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Budget Utilization</p>
                                <h3 class="text-3xl font-bold mt-1">82%</h3>
                                <p class="text-xs text-gray-500 mt-1">Of allocated budget</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="pie-chart" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Increase Budget -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Increase Budget</p>
                                <h3 class="text-3xl font-bold mt-1">₱1.2M</h3>
                                <p class="text-xs text-gray-500 mt-1">For next cycle</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="dollar-sign" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pay Equity Score -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Pay Equity Score</p>
                                <h3 class="text-3xl font-bold mt-1">94%</h3>
                                <p class="text-xs text-gray-500 mt-1">Gender pay ratio</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="scale" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Market Position vs Competitors -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Market Position vs Competitors</h3>
                        <div class="h-64">
                            <canvas id="marketPositionChart"></canvas>
                        </div>
                    </div>

                    <!-- Budget Allocation by Department -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Budget Allocation</h3>
                        <div class="h-64">
                            <canvas id="budgetAllocationChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Additional Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Pay Equity Analysis -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Pay Equity Analysis</h3>
                        <div class="h-64">
                            <canvas id="payEquityChart"></canvas>
                        </div>
                    </div>

                    <!-- Salary Trend Analysis -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Salary Trend Analysis</h3>
                        <div class="h-64">
                            <canvas id="salaryTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Market Benchmarking Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                        <h3 class="font-semibold text-gray-800">Market Salary Benchmarking</h3>
                        <div class="flex gap-2 mt-2 sm:mt-0">
                            <input type="text" placeholder="Search positions..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            <button class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Our Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Market Avg</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentile</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Front Desk Manager</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱28,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱26,500</td>
                                    <td class="px-4 py-3 text-sm text-green-600 font-medium">+5.7%</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">75th</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Competitive
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Head Chef</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱45,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱46,500</td>
                                    <td class="px-4 py-3 text-sm text-red-600 font-medium">-3.2%</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">45th</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Review Needed
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">HR Manager</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱38,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱35,000</td>
                                    <td class="px-4 py-3 text-sm text-green-600 font-medium">+8.6%</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">80th</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Competitive
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Sales Executive</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱22,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱24,000</td>
                                    <td class="px-4 py-3 text-sm text-red-600 font-medium">-8.3%</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">35th</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Action Required
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Compensation Budgeting Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Compensation Budget Planning</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600 mb-1">₱18.2M</div>
                            <div class="text-sm text-gray-600">Total Budget</div>
                            <div class="text-xs text-gray-500">Annual allocation</div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600 mb-1">₱14.9M</div>
                            <div class="text-sm text-gray-600">Utilized</div>
                            <div class="text-xs text-gray-500">82% of budget</div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600 mb-1">₱1.2M</div>
                            <div class="text-sm text-gray-600">Merit Increase</div>
                            <div class="text-xs text-gray-500">Next cycle</div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-orange-600 mb-1">₱2.4M</div>
                            <div class="text-sm text-gray-600">Bonus Pool</div>
                            <div class="text-xs text-gray-500">Variable pay</div>
                        </div>
                    </div>
                </div>

                <!-- Pay Equity Analysis Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Pay Equity Analysis</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 mb-2">94%</div>
                            <div class="text-sm font-medium text-gray-800">Gender Pay Ratio</div>
                            <div class="text-xs text-gray-500">Female/Male earnings</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-2">92%</div>
                            <div class="text-sm font-medium text-gray-800">Department Equity</div>
                            <div class="text-xs text-gray-500">Across all departments</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600 mb-2">96%</div>
                            <div class="text-sm font-medium text-gray-800">Experience Equity</div>
                            <div class="text-xs text-gray-500">Same experience level</div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-600">Compliance Status</span>
                            <span class="font-medium text-green-600">Fully Compliant</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 98%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Market Salary Benchmarking Modal -->
    <div id="marketSalaryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Market Salary Benchmarking</h3>
                <button id="closeMarketModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-6">
                <!-- Industry Salary Surveys -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i data-lucide="clipboard-list" class="w-4 h-4 mr-2 text-blue-600"></i>
                        Industry Salary Surveys
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Survey Source</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Survey</option>
                                <option value="mercer">Mercer</option>
                                <option value="towers">Towers Watson</option>
                                <option value="willis">Willis Towers Watson</option>
                                <option value="local">Local Market Survey</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Survey Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Competitor Analysis -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i data-lucide="users" class="w-4 h-4 mr-2 text-green-600"></i>
                        Competitor Analysis
                    </h4>
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Primary Competitor</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Competitor name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Their Avg Salary</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="₱">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Market Position</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="lead">Market Leader</option>
                                    <option value="competitive">Competitive</option>
                                    <option value="lag">Market Lagger</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Geographic Pay Differentials -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i data-lucide="map-pin" class="w-4 h-4 mr-2 text-purple-600"></i>
                        Geographic Pay Differentials
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Region/Area</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="ncr">Metro Manila</option>
                                <option value="luzon">Luzon</option>
                                <option value="visayas">Visayas</option>
                                <option value="mindanao">Mindanao</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cost of Living Index</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 1.15">
                        </div>
                    </div>
                </div>

                <!-- Market Trend Analysis -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i data-lucide="trending-up" class="w-4 h-4 mr-2 text-orange-600"></i>
                        Market Trend Analysis
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Projected Increase</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 4.5%">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Inflation Rate</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 3.2%">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelMarket" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Save Benchmark
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Compensation Budgeting Modal -->
    <div id="compensationBudgetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Compensation Budget Planning</h3>
                <button id="closeBudgetModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-6">
                <!-- Annual Salary Budget Planning -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2 text-blue-600"></i>
                        Annual Salary Budget Planning
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fiscal Year</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Budget (₱)</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 18200000">
                        </div>
                    </div>
                </div>

                <!-- Department-wise Allocation -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i data-lucide="building" class="w-4 h-4 mr-2 text-green-600"></i>
                        Department-wise Allocation
                    </h4>
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hotel Department</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="₱">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Restaurant Department</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="₱">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Support Departments</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="₱">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Merit Increase Planning -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i data-lucide="award" class="w-4 h-4 mr-2 text-purple-600"></i>
                        Merit Increase Planning
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Merit Increase Budget</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="₱">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Average Increase %</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 4.5">
                        </div>
                    </div>
                </div>

                <!-- Bonus Pool Management -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i data-lucide="gift" class="w-4 h-4 mr-2 text-orange-600"></i>
                        Bonus Pool Management
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Bonus Pool</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="₱">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Performance Period</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="annual">Annual</option>
                                <option value="semi-annual">Semi-Annual</option>
                                <option value="quarterly">Quarterly</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelBudget" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Save Budget Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pay Equity Analysis Modal -->
    <div id="payEquityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Pay Equity Analysis</h3>
                <button id="closeEquityModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Analysis Type</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="gender">Gender Pay Equity</option>
                        <option value="department">Department Comparison</option>
                        <option value="experience">Experience-based Analysis</option>
                        <option value="compliance">Equal Pay Compliance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Analysis Criteria</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-24" placeholder="Describe analysis criteria and methodology..."></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Pay Ratio</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 95">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tolerance Level</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 5">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelEquity" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Run Analysis
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Initialize Charts
        const marketPositionCtx = document.getElementById('marketPositionChart').getContext('2d');
        const marketPositionChart = new Chart(marketPositionCtx, {
            type: 'bar',
            data: {
                labels: ['Our Company', 'Competitor A', 'Competitor B', 'Competitor C', 'Market Avg'],
                datasets: [{
                    label: 'Average Salary (₱)',
                    data: [25400, 24000, 26500, 23000, 24500],
                    backgroundColor: [
                        '#3B82F6',
                        '#EF4444',
                        '#10B981',
                        '#F59E0B',
                        '#8B5CF6'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const budgetAllocationCtx = document.getElementById('budgetAllocationChart').getContext('2d');
        const budgetAllocationChart = new Chart(budgetAllocationCtx, {
            type: 'pie',
            data: {
                labels: ['Hotel Dept', 'Restaurant', 'HR', 'Logistic', 'Admin', 'Financial'],
                datasets: [{
                    data: [42, 35, 5, 8, 5, 5],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#8B5CF6',
                        '#F59E0B',
                        '#EF4444',
                        '#06B6D4'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        const payEquityCtx = document.getElementById('payEquityChart').getContext('2d');
        const payEquityChart = new Chart(payEquityCtx, {
            type: 'radar',
            data: {
                labels: ['Gender Equity', 'Dept Equity', 'Experience', 'Performance', 'Tenure', 'Education'],
                datasets: [{
                    label: 'Current Score',
                    data: [94, 92, 96, 88, 90, 85],
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: '#3B82F6',
                    borderWidth: 2,
                    pointBackgroundColor: '#3B82F6'
                }, {
                    label: 'Target Score',
                    data: [95, 95, 95, 95, 95, 95],
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: '#10B981',
                    borderWidth: 2,
                    pointBackgroundColor: '#10B981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        const salaryTrendCtx = document.getElementById('salaryTrendChart').getContext('2d');
        const salaryTrendChart = new Chart(salaryTrendCtx, {
            type: 'line',
            data: {
                labels: ['2020', '2021', '2022', '2023', '2024'],
                datasets: [{
                    label: 'Our Company',
                    data: [22000, 23000, 24000, 24800, 25400],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Market Average',
                    data: [21500, 22500, 23500, 24200, 24500],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Modal Functionality
        const marketSalaryModal = document.getElementById('marketSalaryModal');
        const compensationBudgetModal = document.getElementById('compensationBudgetModal');
        const payEquityModal = document.getElementById('payEquityModal');

        // Open modals
        document.getElementById('marketSalaryBtn').addEventListener('click', () => {
            marketSalaryModal.classList.remove('hidden');
        });

        document.getElementById('compensationBudgetBtn').addEventListener('click', () => {
            compensationBudgetModal.classList.remove('hidden');
        });

        document.getElementById('payEquityBtn').addEventListener('click', () => {
            payEquityModal.classList.remove('hidden');
        });

        // Close modals
        document.getElementById('closeMarketModal').addEventListener('click', () => marketSalaryModal.classList.add('hidden'));
        document.getElementById('closeBudgetModal').addEventListener('click', () => compensationBudgetModal.classList.add('hidden'));
        document.getElementById('closeEquityModal').addEventListener('click', () => payEquityModal.classList.add('hidden'));
        
        document.getElementById('cancelMarket').addEventListener('click', () => marketSalaryModal.classList.add('hidden'));
        document.getElementById('cancelBudget').addEventListener('click', () => compensationBudgetModal.classList.add('hidden'));
        document.getElementById('cancelEquity').addEventListener('click', () => payEquityModal.classList.add('hidden'));

        // Close modals when clicking outside
        marketSalaryModal.addEventListener('click', (e) => {
            if (e.target === marketSalaryModal) marketSalaryModal.classList.add('hidden');
        });
        compensationBudgetModal.addEventListener('click', (e) => {
            if (e.target === compensationBudgetModal) compensationBudgetModal.classList.add('hidden');
        });
        payEquityModal.addEventListener('click', (e) => {
            if (e.target === payEquityModal) payEquityModal.classList.add('hidden');
        });

        // Form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('Analysis submitted successfully!');
                marketSalaryModal.classList.add('hidden');
                compensationBudgetModal.classList.add('hidden');
                payEquityModal.classList.add('hidden');
            });
        });
    </script>
</body>
</html>