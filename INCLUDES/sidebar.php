<?php
$role = $_SESSION['role'] ?? 'guest';
// $permissions = include 'USM/role_permissions.php';
// $allowed_modules = $permissions[$role] ?? [];
// $is_supervisor = ($role === 'supervisor' || $role === 'admin');

// Define base path for consistent URL structure
$base_url = '/HR_4'; // Correct full URL
?>

<div class="fixed md:relative flex flex-col bg-[#001f54] shadow-xl pt-5 pb-4 h-full md:transform-none transition-all -translate-x-full md:translate-x-0 duration-300 ease-in-out transform sidebar-expandable" id="sidebar">
    <!-- Sidebar Header -->
    <div class="flex flex-shrink-0 justify-between items-center mb-6 px-4 text-center">
        <h1 class="flex items-center gap-2 font-bold text-white text-xl">
            <img src="<?php echo $base_url; ?>images/tagline_no_bg.png"
                alt="Logo"
                class="w-auto h-25">
        </h1>
    </div>

    <!-- Navigation Menu -->
    <div class="flex flex-col flex-1 overflow-hidden">
        <nav class="flex-1 space-y-1 px-2 overflow-y-auto scrollbar-hide">
            <!-- MAIN DASHBOARD SECTION -->
            <div class="px-4 py-2">
                <p class="font-semibold text-blue-300 text-xs uppercase tracking-wider">Main Dashboard</p>
            </div>

            <!-- HR Analytics Dashboard - Now Main Dashboard -->
            <a href="<?php echo $base_url; ?>/ANALYTICS/main_analytics.php" class="block">
                <div class="group flex items-center bg-blue-600/30 hover:bg-blue-600/50 px-4 py-3 rounded-lg font-medium text-white text-sm transition-all">
                    <div class="bg-blue-700/50 p-1.5 rounded-lg transition-colors">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="ml-3 font-semibold sidebar-text">HR Analytics Dashboard</span>
                </div>
            </a>

            <!-- HUMAN CAPITAL MANAGEMENT SECTION -->
            <div class="mt-4 px-4 py-2">
                <p class="font-semibold text-blue-300 text-xs uppercase tracking-wider">Human Capital Management</p>
            </div>

            <!-- Core Human Capital Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="core-human-capital">
                <button class="flex justify-between items-center hover:bg-blue-600/50 px-4 py-3 rounded-lg w-full font-medium text-white text-sm transition-all dropdown-toggle">
                    <div class="flex items-center">
                        <div class="bg-blue-800/30 p-1.5 rounded-lg transition-colors">
                            <i data-lucide="users" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Core Human Capital</span>
                    </div>
                    <i data-lucide="chevron-down" class="ml-auto w-4 h-4 transition-transform duration-200 dropdown-arrow"></i>
                </button>

                <!-- Dropdown Menu -->
                <div class="max-h-0 overflow-hidden transition-all duration-300 dropdown-content">
                    <div class="space-y-1 py-2">
                        <!-- Employee Management -->
                        <a href="<?php echo $base_url; ?>/CHM/manage_employee.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="user-cog" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Employee Management</span>
                        </a>

                        <!-- Departments -->
                        <a href="<?php echo $base_url; ?>/CHM/departments.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="building" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Departments</span>
                        </a>

                        <!-- Employee Acquisition -->
                        <a href="<?php echo $base_url; ?>/CHM/aquasition.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="user-plus" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Employee Acquisition</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Payroll Management Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="payroll-management">
                <button class="flex justify-between items-center hover:bg-blue-600/50 px-4 py-3 rounded-lg w-full font-medium text-white text-sm transition-all dropdown-toggle">
                    <div class="flex items-center">
                        <div class="bg-blue-800/30 p-1.5 rounded-lg transition-colors">
                            <i data-lucide="dollar-sign" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Payroll Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="ml-auto w-4 h-4 transition-transform duration-200 dropdown-arrow"></i>
                </button>

                <!-- Dropdown Menu -->
                <div class="max-h-0 overflow-hidden transition-all duration-300 dropdown-content">
                    <div class="space-y-1 py-2">
                        <!-- Payroll Processing -->
                        <a href="<?php echo $base_url; ?>/PAYROLL/processing.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="calculator" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Payroll Processing</span>
                        </a>

                        <!-- Payroll Overview -->
                        <a href="<?php echo $base_url; ?>/PAYROLL/overview.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="dollar-sign" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Payroll Overview</span>
                        </a>

                        <!-- Payroll Analytics -->
                        <a href="<?php echo $base_url; ?>/PAYROLL/analytics.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="trending-up" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Payroll Analytics</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Compensation Planning Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="compensation-planning">
                <button class="flex justify-between items-center hover:bg-blue-600/50 px-4 py-3 rounded-lg w-full font-medium text-white text-sm transition-all dropdown-toggle">
                    <div class="flex items-center">
                        <div class="bg-blue-800/30 p-1.5 rounded-lg transition-colors">
                            <i data-lucide="dollar-sign" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Compensation</span>
                    </div>
                    <i data-lucide="chevron-down" class="ml-auto w-4 h-4 transition-transform duration-200 dropdown-arrow"></i>
                </button>

                <!-- Dropdown Menu -->
                <div class="max-h-0 overflow-hidden transition-all duration-300 dropdown-content">
                    <div class="space-y-1 py-2">
                        <!-- Core Compensation Management -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/core_compensation.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="dollar-sign" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Core Compensation</span>
                        </a>

                        <!-- Strategic Planning & Analysis -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/strat_planning.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="trending-up" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Strategic Planning</span>
                        </a>

                        <!-- Performance-Linked Compensation -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/performance_linked.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="award" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Performance Compensation</span>
                        </a>

                        <!-- Compliance & Administration -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/administration.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="shield-check" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Compliance & Admin</span>
                        </a>

                        <!-- Hospitality-Specific Modules -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/Hospitality.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="utensils" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Hospitality Modules</span>
                        </a>

                        <!-- Analytics & Reporting -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/analytics.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="bar-chart-3" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Analytics & Reporting</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- HMO & Benefits Administration Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="hmo-benefits">
                <button class="flex justify-between items-center hover:bg-blue-600/50 px-4 py-3 rounded-lg w-full font-medium text-white text-sm transition-all dropdown-toggle">
                    <div class="flex items-center">
                        <div class="bg-blue-800/30 p-1.5 rounded-lg transition-colors">
                            <i data-lucide="heart" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">HMO & Benefits</span>
                    </div>
                    <i data-lucide="chevron-down" class="ml-auto w-4 h-4 transition-transform duration-200 dropdown-arrow"></i>
                </button>

                <!-- Dropdown Menu -->
                <div class="max-h-0 overflow-hidden transition-all duration-300 dropdown-content">
                    <div class="space-y-1 py-2">
                        <!-- Benefits Enrollment & Management -->
                        <a href="<?php echo $base_url; ?>/HMO/benefits_enrollment.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="clipboard-check" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Benefits Enrollment</span>
                        </a>

                        <!-- HMO Provider Network -->
                        <a href="<?php echo $base_url; ?>/HMO/HMO_provider_network.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="building-2" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Provider Network</span>
                        </a>

                        <!-- Claims & Reimbursement -->
                        <a href="<?php echo $base_url; ?>/HMO/claims_reimbursement.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="file-text" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Claims Processing</span>
                        </a>

                        <!-- Benefits Analytics -->
                        <a href="<?php echo $base_url; ?>/HMO/benefits.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="trending-up" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Benefits Analytics</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- USER MANAGEMENT SECTION -->
            <div class="mt-4 px-4 py-2">
                <p class="font-semibold text-blue-300 text-xs uppercase tracking-wider">Administration</p>
            </div>

            <!-- User Management Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="user-management">
                <button class="flex justify-between items-center hover:bg-blue-600/50 px-4 py-3 rounded-lg w-full font-medium text-white text-sm transition-all dropdown-toggle">
                    <div class="flex items-center">
                        <div class="bg-blue-800/30 p-1.5 rounded-lg transition-colors">
                            <i data-lucide="user-cog" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">User Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="ml-auto w-4 h-4 transition-transform duration-200 dropdown-arrow"></i>
                </button>

                <!-- Dropdown Menu -->
                <div class="max-h-0 overflow-hidden transition-all duration-300 dropdown-content">
                    <div class="space-y-1 py-2">
                        <a href="<?php echo $base_url; ?>/USM/department_accounts.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="users" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Department Accounts</span>
                        </a>

                        <!-- Added Department Logs -->
                        <a href="<?php echo $base_url; ?>/USM/department_logs.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="file-text" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Department Logs</span>
                        </a>

                        <a href="<?php echo $base_url; ?>/USM/audit_trail&transaction.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="history" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Audit Trail & Transaction</span>
                        </a>

                        <a href="<?php echo $base_url; ?>/USM/role_management.php" class="group/item flex items-center hover:bg-blue-600/30 ml-8 px-4 py-2 rounded-lg text-blue-100 hover:text-white text-sm transition-all">
                            <i data-lucide="shield" class="mr-3 w-4 h-4 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Role Management</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <a href="<?php echo $base_url; ?>/settings/general.php" class="block">
                <div class="group flex items-center hover:bg-blue-600/50 px-4 py-3 rounded-lg font-medium text-white text-sm transition-all">
                    <div class="bg-blue-800/30 group-hover:bg-blue-700/50 p-1.5 rounded-lg transition-colors">
                        <i data-lucide="settings" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">Settings</span>
                </div>
            </a>

            <!-- Account Section -->
            <div class="mt-4 px-4 py-2">
                <p class="font-semibold text-blue-300 text-xs uppercase tracking-wider">Account</p>
            </div>

            <!-- Profile -->
            <a href="<?php echo $base_url; ?>/profile.php" class="block">
                <div class="group flex items-center hover:bg-blue-600/50 px-4 py-3 rounded-lg font-medium text-white text-sm transition-all">
                    <div class="bg-blue-800/30 group-hover:bg-blue-700/50 p-1.5 rounded-lg transition-colors">
                        <i data-lucide="user" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">Profile</span>
                </div>
            </a>

            <!-- Logout -->
            <form action="<?php echo $base_url; ?>/USM/logout.php" method="POST" class="px-4 py-3">
                <button type="submit" class="group flex items-center hover:bg-blue-600/50 rounded-lg w-full font-medium text-white text-sm transition-all">
                    <div class="bg-blue-800/30 group-hover:bg-blue-700/50 p-1.5 rounded-lg transition-colors">
                        <i data-lucide="log-out" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">Logout</span>
                </button>
            </form>
        </nav>
    </div>
