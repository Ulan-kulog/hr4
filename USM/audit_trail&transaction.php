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
                        <div>
                            <h1 class="font-bold text-gray-800 text-2xl">Audit Trail & Transaction</h1>
                            <p class="mt-1 text-gray-600">Total 134 records</p>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive border border-gray-200 rounded-lg overflow-x-auto">
                        <table class="audit-table divide-y divide-gray-200 min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">LOB ID</th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">EMPLOYEE</th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">MODULES</th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">ACTION</th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">ACTIVITY</th>
                                    <th class="px-6 py-3 font-medium text-xs text-left uppercase tracking-wider">DATE</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#134</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Daniel Jonathan supervisor</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Menu Management</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            Menu Item created
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Ananya Reynolds' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Nov 12, 2025 9:00:24</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#133</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Daniel Jonathan supervisor</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Menu Management</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            Menu Item created
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Emerald Salinas' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Nov 12, 2025 18:47:01</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#132</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Daniel Jonathan supervisor</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Menu Management</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            Menu Item created
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Hiroko Holland' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Nov 12, 2025 18:44:45</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#131</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Daniel Jonathan supervisor</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Menu Management</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            Menu Item created
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Clio Tran' under b...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Nov 12, 2025 04:00:20</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#130</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Daniel Jonathan supervisor</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Menu Management</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            Menu Item created
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Summer Stanley' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Nov 12, 2025 03:59:51</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#129</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Daniel Jonathan supervisor</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Menu Management</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            Menu Item created
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Logan Byrd' under...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Nov 12, 2025 03:43:36</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#128</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Daniel Jonathan supervisor</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Menu Management</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            Menu Item created
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Porter Murray' u...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Nov 11, 2025 21:30:44</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#127</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Daniel Jonathan supervisor</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Menu Management</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">
                                            Menu Item created
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Created new menu Item 'Cherokee Sparks' ...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Nov 10, 2025 15:58:15</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-blue-600 text-sm whitespace-nowrap">#126</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Daniel Jonathan supervisor</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Table reservation</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-yellow-100 px-2 rounded-full font-semibold text-yellow-800 text-xs leading-5">
                                            Status updated
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 text-sm">Updated reservation #158 to For Compliance...</td>
                                    <td class="px-6 py-4 text-gray-800 text-sm whitespace-nowrap">Nov 7, 2025 23:48:00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-6">
                        <div class="text-gray-700 text-sm">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">134</span> results
                        </div>
                        <div class="flex space-x-2">
                            <button class="flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm pagination-btn">
                                <i class="fa-chevron-left mr-1 fas"></i>
                                Previous
                            </button>
                            <button class="bg-blue-500 px-3 py-1 rounded-md text-white text-sm pagination-btn">1</button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm pagination-btn">2</button>
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm pagination-btn">3</button>
                            <button class="flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm pagination-btn">
                                Next
                                <i class="fa-chevron-right ml-1 fas"></i>
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