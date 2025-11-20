<?php

include '../connection.php';
$conn = $connections['HR_4'] ?? null;

if (!$conn) {
    die("❌ Database connection failed.");
}

$permission_query = "SELECT * FROM permissions";
$permissions = $conn->query($permission_query)->fetch_all(MYSQLI_ASSOC);

$roles_query = "SELECT * FROM roles";
$roles = $conn->query($roles_query)->fetch_all(MYSQLI_ASSOC);

$users_query = "SELECT * FROM department_accounts";
$users = $conn->query($users_query)->fetch_all(MYSQLI_ASSOC);

// Fetch role permissions for each role
$role_permissions = [];
foreach ($roles as $role) {
    $role_id = $role['id'];
    $perm_query = "SELECT permission_id FROM role_permissions WHERE role_id = ?";
    $stmt = $conn->prepare($perm_query);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $role_permissions[$role_id] = array_column($result->fetch_all(MYSQLI_ASSOC), 'permission_id');
}

$grouped = [];

foreach ($permissions as $p) {
    $grouped[$p['permission_for']][] = $p;
}

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $role_id = $_POST['role_id'];
    $name = $_POST['name'] ?? null;
    $description = $_POST['description'] ?? null;
    $permissions = $_POST['permissions'] ?? [];

    mysqli_begin_transaction($conn);
    try {
        // Update role
        $sqlRole = "UPDATE roles SET name = ?, description = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sqlRole);
        mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $role_id);
        mysqli_stmt_execute($stmt);

        // Delete existing permissions
        $sqlDelete = "DELETE FROM role_permissions WHERE role_id = ?";
        $stmtDelete = mysqli_prepare($conn, $sqlDelete);
        mysqli_stmt_bind_param($stmtDelete, "i", $role_id);
        mysqli_stmt_execute($stmtDelete);

        // Insert new permissions
        $sqlPerm = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
        $stmtPerm = mysqli_prepare($conn, $sqlPerm);
        foreach ($permissions as $perm_id) {
            mysqli_stmt_bind_param($stmtPerm, "ii", $role_id, $perm_id);
            mysqli_stmt_execute($stmtPerm);
        }

        mysqli_commit($conn);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=2');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=2');
        exit;
    }
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_role'])) {
    $role_id = $_GET['delete_role'];

    mysqli_begin_transaction($conn);
    try {
        // First delete from role_permissions table
        $sqlDeletePerm = "DELETE FROM role_permissions WHERE role_id = ?";
        $stmtPerm = mysqli_prepare($conn, $sqlDeletePerm);
        mysqli_stmt_bind_param($stmtPerm, "i", $role_id);
        mysqli_stmt_execute($stmtPerm);

        // Then delete from roles table
        $sqlDeleteRole = "DELETE FROM roles WHERE id = ?";
        $stmtRole = mysqli_prepare($conn, $sqlDeleteRole);
        mysqli_stmt_bind_param($stmtRole, "i", $role_id);
        mysqli_stmt_execute($stmtRole);

        mysqli_commit($conn);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=3');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=3');
        exit;
    }
}

// Handle role assignment to user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_role_to_user'])) {
    $user_id = $_POST['user_id'] ?? null;
    $role_id = $_POST['role_id'] ?? null;

    if ($user_id && $role_id) {
        // Check if user already has this role
        $check_query = "SELECT id FROM user_roles WHERE user_id = ? AND role_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $user_id, $role_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Assign role to user
            $assign_query = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
            $stmt = $conn->prepare($assign_query);
            $stmt->bind_param("ii", $user_id, $role_id);

            if ($stmt->execute()) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?success=4');
                exit;
            } else {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?error=4');
                exit;
            }
        } else {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=5');
            exit;
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = $_POST['name'] ?? null;
    $description  = $_POST['description'] ?? null;
    $permissions  = $_POST['permissions'] ?? []; // array of permission IDs

    // Use the HR_4 connection
    $conn = $connections['HR_4'];

    // Safety check
    if (!$conn) {
        die("Database HR_4 not connected.");
    }

    // Start transaction
    mysqli_begin_transaction($conn);

    try {

        // 1️⃣ Insert into roles table
        $sqlRole = "INSERT INTO roles (name, description) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sqlRole);
        mysqli_stmt_bind_param($stmt, "ss", $name, $description);
        mysqli_stmt_execute($stmt);

        // Get new role ID
        $role_id = mysqli_insert_id($conn);

        // 2️⃣ Insert multiple permissions
        $sqlPerm = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
        $stmtPerm = mysqli_prepare($conn, $sqlPerm);

        foreach ($permissions as $perm_id) {
            mysqli_stmt_bind_param($stmtPerm, "ii", $role_id, $perm_id);
            mysqli_stmt_execute($stmtPerm);
        }

        // Commit transaction
        $commit = mysqli_commit($conn);

        if ($commit) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
            exit;
        } else {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=1');
            exit;
        }

        echo "Role saved successfully with permissions!";
    } catch (Exception $e) {

        // Rollback on error
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
}

