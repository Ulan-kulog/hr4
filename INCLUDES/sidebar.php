<?php
$role = $_SESSION['role'] ?? 'guest';
// $permissions = include 'USM/role_permissions.php';
// $allowed_modules = $permissions[$role] ?? [];
// $is_supervisor = ($role === 'supervisor' || $role === 'admin');

// Define base path for consistent URL structure
$base_url = '/HR_4/'; // Correct full URL

// Get current page for active state highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="bg-[#001f54] pt-5 pb-4 flex flex-col fixed md:relative h-full transition-all duration-300 ease-in-out shadow-xl transform -translate-x-full md:transform-none md:translate-x-0 sidebar-expandable" id="sidebar">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between flex-shrink-0 px-4 mb-6 text-center">
        <h1 class="text-xl font-bold text-white flex items-center gap-2">
            <img src="<?php echo $base_url; ?>images/tagline_no_bg.png" 
                alt="Logo" 
                class="h-25 w-auto">
        </h1>
    </div>

    <!-- Navigation Menu -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <nav class="flex-1 px-2 space-y-1 overflow-y-auto scrollbar-hide">
            <!-- =================== -->
            <!-- DASHBOARD SECTION -->
            <!-- =================== -->
            <div class="px-4 py-2">
                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Dashboard</p>
            </div>

            <!-- Main Dashboard -->
            <a href="<?php echo $base_url; ?>/ANALYTICS/main_analytics.php" class="block">
                <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group <?php echo ($current_page == 'main_analytics.php') ? 'bg-blue-600/30 border-l-4 border-[#F7B32B]' : ''; ?>">
                    <div class="p-1.5 rounded-lg bg-blue-700/50 transition-colors">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text font-semibold">HR Analytics Dashboard</span>
                </div>
            </a>

            <!-- =========================== -->
            <!-- HR MANAGEMENT SECTION -->
            <!-- =========================== -->
            <div class="px-4 py-2 mt-4">
                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">HR Management</p>
            </div>
            
            <!-- Employee Management Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="employee-management">
                <button class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white dropdown-toggle <?php echo (in_array($current_page, ['manage_employee.php', 'departments.php', 'aquasition.php', 'under_review.php'])) ? 'bg-blue-600/30 border-l-4 border-[#F7B32B]' : ''; ?>">
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-blue-800/30 transition-colors">
                            <i data-lucide="users" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Employee Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-auto transition-transform duration-200 dropdown-arrow"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="py-2 space-y-1">
                        <!-- All Employees -->
                        <a href="<?php echo $base_url; ?>/CHM/manage_employee.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'manage_employee.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="users" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>All Employees</span>
                        </a>
                        
                        <!-- Under Review -->
                        <a href="<?php echo $base_url; ?>/CHM/main.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'under_review.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="clipboard-check" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Under Review</span>
                            <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo getUnderReviewCount(); ?></span>
                        </a>
                        
                        <!-- Departments -->
                        <a href="<?php echo $base_url; ?>/CHM/departments.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'departments.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="building" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Departments</span>
                        </a>
                        
                        <!-- Employee Acquisition -->
                        <a href="<?php echo $base_url; ?>/CHM/aquasition.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'aquasition.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="user-plus" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Hiring & Onboarding</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- =========================== -->
            <!-- PAYROLL SECTION -->
            <!-- =========================== -->
            <div class="px-4 py-2 mt-4">
                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Payroll</p>
            </div>

            <!-- Payroll Processing Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="payroll-processing">
                <button class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white dropdown-toggle <?php echo (in_array($current_page, ['processing.php', 'history.php'])) ? 'bg-blue-600/30 border-l-4 border-[#F7B32B]' : ''; ?>">
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-blue-800/30 transition-colors">
                            <i data-lucide="dollar-sign" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Payroll Processing</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-auto transition-transform duration-200 dropdown-arrow"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="py-2 space-y-1">
                        <!-- Current Payroll -->
                        <a href="<?php echo $base_url; ?>/PAYROLL/processing.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'processing.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="calculator" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Current Payroll</span>
                            <span class="ml-auto bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo date('M Y'); ?></span>
                        </a>
                        
                        <!-- Under Review Payroll -->
                        <a href="<?php echo $base_url; ?>/PAYROLL/sub-modules/under_review.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'under_review.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="clock" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Under Review</span>
                            <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo getPendingPayrollCount(); ?></span>
                        </a>
                        
                        <!-- Payroll History -->
                        <a href="<?php echo $base_url; ?>/PAYROLL/history.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'history.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="history" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Payroll History</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- =========================== -->
            <!-- COMPENSATION & BENEFITS -->
            <!-- =========================== -->
            <div class="px-4 py-2 mt-4">
                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Compensation & Benefits</p>
            </div>

            <!-- Compensation Management Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="compensation-management">
                <button class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white dropdown-toggle <?php echo (in_array($current_page, ['core_compensation.php', 'strat_planning.php', 'performance_linked.php', 'administration.php', 'Hospitality.php', 'analytics.php'])) ? 'bg-blue-600/30 border-l-4 border-[#F7B32B]' : ''; ?>">
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-blue-800/30 transition-colors">
                            <i data-lucide="award" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Compensation</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-auto transition-transform duration-200 dropdown-arrow"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="py-2 space-y-1">
                        <!-- Salary Structure -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/core_compensation.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'core_compensation.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="dollar-sign" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Salary Structure</span>
                        </a>
                        
                        <!-- Performance Bonus -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/performance_linked.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'performance_linked.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="trending-up" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Performance Bonus</span>
                        </a>

                        <!-- Hospitality Pay -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/Hospitality.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'Hospitality.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="utensils" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Hospitality Pay</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Benefits Administration Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="benefits-management">
                <button class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white dropdown-toggle <?php echo (in_array($current_page, ['benefits_enrollment.php', 'HMO_provider_network.php', 'claims_reimbursement.php'])) ? 'bg-blue-600/30 border-l-4 border-[#F7B32B]' : ''; ?>">
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-blue-800/30 transition-colors">
                            <i data-lucide="heart" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Benefits & HMO</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-auto transition-transform duration-200 dropdown-arrow"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="py-2 space-y-1">
                        <!-- Benefits Enrollment -->
                        <a href="<?php echo $base_url; ?>/HMO/benefits_enrollment.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'benefits_enrollment.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="clipboard-check" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Benefits Enrollment</span>
                        </a>
                        
                        <!-- HMO Providers -->
                        <a href="<?php echo $base_url; ?>/HMO/HMO_provider_network.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'HMO_provider_network.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="shield" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>HMO Providers</span>
                        </a>
                        
                        <!-- Claims Processing -->
                        <a href="<?php echo $base_url; ?>/HMO/claims_reimbursement.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'claims_reimbursement.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="file-text" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Claims Processing</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- =========================== -->
            <!-- PERFORMANCE & ANALYTICS -->
            <!-- =========================== -->
           

            <!-- =========================== -->
            <!-- ADMINISTRATION -->
            <!-- =========================== -->
            <div class="px-4 py-2 mt-4">
                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Administration</p>
            </div>

            <!-- User & System Management -->
            <div class="relative menu-dropdown" data-dropdown="system-management">
                <button class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white dropdown-toggle <?php echo (in_array($current_page, ['department_accounts.php', 'audit_trail&transaction.php', 'role_management.php'])) ? 'bg-blue-600/30 border-l-4 border-[#F7B32B]' : ''; ?>">
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-blue-800/30 transition-colors">
                            <i data-lucide="settings" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">System Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-auto transition-transform duration-200 dropdown-arrow"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="py-2 space-y-1">
                        <!-- User Accounts -->
                        <a href="<?php echo $base_url; ?>/USM/department_accounts.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'department_accounts.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="user-cog" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>User Accounts</span>
                        </a>
                       
                        <!-- Audit Trail -->
                        <a href="<?php echo $base_url; ?>/USM/audit_trail&transaction.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'audit_trail&transaction.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="history" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Audit Trail</span>
                        </a>

                        <!-- Role Management -->
                        <a href="<?php echo $base_url; ?>/USM/role_management.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'role_management.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="shield" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Role Management</span>
                        </a>

                        <!-- System Settings -->
                        <a href="<?php echo $base_url; ?>/settings/general.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8 <?php echo ($current_page == 'general.php') ? 'bg-blue-600/30 text-white' : ''; ?>">
                            <i data-lucide="settings-2" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>System Settings</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- =========================== -->
            <!-- USER SECTION -->
            <!-- =========================== -->
            <div class="px-4 py-2 mt-4">
                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">User</p>
            </div>

            <!-- User Profile -->
            <a href="<?php echo $base_url; ?>/profile.php" class="block">
                <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group <?php echo ($current_page == 'profile.php') ? 'bg-blue-600/30 border-l-4 border-[#F7B32B]' : ''; ?>">
                    <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                        <i data-lucide="user" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">My Profile</span>
                </div>
            </a>

            <!-- Help & Support -->
            <a href="<?php echo $base_url; ?>/help.php" class="block">
                <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group <?php echo ($current_page == 'help.php') ? 'bg-blue-600/30 border-l-4 border-[#F7B32B]' : ''; ?>">
                    <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                        <i data-lucide="help-circle" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">Help & Support</span>
                </div>
            </a>

            <!-- Logout -->
            <form action="<?php echo $base_url; ?>/USM/logout.php" method="POST" class="px-4 py-3">
                <button type="submit" class="flex items-center w-full text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
                    <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                        <i data-lucide="log-out" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">Logout</span>
                </button>
            </form>
        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-blue-700/50 mt-auto">
        <div class="flex items-center justify-center space-x-2">
            <div class="p-2 bg-blue-800/30 rounded-lg">
                <i data-lucide="bell" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs text-blue-200 font-medium">System Status</p>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <p class="text-xs text-blue-300">All systems operational</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper functions for dynamic counts
