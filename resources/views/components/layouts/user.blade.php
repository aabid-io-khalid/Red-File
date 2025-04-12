<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <title>PELIXS - Streaming Platform</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e50914', 
                        dark: {
                            DEFAULT: '#141414',
                            secondary: '#1E1E1E',
                            tertiary: '#2C2C2C'
                        },
                        accent: {
                            light: '#FF4B4B',
                            dark: '#B80F0A'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1E1E1E;
        }
        ::-webkit-scrollbar-thumb {
            background: #e50914;
            border-radius: 4px;
        }

        /* Swiper navigation buttons */
        .swiper-button-next,
        .swiper-button-prev {
            color: #e50914 !important;
            background: rgba(229, 9, 20, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: rgba(229, 9, 20, 0.4);
        }
    </style>
</head>
<body class="bg-dark text-white font-sans selection:bg-primary selection:text-white">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-dark/80 backdrop-blur-md border-b border-dark-secondary">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <a href="/" class="text-3xl font-black text-primary tracking-wider">PELIXS</a>
                <nav class="hidden md:flex space-x-5">
                    <a href="/home" class="text-gray-300 hover:text-primary transition-colors">Home</a>
                    <a href="/browse" class="text-gray-300 hover:text-primary transition-colors">Browse</a>
                    <a href="/movies" class="text-gray-300 hover:text-primary transition-colors">Movies</a>
                    <a href="/shows" class="text-gray-300 hover:text-primary transition-colors">TV Shows</a>
                    <a href="/anime" class="text-gray-300 hover:text-primary transition-colors">Anime</a>
                </nav>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <form id="search-form" class="flex items-center">
                        <input 
                            id="search-input" 
                            type="text" 
                            placeholder="Search movies, shows..." 
                            class="hidden bg-dark-secondary text-white px-4 py-2 rounded-full text-sm w-64 focus:outline-none focus:ring-2 focus:ring-primary transition-all duration-300"
                        >
                        <button 
                            type="button" 
                            id="search-toggle" 
                            class="text-xl text-gray-300 hover:text-primary transition-colors"
                        >
                            <i class="ri-search-line"></i>
                        </button>
                    </form>
                </div>
                
                <button class="text-xl text-gray-300 hover:text-primary transition-colors relative">
                    <i class="ri-notification-3-line"></i>
                    <span class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full px-1.5 py-0.5">3</span>
                </button>
                
                <div class="relative group">
                    <button class="w-10 h-10 bg-primary/20 text-primary rounded-full flex items-center justify-center">
                        <i class="ri-user-line"></i>
                    </button>
                    <div class="hidden group-hover:block absolute right-0 mt-3 w-56 bg-dark-secondary border border-dark-tertiary rounded-lg shadow-xl py-2 transition-all duration-300">
                        <a href="/profile" class="block px-4 py-2 hover:bg-dark-tertiary transition-colors flex items-center">
                            <i class="ri-user-line mr-3 text-primary"></i> Profile
                        </a>
                        <a href="/settings" class="block px-4 py-2 hover:bg-dark-tertiary transition-colors flex items-center">
                            <i class="ri-settings-3-line mr-3 text-primary"></i> Settings
                        </a>
                        <hr class="my-2 border-dark-tertiary">
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-dark-tertiary transition-colors flex items-center text-red-500">
                                <i class="ri-logout-box-r-line mr-3"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark-secondary py-12 mt-16 border-t border-dark-tertiary">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-primary mb-4">PELIXS</h3>
                    <p class="text-gray-400 text-sm">Stream the latest movies, TV shows, and anime. Your entertainment destination.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-gray-200">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="/movies" class="hover:text-primary">Movies</a></li>
                        <li><a href="/shows" class="hover:text-primary">TV Shows</a></li>
                        <li><a href="/anime" class="hover:text-primary">Anime</a></li>
                        <li><a href="/browse" class="hover:text-primary">Browse</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-gray-200">Support</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="/help" class="hover:text-primary">Help Center</a></li>
                        <li><a href="/contact" class="hover:text-primary">Contact Us</a></li>
                        <li><a href="/privacy" class="hover:text-primary">Privacy Policy</a></li>
                        <li><a href="/terms" class="hover:text-primary">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-gray-200">Follow Us</h4>
                    <div class="flex space-x-4 text-gray-400">
                        <a href="#" class="hover:text-primary"><i class="ri-twitter-line text-xl"></i></a>
                        <a href="#" class="hover:text-primary"><i class="ri-facebook-line text-xl"></i></a>
                        <a href="#" class="hover:text-primary"><i class="ri-instagram-line text-xl"></i></a>
                        <a href="#" class="hover:text-primary"><i class="ri-youtube-line text-xl"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center text-gray-500 text-sm mt-8 border-t border-dark-tertiary pt-6">
                © 2025 PELIXS. All rights reserved. Powered by TMDB API.
            </div>
        </div>
    </footer>

    <script>
        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchToggle = document.getElementById('search-toggle');
            const searchInput = document.getElementById('search-input');
            const searchForm = document.getElementById('search-form');
            
            searchToggle.addEventListener('click', function() {
                searchInput.classList.toggle('hidden');
                if (!searchInput.classList.contains('hidden')) {
                    searchInput.focus();
                }
            });
            
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (searchInput.value.trim() !== '') {
                        searchForm.submit();
                    }
                }
            });
        });
    </script>
</body>
</html>