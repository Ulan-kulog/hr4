<?php
$role = $_SESSION['role'] ?? 'guest';
// $permissions = include 'USM/role_permissions.php';
// $allowed_modules = $permissions[$role] ?? [];
// $is_supervisor = ($role === 'supervisor' || $role === 'admin');

// Define base path for consistent URL structure
$base_url = '/HR_4'; // Correct full URL
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
    <div class="flex-1 flex flex-col overflow-hidden hover:overflow-y-auto">
        <nav class="flex-1 px-2 space-y-1">
            <!-- HUMAN CAPITAL MANAGEMENT SECTION -->
            <div class="px-4 py-2 mt-2">
                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Human Capital Management</p>
            </div>
            
            <!-- Core Human Capital Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="core-human-capital">
                <button class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white dropdown-toggle">
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-blue-800/30 transition-colors">
                            <i data-lucide="users" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Core Human Capital</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-auto transition-transform duration-200 dropdown-arrow"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="py-2 space-y-1">
                        <!-- Employee Management -->
                        <a href="<?php echo $base_url; ?>/CHM/manage_employee.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="user-cog" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Employee Management</span>
                        </a>
                        
                        <!-- Departments -->
                        <a href="<?php echo $base_url; ?>/CHM/departments.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="building" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Departments</span>
                        </a>
                        
                        <!-- Employee Acquisition -->
                        <a href="<?php echo $base_url; ?>/CHM/aquasition.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="user-plus" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Employee Acquisition</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Leave Management Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="leave-management">
                <button class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white dropdown-toggle">
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-blue-800/30 transition-colors">
                            <i data-lucide="calendar" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Leave Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-auto transition-transform duration-200 dropdown-arrow"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="py-2 space-y-1">
                        <!-- Leave Review -->
                        <a href="<?php echo $base_url; ?>/LEAVE/leave.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="clipboard-check" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Leave Review</span>
                        </a>
                        
                        <!-- Leave Overview -->
                        <a href="<?php echo $base_url; ?>/LEAVE/overview.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="calendar" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Leave Overview</span>
                        </a>
                        
                        <!-- Leave Analytics -->
                        <a href="<?php echo $base_url; ?>/LEAVE/analytics.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="trending-up" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Leave Analytics</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Compensation Planning Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="compensation-planning">
                <button class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white dropdown-toggle">
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-blue-800/30 transition-colors">
                            <i data-lucide="dollar-sign" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">Compensation</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-auto transition-transform duration-200 dropdown-arrow"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="py-2 space-y-1">
                        <!-- Core Compensation Management -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/core_compensation.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="dollar-sign" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Core Compensation</span>
                        </a>
                        
                        <!-- Strategic Planning & Analysis -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/strat_planning.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="trending-up" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Strategic Planning</span>
                        </a>
                        
                        <!-- Performance-Linked Compensation -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/performance_linked.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="award" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Performance Compensation</span>
                        </a>

                        <!-- Compliance & Administration -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/administration.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="shield-check" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Compliance & Admin</span>
                        </a>

                        <!-- Hospitality-Specific Modules -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/Hospitality.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="utensils" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Hospitality Modules</span>
                        </a>

                        <!-- Analytics & Reporting -->
                        <a href="<?php echo $base_url; ?>/COMPENSATION/analytics.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="bar-chart-3" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Analytics & Reporting</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- HR Analytics Dashboard -->
            <a href="<?php echo $base_url; ?>/ANALYTICS/main_analytics.php" class="block">
                <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
                    <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                        <i data-lucide="trending-up" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">HR Analytics Dashboard</span>
                </div>
            </a>

            <!-- HMO & Benefits Administration -->
            <a href="<?php echo $base_url; ?>/HCM/benefits_administration.php" class="block">
                <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
                    <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                        <i data-lucide="heart" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">HMO & Benefits</span>
                </div>
            </a>

            <!-- USER MANAGEMENT SECTION -->
            <div class="px-4 py-2 mt-4">
                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Administration</p>
            </div>

            <!-- User Management Dropdown -->
            <div class="relative menu-dropdown" data-dropdown="user-management">
                <button class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white dropdown-toggle">
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-blue-800/30 transition-colors">
                            <i data-lucide="user-cog" class="w-5 h-5 text-[#F7B32B]"></i>
                        </div>
                        <span class="ml-3 sidebar-text">User Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-auto transition-transform duration-200 dropdown-arrow"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content overflow-hidden transition-all duration-300 max-h-0">
                    <div class="py-2 space-y-1">
                        <a href="<?php echo $base_url; ?>/USM/department_accounts.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="users" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Department Accounts</span>
                        </a>
                       
                        <a href="<?php echo $base_url; ?>/USM/audit_trail&transaction.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="history" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Audit Trail & Transaction</span>
                        </a>

                        <a href="<?php echo $base_url; ?>/USM/role_management.php" class="flex items-center px-4 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white group/item ml-8">
                            <i data-lucide="shield" class="w-4 h-4 mr-3 text-[#F7B32B] group-hover/item:text-white"></i>
                            <span>Role Management</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <a href="<?php echo $base_url; ?>/settings/general.php" class="block">
                <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
                    <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                        <i data-lucide="settings" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">Settings</span>
                </div>
            </a>

            <!-- Account Section -->
            <div class="px-4 py-2 mt-4">
                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Account</p>
            </div>

            <!-- Profile -->
            <a href="<?php echo $base_url; ?>/profile.php" class="block">
                <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
                    <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                        <i data-lucide="user" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
                    </div>
                    <span class="ml-3 sidebar-text">Profile</span>
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

.dropdown-toggle:hover .dropdown-arrow {
    transform: rotate(180deg);
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