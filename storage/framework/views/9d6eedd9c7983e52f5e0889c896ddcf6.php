<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.js"></script>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>PELIXS - TV Show Details</title>
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
        .show-card:hover .card-overlay { opacity: 1; }
        .show-card:hover .play-button { transform: translateY(0) scale(1); }
        .show-card:hover .card-image { transform: scale(1.05); filter: brightness(0.7); }
        .rating-pill { background: rgba(255, 215, 0, 0.2); border: 1px solid rgba(255, 215, 0, 0.4); }
        .genre-pill { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px); }
        .hero-gradient { background: linear-gradient(to top, rgba(11, 11, 11, 1) 0%, rgba(11, 11, 11, 0) 100%); }
        .blur-backdrop { backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .swiper-container { overflow: hidden; position: relative; }
        .swiper-button-next, .swiper-button-prev {
            color: white !important;
            background: rgba(0, 0, 0, 0.6);
            width: 40px !important;
            height: 40px !important;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .swiper-button-next:hover, .swiper-button-prev:hover { background: rgba(229, 9, 20, 0.8); }
        .swiper-button-next:after, .swiper-button-prev:after { font-size: 18px !important; }
        .search-expand { width: 0; transition: width 0.3s ease; }
        .search-expanded { width: 200px; }
        .banner-overlay { background: linear-gradient(to bottom, rgba(11, 11, 11, 0) 0%, rgba(11, 11, 11, 0.8) 60%, rgba(11, 11, 11, 1) 100%); }
        .season-button:hover .play-icon { transform: scale(1.2); }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(229, 9, 20, 0.5); }
        .swiper-wrapper::-webkit-scrollbar { display: none; }
        .swiper-wrapper { -ms-overflow-style: none; scrollbar-width: none; }
        .episode-item:hover { background-color: rgba(255, 255, 255, 0.05); }
        .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .accordion-open .accordion-content { max-height: 2000px; }
        .accordion-icon { transition: transform 0.3s ease; }
        .accordion-open .accordion-icon { transform: rotate(180deg); }
        .episodes-list { max-height: 500px; overflow-y: auto; }
        .episodes-list::-webkit-scrollbar { width: 6px; }
        .episodes-list::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
        .episodes-list::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 10px; }
        .episodes-list::-webkit-scrollbar-thumb:hover { background: rgba(229, 9, 20, 0.5); }
        .download-btn { transition: all 0.2s ease; }
        .download-btn:hover { transform: scale(1.1); }
        .scale-95 { transform: scale(0.95); }
        .scale-100 { transform: scale(1); }
        .opacity-0 { opacity: 0; }
        .transform { transition-property: transform, opacity; }
        .pulse-animation { animation: pulse 0.5s; }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(229, 9, 20, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(229, 9, 20, 0); }
            100% { box-shadow: 0 0 0 0 rgba(229, 9, 20, 0); }
        }
    </style>
</head>
<body class="bg-darker text-white font-sans">
    <!-- Header -->
    <header class="bg-dark py-4 px-6 shadow-lg fixed top-0 w-full z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-primary tracking-wider">PELIXS</h1>
            <!-- Navigation -->
            <nav class="hidden md:flex space-x-6">
                <a href="/home" class="text-primary font-medium">Home</a>
                <a href="/browse" class="hover:text-primary transition">Browse</a>
                <a href="/movies" class="hover:text-primary transition">Movies</a>
                <a href="/shows" class="hover:text-primary transition">TV Shows</a>
                <a href="/anime" class="hover:text-primary transition">Anime</a>
                <?php if(auth()->guard()->check()): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('access-community-chat')): ?>
                        <a href="<?php echo e(url('/community')); ?>" class="hover:text-primary transition">Community</a>
                        <a href="/mylist" class="hover:text-primary transition">My List</a>
                    <?php endif; ?>
                    <a href="<?php echo e(url('/subscription')); ?>" class="hover:text-primary transition">Subscription</a>
                <?php else: ?>
                    <a href="<?php echo e(url('/login')); ?>" class="hover:text-primary transition">Community</a>
                <?php endif; ?>
            </nav>
            <div class="flex items-center space-x-4">
                <div class="relative flex items-center">
                    <form id="search-form" action="/browse" method="get" class="flex items-center">
                        <input id="search-input" type="text" name="search" placeholder="Search movies & shows..." 
                               class="search-expand bg-gray-800 text-white px-4 py-2 rounded-full text-sm focus:outline-none border border-gray-700 hidden">
                        <button type="button" id="search-toggle" class="text-xl p-2 rounded-full hover:bg-gray-800 transition">
                            <i class="ri-search-line"></i>
                        </button>
                    </form>
                </div>
                <?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(url('/login')); ?>"
                       class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary/90 transition flex items-center">
                        <i class="ri-login-box-line mr-2"></i> Log In
                    </a>
                <?php else: ?>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline-flex">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition flex items-center">
                            <i class="ri-logout-box-r-line mr-2"></i> Log Out
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="pt-16" id="main-content">
        <!-- TV Show Banner (Dynamic) -->
        <div class="relative h-[70vh] w-full" id="show-banner">
            <!-- Banner content will be dynamically populated -->
        </div>

        <!-- TV Show Details -->
        <div class="container mx-auto px-4 py-6 -mt-16 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Section - Show Info -->
                <div class="lg:col-span-2">
                    <!-- Show Stats -->
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        <div id="show-rating" class="rating-pill text-sm px-3 py-1.5 rounded-full flex items-center">
                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                            <span>0.0</span>
                        </div>
                        <div id="show-year" class="genre-pill text-sm px-3 py-1.5 rounded-full">2023</div>
                        <div id="show-seasons" class="genre-pill text-sm px-3 py-1.5 rounded-full flex items-center">
                            <i class="ri-film-line mr-1"></i>
                            <span>0 seasons</span>
                        </div>
                        <div id="show-quality" class="bg-primary text-white text-sm px-3 py-1.5 rounded-full">
                            HD
                        </div>
                    </div>

                    <!-- Show Description -->
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4" id="show-title">Show Title</h2>
                        <p class="text-gray-300 mb-4" id="show-overview">Show overview will appear here.</p>
                        
                        <!-- Genres -->
                        <div class="flex flex-wrap gap-2 mb-6" id="show-genres">
                            <!-- Genres will be dynamically populated -->
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-4">
                            <button class="bg-primary hover:bg-primary/90 px-6 py-3 rounded-lg font-medium flex items-center" id="watch-now-btn">
                                <i class="ri-play-fill mr-2 text-xl play-icon transition-transform duration-300"></i> Watch Now
                            </button>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add-to-my-list')): ?>
    <button 
        id="tv-show-<?php echo e($show['id']); ?>-button"
        onclick="toggleTvShow(<?php echo e($show['id']); ?>)"
        class="bg-primary/10 text-primary border border-primary/30 hover:bg-primary/20 px-4 py-2 rounded-lg font-medium transition-all duration-300 ease-in-out flex items-center justify-center space-x-2 hover:scale-105 active:scale-95">
        <i class="ri-<?php echo e($isInList ? 'close-line' : 'add-line'); ?> mr-1"></i>
        <?php echo e($isInList ? 'Remove from My List' : 'Add to My List'); ?>

    </button>
