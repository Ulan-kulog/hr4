<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Compensation - Compensation Planning</title>
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
            <!-- Core Compensation Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-blue-100/50 text-blue-600">
                            <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                        </span>
                        Core Compensation Management
                    </h2>
                    <div class="flex gap-2">
                        <button id="salaryStructureBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Salary Structure
                        </button>
                        <button id="bonusIncentivesBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="gift" class="w-4 h-4 mr-2"></i>
                            Bonus & Incentives
                        </button>
                        <button id="allowanceBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                            Allowances
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Salary Budget -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Total Salary Budget</p>
                                <h3 class="text-3xl font-bold mt-1">₱18.2M</h3>
                                <p class="text-xs text-gray-500 mt-1">Annual budget</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="credit-card" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Average Salary -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Average Salary</p>
                                <h3 class="text-3xl font-bold mt-1">₱25.4K</h3>
                                <p class="text-xs text-gray-500 mt-1">Per month</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Bonus Pool -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Bonus Pool</p>
                                <h3 class="text-3xl font-bold mt-1">₱2.4M</h3>
                                <p class="text-xs text-gray-500 mt-1">This year</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="gift" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Allowance Budget -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Allowance Budget</p>
                                <h3 class="text-3xl font-bold mt-1">₱1.8M</h3>
                                <p class="text-xs text-gray-500 mt-1">Annual allocation</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="package" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Salary Distribution Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Salary Distribution by Department</h3>
                        <div class="h-64">
                            <canvas id="salaryChart"></canvas>
                        </div>
                    </div>

                    <!-- Bonus vs Base Pay Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Compensation Mix</h3>
                        <div class="h-64">
                            <canvas id="compensationMixChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Salary Structure Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                        <h3 class="font-semibold text-gray-800">Salary Structure</h3>
                        <div class="flex gap-2 mt-2 sm:mt-0">
                            <input type="text" placeholder="Search grades..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            <button class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <!-- Grade 1 -->
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">G1</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Entry Level</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱15,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱20,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">45</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Grade 2 -->
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">G2</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Junior Staff</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱18,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱25,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">89</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Grade 3 -->
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">G3</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Senior Staff</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱25,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱35,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">67</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Grade 4 -->
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">G4</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Supervisor</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱35,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱50,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">23</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending Review
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bonus & Incentives Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Bonus & Incentive Plans</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Sales Commission -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Sales Commission</h4>
                                <span class="p-2 rounded-lg bg-green-100 text-green-600">
                                    <i data-lucide="percent" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Department: Restaurant</p>
                            <p class="text-sm text-gray-600 mb-3">Target: ₱5M monthly sales</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Commission: 5%</span>
                                <div class="flex gap-1">
                                    <button class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                                    <button class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </div>
                            </div>
                        </div>

                        <!-- Service Excellence Bonus -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Service Excellence</h4>
                                <span class="p-2 rounded-lg bg-blue-100 text-blue-600">
                                    <i data-lucide="award" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Department: Hotel</p>
                            <p class="text-sm text-gray-600 mb-3">Based on guest ratings</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Bonus: ₱2,000</span>
                                <div class="flex gap-1">
                                    <button class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                                    <button class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </div>
                            </div>
                        </div>

                        <!-- Referral Bonus -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Referral Program</h4>
                                <span class="p-2 rounded-lg bg-purple-100 text-purple-600">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">All Departments</p>
                            <p class="text-sm text-gray-600 mb-3">Employee referral incentive</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Bonus: ₱3,000</span>
                                <div class="flex gap-1">
                                    <button class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                                    <button class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Allowance Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Allowance Management</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowance Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eligible Employees</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Transportation</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">All</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱2,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Monthly</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">247</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Meal Allowance</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Restaurant</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱150/day</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Daily</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">74</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Uniform</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">All</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱5,000</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">Annual</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">247</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
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

    <!-- Salary Structure Modal -->
    <div id="salaryStructureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Create Salary Structure</h3>
                <button id="closeSalaryModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., G1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position Title</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Junior Staff">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Salary</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="15000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Salary</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="25000">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Department</option>
                        <option value="hotel">Hotel Department</option>
                        <option value="restaurant">Restaurant Department</option>
                        <option value="hr">HR Department</option>
                        <option value="logistic">Logistic Department</option>
                        <option value="all">All Departments</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelSalary" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Create Structure
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bonus & Incentives Modal -->
    <div id="bonusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Create Bonus & Incentive Plan</h3>
                <button id="closeBonusModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Sales Commission Plan">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bonus Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="commission">Commission</option>
                            <option value="performance">Performance Bonus</option>
                            <option value="referral">Referral Bonus</option>
                            <option value="seasonal">Seasonal Incentive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount/Percentage</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 5% or ₱2,000">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Eligibility Criteria</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-20" placeholder="Describe eligibility requirements..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelBonus" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Create Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Allowance Modal -->
    <div id="allowanceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Create Allowance</h3>
                <button id="closeAllowanceModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allowance Type</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="transportation">Transportation</option>
                        <option value="meal">Meal Allowance</option>
                        <option value="uniform">Uniform</option>
                        <option value="housing">Housing</option>
                        <option value="shift">Shift Differential</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="2000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="daily">Daily</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annual">Annual</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Eligible Departments</label>
                    <select multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="hotel">Hotel Department</option>
                        <option value="restaurant">Restaurant Department</option>
                        <option value="hr">HR Department</option>
                        <option value="logistic">Logistic Department</option>
                        <option value="administrative">Administrative</option>
                        <option value="financial">Financial</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelAllowance" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Create Allowance
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Initialize Charts
        const salaryCtx = document.getElementById('salaryChart').getContext('2d');
        const salaryChart = new Chart(salaryCtx, {
            type: 'bar',
            data: {
                labels: ['Hotel', 'Restaurant', 'HR', 'Logistic', 'Admin', 'Financial'],
                datasets: [{
                    label: 'Average Salary (₱)',
                    data: [28000, 22000, 35000, 25000, 32000, 40000],
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

        const mixCtx = document.getElementById('compensationMixChart').getContext('2d');
        const compensationMixChart = new Chart(mixCtx, {
            type: 'doughnut',
            data: {
                labels: ['Base Salary', 'Bonuses', 'Allowances', 'Benefits'],
                datasets: [{
                    data: [65, 15, 12, 8],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#8B5CF6',
                        '#F59E0B'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Modal Functionality
        const salaryStructureModal = document.getElementById('salaryStructureModal');
        const bonusModal = document.getElementById('bonusModal');
        const allowanceModal = document.getElementById('allowanceModal');

        // Open modals
        document.getElementById('salaryStructureBtn').addEventListener('click', () => {
            salaryStructureModal.classList.remove('hidden');
        });

        document.getElementById('bonusIncentivesBtn').addEventListener('click', () => {
            bonusModal.classList.remove('hidden');
        });

        document.getElementById('allowanceBtn').addEventListener('click', () => {
            allowanceModal.classList.remove('hidden');
        });

        // Close modals
        document.getElementById('closeSalaryModal').addEventListener('click', () => salaryStructureModal.classList.add('hidden'));
        document.getElementById('closeBonusModal').addEventListener('click', () => bonusModal.classList.add('hidden'));
        document.getElementById('closeAllowanceModal').addEventListener('click', () => allowanceModal.classList.add('hidden'));
        
        document.getElementById('cancelSalary').addEventListener('click', () => salaryStructureModal.classList.add('hidden'));
        document.getElementById('cancelBonus').addEventListener('click', () => bonusModal.classList.add('hidden'));
        document.getElementById('cancelAllowance').addEventListener('click', () => allowanceModal.classList.add('hidden'));

        // Close modals when clicking outside
        salaryStructureModal.addEventListener('click', (e) => {
            if (e.target === salaryStructureModal) salaryStructureModal.classList.add('hidden');
        });
        bonusModal.addEventListener('click', (e) => {
            if (e.target === bonusModal) bonusModal.classList.add('hidden');
        });
        allowanceModal.addEventListener('click', (e) => {
            if (e.target === allowanceModal) allowanceModal.classList.add('hidden');
        });

        // Form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('Form submitted successfully!');
                salaryStructureModal.classList.add('hidden');
                bonusModal.classList.add('hidden');
                allowanceModal.classList.add('hidden');
            });
        });
    </script>
</body>
</html>