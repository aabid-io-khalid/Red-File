<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @if(isset($csrf_token))
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @endif
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Google Fonts - Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @yield('additional-head')
    
    <title>PELIXS - @yield('title')</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e50914', 
                        secondary: '#ff8a00',
                        dark: '#141414',
                        darker: '#0b0b0b',
                        gray: {
                            850: '#1c1c1c'
                        }
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif']
                    },
                    boxShadow: {
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.2)',
                        'nav': '0 4px 6px -1px rgba(0, 0, 0, 0.3)'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        
        /* Gradient background */
        .bg-gradient-dark {
            background: linear-gradient(180deg, rgba(11,11,11,1) 0%, rgba(20,20,20,1) 100%);
        }
        
        /* Header & Navigation */
        .nav-link {
            position: relative;
            padding-bottom: 2px;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #e50914;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        
        /* Movie & Anime Cards */
        .content-card {
            transition: transform 0.35s ease, box-shadow 0.35s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .content-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }
        
        .card-image {
            transition: transform 0.5s ease, filter 0.5s ease;
        }
        
        .card-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.2) 100%);
            opacity: 0;
            transition: opacity 0.35s ease;
        }
        
        .content-card:hover .card-overlay {
            opacity: 1;
        }
        
        .content-card:hover .card-image {
            transform: scale(1.08);
            filter: brightness(0.6);
        }
        
        /* Rating styles */
        .rating-pill {
            background: linear-gradient(90deg, rgba(255,215,0,0.15) 0%, rgba(255,215,0,0.3) 100%);
            border: 1px solid rgba(255,215,0,0.4);
            backdrop-filter: blur(4px);
        }
        
        .genre-pill {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .genre-pill:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        /* Filter elements */
        .filter-dropdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        
        .filter-dropdown.open {
            max-height: 500px;
        }
        
        .genre-btn, .rating-btn {
            transition: all 0.3s ease;
            border: 1px solid rgba(229, 9, 20, 0.3);
        }
        
        .genre-btn:hover, .rating-btn:hover {
            background-color: rgba(229, 9, 20, 0.2);
        }
        
        .genre-btn.active, .rating-btn.active {
            background-color: #e50914;
            color: white;
            box-shadow: 0 2px 10px rgba(229, 9, 20, 0.4);
        }
        
        /* Custom slider */
        .year-slider {
            -webkit-appearance: none;
            height: 4px;
            background: linear-gradient(90deg, #3d3d3d 0%, #e50914 100%);
            border-radius: 2px;
        }
        
        .year-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #e50914;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .year-slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #e50914;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        /* Play button */
        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #e50914 0%, #b20710 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            border: 2px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .content-card:hover .play-button {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        
        .play-button:hover {
            transform: translate(-50%, -50%) scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
        }
        
        /* Profile dropdown */
        .profile-dropdown { 
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        .profile-dropdown.show { 
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #141414;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Search field animation */
        .search-input {
            width: 200px;
            transition: width 0.3s ease;
        }
        
        .search-input:focus {
            width: 240px;
            box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.3);
        }
        
        /* Mobile menu */
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .mobile-menu.open {
            transform: translateX(0);
        }
        
        @yield('additional-styles')
    </style>
</head>
<body class="bg-gradient-dark text-white font-sans min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-dark py-4 px-6 shadow-nav fixed top-0 w-full z-50 border-b border-gray-800">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <h1 class="text-3xl font-bold tracking-wider">
                    <a href="/home" class="flex items-center">
                        <span class="text-primary">PELIX</span><span class="text-white">S</span>
                    </a>
                </h1>
            </div>
            
            <!-- Mobile menu button -->
            <button id="mobile-menu-toggle" class="md:hidden text-2xl">
                <i class="ri-menu-line"></i>
            </button>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="/home" class="nav-link @yield('home-active', '')">Home</a>
                <a href="/browse" class="nav-link @yield('browse-active', '')">Browse</a>
                <a href="/movies" class="nav-link @yield('movies-active', '')">Movies</a>
                <a href="/shows" class="nav-link @yield('shows-active', '')">TV Shows</a>
                <a href="/anime" class="nav-link @yield('anime-active', '')">Anime</a>
                <a href="/mylist" class="nav-link @yield('mylist-active', '')">My List</a>
                
                @auth
                    @can('access-community-chat')
                        <a href="{{ url('/community') }}" class="nav-link">Community</a>
                    @endcan
                    <a href="{{ url('/subscription') }}" class="nav-link">Subscription</a>
                @else
                    <a href="{{ url('/login') }}" class="nav-link">Login</a>
                    <a href="{{ url('/community') }}" class="nav-link">Community</a>
                @endauth
                
                @yield('additional-nav-items')
            </nav>
            
            <!-- Right-side elements -->
            <div class="flex items-center space-x-5">
                <!-- Search bar -->
                <div class="relative hidden md:flex items-center">
                    <form id="search-form" action="@yield('search-action', '/browse')" method="get" class="flex items-center">
                        @if(request()->has('type'))
                            <input type="hidden" name="type" value="{{ request()->query('type') }}">
                        @endif
                        <input id="search-input" type="text" name="search" placeholder="Search..." 
                               value="{{ request()->query('search') }}"
                               class="search-input bg-gray-850 text-white px-4 py-2.5 rounded-full text-sm focus:outline-none border border-gray-700">
                        <button type="submit" class="absolute right-2 text-xl p-2 rounded-full hover:bg-gray-800 transition">
                            <i class="ri-search-line"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Notifications -->
                <button class="text-xl p-2 rounded-full hover:bg-gray-800 transition relative">
                    <i class="ri-notification-3-line"></i>
                    <span class="absolute top-0 right-0 h-4 w-4 bg-primary rounded-full text-xs flex items-center justify-center">3</span>
                </button>
                
                <!-- Profile dropdown -->
                <div class="relative">
                    <button id="profile-toggle" class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center border-2 border-white hover:scale-105 transition duration-300">
                        <i class="ri-user-line text-white"></i>
                    </button>
                    <div id="profile-dropdown" class="profile-dropdown absolute right-0 top-full mt-3 w-64 bg-gray-850 border border-gray-700 rounded-xl shadow-card overflow-hidden">
                        <div class="p-4 border-b border-gray-700">
                            <h3 class="font-bold">John Doe</h3>
                            <p class="text-xs text-gray-400">Premium Member</p>
                        </div>
                        <ul class="py-2">
                            <li>
                                <a href="/profile" class="block px-4 py-3 hover:bg-gray-800 transition flex items-center">
                                    <i class="ri-user-line mr-3 text-primary"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a href="/settings" class="block px-4 py-3 hover:bg-gray-800 transition flex items-center">
                                    <i class="ri-settings-3-line mr-3 text-primary"></i> Settings
                                </a>
                            </li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                                   class="block px-4 py-3 hover:bg-gray-800 transition text-gray-300 hover:text-white flex items-center">
                                    <i class="ri-logout-box-r-line mr-3 text-primary"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile navigation menu (off-canvas) -->
    <div id="mobile-menu" class="mobile-menu fixed z-40 top-0 left-0 w-72 h-full bg-gray-850 shadow-lg pt-20 border-r border-gray-800">
        <div class="px-4 py-2 flex flex-col">
            <div class="mb-4 relative">
                <form id="mobile-search-form" action="@yield('search-action', '/browse')" method="get">
                    @if(request()->has('type'))
                        <input type="hidden" name="type" value="{{ request()->query('type') }}">
                    @endif
                    <input type="text" name="search" placeholder="Search..." 
                           value="{{ request()->query('search') }}"
                           class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm focus:outline-none border border-gray-700 w-full">
                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-xl">
                        <i class="ri-search-line"></i>
                    </button>
                </form>
            </div>
            <nav class="flex flex-col space-y-1">
                <a href="/home" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center @yield('home-active', '') transition duration-200">
                    <i class="ri-home-5-line mr-3 text-primary"></i> Home
                </a>
                <a href="/browse" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center @yield('browse-active', '') transition duration-200">
                    <i class="ri-compass-3-line mr-3 text-primary"></i> Browse
                </a>
                <a href="/movies" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center @yield('movies-active', '') transition duration-200">
                    <i class="ri-film-line mr-3 text-primary"></i> Movies
                </a>
                <a href="/shows" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center @yield('shows-active', '') transition duration-200">
                    <i class="ri-tv-2-line mr-3 text-primary"></i> TV Shows
                </a>
                <a href="/anime" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center @yield('anime-active', '') transition duration-200">
                    <i class="ri-rocket-line mr-3 text-primary"></i> Anime
                </a>
                <a href="/mylist" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center @yield('mylist-active', '') transition duration-200">
                    <i class="ri-bookmark-line mr-3 text-primary"></i> My List
                </a>
                
                <div class="border-t border-gray-700 my-2"></div>
                
                @auth
                    @can('access-community-chat')
                        <a href="{{ url('/community') }}" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center transition duration-200">
                            <i class="ri-chat-3-line mr-3 text-primary"></i> Community
                        </a>
                    @endcan
                    <a href="{{ url('/subscription') }}" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center transition duration-200">
                        <i class="ri-vip-crown-line mr-3 text-primary"></i> Subscription
                    </a>
                @else
                    <a href="{{ url('/login') }}" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center transition duration-200">
                        <i class="ri-login-box-line mr-3 text-primary"></i> Login
                    </a>
                    <a href="{{ url('/community') }}" class="py-3 px-4 rounded-lg hover:bg-dark flex items-center transition duration-200">
                        <i class="ri-chat-3-line mr-3 text-primary"></i> Community
                    </a>
                @endauth
            </nav>
        </div>
    </div>
    
    <!-- Overlay for mobile menu -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main content with proper spacing from fixed header -->
    <main class="flex-grow pt-24 pb-12">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark py-10 border-t border-gray-800">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <span class="text-primary">PELIX</span><span>S</span>
                    </h3>
                    <p class="text-gray-400 text-sm">Your ultimate destination for movies, TV shows, and anime streaming.</p>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-4">Navigate</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="/home" class="hover:text-primary transition">Home</a></li>
                        <li><a href="/browse" class="hover:text-primary transition">Browse</a></li>
                        <li><a href="/movies" class="hover:text-primary transition">Movies</a></li>
                        <li><a href="/shows" class="hover:text-primary transition">TV Shows</a></li>
                        <li><a href="/anime" class="hover:text-primary transition">Anime</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="/profile" class="hover:text-primary transition">Profile</a></li>
                        <li><a href="/subscription" class="hover:text-primary transition">Subscription</a></li>
                        <li><a href="/settings" class="hover:text-primary transition">Settings</a></li>
                        <li><a href="/help" class="hover:text-primary transition">Help Center</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="/terms" class="hover:text-primary transition">Terms of Service</a></li>
                        <li><a href="/privacy" class="hover:text-primary transition">Privacy Policy</a></li>
                        <li><a href="/copyright" class="hover:text-primary transition">Copyright</a></li>
                        <li><a href="/contact" class="hover:text-primary transition">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-6 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">© 2025 PELIXS. All rights reserved. Powered by TMDB API.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-primary transition"><i class="ri-facebook-fill"></i></a>
                    <a href="#" class="text-gray-400 hover:text-primary transition"><i class="ri-twitter-fill"></i></a>
                    <a href="#" class="text-gray-400 hover:text-primary transition"><i class="ri-instagram-line"></i></a>
                    <a href="#" class="text-gray-400 hover:text-primary transition"><i class="ri-youtube-fill"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        const profileToggle = document.getElementById('profile-toggle');
        const profileDropdown = document.getElementById('profile-dropdown');
        
        if (profileToggle && profileDropdown) {
            profileToggle.addEventListener('click', () => {
                profileDropdown.classList.toggle('show');
            });
            
            document.addEventListener('click', (e) => {
                if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('show');
                }
            });
        }
        
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileOverlay = document.getElementById('mobile-overlay');
        
        if (mobileMenuToggle && mobileMenu && mobileOverlay) {
            mobileMenuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('open');
                mobileOverlay.classList.toggle('hidden');
                
                const icon = mobileMenuToggle.querySelector('i');
                if (icon.classList.contains('ri-menu-line')) {
                    icon.classList.remove('ri-menu-line');
                    icon.classList.add('ri-close-line');
                } else {
                    icon.classList.remove('ri-close-line');
                    icon.classList.add('ri-menu-line');
                }
            });
            
            mobileOverlay.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                mobileOverlay.classList.add('hidden');
                const icon = mobileMenuToggle.querySelector('i');
                icon.classList.remove('ri-close-line');
                icon.classList.add('ri-menu-line');
            });
        }
        
        // Filter dropdown functionality (if used in the content section)
        document.querySelectorAll('.filter-toggle').forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                const target = document.querySelector(toggle.dataset.target);
                if (target) {
                    target.classList.toggle('open');
                    
                    const icon = toggle.querySelector('i');
                    if (icon.classList.contains('ri-arrow-down-s-line')) {
                        icon.classList.remove('ri-arrow-down-s-line');
                        icon.classList.add('ri-arrow-up-s-line');
                    } else {
                        icon.classList.remove('ri-arrow-up-s-line');
                        icon.classList.add('ri-arrow-down-s-line');
                    }
                }
            });
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = window.location.pathname;
            document.querySelectorAll('nav a').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>