<?php endif; ?>


                            <button class="bg-gray-800 hover:bg-gray-700 px-6 py-3 rounded-lg font-medium flex items-center">
                                <i class="ri-share-line mr-2"></i> Share
                            </button>
                            <button class="bg-gray-800 hover:bg-gray-700 px-6 py-3 rounded-lg font-medium flex items-center" id="watch-trailer-btn">
                                <i class="ri-film-line mr-2"></i> Watch Trailer
                            </button>
                        </div>
                    </div>

                    <!-- Seasons Section -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-semibold mb-4">Seasons</h3>
                        <div class="grid grid-cols-1 gap-4" id="seasons-accordion">
                            <!-- Seasons will be dynamically populated -->
                        </div>
                    </div>

                    <!-- Cast & Crew -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-semibold mb-4">Cast & Crew</h3>
                        <div class="swiper-container cast-swiper">
                            <div class="swiper-wrapper" id="cast-wrapper">
                                <!-- Cast members will be dynamically populated -->
                            </div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Production Information -->
                        <div>
                            <h3 class="text-xl font-semibold mb-4">Production Info</h3>
                            <div class="space-y-3">
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Created By:</span>
                                    <span id="show-creator" class="text-white">-</span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Network:</span>
                                    <span id="show-network" class="text-white">-</span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Production:</span>
                                    <span id="show-production" class="text-white">-</span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Country:</span>
                                    <span id="show-country" class="text-white">-</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Release Information -->
                        <div>
                            <h3 class="text-xl font-semibold mb-4">Show Info</h3>
                            <div class="space-y-3">
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">First Air Date:</span>
                                    <span id="show-first-air-date" class="text-white">-</span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Last Air Date:</span>
                                    <span id="show-last-air-date" class="text-white">-</span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Episode Runtime:</span>
                                    <span id="show-runtime" class="text-white">-</span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Status:</span>
                                    <span id="show-status" class="text-white">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Similar Shows -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-semibold mb-4">You May Also Like</h3>
                        <div class="swiper-container similar-swiper">
                            <div class="swiper-wrapper" id="similar-wrapper">
                                <!-- Similar shows will be dynamically populated -->
                            </div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Section - Current Season & Reviews -->
                <div class="lg:col-span-1">


<!-- Replace the current trailer-section with this enhanced version -->
<div id="trailer-section" class="bg-dark rounded-xl overflow-hidden mb-8 border border-gray-800 hidden transform transition-all duration-300">
    <div class="relative pb-[56.25%] h-0 group">
        <iframe id="trailer-iframe" class="absolute top-0 left-0 w-full h-full" frameborder="0" allowfullscreen></iframe>
        <div class="absolute inset-0 bg-gradient-to-t from-dark to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
            <div class="w-full p-4 text-white">
                <h3 class="text-xl font-semibold truncate" id="trailer-title">Official Trailer</h3>
            </div>
        </div>
    </div>
    <div class="p-4 bg-gradient-to-t from-darker to-dark">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold" id="trailer-title-bottom">Official Trailer</h3>
                <p class="text-sm text-gray-400" id="trailer-release-date">Release date</p>
            </div>
            <div class="flex space-x-2">
                <button class="bg-gray-800 hover:bg-gray-700 p-2 rounded-full transition-colors duration-200" id="share-trailer">
                    <i class="ri-share-line text-primary"></i>
                </button>
                <button class="bg-gray-800 hover:bg-gray-700 p-2 rounded-full transition-colors duration-200" id="close-trailer">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</div>

                    <!-- Current Season -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-semibold mb-4">Latest Season</h3>
                        <div class="bg-dark rounded-xl overflow-hidden border border-gray-800" id="latest-season-card">
                            <!-- Latest season information will be populated here -->
                        </div>
                    </div>


<!-- Reviews Section -->
<div class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            <h3 class="text-2xl font-semibold mr-4">Reviews</h3>
            <span id="reviews-count" class="text-sm text-gray-400 bg-dark px-2 py-0.5 rounded-full">0</span>
        </div>
        
        <?php if(auth()->guard()->check()): ?>
        <button id="write-review-btn" class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded-lg flex items-center text-sm transition-colors">
            Write a Review <i class="ri-edit-line ml-1"></i>
        </button>
        <?php endif; ?>
    </div>

    <div class="space-y-4 max-h-[500px] overflow-y-auto custom-scrollbar pr-2" id="reviews-container">
        <!-- Loading State -->
        <div id="reviews-loading" class="flex items-center justify-center h-32 text-gray-500">
            <div class="animate-pulse flex space-x-4">
                <div class="rounded-full bg-gray-700 h-10 w-10"></div>
                <div class="flex-1 space-y-3 py-1">
                    <div class="h-3 bg-gray-700 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-700 rounded w-1/2"></div>
                </div>
            </div>
        </div>

        <!-- No Reviews State -->
        <div id="no-reviews" class="hidden flex items-center justify-center h-32 text-gray-500">
            <div class="text-center">
                <i class="ri-chat-3-line text-3xl mb-2 block"></i>
                <p>No reviews yet</p>
            </div>
        </div>
    </div>

    <!-- Review Form (Initially hidden) -->
    <?php if(auth()->guard()->check()): ?>
    <div id="review-form-container" class="hidden mt-4">
        <div class="bg-dark/50 rounded-lg p-4 border border-gray-700">
            <textarea 
                id="review-content" 
                class="w-full p-2 rounded bg-dark border border-gray-700 focus:ring-2 focus:ring-primary/50 transition-all" 
                placeholder="Write your review..." 
                rows="4"
                maxlength="1000"
            ></textarea>
            
            <div class="flex items-center justify-between mt-3">
                <!-- Star Rating Container -->
                <div id="star-rating" class="flex items-center space-x-1"></div>
                
                <div class="text-xs text-gray-400" id="char-count">0/1000</div>
            </div>
            
            <div class="flex justify-between mt-4">
                <button 
                    id="submit-review-btn" 
                    class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition-colors"
                >
                    Submit Review
                </button>
                <button 
                    id="cancel-review-btn" 
                    class="px-4 py-2 text-gray-400 hover:bg-dark/50 rounded-lg transition-colors"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Notification Container -->