function getUnderReviewCount() {
    // You would typically fetch this from your database
    // For now, return a placeholder
    return "5";
}

function getPendingPayrollCount() {
    // You would typically fetch this from your database
    // For now, return a placeholder
    return "3";
}
?>

<style>
/* Smooth dropdown animations */
.dropdown-content {
    transition: max-height 0.3s ease-in-out, opacity 0.2s ease-in-out;
    max-height: 0;
    overflow: hidden;
}

.menu-dropdown.active .dropdown-content {
    max-height: 500px;
}

.menu-dropdown.active .dropdown-arrow {
    transform: rotate(180deg);
}

/* Active state for dropdown toggle */
.menu-dropdown.active .dropdown-toggle {
    background-color: rgba(59, 130, 246, 0.5);
}

/* Active page border highlight */
.border-l-4 {
    border-left-width: 4px;
}

.border-\[\#F7B32B\] {
    border-color: #F7B32B;
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
    -ms-overflow-style: none;  /* Internet Explorer 10+ */
    scrollbar-width: none;  /* Firefox */
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;  /* Safari and Chrome */
}

/* Ensure proper spacing when dropdown opens */
.menu-dropdown {
    margin-bottom: 0;
}

/* Adjust spacing for following modules when dropdown is open */
.menu-dropdown.active + *,
.menu-dropdown.active ~ * {
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
    background: rgba(255,255,255,0.1);
    border-radius: 2px;
}

