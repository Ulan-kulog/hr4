<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMO Provider Network</title>
        <?php include '../INCLUDES/header.php'; ?>

    <script src="https://cdn.tailwindcss.com"></script>
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
                    <h1 class="text-2xl font-bold text-gray-800">HMO Provider Network</h1>
                    <div class="flex items-center space-x-4">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Add Provider
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
                                <p class="text-sm font-medium text-[#001f54]">Total Providers</p>
                                <h3 class="text-3xl font-bold mt-1">1,248</h3>
                                <p class="text-xs text-gray-500 mt-1">Network wide</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="building-2" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">Hospitals</p>
                                <h3 class="text-3xl font-bold mt-1">42</h3>
                                <p class="text-xs text-gray-500 mt-1">In network</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="home" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">Clinics</p>
                                <h3 class="text-3xl font-bold mt-1">156</h3>
                                <p class="text-xs text-gray-500 mt-1">Primary care</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="stethoscope" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54]">Avg Rating</p>
                                <h3 class="text-3xl font-bold mt-1">4.2</h3>
                                <p class="text-xs text-gray-500 mt-1">Out of 5</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center">
                                <i data-lucide="star" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Provider Search and Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" placeholder="Search providers..." class="px-4 py-2 border border-gray-300 rounded-lg">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg">
                            <option>All Specialties</option>
                            <option>Cardiology</option>
                            <option>Dermatology</option>
                            <option>Pediatrics</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg">
                            <option>All Locations</option>
                            <option>Manila</option>
                            <option>Quezon City</option>
                            <option>Makati</option>
                        </select>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Search</button>
                    </div>
                </div>

                <!-- Provider Directory -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-semibold text-gray-800">Provider Directory</h3>
                        <div class="flex gap-2">
                            <button class="px-4 py-2 border border-gray-300 rounded-lg">Export</button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Provider Card -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-800">St. Luke's Medical Center</h4>
                                    <p class="text-sm text-gray-600">Quezon City</p>
                                </div>
                                <div class="flex items-center">
                                    <i data-lucide="star" class="w-4 h-4 text-yellow-500 fill-current"></i>
                                    <span class="text-sm ml-1">4.5</span>
                                </div>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                    (02) 8723-0101
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                                    279 E Rodriguez Sr. Ave
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">24/7 Emergency</span>
                                <div class="flex gap-2">
                                    <button class="text-blue-600 hover:text-blue-800">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- More provider cards would go here -->
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>