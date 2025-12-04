<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail & Transaction</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <!-- Audit Trail Section -->
                <div class="shadow-sm mb-6 p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <div class="flex md:flex-row flex-col md:justify-between md:items-center mb-6">
                        <div class="flex items-center">
                            <i data-lucide="file-text" class="mr-3 w-8 h-8 text-blue-500"></i>
                            <div>
                                <h1 class="font-bold text-gray-800 text-2xl">Audit Trail & Transaction</h1>
                                <p class="flex items-center mt-1 text-gray-600">
                                    <i data-lucide="database" class="mr-1 w-4 h-4"></i>
                                    Total 134 records
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive border border-gray-200 rounded-lg overflow-x-auto">
                        <table class="audit-table divide-y divide-gray-200 min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i data-lucide="hash" class="mr-1 w-4 h-4"></i>
                                            LOB ID
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i data-lucide="user" class="mr-1 w-4 h-4"></i>
                                            EMPLOYEE
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i data-lucide="folder" class="mr-1 w-4 h-4"></i>
                                            MODULES
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i data-lucide="activity" class="mr-1 w-4 h-4"></i>
                                            ACTION
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i data-lucide="file-text" class="mr-1 w-4 h-4"></i>
                                            ACTIVITY
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i data-lucide="calendar" class="mr-1 w-4 h-4"></i>
                                            DATE
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#134</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="user-check" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Daniel Jonathan supervisor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="utensils" class="mr-2 w-4 h-4 text-blue-500"></i>
                                            Menu Management
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            <div class="flex items-center">
                                                <i data-lucide="plus-circle" class="mr-1 w-3 h-3"></i>
                                                Menu Item created
                                            </div>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Ananya Reynolds' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="mr-1 w-4 h-4 text-gray-400"></i>
                                            Nov 12, 2025 9:00:24
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#133</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="user-check" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Daniel Jonathan supervisor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="utensils" class="mr-2 w-4 h-4 text-blue-500"></i>
                                            Menu Management
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            <div class="flex items-center">
                                                <i data-lucide="plus-circle" class="mr-1 w-3 h-3"></i>
                                                Menu Item created
                                            </div>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Emerald Salinas' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="mr-1 w-4 h-4 text-gray-400"></i>
                                            Nov 12, 2025 18:47:01
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#132</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="user-check" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Daniel Jonathan supervisor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="utensils" class="mr-2 w-4 h-4 text-blue-500"></i>
                                            Menu Management
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            <div class="flex items-center">
                                                <i data-lucide="plus-circle" class="mr-1 w-3 h-3"></i>
                                                Menu Item created
                                            </div>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Hiroko Holland' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="mr-1 w-4 h-4 text-gray-400"></i>
                                            Nov 12, 2025 18:44:45
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#131</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="user-check" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Daniel Jonathan supervisor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="utensils" class="mr-2 w-4 h-4 text-blue-500"></i>
                                            Menu Management
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            <div class="flex items-center">
                                                <i data-lucide="plus-circle" class="mr-1 w-3 h-3"></i>
                                                Menu Item created
                                            </div>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Clio Tran' under b...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="mr-1 w-4 h-4 text-gray-400"></i>
                                            Nov 12, 2025 04:00:20
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#130</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="user-check" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Daniel Jonathan supervisor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="utensils" class="mr-2 w-4 h-4 text-blue-500"></i>
                                            Menu Management
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            <div class="flex items-center">
                                                <i data-lucide="plus-circle" class="mr-1 w-3 h-3"></i>
                                                Menu Item created
                                            </div>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Summer Stanley' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="mr-1 w-4 h-4 text-gray-400"></i>
                                            Nov 12, 2025 03:59:51
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#129</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="user-check" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Daniel Jonathan supervisor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="utensils" class="mr-2 w-4 h-4 text-blue-500"></i>
                                            Menu Management
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            <div class="flex items-center">
                                                <i data-lucide="plus-circle" class="mr-1 w-3 h-3"></i>
                                                Menu Item created
                                            </div>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Logan Byrd' under...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="mr-1 w-4 h-4 text-gray-400"></i>
                                            Nov 12, 2025 03:43:36
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#128</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="user-check" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Daniel Jonathan supervisor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="utensils" class="mr-2 w-4 h-4 text-blue-500"></i>
                                            Menu Management
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            <div class="flex items-center">
                                                <i data-lucide="plus-circle" class="mr-1 w-3 h-3"></i>
                                                Menu Item created
                                            </div>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Porter Murray' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="mr-1 w-4 h-4 text-gray-400"></i>
                                            Nov 11, 2025 21:30:44
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#127</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="user-check" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Daniel Jonathan supervisor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="utensils" class="mr-2 w-4 h-4 text-blue-500"></i>
                                            Menu Management
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            <div class="flex items-center">
                                                <i data-lucide="plus-circle" class="mr-1 w-3 h-3"></i>
                                                Menu Item created
                                            </div>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Cherokee Sparks' ...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="mr-1 w-4 h-4 text-gray-400"></i>
                                            Nov 10, 2025 15:58:15
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#126</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="user-check" class="mr-2 w-4 h-4 text-green-500"></i>
                                            Daniel Jonathan supervisor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="calendar" class="mr-2 w-4 h-4 text-purple-500"></i>
                                            Table reservation
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-yellow-100 px-2 rounded-full font-semibold text-yellow-800 text-xs leading-5">
                                            <div class="flex items-center">
                                                <i data-lucide="edit" class="mr-1 w-3 h-3"></i>
                                                Status updated
                                            </div>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Updated reservation #158 to For Compliance...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="mr-1 w-4 h-4 text-gray-400"></i>
                                            Nov 7, 2025 23:48:00
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-6">
                        <div class="flex items-center text-gray-700 text-sm">
                            <i data-lucide="info" class="mr-1 w-4 h-4"></i>
                            Showing <span class="mx-1 font-medium">1</span> to <span class="mx-1 font-medium">10</span> of <span class="mx-1 font-medium">134</span> results
                        </div>
                        <div class="flex space-x-2">
                            <button class="flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm pagination-btn">
                                <i data-lucide="chevron-left" class="mr-1 w-4 h-4"></i>
                                Previous
                            </button>
                            <button class="flex items-center bg-blue-500 px-3 py-1 rounded-md text-white text-sm pagination-btn">
                                <span>1</span>
                            </button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm pagination-btn">2</button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm pagination-btn">3</button>
                            <button class="flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm pagination-btn">
                                Next
                                <i data-lucide="chevron-right" class="ml-1 w-4 h-4"></i>
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

        // Filter button functionality
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active', 'bg-blue-500', 'text-white');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                });

                this.classList.add('active', 'bg-blue-500', 'text-white');
                this.classList.remove('bg-gray-100', 'text-gray-700');
            });
        });

        // Pagination button functionality
        document.querySelectorAll('.pagination-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (!this.classList.contains('bg-blue-500')) {
                    document.querySelectorAll('.pagination-btn').forEach(btn => {
                        btn.classList.remove('bg-blue-500', 'text-white');
                        btn.classList.add('border', 'border-gray-300');
                    });

                    if (this.textContent.trim() === 'Next' || this.textContent.trim() === 'Previous') {
                        // For next/previous buttons, we don't change their style
                    } else {
                        this.classList.add('bg-blue-500', 'text-white');
                        this.classList.remove('border', 'border-gray-300');
                    }
                }
            });
        });
    </script>

</body>

</html>