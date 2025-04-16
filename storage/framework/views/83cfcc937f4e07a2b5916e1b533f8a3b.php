<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'PELIXS Admin Dashboard'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6d28d9', // Purple
                        secondary: '#4f46e5', // Indigo
                        accent: '#10b981', // Emerald
                        dark: {
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-in-out',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 3s infinite',
                        'spin-slow': 'spin 8s linear infinite',
                    },
                    boxShadow: {
                        'glow': '0 0 15px rgba(109, 40, 217, 0.4)'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        .sidebar-item {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .sidebar-item.active {
            background: linear-gradient(90deg, rgba(109,40,217,0.2) 0%, rgba(79,70,229,0.1) 100%);
            border-right: 3px solid #6d28d9;
        }
        
        .sidebar-item:hover::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: #6d28d9;
            box-shadow: 0 0 8px #6d28d9;
        }
        
        .glowing-text {
            text-shadow: 0 0 5px rgba(109, 40, 217, 0.5);
        }
        
        .sidebar-toggle-btn {
            background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
            border-radius: 50%;
            box-shadow: 0 0 15px rgba(109, 40, 217, 0.6);
        }
        
        .top-gradient {
            background: linear-gradient(to right, rgba(109, 40, 217, 0.05), rgba(79, 70, 229, 0.05), rgba(109, 40, 217, 0.05));
        }
        
        .stats-card {
            transition: all 0.3s ease;
            transform: translateY(0);
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(109, 40, 217, 0.5);
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(109, 40, 217, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(109, 40, 217, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(109, 40, 217, 0);
            }
        }
        
        .animate-delay-100 {
            animation-delay: 100ms;
        }
        
        .animate-delay-200 {
            animation-delay: 200ms;
        }
        
        .animate-delay-300 {
            animation-delay: 300ms;
        }
        
        .animate-delay-400 {
            animation-delay: 400ms;
        }
        
        .animate-delay-500 {
            animation-delay: 500ms;
        }
        
        [x-cloak] { 
            display: none !important; 
        }
        
        .progress-ring {
            transition: stroke-dashoffset 1s ease;
            transform: rotate(-90deg);
            transform-origin: center;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-dark-900 text-white font-sans" <?php echo $__env->yieldContent('body-attributes'); ?>>
    <!-- Background with animated overlay -->
    <div class="fixed inset-0 z-[-1]">
        <img src="../sttings/banner.png" onerror="this.src='https://cdn.pixabay.com/photo/2021/12/09/18/04/cinema-6858825_1280.jpg'" alt="background" class="object-cover w-full h-full opacity-15">
        <div class="absolute inset-0 bg-gradient-to-br from-dark-900/90 via-dark-900/80 to-dark-800/90"></div>
        <!-- Subtle grid overlay -->
        <div class="absolute inset-0" style="background-image: linear-gradient(rgba(79, 70, 229, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(79, 70, 229, 0.03) 1px, transparent 1px); background-size: 20px 20px;"></div>
    </div>

    <!-- Mobile Sidebar Toggle -->
    <button id="sidebar-toggle" class="lg:hidden fixed bottom-6 right-6 z-50 sidebar-toggle-btn p-3 text-white">
        <i id="sidebar-icon" class="ri-menu-line text-xl"></i>
    </button>

    <!-- Admin Dashboard Layout -->
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-dark-800/80 backdrop-blur-md z-40 transform transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full shadow-xl border-r border-dark-700/50">
            <div class="flex flex-col h-full justify-between p-4">
                <!-- Logo -->
                <div class="mb-8 mt-2">
                    <a href="#" class="text-2xl font-bold text-white flex items-center justify-center p-3 glowing-text">
                        <i class="ri-movie-2-line mr-2 text-primary text-3xl"></i>
                        <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">PELIXS</span>
                    </a>
                    <div class="h-px bg-gradient-to-r from-transparent via-primary/30 to-transparent my-4"></div>
                </div>
                
                <!-- Admin Navigation -->
                <nav class="flex-grow">
                    <ul class="space-y-2">
                        <li>
                            <a href="<?php echo e(route('admin.index')); ?>" class="sidebar-item flex items-center p-3 text-gray-100 rounded-lg hover:bg-dark-700/50 group transition-all">
                                <i class="ri-dashboard-line text-lg mr-3 text-primary group-hover:text-white transition-colors duration-300"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/movies" class="sidebar-item flex items-center p-3 text-gray-100 rounded-lg hover:bg-dark-700/50 group transition-all">
                                <i class="ri-movie-line text-lg mr-3 text-primary group-hover:text-white transition-colors duration-300"></i>
                                <span>Manage Movies</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/series" class="sidebar-item flex items-center p-3 text-gray-100 rounded-lg hover:bg-dark-700/50 group transition-all">
                                <i class="ri-film-line text-lg mr-3 text-primary group-hover:text-white transition-colors duration-300"></i>
                                <span>Manage Series</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/users" class="sidebar-item flex items-center p-3 text-gray-100 rounded-lg hover:bg-dark-700/50 group transition-all">
                                <i class="ri-user-line text-lg mr-3 text-primary group-hover:text-white transition-colors duration-300"></i>
                                <span>User Management</span>
                            </a>
                        </li>
                        
                    </ul>
                </nav>

                <!-- Quick Actions -->
                <div class="border-t border-dark-700/50 pt-4 space-y-2">
                    <a href="/home" class="sidebar-item flex items-center p-3 text-gray-100 rounded-lg hover:bg-dark-700/50 group transition-all">
                        <i class="ri-home-4-line text-lg mr-3 text-primary group-hover:text-white transition-colors duration-300"></i>
                        <span>Visit Website</span>
                    </a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="hidden">
                        <?php echo csrf_field(); ?>
                    </form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-item flex items-center p-3 text-gray-100 rounded-lg hover:bg-red-500/20 group transition-all">
                        <i class="ri-logout-box-r-line text-lg mr-3 text-red-400 group-hover:text-red-300 transition-colors duration-300"></i>
                        <span>Logout</span>
                    </a>
                </div>
                
                <!-- Admin version -->
                <div class="text-xs text-center text-gray-500 mt-6">
                    <p>PELIXS Admin v2.5.3</p>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 transition-all duration-300 lg:ml-64">
            <!-- Minimalist Top Bar -->
            <header class="bg-dark-800/70 backdrop-blur-md border-b border-dark-700/50 py-4 px-6 sticky top-0 z-30">
                <div class="flex items-center justify-between">
                    <!-- Page Title with decorative element -->
                    <div class="flex items-center">
                        <div class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></div>
                        <h1 class="text-xl font-semibold"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                    </div>
                    
                    <!-- Right side elements -->
                    <?php $__env->startSection('header-right'); ?>
                    <!-- Current Date & Time -->
                    <div class="text-sm text-gray-400">
                        <span id="current-date-time"><?php echo e(date('F j, Y')); ?></span>
                    </div>
                    <?php echo $__env->yieldSection(); ?>
                </div>
            </header>
            
            <!-- Decorative Gradient Bar -->
            <div class="h-1 top-gradient"></div>
            
            <!-- Dashboard Content -->
            <div class="p-6">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
            
            <!-- Footer -->
            <footer class="bg-dark-800/50 backdrop-blur-sm border-t border-dark-700/30 p-4 text-center text-gray-400 text-sm">
                <p>&copy; <?php echo date('Y'); ?> Ycode Admin Dashboard. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarIcon = document.getElementById('sidebar-icon');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                sidebarIcon.classList.remove('ri-menu-line');
                sidebarIcon.classList.add('ri-close-line');
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebarIcon.classList.remove('ri-close-line');
                sidebarIcon.classList.add('ri-menu-line');
            }
        });
        
        // Set active sidebar item based on current path
        document.addEventListener('DOMContentLoaded', function() {
            const path = window.location.pathname;
            const sidebarItems = document.querySelectorAll('.sidebar-item');
            
            sidebarItems.forEach(item => {
                const href = item.getAttribute('href');
                if (path === href || path.startsWith(href + '/')) {
                    item.classList.add('active');
                }
            });
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\Youcode\Herd\file-rouge\resources\views/components/layouts/admin.blade.php ENDPATH**/ ?>