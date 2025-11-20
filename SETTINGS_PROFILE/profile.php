<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .profile-card {
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .skill-bar {
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .skill-progress {
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            height: 8px;
            border-radius: 10px;
            transition: width 0.8s ease-in-out;
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
            <main class="flex-1 p-4 md:p-6 overflow-auto">
                <!-- Profile Header -->
                <div class="mx-auto max-w-7xl">
                    <!-- Profile Header Card -->
                    <div class="bg-white/70 shadow-sm backdrop-blur-sm mb-6 p-6 border border-gray-100/50 rounded-2xl glass-effect">
                        <div class="flex md:flex-row flex-col items-center md:items-start gap-6">
                            <!-- Profile Picture -->
                            <div class="relative">
                                <div class="flex justify-center items-center bg-gradient-to-br from-blue-500 to-purple-600 rounded-full w-24 md:w-32 h-24 md:h-32 font-bold text-white text-2xl">
                                    JS
                                </div>
                                <div class="right-2 bottom-2 absolute bg-green-500 border-2 border-white rounded-full w-6 h-6"></div>
                            </div>

                            <!-- Profile Info -->
                            <div class="flex-1 md:text-left text-center">
                                <div class="flex md:flex-row flex-col md:justify-between md:items-center mb-4">
                                    <div>
                                        <h1 class="font-bold text-gray-800 text-2xl md:text-3xl">John Smith</h1>
                                        <p class="text-gray-600 text-lg">Senior Software Developer</p>
                                        <p class="mt-1 text-gray-500">Engineering Department</p>
                                    </div>
                                    <button class="mt-4 md:mt-0 btn-outline btn btn-primary">
                                        <i data-lucide="edit-3" class="mr-2 w-4 h-4"></i>
                                        Edit Profile
                                    </button>
                                </div>

                                <!-- Stats -->
                                <div class="gap-4 grid grid-cols-2 md:grid-cols-4 mt-6">
                                    <div class="text-center">
                                        <div class="font-bold text-gray-800 text-2xl">2.5y</div>
                                        <div class="text-gray-500 text-sm">Experience</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-bold text-gray-800 text-2xl">24</div>
                                        <div class="text-gray-500 text-sm">Projects</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-bold text-gray-800 text-2xl">98%</div>
                                        <div class="text-gray-500 text-sm">Attendance</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-bold text-gray-800 text-2xl">A+</div>
                                        <div class="text-gray-500 text-sm">Performance</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="gap-6 grid grid-cols-1 lg:grid-cols-3">
                        <!-- Left Column -->
                        <div class="space-y-6 lg:col-span-2">
                            <!-- About Section -->
                            <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect profile-card">
                                <h2 class="flex items-center mb-4 font-bold text-gray-800 text-xl">
                                    <i data-lucide="user" class="mr-2 w-5 h-5 text-blue-500"></i>
                                    About
                                </h2>
                                <p class="text-gray-600 leading-relaxed">
                                    Passionate software developer with expertise in modern web technologies.
                                    Specialized in creating scalable applications with clean code architecture.
                                    Always eager to learn new technologies and contribute to innovative projects.
                                </p>

                                <div class="gap-4 grid grid-cols-1 md:grid-cols-2 mt-4">
                                    <div class="flex items-center text-gray-600">
                                        <i data-lucide="mail" class="mr-3 w-4 h-4 text-blue-500"></i>
                                        <span>john.smith@company.com</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i data-lucide="phone" class="mr-3 w-4 h-4 text-green-500"></i>
                                        <span>+1 (555) 123-4567</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i data-lucide="map-pin" class="mr-3 w-4 h-4 text-red-500"></i>
                                        <span>New York, NY</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i data-lucide="calendar" class="mr-3 w-4 h-4 text-purple-500"></i>
                                        <span>Joined March 2022</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Skills Section -->
                            <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect profile-card">
                                <h2 class="flex items-center mb-4 font-bold text-gray-800 text-xl">
                                    <i data-lucide="award" class="mr-2 w-5 h-5 text-yellow-500"></i>
                                    Skills & Expertise
                                </h2>
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-gray-700">JavaScript/TypeScript</span>
                                            <span class="text-gray-500">95%</span>
                                        </div>
                                        <div class="skill-bar">
                                            <div class="skill-progress" style="width: 95%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-gray-700">React & Vue.js</span>
                                            <span class="text-gray-500">90%</span>
                                        </div>
                                        <div class="skill-bar">
                                            <div class="skill-progress" style="width: 90%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-gray-700">Node.js</span>
                                            <span class="text-gray-500">88%</span>
                                        </div>
                                        <div class="skill-bar">
                                            <div class="skill-progress" style="width: 88%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-gray-700">Database Design</span>
                                            <span class="text-gray-500">85%</span>
                                        </div>
                                        <div class="skill-bar">
                                            <div class="skill-progress" style="width: 85%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect profile-card">
                                <h2 class="flex items-center mb-4 font-bold text-gray-800 text-xl">
                                    <i data-lucide="activity" class="mr-2 w-5 h-5 text-green-500"></i>
                                    Recent Activity
                                </h2>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex flex-shrink-0 justify-center items-center bg-blue-100 rounded-full w-8 h-8">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-blue-500"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Completed Project Alpha</p>
                                            <p class="text-gray-500 text-sm">2 hours ago</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="flex flex-shrink-0 justify-center items-center bg-green-100 rounded-full w-8 h-8">
                                            <i data-lucide="message-circle" class="w-4 h-4 text-green-500"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Team meeting with design department</p>
                                            <p class="text-gray-500 text-sm">5 hours ago</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="flex flex-shrink-0 justify-center items-center bg-purple-100 rounded-full w-8 h-8">
                                            <i data-lucide="file-text" class="w-4 h-4 text-purple-500"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Submitted quarterly report</p>
                                            <p class="text-gray-500 text-sm">1 day ago</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Quick Stats -->
                            <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect profile-card">
                                <h2 class="mb-4 font-bold text-gray-800 text-xl">Quick Stats</h2>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Tasks Completed</span>
                                        <span class="font-bold text-gray-800">42</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Team Members</span>
                                        <span class="font-bold text-gray-800">8</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Hours This Week</span>
                                        <span class="font-bold text-gray-800">38.5</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Projects Active</span>
                                        <span class="font-bold text-gray-800">3</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Members -->
                            <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect profile-card">
                                <h2 class="mb-4 font-bold text-gray-800 text-xl">Team Members</h2>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-gradient-to-br from-green-400 to-blue-500 rounded-full w-10 h-10"></div>
                                        <div>
                                            <p class="font-medium text-gray-800">Sarah Johnson</p>
                                            <p class="text-gray-500 text-sm">UI/UX Designer</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="bg-gradient-to-br from-purple-400 to-pink-500 rounded-full w-10 h-10"></div>
                                        <div>
                                            <p class="font-medium text-gray-800">Mike Chen</p>
                                            <p class="text-gray-500 text-sm">Backend Developer</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full w-10 h-10"></div>
                                        <div>
                                            <p class="font-medium text-gray-800">Emily Davis</p>
                                            <p class="text-gray-500 text-sm">Project Manager</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Upcoming Events -->
                            <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect profile-card">
                                <h2 class="mb-4 font-bold text-gray-800 text-xl">Upcoming Events</h2>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3 bg-blue-50 p-3 rounded-lg">
                                        <div class="flex flex-col justify-center items-center bg-blue-500 rounded-lg w-12 h-12 text-white">
                                            <span class="font-bold text-sm">15</span>
                                            <span class="text-xs">NOV</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Team Standup</p>
                                            <p class="text-gray-500 text-sm">9:00 AM</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 bg-green-50 p-3 rounded-lg">
                                        <div class="flex flex-col justify-center items-center bg-green-500 rounded-lg w-12 h-12 text-white">
                                            <span class="font-bold text-sm">16</span>
                                            <span class="text-xs">NOV</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Client Presentation</p>
                                            <p class="text-gray-500 text-sm">2:00 PM</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Simple animation for skill bars
        document.addEventListener('DOMContentLoaded', function() {
            const skillBars = document.querySelectorAll('.skill-progress');
            skillBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        });
    </script>
    <script src="../JAVASCRIPT/sidebar.js"></script>
</body>

</html>