// Update success messages
if (isset($_GET['success'])) {
    $messages = [
        '1' => "Role created successfully!",
        '2' => "Role updated successfully!",
        '3' => "Role deleted successfully!",
        '4' => "Role assigned to user successfully!"
    ];
    $message = $messages[$_GET['success']] ?? "Operation completed successfully!";
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        alert("' . $message . '");
    });
    </script>';
}

if (isset($_GET['error'])) {
    $messages = [
        '1' => "Error creating role. Please try again.",
        '2' => "Error updating role. Please try again.",
        '3' => "Error deleting role. Please try again.",
        '4' => "Error assigning role to user. Please try again.",
        '5' => "User already has this role assigned."
    ];
    $message = $messages[$_GET['error']] ?? "An error occurred. Please try again.";
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        alert("' . $message . '");
    });
    </script>';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .role-card {
            transition: all 0.3s ease;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .permission-group {
            border-left: 3px solid #3b82f6;
            padding-left: 1rem;
        }

        .btn-subtle-white {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(209, 213, 219, 0.5);
            color: #374151;
            transition: all 0.3s ease;
        }

        .btn-subtle-white:hover {
            background-color: rgba(255, 255, 255, 1);
            border-color: rgba(156, 163, 175, 0.7);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .modal-subtle-white {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
        }

        .modal-header {
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
            padding-bottom: 1rem;
        }

        /* Custom zebra table styling with #086788 */
        .table-zebra tbody tr:nth-child(even) {
            background-color: rgba(8, 103, 136, 0.08) !important;
        }

        .table-zebra tbody tr:nth-child(even):hover {
            background-color: rgba(8, 103, 136, 0.12) !important;
        }

        .table-zebra tbody tr:nth-child(odd) {
            background-color: transparent;
        }

        .table-zebra tbody tr:nth-child(odd):hover {
            background-color: rgba(0, 0, 0, 0.02);
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
                <!-- Role Management Section -->
                <div class="bg-white/70 shadow-sm backdrop-blur-sm mb-6 p-6 border border-gray-100/50 rounded-2xl glass-effect">
                    <div class="flex justify-between items-center mb-6 text-black">
                        <h2 class="font-bold text-2xl">Role Management</h2>
                        <div>
                            <button class="bg-[#001f54] text-white btn" onclick="document.getElementById('attach-role-modal').showModal()">
                                <i data-lucide="link" class="mr-2 w-5 h-5"></i> Attach Role to User
                            </button>
                            <button class="bg-[#001f54] text-white btn" onclick="document.getElementById('add-role-modal').showModal()">
                                <i data-lucide="plus" class="mr-2 w-5 h-5"></i> Add New Role
                            </button>
                        </div>
                    </div>

                    <div class="gap-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($roles as $role): ?>
                            <div class="bg-white shadow-sm p-5 border border-gray-200 rounded-xl role-card">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-lg"><?= htmlspecialchars($role['name']); ?></h3>
                                        <p class="text-gray-500 text-sm"><?= htmlspecialchars($role['description']); ?></p>
                                    </div>
                                    <div class="bg-[#001f54] text-white badge"><?= count($role_permissions[$role['id']] ?? []) ?> permissions</div>
                                </div>
                                <p class="mb-4 text-gray-600">Role ID: <?= $role['id'] ?></p>
                                <div class="flex justify-between">
                                    <button class="btn-outline btn btn-sm" onclick="editRole(<?= $role['id'] ?>)">
                                        <i data-lucide="edit" class="mr-1 w-4 h-4"></i> Edit
                                    </button>
                                    <div class="flex gap-1">
                                        <button class="text-blue-500 btn btn-sm btn-ghost" onclick="attachRoleToUser(<?= $role['id'] ?>)">
                                            <i data-lucide="user-plus" class="mr-1 w-4 h-4"></i> Attach
                                        </button>
                                        <button class="text-red-500 btn btn-sm btn-ghost" onclick="deleteRole(<?= $role['id'] ?>)">
                                            <i data-lucide="trash-2" class="mr-1 w-4 h-4"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl text-black glass-effect">
                    <h2 class="mb-6 font-bold text-2xl">Role Permissions</h2>

                    <?php foreach ($grouped as $module => $modulePermissions): ?>
                        <div class="mb-8">
                            <h3 class="mb-4 pb-2 border-gray-200 border-b font-semibold text-gray-800 text-lg">
                                <?= ucfirst($module) ?> Permissions
                                <span class="ml-2 font-normal text-gray-500 text-sm">
                                    (<?= count($modulePermissions) ?> permissions)
                                </span>
                            </h3>

                            <div class="gap-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                <?php foreach ($modulePermissions as $permission): ?>
                                    <div class="bg-white hover:shadow-md p-4 border border-gray-200 rounded-lg transition-shadow duration-200">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium text-gray-800 text-sm">
                                                <?= htmlspecialchars($permission['name']); ?>
                                            </h4>
                                            <div class="flex space-x-1">
                                                <div class="tooltip" data-tip="Admin">
                                                    <i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i>
                                                </div>
                                                <div class="tooltip" data-tip="Manager">
                                                    <i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i>
                                                </div>
                                                <div class="tooltip" data-tip="Employee">
                                                    <i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-gray-500 text-xs">
                                            ID: <?= $permission['id'] ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Role Modal -->
    <dialog id="add-role-modal" class="modal">
        <div class="w-11/12 max-w-2xl modal-box modal-subtle-white">
            <div class="modal-header">
                <h3 class="font-bold text-gray-800 text-lg">Add New Role</h3>
            </div>
            <form id="add-role-form" class="mt-4" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="text-gray-700 label-text">Role Name</span>
                    </label>
                    <input type="text" name="name" placeholder="Enter role name" class="bg-white/80 w-full input input-bordered" required />
                </div>

                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="text-gray-700 label-text">Description</span>
                    </label>
                    <textarea name="description" class="bg-white/80 h-24 textarea textarea-bordered" placeholder="Enter role description"></textarea>
                </div>

                <div class="mb-6">
                    <?php foreach ($grouped as $module => $items): ?>
                        <h4 class="mb-3 font-semibold text-gray-800"><?= ucfirst($module) ?> Permissions</h4>
                        <div class="permission-group mb-4">
                            <?php foreach ($items as $p): ?>
                                <div class="space-y-2 ml-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="permissions[]" class="mr-2 checkbox checkbox-sm" value="<?= $p['id']; ?>" />
                                        <span class="text-gray-600 text-sm"><?= htmlspecialchars($p['name']) ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
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
        <div class="w-11/12 max-w-2xl modal-box modal-subtle-white">
            <div class="modal-header">
                <h3 class="font-bold text-gray-800 text-lg">Edit Role</h3>
            </div>
            <form id="edit-role-form" class="mt-4" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="update_role" value="1">
                <input type="hidden" id="edit-role-id" name="role_id" value="">

                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="text-gray-700 label-text">Role Name</span>
                    </label>
                    <input type="text" id="edit-role-name" name="name" placeholder="Enter role name" class="bg-white/80 w-full input input-bordered" required />
                </div>

                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="text-gray-700 label-text">Description</span>
                    </label>
                    <textarea id="edit-role-description" name="description" class="bg-white/80 h-24 textarea textarea-bordered" placeholder="Enter role description"></textarea>
                </div>

                <div class="mb-6">
                    <?php foreach ($grouped as $module => $items): ?>
                        <h4 class="mb-3 font-semibold text-gray-800"><?= ucfirst($module) ?> Permissions</h4>
                        <div class="permission-group mb-4">
                            <?php foreach ($items as $p): ?>
                                <div class="space-y-2 ml-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="permissions[]" class="mr-2 edit-permission checkbox checkbox-sm" value="<?= $p['id']; ?>" />
                                        <span class="text-gray-600 text-sm"><?= htmlspecialchars($p['name']) ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('edit-role-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Attach Role to User Modal -->
    <dialog id="attach-role-modal" class="modal">
        <div class="w-11/12 max-w-2xl modal-box modal-subtle-white">
            <div class="modal-header">
                <h3 class="font-bold text-gray-800 text-lg">Attach Role to User</h3>
            </div>
            <form id="attach-role-form" class="mt-4" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="assign_role_to_user" value="1">

                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="text-gray-700 label-text">Select User</span>
                    </label>
                    <select name="user_id" class="bg-white/80 w-full select-bordered select" required>
                        <option value="" disabled selected>Choose a user</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['dept_id'] ?>">
                                <?= htmlspecialchars($user['employee_name']) ?> - <?= htmlspecialchars($user['email']) ?> (<?= htmlspecialchars($user['dept_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="text-gray-700 label-text">Select Role</span>
                    </label>
                    <select name="role_id" class="bg-white/80 w-full select-bordered select" required>
                        <option value="" disabled selected>Choose a role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>">
                                <?= htmlspecialchars($role['name']) ?> - <?= htmlspecialchars($role['description']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('attach-role-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Attach Role</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Quick Attach Role Modal (for individual role cards) -->
    <dialog id="quick-attach-modal" class="modal">
        <div class="w-11/12 max-w-md modal-box modal-subtle-white">
            <div class="modal-header">
                <h3 class="font-bold text-gray-800 text-lg">Attach Role to User</h3>
            </div>
            <form id="quick-attach-form" class="mt-4" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="assign_role_to_user" value="1">
                <input type="hidden" id="quick-attach-role-id" name="role_id" value="">

                <div class="mb-4">
                    <p class="mb-2 text-gray-600">Role: <span id="quick-attach-role-name" class="font-semibold"></span></p>
                </div>

                <div class="mb-4 w-full form-control">
                    <label class="label">
                        <span class="text-gray-700 label-text">Select User</span>
                    </label>
                    <select name="user_id" class="bg-white/80 w-full select-bordered select" required>
                        <option value="" disabled selected>Choose a user</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['dept_id'] ?>">
                                <?= htmlspecialchars($user['employee_name']) ?> - <?= htmlspecialchars($user['email']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('quick-attach-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Attach Role</button>
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
            this.submit();
            document.getElementById('add-role-modal').close();
        });

        document.getElementById('edit-role-form').addEventListener('submit', function(e) {
            e.preventDefault();
            this.submit();
            document.getElementById('edit-role-modal').close();
        });

        document.getElementById('attach-role-form').addEventListener('submit', function(e) {
            e.preventDefault();
            this.submit();
            document.getElementById('attach-role-modal').close();
        });

        document.getElementById('quick-attach-form').addEventListener('submit', function(e) {
            e.preventDefault();
            this.submit();
            document.getElementById('quick-attach-modal').close();
        });

        // Role management functions
        function editRole(roleId) {
            const role = <?= json_encode(array_column($roles, null, 'id')) ?>[roleId];
            const rolePermissions = <?= json_encode($role_permissions) ?>[roleId] || [];

            if (role) {
                // Populate the form with role data
                document.getElementById('edit-role-id').value = role.id;
                document.getElementById('edit-role-name').value = role.name;
                document.getElementById('edit-role-description').value = role.description;

                // Reset all checkboxes
                document.querySelectorAll('.edit-permission').forEach(checkbox => {
                    checkbox.checked = false;
                });

                // Check the permissions this role has
                rolePermissions.forEach(permId => {
                    const checkbox = document.querySelector(`.edit-permission[value="${permId}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });

                document.getElementById('edit-role-modal').showModal();
            }
        }

        function attachRoleToUser(roleId) {
            const role = <?= json_encode(array_column($roles, null, 'id')) ?>[roleId];
            if (role) {
                document.getElementById('quick-attach-role-id').value = role.id;
                document.getElementById('quick-attach-role-name').textContent = role.name + ' - ' + role.description;
                document.getElementById('quick-attach-modal').showModal();
            }
        }

        function deleteRole(roleId) {
            if (confirm('Are you sure you want to delete this role?')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '<?php echo $_SERVER['PHP_SELF']; ?>';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_role';
                input.value = roleId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>