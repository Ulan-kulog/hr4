<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Logs - User Management</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .status-success {
            color: #10b981;
            background-color: #ecfdf5;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .table-container {
            max-height: calc(100vh - 250px);
            overflow-y: auto;
        }

        .table-container::-webkit-scrollbar {
            width: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
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
                <!-- Department Logs Section -->
                <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="font-bold text-gray-800 text-2xl">Department Logs</h1>
                            <p class="mt-1 text-gray-500">Total 94 log entries</p>
                        </div>
                    </div>

                    <!-- Logs Table -->
                    <div class="table-container">
                        <table class="w-full text-gray-700 text-sm text-left">
                            <thead class="top-0 sticky bg-gray-50/80 text-gray-500 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3 font-medium">LOG ID</th>
                                    <th class="px-4 py-3 font-medium">DEPARTMENT</th>
                                    <th class="px-4 py-3 font-medium">EMPLOYEE</th>
                                    <th class="px-4 py-3 font-medium">TYPE</th>
                                    <th class="px-4 py-3 font-medium">STATUS</th>
                                    <th class="px-4 py-3 font-medium">DETAILS</th>
                                    <th class="px-4 py-3 font-medium">DATE</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-medium">#216</td>
                                    <td class="px-4 py-3">C22510</td>
                                    <td class="px-4 py-3">Daniel Jonathan (ID: SU251001)</td>
                                    <td class="px-4 py-3">-q Login</td>
                                    <td class="px-4 py-3">
                                        <span class="status-success">Success</span>
                                    </td>
                                    <td class="flex items-center px-4 py-3">
                                        <span class="mr-1">💶</span> Login Successful
                                    </td>
                                    <td class="px-4 py-3">Sep 12, 2025 09:51:32</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-medium">#215</td>
                                    <td class="px-4 py-3">C22510</td>
                                    <td class="px-4 py-3">Daniel Jonathan (ID: SU251001)</td>
                                    <td class="px-4 py-3">-q Login</td>
                                    <td class="px-4 py-3">
                                        <span class="status-success">Success</span>
                                    </td>
                                    <td class="flex items-center px-4 py-3">
                                        <span class="mr-1">💶</span> Login Successful
                                    </td>
                                    <td class="px-4 py-3">Sep 10, 2025 17:32:15</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-medium">#214</td>
                                    <td class="px-4 py-3">C22510</td>
                                    <td class="px-4 py-3">Daniel Jonathan (ID: SU251001)</td>
                                    <td class="px-4 py-3">-q Login</td>
                                    <td class="px-4 py-3">
                                        <span class="status-success">Success</span>
                                    </td>
                                    <td class="flex items-center px-4 py-3">
                                        <span class="mr-1">💶</span> Login Successful
                                    </td>
                                    <td class="px-4 py-3">Sep 8, 2025 10:26:35</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-medium">#213</td>
                                    <td class="px-4 py-3">C22510</td>
                                    <td class="px-4 py-3">Justine James Nash (ID: SD251003)</td>
                                    <td class="px-4 py-3">-q Login</td>
                                    <td class="px-4 py-3">
                                        <span class="status-success">Success</span>
                                    </td>
                                    <td class="flex items-center px-4 py-3">
                                        <span class="mr-1">💶</span> Login Successful
                                    </td>
                                    <td class="px-4 py-3">Sep 8, 2025 06:13:05</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-medium">#212</td>
                                    <td class="px-4 py-3">C22510</td>
                                    <td class="px-4 py-3">Justine James Nash (ID: SD251003)</td>
                                    <td class="px-4 py-3">-q Login</td>
                                    <td class="px-4 py-3">
                                        <span class="status-success">Success</span>
                                    </td>
                                    <td class="flex items-center px-4 py-3">
                                        <span class="mr-1">💶</span> Login Successful
                                    </td>
                                    <td class="px-4 py-3">Sep 8, 2025 07:51:31</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-medium">#211</td>
                                    <td class="px-4 py-3">C22510</td>
                                    <td class="px-4 py-3">Justine James Nash (ID: SD251003)</td>
                                    <td class="px-4 py-3">-q Login</td>
                                    <td class="px-4 py-3">
                                        <span class="status-success">Success</span>
                                    </td>
                                    <td class="flex items-center px-4 py-3">
                                        <span class="mr-1">💶</span> Login Successful
                                    </td>
                                    <td class="px-4 py-3">Sep 8, 2025 05:44:56</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-medium">#210</td>
                                    <td class="px-4 py-3">C22510</td>
                                    <td class="px-4 py-3">Jasmine Keith (ID: S251005)</td>
                                    <td class="px-4 py-3">-q Login</td>
                                    <td class="px-4 py-3">
                                        <span class="status-success">Success</span>
                                    </td>
                                    <td class="flex items-center px-4 py-3">
                                        <span class="mr-1">💶</span> Login Successful
                                    </td>
                                    <td class="px-4 py-3">Sep 8, 2025 05:38:48</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-medium">#209</td>
                                    <td class="px-4 py-3">C22510</td>
                                    <td class="px-4 py-3">Mikel (ID: S251007)</td>
                                    <td class="px-4 py-3">-q Login</td>
                                    <td class="px-4 py-3">
                                        <span class="status-success">Success</span>
                                    </td>
                                    <td class="flex items-center px-4 py-3">
                                        <span class="mr-1">💶</span> Login Successful
                                    </td>
                                    <td class="px-4 py-3">Sep 8, 2025 05:37:30</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-medium">#208</td>
                                    <td class="px-4 py-3">C22510</td>
                                    <td class="px-4 py-3">Mikel (ID: S251007)</td>
                                    <td class="px-4 py-3">-q Login</td>
                                    <td class="px-4 py-3">
                                        <span class="status-success">Success</span>
                                    </td>
                                    <td class="flex items-center px-4 py-3">
                                        <span class="mr-1">💶</span> Login Successful
                                    </td>
                                    <td class="px-4 py-3">Sep 8, 2025 05:36:39</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-6">
                        <div class="text-gray-500 text-sm">
                            Showing 1 to 9 of 94 entries
                        </div>
                        <div class="flex space-x-2">
                            <button class="flex justify-center items-center hover:bg-gray-50 border border-gray-300 rounded-lg w-10 h-10 transition-colors">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </button>
                            <button class="flex justify-center items-center bg-blue-600 rounded-lg w-10 h-10 text-white">1</button>
                            <button class="flex justify-center items-center hover:bg-gray-50 border border-gray-300 rounded-lg w-10 h-10 transition-colors">2</button>
                            <button class="flex justify-center items-center hover:bg-gray-50 border border-gray-300 rounded-lg w-10 h-10 transition-colors">3</button>
                            <button class="flex justify-center items-center hover:bg-gray-50 border border-gray-300 rounded-lg w-10 h-10 transition-colors">4</button>
                            <button class="flex justify-center items-center hover:bg-gray-50 border border-gray-300 rounded-lg w-10 h-10 transition-colors">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </button>
                        </div>
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