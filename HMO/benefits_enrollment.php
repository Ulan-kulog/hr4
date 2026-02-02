<?php require_once 'backend.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benefits Enrollment & Management</title>
    <?php include '../INCLUDES/header.php'; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="CSS/benefits_enrollment.css">
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
            <div class="flex flex-col flex-1 overflow-auto">
                <!-- Navbar -->
                <header class="bg-white shadow-sm border-gray-200 border-b">
                    <div class="flex justify-between items-center px-6 py-4">
                        <h1 class="font-bold text-gray-800 text-2xl">Benefits Enrollment & Management</h1>
                        <div class="flex items-center space-x-4">
                            <button onclick="openNewEnrollmentModal()" class="flex items-center bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                                New Enrollment
                            </button>
                            <!-- Create Benefits Button -->
                            <button onclick="openCreateBenefitModal()" class="flex items-center bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="plus-circle" class="mr-2 w-4 h-4"></i>
                                Create Benefits
                            </button>
                            <!-- Create Policy Button -->
                            <button onclick="openCreatePolicyModal()" class="flex items-center bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="file-text" class="mr-2 w-4 h-4"></i>
                                Create Policy
                            </button>
                            <!-- Export Reports Button -->
                            <button onclick="exportAnalyticsCSV()" class="flex items-center bg-gray-800 hover:bg-gray-900 px-4 py-2 rounded-lg text-white transition-colors">
                                <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                                Export Reports
                            </button>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-6">
                    <!-- Stats Cards - NOW FUNCTIONAL -->
                    <div class="gap-6 grid grid-cols-1 md:grid-cols-4 mb-8">
                        <!-- Total Benefits Card -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-[#001f54] text-sm">Total Benefits</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?= $benefits_count ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">
                                        <?= $active_benefits ?> active plans
                                    </p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] p-3 rounded-lg">
                                    <i data-lucide="package" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Enrolled Employees Card -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-[#001f54] text-sm">Enrolled Employees</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?= $enrolled_employees ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">
                                        <?= $pending_enrollments ?> pending
                                    </p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] p-3 rounded-lg">
                                    <i data-lucide="users" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Active Policies Card -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-[#001f54] text-sm">Active Policies</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?= $active_policies ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">
                                        <?= count($policies) ?> total policies
                                    </p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] p-3 rounded-lg">
                                    <i data-lucide="shield-check" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Coverage Rate Card -->
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-[#001f54] text-sm">Coverage Rate</p>
                                    <h3 class="mt-1 font-bold text-3xl"><?= $coverage_rate ?>%</h3>
                                    <p class="mt-1 text-gray-500 text-xs">
                                        Of <?= $total_employees ?> employees
                                    </p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] p-3 rounded-lg">
                                    <i data-lucide="percent" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Optional: Additional Stats Row -->
                    <div class="gap-6 grid grid-cols-1 md:grid-cols-3 mb-8">
                        <div class="bg-white hover:bg-gray-50 shadow hover:shadow-md p-4 border border-gray-100 rounded-xl">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-600 text-sm">Total Employees</p>
                                    <h3 class="mt-1 font-bold text-2xl"><?= $total_employees ?></h3>
                                </div>
                                <div class="flex justify-center items-center bg-gray-100 p-2 rounded-lg">
                                    <i data-lucide="briefcase" class="w-5 h-5 text-gray-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white hover:bg-gray-50 shadow hover:shadow-md p-4 border border-gray-100 rounded-xl">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-600 text-sm">Pending Approvals</p>
                                    <h3 class="mt-1 font-bold text-2xl"><?= $pending_enrollments ?></h3>
                                </div>
                                <div class="flex justify-center items-center bg-yellow-100 p-2 rounded-lg">
                                    <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white hover:bg-gray-50 shadow hover:shadow-md p-4 border border-gray-100 rounded-xl">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-600 text-sm">Upcoming Renewals</p>
                                    <h3 class="mt-1 font-bold text-2xl"><?= $upcoming_renewals ?></h3>
                                </div>
                                <div class="flex justify-center items-center bg-blue-100 p-2 rounded-lg">
                                    <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reduced Size Charts -->
                    <div class="gap-6 grid grid-cols-1 lg:grid-cols-1 mb-6">
                        <div class="bg-white shadow-sm p-4 border border-gray-100 rounded-xl">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-semibold text-gray-800 text-sm">Top Benefits Enrollment Rate</h3>
                                <select id="enrollmentTimeFilter" class="px-3 py-1 border border-gray-300 rounded-lg text-xs">
                                    <option value="all">All Time</option>
                                    <option value="month">This Month</option>
                                    <option value="quarter">This Quarter</option>
                                    <option value="year">This Year</option>
                                </select>
                            </div>
                            <div class="small-chart">
                                <canvas id="enrollmentStatusChart"></canvas>
                            </div>
                            <div class="mt-2 text-gray-500 text-xs text-center">
                                Based on <?= $total_employees ?> employees
                            </div>
                        </div>
                    </div>

                    <!-- Additional Charts Section -->
                    <div class="gap-6 grid grid-cols-1 lg:grid-cols-1 mb-6">
                        <div class="bg-white shadow-sm p-4 border border-gray-100 rounded-xl">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-semibold text-gray-800 text-sm">Department Enrollment</h3>
                                <select id="deptFilter" class="px-3 py-1 border border-gray-300 rounded-lg text-xs">
                                    <option value="all">All Departments</option>
                                    <option value="top5">Top 5 Departments</option>
                                </select>
                            </div>
                            <div class="small-chart">
                                <canvas id="departmentChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits Table Section -->
                    <div class="bg-white shadow-sm mb-6 p-6 border border-gray-100 rounded-xl">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Benefits Management</h3>
                                <p class="text-gray-500 text-sm">Manage all benefit plans and offerings</p>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" placeholder="Search benefits..." class="px-4 py-2 border border-gray-300 rounded-lg w-64" id="searchBenefits">
                                <select class="px-4 py-2 border border-gray-300 rounded-lg" id="benefitTypeFilter">
                                    <option value="">All Types</option>
                                    <option value="fixed">Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                                <select class="px-4 py-2 border border-gray-300 rounded-lg" id="taxableFilter">
                                    <option value="">Taxable Status</option>
                                    <option value="1">Taxable</option>
                                    <option value="0">Non-Taxable</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="w-full" id="benefitsTable">
                                <thead>
                                    <tr class="border-gray-200 border-b">
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Benefit Code</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Benefit Name</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Provider</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Type</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Value</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Taxable</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Status</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($benefits) > 0): ?>
                                        <?php foreach ($benefits as $benefit): ?>
                                            <?php
                                            // Get status badge class
                                            $status_class = 'status-pending';
                                            $status_text = 'Pending';
                                            if (isset($benefit->status)) {
                                                switch ($benefit->status) {
                                                    case 'active':
                                                        $status_class = 'status-active';
                                                        $status_text = 'Active';
                                                        break;
                                                    case 'pending':
                                                        $status_class = 'status-pending';
                                                        $status_text = 'Pending';
                                                        break;
                                                    case 'inactive':
                                                        $status_class = 'status-inactive';
                                                        $status_text = 'Inactive';
                                                        break;
                                                }
                                            }

                                            // Get type badge class
                                            $type_class = 'type-other';
                                            $type_text = 'Other';
                                            if (isset($benefit->benefit_type)) {
                                                switch ($benefit->benefit_type) {
                                                    case 'fixed':
                                                        $type_class = 'type-health';
                                                        $type_text = 'Fixed';
                                                        break;
                                                    case 'percentage':
                                                        $type_class = 'type-retirement';
                                                        $type_text = 'Percentage';
                                                        break;
                                                    case 'condition-based':
                                                        $type_class = 'type-wellness';
                                                        $type_text = 'Condition Based';
                                                        break;
                                                }
                                            }

                                            // Format value
                                            $value = isset($benefit->value) ? '$' . number_format($benefit->value, 2) : '-';
                                            if (isset($benefit->unit) && $benefit->unit == 'percentage') {
                                                $value = isset($benefit->value) ? $benefit->value . '%' : '-';
                                            }

                                            // Taxable status
                                            $taxable_text = isset($benefit->is_taxable) && $benefit->is_taxable == 1 ? 'Yes' : 'No';
                                            $taxable_class = isset($benefit->is_taxable) && $benefit->is_taxable == 1 ? 'text-green-600' : 'text-gray-500';
                                            ?>

                                            <tr class="hover:bg-gray-50 border-gray-100 border-b benefit-row"
                                                data-type="<?= htmlspecialchars($benefit->benefit_type ?? '') ?>"
                                                data-taxable="<?= htmlspecialchars($benefit->is_taxable ?? '') ?>">
                                                <td class="px-4 py-3">
                                                    <span class="font-medium text-gray-900 text-sm">
                                                        <?= htmlspecialchars($benefit->benefit_code ?? 'N/A') ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div>
                                                            <p class="font-medium text-gray-900 text-sm">
                                                                <?= htmlspecialchars($benefit->benefit_name ?? 'Unnamed Benefit') ?>
                                                            </p>
                                                            <p class="text-gray-500 text-xs description-cell" title="<?= htmlspecialchars($benefit->description ?? '') ?>">
                                                                <?= htmlspecialchars(substr($benefit->description ?? '', 0, 50)) . (strlen($benefit->description ?? '') > 50 ? '...' : '') ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="text-gray-700 text-sm">
                                                        <?= htmlspecialchars($benefit->provider_name ?? 'N/A') ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="type-badge <?= $type_class ?>">
                                                        <?= $type_text ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="font-medium text-gray-900 text-sm">
                                                        <?= $value ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="text-sm <?= $taxable_class ?>">
                                                        <?= $taxable_text ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="status-badge <?= $status_class ?>">
                                                        <?= $status_text ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex gap-2">
                                                        <button onclick="viewBenefitDetails(<?= $benefit->id ?>)"
                                                            class="hover:bg-purple-50 p-1 rounded text-purple-600 hover:text-purple-800"
                                                            title="View Details">
                                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                                        </button>

                                                        <button onclick="editBenefit(<?= $benefit->id ?>)"
                                                            class="hover:bg-blue-50 p-1 rounded text-blue-600 hover:text-blue-800"
                                                            title="Edit">
                                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                                        </button>

                                                        <?php if ($benefit->status === 'pending'): ?>
                                                            <button onclick="approveBenefit(<?= $benefit->id ?>)"
                                                                class="hover:bg-green-50 p-1 rounded text-green-600 hover:text-green-800"
                                                                title="Approve">
                                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                            </button>
                                                        <?php endif ?>

                                                        <button onclick="deleteBenefit(<?= $benefit->id ?>)"
                                                            class="hover:bg-red-50 p-1 rounded text-red-600 hover:text-red-800"
                                                            title="Delete">
                                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="px-4 py-6 text-gray-500 text-center">
                                                <div class="flex flex-col items-center">
                                                    <i data-lucide="package" class="mb-2 w-12 h-12 text-gray-300"></i>
                                                    <p class="text-gray-500">No benefits found</p>
                                                    <p class="mt-1 text-gray-400 text-sm">Create your first benefit to get started</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Employee Enrollment Table - FIXED WITH DEPARTMENT JOINS -->
                    <div class="bg-white shadow-sm mb-6 p-6 border border-gray-100 rounded-xl">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Employee Enrollments</h3>
                                <p class="text-gray-500 text-sm">View and manage employee benefit enrollments</p>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" placeholder="Search employees..." class="px-4 py-2 border border-gray-300 rounded-lg w-64" id="searchEmployees">
                                <select class="px-4 py-2 border border-gray-300 rounded-lg" id="benefitFilter">
                                    <option value="">All Benefits</option>
                                    <option value="Premium HMO">Premium HMO</option>
                                    <option value="Dental Insurance">Dental Insurance</option>
                                    <option value="401(k) Match">401(k) Match</option>
                                    <option value="Gym Membership">Gym Membership</option>
                                </select>
                                <select class="px-4 py-2 border border-gray-300 rounded-lg" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-gray-200 border-b">
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Employee</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Employee ID</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Department</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Sub-Department</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Enrolled Benefits</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Total Cost</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Last Updated</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Status</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($employee_benefits) > 0): ?>
                                        <?php foreach ($employee_benefits as $eb): ?>
                                            <tr class="hover:bg-gray-50 border-gray-100 border-b">
                                                <!-- Employee -->
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div class="flex justify-center items-center bg-blue-100 mr-3 rounded-full w-8 h-8">
                                                            <span class="font-medium text-blue-600 text-sm">
                                                                <?= strtoupper(mb_substr($eb['first_name'] ?? '', 0, 1)) ?>
                                                                <?= strtoupper(mb_substr($eb['last_name'] ?? '', 0, 1)) ?>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-gray-900 text-sm">
                                                                <?= htmlspecialchars(($eb['first_name'] ?? '') . ' ' . ($eb['last_name'] ?? '')) ?>
                                                            </p>
                                                            <p class="text-gray-500 text-xs">
                                                                <?= htmlspecialchars($eb['email'] ?? '') ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Employee ID -->
                                                <td class="px-4 py-3">
                                                    <span class="text-gray-700 text-sm">
                                                        <?= htmlspecialchars($eb['employee_code'] ?? 'N/A') ?>
                                                    </span>
                                                </td>

                                                <!-- Department -->
                                                <td class="px-4 py-3">
                                                    <span class="text-gray-700 text-sm">
                                                        <?= htmlspecialchars($eb['department_name'] ?? 'No Department') ?>
                                                    </span>
                                                </td>

                                                <!-- Sub-Department -->
                                                <td class="px-4 py-3">
                                                    <span class="text-gray-700 text-sm">
                                                        <?= htmlspecialchars($eb['sub_department_name'] ?? '-') ?>
                                                    </span>
                                                </td>

                                                <!-- Enrolled Benefits -->
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-wrap gap-1">
                                                        <?php if (!empty($eb['benefits'])): ?>
                                                            <?php foreach ($eb['benefits'] as $benefit): ?>
                                                                <span class="bg-blue-100 px-2 py-1 rounded text-blue-800 text-xs">
                                                                    <?= htmlspecialchars($benefit['benefit_name'] ?? 'Unknown Benefit') ?>
                                                                </span>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <span class="text-gray-400 text-xs">No benefits enrolled</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                                <!-- Total Cost -->
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-col">
                                                        <span class="font-medium text-gray-900 text-sm">
                                                            -
                                                        </span>
                                                        <span class="text-gray-500 text-xs">Employee pays</span>
                                                    </div>
                                                </td>

                                                <!-- Last Updated -->
                                                <td class="px-4 py-3">
                                                    <span class="text-gray-700 text-sm">
                                                        <?= !empty($eb['updated_at']) ? date('Y-m-d', strtotime($eb['updated_at'])) : '-' ?>
                                                    </span>
                                                </td>

                                                <!-- Status -->
                                                <td class="px-4 py-3">
                                                    <?php
                                                    $status = $eb['status'] ?? 'pending';
                                                    $statusMap = [
                                                        'active' => 'bg-green-100 text-green-700',
                                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                                        'cancelled' => 'bg-red-100 text-red-700',
                                                        'expired' => 'bg-gray-100 text-gray-600',
                                                        'inactive' => 'bg-red-100 text-red-700',
                                                    ];

                                                    $statusClass = $statusMap[$status] ?? 'bg-gray-100 text-gray-600';
                                                    ?>
                                                    <span class="px-2 py-1 rounded text-xs font-medium <?= $statusClass ?>">
                                                        <?= ucfirst($status) ?>
                                                    </span>
                                                </td>

                                                <!-- Actions -->
                                                <td class="px-4 py-3">
                                                    <div class="flex gap-2">
                                                        <button onclick="viewEmployeeEnrollment(<?= $eb['employee_id'] ?>)"
                                                            class="hover:bg-purple-50 p-1 rounded text-purple-600 hover:text-purple-800"
                                                            title="View Enrollment">
                                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                                        </button>

                                                        <button onclick="openEditEnrollmentModal(<?= $eb['employee_id'] ?>)"
                                                            class="hover:bg-blue-50 p-1 rounded text-blue-600 hover:text-blue-800"
                                                            title="Edit Enrollment">
                                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                                        </button>

                                                        <button onclick="deleteEmployeeEnrollment(<?= $eb['benefit_enrollment_id'] ?? 0 ?>, <?= $eb['employee_id'] ?>, '<?= addslashes(($eb['first_name'] ?? '') . ' ' . ($eb['last_name'] ?? '')) ?>')"
                                                            class="hover:bg-red-50 p-1 rounded text-red-600 hover:text-red-800"
                                                            title="Delete Enrollment">
                                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="px-4 py-6 text-gray-500 text-center">
                                                <div class="flex flex-col items-center">
                                                    <i data-lucide="users" class="mb-2 w-12 h-12 text-gray-300"></i>
                                                    <p class="text-gray-500">No employee enrollments found</p>
                                                    <p class="mt-1 text-gray-400 text-sm">Create your first enrollment to get started</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Table Footer -->
                        <div class="flex justify-between items-center mt-6 pt-6 border-gray-200 border-t">
                            <div class="text-gray-500 text-sm">
                                Showing <?= count($employee_benefits) ?> of <?= $total_employees ?> employees
                            </div>
                            <div class="flex gap-2">
                                <button class="hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-lg text-sm">
                                    Previous
                                </button>
                                <button class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-lg text-white text-sm">
                                    1
                                </button>
                                <button class="hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-lg text-sm">
                                    2
                                </button>
                                <button class="hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-lg text-sm">
                                    3
                                </button>
                                <button class="hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-lg text-sm">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- NEW: Policies Table Section -->
                    <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Benefit Policies</h3>
                                <p class="text-gray-500 text-sm">Manage company benefit policies and guidelines</p>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" placeholder="Search policies..." class="px-4 py-2 border border-gray-300 rounded-lg w-64">
                                <select class="px-4 py-2 border border-gray-300 rounded-lg">
                                    <option>All Types</option>
                                    <option>Benefit Policy</option>
                                    <option>Enrollment Policy</option>
                                    <option>Compliance</option>
                                    <option>Eligibility Rules</option>
                                </select>
                                <select class="px-4 py-2 border border-gray-300 rounded-lg">
                                    <option>All Status</option>
                                    <option>Active</option>
                                    <option>Draft</option>
                                    <option>Archived</option>
                                    <option>Under Review</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-gray-200 border-b">
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Policy Code</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Policy Name</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Applies to</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Effective Date</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Expiration Date</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Status</th>
                                        <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($policies as $policy): ?>
                                        <tr class="hover:bg-gray-50 border-gray-100 border-b">
                                            <!-- Policy Code + Description -->
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <div class="flex justify-center items-center bg-blue-100 mr-3 rounded-full w-8 h-8">
                                                        <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-900 text-sm">
                                                            <?= htmlspecialchars($policy->policy_code) ?>
                                                        </p>
                                                        <p class="text-gray-500 text-xs">
                                                            <?= htmlspecialchars($policy->description) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Policy Name -->
                                            <td class="px-4 py-3">
                                                <span class="policy-type-badge policy-type-enrollment">
                                                    <?= htmlspecialchars($policy->policy_name) ?>
                                                </span>
                                            </td>

                                            <!-- Applies To -->
                                            <td class="px-4 py-3">
                                                <span class="text-gray-700 text-sm">
                                                    <?= htmlspecialchars($policy->applies_to) ?>
                                                </span>
                                            </td>

                                            <!-- Effective Date -->
                                            <td class="px-4 py-3">
                                                <span class="text-gray-700 text-sm">
                                                    <?= htmlspecialchars($policy->effective_date) ?>
                                                </span>
                                            </td>

                                            <!-- Expiration Date -->
                                            <td class="px-4 py-3">
                                                <span class="text-gray-700 text-sm">
                                                    <?= htmlspecialchars($policy->expiration_date ?? '-')  ?>
                                                </span>
                                            </td>

                                            <!-- Status -->
                                            <td class="px-4 py-3">
                                                <span class="status-badge <?= $policy->status === 'active' ? 'status-active' : 'status-inactive' ?>">
                                                    <?= ucfirst($policy->status) ?>
                                                </span>
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-4 py-3">
                                                <div class="flex gap-2">
                                                    <button onclick="viewPolicyDetails(<?= $policy->id ?>)"
                                                        class="hover:bg-purple-50 p-1 rounded text-purple-600 hover:text-purple-800"
                                                        title="View Policy">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                    </button>

                                                    <button onclick="editPolicy(<?= $policy->id ?>)"
                                                        class="hover:bg-blue-50 p-1 rounded text-blue-600 hover:text-blue-800"
                                                        title="Edit Policy">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                    </button>

                                                    <button onclick="deletePolicy(<?= $policy->id ?>)"
                                                        class="hover:bg-green-50 p-1 rounded text-red-600 hover:text-red-800"
                                                        title="Delete Policy">
                                                        <i data-lucide="trash" class="w-4 h-4"></i>
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <form action="API/delete_policy.php" method="POST" id="deletePolicyForm" style="display: none;">
                                        <input type="hidden" name="id" id="deletePolicyId">
                                    </form>
                                </tbody>
                            </table>
                        </div>

                        <!-- Policy Table Footer -->
                        <div class="flex justify-between items-center mt-6 pt-6 border-gray-200 border-t">
                            <div class="text-gray-500 text-sm">
                                Showing 6 of 28 policies
                            </div>
                            <div class="flex gap-2">
                                <button class="hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-lg text-sm">
                                    Previous
                                </button>
                                <button class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-lg text-white text-sm">
                                    1
                                </button>
                                <button class="hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-lg text-sm">
                                    2
                                </button>
                                <button class="hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-lg text-sm">
                                    3
                                </button>
                                <button class="hover:bg-gray-50 px-3 py-1 border border-gray-300 rounded-lg text-sm">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </main>

                <?php include 'modals/view_policy_modal.php'; ?>
                <?php include 'modals/view_enrollment_modal.php'; ?>
                <?php include 'modals/view_benefit_modal.php'; ?>
                <?php include 'modals/create_benefit_modal.php'; ?>
                <?php include 'modals/edit_benefit_modal.php'; ?>
                <?php include 'modals/edit_enrollment_modal.php'; ?>
                <?php include 'modals/edit_policy_modal.php'; ?>
                <?php include 'modals/enrollment_modal.php'; ?>
                <?php include 'modals/policy_modal.php'; ?>
                <?php include 'modals/report_modal.php'; ?>
            </div>
        </div>

        <script src="js/enrollment.js"></script>
        <script>
            // View Employee Enrollment Function
            function viewEmployeeEnrollment(employeeId) {
                const employeeBenefits = <?php echo json_encode(array_values($employee_benefits)); ?>;
                const employeeData = employeeBenefits.find(emp => emp.employee_id == employeeId);

                if (!employeeData) {
                    console.error('Employee data not found');
                    alert('Employee data not found!');
                    return;
                }

                // Populate Employee Information
                document.getElementById('view_employee_code').value = employeeData.employee_code || '';
                document.getElementById('view_full_name').value =
                    `${employeeData.first_name || ''} ${employeeData.last_name || ''}`;

                // Show department and sub-department
                let departmentText = employeeData.department_name || '';
                if (employeeData.sub_department_name) {
                    departmentText += ` (${employeeData.sub_department_name})`;
                }
                document.getElementById('view_department').value = departmentText;

                document.getElementById('view_email').value = employeeData.email || '';

                // Populate Enrollment Information
                if (employeeData.benefit_enrollment_id) {
                    document.getElementById('view_benefit_enrollment_id').value =
                        `ENR-${String(employeeData.benefit_enrollment_id).padStart(3, '0')}`;
                } else {
                    document.getElementById('view_benefit_enrollment_id').value = 'N/A';
                }

                // Status with badge styling
                const statusElement = document.getElementById('view_status');
                const status = employeeData.status || 'pending';
                const statusMap = {
                    'active': 'bg-green-100 text-green-700',
                    'pending': 'bg-yellow-100 text-yellow-700',
                    'inactive': 'bg-red-100 text-red-700',
                    'cancelled': 'bg-red-100 text-red-700',
                    'expired': 'bg-gray-100 text-gray-600'
                };
                statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                statusElement.className = `px-3 py-2 rounded-lg text-sm font-medium inline-block ${statusMap[status] || 'bg-gray-100 text-gray-600'}`;

                document.getElementById('view_start_date').value = employeeData.start_date || '';
                document.getElementById('view_end_date').value = employeeData.end_date || '';
                document.getElementById('view_payroll_frequency').value = employeeData.payroll_frequency || '';
                document.getElementById('view_payroll_deductible').value = employeeData.payroll_deductible ? 'Yes' : 'No';
                document.getElementById('view_updated_at').value = employeeData.updated_at || '';

                // Populate Benefits Table
                const benefitsTableBody = document.getElementById('benefitsTableBody');
                benefitsTableBody.innerHTML = '';

                if (employeeData.benefits && employeeData.benefits.length > 0) {
                    employeeData.benefits.forEach(benefit => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50 border-gray-100 border-b';

                        // Determine taxable status
                        const taxableText = benefit.is_taxable == 1 ? 'Yes' : 'No';
                        const taxableClass = benefit.is_taxable == 1 ? 'text-green-600' : 'text-gray-500';

                        // Determine benefit value display
                        let valueDisplay = benefit.value || '';
                        if (benefit.unit === 'percentage' && benefit.value) {
                            valueDisplay = `${benefit.value}%`;
                        } else if (benefit.unit === 'fixed' && benefit.value) {
                            valueDisplay = `$${parseFloat(benefit.value).toFixed(2)}`;
                        }

                        // Determine status badge
                        const benefitStatus = benefit.status || 'pending';
                        const benefitStatusMap = {
                            'active': 'bg-green-100 text-green-700',
                            'pending': 'bg-yellow-100 text-yellow-700',
                            'inactive': 'bg-red-100 text-red-700'
                        };
                        const benefitStatusClass = benefitStatusMap[benefitStatus] || 'bg-gray-100 text-gray-600';

                        row.innerHTML = `
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900 text-sm">
                                    ${benefit.benefit_name || 'N/A'}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-700 text-sm">
                                    ${benefit.type ? benefit.type.charAt(0).toUpperCase() + benefit.type.slice(1) : 'N/A'}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900 text-sm">
                                    ${valueDisplay}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm ${taxableClass}">
                                    ${taxableText}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium ${benefitStatusClass}">
                                    ${benefitStatus.charAt(0).toUpperCase() + benefitStatus.slice(1)}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-700 text-sm">
                                    ${benefit.enrollment_id || 'N/A'}
                                </span>
                            </td>
                        `;

                        benefitsTableBody.appendChild(row);
                    });
                } else {
                    // Show empty state
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="6" class="px-4 py-6 text-gray-500 text-center">
                            <div class="flex flex-col items-center">
                                <i data-lucide="package" class="mb-2 w-12 h-12 text-gray-300"></i>
                                <p class="text-gray-500">No benefits enrolled</p>
                            </div>
                        </td>
                    `;
                    benefitsTableBody.appendChild(row);
                }

                // Show the modal
                const modal = document.getElementById('viewEmployeeModal');
                if (modal) {
                    modal.showModal();
                } else {
                    console.error('Modal element not found');
                }

                // Re-initialize icons for any new lucide icons
                if (typeof lucide !== 'undefined' && lucide.createIcons) {
                    lucide.createIcons();
                }
            }

            // Edit Enrollment Function
            function openEditEnrollmentModal(employeeId) {
                const employeeBenefits = <?php echo json_encode(array_values($employee_benefits)); ?>;
                const employeeData = employeeBenefits.find(emp => emp.employee_id == employeeId);

                if (!employeeData) {
                    console.error('Employee data not found');
                    alert('Employee data not found!');
                    return;
                }

                // Populate Employee Information
                document.getElementById('edit_employee_id').value = employeeData.employee_id;
                document.getElementById('edit_benefit_enrollment_id').value = employeeData.benefit_enrollment_id || '';
                document.getElementById('edit_employee_code').value = employeeData.employee_code || '';
                document.getElementById('edit_full_name').value = `${employeeData.first_name || ''} ${employeeData.last_name || ''}`;

                // Show department and sub-department
                let departmentText = employeeData.department_name || '';
                if (employeeData.sub_department_name) {
                    departmentText += ` (${employeeData.sub_department_name})`;
                }
                document.getElementById('edit_department').value = departmentText;

                document.getElementById('edit_email').value = employeeData.email || '';

                // Populate Enrollment Details
                if (employeeData.benefit_enrollment_id) {
                    document.getElementById('edit_benefit_enrollment_id_display').value =
                        `ENR-${String(employeeData.benefit_enrollment_id).padStart(3, '0')}`;
                } else {
                    document.getElementById('edit_benefit_enrollment_id_display').value = 'N/A';
                }
                document.getElementById('edit_status').value = employeeData.status || 'pending';
                document.getElementById('edit_start_date').value = employeeData.start_date || '';
                document.getElementById('edit_end_date').value = employeeData.end_date || '';
                document.getElementById('edit_payroll_frequency').value = employeeData.payroll_frequency || 'weekly';
                document.getElementById('edit_payroll_deductible').checked = employeeData.payroll_deductible == 1;
                document.getElementById('edit_updated_at').value = employeeData.updated_at || '';

                // Populate Benefits List
                const benefitsList = document.getElementById('edit_benefits_list');
                const benefitsCount = document.getElementById('edit_benefits_count');

                benefitsList.innerHTML = '';

                if (employeeData.benefits && employeeData.benefits.length > 0) {
                    benefitsCount.textContent = employeeData.benefits.length;

                    employeeData.benefits.forEach((benefit, index) => {
                        const benefitCard = document.createElement('div');
                        benefitCard.className = 'bg-white p-4 rounded-lg border border-gray-200 mb-2';

                        // Format value based on type
                        let valueDisplay = benefit.value || '0';
                        if (benefit.unit === 'percentage' && benefit.value) {
                            valueDisplay = `${benefit.value}%`;
                        } else if (benefit.unit === 'fixed' && benefit.value) {
                            valueDisplay = `$${parseFloat(benefit.value).toFixed(2)}`;
                        }

                        // Status badge color
                        let statusClass = 'badge-warning';
                        let statusIcon = 'clock';
                        if (benefit.status === 'active') {
                            statusClass = 'badge-success';
                            statusIcon = 'check-circle';
                        } else if (benefit.status === 'inactive') {
                            statusClass = 'badge-error';
                            statusIcon = 'x-circle';
                        }

                        // Taxable status
                        const taxableText = benefit.is_taxable == 1 ? 'Taxable' : 'Non-Taxable';
                        const taxableClass = benefit.is_taxable == 1 ? 'badge-success' : 'badge-info';

                        benefitCard.innerHTML = `
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="flex justify-center items-center bg-blue-100 rounded-lg w-8 h-8">
                                            <i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-bold text-gray-900">${benefit.benefit_name || 'Unnamed Benefit'}</h5>
                                            <p class="text-gray-500 text-sm">Benefit ID: ${benefit.benefit_id || 'N/A'}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="gap-3 grid grid-cols-3 mt-3">
                                        <div class="bg-gray-50 p-2 rounded text-center">
                                            <div class="font-medium text-gray-500 text-sm">Type</div>
                                            <div class="mt-1 font-bold text-gray-900">${benefit.type ? benefit.type.charAt(0).toUpperCase() + benefit.type.slice(1) : 'N/A'}</div>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-2 rounded text-center">
                                            <div class="font-medium text-gray-500 text-sm">Value</div>
                                            <div class="mt-1 font-bold text-gray-900">${valueDisplay}</div>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-2 rounded text-center">
                                            <div class="font-medium text-gray-500 text-sm">Taxable</div>
                                            <div class="mt-1">
                                                <span class="badge badge-sm ${taxableClass}">${taxableText}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ml-4">
                                    <span class="badge ${statusClass} gap-1">
                                        <i data-lucide="${statusIcon}" class="w-3 h-3"></i>
                                        ${benefit.status ? benefit.status.charAt(0).toUpperCase() + benefit.status.slice(1) : 'Pending'}
                                    </span>
                                </div>
                            </div>
                        `;

                        benefitsList.appendChild(benefitCard);
                    });
                } else {
                    benefitsCount.textContent = '0';
                    benefitsList.innerHTML = `
                        <div class="py-8 text-center">
                            <i data-lucide="package-x" class="mx-auto mb-3 w-12 h-12 text-gray-300"></i>
                            <p class="text-gray-500">No benefits enrolled</p>
                            <p class="mt-1 text-gray-400 text-sm">This employee has not enrolled in any benefits</p>
                        </div>
                    `;
                }

                // Show modal
                const modal = document.getElementById('editEnrollmentModal');
                if (modal) {
                    modal.showModal();
                } else {
                    console.error('Modal element not found');
                }

                // Re-initialize icons
                if (typeof lucide !== 'undefined' && lucide.createIcons) {
                    lucide.createIcons();
                }
            }

            // Delete Employee Enrollment
            function deleteEmployeeEnrollment(enrollmentId, employeeId, employeeName) {
                if (confirm(`Are you sure you want to delete enrollment for ${employeeName}?`)) {
                    // AJAX call to delete enrollment
                    fetch('API/delete_enrollment.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                enrollment_id: enrollmentId,
                                employee_id: employeeId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Enrollment deleted successfully!');
                                location.reload();
                            } else {
                                alert('Error deleting enrollment: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting enrollment');
                        });
                }
            }
        </script>

        <script src="js/benefit.js"></script>
        <script src="js/policy.js"></script>
        <script>
            // Initialize lucide icons
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }

            function initializeCharts() {
                // Ensure charts render crisply on high-DPI displays
                if (typeof Chart !== 'undefined') {
                    Chart.defaults.devicePixelRatio = window.devicePixelRatio || 1;
                }
                // Enrollment Status Chart
                const statusCtx = document.getElementById('enrollmentStatusChart');
                if (statusCtx) {
                    // give the chart container an explicit height so Chart.js can size the canvas correctly
                    if (statusCtx.parentElement) statusCtx.parentElement.style.height = '240px';
                    const enrollLabels = <?= json_encode($enrollment_labels) ?>;
                    const enrollData = <?= json_encode($enrollment_data) ?>;

                    // If no data, show placeholder
                    const finalEnrollLabels = (enrollData.length === 0 || enrollData.every(val => val === 0)) ? ['Health Insurance', 'Dental', 'Vision', '401(k)', 'Gym Membership'] :
                        enrollLabels;

                    const finalEnrollData = (enrollData.length === 0 || enrollData.every(val => val === 0)) ? [78, 65, 45, 85, 60] :
                        enrollData;

                    // Expose enrollment chart data for export
                    window.enrollmentChartData = {
                        labels: finalEnrollLabels,
                        data: finalEnrollData,
                        total_employees: <?= $total_employees ?>
                    };

                    new Chart(statusCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: finalEnrollLabels,
                            datasets: [{
                                label: 'Enrolled Employees',
                                data: finalEnrollData,
                                backgroundColor: '#3b82f6',
                                borderRadius: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        font: {
                                            size: 9
                                        },
                                        callback: function(value) {
                                            return value;
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 9
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Department Chart
                const deptCtx = document.getElementById('departmentChart');
                if (deptCtx) {
                    // give the chart container an explicit height so Chart.js can size the canvas correctly
                    if (deptCtx.parentElement) deptCtx.parentElement.style.height = '300px';
                    const deptData = <?= json_encode($department_enrollment) ?>;
                    const deptLabels = deptData.map(item => item.department_name || 'Unknown');
                    const deptCounts = deptData.map(item => (item.enrolled_count !== undefined) ? parseInt(item.enrolled_count) : 0);

                    // If no data, show placeholder
                    const finalDeptLabels = deptLabels.length > 0 ? deptLabels : ['HR', 'IT', 'Sales', 'Marketing', 'Finance'];
                    const finalDeptCounts = deptCounts.length > 0 ? deptCounts : [0, 0, 0, 0, 0];

                    // Expose department chart data for export
                    window.departmentChartData = deptData;

                    new Chart(deptCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: finalDeptLabels,
                            datasets: [{
                                label: 'Enrolled Employees',
                                data: finalDeptCounts,
                                backgroundColor: '#10b981',
                                borderRadius: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y', // horizontal
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        font: {
                                            size: 9
                                        },
                                        callback: function(value) {
                                            return value;
                                        }
                                    }
                                },
                                y: {
                                    ticks: {
                                        font: {
                                            size: 9
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Export analytics CSV by posting current chart data to the server and triggering download
            function exportAnalyticsCSV() {
                const payload = {
                    enrollment: window.enrollmentChartData || {
                        labels: [],
                        data: [],
                        total_employees: 0
                    },
                    department: window.departmentChartData || []
                };

                fetch('API/export_analytics.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Export failed');
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        const now = new Date();
                        const ts = now.toISOString().slice(0, 19).replace(/[:T]/g, '-');
                        a.download = `analytics_export_${ts}.csv`;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Failed to export analytics.');
                    });
            }

            // Initialize charts on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize charts after a short delay to ensure DOM is fully loaded
                setTimeout(() => {
                    if (typeof initializeCharts === 'function') {
                        initializeCharts();
                    }
                }, 100);

                // Add search functionality for employee table
                const searchInput = document.getElementById('searchEmployees');
                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        const rows = document.querySelectorAll('#enrollmentsTable tbody tr');

                        rows.forEach(row => {
                            const employeeName = row.querySelector('td:nth-child(1) .font-medium').textContent.toLowerCase();
                            const employeeId = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                            const department = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                            const subDepartment = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

                            if (employeeName.includes(searchTerm) ||
                                employeeId.includes(searchTerm) ||
                                department.includes(searchTerm) ||
                                subDepartment.includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                }

                // Add filter functionality for benefit type
                const benefitFilter = document.getElementById('benefitFilter');
                if (benefitFilter) {
                    benefitFilter.addEventListener('change', function(e) {
                        const selectedBenefit = e.target.value;
                        const rows = document.querySelectorAll('#enrollmentsTable tbody tr');

                        rows.forEach(row => {
                            const benefitsCell = row.querySelector('td:nth-child(5)');
                            if (!selectedBenefit || benefitsCell.textContent.includes(selectedBenefit)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                }

                // Add filter functionality for status
                const statusFilter = document.getElementById('statusFilter');
                if (statusFilter) {
                    statusFilter.addEventListener('change', function(e) {
                        const selectedStatus = e.target.value;
                        const rows = document.querySelectorAll('#enrollmentsTable tbody tr');

                        rows.forEach(row => {
                            const statusCell = row.querySelector('td:nth-child(8) span');
                            if (!selectedStatus || statusCell.textContent.toLowerCase().includes(selectedStatus.toLowerCase())) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                }
            });
        </script>
    </div>
</body>

</html>