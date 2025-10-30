<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Human Capital | HR Management System</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-color: #001f54;
            --accent-color: #F7B32B;
            --accent-light: #F7B32B1A;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
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
            <!-- Page Content -->
            <main class="p-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Employees Card -->
                    <div class="p-5 rounded-xl shadow-lg border border-gray-100 bg-white stats-card transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium" style="color:#001f54;">Total Employees</p>
                                <h3 class="text-3xl font-bold mt-1 text-gray-800">247</h3>
                            </div>
                            <div class="p-3 rounded-full" style="background:#F7B32B1A; color:#F7B32B;">
                                <i data-lucide="users" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="background:#F7B32B; width: 100%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Active employees</span>
                                <span class="font-medium">247 staff</span>
                            </div>
                        </div>
                    </div>

                    <!-- New Hires Card -->
                    <div class="p-5 rounded-xl shadow-lg border border-gray-100 bg-white stats-card transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium" style="color:#001f54;">New Hires</p>
                                <h3 class="text-3xl font-bold mt-1 text-gray-800">18</h3>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i data-lucide="user-plus" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full" style="width: 72%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>This month</span>
                                <span class="font-medium">18 hires</span>
                            </div>
                        </div>
                    </div>

                    <!-- Departments Card -->
                    <div class="p-5 rounded-xl shadow-lg border border-gray-100 bg-white stats-card transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium" style="color:#001f54;">Departments</p>
                                <h3 class="text-3xl font-bold mt-1 text-gray-800">12</h3>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i data-lucide="building" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500 rounded-full" style="width: 80%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Active departments</span>
                                <span class="font-medium">12 units</span>
                            </div>
                        </div>
                    </div>

                    <!-- Training Required Card -->
                    <div class="p-5 rounded-xl shadow-lg border border-gray-100 bg-white stats-card transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium" style="color:#001f54;">Training Required</p>
                                <h3 class="text-3xl font-bold mt-1 text-gray-800">32</h3>
                            </div>
                            <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                                <i data-lucide="award" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 rounded-full" style="width: 64%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Pending certifications</span>
                                <span class="font-medium">32 staff</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 mb-8">
                    <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                        Add New Employee
                    </button>
                    <button class="flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                        Import Data
                    </button>
                    <button class="flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                        Export Report
                    </button>
                    <button class="flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                        Filter Employees
                    </button>
                </div>

                <!-- Employee Table -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-800">Employee Directory</h3>
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                            <input type="text" placeholder="Search employees..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Employee</th>
                                    <th class="px-6 py-3">Position</th>
                                    <th class="px-6 py-3">Department</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Join Date</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=Maria+Garcia&background=001f54&color=fff" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">Maria Garcia</div>
                                                <div class="text-sm text-gray-500">ID: EMP-001</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Restaurant Server</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Food & Beverage</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        15 Mar 2022
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="#" class="text-gray-600 hover:text-gray-900">Edit</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=James+Wilson&background=001f54&color=fff" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">James Wilson</div>
                                                <div class="text-sm text-gray-500">ID: EMP-002</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Front Desk Agent</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Reception</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        22 Jan 2023
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="#" class="text-gray-600 hover:text-gray-900">Edit</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=Sarah+Chen&background=001f54&color=fff" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">Sarah Chen</div>
                                                <div class="text-sm text-gray-500">ID: EMP-003</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Sous Chef</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Kitchen</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            On Leave
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        05 Nov 2021
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="#" class="text-gray-600 hover:text-gray-900">Edit</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">247</span> results
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 rounded border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </button>
                            <button class="px-3 py-1 rounded border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

            <script src="../JAVASCRIPT/sidebar.js"></script>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Mobile menu toggle
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>