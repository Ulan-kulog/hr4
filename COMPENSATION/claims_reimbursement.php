<?php
// claims_management.php - Simplified version without provider_id
require_once 'DB.php';

// Check user permissions/session
session_start();
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../login.php');
//     exit();
// }

// Get user role and ID from session
$user_id = $_SESSION['user_id'] ?? 0;
$user_role = $_SESSION['user_role'] ?? 'employee'; // admin, manager, hr, finance, employee
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Claims Management System</title>
    <?php include '../INCLUDES/header.php'; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../INCLUDES/sidebar.php'; ?>

        <!-- Content Area -->
        <div class="flex flex-col flex-1 overflow-auto">
            <!-- Navbar -->
            <?php include '../INCLUDES/navbar.php'; ?>

            <!-- Main Content Area -->
            <div class="space-y-6 p-6">
                <!-- Header Section -->
                <div class="bg-white shadow-sm p-6 rounded-lg">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="font-bold text-gray-800 text-2xl">Employee Claims Management</h1>
                            <p class="mt-1 text-gray-600">Submit, track, and manage employee reimbursement claims</p>
                        </div>
                        <button id="newClaimBtn" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white">
                            <i data-lucide="plus-circle"></i>
                            New Claim
                        </button>
                    </div>

                    <!-- Stats Cards -->
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-5 mb-6">
                        <!-- Total Claims -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-blue-600 text-sm">Total Claims</p>
                                    <p class="font-bold text-gray-800 text-2xl">
                                        <?php
                                        if ($user_role === 'employee') {
                                            $total = Database::fetchColumn(
                                                "SELECT COUNT(*) FROM employee_claims WHERE employee_id = ?",
                                                [$user_id]
                                            );
                                        } else {
                                            $total = Database::fetchColumn("SELECT COUNT(*) FROM employee_claims");
                                        }
                                        echo $total;
                                        ?>
                                    </p>
                                </div>
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Review -->
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-yellow-600 text-sm">Pending Review</p>
                                    <p class="font-bold text-gray-800 text-2xl">
                                        <?php
                                        $sql = "SELECT COUNT(*) FROM employee_claims WHERE status IN ('submitted', 'pending_manager', 'pending_hr', 'pending_finance')";
                                        $params = [];

                                        if ($user_role === 'employee') {
                                            $sql .= " AND employee_id = ?";
                                            $params = [$user_id];
                                        }

                                        $pending = Database::fetchColumn($sql, $params);
                                        echo $pending;
                                        ?>
                                    </p>
                                </div>
                                <div class="bg-yellow-100 p-2 rounded-full">
                                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Approved -->
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-green-600 text-sm">Approved</p>
                                    <p class="font-bold text-gray-800 text-2xl">
                                        <?php
                                        $sql = "SELECT COUNT(*) FROM employee_claims WHERE status = 'approved'";
                                        $params = [];

                                        if ($user_role === 'employee') {
                                            $sql .= " AND employee_id = ?";
                                            $params = [$user_id];
                                        }

                                        $approved = Database::fetchColumn($sql, $params);
                                        echo $approved;
                                        ?>
                                    </p>
                                </div>
                                <div class="bg-green-100 p-2 rounded-full">
                                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Rejected -->
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-red-600 text-sm">Rejected</p>
                                    <p class="font-bold text-gray-800 text-2xl">
                                        <?php
                                        $sql = "SELECT COUNT(*) FROM employee_claims WHERE status = 'rejected'";
                                        $params = [];

                                        if ($user_role === 'employee') {
                                            $sql .= " AND employee_id = ?";
                                            $params = [$user_id];
                                        }

                                        $rejected = Database::fetchColumn($sql, $params);
                                        echo $rejected;
                                        ?>
                                    </p>
                                </div>
                                <div class="bg-red-100 p-2 rounded-full">
                                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Paid -->
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-purple-600 text-sm">Paid</p>
                                    <p class="font-bold text-gray-800 text-2xl">
                                        <?php
                                        $sql = "SELECT COUNT(*) FROM employee_claims WHERE status = 'paid'";
                                        $params = [];

                                        if ($user_role === 'employee') {
                                            $sql .= " AND employee_id = ?";
                                            $params = [$user_id];
                                        }

                                        $paid = Database::fetchColumn($sql, $params);
                                        echo $paid;
                                        ?>
                                    </p>
                                </div>
                                <div class="bg-purple-100 p-2 rounded-full">
                                    <i data-lucide="credit-card" class="w-6 h-6 text-purple-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white shadow-sm p-4 rounded-lg">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Status Filter</label>
                            <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="">All Status</option>
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                                <option value="pending_manager">Pending Manager</option>
                                <option value="pending_hr">Pending HR</option>
                                <option value="pending_finance">Pending Finance</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>

                        <div class="flex-1 min-w-[200px]">
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Claim Type</label>
                            <select id="typeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                <option value="">All Types</option>
                                <option value="medical">Medical</option>
                                <option value="allowance">Allowance</option>
                                <option value="reimbursement">Reimbursement</option>
                                <option value="accident">Accident</option>
                                <option value="leave_conversion">Leave Conversion</option>
                            </select>
                        </div>

                        <div class="flex-1 min-w-[200px]">
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Date From</label>
                            <input type="date" id="dateFromFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        </div>

                        <div class="flex-1 min-w-[200px]">
                            <label class="block mb-1 font-medium text-gray-700 text-sm">Date To</label>
                            <input type="date" id="dateToFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        </div>

                        <div class="flex items-end gap-2">
                            <button id="applyFilters" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white">
                                Apply Filters
                            </button>
                            <button id="clearFilters" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg text-gray-800">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Claims Table -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table id="claimsTable" class="divide-y divide-gray-200 min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Claim #</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Employee</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Amount Requested</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Amount Approved</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Filed Date</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                    // Build query based on user role
                                    $sql = "SELECT 
                                                ec.*,
                                                e.first_name,
                                                e.last_name,
                                                e.employee_code,
                                                e.department
                                            FROM employee_claims ec
                                            JOIN employees e ON ec.employee_id = e.id";

                                    $params = [];

                                    // if ($user_role === 'employee') {
                                    //     $sql .= " WHERE ec.employee_id = ?";
                                    //     $params = [$user_id];
                                    // }

                                    // $sql .= " ORDER BY ec.filed_date DESC";

                                    $claims = Database::fetchAll($sql, $params);
                                    // dd($claims);
                                    foreach ($claims as $row) {
                                        // Get status color using switch
                                        $statusColor = 'bg-gray-100 text-gray-800'; // default
                                        switch ($row->status) {
                                            case 'draft':
                                                $statusColor = 'bg-gray-100 text-gray-800';
                                                break;
                                            case 'submitted':
                                                $statusColor = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'pending_manager':
                                                $statusColor = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'pending_hr':
                                                $statusColor = 'bg-orange-100 text-orange-800';
                                                break;
                                            case 'pending_finance':
                                                $statusColor = 'bg-purple-100 text-purple-800';
                                                break;
                                            case 'approved':
                                                $statusColor = 'bg-green-100 text-green-800';
                                                break;
                                            case 'rejected':
                                                $statusColor = 'bg-red-100 text-red-800';
                                                break;
                                            case 'paid':
                                                $statusColor = 'bg-indigo-100 text-indigo-800';
                                                break;
                                        }

                                        // Get type color using switch
                                        $typeColor = 'bg-gray-100 text-gray-800'; // default
                                        switch ($row->claim_type) {
                                            case 'medical':
                                                $typeColor = 'bg-red-100 text-red-800';
                                                break;
                                            case 'allowance':
                                                $typeColor = 'bg-green-100 text-green-800';
                                                break;
                                            case 'reimbursement':
                                                $typeColor = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'accident':
                                                $typeColor = 'bg-orange-100 text-orange-800';
                                                break;
                                            case 'leave_conversion':
                                                $typeColor = 'bg-purple-100 text-purple-800';
                                                break;
                                        }
                                    ?>
                                        <tr>
                                            <td class="px-6 py-4 text-gray-900 whitespace-nowrap">
                                                <?= htmlspecialchars($row->claim_number ?? 'N/A') ?>
                                                <?php if ($row->status === 'draft'): ?>
                                                    <span class="ml-2 text-gray-500 text-xs">(Draft)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div class="font-medium text-gray-900">
                                                        <?= htmlspecialchars($row->first_name . ' ' . $row->last_name) ?>
                                                    </div>
                                                    <div class="text-gray-500 text-sm">
                                                        <?= htmlspecialchars($row->employee_code) ?>
                                                        <?php if (!empty($row->department)): ?>
                                                            | <?= htmlspecialchars($row->department) ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium text-xs <?= $typeColor ?>">
                                                    <?= ucfirst(htmlspecialchars($row->claim_type)) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                                <?= htmlspecialchars($row->claim_category) ?>
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                ₱<?= number_format($row->amount_requested, 2) ?>
                                            </td>
                                            <td class="px-6 py-4 font-medium <?= $row->amount_approved > 0 ? 'text-green-600' : 'text-gray-500' ?> whitespace-nowrap">
                                                ₱<?= number_format($row->amount_approved, 2) ?>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                                <?= date('M d, Y', strtotime($row->filed_date)) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= $statusColor ?>">
                                                    <?= ucfirst(str_replace('_', ' ', htmlspecialchars($row->status))) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <button onclick="viewClaim(<?= $row->id ?>)"
                                                        class="flex items-center gap-1 text-blue-600 hover:text-blue-900"
                                                        title="View Details">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                        View
                                                    </button>

                                                    <?php if (in_array($row->status, ['draft', 'submitted']) && ($user_role === 'admin' || $row->employee_id == $user_id)): ?>
                                                        <button onclick="editClaim(<?= $row->id ?>)"
                                                            class="flex items-center gap-1 text-green-600 hover:text-green-900"
                                                            title="Edit Claim">
                                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                                            Edit
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if ($row->status === 'draft' && $row->employee_id == $user_id): ?>
                                                        <button onclick="submitClaim(<?= $row->id ?>)"
                                                            class="flex items-center gap-1 text-indigo-600 hover:text-indigo-900"
                                                            title="Submit for Approval">
                                                            <i data-lucide="send" class="w-4 h-4"></i>
                                                            Submit
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if (
                                                        in_array($row->status, ['pending_manager', 'pending_hr', 'pending_finance']) &&
                                                        (($user_role === 'manager' && $row->status === 'pending_manager') ||
                                                            ($user_role === 'hr' && $row->status === 'pending_hr') ||
                                                            ($user_role === 'finance' && $row->status === 'pending_finance'))
                                                    ): ?>
                                                        <button onclick="reviewClaim(<?= $row->id ?>)"
                                                            class="flex items-center gap-1 text-orange-600 hover:text-orange-900"
                                                            title="Review Claim">
                                                            <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                                                            Review
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if ($user_role === 'admin' || $row->employee_id == $user_id): ?>
                                                        <button onclick="deleteClaim(<?= $row->id ?>)"
                                                            class="flex items-center gap-1 text-red-600 hover:text-red-900"
                                                            title="Delete Claim">
                                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Claim Modal -->
            <div id="newClaimModal" class="hidden z-50 fixed inset-0 bg-gray-600 bg-opacity-50 w-full h-full overflow-y-auto">
                <div class="top-20 relative bg-white shadow-lg mx-auto p-5 border rounded-lg w-full max-w-4xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800 text-xl">New Employee Claim</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form id="claimForm" class="space-y-4">
                        <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                            <!-- Claim Type -->
                            <div>
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Claim Type *</label>
                                <select name="claim_type" id="claim_type" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                                    onchange="updateCategoryOptions()">
                                    <option value="">Select Type</option>
                                    <option value="medical">Medical</option>
                                    <option value="allowance">Allowance</option>
                                    <option value="reimbursement">Reimbursement</option>
                                    <option value="accident">Accident</option>
                                    <option value="leave_conversion">Leave Conversion</option>
                                </select>
                            </div>

                            <!-- Claim Category -->
                            <div>
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Category *</label>
                                <select name="claim_category" id="claim_category" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                    <option value="">Select Category</option>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>

                            <!-- Hospital/Provider Name (for medical/accident claims) -->
                            <div id="providerField" class="hidden">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Hospital/Provider Name</label>
                                <input type="text" name="provider_name" id="provider_name"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                                    placeholder="e.g., St. Luke's Medical Center">
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Amount Requested *</label>
                                <input type="number" step="0.01" name="amount_requested" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                                    placeholder="0.00">
                            </div>

                            <!-- Incident Date (for accident/medical) -->
                            <div id="incidentDateField" class="hidden">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Incident Date *</label>
                                <input type="date" name="incident_date"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                            </div>

                            <!-- Filed Date -->
                            <div>
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Filed Date *</label>
                                <input type="date" name="filed_date" value="<?= date('Y-m-d') ?>" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                            </div>

                            <!-- Status (only for admins or editing) -->
                            <?php if ($user_role === 'admin'): ?>
                                <div>
                                    <label class="block mb-1 font-medium text-gray-700 text-sm">Initial Status</label>
                                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                        <option value="draft">Draft</option>
                                        <option value="submitted">Submitted</option>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Description *</label>
                                <textarea name="description" rows="3" required
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                                    placeholder="Provide details about this claim..."></textarea>
                            </div>

                            <!-- File Upload (receipts/attachments) -->
                            <div class="md:col-span-2">
                                <label class="block mb-1 font-medium text-gray-700 text-sm">Attachments (Optional)</label>
                                <div class="p-6 border-2 border-gray-300 border-dashed rounded-lg text-center">
                                    <input type="file" name="attachments[]" id="attachments"
                                        class="hidden" multiple
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="upload" class="mb-2 w-12 h-12 text-gray-400"></i>
                                        <p class="mb-2 text-gray-600">Drag & drop files or click to browse</p>
                                        <button type="button" onclick="document.getElementById('attachments').click()"
                                            class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-gray-700">
                                            Choose Files
                                        </button>
                                    </div>
                                    <div id="fileList" class="mt-4 text-left"></div>
                                </div>
                                <p class="mt-1 text-gray-500 text-sm">Supported formats: PDF, JPG, PNG, DOC (Max: 5MB each)</p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t">
                            <button type="button" onclick="closeModal()"
                                class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700">
                                Cancel
                            </button>
                            <button type="submit" name="save_as_draft"
                                class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-white">
                                Save as Draft
                            </button>
                            <button type="submit" name="submit_claim"
                                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white">
                                Submit Claim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#claimsTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [
                    [6, 'desc']
                ] // Sort by filed date descending
            });

            lucide.createIcons();

            // Initialize category options
            updateCategoryOptions();
        });

        // Category options based on claim type
        const categoryOptions = {
            medical: [{
                    value: 'OUTPATIENT',
                    label: 'Outpatient Consultation'
                },
                {
                    value: 'EMERGENCY',
                    label: 'Emergency Room'
                },
                {
                    value: 'LABORATORY',
                    label: 'Laboratory Tests'
                },
                {
                    value: 'MEDICINE',
                    label: 'Medicine/Pharmacy'
                },
                {
                    value: 'DENTAL',
                    label: 'Dental Procedure'
                }
            ],
            allowance: [{
                    value: 'RICE',
                    label: 'Rice Allowance'
                },
                {
                    value: 'TRANSPO',
                    label: 'Transportation Allowance'
                },
                {
                    value: 'UNIFORM',
                    label: 'Uniform Allowance'
                },
                {
                    value: 'LAUNDRY',
                    label: 'Laundry Allowance'
                }
            ],
            reimbursement: [{
                    value: 'TRAVEL',
                    label: 'Travel Expenses'
                },
                {
                    value: 'MEALS',
                    label: 'Meals & Entertainment'
                },
                {
                    value: 'OFFICE_SUPPLIES',
                    label: 'Office Supplies'
                },
                {
                    value: 'TRAINING',
                    label: 'Training & Certification'
                },
                {
                    value: 'EQUIPMENT',
                    label: 'Equipment Purchase'
                }
            ],
            accident: [{
                    value: 'WORK_RELATED',
                    label: 'Work-related Accident'
                },
                {
                    value: 'COMMUTE',
                    label: 'Commuting Accident'
                }
            ],
            leave_conversion: [{
                    value: 'VL_CONVERSION',
                    label: 'Vacation Leave Conversion'
                },
                {
                    value: 'SL_CONVERSION',
                    label: 'Sick Leave Conversion'
                }
            ]
        };

        function updateCategoryOptions() {
            const claimType = document.getElementById('claim_type').value;
            const categorySelect = document.getElementById('claim_category');
            const providerField = document.getElementById('providerField');
            const incidentDateField = document.getElementById('incidentDateField');

            // Clear existing options
            categorySelect.innerHTML = '<option value="">Select Category</option>';

            // Add new options
            if (claimType && categoryOptions[claimType]) {
                categoryOptions[claimType].forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.value;
                    option.textContent = cat.label;
                    categorySelect.appendChild(option);
                });
            }

            // Show/hide provider field for medical/accident claims
            const incidentDateInput = document.querySelector('input[name="incident_date"]');
            if (claimType === 'medical' || claimType === 'accident') {
                providerField.classList.remove('hidden');
                if (claimType === 'accident') {
                    incidentDateField.classList.remove('hidden');
                    if (incidentDateInput) incidentDateInput.required = true;
                } else {
                    incidentDateField.classList.add('hidden');
                    if (incidentDateInput) {
                        incidentDateInput.required = false;
                        incidentDateInput.value = '';
                    }
                }
            } else {
                providerField.classList.add('hidden');
                if (incidentDateInput) {
                    incidentDateField.classList.add('hidden');
                    incidentDateInput.required = false;
                    incidentDateInput.value = '';
                } else {
                    incidentDateField.classList.add('hidden');
                }
            }
        }

        // File upload handling
        document.getElementById('attachments').addEventListener('change', function(e) {
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';

            Array.from(e.target.files).forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-2 bg-gray-50 rounded mb-2';
                div.innerHTML = `
                    <div class="flex items-center">
                        <i data-lucide="file" class="mr-2 w-4 h-4 text-gray-500"></i>
                        <span class="text-sm">${file.name}</span>
                        <span class="ml-2 text-gray-500 text-xs">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                `;
                fileList.appendChild(div);
            });

            lucide.createIcons();
        });

        function removeFile(index) {
            const input = document.getElementById('attachments');
            const dt = new DataTransfer();
            const files = Array.from(input.files);

            files.forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });

            input.files = dt.files;
            document.getElementById('attachments').dispatchEvent(new Event('change'));
        }

        // Modal Functions
        document.getElementById('newClaimBtn').addEventListener('click', function() {
            document.getElementById('newClaimModal').classList.remove('hidden');
            document.getElementById('claimForm').reset();
            updateCategoryOptions();
            document.getElementById('fileList').innerHTML = '';
        });

        function closeModal() {
            document.getElementById('newClaimModal').classList.add('hidden');
        }

        // Form Submission
        document.getElementById('claimForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitType = e.submitter?.name;

            // Determine status based on button clicked
            formData.append('status', submitType === 'submit_claim' ? 'submitted' : 'draft');
            formData.append('employee_id', '<?= $user_id ?>');
            formData.append('action', 'create_claim');

            // Submit via AJAX
            $.ajax({
                url: 'API/submit_claim.php',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    try {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            console.log('test');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Invalid response from server',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to submit claim. Please try again.',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });

        // Action Functions
        function viewClaim(id) {
            window.location.href = 'view_claim.php?id=' + id;
        }

        function editClaim(id) {
            window.location.href = 'edit_claim.php?id=' + id;
        }

        function submitClaim(id) {
            Swal.fire({
                title: 'Submit for Approval',
                text: "Are you sure you want to submit this claim for approval?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'update_claim_status.php',
                        method: 'POST',
                        data: {
                            id: id,
                            status: 'submitted',
                            action: 'submit'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Submitted!',
                                'Claim has been submitted for approval.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to submit claim. Please try again.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }

        function reviewClaim(id) {
            window.location.href = 'review_claim.php?id=' + id;
        }

        function deleteClaim(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_claim.php',
                        method: 'POST',
                        data: {
                            id: id,
                            action: 'delete_claim'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'Claim has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to delete claim. Please try again.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }

        // Filter Functions
        document.getElementById('applyFilters').addEventListener('click', function() {
            const status = document.getElementById('statusFilter').value;
            const type = document.getElementById('typeFilter').value;
            const dateFrom = document.getElementById('dateFromFilter').value;
            const dateTo = document.getElementById('dateToFilter').value;

            const table = $('#claimsTable').DataTable();
            table.columns().search('').draw();

            if (status) {
                table.column(7).search(status, true, false).draw();
            }
            if (type) {
                table.column(2).search(type, true, false).draw();
            }
            if (dateFrom || dateTo) {
                table.draw();
            }
        });

        document.getElementById('clearFilters').addEventListener('click', function() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('typeFilter').value = '';
            document.getElementById('dateFromFilter').value = '';
            document.getElementById('dateToFilter').value = '';

            const table = $('#claimsTable').DataTable();
            table.columns().search('').draw();
            table.search('').draw();
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>

</html>