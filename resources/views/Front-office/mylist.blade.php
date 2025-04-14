<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PELIXS - My List</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
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
    </style>
</head>
<body class="bg-darker text-white font-sans">
    <!-- Header -->
    <header class="bg-dark py-4 px-6 shadow-lg fixed top-0 w-full z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-primary tracking-wider">PELIXS</h1>
            <nav class="hidden md:flex space-x-6">
                <a href="/home" class="hover:text-primary transition">Home</a>
                <a href="/browse" class="hover:text-primary transition">Browse</a>
                <a href="/movies" class="hover:text-primary transition">Movies</a>
                <a href="/shows" class="hover:text-primary transition">TV Shows</a>
                <a href="/anime" class="hover:text-primary transition">Anime</a>
                <a href="/my-list" class="text-primary font-medium">My List</a>
            </nav>
            <div class="flex items-center space-x-4">
                <div class="relative flex items-center">
                    <form id="search-form" action="/browse" method="get" class="flex items-center">
                        <input id="search-input" type="text" name="search" placeholder="Search..." 
                               class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm focus:outline-none border border-gray-700">
                        <button type="submit" class="text-xl p-2 rounded-full hover:bg-gray-800 transition">
                            <i class="ri-search-line"></i>
                        </button>
                    </form>
                </div>
                <button class="text-xl p-2 rounded-full hover:bg-gray-800 transition">
                    <i class="ri-notification-3-line"></i>
                </button>
                <div class="w-8 h-8 bg-primary rounded-full"></div>
            </div>
        </div>
    </header>

    <main class="pt-24 px-4 md:px-6 pb-16">
        <div class="container mx-auto">
            <!-- Page Title -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">My List</h1>
            </div>

            <!-- Tabs -->
            <div class="flex mb-8 border-b border-gray-800">
                <button id="movies-tab" class="px-4 py-2 border-b-2 border-primary text-primary font-medium">Favorite Movies</button>
                <button id="shows-tab" class="px-4 py-2 text-gray-400 hover:text-white">Favorite TV Shows</button>
            </div>

            <!-- Movies Section -->
            <div id="movies-section">
                @if(!empty($movies) && count($movies) > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
                        @foreach($movies as $movie)
                            <a href="/movie/{{ $movie['id'] }}" class="block">
                                <div class="movie-card relative group cursor-pointer">
                                    <div class="aspect-[2/3] overflow-hidden rounded-xl relative">
                                        <img src="{{ $movie['poster_path'] ? 'https://image.tmdb.org/t/p/w300' . $movie['poster_path'] : 'https://via.placeholder.com/300x450?text=No+Image' }}" alt="{{ $movie['title'] }}" class="card-image w-full h-full object-cover transition-all duration-300">
                                        <div class="card-overlay absolute inset-0 bg-black bg-opacity-50 opacity-0 transition-opacity flex items-center justify-center">
                                            <button class="play-button bg-primary text-white px-4 py-2 rounded-full transform translate-y-4 scale-75 transition-all duration-300">
                                                <i class="ri-play-fill text-2xl"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <h3 class="text-sm font-medium truncate">{{ $movie['title'] }}</h3>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="rating-pill text-xs px-2 py-1 rounded-full text-yellow-400">
                                                {{ number_format($movie['vote_average'], 1) }} / 10
                                            </span>
                                            <span class="text-xs text-gray-400">{{ $movie['release_date'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 text-gray-400">
                        <i class="ri-movie-line text-6xl mb-4 block"></i>
                        <p>No favorite movies added.</p>
                    </div>
                @endif
            </div>

            <!-- TV Shows Section (Initially Hidden) -->
            <div id="shows-section" class="hidden">
                @if(!empty($tvShows) && count($tvShows) > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
                        @foreach($tvShows as $show)
                            <a href="/shows/{{ $show['id'] }}" class="block">
                                <div class="movie-card relative group cursor-pointer">
                                    <div class="aspect-[2/3] overflow-hidden rounded-xl relative">
                                        <img src="{{ $show['poster_path'] ? 'https://image.tmdb.org/t/p/w300' . $show['poster_path'] : 'https://via.placeholder.com/300x450?text=No+Image' }}" alt="{{ $show['name'] }}" class="card-image w-full h-full object-cover transition-all duration-300">
                                        <div class="card-overlay absolute inset-0 bg-black bg-opacity-50 opacity-0 transition-opacity flex items-center justify-center">
                                            <button class="play-button bg-primary text-white px-4 py-2 rounded-full transform translate-y-4 scale-75 transition-all duration-300">
                                                <i class="ri-play-fill text-2xl"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <h3 class="text-sm font-medium truncate">{{ $show['name'] }}</h3>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="rating-pill text-xs px-2 py-1 rounded-full text-yellow-400">
                                                {{ number_format($show['vote_average'], 1) }} / 10
                                            </span>
                                            <span class="text-xs text-gray-400">{{ $show['first_air_date'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 text-gray-400">
                        <i class="ri-tv-line text-6xl mb-4 block"></i>
                        <p>No favorite TV shows added.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>
    
    <footer class="bg-dark py-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-400 text-sm">
                <p>© 2025 PELIXS. All rights reserved. Powered by TMDB API.</p>
            </div>
        </div>
    </footer>
    
    <!-- Tab Switching Script -->
    <script>
        const moviesTab = document.getElementById('movies-tab');
        const showsTab = document.getElementById('shows-tab');
        const moviesSection = document.getElementById('movies-section');
        const showsSection = document.getElementById('shows-section');

        moviesTab.addEventListener('click', () => {
            moviesTab.classList.add('border-primary', 'text-primary');
            moviesTab.classList.remove('text-gray-400');
            showsTab.classList.remove('border-primary', 'text-primary');
            showsTab.classList.add('text-gray-400');
            
            moviesSection.classList.remove('hidden');
            showsSection.classList.add('hidden');
        });

        showsTab.addEventListener('click', () => {
            showsTab.classList.add('border-primary', 'text-primary');
            showsTab.classList.remove('text-gray-400');
            moviesTab.classList.remove('border-primary', 'text-primary');
            moviesTab.classList.add('text-gray-400');
            
            showsSection.classList.remove('hidden');
            moviesSection.classList.add('hidden');
        });
    </script>
</body>
</html>
