<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Remix Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css">
    
    <!-- Swiper JS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#5865F2",
                        secondary: "#151f32",
                        dark: "#0f172a",
                        light: "#f8fafc"
                    }
                }
            }
        }
    </script>
    
    <title>PELIXS - Premium Movie Experience</title>
</head>
<body class="bg-gray-900 text-white font-sans">
    <!-- Background with overlay -->
    <div class="fixed inset-0 z-[-1]">
        <img src="{{ asset('assets/img/banner.png') }}" alt="background" class="object-cover w-full h-full opacity-20">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/80 to-gray-900"></div>
    </div>

    <!-- Sidebar Navigation (hidden on mobile) -->
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gray-800/70 backdrop-blur-md transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 z-50">
        <div class="flex flex-col h-full justify-between p-4">
            <!-- Logo -->
            <div class="mb-6">
                <a href="#" class="text-2xl font-bold text-primary flex items-center">
                    <i class="ri-movie-2-line mr-2"></i>
                    <span>PELIXS</span>
                </a>
            </div>
            
            <!-- Main Navigation -->
            <nav class="flex-grow">
                <ul class="space-y-1">
                    <li>
                        <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                            <i class="ri-home-5-line text-lg mr-3 text-primary"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                            <i class="ri-movie-line text-lg mr-3 text-primary"></i>
                            <span>Movies</span>
                        </a>
                    </li>
                    <li>
                        <a href="./tv-shows" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                            <i class="ri-film-line text-lg mr-3 text-primary"></i>
                            <span>Series</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                            <i class="ri-heart-3-line text-lg mr-3 text-primary"></i>
                            <span>Favorites</span>
                        </a>
                    </li>
                </ul>
                
                <!-- Categories Section -->
                <div class="mt-8">
                    <h3 class="px-3 mb-3 text-sm font-medium text-gray-400 uppercase">Categories</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-sword-line text-lg mr-3 text-primary"></i>
                                <span>Action</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-aliens-line text-lg mr-3 text-primary"></i>
                                <span>Sci-Fi</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-ghost-line text-lg mr-3 text-primary"></i>
                                <span>Horror</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center justify-between p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <div class="flex items-center">
                                    <i class="ri-movie-2-line text-lg mr-3 text-primary"></i>
                                    <span>More Genres</span>
                                </div>
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Account Section -->
                <div class="mt-8">
                    <h3 class="px-3 mb-3 text-sm font-medium text-gray-400 uppercase">Account</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-account-circle-line text-lg mr-3 text-primary"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-settings-3-line text-lg mr-3 text-primary"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Logout Button -->
            <div class="pt-4 border-t border-gray-700">
                <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                    <i class="ri-logout-box-line text-lg mr-3 text-red-400"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </div>
    </aside>
    
    <!-- Mobile Header -->
    <header class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-gray-800/80 backdrop-blur-md border-b border-gray-700">
        <div class="flex items-center justify-between p-4">
            <button id="menu-button" class="p-2 focus:outline-none">
                <i class="ri-menu-line text-xl"></i>
            </button>
            
            <a href="#" class="text-2xl font-bold text-primary flex items-center">
                <i class="ri-movie-2-line mr-2"></i>
                <span>PELIXS</span>
            </a>
            
            <div class="flex items-center gap-4">
                <button class="p-2 focus:outline-none">
                    <i class="ri-search-line text-xl"></i>
                </button>
                <button class="p-2 focus:outline-none">
                    <i class="ri-notification-2-line text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Search Bar (expandable) -->
        <div id="search-container" class="hidden px-4 pb-4">
            <div class="relative">
                <input type="search" placeholder="Search movies, series, actors..." class="w-full px-4 py-2 pl-10 bg-gray-700/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="min-h-screen lg:pl-64 pt-16 lg:pt-0">
        <div class="container mx-auto px-4 py-6">
            <!-- Hero Section -->
            <section class="mb-12 relative rounded-2xl overflow-hidden">
                <div class="swiper hero-swiper">
                    <div class="swiper-wrapper">
                        <!-- Hero Slide 1 -->
                        <div class="swiper-slide">
                            <div class="relative h-96 md:h-[500px] rounded-2xl overflow-hidden">
                                <img src="{{ asset('assets/img/banner.png') }}" alt="Avengers" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-r from-gray-900 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 p-6 md:p-10 w-full md:w-2/3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary text-white mb-3">
                                        Featured
                                    </span>
                                    <h1 class="text-3xl md:text-5xl font-bold mb-2">Avengers: Endgame</h1>
                                    <div class="flex items-center text-sm mb-4">
                                        <span class="flex items-center mr-4">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>9.2/10</span>
                                        </span>
                                        <span class="mr-4">2019</span>
                                        <span class="mr-4">3h 1m</span>
                                        <span>Action, Adventure</span>
                                    </div>
                                    <p class="text-gray-300 text-sm md:text-base mb-6 max-w-xl">
                                        After Thanos wiped out half of all life in the universe, the Avengers must reunite and assemble again to restore balance.
                                    </p>
                                    <div class="flex flex-wrap gap-3">
                                        <a href="#" class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white font-medium rounded-lg inline-flex items-center transition-all">
                                            <i class="ri-play-fill mr-2"></i> Watch Now
                                        </a>
                                        <a href="#" class="px-6 py-2.5 bg-gray-700/50 hover:bg-gray-700 text-white font-medium rounded-lg inline-flex items-center transition-all">
                                            <i class="ri-add-line mr-2"></i> Add to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hero Slide 2 -->
                        <div class="swiper-slide">
                            <div class="relative h-96 md:h-[500px] rounded-2xl overflow-hidden">
                                <img src="{{ asset('assets/img/movie-2.png') }}" alt="Interstellar" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-r from-gray-900 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 p-6 md:p-10 w-full md:w-2/3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary text-white mb-3">
                                        Featured
                                    </span>
                                    <h1 class="text-3xl md:text-5xl font-bold mb-2">Interstellar</h1>
                                    <div class="flex items-center text-sm mb-4">
                                        <span class="flex items-center mr-4">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>8.7/10</span>
                                        </span>
                                        <span class="mr-4">2014</span>
                                        <span class="mr-4">2h 49m</span>
                                        <span>Sci-Fi, Adventure</span>
                                    </div>
                                    <p class="text-gray-300 text-sm md:text-base mb-6 max-w-xl">
                                        When Earth becomes uninhabitable, a farmer and ex-NASA pilot leads a mission through a wormhole seeking a new home for humanity.
                                    </p>
                                    <div class="flex flex-wrap gap-3">
                                        <a href="#" class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white font-medium rounded-lg inline-flex items-center transition-all">
                                            <i class="ri-play-fill mr-2"></i> Watch Now
                                        </a>
                                        <a href="#" class="px-6 py-2.5 bg-gray-700/50 hover:bg-gray-700 text-white font-medium rounded-lg inline-flex items-center transition-all">
                                            <i class="ri-add-line mr-2"></i> Add to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hero Navigation -->
                    <div class="swiper-button-next text-white after:content-['']">
                        <i class="ri-arrow-right-s-line text-xl bg-gray-800/50 backdrop-blur-sm p-3 rounded-full"></i>
                    </div>
                    <div class="swiper-button-prev text-white after:content-['']">
                        <i class="ri-arrow-left-s-line text-xl bg-gray-800/50 backdrop-blur-sm p-3 rounded-full"></i>
                    </div>
                </div>
            </section>
            
            <!-- Advanced Filter Section -->
            <section class="mb-12">
                <div class="bg-gray-800/30 backdrop-blur-sm p-5 rounded-xl border border-gray-700/50">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                        <h2 class="text-xl font-semibold">Advanced Filters</h2>
                        <div class="flex flex-wrap gap-2">
                            <button class="px-4 py-2 bg-primary/20 hover:bg-primary/30 text-primary rounded-lg text-sm font-medium transition-all">
                                Reset
                            </button>
                            <button class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-medium transition-all">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Genre Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Genre</label>
                            <select class="w-full px-4 py-2.5 bg-gray-700/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600">
                                <option>All Genres</option>
                                <option>Action</option>
                                <option>Comedy</option>
                                <option>Drama</option>
                                <option>Horror</option>
                                <option>Sci-Fi</option>
                                <option>Thriller</option>
                            </select>
                        </div>
                        
                        <!-- Year Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Release Year</label>
                            <select class="w-full px-4 py-2.5 bg-gray-700/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600">
                                <option>All Years</option>
                                <option>2025</option>
                                <option>2024</option>
                                <option>2023</option>
                                <option>2022</option>
                                <option>2021</option>
                                <option>2015-2020</option>
                                <option>Before 2015</option>
                            </select>
                        </div>
                        
                        <!-- Rating Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Rating</label>
                            <select class="w-full px-4 py-2.5 bg-gray-700/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600">
                                <option>All Ratings</option>
                                <option>9+ Stars</option>
                                <option>8+ Stars</option>
                                <option>7+ Stars</option>
                                <option>6+ Stars</option>
                                <option>5+ Stars</option>
                            </select>
                        </div>
                        
                        <!-- Sort By Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Sort By</label>
                            <select class="w-full px-4 py-2.5 bg-gray-700/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600">
                                <option>Popularity</option>
                                <option>Latest Release</option>
                                <option>Rating (High to Low)</option>
                                <option>Rating (Low to High)</option>
                                <option>Title (A-Z)</option>
                                <option>Title (Z-A)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Trending Movies Section -->
            <section class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold">Trending Movies</h2>
                    <a href="#" class="text-primary hover:text-primary/80 font-medium inline-flex items-center">
                        View All <i class="ri-arrow-right-s-line ml-1"></i>
                    </a>
                </div>
                
                <div class="swiper movie-swiper">
                    <div class="swiper-wrapper pb-8">
                        <!-- Movie Card 1 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/movie-1.png') }}" alt="Avengers" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>9.0</span>
                                        </span>
                                        <span>2019</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">Avengers: Endgame</h3>
                                    <p class="text-gray-400 text-sm">Action, Adventure</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Movie Card 2 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/movie-2.png') }}" alt="Interstellar" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>8.7</span>
                                        </span>
                                        <span>2014</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">Interstellar</h3>
                                    <p class="text-gray-400 text-sm">Sci-Fi, Adventure</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Movie Card 3 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/movie-3.png') }}" alt="Lord of the Rings" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>8.9</span>
                                        </span>
                                        <span>2003</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">The Lord of the Rings</h3>
                                    <p class="text-gray-400 text-sm">Fantasy, Adventure</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Movie Card 4 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/movie-4.png') }}" alt="Inception" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>8.8</span>
                                        </span>
                                        <span>2010</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">Inception</h3>
                                    <p class="text-gray-400 text-sm">Sci-Fi, Action</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Movie Card 5 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/movie-5.png') }}" alt="The Dark Knight" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>9.1</span>
                                        </span>
                                        <span>2008</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">The Dark Knight</h3>
                                    <p class="text-gray-400 text-sm">Action, Crime, Drama</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Movie Card 6 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/movie-6.png') }}" alt="Dune" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>8.5</span>
                                        </span>
                                        <span>2021</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">Dune</h3>
                                    <p class="text-gray-400 text-sm">Sci-Fi, Adventure</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Slider navigation -->
                    <div class="swiper-pagination"></div>
                </div>
            </section>
            
            <!-- Popular Series Section -->
            <section class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold">Popular Series</h2>
                    <a href="#" class="text-primary hover:text-primary/80 font-medium inline-flex items-center">
                        View All <i class="ri-arrow-right-s-line ml-1"></i>
                    </a>
                </div>
                
                <div class="swiper series-swiper">
                    <div class="swiper-wrapper pb-8">
                        <!-- Series Card 1 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/series-1.png') }}" alt="Stranger Things" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>8.7</span>
                                        </span>
                                        <span>4 Seasons</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">Stranger Things</h3>
                                    <p class="text-gray-400 text-sm">Sci-Fi, Horror</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Series Card 2 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/series-2.png') }}" alt="Breaking Bad" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>9.5</span>
                                        </span>
                                        <span>5 Seasons</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">Breaking Bad</h3>
                                    <p class="text-gray-400 text-sm">Crime, Drama</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Series Card 3 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/series-3.png') }}" alt="Game of Thrones" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>9.2</span>
                                        </span>
                                        <span>8 Seasons</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">Game of Thrones</h3>
                                    <p class="text-gray-400 text-sm">Fantasy, Drama</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Series Card 4 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/series-4.png') }}" alt="The Witcher" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>8.3</span>
                                        </span>
                                        <span>2 Seasons</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">The Witcher</h3>
                                    <p class="text-gray-400 text-sm">Fantasy, Action</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Series Card 5 -->
                        <div class="swiper-slide">
                            <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                                <div class="relative aspect-[2/3] overflow-hidden">
                                    <img src="{{ asset('assets/img/series-5.png') }}" alt="The Mandalorian" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                            HD
                                        </span>
                                        <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                            <i class="ri-play-fill text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <button class="w-8 h-8 bg-gray-800/70 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="ri-heart-3-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center text-xs text-gray-400 mb-2">
                                        <span class="flex items-center mr-3">
                                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                            <span>8.7</span>
                                        </span>
                                        <span>3 Seasons</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-1 truncate">The Mandalorian</h3>
                                    <p class="text-gray-400 text-sm">Sci-Fi, Action</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Slider navigation -->
                    <div class="swiper-pagination"></div>
                </div>
            </section>
            
            <!-- Upcoming Releases Section -->
            <section class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold">Upcoming Releases</h2>
                    <a href="#" class="text-primary hover:text-primary/80 font-medium inline-flex items-center">
                        View All <i class="ri-arrow-right-s-line ml-1"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Upcoming Movie 1 -->
                    <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300">
                        <div class="relative aspect-video overflow-hidden">
                            <img src="{{ asset('assets/img/upcoming-1.png') }}" alt="Upcoming Movie" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent">
                                <div class="absolute bottom-4 left-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-500/90 text-white mb-2">
                                        Coming Soon
                                    </span>
                                    <h3 class="font-semibold text-lg mb-1">Avatar 3</h3>
                                    <p class="text-gray-300 text-xs">March 15, 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upcoming Movie 2 -->
                    <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300">
                        <div class="relative aspect-video overflow-hidden">
                            <img src="{{ asset('assets/img/upcoming-2.png') }}" alt="Upcoming Movie" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent">
                                <div class="absolute bottom-4 left-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-500/90 text-white mb-2">
                                        Coming Soon
                                    </span>
                                    <h3 class="font-semibold text-lg mb-1">Mission: Impossible 8</h3>
                                    <p class="text-gray-300 text-xs">April 22, 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upcoming Movie 3 -->
                    <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300">
                        <div class="relative aspect-video overflow-hidden">
                            <img src="{{ asset('assets/img/upcoming-3.png') }}" alt="Upcoming Movie" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent">
                                <div class="absolute bottom-4 left-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-500/90 text-white mb-2">
                                        Coming Soon
                                    </span>
                                    <h3 class="font-semibold text-lg mb-1">Guardians of the Galaxy Vol. 4</h3>
                                    <p class="text-gray-300 text-xs">May 5, 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Newsletter Section -->
            <section class="mb-12">
                <div class="bg-gradient-to-r from-primary/20 to-secondary/30 backdrop-blur-lg rounded-2xl p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 rounded-full filter blur-3xl -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-primary/10 rounded-full filter blur-3xl -ml-20 -mb-20"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="md:w-1/2">
                            <h2 class="text-2xl md:text-3xl font-bold mb-4">Subscribe to Our Newsletter</h2>
                            <p class="text-gray-300 mb-6">Stay updated with the latest movies, TV shows, and exclusive content. Get personalized recommendations direct to your inbox.</p>
                            <form class="flex flex-col sm:flex-row gap-3">
                                <input type="email" placeholder="Enter your email" class="flex-grow px-4 py-3 bg-gray-700/70 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600">
                                <button type="submit" class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-medium rounded-lg inline-flex items-center transition-all whitespace-nowrap">
                                    Subscribe <i class="ri-arrow-right-line ml-2"></i>
                                </button>
                            </form>
                        </div>
                        <div class="md:w-1/3 flex justify-center">
                            <img src="{{ asset('assets/img/newsletter.png') }}" alt="Newsletter illustration" class="w-64">
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="lg:pl-64 bg-gray-800/30 backdrop-blur-md border-t border-gray-700/50">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Brand Info -->
                <div>
                    <a href="#" class="text-2xl font-bold text-primary flex items-center mb-4">
                        <i class="ri-movie-2-line mr-2"></i>
                        <span>PELIXS</span>
                    </a>
                    <p class="text-gray-400 mb-6">Your premium movie streaming platform with the latest releases and classic favorites.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-700/50 rounded-full flex items-center justify-center text-gray-300 hover:text-primary hover:bg-gray-700 transition-all">
                            <i class="ri-facebook-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-700/50 rounded-full flex items-center justify-center text-gray-300 hover:text-primary hover:bg-gray-700 transition-all">
                            <i class="ri-twitter-x-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-700/50 rounded-full flex items-center justify-center text-gray-300 hover:text-primary hover:bg-gray-700 transition-all">
                            <i class="ri-instagram-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-700/50 rounded-full flex items-center justify-center text-gray-300 hover:text-primary hover:bg-gray-700 transition-all">
                            <i class="ri-youtube-fill"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Links 1 -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">Movies</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">TV Shows</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">Top IMDB</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">New Releases</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">Popular</a></li>
                    </ul>
                </div>
                
                <!-- Links 2 -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Help</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">Account</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">FAQs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="ri-map-pin-line text-primary mt-1 mr-3"></i>
                            <span class="text-gray-400">123 Streaming Ave, Hollywood, CA 91234</span>
                        </li>
                        <li class="flex items-center">
                            <i class="ri-phone-line text-primary mr-3"></i>
                            <span class="text-gray-400">+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="ri-mail-line text-primary mr-3"></i>
                            <span class="text-gray-400">support@pelixs.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-12 pt-6 border-t border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">© 2025 PELIXS. All rights reserved.</p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-primary text-sm transition-colors">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-primary text-sm transition-colors">Terms of Service</a>
                        <a href="#" class="text-gray-400 hover:text-primary text-sm transition-colors">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Mobile Navigation Menu Overlay -->
    <div id="menu-overlay" class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden hidden"></div>
    
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-6 right-6 w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white shadow-lg z-10 transform translate-y-20 opacity-0 transition-all">
        <i class="ri-arrow-up-line text-xl"></i>
    </button>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- Custom Script -->
    <script>
        // Initialize Hero Swiper
        const heroSwiper = new Swiper('.hero-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
        
        // Initialize Movie Swiper
        const movieSwiper = new Swiper('.movie-swiper', {
            slidesPerView: 2,
            spaceBetween: 16,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 4,
                },
                1024: {
                    slidesPerView: 5,
                },
            },
        });
        
        // Initialize Series Swiper
        const seriesSwiper = new Swiper('.series-swiper', {
            slidesPerView: 2,
            spaceBetween: 16,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 4,
                },
                1024: {
                    slidesPerView: 5,
                },
            },
        });

        // Mobile Menu Toggle
        const menuButton = document.getElementById('menu-button');
        const menuOverlay = document.getElementById('menu-overlay');
        const sidebar = document.getElementById('sidebar');

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            menuOverlay.classList.toggle('hidden');
        });

        menuOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            menuOverlay.classList.add('hidden');
        });

        // Back to Top Button
        const backToTopButton = document.getElementById('back-to-top');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopButton.classList.remove('opacity-0', 'translate-y-20');
                backToTopButton.classList.add('opacity-100', 'translate-y-0');
            } else {
                backToTopButton.classList.add('opacity-0', 'translate-y-20');
                backToTopButton.classList.remove('opacity-100', 'translate-y-0');
            }
        });

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>