</div>

<style>
    /* Smooth dropdown animations */
    .dropdown-content {
        transition: max-height 0.3s ease-in-out, opacity 0.2s ease-in-out;
        max-height: 0;
        overflow: hidden;
    }

    .menu-dropdown.active .dropdown-content {
        max-height: 200px;
    }

    .menu-dropdown.active .dropdown-arrow {
        transform: rotate(180deg);
    }

    /* Active state for dropdown toggle */
    .menu-dropdown.active .dropdown-toggle {
        background-color: rgba(59, 130, 246, 0.5);
    }

    /* Dashboard highlight styles */
    .bg-blue-600\/30 {
        background-color: rgba(37, 99, 235, 0.3);
    }

    .bg-blue-700\/50 {
        background-color: rgba(29, 78, 216, 0.5);
    }

    /* Hide scrollbar but keep functionality */
    .scrollbar-hide {
        -ms-overflow-style: none;
        /* Internet Explorer 10+ */
        scrollbar-width: none;
        /* Firefox */
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
        /* Safari and Chrome */
    }

    /* Ensure proper spacing when dropdown opens */
    .menu-dropdown {
        margin-bottom: 0;
    }

    /* Adjust spacing for following modules when dropdown is open */
    .menu-dropdown.active+*,
    .menu-dropdown.active~* {
        transition: margin-top 0.3s ease-in-out;
    }

    /* Ensure sidebar content flows naturally */
    .sidebar-expandable {
        transition: all 0.3s ease-in-out;
    }

    /* Smooth scrolling for dropdown content */
    .dropdown-content::-webkit-scrollbar {
        width: 4px;
    }

    .dropdown-content::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
    }

    .dropdown-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }

    .dropdown-content::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    /* Prevent layout shift when dropdown opens */
    nav {
        display: flex;
        flex-direction: column;
    }

    nav>* {
        flex-shrink: 0;
    }

    /* Hover effects for dropdown toggle */
    .dropdown-toggle:hover {
        background-color: rgba(59, 130, 246, 0.5);
    }

    .dropdown-toggle:hover .dropdown-arrow {
        transform: rotate(180deg);
    }

    /* Smooth scrolling behavior */
    .scrollbar-hide {
        scroll-behavior: smooth;
    }

    /* Ensure proper scrolling area */
    .overflow-hidden {
        overflow: hidden;
    }

    .overflow-y-auto {
        overflow-y: auto;
    }
