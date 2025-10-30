<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Modules - Compensation Planning</title>
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
            <!-- Employee Modules Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-teal-100/50 text-teal-600">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </span>
                        Employee-Facing Modules
                    </h2>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <i data-lucide="message-circle" class="w-4 h-4 mr-2"></i>
                        Employee Communications
                    </button>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Portal Usage -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Portal Active Users</p>
                                <h3 class="text-3xl font-bold mt-1">89%</h3>
                                <p class="text-xs text-gray-500 mt-1">Monthly active</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="users" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Document Access -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Documents Accessed</p>
                                <h3 class="text-3xl font-bold mt-1">1,248</h3>
                                <p class="text-xs text-gray-500 mt-1">This month</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="file-text" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Satisfaction Score -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Satisfaction Score</p>
                                <h3 class="text-3xl font-bold mt-1">4.2/5</h3>
                                <p class="text-xs text-gray-500 mt-1">Compensation clarity</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="star" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Inquiries Resolved -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Inquiries Resolved</p>
                                <h3 class="text-3xl font-bold mt-1">156</h3>
                                <p class="text-xs text-gray-500 mt-1">This month</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="help-circle" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Self-Service Features -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <div class="p-3 rounded-lg bg-blue-100 text-blue-600 inline-flex mb-4">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Pay Slip Access</h3>
                        <p class="text-sm text-gray-600 mb-4">Digital pay slips with detailed breakdown</p>
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm transition-colors">
                            Access Portal
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <div class="p-3 rounded-lg bg-green-100 text-green-600 inline-flex mb-4">
                            <i data-lucide="gift" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Bonus Tracker</h3>
                        <p class="text-sm text-gray-600 mb-4">Real-time bonus and commission tracking</p>
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm transition-colors">
                            View Bonuses
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <div class="p-3 rounded-lg bg-purple-100 text-purple-600 inline-flex mb-4">
                            <i data-lucide="calculator" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Total Rewards</h3>
                        <p class="text-sm text-gray-600 mb-4">Complete compensation statement</p>
                        <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-sm transition-colors">
                            View Statement
                        </button>
                    </div>
                </div>

                <!-- Communication Center -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Compensation Communications</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-blue-100 text-blue-600">
                                    <i data-lucide="megaphone" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">2025 Compensation Changes</p>
                                    <p class="text-xs text-gray-500">Published: Dec 1, 2024</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">New</span>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-green-100 text-green-600">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Bonus Policy Update</p>
                                    <p class="text-xs text-gray-500">Published: Nov 15, 2024</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Viewed</span>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-purple-100 text-purple-600">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Compensation FAQ</p>
                                    <p class="text-xs text-gray-500">Updated: Nov 10, 2024</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Viewed</span>
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