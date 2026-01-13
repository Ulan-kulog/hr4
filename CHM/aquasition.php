<?php
session_start();
include("../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die("❌ Connection not found for $db_name");
}
$conn = $connections[$db_name];

// Get filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$department_filter = $_GET['department'] ?? '';

// Build WHERE clause for filters
$where_clause = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where_clause .= " AND (job_position LIKE ? OR department LIKE ? OR required_skills LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($status_filter)) {
    $where_clause .= " AND status = ?";
    $params[] = $status_filter;
}

if (!empty($department_filter)) {
    $where_clause .= " AND department = ?";
    $params[] = $department_filter;
}

// Get unique departments for filter dropdown
$departments_result = $conn->query("SELECT DISTINCT department FROM job_positions ORDER BY department");
$departments = [];
while ($row = $departments_result->fetch_assoc()) {
    $departments[] = $row['department'];
}

// Get stats data
$stats = [];

// Open positions
$result = $conn->query("SELECT COUNT(*) as count FROM job_positions WHERE status = 'active'");
$stats['open_positions'] = $result->fetch_assoc()['count'];

// Total job positions
$result = $conn->query("SELECT COUNT(*) as count FROM job_positions");
$stats['total_positions'] = $result->fetch_assoc()['count'];

// Approved positions
$result = $conn->query("SELECT COUNT(*) as count FROM job_positions WHERE status = 'approved'");
$stats['approved_positions'] = $result->fetch_assoc()['count'];

// Pending positions
$result = $conn->query("SELECT COUNT(*) as count FROM job_positions WHERE status = 'under review'");
$stats['pending_positions'] = $result->fetch_assoc()['count'];

// Rejected positions
$result = $conn->query("SELECT COUNT(*) as count FROM job_positions WHERE status = 'rejected'");
$stats['rejected_positions'] = $result->fetch_assoc()['count'];

// For compliance positions
$result = $conn->query("SELECT COUNT(*) as count FROM job_positions WHERE status = 'for compliance'");
$stats['compliance_positions'] = $result->fetch_assoc()['count'];

// Departments with open positions
$result = $conn->query("SELECT department, COUNT(*) as count FROM job_positions WHERE status = 'active' GROUP BY department");
$stats['departments'] = [];
while ($row = $result->fetch_assoc()) {
    $stats['departments'][] = $row;
}

// Recruitment pipeline
$stats['pipeline'] = [
    'applied' => 0,
    'screening' => 45,
    'interview' => 28,
    'offer' => 9,
    'hired' => 12
];

