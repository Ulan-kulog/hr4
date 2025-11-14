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
                <!-- Role Management Section -->
                <div class="bg-white/70 shadow-sm backdrop-blur-sm mb-6 p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="font-bold text-gray-800 text-2xl">Role Management</h2>
                        <button class="btn btn-primary" onclick="document.getElementById('add-role-modal').showModal()">
                            <i data-lucide="plus" class="mr-2 w-5 h-5"></i> Add New Role
                        </button>
                    </div>

                    <!-- Role Cards Grid -->
                    <div class="gap-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Admin Role Card -->
                        <div class="bg-white shadow-sm p-5 border border-gray-200 rounded-xl role-card">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-lg">Administrator</h3>
                                    <p class="text-gray-500 text-sm">Full system access</p>
                                </div>
                                <div class="badge badge-primary">12 users</div>
                            </div>
                            <p class="mb-4 text-gray-600">Has complete access to all system features and data.</p>
                            <div class="flex justify-between">
                                <button class="btn-outline btn btn-sm" onclick="editRole('admin')">
                                    <i data-lucide="edit" class="mr-1 w-4 h-4"></i> Edit
                                </button>
                                <button class="text-red-500 btn btn-sm btn-ghost" onclick="deleteRole('admin')">
                                    <i data-lucide="trash-2" class="mr-1 w-4 h-4"></i> Delete
                                </button>
                            </div>
                        </div>

                        <!-- Manager Role Card -->
                        <div class="bg-white shadow-sm p-5 border border-gray-200 rounded-xl role-card">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-lg">Manager</h3>
                                    <p class="text-gray-500 text-sm">Team management access</p>
                                </div>
                                <div class="badge badge-secondary">24 users</div>
                            </div>
                            <p class="mb-4 text-gray-600">Can manage team members and view performance data.</p>
                            <div class="flex justify-between">
                                <button class="btn-outline btn btn-sm" onclick="editRole('manager')">
                                    <i data-lucide="edit" class="mr-1 w-4 h-4"></i> Edit
                                </button>
                                <button class="text-red-500 btn btn-sm btn-ghost" onclick="deleteRole('manager')">
                                    <i data-lucide="trash-2" class="mr-1 w-4 h-4"></i> Delete
                                </button>
                            </div>
                        </div>

                        <!-- Employee Role Card -->
                        <div class="bg-white shadow-sm p-5 border border-gray-200 rounded-xl role-card">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-lg">Employee</h3>
                                    <p class="text-gray-500 text-sm">Limited access</p>
                                </div>
                                <div class="badge badge-accent">156 users</div>
                            </div>
                            <p class="mb-4 text-gray-600">Can view personal data and submit requests.</p>
                            <div class="flex justify-between">
                                <button class="btn-outline btn btn-sm" onclick="editRole('employee')">
                                    <i data-lucide="edit" class="mr-1 w-4 h-4"></i> Edit
                                </button>
                                <button class="text-red-500 btn btn-sm btn-ghost" onclick="deleteRole('employee')">
                                    <i data-lucide="trash-2" class="mr-1 w-4 h-4"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <h2 class="mb-6 font-bold text-gray-800 text-2xl">Role Permissions</h2>

                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Permission</th>
                                    <th class="text-center">Admin</th>
                                    <th class="text-center">Manager</th>
                                    <th class="text-center">Employee</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>View Dashboard</td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                </tr>
                                <tr>
                                    <td>Manage Employees</td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                    <td class="text-center"><i data-lucide="x" class="mx-auto w-5 h-5 text-red-500"></i></td>
                                </tr>
                                <tr>
                                    <td>Manage Roles</td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                    <td class="text-center"><i data-lucide="x" class="mx-auto w-5 h-5 text-red-500"></i></td>
                                    <td class="text-center"><i data-lucide="x" class="mx-auto w-5 h-5 text-red-500"></i></td>
                                </tr>
                                <tr>
                                    <td>View Reports</td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                    <td class="text-center"><i data-lucide="x" class="mx-auto w-5 h-5 text-red-500"></i></td>
                                </tr>
                                <tr>
                                    <td>Manage Compensation</td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                    <td class="text-center"><i data-lucide="check" class="mx-auto w-5 h-5 text-green-500"></i></td>
                                    <td class="text-center"><i data-lucide="x" class="mx-auto w-5 h-5 text-red-500"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Role Modal -->
    <dialog id="add-role-modal" class="modal">
        <div class="w-11/12 max-w-2xl modal-box">
            <h3 class="mb-4 font-bold text-lg">Add New Role</h3>
            <form id="add-role-form">
                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="label-text">Role Name</span>
                    </label>
                    <input type="text" placeholder="Enter role name" class="w-full input input-bordered" required />
                </div>

                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea class="h-24 textarea textarea-bordered" placeholder="Enter role description"></textarea>
                </div>

                <div class="mb-6">
                    <h4 class="mb-3 font-semibold">Permissions</h4>

                    <div class="permission-group mb-4">
                        <h5 class="mb-2 font-medium">Employee Management</h5>
                        <div class="space-y-2 ml-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" />
                                <span class="text-sm">View Employees</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" />
                                <span class="text-sm">Add Employees</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" />
                                <span class="text-sm">Edit Employees</span>
                            </label>
                        </div>
                    </div>

                    <div class="permission-group mb-4">
                        <h5 class="mb-2 font-medium">Compensation</h5>
                        <div class="space-y-2 ml-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" />
                                <span class="text-sm">View Compensation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" />
                                <span class="text-sm">Manage Compensation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" />
                                <span class="text-sm">Approve Compensation</span>
                            </label>
                        </div>
                    </div>

                    <div class="permission-group mb-4">
                        <h5 class="mb-2 font-medium">Reports</h5>
                        <div class="space-y-2 ml-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" />
                                <span class="text-sm">View Reports</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" />
                                <span class="text-sm">Export Reports</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('add-role-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Edit Role Modal -->
    <dialog id="edit-role-modal" class="modal">
        <div class="w-11/12 max-w-2xl modal-box">
            <h3 class="mb-4 font-bold text-lg">Edit Role</h3>
            <form id="edit-role-form">
                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="label-text">Role Name</span>
                    </label>
                    <input type="text" placeholder="Enter role name" class="w-full input input-bordered" value="Administrator" required />
                </div>

                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea class="h-24 textarea textarea-bordered" placeholder="Enter role description">Has complete access to all system features and data.</textarea>
                </div>

                <div class="mb-6">
                    <h4 class="mb-3 font-semibold">Permissions</h4>

                    <div class="permission-group mb-4">
                        <h5 class="mb-2 font-medium">Employee Management</h5>
                        <div class="space-y-2 ml-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" checked />
                                <span class="text-sm">View Employees</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" checked />
                                <span class="text-sm">Add Employees</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" checked />
                                <span class="text-sm">Edit Employees</span>
                            </label>
                        </div>
                    </div>

                    <div class="permission-group mb-4">
                        <h5 class="mb-2 font-medium">Compensation</h5>
                        <div class="space-y-2 ml-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" checked />
                                <span class="text-sm">View Compensation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" checked />
                                <span class="text-sm">Manage Compensation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" checked />
                                <span class="text-sm">Approve Compensation</span>
                            </label>
                        </div>
                    </div>

                    <div class="permission-group mb-4">
                        <h5 class="mb-2 font-medium">Reports</h5>
                        <div class="space-y-2 ml-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" checked />
                                <span class="text-sm">View Reports</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 checkbox checkbox-sm" checked />
                                <span class="text-sm">Export Reports</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('edit-role-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        lucide.createIcons();

        // Form submission handlers
        document.getElementById('add-role-form').addEventListener('submit', function(e) {
            e.preventDefault();
            // In a real app, you would send this data to the server
            alert('Role created successfully!');
            document.getElementById('add-role-modal').close();
        });

        document.getElementById('edit-role-form').addEventListener('submit', function(e) {
            e.preventDefault();
            // In a real app, you would send this data to the server
            alert('Role updated successfully!');
            document.getElementById('edit-role-modal').close();
        });

        // Role management functions
        function editRole(roleId) {
            // In a real app, you would fetch role data based on roleId
            document.getElementById('edit-role-modal').showModal();
        }

        function deleteRole(roleId) {
            if (confirm('Are you sure you want to delete this role?')) {
                // In a real app, you would send a delete request to the server
                alert('Role deleted successfully!');
            }
        }
    </script>
</body>

</html>