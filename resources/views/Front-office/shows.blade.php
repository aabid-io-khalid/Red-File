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
    <title>PELIXS - TV Shows</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e50914',
                        dark: '#141414',
                        darker: '#0b0b0b'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        .movie-card:hover .card-overlay { opacity: 1; }
        .movie-card:hover .play-button { transform: translateY(0) scale(1); }
        .movie-card:hover .card-image { transform: scale(1.05); filter: brightness(0.7); }
        .rating-pill { background: rgba(255, 215, 0, 0.2); border: 1px solid rgba(255, 215, 0, 0.4); }
        .genre-pill { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px); }
        .filter-dropdown { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .filter-dropdown.open { max-height: 500px; }
        .genre-btn.active, .rating-btn.active { background-color: #e50914; color: white; }
        .year-slider::-webkit-slider-thumb {
            -webkit-appearance: none; appearance: none; width: 18px; height: 18px;
            border-radius: 50%; background: #e50914; cursor: pointer;
        }
        .year-slider::-moz-range-thumb {
            width: 18px; height: 18px; border-radius: 50%; background: #e50914; cursor: pointer;
        }
        .header-container {
            background: rgba(11, 11, 11, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
        .nav-link {
            position: relative;
            padding: 0.5rem 0;
            font-weight: 500;
            transition: all 0.3s ease;
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
        .nav-link.active {
            color: #e50914;
            font-weight: 600;
        }
        .logo-text {
            font-weight: 800;
            letter-spacing: 1px;
            text-shadow: 0 0 10px rgba(229, 9, 20, 0.5);
            background: linear-gradient(135deg, #ff0a18, #e50914);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .search-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .search-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(229, 9, 20, 0.5);
            box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.25);
        }
        .auth-button {
            background: linear-gradient(135deg, #ff0a18, #e50914);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(229, 9, 20, 0.3);
        }
        .auth-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(229, 9, 20, 0.4);
        }
        .logout-button {
            background: linear-gradient(135deg, #ff0a18, #e50914);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(229, 9, 20, 0.3);
        }
        .logout-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(229, 9, 20, 0.4);
        }
    </style>
</head>
<body class="bg-darker text-white font-sans">
    <!-- Header -->
    <header class="header-container py-4 fixed top-0 w-full z-50 transition-all duration-300">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <h1 class="logo-text text-3xl">PELIXS</h1>
                <!-- Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="/home" class="nav-link {{ request()->is('home') ? 'active' : '' }}">Home</a>
                    <a href="/browse" class="nav-link {{ request()->is('browse') ? 'active' : '' }}">Browse</a>
                    <a href="/movies" class="nav-link {{ request()->is('movies') ? 'active' : '' }}">Movies</a>
                    <a href="/shows" class="nav-link {{ request()->is('shows') ? 'active' : '' }}">TV Shows</a>
                    <a href="/anime" class="nav-link {{ request()->is('anime') ? 'active' : '' }}">Anime</a>
                    @auth
                        @can('access-community-chat')
                            <a href="{{ url('/community') }}" class="nav-link {{ request()->is('community') ? 'active' : '' }}">Community</a>
                            <a href="/mylist" class="nav-link {{ request()->is('mylist') ? 'active' : '' }}">My List</a>
                        @endcan
                        <a href="{{ url('/subscription') }}" class="nav-link {{ request()->is('subscription') ? 'active' : '' }}">Subscription</a>
                    @else
                        <a href="{{ url('/login') }}" class="nav-link">Community</a>
                    @endauth
                </nav>
                <!-- Search Bar & Auth -->
                <div class="flex items-center space-x-5">
                    <div class="relative flex items-center">
                        <form id="search-form" action="/shows" method="get" class="flex items-center">
                            <div class="relative">
                                <input id="search-input" type="text" name="search" placeholder="Search TV shows..." 
                                       value="{{ $search }}"
                                       class="search-input pl-10 pr-4 py-2 rounded-full text-sm focus:outline-none w-52">
                                <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition">
                                    <i class="ri-search-line"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    @guest
                        <a href="{{ url('/login') }}"
                           class="auth-button text-white px-5 py-2 rounded-full flex items-center">
                            <i class="ri-login-box-line mr-2"></i> Log In
                        </a>
                    @else
                        <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                            @csrf
                            <button type="submit"
                                    class="logout-button text-white px-5 py-2 rounded-full flex items-center">
                                <i class="ri-logout-box-r-line mr-2"></i> Log Out
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </header>


    <main class="pt-24 px-4 md:px-6 pb-16">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-3xl font-bold mb-4 md:mb-0">
                    @if($search) Search Results for "{{ $search }}"
                    @elseif($sort == 'trending') Trending TV Shows
                    @elseif($sort == 'release_date' && $order == 'desc') New TV Shows
                    @elseif($sort == 'vote_average' && $order == 'desc') Top Rated TV Shows
                    @else TV Shows @endif
                </h1>
            </div>

            <div class="mb-8 bg-dark rounded-xl p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Filters</h2>
                    <button id="toggle-filters" class="flex items-center text-sm text-primary">
                        <span id="filter-text">Show Filters</span>
                        <i id="filter-icon" class="ri-arrow-down-s-line ml-1 text-lg transition-transform"></i>
                    </button>
                </div>
                <div id="filter-dropdown" class="filter-dropdown">
                    <form id="filter-form" action="/shows" method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 py-4">
                        @if($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        <div>
                            <label class="block text-gray-400 mb-2">Genres</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($genres as $genre)
                                    <button type="button" class="genre-btn px-3 py-1 rounded-full bg-gray-800 hover:bg-gray-700 transition text-sm {{ in_array((string)$genre['id'], array_map('strval', $selectedGenres)) ? 'active' : '' }}"
                                            data-genre="{{ $genre['id'] }}">{{ $genre['name'] }}</button>
                                @endforeach
                            </div>
                            <input type="hidden" name="genres[]" id="selected-genres" value="">
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2">Year</label>
                            <div class="flex flex-col space-y-2">
                                <input type="range" min="1900" max="2025" value="{{ $year ?: '2025' }}"
                                       class="year-slider w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer" id="year-slider">
                                <div class="flex justify-between">
                                    <span>1900</span>
                                    <span id="year-value">{{ $year ?: '2025' }}</span>
                                    <span>2025</span>
                                </div>
                                <input type="hidden" name="year" id="year-input" value="{{ $year ?: '2025' }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2">Minimum Rating</label>
                            <div class="flex items-center space-x-2">
                                @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $r)
                                    <button type="button" class="rating-btn w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 transition {{ $rating == $r ? 'active' : '' }}"
                                            data-rating="{{ $r }}">{{ $r }}</button>
                                @endforeach
                            </div>
                            <input type="hidden" name="rating" id="selected-rating" value="{{ $rating }}">
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2">Sort By</label>
                            <select name="sort" class="w-full bg-gray-800 text-white px-4 py-2 rounded-lg focus:outline-none border border-gray-700">
                                <option value="popularity" {{ $sort == 'popularity' ? 'selected' : '' }}>Popularity</option>
                                <option value="release_date" {{ $sort == 'release_date' ? 'selected' : '' }}>Release Date</option>
                                <option value="vote_average" {{ $sort == 'vote_average' ? 'selected' : '' }}>Rating</option>
                                <option value="trending" {{ $sort == 'trending' ? 'selected' : '' }}>Trending</option>
                            </select>
                            <label class="block text-gray-400 mt-4 mb-2">Order</label>
                            <div class="flex space-x-3">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="order" value="desc" class="form-radio text-primary" {{ $order == 'desc' ? 'checked' : '' }}>
                                    <span class="ml-2">Descending</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="order" value="asc" class="form-radio text-primary" {{ $order == 'asc' ? 'checked' : '' }}>
                                    <span class="ml-2">Ascending</span>
                                </label>
                            </div>
                        </div>
                        <div class="md:col-span-2 lg:col-span-4 flex justify-end mt-4">
                            <button type="button" id="clear-filters" class="px-6 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg mr-4 transition">Clear Filters</button>
                            <button type="submit" class="px-6 py-2 bg-primary hover:bg-primary/90 rounded-lg transition">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
                @forelse ($content as $item)
                    @php
                        $isTmdb = isset($item['poster_path']);
                        $isLocal = $item['is_local'] ?? false;
                        $id = is_numeric($item['id']) ? (int)$item['id'] : null;
                        $title = $isTmdb ? ($item['name'] ?? 'Untitled') : ($item['title'] ?? 'Untitled');
                        $posterPath = $isTmdb
                            ? ($item['poster_path'] ? "https://image.tmdb.org/t/p/w500{$item['poster_path']}" : '/api/placeholder/500/750')
                            : ($item['poster'] ?? '/api/placeholder/500/750');
                        $releaseYear = $isTmdb
                            ? (isset($item['first_air_date']) ? (new DateTime($item['first_air_date']))->format('Y') : 'N/A')
                            : ($item['year'] ?? 'N/A');
                        $voteAverage = $isTmdb ? ($item['vote_average'] ?? 'N/A') : ($item['rating'] ?? 'N/A');
                        $genres = $item['genre_ids'] ?? [];
                    @endphp
                    @if($id)
                        <a href="{{ $isLocal ? route('tv-shows.local', $id) : route('tv-shows.details', $id) }}"
                           class="movie-card group rounded-xl overflow-hidden shadow-xl bg-dark border border-gray-800 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/20">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="{{ $posterPath }}" alt="{{ $title }}"
                                     class="card-image w-full h-full object-cover transition-all duration-500">
                                <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-0 transition-opacity duration-300 p-4 flex flex-col justify-between">
                                    <div class="flex justify-between items-start">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary text-white">
                                            TV Show
                                        </span>
                                        <button class="w-9 h-9 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-primary borderJustice League
                                        border border-gray-700 transition-colors">
                                            <i class="ri-heart-line"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <div class="flex gap-2 mb-3">
                                            <span class="rating-pill text-xs px-2 py-1 rounded-full flex items-center">
                                                <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                                <span>{{ is_numeric($voteAverage) ? number_format($voteAverage, 1) : $voteAverage }}</span>
                                            </span>
                                            <span class="genre-pill text-xs px-2 py-1 rounded-full">{{ $releaseYear }}</span>
                                        </div>
                                        <div class="play-button w-12 h-12 mx-auto bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-8 scale-75 transition-all duration-300">
                                            <i class="ri-play-fill text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg tracking-wide mb-1 truncate">{{ $title }}</h3>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($genres as $genreId)
                                        <span class="text-xs text-gray-400 bg-gray-800/80 px-2 py-0.5 rounded">
                                            {{ $genreMap[$genreId] ?? 'Unknown' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </a>
                    @endif
                @empty
                    <div class="col-span-full text-center py-20">
                        <i class="ri-film-line text-6xl text-gray-600 mb-4"></i>
                        <h3 class="text-2xl font-medium text-gray-400">No TV shows found</h3>
                        <p class="text-gray-500 mt-2">Try adjusting your filters or search terms.</p>
                    </div>
                @endforelse
            </div>

            @if($totalPages > 1)
                <div class="mt-10 flex justify-center items-center">
                    <nav class="flex items-center space-x-1">
                        @if($page > 1)
                            <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => $page - 1])) }}"
                               class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 text-primary transition">
                                <i class="ri-arrow-left-s-line"></i>
                            </a>
                        @endif
                        @php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            if ($startPage > 1) {
                                echo '<a href="' . url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => 1])) . '" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 text-white transition">1</a>';
                                if ($startPage > 2) {
                                    echo '<span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>';
                                }
                            }
                        @endphp
                        @for($i = $startPage; $i <= $endPage; $i++)
                            <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => $i])) }}"
                               class="w-10 h-10 flex items-center justify-center rounded-full {{ $i == $page ? 'bg-primary' : 'bg-gray-800 hover:bg-gray-700' }} text-white transition">{{ $i }}</a>
                        @endfor
                        @php
                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>';
                                }
                                echo '<a href="' . url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => $totalPages])) . '" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 text-white transition">' . $totalPages . '</a>';
                            }
                        @endphp
                        @if($page < $totalPages)
                            <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => $page + 1])) }}"
                               class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 text-primary transition">
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </main>

    <footer class="bg-dark py-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-400 text-sm">
                <p>© 2025 PELIXS. All rights reserved. Powered by TMDB API.</p>
            </div>
        </div>
    </footer>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Login form toggle for unauthenticated users
    const loginToggle = document.getElementById('login-toggle');
    const loginForm = document.getElementById('login-form');

    if (loginToggle && loginForm) {
        loginToggle.addEventListener('click', function() {
            loginForm.classList.toggle('show');
        });

        // Close login form when clicking outside
        document.addEventListener('click', function(event) {
            if (!loginForm.contains(event.target) && !loginToggle.contains(event.target)) {
                loginForm.classList.remove('show');
            }
        });
    }

    // Filter dropdown toggle
    const toggleFilters = document.getElementById('toggle-filters');
    if (toggleFilters) {
        toggleFilters.addEventListener('click', function() {
            const filterDropdown = document.getElementById('filter-dropdown');
            const filterIcon = document.getElementById('filter-icon');
            const filterText = document.getElementById('filter-text');
            filterDropdown.classList.toggle('open');
            filterIcon.classList.toggle('rotate-180');
            filterText.textContent = filterDropdown.classList.contains('open') ? 'Hide Filters' : 'Show Filters';
        });
    }

    // Year slider
    const yearSlider = document.getElementById('year-slider');
    const yearValue = document.getElementById('year-value');
    const yearInput = document.getElementById('year-input');
    if (yearSlider && yearValue && yearInput) {
        yearSlider.addEventListener('input', function() {
            yearValue.textContent = this.value || 'Any';
            yearInput.value = this.value;
        });
    }

    // Rating buttons
    document.querySelectorAll('.rating-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.rating-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('selected-rating').value = this.dataset.rating;
        });
    });

    // Multi-select genres
    const selectedGenres = [];
    document.querySelectorAll('.genre-btn').forEach(button => {
        button.addEventListener('click', function() {
            const genreId = this.dataset.genre;
            const index = selectedGenres.indexOf(genreId);
            if (index === -1) {
                selectedGenres.push(genreId);
                this.classList.add('active');
            } else {
                selectedGenres.splice(index, 1);
                this.classList.remove('active');
            }
            // Update hidden inputs
            const form = document.getElementById('filter-form');
            document.querySelectorAll('input[name="genres[]"]').forEach(input => input.remove());
            selectedGenres.forEach(genre => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'genres[]';
                input.value = genre;
                form.appendChild(input);
            });
        });
    });

    // Clear filters
    const clearFilters = document.getElementById('clear-filters');
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            document.getElementById('filter-form').reset();
            document.getElementById('selected-rating').value = '';
            document.querySelectorAll('.rating-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.genre-btn').forEach(btn => btn.classList.remove('active'));
            selectedGenres.length = 0;
            document.querySelectorAll('input[name="genres[]"]').forEach(input => input.remove());
            yearSlider.value = '';
            yearValue.textContent = 'Any';
            yearInput.value = '';
            document.querySelector('select[name="sort"]').value = 'popularity';
            document.querySelector('input[name="order"][value="desc"]').checked = true;
        });
    }
});
</script>
</body>
</html>