<div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>


                    

                    <!-- Keywords -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-4">Keywords</h3>
                        <div class="flex flex-wrap gap-2" id="keywords-container">
                            <!-- Keywords will be dynamically populated -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-dark mt-16 py-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-400 text-sm">
                <p>© 2025 PELIXS. All rights reserved. Powered by TMDB API.</p>
            </div>
        </div>
    </footer>

    <script>
        const API_KEY = 'fcc76d42a33f4349db9ab3a42d7cc207'; 
        const BASE_URL = 'https://api.themoviedb.org/3';
        const IMAGE_BASE_URL = 'https://image.tmdb.org/t/p';

        const urlParams = new URLSearchParams(window.location.search);
        const mediaType = 'tv'; 
        const mediaId = window.location.pathname.split('/').pop(); 

        let showData = null;
        let seasonData = {};
        let castData = [];
        let reviewsData = [];
        let similarData = [];
        let keywordsData = [];

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

            fetchShowDetails();
        });

        async function fetchShowDetails() {
            try {
                const response = await fetch(`${BASE_URL}/${mediaType}/${mediaId}?api_key=${API_KEY}&append_to_response=credits,reviews,similar,keywords,content_ratings,external_ids`);
                showData = await response.json();

                castData = showData.credits?.cast || [];
                reviewsData = showData.reviews?.results || [];
                similarData = showData.similar?.results || [];
                keywordsData = showData.keywords?.results || [];

                updateUI();
                
                if (showData.seasons && showData.seasons.length > 0) {
                    await fetchSeasonDetails();
                }
            } catch (error) {
                console.error('Error fetching show details:', error);
                showErrorMessage();
            }
        }

        async function fetchSeasonDetails() {
            try {
                const promises = showData.seasons.map(async (season) => {
                    if (season.season_number === 0) return; 
                    
                    const response = await fetch(`${BASE_URL}/${mediaType}/${mediaId}/season/${season.season_number}?api_key=${API_KEY}`);
                    const data = await response.json();
                    seasonData[season.season_number] = data;
                });
                
                await Promise.all(promises);
                updateSeasons();
                updateLatestSeason();
            } catch (error) {
                console.error('Error fetching season details:', error);
            }
        }

        function updateUI() {
            document.title = `PELIXS - ${showData.name}`;

            updateBanner();
            updateShowDetails();
            updateCast();
            updateReviews();
            updateSimilar();
            updateKeywords();
        }

        function updateBanner() {
            const bannerElement = document.getElementById('show-banner');
            const backdropPath = showData.backdrop_path 
                ? `${IMAGE_BASE_URL}/original${showData.backdrop_path}`
                : '/api/placeholder/1920/800';

            bannerElement.innerHTML = `
                <img src="${backdropPath}" alt="${showData.name}" class="w-full h-full object-cover">
                <div class="absolute inset-0 banner-overlay"></div>
            `;
        }

        function updateShowDetails() {
            document.getElementById('show-title').textContent = showData.name;
            document.getElementById('show-overview').textContent = showData.overview || 'No overview available.';

            document.getElementById('show-rating').innerHTML = `
                <i class="ri-star-fill text-yellow-500 mr-1"></i>
                <span>${showData.vote_average ? showData.vote_average.toFixed(1) : 'N/A'}</span>
            `;

            const firstAirDate = showData.first_air_date;
            document.getElementById('show-year').textContent = firstAirDate ? new Date(firstAirDate).getFullYear() : 'N/A';

            document.getElementById('show-seasons').innerHTML = `
                <i class="ri-film-line mr-1"></i>
                <span>${showData.number_of_seasons || 0} season${showData.number_of_seasons !== 1 ? 's' : ''}</span>
            `;

            const genresContainer = document.getElementById('show-genres');
            genresContainer.innerHTML = '';

            if (showData.genres && showData.genres.length > 0) {
                showData.genres.forEach(genre => {
                    const genreElement = document.createElement('span');
                    genreElement.className = 'text-sm bg-gray-800 px-3 py-1.5 rounded-full';
                    genreElement.textContent = genre.name;
                    genresContainer.appendChild(genreElement);
                });
            } else {
                genresContainer.innerHTML = '<span class="text-sm text-gray-400">No genres available</span>';
            }

            document.getElementById('show-creator').textContent = showData.created_by && showData.created_by.length > 0 
                ? showData.created_by.map(creator => creator.name).join(', ') 
                : 'N/A';

            document.getElementById('show-network').textContent = showData.networks && showData.networks.length > 0 
                ? showData.networks.map(network => network.name).join(', ') 
                : 'N/A';

            const production = showData.production_companies && showData.production_companies.length > 0 
                ? showData.production_companies.slice(0, 2).map(company => company.name).join(', ') 
                : 'N/A';
            document.getElementById('show-production').textContent = production;

            const countries = showData.production_countries && showData.production_countries.length > 0 
                ? showData.production_countries.map(country => country.name).join(', ') 
                : 'N/A';
            document.getElementById('show-country').textContent = countries;

            document.getElementById('show-first-air-date').textContent = firstAirDate 
                ? new Date(firstAirDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) 
                : 'N/A';

            document.getElementById('show-last-air-date').textContent = showData.last_air_date 
                ? new Date(showData.last_air_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) 
                : 'N/A';

            document.getElementById('show-runtime').textContent = showData.episode_run_time && showData.episode_run_time.length > 0 
                ? `${showData.episode_run_time[0]} min` 
                : 'N/A';

            document.getElementById('show-status').textContent = showData.status || 'N/A';
        }

        function updateSeasons() {
            const seasonsContainer = document.getElementById('seasons-accordion');
            seasonsContainer.innerHTML = '';

            if (showData.seasons && showData.seasons.length > 0) {
                const sortedSeasons = [...showData.seasons].sort((a, b) => a.season_number - b.season_number);

                const filteredSeasons = sortedSeasons.filter(season => season.season_number > 0);

                filteredSeasons.forEach(season => {
                    const seasonElement = document.createElement('div');
                    seasonElement.className = 'bg-dark rounded-lg overflow-hidden border border-gray-800 mb-4';

                    const seasonPosterPath = season.poster_path 
                        ? `${IMAGE_BASE_URL}/w300${season.poster_path}` 
                        : '/api/placeholder/300/450';

                    seasonElement.innerHTML = `
                        <div class="accordion-header cursor-pointer p-4 flex items-center justify-between" data-season="${season.season_number}">
                            <div class="flex items-center">
                                <div class="w-16 h-24 rounded overflow-hidden mr-4">
                                    <img src="${seasonPosterPath}" alt="${season.name}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg">${season.name}</h4>
                                    <div class="text-sm text-gray-400">${season.air_date ? new Date(season.air_date).getFullYear() : 'N/A'} • ${season.episode_count} episodes</div>
                                </div>
                            </div>
                            <i class="ri-arrow-down-s-line text-2xl accordion-icon"></i>
                        </div>
                        <div class="accordion-content border-t border-gray-800">
                            <div class="p-4">
                                <div class="text-sm text-gray-300 mb-4">${season.overview || 'No overview available.'}</div>
                                <div class="episodes-list custom-scrollbar space-y-2">
                                    ${generateEpisodesList(season.season_number)}
                                </div>
                            </div>
                        </div>
                    `;

                    seasonsContainer.appendChild(seasonElement);
                });

                document.querySelectorAll('.accordion-header').forEach(header => {
                    header.addEventListener('click', function() {
                        const parent = this.parentElement;
                        parent.classList.toggle('accordion-open');
                    });
                });
            } else {
                seasonsContainer.innerHTML = '<div class="text-center text-gray-400">No seasons available</div>';
            }
        }

        // replacemet of can directives 


    const userCanDownload = <?php echo e(Auth::user() && Auth::user()->can('download-content') ? 'true' : 'false'); ?>;

    
    function generateEpisodesList(seasonNumber) {
    const season = seasonData[seasonNumber];
    if (!season || !season.episodes || season.episodes.length === 0) {
        return '<div class="text-gray-400">No episodes available</div>';
    }

    let episodesHTML = '';
    season.episodes.forEach(episode => {
        const episodeStillPath = episode.still_path 
            ? `${IMAGE_BASE_URL}/w300${episode.still_path}` 
            : '/api/placeholder/300/170';

        // HTML for episode info
        let episodeHTML = `
            <div class="episode-item p-2 rounded flex items-start hover:bg-gray-800/50 transition-colors duration-200">
                <div class="w-24 h-14 rounded overflow-hidden mr-3 flex-shrink-0">
                    <img src="${episodeStillPath}" alt="Episode ${episode.episode_number}" class="w-full h-full object-cover">
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Episode ${episode.episode_number}</span>
                        <span class="text-xs text-gray-400">${episode.runtime ? episode.runtime + ' min' : 'N/A'}</span>
                    </div>
                    <h5 class="font-medium text-sm">${episode.name}</h5>
                    <p class="text-xs text-gray-300 line-clamp-1">${episode.overview || 'No overview available'}</p>
                </div>`;
        
        // Only add download button if user has permission
        if (userCanDownload) {
            episodeHTML += `
                <div class="flex items-center">
                    <button class="bg-gray-800 hover:bg-gray-700 p-2 rounded-full text-sm download-btn shadow-sm transition-all duration-200" 
                            title="Download ${episode.name}"
                            onclick="fetchEpisodeDownloadLink('${showData.name}', ${seasonNumber}, ${episode.episode_number})">
                        <i class="ri-download-2-line text-amber-500"></i>
                    </button>
                </div>`;
        }
        
        episodeHTML += `</div>`;
        episodesHTML += episodeHTML;
    });

    return episodesHTML;
}

