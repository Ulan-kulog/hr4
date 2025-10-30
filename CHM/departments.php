<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments - Core Human Capital</title>
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
            <!-- Departments Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-green-100/50 text-green-600">
                            <i data-lucide="building" class="w-5 h-5"></i>
                        </span>
                        Manage Departments
                    </h2>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Create New Department
                    </button>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-3 gap-4 mb-8">
                   

                    <!-- HR Department -->
                    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">HR Department</p>
                                <h3 class="text-2xl font-bold mt-1">12</h3>
                                <p class="text-xs text-gray-500 mt-1">Employees</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="users" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Logistic Department -->
                    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Logistic Department</p>
                                <h3 class="text-2xl font-bold mt-1">18</h3>
                                <p class="text-xs text-gray-500 mt-1">Employees</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="truck" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Administrative Department -->
                    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Administrative</p>
                                <h3 class="text-2xl font-bold mt-1">8</h3>
                                <p class="text-xs text-gray-500 mt-1">Employees</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="clipboard-list" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Financials Department -->
                    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Financials</p>
                                <h3 class="text-2xl font-bold mt-1">14</h3>
                                <p class="text-xs text-gray-500 mt-1">Employees</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="dollar-sign" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Hotel Department -->
                    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Hotel Department</p>
                                <h3 class="text-2xl font-bold mt-1">89</h3>
                                <p class="text-xs text-gray-500 mt-1">Employees</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="home" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Restaurant Department -->
                    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Restaurant</p>
                                <h3 class="text-2xl font-bold mt-1">74</h3>
                                <p class="text-xs text-gray-500 mt-1">Employees</p>
                            </div>
                            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="utensils" class="w-5 h-5 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Management Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Department List</h3>
                        <div class="flex gap-2 mt-2 sm:mt-0">
                            <input type="text" placeholder="Search departments..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-64">
                            <button class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Department Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- HR Department Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-800">HR Department</h3>
                                <span class="p-2 rounded-lg bg-pink-100 text-pink-600">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Human resources management, recruitment, employee relations</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-gray-800">12 Employees</span>
                                <span class="text-sm text-green-600 font-medium">Active</span>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    View
                                </button>
                                <button class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Edit
                                </button>
                            </div>
                        </div>

                        <!-- Logistic Department Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-800">Logistic Department</h3>
                                <span class="p-2 rounded-lg bg-orange-100 text-orange-600">
                                    <i data-lucide="truck" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Supply chain, delivery, inventory management, transportation</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-gray-800">18 Employees</span>
                                <span class="text-sm text-green-600 font-medium">Active</span>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    View
                                </button>
                                <button class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Edit
                                </button>
                            </div>
                        </div>

                        <!-- Administrative Department Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-800">Administrative Department</h3>
                                <span class="p-2 rounded-lg bg-purple-100 text-purple-600">
                                    <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Office administration, documentation, support services</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-gray-800">8 Employees</span>
                                <span class="text-sm text-green-600 font-medium">Active</span>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    View
                                </button>
                                <button class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Edit
                                </button>
                            </div>
                        </div>

                        <!-- Financials Department Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-800">Financials Department</h3>
                                <span class="p-2 rounded-lg bg-green-100 text-green-600">
                                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Accounting, finance, payroll, budgeting and financial reporting</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-gray-800">14 Employees</span>
                                <span class="text-sm text-green-600 font-medium">Active</span>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    View
                                </button>
                                <button class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Edit
                                </button>
                            </div>
                        </div>

                        <!-- Hotel Department Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-800">Hotel Department</h3>
                                <span class="p-2 rounded-lg bg-blue-100 text-blue-600">
                                    <i data-lucide="home" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Front desk, housekeeping, concierge, guest services</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-gray-800">89 Employees</span>
                                <span class="text-sm text-green-600 font-medium">Active</span>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    View
                                </button>
                                <button class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Edit
                                </button>
                            </div>
                        </div>

                        <!-- Restaurant Department Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-800">Restaurant Department</h3>
                                <span class="p-2 rounded-lg bg-red-100 text-red-600">
                                    <i data-lucide="utensils" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Food & Beverage operations, kitchen staff, servers, bar</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-gray-800">74 Employees</span>
                                <span class="text-sm text-green-600 font-medium">Active</span>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    View
                                </button>
                                <button class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Edit
                                </button>
                            </div>
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