.dropdown-content::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 2px;
}

.dropdown-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.5);
}

/* Prevent layout shift when dropdown opens */
nav {
    display: flex;
    flex-direction: column;
}

nav > * {
    flex-shrink: 0;
}

/* Hover effects for dropdown toggle */
.dropdown-toggle:hover {
    background-color: rgba(59, 130, 246, 0.5);
}

/* Active page highlighting */
.bg-blue-600\/30 {
    background-color: rgba(37, 99, 235, 0.3);
}

/* Submenu item hover effects */
.group\/item:hover .group-hover\/item\:text-white {
    color: white;
}

/* Sidebar footer styling */
.border-blue-700\/50 {
    border-color: rgba(29, 78, 216, 0.5);
}

/* Logo styling */
.h-25 {
    height: 2.5rem;
}

/* Badge styling for counts */
.text-xs {
    font-size: 0.75rem;
    line-height: 1rem;
}

.px-2 {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}

.py-0\.5 {
    padding-top: 0.125rem;
    padding-bottom: 0.125rem;
}

.bg-yellow-500 {
    background-color: #eab308;
}

.bg-blue-500 {
    background-color: #3b82f6;
}

.rounded-full {
    border-radius: 9999px;
}

/* Section divider styling */
.px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
}

.py-2 {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}

.mt-4 {
    margin-top: 1rem;
}

.text-blue-300 {
    color: #93c5fd;
}

.uppercase {
    text-transform: uppercase;
}

.tracking-wider {
    letter-spacing: 0.05em;
}

.font-semibold {
    font-weight: 600;
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
    
    // Auto-expand dropdown if current page is in that section
    function autoExpandActiveDropdown() {
        const activeLinks = document.querySelectorAll('.dropdown-content a.bg-blue-600\\/30');
        
        activeLinks.forEach(link => {
            const dropdown = link.closest('.menu-dropdown');
            if (dropdown) {
                dropdown.classList.add('active');
            }
        });
    }
    
    // Run on page load
    autoExpandActiveDropdown();
    
    // Keep dropdown open on page refresh if current page is in that section
    const currentPath = window.location.pathname;
    const dropdowns = document.querySelectorAll('.menu-dropdown');
    
    dropdowns.forEach(dropdown => {
        const dropdownLinks = dropdown.querySelectorAll('.dropdown-content a');
        const shouldBeActive = Array.from(dropdownLinks).some(link => {
            return link.href && currentPath.includes(link.getAttribute('href').split('/').pop());
        });
        
        if (shouldBeActive) {
            dropdown.classList.add('active');
        }
    });
    
    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && activeDropdown) {
            activeDropdown.classList.remove('active');
            activeDropdown = null;
        }
    });
});
</script>