function fetchEpisodeDownloadLink(tvShowTitle, seasonNumber, episodeNumber) {

    const loadingToast = document.createElement('div');
    loadingToast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded shadow-lg z-50';
    loadingToast.textContent = 'Fetching download link...';
    document.body.appendChild(loadingToast);
    
    const url = `/api/episode-download?title=${encodeURIComponent(tvShowTitle)}&season=${seasonNumber}&episode=${episodeNumber}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            document.body.removeChild(loadingToast);
            
            if (data.download_link) {
                const successToast = document.createElement('div');
                successToast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 flex flex-col';
                
                const toastMessage = document.createElement('p');
                toastMessage.textContent = 'Download link ready!';
                successToast.appendChild(toastMessage);
                
                const downloadAnchor = document.createElement('a');
                downloadAnchor.href = data.download_link;
                downloadAnchor.className = 'text-white underline hover:text-amber-200 mt-1';
                downloadAnchor.textContent = 'Click here to download';
                downloadAnchor.target = '_blank';
                successToast.appendChild(downloadAnchor);
                
                document.body.appendChild(successToast);
                
                setTimeout(() => {
                    if (document.body.contains(successToast)) {
                        document.body.removeChild(successToast);
                    }
                }, 10000);
            } else {
                // Show error toast
                const errorToast = document.createElement('div');
                errorToast.className = 'fixed bottom-4 right-4 bg-red-600 text-white px-4 py-2 rounded shadow-lg z-50';
                errorToast.textContent = 'Download link not available for this episode.';
                document.body.appendChild(errorToast);
                
                setTimeout(() => {
                    if (document.body.contains(errorToast)) {
                        document.body.removeChild(errorToast);
                    }
                }, 5000);
            }
        })
        .catch(error => {
            if (document.body.contains(loadingToast)) {
                document.body.removeChild(loadingToast);
            }
            
            const errorToast = document.createElement('div');
            errorToast.className = 'fixed bottom-4 right-4 bg-red-600 text-white px-4 py-2 rounded shadow-lg z-50';
            errorToast.textContent = 'Error fetching download link.';
            document.body.appendChild(errorToast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (document.body.contains(errorToast)) {
                    document.body.removeChild(errorToast);
                }
            }, 5000);
            
            console.error('Error fetching download link:', error);
        });
}









function updateLatestSeason() {
const latestSeasonCard = document.getElementById('latest-season-card');

if (showData.seasons && showData.seasons.length > 0) {
    const regularSeasons = showData.seasons.filter(season => season.season_number > 0);
    const latestSeason = regularSeasons.sort((a, b) => 
        new Date(b.air_date || '1900-01-01') - new Date(a.air_date || '1900-01-01')
    )[0];

    if (latestSeason) {
        const seasonPosterPath = latestSeason.poster_path 
            ? `${IMAGE_BASE_URL}/w300${latestSeason.poster_path}` 
            : '/api/placeholder/300/450';

        latestSeasonCard.innerHTML = `
            <div class="relative">
                <img src="${seasonPosterPath}" alt="${latestSeason.name}" class="w-full h-auto">
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent">
                    <h4 class="font-semibold">${latestSeason.name}</h4>
                    <div class="text-sm text-gray-300">${latestSeason.episode_count} episodes</div>
                </div>
            </div>
            <div class="p-4">
                <div class="text-sm text-gray-300 mb-4">${latestSeason.overview || 'No overview available.'}</div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Air Date: ${latestSeason.air_date ? new Date(latestSeason.air_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</span>
                    <button class="text-primary hover:underline text-sm" data-season="${latestSeason.season_number}">
                        View All Episodes
                    </button>
                </div>
            </div>
        `;

        latestSeasonCard.querySelector('button[data-season]').addEventListener('click', function() {
            const seasonNumber = this.getAttribute('data-season');
            const seasonHeader = document.querySelector(`.accordion-header[data-season="${seasonNumber}"]`);
            if (seasonHeader) {
                seasonHeader.scrollIntoView({ behavior: 'smooth' });
                setTimeout(() => {
                    if (!seasonHeader.parentElement.classList.contains('accordion-open')) {
                        seasonHeader.click();
                    }
                }, 500);
            }
        });
    } else {
        latestSeasonCard.innerHTML = '<div class="p-4 text-center text-gray-400">No season information available</div>';
    }
} else {
    latestSeasonCard.innerHTML = '<div class="p-4 text-center text-gray-400">No seasons available</div>';
}
}

function updateCast() {
const castWrapper = document.getElementById('cast-wrapper');
castWrapper.innerHTML = '';

if (castData && castData.length > 0) {
    castData.slice(0, 20).forEach(person => {
        const profilePath = person.profile_path 
            ? `${IMAGE_BASE_URL}/w185${person.profile_path}` 
            : '/api/placeholder/185/278';

        const castElement = document.createElement('div');
        castElement.className = 'swiper-slide';
        castElement.innerHTML = `
            <div class="bg-dark rounded-lg overflow-hidden border border-gray-800">
                <div class="w-full h-40 overflow-hidden">
                    <img src="${profilePath}" alt="${person.name}" class="w-full h-full object-cover">
                </div>
                <div class="p-3">
                    <h4 class="font-medium text-sm truncate">${person.name}</h4>
                    <p class="text-xs text-gray-400 truncate">${person.character || 'Unknown Role'}</p>
                </div>
            </div>
        `;

        castWrapper.appendChild(castElement);
    });

    initSwiper('.cast-swiper', {
        slidesPerView: 2,
        spaceBetween: 16,
        navigation: {
            nextEl: '.cast-swiper .swiper-button-next',
            prevEl: '.cast-swiper .swiper-button-prev',
        },
        breakpoints: {
            640: { slidesPerView: 3 },
            768: { slidesPerView: 4 },
            1024: { slidesPerView: 5 },
        }
    });
} else {
    castWrapper.innerHTML = '<div class="w-full text-center text-gray-400 py-8">No cast information available</div>';
}
}

function updateSimilar() {
const similarWrapper = document.getElementById('similar-wrapper');
similarWrapper.innerHTML = '';

if (similarData && similarData.length > 0) {
    similarData.slice(0, 10).forEach(show => {
        const posterPath = show.poster_path 
            ? `${IMAGE_BASE_URL}/w300${show.poster_path}` 
            : '/api/placeholder/300/450';

        const similarElement = document.createElement('div');
        similarElement.className = 'swiper-slide';
        similarElement.innerHTML = `
            <div class="show-card relative rounded-lg overflow-hidden cursor-pointer">
                <img src="${posterPath}" alt="${show.name}" class="w-full h-auto card-image transition-all duration-300">
                <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-0 transition-opacity duration-300 p-4 flex flex-col justify-end">
                    <h4 class="font-semibold text-white">${show.name}</h4>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400 text-sm mr-2">${show.vote_average ? show.vote_average.toFixed(1) : 'N/A'}</span>
                        <span class="text-gray-300 text-xs">${show.first_air_date ? new Date(show.first_air_date).getFullYear() : 'N/A'}</span>
                    </div>
                    <div class="play-button absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 translate-y-4 scale-75 opacity-0 transition-all duration-300">
                        <div class="w-12 h-12 rounded-full bg-primary/80 flex items-center justify-center">
                            <i class="ri-play-fill text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        `;

        similarElement.addEventListener('click', () => {
            window.location.href = `/shows/${show.id}`;
        });

        similarWrapper.appendChild(similarElement);
    });

    initSwiper('.similar-swiper', {
        slidesPerView: 2,
        spaceBetween: 16,
        navigation: {
            nextEl: '.similar-swiper .swiper-button-next',
            prevEl: '.similar-swiper .swiper-button-prev',
        },
        breakpoints: {
            640: { slidesPerView: 3 },
            768: { slidesPerView: 4 },
            1024: { slidesPerView: 5 },
        }
    });
} else {
    similarWrapper.innerHTML = '<div class="w-full text-center text-gray-400 py-8">No similar shows available</div>';
}
}

