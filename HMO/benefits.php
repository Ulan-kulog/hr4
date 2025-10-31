<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benefits Analytics & Reporting</title>
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
                    <h1 class="text-2xl font-bold text-gray-800">Benefits Analytics & Reporting</h1>
                    <div class="flex items-center space-x-4">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                            Export Report
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
                                <p class="text-sm font-medium text-[#001f54]">Total Benefits Cost</p>
                                <h3 class="text-3xl font-bold mt-1">₱8.2M</h3>
                                <p class="text-xs text-gray-500 mt-1">Annual</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="dollar-sign" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">Utilization Rate</p>
                                <h3 class="text-3xl font-bold mt-1">72%</h3>
                                <p class="text-xs text-gray-500 mt-1">Of employees</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">Satisfaction Score</p>
                                <h3 class="text-3xl font-bold mt-1">4.3</h3>
                                <p class="text-xs text-gray-500 mt-1">Out of 5</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="heart" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">ROI</p>
                                <h3 class="text-3xl font-bold mt-1">2.8x</h3>
                                <p class="text-xs text-gray-500 mt-1">Return</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="bar-chart-3" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Benefits Cost Breakdown</h3>
                        <canvas id="costBreakdownChart"></canvas>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Utilization by Department</h3>
                        <canvas id="utilizationChart"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Claims Trend Analysis</h3>
                        <canvas id="claimsAnalysisChart"></canvas>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Employee Satisfaction</h3>
                        <canvas id="satisfactionChart"></canvas>
                    </div>
                </div>

                <!-- Detailed Reports -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Benefits Performance Metrics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 mb-1">18.5%</div>
                            <div class="text-sm text-gray-600">Cost Increase YoY</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 mb-1">+12%</div>
                            <div class="text-sm text-gray-600">Employee Retention</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 mb-1">94%</div>
                            <div class="text-sm text-gray-600">Claims Accuracy</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600 mb-1">2.1 Days</div>
                            <div class="text-sm text-gray-600">Avg Processing Time</div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        // Cost Breakdown Chart
        const costCtx = document.getElementById('costBreakdownChart').getContext('2d');
        new Chart(costCtx, {
            type: 'doughnut',
            data: {
                labels: ['HMO Premiums', 'Dental', 'Vision', 'Life Insurance', 'Disability'],
                datasets: [{
                    data: [65, 15, 8, 7, 5],
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
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

        // Utilization by Department Chart
        const utilizationCtx = document.getElementById('utilizationChart').getContext('2d');
        new Chart(utilizationCtx, {
            type: 'bar',
            data: {
                labels: ['Front Desk', 'Housekeeping', 'F&B Service', 'Kitchen', 'Management'],
                datasets: [{
                    label: 'Utilization Rate %',
                    data: [85, 78, 92, 65, 88],
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Claims Analysis Chart
        const claimsAnalysisCtx = document.getElementById('claimsAnalysisChart').getContext('2d');
        new Chart(claimsAnalysisCtx, {
            type: 'line',
            data: {
                labels: ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024'],
                datasets: [
                    {
                        label: 'Claims Count',
                        data: [450, 520, 480, 610, 580],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Claims Cost (₱K)',
                        data: [1200, 1450, 1350, 1650, 1580],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true
            }
        });

        // Satisfaction Chart
        const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
        new Chart(satisfactionCtx, {
            type: 'radar',
            data: {
                labels: ['Coverage', 'Provider Network', 'Claims Process', 'Customer Service', 'Cost Value', 'Ease of Use'],
                datasets: [{
                    label: 'Employee Satisfaction',
                    data: [4.5, 4.2, 4.0, 4.3, 4.1, 4.4],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                    pointBackgroundColor: '#8b5cf6'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5
                    }
                }
            }
        });
    </script>
</body>
</html>