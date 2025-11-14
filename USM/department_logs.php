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
            display: flex;
            align-items: center;
            gap: 4px;
            width: fit-content;
        }

        .status-warning {
            color: #f59e0b;
            background-color: #fffbeb;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
            width: fit-content;
        }

        .status-error {
            color: #ef4444;
            background-color: #fef2f2;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
            width: fit-content;
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

        .log-type-login {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #3b82f6;
        }

        .log-type-logout {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #6b7280;
        }

        .log-type-access {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #8b5cf6;
        }

        .log-type-security {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #f59e0b;
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
                <div class="bg-white shadow-sm border border-gray-200 rounded-xl glass-effect">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h1 class="flex items-center gap-2 font-bold text-gray-800 text-2xl">
                                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                                    Department Logs
                                </h1>
                                <p class="flex items-center gap-1 mt-1 text-gray-500">
                                    <i data-lucide="database" class="w-4 h-4"></i>
                                    Total 94 log entries
                                </p>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="gap-4 grid grid-cols-1 md:grid-cols-4 mb-6">
                            <div class="bg-blue-50 p-4 border border-blue-100 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-blue-600 text-sm">Total Logs</p>
                                        <p class="font-bold text-blue-800 text-2xl">94</p>
                                    </div>
                                    <div class="bg-blue-100 p-2 rounded-lg">
                                        <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 border border-green-100 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-green-600 text-sm">Successful</p>
                                        <p class="font-bold text-green-800 text-2xl">89</p>
                                    </div>
                                    <div class="bg-green-100 p-2 rounded-lg">
                                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-amber-50 p-4 border border-amber-100 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-amber-600 text-sm">Warnings</p>
                                        <p class="font-bold text-amber-800 text-2xl">3</p>
                                    </div>
                                    <div class="bg-amber-100 p-2 rounded-lg">
                                        <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-600"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-red-50 p-4 border border-red-100 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-red-600 text-sm">Errors</p>
                                        <p class="font-bold text-red-800 text-2xl">2</p>
                                    </div>
                                    <div class="bg-red-100 p-2 rounded-lg">
                                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logs Table -->
                        <div class="table-container border border-gray-200 rounded-lg">
                            <table class="w-full text-gray-700 text-sm text-left">
                                <thead class="top-0 sticky bg-gray-50/80 text-gray-500 text-xs uppercase">
                                    <tr>
                                        <th class="px-4 py-3 font-medium">
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="hash" class="w-4 h-4"></i>
                                                LOG ID
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="building" class="w-4 h-4"></i>
                                                DEPARTMENT
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="user" class="w-4 h-4"></i>
                                                EMPLOYEE
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="activity" class="w-4 h-4"></i>
                                                TYPE
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                                STATUS
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                                DETAILS
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                                DATE
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3 font-medium">#216</td>
                                        <td class="px-4 py-3">C22510</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex justify-center items-center bg-blue-100 rounded-full w-6 h-6">
                                                    <i data-lucide="user" class="w-3 h-3 text-blue-600"></i>
                                                </div>
                                                Daniel Jonathan (ID: SU251001)
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="log-type-login">
                                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                                Login
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-success">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                Success
                                            </span>
                                        </td>
                                        <td class="flex items-center px-4 py-3">
                                            <i data-lucide="log-in" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Login Successful
                                        </td>
                                        <td class="px-4 py-3">Sep 12, 2025 09:51:32</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3 font-medium">#215</td>
                                        <td class="px-4 py-3">C22510</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex justify-center items-center bg-blue-100 rounded-full w-6 h-6">
                                                    <i data-lucide="user" class="w-3 h-3 text-blue-600"></i>
                                                </div>
                                                Daniel Jonathan (ID: SU251001)
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="log-type-login">
                                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                                Login
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-success">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                Success
                                            </span>
                                        </td>
                                        <td class="flex items-center px-4 py-3">
                                            <i data-lucide="log-in" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Login Successful
                                        </td>
                                        <td class="px-4 py-3">Sep 10, 2025 17:32:15</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3 font-medium">#214</td>
                                        <td class="px-4 py-3">C22510</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex justify-center items-center bg-blue-100 rounded-full w-6 h-6">
                                                    <i data-lucide="user" class="w-3 h-3 text-blue-600"></i>
                                                </div>
                                                Daniel Jonathan (ID: SU251001)
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="log-type-login">
                                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                                Login
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-success">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                Success
                                            </span>
                                        </td>
                                        <td class="flex items-center px-4 py-3">
                                            <i data-lucide="log-in" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Login Successful
                                        </td>
                                        <td class="px-4 py-3">Sep 8, 2025 10:26:35</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3 font-medium">#213</td>
                                        <td class="px-4 py-3">C22510</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex justify-center items-center bg-purple-100 rounded-full w-6 h-6">
                                                    <i data-lucide="user" class="w-3 h-3 text-purple-600"></i>
                                                </div>
                                                Justine James Nash (ID: SD251003)
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="log-type-login">
                                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                                Login
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-success">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                Success
                                            </span>
                                        </td>
                                        <td class="flex items-center px-4 py-3">
                                            <i data-lucide="log-in" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Login Successful
                                        </td>
                                        <td class="px-4 py-3">Sep 8, 2025 06:13:05</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3 font-medium">#212</td>
                                        <td class="px-4 py-3">C22510</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex justify-center items-center bg-purple-100 rounded-full w-6 h-6">
                                                    <i data-lucide="user" class="w-3 h-3 text-purple-600"></i>
                                                </div>
                                                Justine James Nash (ID: SD251003)
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="log-type-login">
                                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                                Login
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-success">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                Success
                                            </span>
                                        </td>
                                        <td class="flex items-center px-4 py-3">
                                            <i data-lucide="log-in" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Login Successful
                                        </td>
                                        <td class="px-4 py-3">Sep 8, 2025 07:51:31</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3 font-medium">#211</td>
                                        <td class="px-4 py-3">C22510</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex justify-center items-center bg-purple-100 rounded-full w-6 h-6">
                                                    <i data-lucide="user" class="w-3 h-3 text-purple-600"></i>
                                                </div>
                                                Justine James Nash (ID: SD251003)
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="log-type-login">
                                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                                Login
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-success">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                Success
                                            </span>
                                        </td>
                                        <td class="flex items-center px-4 py-3">
                                            <i data-lucide="log-in" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Login Successful
                                        </td>
                                        <td class="px-4 py-3">Sep 8, 2025 05:44:56</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3 font-medium">#210</td>
                                        <td class="px-4 py-3">C22510</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex justify-center items-center bg-amber-100 rounded-full w-6 h-6">
                                                    <i data-lucide="user" class="w-3 h-3 text-amber-600"></i>
                                                </div>
                                                Jasmine Keith (ID: S251005)
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="log-type-login">
                                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                                Login
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-warning">
                                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                                Warning
                                            </span>
                                        </td>
                                        <td class="flex items-center px-4 py-3">
                                            <i data-lucide="alert-triangle" class="mr-2 w-4 h-4 text-amber-500"></i>
                                            Multiple Failed Attempts
                                        </td>
                                        <td class="px-4 py-3">Sep 8, 2025 05:38:48</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3 font-medium">#209</td>
                                        <td class="px-4 py-3">C22510</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex justify-center items-center bg-green-100 rounded-full w-6 h-6">
                                                    <i data-lucide="user" class="w-3 h-3 text-green-600"></i>
                                                </div>
                                                Mikel (ID: S251007)
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="log-type-login">
                                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                                Login
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-error">
                                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                                Failed
                                            </span>
                                        </td>
                                        <td class="flex items-center px-4 py-3">
                                            <i data-lucide="x-circle" class="mr-2 w-4 h-4 text-red-500"></i>
                                            Invalid Credentials
                                        </td>
                                        <td class="px-4 py-3">Sep 8, 2025 05:37:30</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3 font-medium">#208</td>
                                        <td class="px-4 py-3">C22510</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex justify-center items-center bg-green-100 rounded-full w-6 h-6">
                                                    <i data-lucide="user" class="w-3 h-3 text-green-600"></i>
                                                </div>
                                                Mikel (ID: S251007)
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="log-type-login">
                                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                                Login
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="status-success">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                Success
                                            </span>
                                        </td>
                                        <td class="flex items-center px-4 py-3">
                                            <i data-lucide="log-in" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Login Successful
                                        </td>
                                        <td class="px-4 py-3">Sep 8, 2025 05:36:39</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-between items-center mt-6">
                            <div class="flex items-center gap-1 text-gray-500 text-sm">
                                <i data-lucide="info" class="w-4 h-4"></i>
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
                </div>
            </main>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>