// Calculate approval rate
$total_processed = $stats['approved_positions'] + $stats['rejected_positions'];
$stats['approval_rate'] = $total_processed > 0 ? round(($stats['approved_positions'] / $total_processed) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Acquisition - Core Human Capital</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sweet-alert-button {
            opacity: 1 !important;
            visibility: visible !important;
        }
        .swal2-popup {
            padding: 2rem !important;
        }
        .swal2-actions {
            margin: 1.5rem auto 0 !important;
        }
        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-base-100 min-h-screen bg-white">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <?php include '../INCLUDES/sidebar.php'; ?>

    <!-- Content Area -->
    <div class="flex flex-col flex-1 overflow-auto">
        <!-- Navbar -->
        <?php include '../INCLUDES/navbar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Employee Acquisition Section -->
            <div class="glass-effect p-6 rounded-2xl shadow-sm border border-gray-100/50 backdrop-blur-sm bg-white/70">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <span class="p-2 mr-3 rounded-lg bg-purple-100/50 text-purple-600">
                            <i data-lucide="briefcase" class="w-5 h-5"></i>
                        </span>
                        Employee Acquisition
                    </h2>
                    <div class="flex gap-2">
                        <button id="createJobPosition" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="file-plus" class="w-4 h-4 mr-2"></i>
                            Create Job Position
                        </button>
                    </div>
                </div>
                
               <!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-3 gap-4 mb-8">
    <!-- Total Positions -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Total Positions</p>
                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['total_positions']; ?></h3>
                <p class="text-xs text-gray-500 mt-1">All Job Positions</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="briefcase" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- Open Positions -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Open Positions</p>
                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['open_positions']; ?></h3>
                <p class="text-xs text-gray-500 mt-1">Active Positions</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="file-text" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- Approved Positions -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Approved</p>
                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['approved_positions']; ?></h3>
                <p class="text-xs text-gray-500 mt-1">Approved Positions</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="check-circle" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- Under Review -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Under Review</p>
                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['pending_positions']; ?></h3>
                <p class="text-xs text-gray-500 mt-1">Pending Review</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="clock" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- For Compliance -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">For Compliance</p>
                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['compliance_positions']; ?></h3>
                <p class="text-xs text-gray-500 mt-1">Compliance Review</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="file-check" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- Rejected -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Rejected</p>
                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['rejected_positions']; ?></h3>
                <p class="text-xs text-gray-500 mt-1">Rejected Positions</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="x-circle" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>

    <!-- Approval Rate -->
    <div class="stat-card bg-white text-black shadow-2xl p-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Approval Rate</p>
                <h3 class="text-2xl font-bold mt-1"><?php echo $stats['approval_rate']; ?>%</h3>
                <p class="text-xs text-gray-500 mt-1">Success Rate</p>
            </div>
            <div class="p-2 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                <i data-lucide="trending-up" class="w-5 h-5 text-[#F7B32B]"></i>
            </div>
        </div>
    </div>
</div>

                <!-- Search and Filter Section -->
                <div class="mb-6 p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                    <form id="searchFilterForm" method="GET" class="flex flex-col sm:flex-row gap-4">
                        <!-- Search Bar -->
                        <div class="flex-1">
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" 
                                       placeholder="Search by job position, department, or skills...">
                                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="sm:w-48">
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">All Status</option>
                                <option value="under review" <?php echo $status_filter === 'under review' ? 'selected' : ''; ?>>Under Review</option>
                                <option value="for compliance" <?php echo $status_filter === 'for compliance' ? 'selected' : ''; ?>>For Compliance</option>
                                <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                            </select>
                        </div>

                        <!-- Department Filter -->
                        <div class="sm:w-48">
                            <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo htmlspecialchars($dept); ?>" 
                                            <?php echo $department_filter === $dept ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                                Apply
                            </button>
                            <button type="button" onclick="clearFilters()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center gap-2">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                Clear
                            </button>
                        </div>
                    </form>

                    <!-- Active Filters Display -->
                    <?php if (!empty($search) || !empty($status_filter) || !empty($department_filter)): ?>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        <?php if (!empty($search)): ?>
                            <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                Search: "<?php echo htmlspecialchars($search); ?>"
                                <button onclick="removeFilter('search')" class="ml-1 text-blue-600 hover:text-blue-800">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($status_filter)): ?>
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                Status: <?php echo ucfirst($status_filter); ?>
                                <button onclick="removeFilter('status')" class="ml-1 text-green-600 hover:text-green-800">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($department_filter)): ?>
                            <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                                Department: <?php echo htmlspecialchars($department_filter); ?>
                                <button onclick="removeFilter('department')" class="ml-1 text-purple-600 hover:text-purple-800">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Job Positions Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                        <i data-lucide="briefcase" class="w-5 h-5 mr-2"></i>
                        Job Positions
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="jobPositionsContainer">
                        <!-- Job positions will be loaded dynamically -->
                    </div>
                </div>

                <!-- Departments Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Departments with Open Positions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="departmentsContainer">
                        <!-- Departments will be dynamically populated -->
                    </div>
                </div>

                <!-- Recruitment Pipeline -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Recruitment Pipeline</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4" id="pipelineContainer">
                        <!-- Pipeline will be loaded dynamically -->
                    </div>
                </div>
            </div>
        </main>
    </div>

   <!-- Create Job Position Modal -->
