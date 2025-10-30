<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Compensation - Compensation Planning</title>
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
            <!-- Performance Compensation Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-purple-100/50 text-purple-600">
                            <i data-lucide="award" class="w-5 h-5"></i>
                        </span>
                        Performance-Linked Compensation
                    </h2>
                    <div class="flex gap-2">
                        <button id="createRewardsBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="gift" class="w-4 h-4 mr-2"></i>
                            Create Rewards
                        </button>
                        <button id="releaseRewardsBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                            Release Rewards
                        </button>
                        <button id="createPoliciesBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                            Create Policies
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Bonus Pool -->
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

                    <!-- Merit Increase Budget -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Merit Budget</p>
                                <h3 class="text-3xl font-bold mt-1">₱1.2M</h3>
                                <p class="text-xs text-gray-500 mt-1">Increase pool</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Commission Paid -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Commissions</p>
                                <h3 class="text-3xl font-bold mt-1">₱856K</h3>
                                <p class="text-xs text-gray-500 mt-1">YTD paid</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="percent" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Policies -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Active Policies</p>
                                <h3 class="text-3xl font-bold mt-1">12</h3>
                                <p class="text-xs text-gray-500 mt-1">Compensation policies</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="file-text" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Performance vs Compensation -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Performance vs Compensation</h3>
                        <div class="h-64">
                            <canvas id="performanceCompensationChart"></canvas>
                        </div>
                    </div>

                    <!-- Variable Pay Distribution -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Variable Pay Distribution</h3>
                        <div class="h-64">
                            <canvas id="variablePayChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Additional Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Merit Increase Distribution -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Merit Increase Distribution</h3>
                        <div class="h-64">
                            <canvas id="meritIncreaseChart"></canvas>
                        </div>
                    </div>

                    <!-- Policy Compliance Status -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Policy Compliance Status</h3>
                        <div class="h-64">
                            <canvas id="policyComplianceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Merit Increase Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                        <h3 class="font-semibold text-gray-800">Merit Increase Management</h3>
                        <div class="flex gap-2 mt-2 sm:mt-0">
                            <button id="meritIncreaseBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                Merit Increase
                            </button>
                            <input type="text" placeholder="Search employees..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance Rating</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proposed Increase</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr>
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
                                    <td class="px-4 py-3 text-sm text-gray-900">Hotel</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱28,000</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            4.5/5
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-green-600 font-medium">+6%</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Approve</button>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">Reject</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
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
                                    <td class="px-4 py-3 text-sm text-gray-900">Restaurant</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">₱22,000</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            4.2/5
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-green-600 font-medium">+5%</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">Cancel</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Variable Pay Planning -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Variable Pay Plans</h3>
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
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                            </div>
                        </div>

                        <!-- Profit Sharing -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Profit Sharing</h4>
                                <span class="p-2 rounded-lg bg-blue-100 text-blue-600">
                                    <i data-lucide="pie-chart" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">All Departments</p>
                            <p class="text-sm text-gray-600 mb-3">Based on company profits</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Pool: 10% of profits</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                            </div>
                        </div>

                        <!-- KPI-based Bonus -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">KPI Bonus</h4>
                                <span class="p-2 rounded-lg bg-purple-100 text-purple-600">
                                    <i data-lucide="target" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Management Level</p>
                            <p class="text-sm text-gray-600 mb-3">Based on KPIs achievement</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Bonus: Up to 20%</span>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Draft</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compensation Policy Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                        <h3 class="font-semibold text-gray-800">Compensation Policy Management</h3>
                        <div class="flex gap-2 mt-2 sm:mt-0">
                            <button id="reviewPoliciesBtn" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                                <i data-lucide="clipboard-check" class="w-4 h-4 mr-2"></i>
                                Review Policies
                            </button>
                            <button id="uploadPoliciesBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                                <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                                Upload Policies
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Policy Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compliance</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Bonus Policy</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">v3.2</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">2024-01-01</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            98%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</button>
                                            <button class="text-orange-600 hover:text-orange-800 text-sm font-medium">Update</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Commission Policy</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">v2.1</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">2024-03-15</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Under Review
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            85%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Review</button>
                                            <button class="text-green-600 hover:text-green-800 text-sm font-medium">Approve</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total Rewards Statements -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Total Rewards Statements</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="text-center p-6 border border-gray-200 rounded-lg">
                            <div class="p-3 rounded-lg bg-blue-100 text-blue-600 inline-flex mb-4">
                                <i data-lucide="file-text" class="w-6 h-6"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-2">Statement Generation</h4>
                            <p class="text-sm text-gray-600 mb-4">Generate annual compensation summaries</p>
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm transition-colors">
                                Generate Statements
                            </button>
                        </div>

                        <div class="text-center p-6 border border-gray-200 rounded-lg">
                            <div class="p-3 rounded-lg bg-green-100 text-green-600 inline-flex mb-4">
                                <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-2">Benefits Valuation</h4>
                            <p class="text-sm text-gray-600 mb-4">Calculate total compensation value</p>
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm transition-colors">
                                Calculate Benefits
                            </button>
                        </div>

                        <div class="text-center p-6 border border-gray-200 rounded-lg">
                            <div class="p-3 rounded-lg bg-purple-100 text-purple-600 inline-flex mb-4">
                                <i data-lucide="send" class="w-6 h-6"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-2">Digital Delivery</h4>
                            <p class="text-sm text-gray-600 mb-4">Distribute statements electronically</p>
                            <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-sm transition-colors">
                                Deliver Statements
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Create Rewards Modal -->
    <div id="createRewardsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Create Performance Rewards</h3>
                <button id="closeRewardsModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reward Type</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="bonus">Performance Bonus</option>
                        <option value="commission">Sales Commission</option>
                        <option value="profit">Profit Sharing</option>
                        <option value="kpi">KPI-based Incentive</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Amount/Percentage</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 5% or ₱5,000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Performance Period</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="semi-annual">Semi-Annual</option>
                            <option value="annual">Annual</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Eligibility Criteria</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-20" placeholder="Describe eligibility requirements..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelRewards" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Create Reward
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Release Rewards Modal -->
    <div id="releaseRewardsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Release Performance Rewards</h3>
                <button id="closeReleaseModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Reward Program</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="q1-bonus">Q1 Performance Bonus</option>
                        <option value="sales-commission">Sales Commission Q2</option>
                        <option value="profit-sharing">Annual Profit Sharing</option>
                        <option value="kpi-bonus">KPI Achievement Bonus</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Release Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="payroll">Payroll Integration</option>
                            <option value="separate">Separate Payment</option>
                            <option value="voucher">Voucher System</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Release Notes</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-20" placeholder="Add any special instructions or notes..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelRelease" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Release Rewards
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Policies Modal -->
    <div id="createPoliciesModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Create Compensation Policy</h3>
                <button id="closePoliciesModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Policy Name</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Bonus Policy 2024">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Policy Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="bonus">Bonus Policy</option>
                            <option value="commission">Commission Policy</option>
                            <option value="merit">Merit Increase Policy</option>
                            <option value="equity">Pay Equity Policy</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Effective Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Policy Description</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-24" placeholder="Describe the policy details and guidelines..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelPolicies" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Create Policy
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Merit Increase Modal -->
    <div id="meritIncreaseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Merit Increase Management</h3>
                <button id="closeMeritModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Employee</option>
                            <option value="emp001">John Doe</option>
                            <option value="emp002">Sarah Johnson</option>
                            <option value="emp003">Mike Wilson</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Performance Rating</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="5">5 - Outstanding</option>
                            <option value="4">4 - Exceeds Expectations</option>
                            <option value="3">3 - Meets Expectations</option>
                            <option value="2">2 - Needs Improvement</option>
                            <option value="1">1 - Unsatisfactory</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Salary</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="₱">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proposed Increase %</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 5.5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Justification</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-20" placeholder="Provide justification for the merit increase..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelMerit" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Submit Increase
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Initialize Charts
        const performanceCompensationCtx = document.getElementById('performanceCompensationChart').getContext('2d');
        const performanceCompensationChart = new Chart(performanceCompensationCtx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Employees',
                    data: [
                        {x: 3.2, y: 22000}, {x: 4.1, y: 28000}, {x: 4.8, y: 35000},
                        {x: 3.5, y: 24000}, {x: 4.3, y: 32000}, {x: 4.6, y: 38000},
                        {x: 3.8, y: 26000}, {x: 4.4, y: 34000}, {x: 4.9, y: 42000}
                    ],
                    backgroundColor: '#3B82F6',
                    borderColor: '#3B82F6',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Performance Rating'
                        },
                        min: 3,
                        max: 5
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Salary (₱)'
                        },
                        beginAtZero: false
                    }
                }
            }
        });

        const variablePayCtx = document.getElementById('variablePayChart').getContext('2d');
        const variablePayChart = new Chart(variablePayCtx, {
            type: 'doughnut',
            data: {
                labels: ['Performance Bonus', 'Sales Commission', 'Profit Sharing', 'KPI Incentives'],
                datasets: [{
                    data: [45, 30, 15, 10],
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

        const meritIncreaseCtx = document.getElementById('meritIncreaseChart').getContext('2d');
        const meritIncreaseChart = new Chart(meritIncreaseCtx, {
            type: 'bar',
            data: {
                labels: ['1-2%', '3-4%', '5-6%', '7-8%', '9-10%', '10%+'],
                datasets: [{
                    label: 'Number of Employees',
                    data: [12, 45, 67, 34, 18, 8],
                    backgroundColor: '#8B5CF6',
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

        const policyComplianceCtx = document.getElementById('policyComplianceChart').getContext('2d');
        const policyComplianceChart = new Chart(policyComplianceCtx, {
            type: 'polarArea',
            data: {
                labels: ['Bonus Policy', 'Commission', 'Merit Increase', 'Pay Equity', 'Allowances'],
                datasets: [{
                    data: [98, 85, 92, 94, 96],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#8B5CF6',
                        '#F59E0B',
                        '#EF4444'
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

        // Modal Functionality
        const createRewardsModal = document.getElementById('createRewardsModal');
        const releaseRewardsModal = document.getElementById('releaseRewardsModal');
        const createPoliciesModal = document.getElementById('createPoliciesModal');
        const meritIncreaseModal = document.getElementById('meritIncreaseModal');

        // Open modals
        document.getElementById('createRewardsBtn').addEventListener('click', () => {
            createRewardsModal.classList.remove('hidden');
        });

        document.getElementById('releaseRewardsBtn').addEventListener('click', () => {
            releaseRewardsModal.classList.remove('hidden');
        });

        document.getElementById('createPoliciesBtn').addEventListener('click', () => {
            createPoliciesModal.classList.remove('hidden');
        });

        document.getElementById('meritIncreaseBtn').addEventListener('click', () => {
            meritIncreaseModal.classList.remove('hidden');
        });

        document.getElementById('reviewPoliciesBtn').addEventListener('click', () => {
            createPoliciesModal.classList.remove('hidden');
        });

        document.getElementById('uploadPoliciesBtn').addEventListener('click', () => {
            alert('Policy upload functionality would be implemented here');
        });

        // Close modals
        document.getElementById('closeRewardsModal').addEventListener('click', () => createRewardsModal.classList.add('hidden'));
        document.getElementById('closeReleaseModal').addEventListener('click', () => releaseRewardsModal.classList.add('hidden'));
        document.getElementById('closePoliciesModal').addEventListener('click', () => createPoliciesModal.classList.add('hidden'));
        document.getElementById('closeMeritModal').addEventListener('click', () => meritIncreaseModal.classList.add('hidden'));
        
        document.getElementById('cancelRewards').addEventListener('click', () => createRewardsModal.classList.add('hidden'));
        document.getElementById('cancelRelease').addEventListener('click', () => releaseRewardsModal.classList.add('hidden'));
        document.getElementById('cancelPolicies').addEventListener('click', () => createPoliciesModal.classList.add('hidden'));
        document.getElementById('cancelMerit').addEventListener('click', () => meritIncreaseModal.classList.add('hidden'));

        // Close modals when clicking outside
        createRewardsModal.addEventListener('click', (e) => {
            if (e.target === createRewardsModal) createRewardsModal.classList.add('hidden');
        });
        releaseRewardsModal.addEventListener('click', (e) => {
            if (e.target === releaseRewardsModal) releaseRewardsModal.classList.add('hidden');
        });
        createPoliciesModal.addEventListener('click', (e) => {
            if (e.target === createPoliciesModal) createPoliciesModal.classList.add('hidden');
        });
        meritIncreaseModal.addEventListener('click', (e) => {
            if (e.target === meritIncreaseModal) meritIncreaseModal.classList.add('hidden');
        });

        // Form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('Action completed successfully!');
                createRewardsModal.classList.add('hidden');
                releaseRewardsModal.classList.add('hidden');
                createPoliciesModal.classList.add('hidden');
                meritIncreaseModal.classList.add('hidden');
            });
        });
    </script>
</body>
</html>