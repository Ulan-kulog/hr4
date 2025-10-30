<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospitality Modules - Compensation Planning</title>
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
            <!-- Hospitality Modules Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-red-100/50 text-red-600">
                            <i data-lucide="utensils" class="w-5 h-5"></i>
                        </span>
                        Hospitality-Specific Modules
                    </h2>
                    <div class="flex gap-2">
                        <button id="serviceChargeReviewBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="file-search" class="w-4 h-4 mr-2"></i>
                            Service Charge Review
                        </button>
                        <button id="serviceChargeDistributionBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="dollar-sign" class="w-4 h-4 mr-2"></i>
                            Service Charge Distribution
                        </button>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                            Configure Tips
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Tips Collected -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Tips Collected</p>
                                <h3 class="text-3xl font-bold mt-1">₱1.8M</h3>
                                <p class="text-xs text-gray-500 mt-1">This year</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="heart" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Service Charge -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Service Charge</p>
                                <h3 class="text-3xl font-bold mt-1">₱2.3M</h3>
                                <p class="text-xs text-gray-500 mt-1">YTD collection</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="credit-card" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Avg Tip per Employee -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Avg. Tip/Employee</p>
                                <h3 class="text-3xl font-bold mt-1">₱8.2K</h3>
                                <p class="text-xs text-gray-500 mt-1">Monthly average</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="users" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Overtime Hours -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Overtime Hours</p>
                                <h3 class="text-3xl font-bold mt-1">1,248</h3>
                                <p class="text-xs text-gray-500 mt-1">This month</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="clock" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tip & Service Charge Management -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Tip Distribution</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Servers</span>
                                <span class="text-sm font-medium text-gray-700">45% share</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Bartenders</span>
                                <span class="text-sm font-medium text-gray-700">25% share</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Kitchen Staff</span>
                                <span class="text-sm font-medium text-gray-700">20% share</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Support Staff</span>
                                <span class="text-sm font-medium text-gray-700">10% share</span>
                            </div>
                        </div>
                        <div class="mt-6">
                            <canvas id="tipDistributionChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Service Charge Allocation</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Direct Staff</span>
                                    <span class="font-medium">70%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 70%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Management</span>
                                    <span class="font-medium">15%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Reserve Fund</span>
                                    <span class="font-medium">15%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <canvas id="serviceChargeChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Monthly Tips & Service Charge</h3>
                        <canvas id="monthlyComparisonChart"></canvas>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Overtime Distribution</h3>
                        <canvas id="overtimeChart"></canvas>
                    </div>
                </div>

                <!-- Shift Premiums -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Shift Differential Rates</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 mb-1">+15%</div>
                            <div class="text-sm text-gray-600">Evening Shift</div>
                            <div class="text-xs text-gray-500">(4 PM - 12 AM)</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 mb-1">+25%</div>
                            <div class="text-sm text-gray-600">Night Shift</div>
                            <div class="text-xs text-gray-500">(12 AM - 8 AM)</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 mb-1">+50%</div>
                            <div class="text-sm text-gray-600">Holiday Rate</div>
                            <div class="text-xs text-gray-500">Public holidays</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Service Charge Review Modal -->
    <div id="serviceChargeReviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl mx-4">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Service Charge Review</h3>
                <button id="closeServiceChargeReviewModal" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-3">Review Period</h4>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-3">Service Charge Summary</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Total Service Charge</p>
                                <p class="text-lg font-bold">₱2,345,670</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Number of Transactions</p>
                                <p class="text-lg font-bold">12,456</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Average per Transaction</p>
                                <p class="text-lg font-bold">₱188.25</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Compliance Status</p>
                                <p class="text-lg font-bold text-green-600">100% Compliant</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Export Report</button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Generate Review</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Charge Distribution Modal -->
    <div id="serviceChargeDistributionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl mx-4">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Service Charge Distribution</h3>
                <button id="closeServiceChargeDistributionModal" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-3">Distribution Period</h4>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Distribution Date</label>
                            <input type="date" class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg" value="₱2,345,670" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-3">Distribution Rules</h4>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Direct Staff</span>
                            <div class="flex items-center gap-2">
                                <input type="number" class="w-20 p-2 border border-gray-300 rounded-lg" value="70">
                                <span>%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Management</span>
                            <div class="flex items-center gap-2">
                                <input type="number" class="w-20 p-2 border border-gray-300 rounded-lg" value="15">
                                <span>%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Reserve Fund</span>
                            <div class="flex items-center gap-2">
                                <input type="number" class="w-20 p-2 border border-gray-300 rounded-lg" value="15">
                                <span>%</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-3">Distribution Preview</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span>Direct Staff Allocation</span>
                                <span class="font-bold">₱1,641,969</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Management Allocation</span>
                                <span class="font-bold">₱351,850</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Reserve Fund</span>
                                <span class="font-bold">₱351,850</span>
                            </div>
                            <div class="border-t pt-2 mt-2 flex justify-between font-bold">
                                <span>Total</span>
                                <span>₱2,345,670</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Save as Template</button>
                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Process Distribution</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        // Modal functionality
        const serviceChargeReviewBtn = document.getElementById('serviceChargeReviewBtn');
        const serviceChargeDistributionBtn = document.getElementById('serviceChargeDistributionBtn');
        const serviceChargeReviewModal = document.getElementById('serviceChargeReviewModal');
        const serviceChargeDistributionModal = document.getElementById('serviceChargeDistributionModal');
        const closeServiceChargeReviewModal = document.getElementById('closeServiceChargeReviewModal');
        const closeServiceChargeDistributionModal = document.getElementById('closeServiceChargeDistributionModal');
        
        serviceChargeReviewBtn.addEventListener('click', () => {
            serviceChargeReviewModal.classList.remove('hidden');
        });
        
        serviceChargeDistributionBtn.addEventListener('click', () => {
            serviceChargeDistributionModal.classList.remove('hidden');
        });
        
        closeServiceChargeReviewModal.addEventListener('click', () => {
            serviceChargeReviewModal.classList.add('hidden');
        });
        
        closeServiceChargeDistributionModal.addEventListener('click', () => {
            serviceChargeDistributionModal.classList.add('hidden');
        });
        
        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === serviceChargeReviewModal) {
                serviceChargeReviewModal.classList.add('hidden');
            }
            if (e.target === serviceChargeDistributionModal) {
                serviceChargeDistributionModal.classList.add('hidden');
            }
        });
        
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Tip Distribution Chart (Doughnut)
            const tipDistributionCtx = document.getElementById('tipDistributionChart').getContext('2d');
            const tipDistributionChart = new Chart(tipDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Servers', 'Bartenders', 'Kitchen Staff', 'Support Staff'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: [
                            '#3b82f6', // blue
                            '#10b981', // green
                            '#f59e0b', // amber
                            '#8b5cf6'  // purple
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
            
            // Service Charge Chart (Doughnut)
            const serviceChargeCtx = document.getElementById('serviceChargeChart').getContext('2d');
            const serviceChargeChart = new Chart(serviceChargeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Direct Staff', 'Management', 'Reserve Fund'],
                    datasets: [{
                        data: [70, 15, 15],
                        backgroundColor: [
                            '#10b981', // green
                            '#3b82f6', // blue
                            '#8b5cf6'  // purple
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
            
            // Monthly Comparison Chart (Bar)
            const monthlyComparisonCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
            const monthlyComparisonChart = new Chart(monthlyComparisonCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'Tips',
                            data: [120000, 135000, 150000, 145000, 160000, 175000, 190000, 185000, 200000, 195000, 210000, 225000],
                            backgroundColor: '#3b82f6',
                            borderRadius: 4
                        },
                        {
                            label: 'Service Charge',
                            data: [150000, 165000, 180000, 175000, 190000, 205000, 220000, 215000, 230000, 225000, 240000, 255000],
                            backgroundColor: '#10b981',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + (value / 1000) + 'K';
                                }
                            }
                        }
                    }
                }
            });
            
            // Overtime Chart (Bar)
            const overtimeCtx = document.getElementById('overtimeChart').getContext('2d');
            const overtimeChart = new Chart(overtimeCtx, {
                type: 'bar',
                data: {
                    labels: ['Servers', 'Bartenders', 'Kitchen', 'Management', 'Support'],
                    datasets: [{
                        label: 'Overtime Hours',
                        data: [320, 180, 420, 150, 178],
                        backgroundColor: '#f59e0b',
                        borderRadius: 4
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
        });
    </script>
</body>
</html>