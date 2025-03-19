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
    
    <title>Ycode - Premium Movie Experience</title>
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
                    <span>Ycode</span>
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
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all" data-genre="28">
                                <i class="ri-sword-line text-lg mr-3 text-primary"></i>
                                <span>Action</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all" data-genre="35">
                                <i class="ri-aliens-line text-lg mr-3 text-primary"></i>
                                <span>Comedy</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all" data-genre="18">
                                <i class="ri-ghost-line text-lg mr-3 text-primary"></i>
                                <span>Drama</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all" data-genre="27">
                                <i class="ri-ghost-line text-lg mr-3 text-primary"></i>
                                <span>Horror</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all" data-genre="878">
                                <i class="ri-ghost-line text-lg mr-3 text-primary"></i>
                                <span>Sci-Fi</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all" data-genre="53">
                                <i class="ri-ghost-line text-lg mr-3 text-primary"></i>
                                <span>Thriller</span>
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
                    <div class="swiper-wrapper" id="hero-container">
                        <!-- Hero slides will be injected here -->
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
                <button id="reset-filters" class="px-4 py-2 bg-primary/20 hover:bg-primary/30 text-primary rounded-lg text-sm font-medium transition-all">
                    Reset
                </button>
                <button id="apply-filters" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-medium transition-all">
                    Apply Filters
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Genre Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Genre</label>
                <select id="genre-filter" class="w-full px-4 py-2.5 bg-gray-700/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600">
                    <option value="all">All Genres</option>
                    <option value="28">Action</option>
                    <option value="35">Comedy</option>
                    <option value="18">Drama</option>
                    <option value="27">Horror</option>
                    <option value="878">Sci-Fi</option>
                    <option value="53">Thriller</option>
                </select>
            </div>
            
            <!-- Year Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Release Year</label>
                <select id="year-filter" class="w-full px-4 py-2.5 bg-gray-700/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600">
                    <option value="all">All Years</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                    <option value="2015-2020">2015-2020</option>
                    <option value="before-2015">Before 2015</option>
                </select>
            </div>
            
            <!-- Rating Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Rating</label>
                <select id="rating-filter" class="w-full px-4 py-2.5 bg-gray-700/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600">
                    <option value="all">All Ratings</option>
                    <option value="9">9+ Stars</option>
                    <option value="8">8+ Stars</option>
                    <option value="7">7+ Stars</option>
                    <option value="6">6+ Stars</option>
                    <option value="5">5+ Stars</option>
                </select>
            </div>
            
            <!-- Sort By Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Sort By</label>
                <select id="sort-filter" class="w-full px-4 py-2.5 bg-gray-700/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600">
                    <option value="popularity.desc">Popularity</option>
                    <option value="release_date.desc">Latest Release</option>
                    <option value="vote_average.desc">Rating (High to Low)</option>
                    <option value="vote_average.asc">Rating (Low to High)</option>
                    <option value="title.asc">Title (A-Z)</option>
                    <option value="title.desc">Title (Z-A)</option>
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
                    <div class="swiper-wrapper pb-8" id="movie-container">
                        <!-- Movie cards will be injected here -->
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
                    <div class="swiper-wrapper pb-8" id="series-container">
                        <!-- Series cards will be injected here -->
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
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="upcoming-container">
                    <!-- Upcoming movie cards will be injected here -->
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
        const apiKey = 'fcc76d42a33f4349db9ab3a42d7cc207'; // Replace with your actual API key
        const heroContainer = document.getElementById('hero-container');
        const movieContainer = document.getElementById('movie-container');
        const seriesContainer = document.getElementById('series-container');
        const upcomingContainer = document.getElementById('upcoming-container');

        async function fetchMovies(genre = 'all') {
            let url = `https://api.themoviedb.org/3/trending/movie/week?api_key=${apiKey}`;
            if (genre !== 'all') {
                url = `https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&with_genres=${genre}`;
            }
            try {
                const response = await fetch(url);
                const data = await response.json();
                displayMovies(data.results);
            } catch (error) {
                console.error('Error fetching movies:', error);
            }
        }

        async function fetchSeries() {
            try {
                const response = await fetch(`https://api.themoviedb.org/3/tv/popular?api_key=${apiKey}`);
                const data = await response.json();
                displaySeries(data.results);
            } catch (error) {
                console.error('Error fetching series:', error);
            }
        }

        async function fetchUpcoming() {
            try {
                const response = await fetch(`https://api.themoviedb.org/3/movie/upcoming?api_key=${apiKey}`);
                const data = await response.json();
                displayUpcoming(data.results);
            } catch (error) {
                console.error('Error fetching upcoming movies:', error);
            }
        }

        function displayMovies(movies) {
            movieContainer.innerHTML = ''; // Clear existing content
            movies.forEach(movie => {
                const movieCard = `
                    <div class="swiper-slide">
                        <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="https://image.tmdb.org/t/p/w500${movie.poster_path}" alt="${movie.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                        HD
                                    </span>
                                    <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                        <i class="ri-play-fill text-xl"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center text-xs text-gray-400 mb-2">
                                    <span class="flex items-center mr-3">
                                        <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                        <span>${movie.vote_average}</span>
                                    </span>
                                    <span>${new Date(movie.release_date).getFullYear()}</span>
                                </div>
                                <h3 class="font-semibold text-lg mb-1 truncate">${movie.title}</h3>
                                <p class="text-gray-400 text-sm">${movie.genre_ids.join(', ')}</p>
                            </div>
                        </div>
                    </div>
                `;
                movieContainer.innerHTML += movieCard;
            });
            initializeMovieSwiper();
        }

        function displaySeries(series) {
            seriesContainer.innerHTML = ''; // Clear existing content
            series.forEach(serie => {
                const seriesCard = `
                    <div class="swiper-slide">
                        <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300 h-full">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="https://image.tmdb.org/t/p/w500${serie.poster_path}" alt="${serie.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary/80 text-white">
                                        HD
                                    </span>
                                    <button class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all">
                                        <i class="ri-play-fill text-xl"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center text-xs text-gray-400 mb-2">
                                    <span class="flex items-center mr-3">
                                        <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                        <span>${serie.vote_average}</span>
                                    </span>
                                    <span>${serie.number_of_seasons} Seasons</span>
                                </div>
                                <h3 class="font-semibold text-lg mb-1 truncate">${serie.name}</h3>
                                <p class="text-gray-400 text-sm">${serie.genre_ids.join(', ')}</p>
                            </div>
                        </div>
                    </div>
                `;
                seriesContainer.innerHTML += seriesCard;
            });
            initializeSeriesSwiper();
        }

        function displayUpcoming(upcoming) {
            upcomingContainer.innerHTML = ''; // Clear existing content
            upcoming.forEach(movie => {
                const upcomingCard = `
                    <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl overflow-hidden group transition-all duration-300">
                        <div class="relative aspect-video overflow-hidden">
                            <img src="https://image.tmdb.org/t/p/w500${movie.poster_path}" alt="${movie.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent">
                                <div class="absolute bottom-4 left-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-500/90 text-white mb-2">
                                        Coming Soon
                                    </span>
                                    <h3 class="font-semibold text-lg mb-1">${movie.title}</h3>
                                    <p class="text-gray-300 text-xs">${new Date(movie.release_date).toLocaleDateString()}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                upcomingContainer.innerHTML += upcomingCard;
            });
        }

        function initializeMovieSwiper() {
            new Swiper('.movie-swiper', {
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
        }

        function initializeSeriesSwiper() {
            new Swiper('.series-swiper', {
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
        }

        // Event listeners for filters
        document.getElementById('genre-filter').addEventListener('change', (event) => {
            const selectedGenre = event.target.value;
            fetchMovies(selectedGenre);
        });

        // Fetch data on page load
        fetchMovies();
        fetchSeries();
        fetchUpcoming();
    
        // Event listener for the Apply Filters button
document.getElementById('apply-filters').addEventListener('click', () => {
    const genre = document.getElementById('genre-filter').value;
    const year = document.getElementById('year-filter').value;
    const rating = document.getElementById('rating-filter').value;
    const sort = document.getElementById('sort-filter').value;

    // Construct the URL with query parameters
    let query = `filtered-movies?genre=${genre}&year=${year}&rating=${rating}&sort=${sort}`;
    window.location.href = query; // Redirect to the filtered movies page
});
    </script>
</body>
</html>