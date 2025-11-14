<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Compensation - Compensation Planning</title>
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

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .table-header {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 14px;
        }

        .table-row {
            border-bottom: 1px solid #e2e8f0;
        }

        .table-row:hover {
            background-color: #f8fafc;
        }

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-under-review {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-notice-period {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .status-awol {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-floating {
            background-color: #f3e8ff;
            color: #6b21a8;
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
                <!-- Department Accounts Section -->
                <div class="shadow-sm mb-6 p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <i data-lucide="building" class="w-8 h-8 text-blue-500"></i>
                            <h1 class="font-bold text-gray-800 text-2xl">Department Accounts</h1>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i data-lucide="users" class="w-5 h-5"></i>
                            <span>Total 11 accounts</span>
                        </div>
                    </div>

                    <!-- Employee List Table -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-x-auto">
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="hash" class="w-4 h-4"></i>
                                            DEPT ID
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="building" class="w-4 h-4"></i>
                                            DEPARTMENT
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="user" class="w-4 h-4"></i>
                                            EMPLOYEE
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="briefcase" class="w-4 h-4"></i>
                                            ROLE
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="activity" class="w-4 h-4"></i>
                                            STATUS
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                                            CREATED AT
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar-check" class="w-4 h-4"></i>
                                            UPDATED AT
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row">
                                    <td class="px-4 py-3 text-gray-700">CZ2500</td>
                                    <td class="px-4 py-3 text-gray-700">Soliera Restaurant</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex justify-center items-center bg-blue-100 rounded-full w-8 h-8">
                                                <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">Daniel Jonathan</div>
                                                <div class="email-text">Timervorad@gmail.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="shield" class="w-4 h-4 text-blue-500"></i>
                                            supervisor
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                                            <span class="status-active">Active</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2023-05-15</div>
                                        <div class="timestamp">10:30 AM</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2024-01-20</div>
                                        <div class="timestamp">02:15 PM</div>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="px-4 py-3 text-gray-700">CZ2500</td>
                                    <td class="px-4 py-3 text-gray-700">Soliera Restaurant</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex justify-center items-center bg-purple-100 rounded-full w-8 h-8">
                                                <i data-lucide="user" class="w-4 h-4 text-purple-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">James gervin Jr.</div>
                                                <div class="email-text">Gervingabul@gmail.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="package" class="w-4 h-4 text-purple-500"></i>
                                            inventory
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                                            <span class="status-active">Active</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2023-06-22</div>
                                        <div class="timestamp">09:45 AM</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2024-02-10</div>
                                        <div class="timestamp">11:20 AM</div>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="px-4 py-3 text-gray-700">CZ2500</td>
                                    <td class="px-4 py-3 text-gray-700">Soliera Restaurant</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex justify-center items-center bg-green-100 rounded-full w-8 h-8">
                                                <i data-lucide="user" class="w-4 h-4 text-green-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">Justine james cash</div>
                                                <div class="email-text">Timervorad@gmail.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="shield" class="w-4 h-4 text-green-500"></i>
                                            security
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                                            <span class="status-active">Active</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2023-07-10</div>
                                        <div class="timestamp">03:20 PM</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2024-01-15</div>
                                        <div class="timestamp">04:30 PM</div>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="px-4 py-3 text-gray-700">CZ2500</td>
                                    <td class="px-4 py-3 text-gray-700">Soliera Restaurant</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex justify-center items-center bg-yellow-100 rounded-full w-8 h-8">
                                                <i data-lucide="user" class="w-4 h-4 text-yellow-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">chery nae nadie lyn</div>
                                                <div class="email-text">cerczsackenop037@gmail.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="credit-card" class="w-4 h-4 text-yellow-500"></i>
                                            cashier
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                                            <span class="status-active">Active</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2023-08-05</div>
                                        <div class="timestamp">01:10 PM</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2024-02-28</div>
                                        <div class="timestamp">10:45 AM</div>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="px-4 py-3 text-gray-700">CZ2500</td>
                                    <td class="px-4 py-3 text-gray-700">Soliera Restaurant</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex justify-center items-center bg-red-100 rounded-full w-8 h-8">
                                                <i data-lucide="user" class="w-4 h-4 text-red-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">Jasmine keith</div>
                                                <div class="email-text">tremolstephaniekeithoud@gmail.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="user" class="w-4 h-4 text-red-500"></i>
                                            staff
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                                            <span class="status-active">Active</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2023-09-18</div>
                                        <div class="timestamp">11:05 AM</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="timestamp">2024-03-05</div>
                                        <div class="timestamp">03:50 PM</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-6">
                        <div class="flex items-center gap-2 text-gray-500 text-sm">
                            <i data-lucide="info" class="w-4 h-4"></i>
                            Showing 1 to 5 of 11 accounts
                        </div>
                        <div class="flex space-x-2">
                            <button class="flex items-center gap-1 bg-white hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-md transition-colors">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                Previous
                            </button>
                            <button class="bg-blue-500 px-3 py-1 rounded-md text-white">1</button>
                            <button class="bg-white hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-md transition-colors">2</button>
                            <button class="bg-white hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-md transition-colors">3</button>
                            <button class="flex items-center gap-1 bg-white hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-md transition-colors">
                                Next
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Simple script for interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('.table-row');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8fafc';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>
</body>

</html>