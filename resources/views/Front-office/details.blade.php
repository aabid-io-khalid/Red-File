<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.js"></script>
    <title>PELIXS - {{ $movieData['title'] ?? 'Movie Details' }}</title>
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
        .movie-card:hover .card-overlay { opacity: 1; }
        .movie-card:hover .play-button { transform: translateY(0) scale(1); }
        .movie-card:hover .card-image { transform: scale(1.05); filter: brightness(0.7); }
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
        .search-expanded { width: 500px; }
        .banner-overlay { background: linear-gradient(to bottom, rgba(11, 11, 11, 0) 0%, rgba(11, 11, 11, 0.8) 60%, rgba(11, 11, 11, 1) 100%); }
        .trailer-button:hover .play-icon { transform: scale(1.2); }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(229, 9, 20, 0.5); }
        .swiper-wrapper::-webkit-scrollbar { display: none; }
        .swiper-wrapper { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-darker text-white font-sans">
    <!-- Header -->
    <header class="bg-dark py-4 px-6 shadow-lg fixed top-0 w-full z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-primary tracking-wider">PELIXS</h1>
            <!-- Navigation -->
            <nav class="hidden md:flex space-x-6">
                <a href="/home" class="hover:text-primary transition">Home</a>
                <a href="/browse" class="hover:text-primary transition">Browse</a>
                <a href="/movies" class="hover:text-primary transition">Movies</a>
                <a href="/shows" class="hover:text-primary transition">TV Shows</a>
                <a href="/anime" class="hover:text-primary transition">Anime</a>
                @auth
                    @can('access-community-chat')
                        <a href="{{ url('/community') }}" class="hover:text-primary transition">Community</a>
                        <a href="/mylist" class="hover:text-primary transition">My List</a>
                    @endcan
                    <a href="{{ url('/subscription') }}" class="hover:text-primary transition">Subscription</a>
                @else
                    <a href="{{ url('/login') }}" class="hover:text-primary transition">Community</a>
                @endauth
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
                @guest
                    <a href="{{ url('/login') }}"
                       class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary/90 transition flex items-center">
                        <i class="ri-login-box-line mr-2"></i> Log In
                    </a>
                @else
                    <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition flex items-center">
                            <i class="ri-logout-box-r-line mr-2"></i> Log Out
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </header>


    <main class="pt-16" id="main-content">
        <!-- Movie Banner -->
        <div class="relative h-[70vh] w-full" id="movie-banner">
            <img src="{{ $movieData['backdrop_path'] ? 'https://image.tmdb.org/t/p/original' . $movieData['backdrop_path'] : '/api/placeholder/1920/800' }}" 
                 alt="{{ $movieData['title'] }}" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 banner-overlay"></div>
        </div>

        <!-- Movie Details -->
        <div class="container mx-auto px-4 py-6 -mt-16 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Section - Movie Info -->
                <div class="lg:col-span-2">
                    <!-- Movie Stats -->
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        <div id="movie-rating" class="rating-pill text-sm px-3 py-1.5 rounded-full flex items-center">
                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                            <span>{{ $movieData['vote_average'] ? number_format($movieData['vote_average'], 1) : 'N/A' }}</span>
                        </div>
                        <div id="movie-year" class="genre-pill text-sm px-3 py-1.5 rounded-full">
                            {{ $movieData['release_date'] ? date('Y', strtotime($movieData['release_date'])) : 'N/A' }}
                        </div>
                        <div id="movie-runtime" class="genre-pill text-sm px-3 py-1.5 rounded-full flex items-center">
                            <i class="ri-time-line mr-1"></i>
                            <span>{{ $movieData['runtime'] ? $movieData['runtime'] . ' min' : 'N/A' }}</span>
                        </div>
                        <div id="movie-quality" class="bg-primary text-white text-sm px-3 py-1.5 rounded-full">HD</div>
                    </div>

                    <!-- Movie Description -->
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4" id="movie-title">{{ $movieData['title'] }}</h2>
                        <p class="text-gray-300 mb-4" id="movie-overview">{{ $movieData['overview'] ?: 'No overview available.' }}</p>
                        
                        <!-- Genres -->
                        <div class="flex flex-wrap gap-2 mb-6" id="movie-genres">
                            @forelse ($movieData['genres'] as $genre)
                                <span class="text-sm bg-gray-800 px-3 py-1.5 rounded-full">{{ $genre['name'] }}</span>
                            @empty
                                <span class="text-sm text-gray-400">No genres available</span>
                            @endforelse
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-4">
                            <button class="bg-primary hover:bg-primary/90 px-6 py-3 rounded-lg font-medium flex items-center trailer-button" id="watch-trailer-btn">
                                <i class="ri-play-fill mr-2 text-xl play-icon transition-transform duration-300"></i> Watch Trailer
                            </button>
                            @can('add-to-my-list')
                                <button 
                                    id="movie-{{ $movieData['id'] }}-button"
                                    onclick="toggleMyList({{ $movieData['id'] }})"
                                    class="bg-primary/10 text-primary border border-primary/30 hover:bg-primary/20 px-4 py-2 rounded-lg font-medium transition-all duration-300 ease-in-out flex items-center justify-center space-x-2 hover:scale-105 active:scale-95">
                                    <i class="ri-{{ $isInList ? 'close-line' : 'add-line' }} mr-1"></i>
                                    {{ $isInList ? 'Remove from My List' : 'Add to My List' }}
                                </button>
                            @endcan
                            <button class="bg-gray-800 hover:bg-gray-700 px-6 py-3 rounded-lg font-medium flex items-center">
                                <i class="ri-share-line mr-2"></i> Share
                            </button>
                        </div>
                    </div>

                    <!-- Download Link Section -->
                    <div id="download-section" class="mt-4">
                        @can('download-content')
                            <button id="fetch-download-btn" class="bg-yellow-500 hover:bg-yellow-400 px-6 py-3 rounded-lg font-medium flex items-center">
                                <i class="ri-download-2-line mr-2 text-xl"></i> Get Download Link
                            </button>
                            <div id="download-link-container" class="mt-2 hidden">
                                <p id="loading-message" class="text-gray-300">Fetching download link...</p>
                                <a id="download-link" href="#" target="_blank" class="hidden bg-yellow-500 hover:bg-yellow-400 px-2 py-3 rounded-lg font-medium flex items-center">
                                    <i class="ri-download-2-line mr-2 text-xl"></i> Download Movie
                                </a>
                            </div>
                        @else
                            <p class="text-red-500">You need a premium subscription to download content.</p>
                        @endcan
                    </div>

                    <!-- Cast & Crew -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-semibold mb-4">Cast & Crew</h3>
                        <div class="swiper-container cast-swiper">
                            <div class="swiper-wrapper" id="cast-wrapper">
                                @forelse ($movieData['credits']['cast'] ?? [] as $person)
                                    <div class="swiper-slide">
                                        <div class="text-center">
                                            <div class="w-24 h-24 mx-auto rounded-full overflow-hidden mb-3 border-2 border-gray-700">
                                                <img src="{{ $person['profile_path'] ? 'https://image.tmdb.org/t/p/w185' . $person['profile_path'] : '/api/placeholder/185/278' }}" 
                                                     alt="{{ $person['name'] }}" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                            <h4 class="font-medium text-sm">{{ $person['name'] }}</h4>
                                            <p class="text-xs text-gray-400">{{ $person['character'] }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-gray-400">No cast information available</div>
                                @endforelse
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
                                    <span class="text-gray-400 w-32">Director:</span>
                                    <span id="movie-director">
                                        {{ $movieData['credits']['crew'] ? collect($movieData['credits']['crew'])->firstWhere('job', 'Director')['name'] ?? 'N/A' : 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Writer:</span>
                                    <span id="movie-writer">
                                        {{ $movieData['credits']['crew'] ? collect($movieData['credits']['crew'])->filter(fn($person) => in_array($person['job'], ['Writer', 'Screenplay']))->pluck('name')->take(2)->implode(', ') ?: 'N/A' : 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Production:</span>
                                    <span id="movie-production">
                                        {{ $movieData['production_companies'] ? collect($movieData['production_companies'])->take(2)->pluck('name')->implode(', ') ?: 'N/A' : 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Country:</span>
                                    <span id="movie-country">
                                        {{ $movieData['production_countries'] ? collect($movieData['production_countries'])->pluck('name')->implode(', ') ?: 'N/A' : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Release Information -->
                        <div>
                            <h3 class="text-xl font-semibold mb-4">Release Info</h3>
                            <div class="space-y-3">
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Release Date:</span>
                                    <span id="movie-release-date">
                                        {{ $movieData['release_date'] ? date('F j, Y', strtotime($movieData['release_date'])) : 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Budget:</span>
                                    <span id="movie-budget">
                                        {{ $movieData['budget'] ? '$' . number_format($movieData['budget'] / 1000000, 1) . ' million' : 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Revenue:</span>
                                    <span id="movie-revenue">
                                        {{ $movieData['revenue'] ? '$' . number_format($movieData['revenue'] / 1000000, 1) . ' million' : 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap">
                                    <span class="text-gray-400 w-32">Status:</span>
                                    <span id="movie-status">{{ $movieData['status'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Similar Movies -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-semibold mb-4">You May Also Like</h3>
                        <div class="swiper-container similar-swiper">
                            <div class="swiper-wrapper" id="similar-wrapper">
                                @forelse ($movieData['similar']['results'] ?? [] as $item)
                                    <div class="swiper-slide">
                                        <div class="movie-card relative overflow-hidden rounded-lg">
                                            <img src="{{ $item['poster_path'] ? 'https://image.tmdb.org/t/p/w342' . $item['poster_path'] : '/api/placeholder/342/513' }}" 
                                                 alt="{{ $item['title'] }}" 
                                                 class="w-full h-auto object-cover transition-transform duration-300 card-image">
                                            <div class="absolute inset-0 card-overlay flex items-center justify-center opacity-0 transition-opacity duration-300">
                                                <a href="/movie/{{ $item['id'] }}" class="bg-primary text-white px-4 py-2 rounded-lg flex items-center">
                                                    <i class="ri-play-fill mr-2"></i> Watch
                                                </a>
                                            </div>
                                        </div>
                                        <h4 class="mt-2 text-sm font-medium">{{ $item['title'] }}</h4>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-gray-400">No similar titles available</div>
                                @endforelse
                            </div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Section - Trailer & Reviews -->
                <div class="lg:col-span-1">
                    <!-- Trailer -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-semibold mb-4">Official Trailer</h3>
                        <div class="relative aspect-video bg-dark rounded-xl overflow-hidden" id="trailer-container">
                            @php
                                $trailer = collect($movieData['videos']['results'] ?? [])->firstWhere('type', 'Trailer') 
                                        ?? collect($movieData['videos']['results'] ?? [])->firstWhere('type', 'Teaser');
                            @endphp
                            @if ($trailer && $trailer['site'] === 'YouTube')
                                <iframe 
                                    width="100%" 
                                    height="100%" 
                                    src="https://www.youtube.com/embed/{{ $trailer['key'] }}" 
                                    title="{{ $trailer['name'] }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            @else
                                <div class="absolute inset-0 flex items-center justify-center bg-dark/50" id="trailer-placeholder">
                                    <div class="text-center">
                                        <i class="ri-film-line text-6xl text-primary/50 mb-2"></i>
                                        <p class="text-gray-400">No trailer available</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center">
                                <h3 class="text-2xl font-semibold mr-4">Movie Reviews</h3>
                                <span id="reviews-count" class="text-sm text-gray-400 bg-dark px-2 py-0.5 rounded-full">0</span>
                            </div>
                            @auth
                                <button id="write-review-btn" class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded-lg flex items-center text-sm transition-colors">
                                    Write a Movie Review <i class="ri-edit-line ml-1"></i>
                                </button>
                            @endauth
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
                                    <p>No movie reviews yet</p>
                                </div>
                            </div>
                        </div>

                        <!-- Review Form -->
                        @auth
                            <div id="review-form-container" class="hidden mt-4">
                                <div class="bg-dark/50 rounded-lg p-4 border border-gray-700">
                                    <textarea 
                                        id="review-content" 
                                        class="w-full p-2 rounded bg-dark border border-gray-700 focus:ring-2 focus:ring-primary/50 transition-all" 
                                        placeholder="Write your movie review..." 
                                        rows="4"
                                        maxlength="1000">
                                    </textarea>
                                    <div class="flex items-center justify-between mt-3">
                                        <div id="star-rating" class="flex items-center space-x-1"></div>
                                        <div class="text-xs text-gray-400" id="char-count">0/1000</div>
                                    </div>
                                    <div class="flex justify-between mt-4">
                                        <button 
                                            id="submit-review-btn" 
                                            class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition-colors">
                                            Submit Review
                                        </button>
                                        <button 
                                            id="cancel-review-btn" 
                                            class="px-4 py-2 text-gray-400 hover:bg-dark/50 rounded-lg transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>

                    <!-- Keywords -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-4">Keywords</h3>
                        <div class="flex flex-wrap gap-2" id="keywords-container">
                            @forelse ($movieData['keywords']['keywords'] ?? [] as $keyword)
                                <span class="text-sm bg-gray-800 px-3 py-1.5 rounded-full">{{ $keyword['name'] }}</span>
                            @empty
                                <span class="text-sm text-gray-400">No keywords available</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Trailer Modal -->
    <div id="trailer-modal" class="fixed inset-0 bg-black/90 z-[100] hidden flex items-center justify-center p-4">
        <div class="w-full max-w-4xl relative">
            <button id="close-trailer" class="absolute -top-12 right-0 text-white text-xl hover:text-primary">
                <i class="ri-close-line"></i> Close
            </button>
            <div class="aspect-video bg-black rounded-lg overflow-hidden" id="modal-trailer-container">
                <!-- Trailer iframe will be inserted here -->
            </div>
        </div>
    </div>

    <footer class="bg-dark mt-16 py-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-400 text-sm">
                <p>© 2025 PELIXS. All rights reserved. Powered by TMDB API.</p>
            </div>
        </div>
    </footer>

    <script>
        const mediaData = @json($movieData);
        const isInList = @json($isInList);
        const IMAGE_BASE_URL = 'https://image.tmdb.org/t/p';

        const castData = mediaData.credits?.cast || [];
        const reviewsData = mediaData.reviews?.results || [];
        const similarData = mediaData.similar?.results || [];
        const videosData = mediaData.videos?.results || [];
        const keywordsData = mediaData.keywords?.keywords || [];

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

            const trailerModal = document.getElementById('trailer-modal');
            const watchTrailerBtn = document.getElementById('watch-trailer-btn');
            const closeTrailerBtn = document.getElementById('close-trailer');

            watchTrailerBtn.addEventListener('click', function() {
                openTrailerModal();
            });

            closeTrailerBtn.addEventListener('click', function() {
                closeTrailerModal();
            });

            trailerModal.addEventListener('click', function(e) {
                if (e.target === trailerModal) {
                    closeTrailerModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !trailerModal.classList.contains('hidden')) {
                    closeTrailerModal();
                }
            });

            updateCast();
            updateSimilar();
            fetchAllMovieReviews(); 
        });

        function updateCast() {
            if (castData.length > 0) {
                new Swiper('.cast-swiper', {
                    slidesPerView: 3,
                    spaceBetween: 16,
                    grabCursor: true,
                    navigation: {
                        nextEl: '.cast-swiper .swiper-button-next',
                        prevEl: '.cast-swiper .swiper-button-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 4 },
                        768: { slidesPerView: 5 },
                        1024: { slidesPerView: 6 }
                    }
                });
            }
        }

        function updateSimilar() {
            if (similarData.length > 0) {
                new Swiper('.similar-swiper', {
                    slidesPerView: 2,
                    spaceBetween: 16,
                    grabCursor: true,
                    navigation: {
                        nextEl: '.similar-swiper .swiper-button-next',
                        prevEl: '.similar-swiper .swiper-button-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 3 },
                        768: { slidesPerView: 4 },
                        1024: { slidesPerView: 5 }
                    }
                });
            }
        }

        function openTrailerModal() {
            const trailer = videosData.find(video => 
                video.type === 'Trailer' && video.site === 'YouTube'
            ) || videosData.find(video => 
                video.type === 'Teaser' && video.site === 'YouTube'
            );
            
            const modalTrailerContainer = document.getElementById('modal-trailer-container');
            if (trailer) {
                modalTrailerContainer.innerHTML = `
                    <iframe 
                        width="100%" 
                        height="100%" 
                        src="https://www.youtube.com/embed/${trailer.key}" 
                        title="${trailer.name}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                `;
                document.getElementById('trailer-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeTrailerModal() {
            document.getElementById('trailer-modal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('modal-trailer-container').innerHTML = '';
        }

        document.getElementById('fetch-download-btn')?.addEventListener('click', function() {
            const movieTitle = "{{ $movieData['title'] }}";
            const loadingMessage = document.getElementById('loading-message');
            const downloadLinkContainer = document.getElementById('download-link-container');
            const downloadLink = document.getElementById('download-link');

            loadingMessage.classList.remove('hidden');
            downloadLink.classList.add('hidden');

            fetch(`/fetch-download-link?title=${encodeURIComponent(movieTitle)}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.download_link) {
                        downloadLink.href = data.download_link;
                        downloadLink.classList.remove('hidden');
                        downloadLinkContainer.classList.remove('hidden');
                        loadingMessage.classList.add('hidden');
                    } else {
                        loadingMessage.textContent = 'Download link not available.';
                    }
                })
                .catch(error => {
                    console.error('Error fetching download link:', error);
                    loadingMessage.textContent = 'Error fetching download link.';
                });
        });

        function toggleMyList(movieId) {
            const button = document.querySelector(`#movie-${movieId}-button`);
            fetch(`/movies/${movieId}/toggle-list`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ movie_id: movieId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message.includes('added')) {
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
                })
                .catch(error => {
                    console.error('Error:', error);
                    button.classList.add('bg-red-500/20', 'text-red-600', 'border-red-500/50');
                    button.innerHTML = `
                        <i class="ri-error-warning-line mr-1"></i>
                        Error. Try again.
                    `;
                });
        }

        document.addEventListener("DOMContentLoaded", function() {
            function getMovieIdFromPage() {
                return window.location.pathname.split('/')[2];
            }
            
            function getUserId() {
                return @json(auth()->user()->id ?? null);
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

            async function fetchAllMovieReviews() {
                const movieId = getMovieIdFromPage();
                const reviewsContainer = document.getElementById('reviews-container');
                const loadingState = document.getElementById('reviews-loading');
                const noReviewsState = document.getElementById('no-reviews');

                if (loadingState) loadingState.classList.remove('hidden');
                if (noReviewsState) noReviewsState.classList.add('hidden');

                try {
                    const localResponse = await fetch(`/movies/${movieId}/comments`);
                    if (!localResponse.ok) {
                        throw new Error(`Local reviews fetch error: ${localResponse.status}`);
                    }
                    const localReviews = await localResponse.json();
                    
                    const tmdbResponse = await fetch(`/movies/${movieId}/tmdb-reviews`);
                    if (!tmdbResponse.ok) {
                        throw new Error(`TMDB reviews fetch error: ${tmdbResponse.status}`);
                    }
                    const tmdbReviews = await tmdbResponse.json();
                    
                    const allReviews = [...localReviews, ...tmdbReviews].sort((a, b) => {
                        return new Date(b.created_at) - new Date(a.created_at);
                    });
                    
                    updateMovieReviews(allReviews);
                } catch (error) {
                    console.error("Error fetching movie reviews:", error);
                    showNotification("Failed to load movie reviews", 'error');
                    
                    if (loadingState) loadingState.classList.add('hidden');
                    if (noReviewsState) {
                        noReviewsState.textContent = 'Failed to load reviews';
                        noReviewsState.classList.remove('hidden');
                    }
                }
            }
            
            function updateMovieReviews(reviewsData = []) {
                const reviewsContainer = document.getElementById('reviews-container');
                const reviewsCount = document.getElementById('reviews-count');
                const loadingState = document.getElementById('reviews-loading');
                const noReviewsState = document.getElementById('no-reviews');

                reviewsContainer.innerHTML = '';
                
                if (reviewsData.length > 0) {
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

                        const currentUserId = @json(auth()->check() ? auth()->id() : null);
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
                    setupMovieReviewForm();
                }
            }
            
            function setupMovieReviewForm() {
                const writeReviewBtn = document.getElementById('write-review-btn');
                if (writeReviewBtn) {
                    writeReviewBtn.addEventListener('click', () => {
                        document.getElementById('review-form-container').classList.remove('hidden');
                        writeReviewBtn.classList.add('hidden');
                    });
                }
            }
            
            (function setupInitialMovieReviewForm() {
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
                        const movieId = getMovieIdFromPage();
                        const response = await fetch(`/movies/${movieId}/comments`, {
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
                        
                        fetchAllMovieReviews();
                    } catch (error) {
                        console.error('Error submitting review:', error);
                        showNotification('An error occurred while submitting the review', 'error');
                    }
                });
            }
            
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
                    
                    fetchAllMovieReviews();
                } catch (error) {
                    console.error('Error deleting comment:', error);
                    showNotification(error.message, 'error');
                }
            }
            
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
        });
    </script>
</body>
</html>