<?php include 'compensation_policies.php' ?>
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

<body class="bg-base-100 bg-white min-h-screen">
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
                <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center gap-4 mb-6">
                        <h2 class="flex items-center font-bold text-gray-800 text-2xl">
                            <span class="bg-purple-100/50 mr-3 p-2 rounded-lg text-purple-600">
                                <i data-lucide="award" class="w-5 h-5"></i>
                            </span>
                            Performance-Linked Compensation
                        </h2>
                        <div class="flex gap-2">
                            <button id="createRewardsBtn" class="flex items-center bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="gift" class="mr-2 w-4 h-4"></i>
                                Create Rewards
                            </button>
                            <button id="releaseRewardsBtn" class="flex items-center bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="send" class="mr-2 w-4 h-4"></i>
                                Release Rewards
                            </button>
                            <button id="createPoliciesBtn" class="flex items-center bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="file-text" class="mr-2 w-4 h-4"></i>
                                Create Policies
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="gap-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                        <!-- Total Bonus Pool -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Bonus Pool</p>
                                    <h3 class="mt-1 font-bold text-3xl">₱2.4M</h3>
                                    <p class="mt-1 text-gray-500 text-xs">This year</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="gift" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Merit Increase Budget -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Merit Budget</p>
                                    <h3 class="mt-1 font-bold text-3xl">₱1.2M</h3>
                                    <p class="mt-1 text-gray-500 text-xs">Increase pool</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Paid -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Commissions</p>
                                    <h3 class="mt-1 font-bold text-3xl">₱856K</h3>
                                    <p class="mt-1 text-gray-500 text-xs">YTD paid</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="percent" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Active Policies -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="hover:drop-shadow-md font-medium text-[#001f54] text-sm transition-all">Active Policies</p>
                                    <h3 class="mt-1 font-bold text-3xl">12</h3>
                                    <p class="mt-1 text-gray-500 text-xs">Compensation policies</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] hover:bg-[#002b70] p-3 rounded-lg transition-all duration-300">
                                    <i data-lucide="file-text" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="gap-6 grid grid-cols-1 lg:grid-cols-2 mb-8">
                        <!-- Performance vs Compensation -->
                        <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                            <h3 class="mb-4 font-semibold text-gray-800">Performance vs Compensation</h3>
                            <div class="h-64">
                                <canvas id="performanceCompensationChart"></canvas>
                            </div>
                        </div>

                        <!-- Variable Pay Distribution -->
                        <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                            <h3 class="mb-4 font-semibold text-gray-800">Variable Pay Distribution</h3>
                            <div class="h-64">
                                <canvas id="variablePayChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Charts Section -->
                    <div class="gap-6 grid grid-cols-1 lg:grid-cols-2 mb-8">
                        <!-- Merit Increase Distribution -->
                        <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                            <h3 class="mb-4 font-semibold text-gray-800">Merit Increase Distribution</h3>
                            <div class="h-64">
                                <canvas id="meritIncreaseChart"></canvas>
                            </div>
                        </div>

                        <!-- Policy Compliance Status -->
                        <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                            <h3 class="mb-4 font-semibold text-gray-800">Policy Compliance Status</h3>
                            <div class="h-64">
                                <canvas id="policyComplianceChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Merit Increase Management -->
                    <div class="bg-white shadow-sm mb-6 p-6 border border-gray-100 rounded-xl">
                        <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center mb-4">
                            <h3 class="font-semibold text-gray-800">Merit Increase Management</h3>
                            <div class="flex gap-2 mt-2 sm:mt-0">
                                <button id="meritIncreaseBtn" class="flex items-center bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition-colors">
                                    <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                                    Merit Increase
                                </button>
                                <input type="text" placeholder="Search employees..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-gray-100 border-b">
                                    <tr>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Employee</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Department</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Current Salary</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Performance Rating</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Proposed Increase</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                <div class="flex justify-center items-center bg-blue-100 mr-3 rounded-full w-8 h-8">
                                                    <span class="font-medium text-blue-600 text-sm">JD</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">John Doe</p>
                                                    <p class="text-gray-500 text-sm">EMP001</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-900 text-sm">Hotel</td>
                                        <td class="px-4 py-3 text-gray-900 text-sm">₱28,000</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center bg-green-100 px-2.5 py-0.5 rounded-full font-medium text-green-800 text-xs">
                                                4.5/5
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-medium text-green-600 text-sm">+6%</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center bg-yellow-100 px-2.5 py-0.5 rounded-full font-medium text-yellow-800 text-xs">
                                                Pending
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex gap-2">
                                                <button class="font-medium text-blue-600 hover:text-blue-800 text-sm">Approve</button>
                                                <button class="font-medium text-red-600 hover:text-red-800 text-sm">Reject</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                <div class="flex justify-center items-center bg-green-100 mr-3 rounded-full w-8 h-8">
                                                    <span class="font-medium text-green-600 text-sm">SJ</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">Sarah Johnson</p>
                                                    <p class="text-gray-500 text-sm">EMP002</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-900 text-sm">Restaurant</td>
                                        <td class="px-4 py-3 text-gray-900 text-sm">₱22,000</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center bg-blue-100 px-2.5 py-0.5 rounded-full font-medium text-blue-800 text-xs">
                                                4.2/5
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-medium text-green-600 text-sm">+5%</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center bg-green-100 px-2.5 py-0.5 rounded-full font-medium text-green-800 text-xs">
                                                Approved
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex gap-2">
                                                <button class="font-medium text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                                                <button class="font-medium text-red-600 hover:text-red-800 text-sm">Cancel</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Variable Pay Planning -->
                    <div class="bg-white shadow-sm mb-6 p-6 border border-gray-100 rounded-xl">
                        <h3 class="mb-4 font-semibold text-gray-800">Variable Pay Plans</h3>
                        <div class="gap-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Sales Commission -->
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-semibold text-gray-800">Sales Commission</h4>
                                    <span class="bg-green-100 p-2 rounded-lg text-green-600">
                                        <i data-lucide="percent" class="w-4 h-4"></i>
                                    </span>
                                </div>
                                <p class="mb-2 text-gray-600 text-sm">Department: Restaurant</p>
                                <p class="mb-3 text-gray-600 text-sm">Target: ₱5M monthly sales</p>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700 text-sm">Commission: 5%</span>
                                    <span class="bg-green-100 px-2 py-1 rounded-full text-green-800 text-xs">Active</span>
                                </div>
                            </div>

                            <!-- Profit Sharing -->
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-semibold text-gray-800">Profit Sharing</h4>
                                    <span class="bg-blue-100 p-2 rounded-lg text-blue-600">
                                        <i data-lucide="pie-chart" class="w-4 h-4"></i>
                                    </span>
                                </div>
                                <p class="mb-2 text-gray-600 text-sm">All Departments</p>
                                <p class="mb-3 text-gray-600 text-sm">Based on company profits</p>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700 text-sm">Pool: 10% of profits</span>
                                    <span class="bg-green-100 px-2 py-1 rounded-full text-green-800 text-xs">Active</span>
                                </div>
                            </div>

                            <!-- KPI-based Bonus -->
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-semibold text-gray-800">KPI Bonus</h4>
                                    <span class="bg-purple-100 p-2 rounded-lg text-purple-600">
                                        <i data-lucide="target" class="w-4 h-4"></i>
                                    </span>
                                </div>
                                <p class="mb-2 text-gray-600 text-sm">Management Level</p>
                                <p class="mb-3 text-gray-600 text-sm">Based on KPIs achievement</p>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700 text-sm">Bonus: Up to 20%</span>
                                    <span class="bg-yellow-100 px-2 py-1 rounded-full text-yellow-800 text-xs">Draft</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compensation Policy Management -->
                    <div class="bg-white shadow-sm mb-6 p-6 border border-gray-100 rounded-xl">
                        <div class="flex sm:flex-row flex-col justify-between items-start sm:items-center mb-4">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Compensation Policy Management</h3>
                                <p class="text-gray-500 text-sm">Total: <?php echo $stats['total_policies'] ?? 0; ?> policies</p>
                            </div>
                            <div class="flex gap-2 mt-2 sm:mt-0">
                                <button id="createPolicyBtn" class="flex items-center bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white transition-colors">
                                    <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                                    Create Policy
                                </button>
                                <div class="relative">
                                    <input type="text" id="searchPolicies" placeholder="Search policies..." class="px-3 py-2 pl-10 border border-gray-200 rounded-lg text-sm">
                                    <i data-lucide="search" class="top-3 left-3 absolute w-4 h-4 text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Policy Stats -->
                        <div class="gap-4 grid grid-cols-2 md:grid-cols-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-blue-600 text-sm">Active</p>
                                        <p class="font-bold text-blue-700 text-2xl"><?php echo $stats['active_policies'] ?? 0; ?></p>
                                    </div>
                                    <i data-lucide="check-circle" class="w-8 h-8 text-blue-500"></i>
                                </div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-yellow-600 text-sm">Under Review</p>
                                        <p class="font-bold text-yellow-700 text-2xl"><?php echo $stats['review_policies'] ?? 0; ?></p>
                                    </div>
                                    <i data-lucide="clipboard-check" class="w-8 h-8 text-yellow-500"></i>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-gray-600 text-sm">Draft</p>
                                        <p class="font-bold text-gray-700 text-2xl"><?php echo $stats['draft_policies'] ?? 0; ?></p>
                                    </div>
                                    <i data-lucide="file-edit" class="w-8 h-8 text-gray-500"></i>
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-green-600 text-sm">Compliance</p>
                                        <p class="font-bold text-green-700 text-2xl"><?php echo number_format($stats['avg_compliance'] ?? 0, 1); ?>%</p>
                                    </div>
                                    <i data-lucide="trending-up" class="w-8 h-8 text-green-500"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Policies Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full" id="policiesTable">
                                <thead class="bg-gray-50 border-gray-100 border-b">
                                    <tr>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Policy Name</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Version</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Effective Date</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Compliance</th>
                                        <th class="px-4 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($policies as $policy): ?>
                                        <tr data-policy-id="<?php echo $policy['id']; ?>">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($policy['policy_name']); ?></div>
                                                <div class="text-gray-500 text-sm">Created by <?php echo $policy['created_by_name']; ?></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?php
                                                $type_badges = [
                                                    'bonus' => ['bg-blue-100', 'text-blue-800', 'Bonus'],
                                                    'commission' => ['bg-green-100', 'text-green-800', 'Commission'],
                                                    'merit' => ['bg-purple-100', 'text-purple-800', 'Merit'],
                                                    'equity' => ['bg-yellow-100', 'text-yellow-800', 'Equity'],
                                                    'allowance' => ['bg-indigo-100', 'text-indigo-800', 'Allowance']
                                                ];
                                                $badge = $type_badges[$policy['policy_type']] ?? ['bg-gray-100', 'text-gray-800', 'Other'];
                                                ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badge[0]; ?> <?php echo $badge[1]; ?>">
                                                    <?php echo $badge[2]; ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-900 text-sm"><?php echo $policy['version']; ?></td>
                                            <td class="px-4 py-3 text-gray-900 text-sm">
                                                <?php echo date('M d, Y', strtotime($policy['effective_date'])); ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?php
                                                $status_badges = [
                                                    'active' => ['bg-green-100', 'text-green-800', 'Active'],
                                                    'inactive' => ['bg-red-100', 'text-red-800', 'Inactive'],
                                                    'draft' => ['bg-gray-100', 'text-gray-800', 'Draft'],
                                                    'under_review' => ['bg-yellow-100', 'text-yellow-800', 'Under Review']
                                                ];
                                                $status_badge = $status_badges[$policy['status']] ?? ['bg-gray-100', 'text-gray-800', 'Unknown'];
                                                ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_badge[0]; ?> <?php echo $status_badge[1]; ?>">
                                                    <?php echo $status_badge[2]; ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <div class="bg-gray-200 mr-2 rounded-full w-full h-2">
                                                        <div class="bg-green-600 rounded-full h-2" style="width: <?php echo $policy['compliance_rate']; ?>%"></div>
                                                    </div>
                                                    <span class="font-medium text-sm"><?php echo $policy['compliance_rate']; ?>%</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex gap-2">
                                                    <button onclick="viewPolicy(<?php echo $policy['id']; ?>)" class="hover:bg-blue-50 p-1 rounded text-blue-600 hover:text-blue-800">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                    </button>
                                                    <button onclick="editPolicy(<?php echo $policy['id']; ?>)" class="hover:bg-orange-50 p-1 rounded text-orange-600 hover:text-orange-800">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                    </button>
                                                    <button onclick="deletePolicy(<?php echo $policy['id']; ?>)" class="hover:bg-red-50 p-1 rounded text-red-600 hover:text-red-800">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                    <button onclick="uploadDocument(<?php echo $policy['id']; ?>)" class="hover:bg-indigo-50 p-1 rounded text-indigo-600 hover:text-indigo-800">
                                                        <i data-lucide="upload" class="w-4 h-4"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Total Rewards Statements -->
                    <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                        <h3 class="mb-4 font-semibold text-gray-800">Total Rewards Statements</h3>
                        <div class="gap-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <div class="p-6 border border-gray-200 rounded-lg text-center">
                                <div class="inline-flex bg-blue-100 mb-4 p-3 rounded-lg text-blue-600">
                                    <i data-lucide="file-text" class="w-6 h-6"></i>
                                </div>
                                <h4 class="mb-2 font-semibold text-gray-800">Statement Generation</h4>
                                <p class="mb-4 text-gray-600 text-sm">Generate annual compensation summaries</p>
                                <button class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg w-full text-white text-sm transition-colors">
                                    Generate Statements
                                </button>
                            </div>

                            <div class="p-6 border border-gray-200 rounded-lg text-center">
                                <div class="inline-flex bg-green-100 mb-4 p-3 rounded-lg text-green-600">
                                    <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                                </div>
                                <h4 class="mb-2 font-semibold text-gray-800">Benefits Valuation</h4>
                                <p class="mb-4 text-gray-600 text-sm">Calculate total compensation value</p>
                                <button class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg w-full text-white text-sm transition-colors">
                                    Calculate Benefits
                                </button>
                            </div>

                            <div class="p-6 border border-gray-200 rounded-lg text-center">
                                <div class="inline-flex bg-purple-100 mb-4 p-3 rounded-lg text-purple-600">
                                    <i data-lucide="send" class="w-6 h-6"></i>
                                </div>
                                <h4 class="mb-2 font-semibold text-gray-800">Digital Delivery</h4>
                                <p class="mb-4 text-gray-600 text-sm">Distribute statements electronically</p>
                                <button class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg w-full text-white text-sm transition-colors">
                                    Deliver Statements
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Create Rewards Modal -->
        <div id="createRewardsModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
            <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Create Performance Rewards</h3>
                    <button id="closeRewardsModal" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Reward Type</label>
                        <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                            <option value="bonus">Performance Bonus</option>
                            <option value="commission">Sales Commission</option>
                            <option value="profit">Profit Sharing</option>
                            <option value="kpi">KPI-based Incentive</option>
                        </select>
                    </div>
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Target Amount/Percentage</label>
                            <input type="text" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., 5% or ₱5,000">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Performance Period</label>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="semi-annual">Semi-Annual</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Eligibility Criteria</label>
                        <textarea class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full h-20" placeholder="Describe eligibility requirements..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancelRewards" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition-colors">
                            Create Reward
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Release Rewards Modal -->
        <div id="releaseRewardsModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
            <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Release Performance Rewards</h3>
                    <button id="closeReleaseModal" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Select Reward Program</label>
                        <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                            <option value="q1-bonus">Q1 Performance Bonus</option>
                            <option value="sales-commission">Sales Commission Q2</option>
                            <option value="profit-sharing">Annual Profit Sharing</option>
                            <option value="kpi-bonus">KPI Achievement Bonus</option>
                        </select>
                    </div>
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Release Date</label>
                            <input type="date" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Payment Method</label>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="payroll">Payroll Integration</option>
                                <option value="separate">Separate Payment</option>
                                <option value="voucher">Voucher System</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Release Notes</label>
                        <textarea class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full h-20" placeholder="Add any special instructions or notes..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancelRelease" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors">
                            Release Rewards
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create Policies Modal -->
        <div id="createPoliciesModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
            <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Create Compensation Policy</h3>
                    <button id="closePoliciesModal" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Policy Name</label>
                        <input type="text" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., Bonus Policy 2024">
                    </div>
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Policy Type</label>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="bonus">Bonus Policy</option>
                                <option value="commission">Commission Policy</option>
                                <option value="merit">Merit Increase Policy</option>
                                <option value="equity">Pay Equity Policy</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Effective Date</label>
                            <input type="date" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Policy Description</label>
                        <textarea class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full h-24" placeholder="Describe the policy details and guidelines..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancelPolicies" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white transition-colors">
                            Create Policy
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Merit Increase Modal -->
        <div id="meritIncreaseModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
            <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Merit Increase Management</h3>
                    <button id="closeMeritModal" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Employee</label>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="">Select Employee</option>
                                <option value="emp001">John Doe</option>
                                <option value="emp002">Sarah Johnson</option>
                                <option value="emp003">Mike Wilson</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Performance Rating</label>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="5">5 - Outstanding</option>
                                <option value="4">4 - Exceeds Expectations</option>
                                <option value="3">3 - Meets Expectations</option>
                                <option value="2">2 - Needs Improvement</option>
                                <option value="1">1 - Unsatisfactory</option>
                            </select>
                        </div>
                    </div>
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Current Salary</label>
                            <input type="number" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="₱">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Proposed Increase %</label>
                            <input type="number" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., 5.5">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 text-sm">Justification</label>
                        <textarea class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full h-20" placeholder="Provide justification for the merit increase..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancelMerit" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition-colors">
                            Submit Increase
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php include 'modals/policies_modal.php' ?>

        <script>
            lucide.createIcons();

            // Initialize Charts
            const performanceCompensationCtx = document.getElementById('performanceCompensationChart').getContext('2d');
            const performanceCompensationChart = new Chart(performanceCompensationCtx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Employees',
                        data: [{
                                x: 3.2,
                                y: 22000
                            }, {
                                x: 4.1,
                                y: 28000
                            }, {
                                x: 4.8,
                                y: 35000
                            },
                            {
                                x: 3.5,
                                y: 24000
                            }, {
                                x: 4.3,
                                y: 32000
                            }, {
                                x: 4.6,
                                y: 38000
                            },
                            {
                                x: 3.8,
                                y: 26000
                            }, {
                                x: 4.4,
                                y: 34000
                            }, {
                                x: 4.9,
                                y: 42000
                            }
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

            // Policy CRUD Operations
            async function loadPolicies() {
                try {
                    const response = await fetch('compensation_policies.php?action=get_policies');
                    const data = await response.json();
                    updatePoliciesTable(data);
                } catch (error) {
                    console.error('Error loading policies:', error);
                }
            }

            function openCreatePolicyModal() {
                document.getElementById('modalTitle').textContent = 'Create Compensation Policy';
                document.getElementById('policyForm').reset();
                document.getElementById('policyId').value = '';
                document.getElementById('policyModal').classList.remove('hidden');
            }

            async function editPolicy(id) {
                try {
                    const response = await fetch(`compensation_policies.php?action=get_policy&id=${id}`);
                    const policy = await response.json();

                    document.getElementById('modalTitle').textContent = 'Edit Compensation Policy';
                    document.getElementById('policyId').value = policy.id;
                    document.getElementById('policyName').value = policy.policy_name;
                    document.getElementById('policyType').value = policy.policy_type;
                    document.getElementById('version').value = policy.version;
                    document.getElementById('effectiveDate').value = policy.effective_date;
                    document.getElementById('description').value = policy.description;
                    document.getElementById('status').value = policy.status;
                    document.getElementById('complianceRate').value = policy.compliance_rate;

                    document.getElementById('policyModal').classList.remove('hidden');
                } catch (error) {
                    console.error('Error loading policy:', error);
                    alert('Failed to load policy details');
                }
            }

            async function viewPolicy(id) {
                try {
                    const response = await fetch(`compensation_policies.php?action=get_policy&id=${id}`);
                    const policy = await response.json();

                    document.getElementById('viewPolicyTitle').textContent = policy.policy_name;

                    const details = `
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="gap-4 grid grid-cols-2">
                    <div>
                        <p class="text-gray-500 text-sm">Type</p>
                        <p class="font-medium">${getPolicyTypeLabel(policy.policy_type)}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Version</p>
                        <p class="font-medium">${policy.version}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Effective Date</p>
                        <p class="font-medium">${new Date(policy.effective_date).toLocaleDateString()}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(policy.status)}">
                            ${policy.status}
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <p class="mb-2 text-gray-500 text-sm">Description</p>
                <p class="text-gray-700">${policy.description}</p>
            </div>
            <div>
                <p class="mb-2 text-gray-500 text-sm">Compliance Rate</p>
                <div class="flex items-center">
                    <div class="bg-gray-200 mr-2 rounded-full w-full h-2">
                        <div class="bg-green-600 rounded-full h-2" style="width: ${policy.compliance_rate}%"></div>
                    </div>
                    <span class="font-medium text-sm">${policy.compliance_rate}%</span>
                </div>
            </div>
            <div>
                <p class="mb-2 text-gray-500 text-sm">Created</p>
                <p class="text-gray-700">By ${policy.created_by_name} on ${new Date(policy.created_at).toLocaleDateString()}</p>
            </div>
        `;

                    document.getElementById('policyDetails').innerHTML = details;
                    document.getElementById('viewPolicyModal').classList.remove('hidden');
                } catch (error) {
                    console.error('Error viewing policy:', error);
                    alert('Failed to load policy details');
                }
            }

            function getPolicyTypeLabel(type) {
                const labels = {
                    'bonus': 'Bonus Policy',
                    'commission': 'Commission Policy',
                    'merit': 'Merit Increase Policy',
                    'equity': 'Pay Equity Policy',
                    'allowance': 'Allowance Policy'
                };
                return labels[type] || type;
            }

            function getStatusClass(status) {
                const classes = {
                    'active': 'bg-green-100 text-green-800',
                    'inactive': 'bg-red-100 text-red-800',
                    'draft': 'bg-gray-100 text-gray-800',
                    'under_review': 'bg-yellow-100 text-yellow-800'
                };
                return classes[status] || 'bg-gray-100 text-gray-800';
            }

            async function deletePolicy(id) {
                if (!confirm('Are you sure you want to delete this policy?')) return;

                try {
                    const response = await fetch('compensation_policies.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `action=delete&id=${id}`
                    });

                    const result = await response.json();
                    if (result.success) {
                        alert('Policy deleted successfully!');
                        loadPolicies(); // Reload the table
                    } else {
                        alert('Failed to delete policy: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error deleting policy:', error);
                    alert('Failed to delete policy');
                }
            }

            function uploadDocument(policyId) {
                document.getElementById('uploadPolicyId').value = policyId;
                document.getElementById('uploadDocumentModal').classList.remove('hidden');
            }

            function closeUploadModal() {
                document.getElementById('uploadDocumentModal').classList.add('hidden');
                document.getElementById('documentUploadForm').reset();
            }

            function closeViewModal() {
                document.getElementById('viewPolicyModal').classList.add('hidden');
            }

            // Handle form submissions
            document.getElementById('policyForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('action', document.getElementById('policyId').value ? 'update' : 'create');

                try {
                    const response = await fetch('compensation_policies.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        alert(result.message);
                        document.getElementById('policyModal').classList.add('hidden');
                        loadPolicies(); // Reload the table
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error saving policy:', error);
                    alert('Failed to save policy');
                }
            });

            document.getElementById('documentUploadForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('action', 'upload_document');
                formData.append('policy_id', document.getElementById('uploadPolicyId').value);

                try {
                    const response = await fetch('compensation_policies.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        alert(result.message);
                        closeUploadModal();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error uploading document:', error);
                    alert('Failed to upload document');
                }
            });

            // Search functionality
            document.getElementById('searchPolicies').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#policiesTable tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            // Update modal event listeners
            document.getElementById('createPolicyBtn').addEventListener('click', openCreatePolicyModal);
            document.getElementById('closePolicyModal').addEventListener('click', () => {
                document.getElementById('policyModal').classList.add('hidden');
            });
            document.getElementById('cancelPolicy').addEventListener('click', () => {
                document.getElementById('policyModal').classList.add('hidden');
            });

            // Close modals when clicking outside
            document.getElementById('policyModal').addEventListener('click', (e) => {
                if (e.target === document.getElementById('policyModal')) {
                    document.getElementById('policyModal').classList.add('hidden');
                }
            });

            document.getElementById('viewPolicyModal').addEventListener('click', (e) => {
                if (e.target === document.getElementById('viewPolicyModal')) {
                    closeViewModal();
                }
            });

            document.getElementById('uploadDocumentModal').addEventListener('click', (e) => {
                if (e.target === document.getElementById('uploadDocumentModal')) {
                    closeUploadModal();
                }
            });
        </script>
</body>

</html>