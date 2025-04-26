<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.js"></script>
    <title>PELIXS - Anime</title>
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
        
        .anime-card:hover .card-overlay { opacity: 1; }
        .anime-card:hover .card-image { transform: scale(1.05); filter: brightness(0.7); }
        .rating-pill { background: rgba(255, 215, 0, 0.2); border: 1px solid rgba(255, 215, 0, 0.4); }
        .genre-pill { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px); }
        .filter-dropdown { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .filter-dropdown.open { max-height: 500px; }
        .genre-btn.active { background-color: #e50914; color: white; }
        .rating-btn.active { background-color: #e50914; color: white; }
        .year-slider::-webkit-slider-thumb {
            -webkit-appearance: none; appearance: none; width: 18px; height: 18px;
            border-radius: 50%; background: #e50914; cursor: pointer;
        }
        .year-slider::-moz-range-thumb {
            width: 18px; height: 18px; border-radius: 50%; background: #e50914; cursor: pointer;
        }
        .play-button {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 60px; height: 60px; background-color: #e50914; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: white;
            font-size: 24px; transition: transform 0.3s; opacity: 0.8;
        }
        .anime-card:hover .play-button { transform: translate(-50%, -50%) scale(1.1); }
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
                        <form id="search-form" action="/anime" method="get" class="flex items-center">
                            @if(request()->has('type'))
                                <input type="hidden" name="type" value="{{ request()->query('type') }}">
                            @endif
                            <div class="relative">
                                <input id="search-input" type="text" name="search" placeholder="Search..." 
                                       value="{{ request()->query('search') }}"
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
            <!-- Display error message if present -->
            @if($error)
            <div class="text-red-500 p-4 bg-red-100 rounded-lg mb-4">
                {{ $error }}
            </div>
            @endif

            <!-- Title Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-3xl font-bold mb-4 md:mb-0">
                    @if(request()->has('search'))
                        Search Results for "{{ request()->query('search') }}"
                    @elseif(request()->has('filter') && request()->query('filter') == 'upcoming')
                        Upcoming Anime
                    @elseif(request()->has('sort') && request()->query('sort') == 'trending')
                        Trending Anime
                    @elseif(request()->has('sort') && request()->query('sort') == 'release_date' && request()->has('order') && request()->query('order') == 'desc')
                        New Releases
                    @elseif(request()->has('sort') && request()->query('sort') == 'vote_average' && request()->has('order') && request()->query('order') == 'desc')
                        Top Rated Anime
                    @else
                        Browse Anime
                    @endif
                </h1>
            </div>

            <!-- Filters Section -->
            <div class="mb-8 bg-dark rounded-xl p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Filters</h2>
                    <button ```blade
                    id="toggle-filters" class="flex items-center text-sm text-primary">
                        <span id="filter-text">Show Filters</span>
                        <i id="filter-icon" class="ri-arrow-down-s-line ml-1 text-lg transition-transform"></i>
                    </button>
                </div>
                
                <div id="filter-dropdown" class="filter-dropdown">
                    <form id="filter-form" action="/anime" method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 py-4">
                        @if(request()->has('type'))
                            <input type="hidden" name="type" value="{{ request()->query('type') }}">
                        @endif
                        @if(request()->has('search'))
                            <input type="hidden" name="search" value="{{ request()->query('search') }}">
                        @endif
                        
                        <!-- Genre Filter -->
                        <div>
                            <label class="block text-gray-400 mb-2">Genre</label>
                            <div class="flex flex-wrap gap-2" id="genre-buttons">
                                <div class="w-full h-8 bg-gray-800 animate-pulse rounded-full"></div>
                            </div>
                            <input type="hidden" name="genre" id="selected-genre" value="{{ request()->query('genre', '') }}">
                        </div>
                        
                        <!-- Year Filter -->
                        <div>
                            <label class="block text-gray-400 mb-2">Year</label>
                            <div class="flex flex-col space-y-2">
                                <input type="range" min="1900" max="2025" 
                                       value="{{ request()->query('year', '2025') }}" 
                                       class="year-slider w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer" 
                                       id="year-slider">
                                <div class="flex justify-between">
                                    <span>1900</span>
                                    <span id="year-value">{{ request()->query('year', '2025') }}</span>
                                    <span>2025</span>
                                </div>
                                <input type="hidden" name="year" id="year-input" value="{{ request()->query('year', '2025') }}">
                            </div>
                        </div>
                        
                        <!-- Rating Filter -->
                        <div>
                            <label class="block text-gray-400 mb-2">Minimum Rating</label>
                            <div class="flex items-center space-x-2">
                                @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $rating)
                                <button type="button" 
                                        class="rating-btn w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 transition {{ request()->query('rating') == $rating ? 'active' : '' }}"
                                        data-rating="{{ $rating }}">
                                    {{ $rating }}
                                </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="rating" id="selected-rating" value="{{ request()->query('rating', '') }}">
                        </div>
                        
                        <!-- Sort Filter -->
                        <div>
                            <label class="block text-gray-400 mb-2">Sort By</label>
                            <select name="sort" class="w-full bg-gray-800 text-white px-4 py-2 rounded-lg focus:outline-none border border-gray-700">
                                <option value="popularity" {{ request()->query('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                                <option value="release_date" {{ request()->query('sort') == 'release_date' ? 'selected' : '' }}>Release Date</option>
                                <option value="vote_average" {{ request()->query('sort') == 'vote_average' ? 'selected' : '' }}>Rating</option>
                                <option value="trending" {{ request()->query('sort') == 'trending' ? 'selected' : '' }}>Trending</option>
                            </select>
                            
                            <label class="block text-gray-400 mt-4 mb-2">Order</label>
                            <div class="flex space-x-3">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="order" value="desc" class="form-radio text-primary" 
                                           {{ !request()->has('order') || request()->query('order') == 'desc' ? 'checked' : '' }}>
                                    <span class="ml-2">Descending</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="order" value="asc" class="form-radio text-primary"
                                           {{ request()->query('order') == 'asc' ? 'checked' : '' }}>
                                    <span class="ml-2"> Ascending</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Apply Filters Button -->
                        <div class="md:col-span-2 lg:col-span-4 flex justify-end mt-4">
                            <button type="button" id="clear-filters" class="px-6 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg mr-4 transition">
                                Clear Filters
                            </button>
                            <button type="submit" class="px-6 py-2 bg-primary hover:bg-primary/90 rounded-lg transition">
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6" id="results-grid">
                @forelse($animes as $anime)
                <div class="anime-card group rounded-xl overflow-hidden shadow-xl bg-dark border border-gray-800 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/20">
                    <div class="relative aspect-[2/3] overflow-hidden">
                        <img src="https://image.tmdb.org/t/p/w500{{ $anime['poster_path'] }}" 
                             alt="{{ $anime['name'] }}" 
                             class="card-image w-full h-full object-cover transition-all duration-500">
                        <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-0 transition-opacity duration-300 p-4 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary text-white">
                                    Anime
                                </span>
                            </div>
                            <div>
                                <div class="flex gap-2 mb-3">
                                    <span class="rating-pill text-xs px-2 py-1 rounded-full flex items-center">
                                        <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                        <span>{{ number_format($anime['vote_average'], 1) }}</span>
                                    </span>
                                    <span class="genre-pill text-xs px-2 py-1 rounded-full">
                                        {{ \Carbon\Carbon::parse($anime['first_air_date'])->format('Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <a href="/anime/{{ $anime['id'] }}" class="play-button">
                            <i class="ri-play-fill"></i>
                        </a>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg tracking-wide mb-1 truncate">{{ $anime['name'] }}</h3>
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($anime['genre_ids'], 0, 2) as $genreId)
                                @php $genre = collect($genres)->firstWhere('id', $genreId); @endphp
                                @if($genre)
                                    <span class="text-xs text-gray-400 bg-gray-800/80 px-2 py-0.5 rounded">
                                        {{ $genre['name'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-20">
                    <i class="ri-film-line text-6xl text-gray-600 mb-4"></i>
                    <h3 class="text-2xl font-medium text-gray-400">No anime found</h3>
                    <p class="text-gray-500 mt-2">Try adjusting your filters</p>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            <div class="mt-10 flex justify-center items-center" id="pagination-container">
                @if($totalPages > 1)
                    <div class="flex space-x-2">
                        @if($currentPage > 1)
                            <a href="?page={{ $currentPage - 1 }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 bg-dark hover:bg-gray-700 rounded-lg">
                                <i class="ri-arrow-left-s-line text-lg"></i>
                            </a>
                        @endif
                        
                        @if($totalPages <= 5)
                            @for($i = 1; $i <= $totalPages; $i++)
                                <a href="?page={{ $i }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 {{ $i == $currentPage ? 'bg-primary' : 'bg-dark hover:bg-gray-700' }} rounded-lg">{{ $i }}</a>
                            @endfor
                        @else
                            @if($currentPage <= 3)
                                @for($i = 1; $i <= 3; $i++)
                                    <a href="?page={{ $i }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 {{ $i == $currentPage ? 'bg-primary' : 'bg-dark hover:bg-gray-700' }} rounded-lg">{{ $i }}</a>
                                @endfor
                                <span class="px-4 py-2">...</span>
                                <a href="?page={{ $totalPages }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 bg-dark hover:bg-gray-700 rounded-lg">{{ $totalPages }}</a>
                            @elseif($currentPage >= $totalPages - 2)
                                <a href="?page=1&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 bg-dark hover:bg-gray-700 rounded-lg">1</a>
                                <span class="px-4 py-2">...</span>
                                @for($i = $totalPages - 2; $i <= $totalPages; $i++)
                                    <a href="?page={{ $i }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 {{ $i == $currentPage ? 'bg-primary' : 'bg-dark hover:bg-gray-700' }} rounded-lg">{{ $i }}</a>
                                @endfor
                            @else
                                <a href="?page=1&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 bg-dark hover:bg-gray-700 rounded-lg">1</a>
                                <span class="px-4 py-2">...</span>
                                <a href="?page={{ $currentPage - 1 }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 bg-dark hover:bg-gray-700 rounded-lg">{{ $currentPage - 1 }}</a>
                                <a href="?page={{ $currentPage }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 bg-primary rounded-lg">{{ $currentPage }}</a>
                                <a href="?page={{ $currentPage + 1 }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 bg-dark hover:bg-gray-700 rounded-lg">{{ $currentPage + 1 }}</a>
                                <span class="px-4 py-2">...</span>
                                <a href="?page={{ $totalPages }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 bg-dark hover:bg-gray-700 rounded-lg">{{ $totalPages }}</a>
                            @endif
                        @endif
                        
                        @if($currentPage < $totalPages)
                            <a href="?page={{ $currentPage + 1 }}&{{ http_build_query(request()->except('page')) }}" class="px-4 py-2 bg-dark hover:bg-gray-700 rounded-lg">
                                <i class="ri-arrow-right-s-line text-lg"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </main>
    
    <footer class="bg-dark py-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-400 text-sm">
                <p> 2025 PELIXS. All rights reserved. Powered by TMDB API.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleFilters = document.getElementById('toggle-filters');
            const filterDropdown = document.getElementById('filter-dropdown');
            const filterText = document.getElementById('filter-text');
            const filterIcon = document.getElementById('filter-icon');
            
            toggleFilters.addEventListener('click', function() {
                const isOpen = filterDropdown.classList.toggle('open');
                filterText.textContent = isOpen ? 'Hide Filters' : 'Show Filters';
                filterIcon.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0)';
            });
    
            const yearSlider = document.getElementById('year-slider');
            const yearValue = document.getElementById('year-value');
            if (yearSlider) {
                yearSlider.addEventListener('input', function() {
                    yearValue.textContent = this.value;
                });
            }
    
            const ratingButtons = document.querySelectorAll('.rating-btn');
 const selectedRatingInput = document.getElementById('selected-rating');
            
            ratingButtons.forEach(button => {
                button.addEventListener('click', function() {
                    ratingButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    selectedRatingInput.value = this.dataset.rating;
                });
            });
    
            document.getElementById('clear-filters').addEventListener('click', function() {

                document.querySelectorAll('.genre-btn').forEach(btn => btn.classList.remove('active'));
                document.getElementById('selected-genre').value = '';
                
                if (yearSlider) {
                    yearSlider.value = 2025;
                    yearValue.textContent = '2025';
                }
                
                ratingButtons.forEach(btn => btn.classList.remove('active'));
                selectedRatingInput.value = '';
                
                document.querySelector('select[name="sort"]').value = 'popularity';
                document.querySelector('input[name="order"][value="desc"]').checked = true;
            });
    
            const genreMap = @json(array_column($genres, 'name', 'id'));
            populateGenreButtons(@json($genres));

            function populateGenreButtons(genres) {
                const container = document.getElementById('genre-buttons');
                container.innerHTML = '';
                
                const allBtn = createGenreButton('', 'All', !window.location.search.includes('genre'));
                container.appendChild(allBtn);

                genres.forEach(genre => {
                    const isActive = new URLSearchParams(window.location.search).get('genre') === genre.id.toString();
                    const btn = createGenreButton(genre.id, genre.name, isActive);
                    container.appendChild(btn);
                });
            }
    
            function createGenreButton(value, text, isActive) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `genre-btn px-3 py-1 rounded-full text-sm transition ${
                    isActive ? 'bg-primary text-white' : 'bg-gray-800 hover:bg-gray-700'
                }`;
                btn.textContent = text;
                btn.dataset.genre = value;
                
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.genre-btn').forEach(b => b.classList.remove('bg-primary', 'text-white'));
                    this.classList.add('bg-primary', 'text-white');
                    document.getElementById('selected-genre').value = value;
                });
                
                return btn;
            }
    
            document.getElementById('pagination-container').addEventListener('click', function(e) {
                if (e.target.closest('a')) {
                    e.preventDefault();
                    const url = new URL(e.target.closest('a').href);
                    window.location.search = url.search;
                }
            });
        });
    </script>
</body>
</html>