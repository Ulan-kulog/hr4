<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Analytics Dashboard - Hospitality</title>
        <?php include '../INCLUDES/header.php'; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
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
                <!-- HR Analytics Dashboard -->
                <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="p-2 mr-3 rounded-lg bg-indigo-100/50 text-indigo-600">
                                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            </span>
                            HR Analytics Dashboard
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
                    
                    <!-- Key HR Metrics -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Employees -->
                        <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Total Employees</p>
                                    <h3 class="text-3xl font-bold mt-1">428</h3>
                                    <p class="text-xs text-gray-500 mt-1">+12 this month</p>
                                </div>
                                <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                    <i data-lucide="users" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Turnover Rate -->
                        <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Turnover Rate</p>
                                    <h3 class="text-3xl font-bold mt-1">18.2%</h3>
                                    <p class="text-xs text-gray-500 mt-1">-2.4% from last quarter</p>
                                </div>
                                <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                    <i data-lucide="repeat" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Avg Time to Hire -->
                        <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Avg Time to Hire</p>
                                    <h3 class="text-3xl font-bold mt-1">24 days</h3>
                                    <p class="text-xs text-gray-500 mt-1">Industry: 28 days</p>
                                </div>
                                <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                    <i data-lucide="clock" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Training Completion -->
                        <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Training Completion</p>
                                    <h3 class="text-3xl font-bold mt-1">87%</h3>
                                    <p class="text-xs text-gray-500 mt-1">+5% this quarter</p>
                                </div>
                                <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                    <i data-lucide="award" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Module 1: Core Human Capital Analytics -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <span class="p-2 mr-2 rounded-lg bg-blue-100 text-blue-600">
                                <i data-lucide="database" class="w-5 h-5"></i>
                            </span>
                            Core Human Capital Analytics
                        </h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Employee Distribution by Department</h4>
                                <canvas id="departmentDistributionChart"></canvas>
                            </div>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Certification Status</h4>
                                <canvas id="certificationChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Module 2: Payroll Management Analytics -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <span class="p-2 mr-2 rounded-lg bg-green-100 text-green-600">
                                <i data-lucide="credit-card" class="w-5 h-5"></i>
                            </span>
                            Payroll Management Analytics
                        </h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Labor Cost by Department</h4>
                                <canvas id="laborCostChart"></canvas>
                            </div>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Overtime Trends</h4>
                                <canvas id="overtimeTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Module 3: Compensation Planning Analytics -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <span class="p-2 mr-2 rounded-lg bg-purple-100 text-purple-600">
                                <i data-lucide="trending-up" class="w-5 h-5"></i>
                            </span>
                            Compensation Planning Analytics
                        </h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Salary vs Market Benchmark</h4>
                                <canvas id="salaryBenchmarkChart"></canvas>
                            </div>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Bonus Distribution</h4>
                                <canvas id="bonusDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Module 4: Hospitality-Specific Analytics -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <span class="p-2 mr-2 rounded-lg bg-red-100 text-red-600">
                                <i data-lucide="utensils" class="w-5 h-5"></i>
                            </span>
                            Hospitality-Specific Analytics
                        </h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Tip Distribution Analysis</h4>
                                <canvas id="tipDistributionChart"></canvas>
                            </div>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Guest Satisfaction vs Staff Performance</h4>
                                <canvas id="satisfactionPerformanceChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Module 5: HR Analytics Dashboard -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <span class="p-2 mr-2 rounded-lg bg-indigo-100 text-indigo-600">
                                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            </span>
                            HR Analytics Dashboard
                        </h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Turnover Analysis</h4>
                                <canvas id="turnoverAnalysisChart"></canvas>
                            </div>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Recruitment Funnel</h4>
                                <canvas id="recruitmentFunnelChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Additional HR Departments -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <!-- HR Department 1 -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="p-2 mr-2 rounded-lg bg-yellow-100 text-yellow-600">
                                    <i data-lucide="users" class="w-5 h-5"></i>
                                </span>
                                HR Department 1
                            </h3>
                            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                                <p class="text-gray-500">Department-specific analytics and charts will appear here</p>
                            </div>
                        </div>

                        <!-- HR Department 2 -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="p-2 mr-2 rounded-lg bg-pink-100 text-pink-600">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </span>
                                HR Department 2
                            </h3>
                            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                                <p class="text-gray-500">Department-specific analytics and charts will appear here</p>
                            </div>
                        </div>

                        <!-- HR Department 3 -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="p-2 mr-2 rounded-lg bg-teal-100 text-teal-600">
                                    <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                                </span>
                                HR Department 3
                            </h3>
                            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                                <p class="text-gray-500">Department-specific analytics and charts will appear here</p>
                            </div>
                        </div>
                    </div>

                    <!-- Performance & Retention Analytics -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Performance & Retention Correlation</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <canvas id="performanceRetentionChart"></canvas>
                            </div>
                            <div>
                                <canvas id="tenureDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        // Initialize all charts when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Department Distribution Chart
            const departmentCtx = document.getElementById('departmentDistributionChart').getContext('2d');
            new Chart(departmentCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Front Desk', 'Housekeeping', 'F&B Service', 'Kitchen', 'Management', 'Support'],
                    datasets: [{
                        data: [22, 28, 25, 15, 5, 5],
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#6b7280'
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
            
            // Certification Status Chart
            const certificationCtx = document.getElementById('certificationChart').getContext('2d');
            new Chart(certificationCtx, {
                type: 'bar',
                data: {
                    labels: ['Food Handler', 'CPR/First Aid', 'Alcohol Service', 'Safety Training', 'Brand Standards'],
                    datasets: [{
                        label: 'Completion Rate',
                        data: [92, 85, 78, 95, 88],
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Completion %'
                            }
                        }
                    }
                }
            });
            
            // Labor Cost Chart
            const laborCostCtx = document.getElementById('laborCostChart').getContext('2d');
            new Chart(laborCostCtx, {
                type: 'bar',
                data: {
                    labels: ['Rooms', 'F&B', 'Kitchen', 'Events', 'Support'],
                    datasets: [{
                        label: 'Labor Cost (₱K)',
                        data: [120, 180, 150, 90, 60],
                        backgroundColor: '#10b981',
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
                                text: 'Cost (₱ Thousands)'
                            }
                        }
                    }
                }
            });
            
            // Overtime Trends Chart
            const overtimeCtx = document.getElementById('overtimeTrendsChart').getContext('2d');
            new Chart(overtimeCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Overtime Hours',
                        data: [1200, 1100, 1300, 1250, 1400, 1500, 1600, 1450, 1350, 1250, 1300, 1200],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Overtime Hours'
                            }
                        }
                    }
                }
            });
            
            // Salary Benchmark Chart
            const salaryBenchmarkCtx = document.getElementById('salaryBenchmarkChart').getContext('2d');
            new Chart(salaryBenchmarkCtx, {
                type: 'bar',
                data: {
                    labels: ['Server', 'Bartender', 'Cook', 'Housekeeper', 'Front Desk'],
                    datasets: [
                        {
                            label: 'Our Pay',
                            data: [25, 28, 30, 22, 26],
                            backgroundColor: '#8b5cf6'
                        },
                        {
                            label: 'Market Avg',
                            data: [28, 30, 32, 24, 28],
                            backgroundColor: '#6b7280'
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
            
            // Bonus Distribution Chart
            const bonusCtx = document.getElementById('bonusDistributionChart').getContext('2d');
            new Chart(bonusCtx, {
                type: 'pie',
                data: {
                    labels: ['Performance', 'Seasonal', 'Retention', 'Referral', 'Other'],
                    datasets: [{
                        data: [45, 25, 15, 10, 5],
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
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
            
            // Tip Distribution Chart
            const tipCtx = document.getElementById('tipDistributionChart').getContext('2d');
            new Chart(tipCtx, {
                type: 'bar',
                data: {
                    labels: ['Servers', 'Bartenders', 'Hosts', 'Bussers', 'Kitchen'],
                    datasets: [{
                        label: 'Avg Monthly Tips (₱K)',
                        data: [8.5, 7.2, 3.5, 4.2, 2.8],
                        backgroundColor: '#f59e0b',
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
                                text: 'Tips (₱ Thousands)'
                            }
                        }
                    }
                }
            });
            
            // Satisfaction vs Performance Chart
            const satisfactionCtx = document.getElementById('satisfactionPerformanceChart').getContext('2d');
            new Chart(satisfactionCtx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Front Desk',
                        data: [
                            {x: 4.2, y: 85}, {x: 4.5, y: 90}, {x: 4.0, y: 80}, 
                            {x: 4.8, y: 95}, {x: 4.3, y: 87}, {x: 4.6, y: 92}
                        ],
                        backgroundColor: '#3b82f6'
                    }, {
                        label: 'F&B Service',
                        data: [
                            {x: 4.1, y: 82}, {x: 4.4, y: 88}, {x: 4.7, y: 93}, 
                            {x: 4.0, y: 78}, {x: 4.3, y: 85}, {x: 4.5, y: 90}
                        ],
                        backgroundColor: '#10b981'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Guest Satisfaction (1-5)'
                            },
                            min: 3.5,
                            max: 5
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Performance Rating (%)'
                            },
                            min: 75,
                            max: 100
                        }
                    }
                }
            });
            
            // Turnover Analysis Chart
            const turnoverCtx = document.getElementById('turnoverAnalysisChart').getContext('2d');
            new Chart(turnoverCtx, {
                type: 'line',
                data: {
                    labels: ['Q1', 'Q2', 'Q3', 'Q4', 'Q1', 'Q2', 'Q3', 'Q4'],
                    datasets: [
                        {
                            label: 'Voluntary Turnover',
                            data: [22, 20, 18, 16, 15, 14, 13, 12],
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Involuntary Turnover',
                            data: [8, 7, 6, 5, 5, 4, 4, 3],
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.4,
                            fill: true
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
                                text: 'Turnover Rate (%)'
                            }
                        }
                    }
                }
            });
            
            // Recruitment Funnel Chart
            const recruitmentCtx = document.getElementById('recruitmentFunnelChart').getContext('2d');
            new Chart(recruitmentCtx, {
                type: 'bar',
                data: {
                    labels: ['Applicants', 'Screened', 'Interviewed', 'Offers', 'Hires'],
                    datasets: [{
                        label: 'Count',
                        data: [450, 180, 75, 35, 28],
                        backgroundColor: '#8b5cf6',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Performance & Retention Chart
            const performanceCtx = document.getElementById('performanceRetentionChart').getContext('2d');
            new Chart(performanceCtx, {
                type: 'bar',
                data: {
                    labels: ['Low Performers', 'Average', 'High Performers', 'Top Performers'],
                    datasets: [{
                        label: 'Retention Rate (%)',
                        data: [45, 72, 88, 94],
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Retention Rate (%)'
                            }
                        }
                    }
                }
            });
            
            // Tenure Distribution Chart
            const tenureCtx = document.getElementById('tenureDistributionChart').getContext('2d');
            new Chart(tenureCtx, {
                type: 'pie',
                data: {
                    labels: ['< 6 months', '6-12 months', '1-3 years', '3-5 years', '5+ years'],
                    datasets: [{
                        data: [18, 22, 35, 15, 10],
                        backgroundColor: [
                            '#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'
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
        });
    </script>
</body>
</html>