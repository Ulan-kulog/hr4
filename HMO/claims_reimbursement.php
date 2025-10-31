<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claims & Reimbursement Processing</title>
        <?php include '../INCLUDES/header.php'; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
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
        <div class="flex-1 flex flex-col overflow-auto">
            <!-- Navbar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-bold text-gray-800">Claims & Reimbursement Processing</h1>
                    <div class="flex items-center space-x-4">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            New Claim
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">Total Claims</p>
                                <h3 class="text-3xl font-bold mt-1">1,842</h3>
                                <p class="text-xs text-gray-500 mt-1">This quarter</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="file-text" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">Pending</p>
                                <h3 class="text-3xl font-bold mt-1">156</h3>
                                <p class="text-xs text-gray-500 mt-1">For review</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="clock" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">Approved</p>
                                <h3 class="text-3xl font-bold mt-1">₱2.8M</h3>
                                <p class="text-xs text-gray-500 mt-1">Total amount</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">Avg Processing</p>
                                <h3 class="text-3xl font-bold mt-1">3.2</h3>
                                <p class="text-xs text-gray-500 mt-1">Days</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="zap" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Claims Overview -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Claims by Status</h3>
                        <canvas id="claimsStatusChart"></canvas>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Monthly Claims Trend</h3>
                        <canvas id="claimsTrendChart"></canvas>
                    </div>
                </div>

                <!-- Claims Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-semibold text-gray-800">Recent Claims</h3>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Search claims..." class="px-4 py-2 border border-gray-300 rounded-lg">
                            <select class="px-4 py-2 border border-gray-300 rounded-lg">
                                <option>All Status</option>
                                <option>Pending</option>
                                <option>Approved</option>
                                <option>Rejected</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Claim ID</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Employee</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Provider</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Amount</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Date</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900">CLM-2024-001</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">Maria Johnson</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">St. Luke's Hospital</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">₱15,250</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">2024-01-15</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Approved</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <button class="text-blue-600 hover:text-blue-800">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                            <button class="text-blue-600 hover:text-blue-800">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- More rows would go here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        // Claims Status Chart
        const claimsStatusCtx = document.getElementById('claimsStatusChart').getContext('2d');
        new Chart(claimsStatusCtx, {
            type: 'pie',
            data: {
                labels: ['Approved', 'Pending', 'Rejected', 'Under Review'],
                datasets: [{
                    data: [65, 15, 12, 8],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#3b82f6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Claims Trend Chart
        const claimsTrendCtx = document.getElementById('claimsTrendChart').getContext('2d');
        new Chart(claimsTrendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Claims Count',
                    data: [120, 135, 148, 165, 142, 158, 175, 190, 168, 185, 195, 210],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>