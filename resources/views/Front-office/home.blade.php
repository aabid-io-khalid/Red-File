<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Swiper.js for carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.js"></script>
    
    <title>PELIXS - Home</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e50914', 
                        dark: '#141414',
                        darker: '#0b0b0b'
                    }
                }
            }
        }
    </script>
    <style>
        .movie-card:hover .card-overlay {
            opacity: 1;
        }
        .movie-card:hover .play-button {
            transform: translateY(0) scale(1);
        }
        .movie-card:hover .card-image {
            transform: scale(1.05);
            filter: brightness(0.7);
        }
        .rating-pill {
            background: rgba(255, 215, 0, 0.2);
            border: 1px solid rgba(255, 215, 0, 0.4);
        }
        .genre-pill {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(4px);
        }
        .hero-gradient {
            background: linear-gradient(to top, rgba(11, 11, 11, 1) 0%, rgba(11, 11, 11, 0) 100%);
        }
        .swiper-container {
            overflow: hidden;
            position: relative;
        }
        .swiper-button-next, .swiper-button-prev {
            color: white !important;
            background: rgba(0, 0, 0, 0.6);
            width: 40px !important;
            height: 40px !important;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .swiper-button-next:hover, .swiper-button-prev:hover {
            background: rgba(229, 9, 20, 0.8);
        }
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 18px !important;
        }
        .swiper-pagination-bullet {
            background: white;
            opacity: 0.6;
        }
        .swiper-pagination-bullet-active {
            background: #e50914;
            opacity: 1;
        }
        .search-expand {
            width: 0;
            transition: width 0.3s ease;
        }
        .search-expanded {
            width: 200px;
        }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .swiper-wrapper::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .swiper-wrapper {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="bg-darker text-white font-sans">
    <!-- Header -->
    <header class="bg-dark py-4 px-6 shadow-lg fixed top-0 w-full z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-primary tracking-wider">PELIXS</h1>
            <nav class="hidden md:flex space-x-6">
                <a href="/home" class="text-primary font-medium">Home</a>
                <a href="/browse" class="hover:text-primary transition">Browse</a>
                <a href="/movies" class="hover:text-primary transition">Movies</a>
                <a href="/shows" class="hover:text-primary transition">TV Shows</a>
                <a href="/anime" class="hover:text-primary transition">Anime</a>
                <a href="/browse?list=favorites" class="hover:text-primary transition">My List</a>
            </nav>
            <div class="flex items-center space-x-4">
                <div class="relative flex items-center">
                    <form id="search-form" action="/browse" method="get" class="flex items-center">
                        <input id="search-input" type="text" name="search" placeholder="Search movies & shows..." class="search-expand bg-gray-800 text-white px-4 py-2 rounded-full text-sm focus:outline-none border border-gray-700 hidden">
                        <button type="button" id="search-toggle" class="text-xl p-2 rounded-full hover:bg-gray-800 transition">
                            <i class="ri-search-line"></i>
                        </button>
                    </form>
                </div>
                <button class="text-xl p-2 rounded-full hover:bg-gray-800 transition"><i class="ri-notification-3-line"></i></button>
                <div class="w-8 h-8 bg-primary rounded-full"></div>
            </div>
        </div>
    </header>

    <main class="pt-16">
        <!-- Hero Carousel -->
        <div class="swiper-container hero-swiper relative h-[70vh]">
            <div class="swiper-wrapper" id="hero-wrapper">
                <!-- Hero slides will be dynamically inserted here -->
            </div>
            <div class="swiper-pagination absolute bottom-6 z-10"></div>
        </div>

        <!-- Trending Now Section -->
        <section class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Trending Now</h2>
                <a href="/browse?sort=trending" class="text-primary hover:underline flex items-center">
                    View All <i class="ri-arrow-right-s-line ml-1"></i>
                </a>
            </div>
            
            <div class="swiper-container trending-swiper">
                <div class="swiper-wrapper" id="trending-wrapper">
                    <!-- Trending movies will be dynamically inserted here -->
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>

        <!-- New Releases Section -->
        <section class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">New Releases</h2>
                <a href="/browse?sort=release_date&order=desc" class="text-primary hover:underline flex items-center">
                    View All <i class="ri-arrow-right-s-line ml-1"></i>
                </a>
            </div>
            
            <div class="swiper-container new-releases-swiper">
                <div class="swiper-wrapper" id="new-releases-wrapper">
                    <!-- New releases will be dynamically inserted here -->
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>

        <!-- Upcoming Movies Section -->
        <section class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Upcoming Movies</h2>
                <a href="/browse?filter=upcoming" class="text-primary hover:underline flex items-center">
                    View All <i class="ri-arrow-right-s-line ml-1"></i>
                </a>
            </div>
            
            <div class="swiper-container upcoming-swiper">
                <div class="swiper-wrapper" id="upcoming-wrapper">
                    <!-- Upcoming movies will be dynamically inserted here -->
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>

        <!-- Top Rated TV Shows Section -->
        <section class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Top Rated TV Shows</h2>
                <a href="/browse?type=tv&sort=vote_average&order=desc" class="text-primary hover:underline flex items-center">
                    View All <i class="ri-arrow-right-s-line ml-1"></i>
                </a>
            </div>
            
            <div class="swiper-container tv-shows-swiper">
                <div class="swiper-wrapper" id="tv-shows-wrapper">
                    <!-- TV shows will be dynamically inserted here -->
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>
    </main>
    
    <footer class="bg-dark mt-16 py-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-400 text-sm">
                <p>© 2025 PELIXS. All rights reserved. Powered by TMDB API.</p>
            </div>
        </div>
    </footer>

    <script>
        // API key and base URLs
        const API_KEY = 'fcc76d42a33f4349db9ab3a42d7cc207'; // Replace with your actual TMDB API key
        const BASE_URL = 'https://api.themoviedb.org/3';
        const IMAGE_BASE_URL = 'https://image.tmdb.org/t/p';

        // Genre mapping - we'll use this to convert genre IDs to names
        const genreMap = {};

        // Handle search functionality
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

        // Fetch genres first to map IDs to names
        async function fetchGenres() {
            try {
                const response = await fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}`);
                const data = await response.json();
                
                data.genres.forEach(genre => {
                    genreMap[genre.id] = genre.name;
                });
                
                // Also fetch TV genres to have a complete mapping
                const tvResponse = await fetch(`${BASE_URL}/genre/tv/list?api_key=${API_KEY}`);
                const tvData = await tvResponse.json();
                
                tvData.genres.forEach(genre => {
                    genreMap[genre.id] = genre.name;
                });
            } catch (error) {
                console.error('Error fetching genres:', error);
            }
        }

        // Fetch hero movies (popular movies for banner)
        async function fetchHeroMovies() {
            try {
                const response = await fetch(`${BASE_URL}/movie/popular?api_key=${API_KEY}&page=1`);
                const data = await response.json();
                const heroWrapper = document.getElementById('hero-wrapper');
                
                // Take top 5 movies for hero banner
                const heroMovies = data.results.slice(0, 5);
                
                heroMovies.forEach(movie => {
                    const releaseYear = movie.release_date ? new Date(movie.release_date).getFullYear() : '';
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide relative h-[70vh]';
                    
                    slide.innerHTML = `
                        <img src="${IMAGE_BASE_URL}/original${movie.backdrop_path}" alt="${movie.title}" class="w-full h-full object-cover">
                        <div class="hero-gradient absolute inset-0 flex flex-col justify-end p-8 md:p-16">
                            <div class="max-w-2xl">
                                <h2 class="text-4xl md:text-6xl font-bold mb-3">${movie.title}</h2>
                                <div class="flex items-center gap-4 mb-4">
                                    <span class="rating-pill text-sm px-2.5 py-1 rounded-full flex items-center">
                                        <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                        <span>${movie.vote_average.toFixed(1)}</span>
                                    </span>
                                    <span class="genre-pill text-sm px-2.5 py-1 rounded-full">${releaseYear}</span>
                                </div>
                                <p class="text-gray-300 mb-6 line-clamp-2 md:line-clamp-3">${movie.overview}</p>
                                <div class="flex flex-wrap gap-3">
                                    <button class="bg-primary hover:bg-primary/90 px-6 py-3 rounded-lg font-medium flex items-center">
                                        <i class="ri-play-fill mr-2 text-xl"></i> Play Now
                                    </button>
                                    <button class="bg-gray-800 hover:bg-gray-700 px-6 py-3 rounded-lg font-medium flex items-center">
                                        <i class="ri-information-line mr-2"></i> More Info
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    heroWrapper.appendChild(slide);
                });
                
                // Initialize hero swiper after content is loaded
                new Swiper('.hero-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.hero-swiper .swiper-pagination',
                        clickable: true,
                    },
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    }
                });
                
            } catch (error) {
                console.error('Error fetching hero movies:', error);
            }
        }

        // Fetch trending movies
        async function fetchTrendingMovies() {
            try {
                const response = await fetch(`${BASE_URL}/trending/movie/week?api_key=${API_KEY}`);
                const data = await response.json();
                const trendingWrapper = document.getElementById('trending-wrapper');
                
                data.results.forEach(movie => {
                    const releaseYear = movie.release_date ? new Date(movie.release_date).getFullYear() : '';
                    const genreNames = movie.genre_ids.map(id => genreMap[id] || '').filter(name => name !== '');
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide';
                    
                    slide.innerHTML = `
                        <div class="movie-card group rounded-xl overflow-hidden shadow-xl bg-dark border border-gray-800 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/20 w-44">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="${IMAGE_BASE_URL}/w500${movie.poster_path}" alt="${movie.title}" class="card-image w-full h-full object-cover transition-all duration-500">
                                <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-0 transition-opacity duration-300 p-4 flex flex-col justify-between">
                                    <div class="flex justify-between items-start">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary text-white">
                                            HD
                                        </span>
                                        <button class="w-9 h-9 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-primary border border-gray-700 transition-colors">
                                            <i class="ri-heart-line"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <div class="flex gap-2 mb-3">
                                            <span class="rating-pill text-xs px-2 py-1 rounded-full flex items-center">
                                                <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                                <span>${movie.vote_average.toFixed(1)}</span>
                                            </span>
                                            <span class="genre-pill text-xs px-2 py-1 rounded-full">${releaseYear}</span>
                                        </div>
                                        <a href="/movie/${movie.id}" class="block">
                                            <div class="play-button w-12 h-12 mx-auto bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-8 scale-75 transition-all duration-300">
                                                <i class="ri-play-fill text-xl"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg tracking-wide mb-1 truncate">${movie.title}</h3>
                                <div class="flex flex-wrap gap-1">
                                    ${genreNames.slice(0, 2).map(genre => 
                                        `<span class="text-xs text-gray-400 bg-gray-800/80 px-2 py-0.5 rounded">${genre}</span>`
                                    ).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    trendingWrapper.appendChild(slide);
                });
                
                // Initialize trending swiper with fixed slides and proper navigation
                new Swiper('.trending-swiper', {
                    slidesPerView: 2,
                    spaceBetween: 16,
                    grabCursor: true,
                    preventClicks: true,
                    navigation: {
                        nextEl: '.trending-swiper .swiper-button-next',
                        prevEl: '.trending-swiper .swiper-button-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 3 },
                        768: { slidesPerView: 4 },
                        1024: { slidesPerView: 5 },
                        1280: { slidesPerView: 6 }
                    }
                });
                
            } catch (error) {
                console.error('Error fetching trending movies:', error);
            }
        }

        // Fetch new releases (recently released movies)
        async function fetchNewReleases() {
            try {
                // Get current date
                const today = new Date();
                const oneMonthAgo = new Date();
                oneMonthAgo.setMonth(today.getMonth() - 1);
                
                const fromDate = oneMonthAgo.toISOString().split('T')[0];
                const toDate = today.toISOString().split('T')[0];
                
                const response = await fetch(`${BASE_URL}/discover/movie?api_key=${API_KEY}&primary_release_date.gte=${fromDate}&primary_release_date.lte=${toDate}&sort_by=primary_release_date.desc`);
                const data = await response.json();
                const newReleasesWrapper = document.getElementById('new-releases-wrapper');
                
                data.results.forEach(movie => {
                    const releaseYear = movie.release_date ? new Date(movie.release_date).getFullYear() : '';
                    const genreNames = movie.genre_ids.map(id => genreMap[id] || '').filter(name => name !== '');
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide';
                    
                    slide.innerHTML = `
                        <div class="movie-card group rounded-xl overflow-hidden shadow-xl bg-dark border border-gray-800 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/20 w-44">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="${movie.poster_path ? `${IMAGE_BASE_URL}/w500${movie.poster_path}` : '/api/placeholder/500/750'}" alt="${movie.title}" class="card-image w-full h-full object-cover transition-all duration-500">
                                <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-0 transition-opacity duration-300 p-4 flex flex-col justify-between">
                                    <div class="flex justify-between items-start">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary text-white">
                                            NEW
                                        </span>
                                        <button class="w-9 h-9 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-primary border border-gray-700 transition-colors">
                                            <i class="ri-heart-line"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <div class="flex gap-2 mb-3">
                                            <span class="rating-pill text-xs px-2 py-1 rounded-full flex items-center">
                                                <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                                <span>${movie.vote_average.toFixed(1)}</span>
                                            </span>
                                            <span class="genre-pill text-xs px-2 py-1 rounded-full">${releaseYear}</span>
                                        </div>
                                        <a href="/movie/${movie.id}" class="block">
                                            <div class="play-button w-12 h-12 mx-auto bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-8 scale-75 transition-all duration-300">
                                                <i class="ri-play-fill text-xl"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg tracking-wide mb-1 truncate">${movie.title}</h3>
                                <div class="flex flex-wrap gap-1">
                                    ${genreNames.slice(0, 2).map(genre => 
                                        `<span class="text-xs text-gray-400 bg-gray-800/80 px-2 py-0.5 rounded">${genre}</span>`
                                    ).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    newReleasesWrapper.appendChild(slide);
                });
                
                // Initialize new releases swiper with fixed slides and proper navigation
                new Swiper('.new-releases-swiper', {
                    slidesPerView: 2,
                    spaceBetween: 16,
                    grabCursor: true,
                    preventClicks: true,
                    navigation: {
                        nextEl: '.new-releases-swiper .swiper-button-next',
                        prevEl: '.new-releases-swiper .swiper-button-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 3 },
                        768: { slidesPerView: 4 },
                        1024: { slidesPerView: 5 },
                        1280: { slidesPerView: 6 }
                    }
                });
                
            } catch (error) {
                console.error('Error fetching new releases:', error);
            }
        }

        // Fetch upcoming movies
        async function fetchUpcomingMovies() {
            try {
                const response = await fetch(`${BASE_URL}/movie/upcoming?api_key=${API_KEY}`);
                const data = await response.json();
                const upcomingWrapper = document.getElementById('upcoming-wrapper');
                
                data.results.forEach(movie => {
                    const releaseYear = movie.release_date ? new Date(movie.release_date).getFullYear() : '';
                    const genreNames = movie.genre_ids.map(id => genreMap[id] || '').filter(name => name !== '');
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide';
                    
                    slide.innerHTML = `
                        <div class="movie-card group rounded-xl overflow-hidden shadow-xl bg-dark border border-gray-800 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/20 w-44">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="${movie.poster_path ? `${IMAGE_BASE_URL}/w500${movie.poster_path}` : '/api/placeholder/500/750'}" alt="${movie.title}" class="card-image w-full h-full object-cover transition-all duration-500">
                                <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-0 transition-opacity duration-300 p-4 flex flex-col justify-between">
                                    <div class="flex justify-between items-start">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary text-white">
                                            COMING SOON
                                        </span>
                                        <button class="w-9 h-9 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-primary border border-gray-700 transition-colors">
                                            <i class="ri-heart-line"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <div class="flex gap-2 mb-3">
                                            <span class="genre-pill text-xs px-2 py-1 rounded-full">${movie.release_date}</span>
                                        </div>
                                        <a href="/movie/${movie.id}" class="block">
                                            <div class="play-button w-12 h-12 mx-auto bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-8 scale-75 transition-all duration-300">
                                                <i class="ri-play-fill text-xl"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg tracking-wide mb-1 truncate">${movie.title}</h3>
                                <div class="flex flex-wrap gap-1">
                                    ${genreNames.slice(0, 2).map(genre => 
                                        `<span class="text-xs text-gray-400 bg-gray-800/80 px-2 py-0.5 rounded">${genre}</span>`
                                    ).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    upcomingWrapper.appendChild(slide);
                });
                
                // Initialize upcoming swiper with fixed slides and proper navigation
                new Swiper('.upcoming-swiper', {
                    slidesPerView: 2,
                    spaceBetween: 16,
                    grabCursor: true,
                    preventClicks: true,
                    navigation: {
                        nextEl: '.upcoming-swiper .swiper-button-next',
                        prevEl: '.upcoming-swiper .swiper-button-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 3 },
                        768: { slidesPerView: 4 },
                        1024: { slidesPerView: 5 },
                        1280: { slidesPerView: 6 }
                    }
                });
                
            } catch (error) {
                console.error('Error fetching upcoming movies:', error);
            }
        }

        // Fetch top-rated TV shows
        async function fetchTopRatedTVShows() {
            try {
                const response = await fetch(`${BASE_URL}/tv/top_rated?api_key=${API_KEY}`);
                const data = await response.json();
                const tvShowsWrapper = document.getElementById('tv-shows-wrapper');
                
                data.results.forEach(show => {
                    const releaseYear = show.first_air_date ? new Date(show.first_air_date).getFullYear() : '';
                    const genreNames = show.genre_ids.map(id => genreMap[id] || '').filter(name => name !== '');
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide';
                    
                    slide.innerHTML = `
                        <div class="movie-card group rounded-xl overflow-hidden shadow-xl bg-dark border border-gray-800 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/20 w-44">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="${show.poster_path ? `${IMAGE_BASE_URL}/w500${show.poster_path}` : '/api/placeholder/500/750'}" alt="${show.name}" class="card-image w-full h-full object-cover transition-all duration-500">
                                <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-0 transition-opacity duration-300 p-4 flex flex-col justify-between">
                                    <div class="flex justify-between items-start">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary text-white">
                                            TV
                                        </span>
                                        <button class="w-9 h-9 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-primary border border-gray-700 transition-colors">
                                            <i class="ri-heart-line"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <div class="flex gap-2 mb-3">
                                            <span class="rating-pill text-xs px-2 py-1 rounded-full flex items-center">
                                                <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                                <span>${show.vote_average.toFixed(1)}</span>
                                            </span>
                                            <span class="genre-pill text-xs px-2 py-1 rounded-full">${releaseYear}</span>
                                        </div>
                                        <a href="/tv/${show.id}" class="block">
                                            <div class="play-button w-12 h-12 mx-auto bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-8 scale-75 transition-all duration-300">
                                                <i class="ri-play-fill text-xl"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg tracking-wide mb-1 truncate">${show.name}</h3>
                                <div class="flex flex-wrap gap-1">
                                    ${genreNames.slice(0, 2).map(genre => 
                                        `<span class="text-xs text-gray-400 bg-gray-800/80 px-2 py-0.5 rounded">${genre}</span>`
                                    ).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    tvShowsWrapper.appendChild(slide);
                });
                
                // Initialize top-rated TV shows swiper with fixed slides and proper navigation
                new Swiper('.tv-shows-swiper', {
                    slidesPerView: 2,
                    spaceBetween: 16,
                    grabCursor: true,
                    preventClicks: true,
                    navigation: {
                        nextEl: '.tv-shows-swiper .swiper-button-next',
                        prevEl: '.tv-shows-swiper .swiper-button-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 3 },
                        768: { slidesPerView: 4 },
                        1024: { slidesPerView: 5 },
                        1280: { slidesPerView: 6 }
                    }
                });
                
            } catch (error) {
                console.error('Error fetching top-rated TV shows:', error);
            }
        }

        // Initialize all fetch functions
        async function init() {
            await fetchGenres();
            await fetchHeroMovies();
            await fetchTrendingMovies();
            await fetchNewReleases();
            await fetchUpcomingMovies();
            await fetchTopRatedTVShows();
        }

        // Start the application
        init();
    </script>
</body>
</html>