function updateReviews() {
const reviewsContainer = document.getElementById('reviews-container');
reviewsContainer.innerHTML = '';

if (reviewsData && reviewsData.length > 0) {
    reviewsData.forEach(review => {
        const reviewElement = document.createElement('div');
        reviewElement.className = 'bg-dark rounded-lg p-4 border border-gray-800';

        const authorAvatar = review.author_details && review.author_details.avatar_path
            ? (review.author_details.avatar_path.startsWith('/http') 
                ? review.author_details.avatar_path.substring(1) 
                : `${IMAGE_BASE_URL}/w45${review.author_details.avatar_path}`)
            : '/api/placeholder/45/45';

        const authorRating = review.author_details && review.author_details.rating
            ? `<div class="flex items-center ml-2 bg-yellow-500/20 px-2 py-0.5 rounded">
                <i class="ri-star-fill text-yellow-500 text-xs mr-1"></i>
                <span class="text-xs">${review.author_details.rating}/10</span>
              </div>`
            : '';

        reviewElement.innerHTML = `
            <div class="flex items-center mb-3">
                <div class="w-8 h-8 rounded-full overflow-hidden mr-2">
                    <img src="${authorAvatar}" alt="${review.author}" class="w-full h-full object-cover">
                </div>
                <div class="flex items-center">
                    <span class="font-medium text-sm">${review.author}</span>
                    ${authorRating}
                </div>
            </div>
            <p class="text-sm text-gray-300 mb-2">${truncateText(review.content, 300)}</p>
            <div class="text-xs text-gray-400">${formatDate(review.created_at)}</div>
        `;

        reviewsContainer.appendChild(reviewElement);
    });
} else {
    reviewsContainer.innerHTML = `
        <div class="flex flex-col items-center justify-center h-48 text-gray-500">
            <i class="ri-chat-3-line text-3xl mb-2"></i>
            <p>No reviews yet</p>
            <button class="mt-4 px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-white text-sm">
                Be the first to review
            </button>
        </div>
    `;
}
}