</style>

<script>
    // Initialize Lucide icons and handle dropdown interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Handle dropdown click events
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        let activeDropdown = null;

        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const dropdown = this.closest('.menu-dropdown');
                const isCurrentlyActive = dropdown.classList.contains('active');

                // Close all dropdowns first
                document.querySelectorAll('.menu-dropdown').forEach(d => {
                    d.classList.remove('active');
                });

                // If the clicked dropdown wasn't active, open it
                if (!isCurrentlyActive) {
                    dropdown.classList.add('active');
                    activeDropdown = dropdown;
                } else {
                    activeDropdown = null;
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.menu-dropdown')) {
                document.querySelectorAll('.menu-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
                activeDropdown = null;
            }
        });

        // Close dropdown when clicking on a link inside it
        document.querySelectorAll('.dropdown-content a').forEach(link => {
            link.addEventListener('click', function() {
                this.closest('.menu-dropdown').classList.remove('active');
                activeDropdown = null;
            });
        });

        // Handle dropdown hover states for better UX (optional)
        const dropdowns = document.querySelectorAll('.menu-dropdown');

        dropdowns.forEach(dropdown => {
            // Keep hover effects for visual feedback, but don't auto-open
            dropdown.addEventListener('mouseenter', function() {
                if (!this.classList.contains('active')) {
                    this.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
                }
            });

            dropdown.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.backgroundColor = '';
                }
            });
        });
    });
</script>