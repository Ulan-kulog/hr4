<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Acquisition - Core Human Capital</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        <button id="requestJobPosting" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="file-plus" class="w-4 h-4 mr-2"></i>
                            Request Job Posting
                        </button>
                        <button id="createJobQualifications" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i data-lucide="clipboard-check" class="w-4 h-4 mr-2"></i>
                            Create Job Qualifications
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Open Positions -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Open Positions</p>
                                <h3 class="text-3xl font-bold mt-1">23</h3>
                                <p class="text-xs text-gray-500 mt-1">Active job posts</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="briefcase" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- New Applications -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">New Applications</p>
                                <h3 class="text-3xl font-bold mt-1">156</h3>
                                <p class="text-xs text-gray-500 mt-1">This week</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="inbox" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Interviews -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Interviews</p>
                                <h3 class="text-3xl font-bold mt-1">28</h3>
                                <p class="text-xs text-gray-500 mt-1">Scheduled</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="calendar" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Offer Stage -->
                    <div class="stat-card bg-white text-black shadow-2xl p-5 rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-[#001f54] hover:drop-shadow-md transition-all">Offer Stage</p>
                                <h3 class="text-3xl font-bold mt-1">9</h3>
                                <p class="text-xs text-gray-500 mt-1">Pending offers</p>
                            </div>
                            <div class="p-3 rounded-lg bg-[#001f54] flex items-center justify-center transition-all duration-300 hover:bg-[#002b70]">
                                <i data-lucide="file-check" class="w-6 h-6 text-[#F7B32B]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Departments Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Departments with Open Positions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Hotel Department -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Hotel Department</h4>
                                <span class="p-2 rounded-lg bg-blue-100 text-blue-600">
                                    <i data-lucide="home" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Open Positions: <span class="font-semibold">8</span></p>
                            <p class="text-xs text-gray-500">Front Desk, Housekeeping, Concierge</p>
                        </div>

                        <!-- Restaurant Department -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Restaurant Department</h4>
                                <span class="p-2 rounded-lg bg-red-100 text-red-600">
                                    <i data-lucide="utensils" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Open Positions: <span class="font-semibold">6</span></p>
                            <p class="text-xs text-gray-500">Chefs, Servers, Bartenders</p>
                        </div>

                        <!-- HR Department -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">HR Department</h4>
                                <span class="p-2 rounded-lg bg-pink-100 text-pink-600">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Open Positions: <span class="font-semibold">2</span></p>
                            <p class="text-xs text-gray-500">HR Assistant, Recruiter</p>
                        </div>

                        <!-- Logistic Department -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Logistic Department</h4>
                                <span class="p-2 rounded-lg bg-orange-100 text-orange-600">
                                    <i data-lucide="truck" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Open Positions: <span class="font-semibold">4</span></p>
                            <p class="text-xs text-gray-500">Drivers, Warehouse Staff</p>
                        </div>

                        <!-- Administrative Department -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Administrative Department</h4>
                                <span class="p-2 rounded-lg bg-purple-100 text-purple-600">
                                    <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Open Positions: <span class="font-semibold">1</span></p>
                            <p class="text-xs text-gray-500">Administrative Assistant</p>
                        </div>

                        <!-- Financials Department -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Financials Department</h4>
                                <span class="p-2 rounded-lg bg-green-100 text-green-600">
                                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Open Positions: <span class="font-semibold">2</span></p>
                            <p class="text-xs text-gray-500">Accountant, Financial Analyst</p>
                        </div>
                    </div>
                </div>

                <!-- Recruitment Pipeline -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Recruitment Pipeline</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 mb-1">156</div>
                            <div class="text-sm text-gray-600">Applied</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600 mb-1">45</div>
                            <div class="text-sm text-gray-600">Screening</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 mb-1">28</div>
                            <div class="text-sm text-gray-600">Interview</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 mb-1">9</div>
                            <div class="text-sm text-gray-600">Offer</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-gray-600 mb-1">12</div>
                            <div class="text-sm text-gray-600">Hired</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Request Job Posting Modal -->
    <div id="jobPostingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Request Job Posting</h3>
                <button id="closeJobModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter job title">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Description</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-32" placeholder="Enter job description"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Openings</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="full-time">Full-time</option>
                            <option value="part-time">Part-time</option>
                            <option value="contract">Contract</option>
                            <option value="temporary">Temporary</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salary Range</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., ₱20,000 - ₱30,000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Application Deadline</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelJobPosting" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Job Qualifications Modal -->
    <div id="qualificationsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Create Job Qualifications</h3>
                <button id="closeQualModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Position</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter job position">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Required Education</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Bachelor's Degree in related field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Required Experience</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 2+ years in similar role">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Required Skills</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-24" placeholder="List required skills (one per line)"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Qualifications</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-20" placeholder="List preferred qualifications"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Certifications (if any)</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Food Safety Certificate, etc.">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelQualifications" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Save Qualifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Modal Elements
        const jobPostingModal = document.getElementById('jobPostingModal');
        const qualificationsModal = document.getElementById('qualificationsModal');
        const requestJobPostingBtn = document.getElementById('requestJobPosting');
        const createJobQualificationsBtn = document.getElementById('createJobQualifications');
        
        // Close buttons
        const closeJobModal = document.getElementById('closeJobModal');
        const closeQualModal = document.getElementById('closeQualModal');
        const cancelJobPosting = document.getElementById('cancelJobPosting');
        const cancelQualifications = document.getElementById('cancelQualifications');

        // Open modals
        requestJobPostingBtn.addEventListener('click', () => {
            jobPostingModal.classList.remove('hidden');
        });

        createJobQualificationsBtn.addEventListener('click', () => {
            qualificationsModal.classList.remove('hidden');
        });

        // Close modals
        closeJobModal.addEventListener('click', () => jobPostingModal.classList.add('hidden'));
        closeQualModal.addEventListener('click', () => qualificationsModal.classList.add('hidden'));
        cancelJobPosting.addEventListener('click', () => jobPostingModal.classList.add('hidden'));
        cancelQualifications.addEventListener('click', () => qualificationsModal.classList.add('hidden'));

        // Close modals when clicking outside
        jobPostingModal.addEventListener('click', (e) => {
            if (e.target === jobPostingModal) jobPostingModal.classList.add('hidden');
        });

        qualificationsModal.addEventListener('click', (e) => {
            if (e.target === qualificationsModal) qualificationsModal.classList.add('hidden');
        });

        // Form submissions (prevent default for demo)
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('Form submitted successfully!');
                jobPostingModal.classList.add('hidden');
                qualificationsModal.classList.add('hidden');
            });
        });
    </script>
</body>
</html>