function updateKeywords() {
const keywordsContainer = document.getElementById('keywords-container');
keywordsContainer.innerHTML = '';

if (keywordsData && keywordsData.length > 0) {
    keywordsData.forEach(keyword => {
        const keywordElement = document.createElement('span');
        keywordElement.className = 'text-xs bg-gray-800 px-2 py-1 rounded';
        keywordElement.textContent = keyword.name;
        keywordsContainer.appendChild(keywordElement);
    });
} else {
    keywordsContainer.innerHTML = '<span class="text-sm text-gray-400">No keywords available</span>';
}
}

function initSwiper(selector, options) {
return new Swiper(selector, options);
}

function truncateText(text, maxLength) {
if (!text) return 'No content available';
if (text.length <= maxLength) return text;
return text.substring(0, maxLength) + '...';
}

function formatDate(dateString) {
if (!dateString) return 'Unknown date';
const date = new Date(dateString);
return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

function showErrorMessage() {
const mainContent = document.getElementById('main-content');
mainContent.innerHTML = `
    <div class="container mx-auto px-4 py-16">
        <div class="bg-dark rounded-xl p-8 text-center">
            <i class="ri-error-warning-line text-6xl text-primary mb-4"></i>
            <h2 class="text-2xl font-bold mb-2">Oops! Something went wrong</h2>
            <p class="text-gray-300 mb-6">We couldn't load the TV show details. Please try again later.</p>
            <a href="/" class="bg-primary hover:bg-primary/90 px-6 py-3 rounded-lg font-medium inline-block">
                Go Home
            </a>
        </div>
    </div>
`;
}

document.getElementById('watch-now-btn').addEventListener('click', function() {
if (showData.seasons && showData.seasons.length > 0) {

    const firstRegularSeason = showData.seasons.find(season => season.season_number > 0);
    if (firstRegularSeason) {
        const seasonNumber = firstRegularSeason.season_number;
        const seasonHeader = document.querySelector(`.accordion-header[data-season="${seasonNumber}"]`);
        if (seasonHeader) {
            seasonHeader.scrollIntoView({ behavior: 'smooth' });
            setTimeout(() => {
                if (!seasonHeader.parentElement.classList.contains('accordion-open')) {
                    seasonHeader.click();
                }
            }, 500);
        }
    }
}
});



async function fetchTrailer() {
    try {
        const response = await fetch(`${BASE_URL}/${mediaType}/${mediaId}/videos?api_key=${API_KEY}`);
        const data = await response.json();
        
        if (data.results && data.results.length > 0) {
            let trailers = data.results.filter(video => 
                video.type.toLowerCase() === 'trailer' && video.site.toLowerCase() === 'youtube');
            
            if (trailers.length === 0) {
                trailers = data.results.filter(video => 
                    video.type.toLowerCase() === 'teaser' && video.site.toLowerCase() === 'youtube');
            }
            
            if (trailers.length === 0) {
                trailers = data.results.filter(video => 
                    video.site.toLowerCase() === 'youtube');
            }
            
            if (trailers.length > 0) {
                trailers.sort((a, b) => new Date(b.published_at) - new Date(a.published_at));
                
                const trailer = trailers[0];
                const trailerSection = document.getElementById('trailer-section');
                const trailerIframe = document.getElementById('trailer-iframe');
                const trailerTitle = document.getElementById('trailer-title');
                const trailerTitleBottom = document.getElementById('trailer-title-bottom');
                const trailerReleaseDate = document.getElementById('trailer-release-date');
                
                trailerIframe.src = `https://www.youtube.com/embed/${trailer.key}`;
                trailerTitle.textContent = trailer.name || 'Official Trailer';
                trailerTitleBottom.textContent = trailer.name || 'Official Trailer';
                
                if (trailer.published_at) {
                    const publishDate = new Date(trailer.published_at);
                    trailerReleaseDate.textContent = publishDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                } else {
                    trailerReleaseDate.textContent = 'Release date not available';
                }
                
                trailerSection.classList.remove('hidden');
                setTimeout(() => {
                    trailerSection.classList.add('scale-100');
                    trailerSection.classList.remove('scale-95', 'opacity-0');
                }, 100);
                
                const trailerBtn = document.getElementById('watch-trailer-btn');
                trailerBtn.classList.remove('hidden');
                
                trailerBtn.addEventListener('click', function() {
                    trailerSection.classList.remove('hidden');
                    trailerSection.scrollIntoView({ behavior: 'smooth' });
                    
                    trailerSection.classList.add('pulse-animation');
                    setTimeout(() => {
                        trailerSection.classList.remove('pulse-animation');
                    }, 1000);
                });
                
                document.getElementById('share-trailer').addEventListener('click', function() {
                    const trailerUrl = `https://www.youtube.com/watch?v=${trailer.key}`;
                    
                    if (navigator.share) {
                        navigator.share({
                            title: `${showData.name} - ${trailer.name}`,
                            text: `Check out this trailer for ${showData.name}!`,
                            url: trailerUrl
                        }).catch(console.error);
                    } else {
                        navigator.clipboard.writeText(trailerUrl).then(() => {
                            alert('Trailer link copied to clipboard!');
                        }).catch(console.error);
                    }
                });
                
                document.getElementById('close-trailer').addEventListener('click', function() {
                    trailerSection.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        trailerSection.classList.add('hidden');
                        trailerIframe.src = trailerIframe.src;
                    }, 300);
                });
                
                return true;
            }
        }
        return false;
    } catch (error) {
        console.error('Error fetching trailer:', error);
        return false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchToggle = document.getElementById('search-toggle');
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    
    searchToggle.addEventListener('click', function() {
        searchInput.classList.toggle('hidden');
        searchInput.classList.toggle('search-expanded');
        if (!searchInput.classList.contains('hidden')) {
            searchInput.focus();
        }
    });
    
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (searchInput.value.trim() !== '') {
            window.location.href = `/browse?search=${encodeURIComponent(searchInput.value.trim())}`;
        }
    });
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (searchInput.value.trim() !== '') {
                window.location.href = `/browse?search=${encodeURIComponent(searchInput.value.trim())}`;
            }
        }
    });
});

