<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Compensation Planning</title>
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
                        <select id="timePeriodSelect" class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700">
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

                <!-- Compensation Analytics Dashboard -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Department-wise Analysis -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Department Cost Analysis</h3>
                        <canvas id="departmentCostChart"></canvas>
                    </div>

                    <!-- Budget vs Actual -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Budget vs Actual</h3>
                        <canvas id="budgetVsActualChart"></canvas>
                    </div>
                </div>

                <!-- Cost-to-Company Analysis -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Cost-to-Company Breakdown</h3>
                        <canvas id="costToCompanyChart"></canvas>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Compensation Ratio Trends</h3>
                        <canvas id="compRatioChart"></canvas>
                    </div>
                </div>

                <!-- Compensation Forecasting -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Salary Projection Models</h3>
                        <canvas id="salaryProjectionChart"></canvas>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Market Adjustment Forecasting</h3>
                        <canvas id="marketAdjustmentChart"></canvas>
                    </div>
                </div>

                <!-- Budget Impact & Scenario Planning -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Budget Impact Analysis</h3>
                        <canvas id="budgetImpactChart"></canvas>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Scenario Planning</h3>
                        <canvas id="scenarioPlanningChart"></canvas>
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
        
        // Initialize charts when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Department Cost Analysis Chart
            const departmentCostCtx = document.getElementById('departmentCostChart').getContext('2d');
            const departmentCostChart = new Chart(departmentCostCtx, {
                type: 'bar',
                data: {
                    labels: ['Hotel Operations', 'Restaurant & F&B', 'Support Departments', 'Management', 'Sales & Marketing'],
                    datasets: [{
                        label: 'Cost (₱M)',
                        data: [8.2, 6.8, 3.2, 2.5, 1.8],
                        backgroundColor: [
                            '#3b82f6', // blue
                            '#10b981', // green
                            '#8b5cf6', // purple
                            '#f59e0b', // amber
                            '#ef4444'  // red
                        ],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cost (₱ Millions)'
                            }
                        }
                    }
                }
            });
            
            // Budget vs Actual Chart
            const budgetVsActualCtx = document.getElementById('budgetVsActualChart').getContext('2d');
            const budgetVsActualChart = new Chart(budgetVsActualCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'Budget',
                            data: [18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Actual',
                            data: [17.5, 18.8, 19.5, 20.8, 21.2, 22.5, 23.8, 24.2, 25.5, 26.8, 27.2, 28.5],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Cost (₱ Millions)'
                            }
                        }
                    }
                }
            });
            
            // Cost-to-Company Breakdown Chart
            const costToCompanyCtx = document.getElementById('costToCompanyChart').getContext('2d');
            const costToCompanyChart = new Chart(costToCompanyCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Base Salary', 'Benefits', 'Bonuses', 'Allowances', 'Taxes', 'Other Costs'],
                    datasets: [{
                        data: [65, 15, 8, 5, 4, 3],
                        backgroundColor: [
                            '#3b82f6', // blue
                            '#10b981', // green
                            '#f59e0b', // amber
                            '#8b5cf6', // purple
                            '#ef4444', // red
                            '#6b7280'  // gray
                        ],
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
            
            // Compensation Ratio Trends Chart
            const compRatioCtx = document.getElementById('compRatioChart').getContext('2d');
            const compRatioChart = new Chart(compRatioCtx, {
                type: 'line',
                data: {
                    labels: ['Q1 2022', 'Q2 2022', 'Q3 2022', 'Q4 2022', 'Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023'],
                    datasets: [
                        {
                            label: 'Company Ratio',
                            data: [1.15, 1.12, 1.10, 1.09, 1.08, 1.07, 1.08, 1.08],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Industry Average',
                            data: [1.18, 1.16, 1.14, 1.13, 1.12, 1.11, 1.12, 1.12],
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Compensation Ratio'
                            }
                        }
                    }
                }
            });
            
            // Salary Projection Models Chart
            const salaryProjectionCtx = document.getElementById('salaryProjectionChart').getContext('2d');
            const salaryProjectionChart = new Chart(salaryProjectionCtx, {
                type: 'line',
                data: {
                    labels: ['2022', '2023', '2024', '2025', '2026'],
                    datasets: [
                        {
                            label: 'Current Trajectory',
                            data: [18.5, 19.8, 21.2, 22.7, 24.3],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Optimistic Scenario',
                            data: [18.5, 20.2, 22.1, 24.3, 26.8],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Conservative Scenario',
                            data: [18.5, 19.5, 20.5, 21.6, 22.7],
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Salary Cost (₱ Millions)'
                            }
                        }
                    }
                }
            });
            
            // Market Adjustment Forecasting Chart
            const marketAdjustmentCtx = document.getElementById('marketAdjustmentChart').getContext('2d');
            const marketAdjustmentChart = new Chart(marketAdjustmentCtx, {
                type: 'bar',
                data: {
                    labels: ['Front Desk', 'Housekeeping', 'Servers', 'Kitchen Staff', 'Management'],
                    datasets: [
                        {
                            label: 'Current Pay',
                            data: [25, 22, 18, 28, 45],
                            backgroundColor: '#3b82f6'
                        },
                        {
                            label: 'Market Rate',
                            data: [28, 24, 20, 32, 48],
                            backgroundColor: '#10b981'
                        },
                        {
                            label: 'Required Adjustment',
                            data: [3, 2, 2, 4, 3],
                            backgroundColor: '#f59e0b',
                            type: 'line',
                            borderColor: '#f59e0b',
                            borderWidth: 2,
                            pointRadius: 4,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Hourly Rate (₱)'
                            }
                        }
                    }
                }
            });
            
            // Budget Impact Analysis Chart
            const budgetImpactCtx = document.getElementById('budgetImpactChart').getContext('2d');
            const budgetImpactChart = new Chart(budgetImpactCtx, {
                type: 'bar',
                data: {
                    labels: ['No Change', '3% Increase', '5% Increase', 'Market Alignment', 'Performance Based'],
                    datasets: [{
                        label: 'Budget Impact (₱M)',
                        data: [0, 0.65, 1.08, 1.42, 0.85],
                        backgroundColor: [
                            '#6b7280', // gray
                            '#3b82f6', // blue
                            '#10b981', // green
                            '#f59e0b', // amber
                            '#8b5cf6'  // purple
                        ],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Additional Cost (₱ Millions)'
                            }
                        }
                    }
                }
            });
            
            // Scenario Planning Chart
            const scenarioPlanningCtx = document.getElementById('scenarioPlanningChart').getContext('2d');
            const scenarioPlanningChart = new Chart(scenarioPlanningCtx, {
                type: 'radar',
                data: {
                    labels: ['Cost Efficiency', 'Market Competitiveness', 'Employee Retention', 'Budget Alignment', 'Performance Impact', 'Compliance Risk'],
                    datasets: [
                        {
                            label: 'Current Plan',
                            data: [75, 65, 70, 80, 60, 85],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            pointBackgroundColor: '#3b82f6'
                        },
                        {
                            label: 'Optimized Plan',
                            data: [80, 85, 90, 75, 85, 80],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            pointBackgroundColor: '#10b981'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
            
            // Time period selector functionality
            const timePeriodSelect = document.getElementById('timePeriodSelect');
            timePeriodSelect.addEventListener('change', function() {
                // In a real application, this would update all charts with new data
                // For this demo, we'll just show a simple alert
                alert(`Time period changed to: ${this.value}. In a real application, this would update all charts with new data.`);
            });
        });
    </script>
</body>
</html>