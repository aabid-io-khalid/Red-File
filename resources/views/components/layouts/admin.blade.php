<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ycode Admin Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-sans">
    <!-- Background with overlay -->
    <div class="fixed inset-0 z-[-1]">
        <img src="{{ asset('assets/img/banner.png') }}" alt="background" class="object-cover w-full h-full opacity-20">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/80 to-gray-900"></div>
    </div>

    <!-- Admin Dashboard Layout -->
    <div class="flex">
        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gray-800/70 backdrop-blur-md z-50">
            <div class="flex flex-col h-full justify-between p-4">
                <!-- Logo -->
                <div class="mb-6">
                    <a href="#" class="text-2xl font-bold text-primary flex items-center">
                        <i class="ri-movie-2-line mr-2"></i>
                        <span>Ycode Admin</span>
                    </a>
                </div>
                
                <!-- Admin Navigation -->
                <nav class="flex-grow">
                    <ul class="space-y-1">
                        <li>
                            <a href="#dashboard" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-dashboard-line text-lg mr-3 text-primary"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="../admin/" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-movie-line text-lg mr-3 text-primary"></i>
                                <span>Manage Movies</span>
                            </a>
                        </li>
                        <li>
                            <a href="../admin/series" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-film-line text-lg mr-3 text-primary"></i>
                                <span>Manage Series</span>
                            </a>
                        </li>
                        <li>
                            <a href="../admin/user" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-user-line text-lg mr-3 text-primary"></i>
                                <span>User Management</span>
                            </a>
                        </li>
                        <li>
                            <a href="../admin/analytics" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-bar-chart-line text-lg mr-3 text-primary"></i>
                                <span>Analytics</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Admin Profile & Settings -->
                <div class="border-t border-gray-700 pt-4">
                    <a href="#settings" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                        <i class="ri-settings-4-line text-lg mr-3 text-primary"></i>
                        <span>Settings</span>
                    </a>
                    <a href="#logout" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                        <i class="ri-logout-box-r-line text-lg mr-3 text-primary"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="lg:ml-64 flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>