async function fetchShowDetails() {
    try {
        const response = await fetch(`${BASE_URL}/${mediaType}/${mediaId}?api_key=${API_KEY}&append_to_response=credits,reviews,similar,keywords,content_ratings,external_ids`);
        showData = await response.json();

        castData = showData.credits?.cast || [];
        reviewsData = showData.reviews?.results || [];
        similarData = showData.similar?.results || [];
        keywordsData = showData.keywords?.results || [];

        updateUI();
        
        const hasTrailer = await fetchTrailer();
        
        if (!hasTrailer) {
            const trailerBtn = document.getElementById('watch-trailer-btn');
            trailerBtn.classList.add('hidden');
        }
        
        if (showData.seasons && showData.seasons.length > 0) {
            await fetchSeasonDetails();
        }
    } catch (error) {
        console.error('Error fetching show details:', error);
        showErrorMessage();
    }
}

function toggleTvShow(tvShowId) {
    console.log(`Attempting to toggle TV show: ${tvShowId}`);

    fetch(`/tv-shows/${tvShowId}/toggle-list`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Content-Type": "application/json",
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        console.log("Server Response:", data);

        let button = document.getElementById(`tv-show-${tvShowId}-button`);
        if (button) {
            if (data.inList) {
                button.innerHTML = `
                    <i class="ri-close-line mr-1"></i>
                    Remove from My List
                `;
                button.classList.remove('bg-primary/10', 'text-primary', 'border-primary/30', 'hover:bg-primary/20');
                button.classList.add('bg-red-500/10', 'text-red-500', 'border-red-500/30', 'hover:bg-red-500/20');
            } else {
                button.innerHTML = `
                    <i class="ri-add-line mr-1"></i>
                    Add to My List
                `;
                button.classList.remove('bg-red-500/10', 'text-red-500', 'border-red-500/30', 'hover:bg-red-500/20');
                button.classList.add('bg-primary/10', 'text-primary', 'border-primary/30', 'hover:bg-primary/20');
            }
        }
    })
    .catch(error => {
        console.error("Error:", error);

        let button = document.getElementById(`tv-show-${tvShowId}-button`);
        if (button) {
            button.classList.add('bg-red-500/20', 'text-red-600', 'border-red-500/50');
            button.innerHTML = `
                <i class="ri-error-warning-line mr-1"></i>
                Error. Try again.
            `;
        }
    });
}



/* ----- review js ------ */

