<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .settings-card {
            transition: all 0.3s ease;
        }

        .settings-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .toggle-checkbox:checked {
            right: 0;
            border-color: #3b82f6;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #3b82f6;
        }

        .tab-active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .setting-group {
            border-left: 3px solid #3b82f6;
            padding-left: 1rem;
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
                <div class="mx-auto max-w-6xl">
                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="mb-2 font-bold text-gray-800 text-3xl">Settings</h1>
                        <p class="text-gray-600">Manage your account settings and preferences</p>
                    </div>

                    <div class="gap-6 grid grid-cols-1 lg:grid-cols-4">
                        <!-- Settings Sidebar -->
                        <div class="lg:col-span-1">
                            <div class="top-6 sticky bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl glass-effect">
                                <nav class="space-y-2">
                                    <button class="hover:bg-blue-50 px-4 py-3 rounded-lg w-full font-medium text-gray-700 text-left transition-colors tab-btn tab-active" data-tab="account">
                                        <i data-lucide="user" class="inline mr-3 w-4 h-4"></i>
                                        Account
                                    </button>
                                    <button class="hover:bg-blue-50 px-4 py-3 rounded-lg w-full font-medium text-gray-700 text-left transition-colors tab-btn" data-tab="notifications">
                                        <i data-lucide="bell" class="inline mr-3 w-4 h-4"></i>
                                        Notifications
                                    </button>
                                    <button class="hover:bg-blue-50 px-4 py-3 rounded-lg w-full font-medium text-gray-700 text-left transition-colors tab-btn" data-tab="security">
                                        <i data-lucide="shield" class="inline mr-3 w-4 h-4"></i>
                                        Security
                                    </button>
                                    <button class="hover:bg-blue-50 px-4 py-3 rounded-lg w-full font-medium text-gray-700 text-left transition-colors tab-btn" data-tab="privacy">
                                        <i data-lucide="lock" class="inline mr-3 w-4 h-4"></i>
                                        Privacy
                                    </button>
                                    <button class="hover:bg-blue-50 px-4 py-3 rounded-lg w-full font-medium text-gray-700 text-left transition-colors tab-btn" data-tab="appearance">
                                        <i data-lucide="palette" class="inline mr-3 w-4 h-4"></i>
                                        Appearance
                                    </button>
                                    <button class="hover:bg-blue-50 px-4 py-3 rounded-lg w-full font-medium text-gray-700 text-left transition-colors tab-btn" data-tab="integrations">
                                        <i data-lucide="plug" class="inline mr-3 w-4 h-4"></i>
                                        Integrations
                                    </button>
                                </nav>
                            </div>
                        </div>

                        <!-- Settings Content -->
                        <div class="space-y-6 lg:col-span-3">
                            <!-- Account Settings Tab -->
                            <div class="bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl tab-content glass-effect settings-card" id="account-tab">
                                <h2 class="flex items-center mb-6 font-bold text-gray-800 text-2xl">
                                    <i data-lucide="user" class="mr-3 w-6 h-6 text-blue-500"></i>
                                    Account Settings
                                </h2>

                                <!-- Profile Information -->
                                <div class="setting-group mb-8">
                                    <h3 class="mb-4 font-semibold text-gray-800 text-lg">Profile Information</h3>
                                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="text-gray-700 label-text">First Name</span>
                                            </label>
                                            <input type="text" class="w-full input input-bordered" value="John">
                                        </div>
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="text-gray-700 label-text">Last Name</span>
                                            </label>
                                            <input type="text" class="w-full input input-bordered" value="Smith">
                                        </div>
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="text-gray-700 label-text">Email</span>
                                            </label>
                                            <input type="email" class="w-full input input-bordered" value="john.smith@company.com">
                                        </div>
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="text-gray-700 label-text">Phone</span>
                                            </label>
                                            <input type="tel" class="w-full input input-bordered" value="+1 (555) 123-4567">
                                        </div>
                                    </div>
                                </div>

                                <!-- Department Information -->
                                <div class="setting-group mb-8">
                                    <h3 class="mb-4 font-semibold text-gray-800 text-lg">Department Information</h3>
                                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="text-gray-700 label-text">Position</span>
                                            </label>
                                            <input type="text" class="w-full input input-bordered" value="Senior Software Developer">
                                        </div>
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="text-gray-700 label-text">Department</span>
                                            </label>
                                            <select class="w-full select-bordered select">
                                                <option selected>Engineering</option>
                                                <option>Design</option>
                                                <option>Marketing</option>
                                                <option>Sales</option>
                                                <option>HR</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Save Button -->
                                <div class="flex justify-end">
                                    <button class="btn btn-primary">
                                        <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                                        Save Changes
                                    </button>
                                </div>
                            </div>

                            <!-- Notifications Settings Tab -->
                            <div class="hidden bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl tab-content glass-effect settings-card" id="notifications-tab">
                                <h2 class="flex items-center mb-6 font-bold text-gray-800 text-2xl">
                                    <i data-lucide="bell" class="mr-3 w-6 h-6 text-green-500"></i>
                                    Notification Preferences
                                </h2>

                                <div class="space-y-6">
                                    <!-- Email Notifications -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Email Notifications</h3>
                                        <div class="space-y-4">
                                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                                <div>
                                                    <p class="font-medium text-gray-800">Project Updates</p>
                                                    <p class="text-gray-600 text-sm">Get notified about project changes and deadlines</p>
                                                </div>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only toggle-checkbox" checked>
                                                    <div class="relative bg-gray-200 rounded-full w-12 h-6 transition-colors toggle-label">
                                                        <div class="top-1 left-1 absolute bg-white rounded-full w-4 h-4 transition-transform"></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                                <div>
                                                    <p class="font-medium text-gray-800">Team Messages</p>
                                                    <p class="text-gray-600 text-sm">Receive notifications for team communications</p>
                                                </div>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only toggle-checkbox" checked>
                                                    <div class="relative bg-gray-200 rounded-full w-12 h-6 transition-colors toggle-label">
                                                        <div class="top-1 left-1 absolute bg-white rounded-full w-4 h-4 transition-transform"></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                                <div>
                                                    <p class="font-medium text-gray-800">System Alerts</p>
                                                    <p class="text-gray-600 text-sm">Important system notifications and maintenance</p>
                                                </div>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only toggle-checkbox">
                                                    <div class="relative bg-gray-200 rounded-full w-12 h-6 transition-colors toggle-label">
                                                        <div class="top-1 left-1 absolute bg-white rounded-full w-4 h-4 transition-transform"></div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Push Notifications -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Push Notifications</h3>
                                        <div class="space-y-4">
                                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                                <div>
                                                    <p class="font-medium text-gray-800">Desktop Notifications</p>
                                                    <p class="text-gray-600 text-sm">Show notifications on your desktop</p>
                                                </div>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only toggle-checkbox" checked>
                                                    <div class="relative bg-gray-200 rounded-full w-12 h-6 transition-colors toggle-label">
                                                        <div class="top-1 left-1 absolute bg-white rounded-full w-4 h-4 transition-transform"></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                                <div>
                                                    <p class="font-medium text-gray-800">Mobile Push</p>
                                                    <p class="text-gray-600 text-sm">Send push notifications to your mobile device</p>
                                                </div>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only toggle-checkbox">
                                                    <div class="relative bg-gray-200 rounded-full w-12 h-6 transition-colors toggle-label">
                                                        <div class="top-1 left-1 absolute bg-white rounded-full w-4 h-4 transition-transform"></div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Settings Tab -->
                            <div class="hidden bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl tab-content glass-effect settings-card" id="security-tab">
                                <h2 class="flex items-center mb-6 font-bold text-gray-800 text-2xl">
                                    <i data-lucide="shield" class="mr-3 w-6 h-6 text-red-500"></i>
                                    Security Settings
                                </h2>

                                <div class="space-y-6">
                                    <!-- Password Change -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Change Password</h3>
                                        <div class="space-y-4">
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="text-gray-700 label-text">Current Password</span>
                                                </label>
                                                <input type="password" class="w-full input input-bordered" placeholder="Enter current password">
                                            </div>
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="text-gray-700 label-text">New Password</span>
                                                </label>
                                                <input type="password" class="w-full input input-bordered" placeholder="Enter new password">
                                            </div>
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="text-gray-700 label-text">Confirm New Password</span>
                                                </label>
                                                <input type="password" class="w-full input input-bordered" placeholder="Confirm new password">
                                            </div>
                                            <button class="btn btn-primary">
                                                <i data-lucide="key" class="mr-2 w-4 h-4"></i>
                                                Update Password
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Two-Factor Authentication -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Two-Factor Authentication</h3>
                                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-800">Enable 2FA</p>
                                                <p class="text-gray-600 text-sm">Add an extra layer of security to your account</p>
                                            </div>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only toggle-checkbox">
                                                <div class="relative bg-gray-200 rounded-full w-12 h-6 transition-colors toggle-label">
                                                    <div class="top-1 left-1 absolute bg-white rounded-full w-4 h-4 transition-transform"></div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Session Management -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Active Sessions</h3>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <i data-lucide="monitor" class="w-5 h-5 text-blue-500"></i>
                                                    <div>
                                                        <p class="font-medium text-gray-800">Chrome on Windows</p>
                                                        <p class="text-gray-600 text-sm">New York, USA • Current session</p>
                                                    </div>
                                                </div>
                                                <span class="font-medium text-green-500 text-sm">Active</span>
                                            </div>
                                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <i data-lucide="smartphone" class="w-5 h-5 text-green-500"></i>
                                                    <div>
                                                        <p class="font-medium text-gray-800">Safari on iPhone</p>
                                                        <p class="text-gray-600 text-sm">Los Angeles, USA • 2 days ago</p>
                                                    </div>
                                                </div>
                                                <button class="font-medium text-red-500 text-sm">Revoke</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Privacy Settings Tab -->
                            <div class="hidden bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl tab-content glass-effect settings-card" id="privacy-tab">
                                <h2 class="flex items-center mb-6 font-bold text-gray-800 text-2xl">
                                    <i data-lucide="lock" class="mr-3 w-6 h-6 text-purple-500"></i>
                                    Privacy Settings
                                </h2>

                                <div class="space-y-6">
                                    <!-- Data Sharing -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Data Sharing</h3>
                                        <div class="space-y-4">
                                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                                <div>
                                                    <p class="font-medium text-gray-800">Usage Analytics</p>
                                                    <p class="text-gray-600 text-sm">Help us improve by sharing usage data</p>
                                                </div>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only toggle-checkbox" checked>
                                                    <div class="relative bg-gray-200 rounded-full w-12 h-6 transition-colors toggle-label">
                                                        <div class="top-1 left-1 absolute bg-white rounded-full w-4 h-4 transition-transform"></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                                <div>
                                                    <p class="font-medium text-gray-800">Marketing Communications</p>
                                                    <p class="text-gray-600 text-sm">Receive product updates and promotional emails</p>
                                                </div>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only toggle-checkbox">
                                                    <div class="relative bg-gray-200 rounded-full w-12 h-6 transition-colors toggle-label">
                                                        <div class="top-1 left-1 absolute bg-white rounded-full w-4 h-4 transition-transform"></div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Profile Visibility -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Profile Visibility</h3>
                                        <div class="space-y-4">
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="text-gray-700 label-text">Who can see your profile?</span>
                                                </label>
                                                <select class="w-full select-bordered select">
                                                    <option selected>Everyone in organization</option>
                                                    <option>Only my department</option>
                                                    <option>Only team members</option>
                                                    <option>Only me</option>
                                                </select>
                                            </div>
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="text-gray-700 label-text">Contact information visibility</span>
                                                </label>
                                                <select class="w-full select-bordered select">
                                                    <option selected>Team members only</option>
                                                    <option>Department only</option>
                                                    <option>Everyone</option>
                                                    <option>No one</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Appearance Settings Tab -->
                            <div class="hidden bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl tab-content glass-effect settings-card" id="appearance-tab">
                                <h2 class="flex items-center mb-6 font-bold text-gray-800 text-2xl">
                                    <i data-lucide="palette" class="mr-3 w-6 h-6 text-yellow-500"></i>
                                    Appearance Settings
                                </h2>

                                <div class="space-y-6">
                                    <!-- Theme Selection -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Theme</h3>
                                        <div class="gap-4 grid grid-cols-1 md:grid-cols-3">
                                            <div class="p-4 border-2 border-blue-500 rounded-lg text-center cursor-pointer">
                                                <i data-lucide="sun" class="mx-auto mb-2 w-8 h-8 text-blue-500"></i>
                                                <p class="font-medium">Light</p>
                                            </div>
                                            <div class="p-4 border-2 border-gray-200 rounded-lg text-center cursor-pointer">
                                                <i data-lucide="moon" class="mx-auto mb-2 w-8 h-8 text-gray-500"></i>
                                                <p class="font-medium">Dark</p>
                                            </div>
                                            <div class="p-4 border-2 border-gray-200 rounded-lg text-center cursor-pointer">
                                                <i data-lucide="monitor" class="mx-auto mb-2 w-8 h-8 text-gray-500"></i>
                                                <p class="font-medium">System</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Language & Region -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Language & Region</h3>
                                        <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="text-gray-700 label-text">Language</span>
                                                </label>
                                                <select class="w-full select-bordered select">
                                                    <option selected>English</option>
                                                    <option>Spanish</option>
                                                    <option>French</option>
                                                    <option>German</option>
                                                </select>
                                            </div>
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="text-gray-700 label-text">Time Zone</span>
                                                </label>
                                                <select class="w-full select-bordered select">
                                                    <option selected>Eastern Time (ET)</option>
                                                    <option>Central Time (CT)</option>
                                                    <option>Pacific Time (PT)</option>
                                                    <option>Mountain Time (MT)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Integrations Settings Tab -->
                            <div class="hidden bg-white/70 shadow-sm backdrop-blur-sm p-6 border border-gray-100/50 rounded-2xl tab-content glass-effect settings-card" id="integrations-tab">
                                <h2 class="flex items-center mb-6 font-bold text-gray-800 text-2xl">
                                    <i data-lucide="plug" class="mr-3 w-6 h-6 text-green-500"></i>
                                    Integrations
                                </h2>

                                <div class="space-y-6">
                                    <!-- Connected Apps -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Connected Applications</h3>
                                        <div class="space-y-4">
                                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex justify-center items-center bg-blue-500 rounded-lg w-10 h-10">
                                                        <i data-lucide="slack" class="w-5 h-5 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-800">Slack</p>
                                                        <p class="text-gray-600 text-sm">Connected to #general channel</p>
                                                    </div>
                                                </div>
                                                <button class="text-red-500 btn btn-ghost btn-sm">Disconnect</button>
                                            </div>
                                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex justify-center items-center bg-gray-800 rounded-lg w-10 h-10">
                                                        <i data-lucide="github" class="w-5 h-5 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-800">GitHub</p>
                                                        <p class="text-gray-600 text-sm">Connected to organization repos</p>
                                                    </div>
                                                </div>
                                                <button class="text-red-500 btn btn-ghost btn-sm">Disconnect</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Available Integrations -->
                                    <div class="setting-group">
                                        <h3 class="mb-4 font-semibold text-gray-800 text-lg">Available Integrations</h3>
                                        <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                                            <div class="p-4 border border-gray-200 rounded-lg">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="flex justify-center items-center bg-red-500 rounded w-8 h-8">
                                                        <i data-lucide="google" class="w-4 h-4 text-white"></i>
                                                    </div>
                                                    <p class="font-medium text-gray-800">Google Drive</p>
                                                </div>
                                                <p class="mb-3 text-gray-600 text-sm">Connect to your Google Drive account</p>
                                                <button class="btn-outline w-full btn btn-sm">Connect</button>
                                            </div>
                                            <div class="p-4 border border-gray-200 rounded-lg">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="flex justify-center items-center bg-blue-400 rounded w-8 h-8">
                                                        <i data-lucide="twitter" class="w-4 h-4 text-white"></i>
                                                    </div>
                                                    <p class="font-medium text-gray-800">Twitter</p>
                                                </div>
                                                <p class="mb-3 text-gray-600 text-sm">Connect to your Twitter account</p>
                                                <button class="btn-outline w-full btn btn-sm">Connect</button>
                                            </div>
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

        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            // Initialize toggle switches
            const toggleCheckboxes = document.querySelectorAll('.toggle-checkbox');
            toggleCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const label = this.nextElementSibling;
                    if (this.checked) {
                        label.classList.add('bg-blue-500');
                        label.querySelector('div').style.transform = 'translateX(24px)';
                    } else {
                        label.classList.remove('bg-blue-500');
                        label.querySelector('div').style.transform = 'translateX(0)';
                    }
                });

                // Initialize toggle positions
                if (checkbox.checked) {
                    const label = checkbox.nextElementSibling;
                    label.classList.add('bg-blue-500');
                    label.querySelector('div').style.transform = 'translateX(24px)';
                }
            });

            // Tab switching
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Update active tab button
                    tabButtons.forEach(btn => {
                        btn.classList.remove('tab-active', 'text-white', 'bg-gradient-to-br', 'from-blue-500', 'to-purple-600');
                        btn.classList.add('text-gray-700', 'hover:bg-blue-50');
                    });
                    this.classList.add('tab-active', 'text-white', 'bg-gradient-to-br', 'from-blue-500', 'to-purple-600');
                    this.classList.remove('text-gray-700', 'hover:bg-blue-50');

                    // Show target tab content
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    document.getElementById(`${targetTab}-tab`).classList.remove('hidden');
                });
            });

            // Theme selection
            const themeOptions = document.querySelectorAll('[class*="rounded-lg p-4 text-center cursor-pointer"]');
            themeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    themeOptions.forEach(opt => {
                        opt.classList.remove('border-blue-500', 'border-2');
                        opt.classList.add('border-gray-200', 'border-2');
                    });
                    this.classList.add('border-blue-500');
                    this.classList.remove('border-gray-200');
                });
            });
        });
    </script>
    <script src="../JAVASCRIPT/sidebar.js"></script>
</body>

</html>