<div id="jobPositionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i data-lucide="file-plus" class="w-5 h-5 mr-2"></i>
                Create Job Position
            </h3>
            <button id="closeJobModal" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="jobPositionForm" class="space-y-4">
            <!-- Job Details Section -->
            <div class="border-b border-gray-200 pb-4">
                <h4 class="text-md font-medium text-gray-800 mb-3 flex items-center">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                    Job Details
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                            Job Position
                        </label>
                        <input type="text" name="job_position" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="Enter job position" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i data-lucide="building" class="w-4 h-4 mr-1"></i>
                            Department
                        </label>
                        <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
                            <option value="">Select Department</option>
                            <option value="hotel">Hotel Department</option>
                            <option value="restaurant">Restaurant Department</option>
                            <option value="hr">HR Department</option>
                            <option value="logistic">Logistic Department</option>
                            <option value="administrative">Administrative Department</option>
                            <option value="financial">Financials Department</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i data-lucide="file-text" class="w-4 h-4 mr-1"></i>
                        Job Description
                    </label>
                    <textarea name="job_description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-32 bg-white" placeholder="Enter job description" required></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                            Number of Openings
                        </label>
                        <input type="number" name="number_of_openings" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="0" min="1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i data-lucide="briefcase" class="w-4 h-4 mr-1"></i>
                            Employment Type
                        </label>
                        <select name="employment_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
                            <option value="full-time">Full-time</option>
                            <option value="part-time">Part-time</option>
                            <option value="contract">Contract</option>
                            <option value="temporary">Temporary</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i data-lucide="dollar-sign" class="w-4 h-4 mr-1"></i>
                            Salary Range
                        </label>
                        <select name="salary_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
                            <option value="">Select Salary Range</option>
                            <option value="₱10,000 - ₱15,000">₱10,000 - ₱15,000</option>
                            <option value="₱15,000 - ₱20,000">₱15,000 - ₱20,000</option>
                            <option value="₱20,000 - ₱25,000">₱20,000 - ₱25,000</option>
                            <option value="₱25,000 - ₱30,000">₱25,000 - ₱30,000</option>
                            <option value="₱30,000 - ₱35,000">₱30,000 - ₱35,000</option>
                            <option value="₱35,000 - ₱40,000">₱35,000 - ₱40,000</option>
                            <option value="₱40,000 - ₱45,000">₱40,000 - ₱45,000</option>
                            <option value="₱45,000 - ₱50,000">₱45,000 - ₱50,000</option>
                            <option value="₱50,000 - ₱60,000">₱50,000 - ₱60,000</option>
                            <option value="₱60,000 - ₱70,000">₱60,000 - ₱70,000</option>
                            <option value="₱70,000 - ₱80,000">₱70,000 - ₱80,000</option>
                            <option value="₱80,000 - ₱90,000">₱80,000 - ₱90,000</option>
                            <option value="₱90,000 - ₱100,000">₱90,000 - ₱100,000</option>
                            <option value="₱100,000 - ₱120,000">₱100,000 - ₱120,000</option>
                            <option value="₱120,000 - ₱150,000">₱120,000 - ₱150,000</option>
                            <option value="₱150,000 - ₱200,000">₱150,000 - ₱200,000</option>
                            <option value="₱200,000 - ₱250,000">₱200,000 - ₱250,000</option>
                            <option value="₱250,000 - ₱300,000">₱250,000 - ₱300,000</option>
                            <option value="₱300,000 - ₱400,000">₱300,000 - ₱400,000</option>
                            <option value="₱400,000 - ₱500,000">₱400,000 - ₱500,000</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i data-lucide="calendar-plus" class="w-4 h-4 mr-1"></i>
                                Start Date
                            </label>
                            <div class="relative">
                                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white date-picker" required>
                                <i data-lucide="calendar" class="w-4 h-4 absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i data-lucide="calendar-minus" class="w-4 h-4 mr-1"></i>
                                End Date
                            </label>
                            <div class="relative">
                                <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white date-picker" required>
                                <i data-lucide="calendar" class="w-4 h-4 absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Qualifications Section -->
            <div class="border-b border-gray-200 pb-4">
                <h4 class="text-md font-medium text-gray-800 mb-3 flex items-center">
                    <i data-lucide="clipboard-check" class="w-4 h-4 mr-2"></i>
                    Qualifications & Requirements
                </h4>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i data-lucide="graduation-cap" class="w-4 h-4 mr-1"></i>
                        Required Education
                    </label>
                    <select name="required_education" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
                        <option value="">Select Education Level</option>
                        <option value="High School Diploma">High School Diploma</option>
                        <option value="Associate Degree">Associate Degree</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="Doctorate">Doctorate</option>
                        <option value="No Formal Education Required">No Formal Education Required</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i data-lucide="award" class="w-4 h-4 mr-1"></i>
                        Required Experience
                    </label>
                    <select name="required_experience" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
                        <option value="">Select Experience Level</option>
                        <option value="No experience">No experience</option>
                        <option value="1-2 years">1-2 years</option>
                        <option value="3-5 years">3-5 years</option>
                        <option value="5+ years">5+ years</option>
                        <option value="10+ years">10+ years</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i data-lucide="zap" class="w-4 h-4 mr-1"></i>
                        Required Skills
                    </label>
                    <textarea name="required_skills" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-24 bg-white" placeholder="List required skills (one per line)" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i data-lucide="star" class="w-4 h-4 mr-1"></i>
                        Preferred Qualifications
                    </label>
                    <textarea name="preferred_qualifications" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-20 bg-white" placeholder="List preferred qualifications"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i data-lucide="file-check" class="w-4 h-4 mr-1"></i>
                        Certifications
                    </label>
                    <select name="certifications" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select Certification (if any)</option>
                        <option value="None">None</option>
                        <option value="Food Safety Certificate">Food Safety Certificate</option>
                        <option value="CPR/First Aid Certified">CPR/First Aid Certified</option>
                        <option value="ServSafe Certification">ServSafe Certification</option>
                        <option value="TIPS Certification">TIPS Certification</option>
                        <option value="Hotel Management Certificate">Hotel Management Certificate</option>
                        <option value="Microsoft Office Specialist">Microsoft Office Specialist</option>
                        <option value="Project Management Professional">Project Management Professional</option>
                        <option value="Human Resources Certification">Human Resources Certification</option>
                        <option value="Accounting Certification">Accounting Certification</option>
                        <option value="IT Certification">IT Certification</option>
                        <option value="Language Proficiency Certificate">Language Proficiency Certificate</option>
                        <option value="Other">Other (specify in preferred qualifications)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancelJobPosition" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Create Position
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Modal Elements
    const jobPositionModal = document.getElementById('jobPositionModal');
    const createJobPositionBtn = document.getElementById('createJobPosition');
    const closeJobModal = document.getElementById('closeJobModal');
    const cancelJobPosition = document.getElementById('cancelJobPosition');
    const jobPositionForm = document.getElementById('jobPositionForm');

    // Set minimum dates for date inputs
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.min = today;
    });

    // Enhanced date picker functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add date validation for end date
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');

        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                if (this.value) {
                    endDateInput.min = this.value;
                    
                    // If end date is before start date, clear it
                    if (endDateInput.value && endDateInput.value < this.value) {
                        endDateInput.value = '';
                    }
                }
            });
        }

        // Enhanced date picker styling
        document.querySelectorAll('.date-picker').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-blue-500', 'rounded-lg');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-blue-500', 'rounded-lg');
            });
        });
    });

    // Open modal
    createJobPositionBtn.addEventListener('click', () => {
        jobPositionModal.classList.remove('hidden');
        // Refresh icons when modal opens
        lucide.createIcons();
    });

    // Close modals
    closeJobModal.addEventListener('click', () => jobPositionModal.classList.add('hidden'));
    cancelJobPosition.addEventListener('click', () => jobPositionModal.classList.add('hidden'));

    // Close modal when clicking outside
    jobPositionModal.addEventListener('click', (e) => {
        if (e.target === jobPositionModal) jobPositionModal.classList.add('hidden');
    });

    // Form submission with SweetAlert
    jobPositionForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = jobPositionForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i> Creating...';
        submitBtn.disabled = true;
        
        // Refresh loader icon
        lucide.createIcons();

        const formData = new FormData(jobPositionForm);
        const data = Object.fromEntries(formData);

        // Validate dates
        const startDate = new Date(data.start_date);
        const endDate = new Date(data.end_date);
        
        if (endDate <= startDate) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Dates',
                text: 'End date must be after start date',
            });
            
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            lucide.createIcons();
            return;
        }

        try {
            const response = await fetch('API/create_job_position.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            // Get response as text first to handle potential HTML errors
            const responseText = await response.text();
            
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                console.error('Failed to parse JSON:', responseText);
                throw new Error(`Server returned invalid JSON. Response: ${responseText.substring(0, 100)}...`);
            }

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: result.message,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors'
                    }
                }).then(() => {
                    // Close modal and reset form
                    jobPositionModal.classList.add('hidden');
                    jobPositionForm.reset();
                    
                    // Reload the page to show updated stats
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: result.message,
                    confirmButtonText: 'Try Again',
                    customClass: {
                        confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors'
                    }
                });
            }
        } catch (error) {
            console.error('Network error details:', error);
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Failed to process your request. Please try again later.',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors'
                }
            });
        } finally {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            lucide.createIcons();
        }
    });

    // Filter functions
    function clearFilters() {
        window.location.href = window.location.pathname;
    }

    function removeFilter(filterName) {
        const url = new URL(window.location);
        url.searchParams.delete(filterName);
        window.location.href = url.toString();
    }
</script>
</body>
</html>