document.addEventListener("DOMContentLoaded", function() {
    // -------------------- Helper Functions --------------------
    function getTvShowIdFromPage() {
        return window.location.pathname.split('/')[2];
    }
    
    function getUserId() {
        return <?php echo json_encode(auth()->user()->id, 15, 512) ?>;
    }
    
    function truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substr(0, maxLength) + '...';
    }
    
    function createStarRating(rating) {
        const fullStars = Math.floor(rating);
        const emptyStars = 10 - fullStars;
        let starsHTML = rating > 0 ? `
            <div class="flex items-center ml-2 bg-yellow-500/20 px-2 py-0.5 rounded">
                <i class="ri-star-fill text-yellow-500 text-xs mr-1"></i>
                <span class="text-xs">${rating}/10</span>
            </div>
        ` : '';
        return starsHTML;
    }
    
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    }
    
    // -------------------- Notification Function --------------------
    function showNotification(message, type = 'info') {
        const notificationContainer = document.getElementById('notification-container') || (() => {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed top-4 right-4 z-50';
            document.body.appendChild(container);
            return container;
        })();

        const notification = document.createElement('div');
        notification.className = `
            px-4 py-2 rounded-lg mb-2 transition-all duration-300 
            ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}
            animate-slide-in-right
        `;
        notification.textContent = message;

        notificationContainer.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('animate-slide-out-right');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // -------------------- Reviews Fetching & Rendering --------------------
    async function fetchAllTvShowReviews() {
        const tvShowId = getTvShowIdFromPage();
        const reviewsContainer = document.getElementById('reviews-container');
        const loadingState = document.getElementById('reviews-loading');
        const noReviewsState = document.getElementById('no-reviews');

        if (loadingState) loadingState.classList.remove('hidden');
        if (noReviewsState) noReviewsState.classList.add('hidden');

        try {
            const localResponse = await fetch(`/tv-shows/${tvShowId}/comments`);
            if (!localResponse.ok) {
                throw new Error(`Local reviews fetch error: ${localResponse.status}`);
            }
            const localReviews = await localResponse.json();
            
            const tmdbResponse = await fetch(`/tv-shows/${tvShowId}/tmdb-reviews`);
            if (!tmdbResponse.ok) {
                throw new Error(`TMDB reviews fetch error: ${tmdbResponse.status}`);
            }
            const tmdbReviews = await tmdbResponse.json();
            
            const allReviews = [...localReviews, ...tmdbReviews].sort((a, b) => {
                return new Date(b.created_at) - new Date(a.created_at);
            });
            
            updateTvShowReviews(allReviews);
        } catch (error) {
            console.error("Error fetching TV show reviews:", error);
            showNotification("Failed to load TV show reviews", 'error');
            
            if (loadingState) loadingState.classList.add('hidden');
            if (noReviewsState) {
                noReviewsState.textContent = 'Failed to load reviews';
                noReviewsState.classList.remove('hidden');
            }
        }
    }
    
    function updateTvShowReviews(reviewsData = []) {
        const reviewsContainer = document.getElementById('reviews-container');
        const reviewsCount = document.getElementById('reviews-count');
        const loadingState = document.getElementById('reviews-loading');
        const noReviewsState = document.getElementById('no-reviews');

        reviewsContainer.innerHTML = '';
        
        if (reviewsData && reviewsData.length > 0) {
            if (reviewsCount) {
                reviewsCount.textContent = reviewsData.length;
            }

            reviewsData.forEach(review => {
                const reviewElement = document.createElement('div');
                reviewElement.className = 'bg-dark rounded-lg p-4 border border-gray-800 mb-4 relative';
                reviewElement.setAttribute('data-comment-id', review.id);

                const authorAvatar = review.user && review.user.avatar 
                    ? review.user.avatar 
                    : (review.author_details && review.author_details.avatar_path
                        ? (review.author_details.avatar_path.startsWith('/http') 
                            ? review.author_details.avatar_path.substring(1) 
                            : `/api/placeholder/45/45`)
                        : '/api/placeholder/45/45');

                const authorName = review.user 
                    ? review.user.name 
                    : (review.author || 'Anonymous');

                const rating = review.rating || (review.author_details && review.author_details.rating ? review.author_details.rating : 0);

                const currentUserId = <?php echo json_encode(auth()->check() ? auth()->id() : null, 15, 512) ?>;
                const canDelete = currentUserId && 
                    (review.user_id === currentUserId || 
                     (review.user && review.user.id === currentUserId));

                const deleteButtonHTML = canDelete ? `
                    <button class="delete-comment-btn absolute top-2 right-2 text-red-500 hover:text-red-700 transition-colors">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                ` : '';

                reviewElement.innerHTML = `
                    ${deleteButtonHTML}
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 rounded-full overflow-hidden mr-2">
                            <img src="${authorAvatar}" alt="${authorName}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex items-center">
                            <span class="font-medium text-sm">${authorName}</span>
                            ${createStarRating(rating)}
                        </div>
                    </div>
                    <p class="text-sm text-gray-300 mb-2 break-words">${truncateText(review.content, 300)}</p>
                    <div class="text-xs text-gray-400">${formatDate(review.created_at)}</div>
                `;

                reviewsContainer.appendChild(reviewElement);

                if (canDelete) {
                    const deleteBtn = reviewElement.querySelector('.delete-comment-btn');
                    deleteBtn.addEventListener('click', () => {
                        const confirmDelete = confirm('Are you sure you want to delete this comment?');
                        if (confirmDelete) {
                            deleteComment(review.id);
                        }
                    });
                }
            });

            if (loadingState) loadingState.classList.add('hidden');
            if (noReviewsState) noReviewsState.classList.add('hidden');
        } else {
            if (loadingState) loadingState.classList.add('hidden');
            
            reviewsContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center h-48 text-gray-500">
                    <i class="ri-chat-3-line text-3xl mb-2"></i>
                    <p>No reviews yet</p>
                    <button id="write-review-btn" class="mt-4 px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-white text-sm">
                        Be the first to review
                    </button>
                </div>
            `;
            
            if (noReviewsState) noReviewsState.classList.remove('hidden');
            setupTvShowReviewForm();
        }
    }
    
    // -------------------- Review Form Setup --------------------
    function setupTvShowReviewForm() {
        const writeReviewBtn = document.getElementById('write-review-btn');
        if (writeReviewBtn) {
            writeReviewBtn.addEventListener('click', () => {
                document.getElementById('review-form-container').classList.remove('hidden');
                writeReviewBtn.classList.add('hidden');
            });
        }
    }
    
    // -------------------- Review Form Initialization --------------------
    (function setupInitialTvShowReviewForm() {
        const writeReviewBtn = document.getElementById('write-review-btn');
        if (writeReviewBtn) {
            writeReviewBtn.addEventListener('click', () => {
                document.getElementById('review-form-container').classList.remove('hidden');
                writeReviewBtn.classList.add('hidden');
            });
        }
    
        const cancelReviewBtn = document.getElementById('cancel-review-btn');
        if (cancelReviewBtn) {
            cancelReviewBtn.addEventListener('click', () => {
                document.getElementById('review-form-container').classList.add('hidden');
                if (writeReviewBtn) {
                    writeReviewBtn.classList.remove('hidden');
                }
                document.getElementById('review-content').value = '';
                selectedRating = 0;
                resetStars();
            });
        }
    })();
    
    // -------------------- Star Rating Setup --------------------
    const starContainer = document.getElementById('star-rating');
    let selectedRating = 0;
    if (starContainer) {
        for (let i = 1; i <= 10; i++) {
            let star = document.createElement("i");
            star.className = "ri-star-line text-gray-400 text-xl cursor-pointer";
            star.dataset.value = i;
            starContainer.appendChild(star);
    
            star.addEventListener("mouseover", function () {
                highlightStars(i);
            });
    
            star.addEventListener("click", function () {
                setRating(i);
            });
    
            star.addEventListener("mouseleave", function () {
                resetStars();
            });
        }
    }
    
    function highlightStars(index) {
        const stars = document.querySelectorAll("#star-rating i");
        stars.forEach((star, idx) => {
            star.className = idx < index ? "ri-star-fill text-yellow-500 text-xl cursor-pointer" : "ri-star-line text-gray-400 text-xl cursor-pointer";
        });
    }
    
    function setRating(index) {
        selectedRating = index;
        highlightStars(index);
    }
    
    function resetStars() {
        if (selectedRating === 0) {
            highlightStars(0);
        } else {
            highlightStars(selectedRating);
        }
    }
    
    // -------------------- TV Show Review Submission --------------------
    const submitReviewBtn = document.getElementById('submit-review-btn');
    if (submitReviewBtn) {
        submitReviewBtn.addEventListener('click', async () => {
            const reviewContent = document.getElementById('review-content');
            const content = reviewContent.value.trim();
            
            if (content === "") {
                showNotification("Please write a review before submitting.", 'error');
                return;
            }
            
            if (selectedRating === 0) {
                showNotification("Please select a rating.", 'error');
                return;
            }
    
            try {
                const tvShowId = getTvShowIdFromPage();
                const response = await fetch(`/tv-shows/${tvShowId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        content: content,
                        rating: selectedRating
                    })
                });
    
                if (!response.ok) {
                    const errorData = await response.json();
                    showNotification(errorData.error || 'Failed to submit review', 'error');
                    return;
                }
    
                const data = await response.json();
                
                reviewContent.value = '';
                document.getElementById('review-form-container').classList.add('hidden');
                const writeReviewBtn = document.getElementById('write-review-btn');
                if (writeReviewBtn) {
                    writeReviewBtn.classList.remove('hidden');
                }
                selectedRating = 0;
                resetStars();
                
                showNotification('Review submitted successfully', 'success');
                
                fetchAllTvShowReviews();
            } catch (error) {
                console.error('Error submitting review:', error);
                showNotification('An error occurred while submitting the review', 'error');
            }
        });
    }

    // -------------------- Delete Comment Function --------------------
    async function deleteComment(commentId) {
        try {
            const response = await fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Failed to delete comment');
            }

            showNotification('Comment deleted successfully', 'success');
            
            fetchAllTvShowReviews();
        } catch (error) {
            console.error('Error deleting comment:', error);
            showNotification(error.message, 'error');
        }
    }
    
    // -------------------- Character Count for Review Textarea --------------------
    const reviewContent = document.getElementById('review-content');
    const charCount = document.getElementById('char-count');
    
    if (reviewContent && charCount) {
        reviewContent.addEventListener('input', function() {
            charCount.textContent = `${this.value.length}/1000`;
            
            if (this.value.length > 900) {
                charCount.classList.add('text-red-500');
            } else {
                charCount.classList.remove('text-red-500');
            }
        });
    }
    
    // -------------------- Load All TV Show Reviews on Page Load --------------------
    fetchAllTvShowReviews();
    
    // -------------------- Expose Helper Functions if Needed --------------------
    window.getTvShowIdFromPage = getTvShowIdFromPage;
    window.getUserId = getUserId;
});


</script>
</body>
</html><?php /**PATH C:\Users\Youcode\Herd\file-rouge\resources\views/front-office/informations.blade.php ENDPATH**/ ?>