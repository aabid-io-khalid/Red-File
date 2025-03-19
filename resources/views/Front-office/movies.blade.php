<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <title>PELIXS - Movies</title>
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
        .filter-dropdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .filter-dropdown.open {
            max-height: 500px;
        }
        .genre-btn.active {
            background-color: #e50914;
            color: white;
        }
        .rating-btn.active {
            background-color: #e50914;
            color: white;
        }
        .year-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #e50914;
            cursor: pointer;
        }
        .year-slider::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #e50914;
            cursor: pointer;
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
                <a href="/movies" class="text-primary font-medium">Movies</a>
                <a href="/shows" class="hover:text-primary transition">TV Shows</a>
                <a href="/anime" class="hover:text-primary transition">Anime</a>
                <a href="/browse?list=favorites" class="{{request()->has('list') && request()->query('list') == 'favorites' ? 'text-primary font-medium' : 'hover:text-primary transition'}}">My List</a>
            </nav>
            <div class="flex items-center space-x-4">
                <div class="relative flex items-center">
                    <form id="search-form" action="/browse" method="get" class="flex items-center">
                        @if(request()->has('type'))
                            <input type="hidden" name="type" value="{{ request()->query('type') }}">
                        @endif
                        <input id="search-input" type="text" name="search" placeholder="Search..." 
                               value="{{ request()->query('search') }}"
                               class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm focus:outline-none border border-gray-700">
                        <button type="submit" class="text-xl p-2 rounded-full hover:bg-gray-800 transition">
                            <i class="ri-search-line"></i>
                        </button>
                    </form>
                </div>
                <button class="text-xl p-2 rounded-full hover:bg-gray-800 transition"><i class="ri-notification-3-line"></i></button>
                <div class="w-8 h-8 bg-primary rounded-full"></div>
            </div>
        </div>
    </header>

    <main class="pt-24 px-4 md:px-6 pb-16">
        <div class="container mx-auto">
            <!-- Title -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-3xl font-bold mb-4 md:mb-0">
                    @if(request()->has('search'))
                        Search Results for "{{ request()->query('search') }}"
                    @elseif(request()->has('filter') && request()->query('filter') == 'upcoming')
                        Upcoming Movies
                    @elseif(request()->has('sort') && request()->query('sort') == 'trending')
                        Trending Movies
                    @elseif(request()->has('sort') && request()->query('sort') == 'release_date' && request()->has('order') && request()->query('order') == 'desc')
                        New Releases
                    @elseif(request()->has('sort') && request()->query('sort') == 'vote_average' && request()->has('order') && request()->query('order') == 'desc')
                        Top Rated Movies
                    @else
                        Browse Movies
                    @endif
                </h1>
            </div>

            <!-- Filters Section -->
            <div class="mb-8 bg-dark rounded-xl p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Filters</h2>
                    <button id="toggle-filters" class="flex items-center text-sm text-primary">
                        <span id="filter-text">Show Filters</span>
                        <i id="filter-icon" class="ri-arrow-down-s-line ml-1 text-lg transition-transform"></i>
                    </button>
                </div>
                
                <div id="filter-dropdown" class="filter-dropdown">
                    <form id="filter-form" action="/browse" method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 py-4">
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
                                    <span class="ml-2">Ascending</span>
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
    <!-- Loading placeholders -->
    @for ($i = 0; $i < 12; $i++)
    <div class="bg-dark rounded-xl overflow-hidden shadow-xl animate-pulse">
        <div class="aspect-[2/3] bg-gray-800"></div>
        <div class="p-4 space-y-2">
            <div class="h-4 bg-gray-800 rounded"></div>
            <div class="h-3 bg-gray-800 rounded w-3/4"></div>
        </div>
    </div>
    @endfor

    @foreach ($movies as $movie)
    @php
        $posterPath = $movie['poster_path'] ? "https://image.tmdb.org/t/p/w500{$movie['poster_path']}" : '/api/placeholder/500/750';
        $releaseYear = $movie['release_date'] ? (new DateTime($movie['release_date']))->format('Y') : 'N/A';
        $voteAverage = $movie['vote_average'] ? $movie['vote_average'] : 'N/A';
    @endphp
    <a href="{{ route('movies.show', $movie['id']) }}" class="movie-card group rounded-xl overflow-hidden shadow-xl bg-dark border border-gray-800 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/20">
        <div class="relative aspect-[2/3] overflow-hidden">
            <img src="{{ $posterPath }}" alt="{{ $movie['title'] }}" class="card-image w-full h-full object-cover transition-all duration-500">
            <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-0 transition-opacity duration-300 p-4 flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary text-white">
                        Movie
                    </span>
                    <button class="w-9 h-9 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-primary border border-gray-700 transition-colors">
                        <i class="ri-heart-line"></i>
                    </button>
                </div>
                <div>
                    <div class="flex gap-2 mb-3">
                        <span class="rating-pill text-xs px-2 py-1 rounded-full flex items-center">
                            <i class="ri-star-fill text-yellow-500 mr-1"></i>
                            <span>{{ number_format((float)$voteAverage ?: 0, 1) }}</span>
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
            <h3 class="font-bold text-lg tracking-wide mb-1 truncate">{{ $movie['title'] }}</h3>
            <div class="flex flex-wrap gap-1">
                @foreach ($movie['genre_ids'] as $genreId)
                    <span class="text-xs text-gray-400 bg-gray-800/80 px-2 py-0.5 rounded">{{ $genreMap[$genreId] ?? 'Unknown' }}</span>
                @endforeach
            </div>
        </div>
    </a>
    @endforeach
</div>
            
            <!-- Pagination -->
            <div class="mt-10 flex justify-center items-center" id="pagination-container">
                <!-- Pagination will be populated by JavaScript -->
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

    <script>
        const API_KEY = 'fcc76d42a33f4349db9ab3a42d7cc207'; 
        const BASE_URL = 'https://api.themoviedb.org/3';
        const IMAGE_BASE_URL = 'https://image.tmdb.org/t/p';
        
        const urlParams = new URLSearchParams(window.location.search);
        const contentType = 'movie'; 
        const searchQuery = urlParams.get('search') || '';
        const selectedGenre = urlParams.get('genre') || '';
        const selectedYear = urlParams.get('year') || '2025';
        const selectedRating = urlParams.get('rating') || '';
        const sortBy = urlParams.get('sort') || 'popularity';
        const orderBy = urlParams.get('order') || 'desc';
        const currentPage = parseInt(urlParams.get('page') || '1');
        
        const genreMap = {};
        
        document.addEventListener('DOMContentLoaded', function() {
            const toggleFilters = document.getElementById('toggle-filters');
            const filterDropdown = document.getElementById('filter-dropdown');
            const filterText = document.getElementById('filter-text');
            const filterIcon = document.getElementById('filter-icon');
            
            toggleFilters.addEventListener('click', function() {
                filterDropdown.classList.toggle('open');
                if (filterDropdown.classList.contains('open')) {
                    filterText.textContent = 'Hide Filters';
                    filterIcon.style.transform = 'rotate(180deg)';
                } else {
                    filterText.textContent = 'Show Filters';
                    filterIcon.style.transform = 'rotate(0)';
                }
            });
            
            init();
        });
        
        const yearSlider = document.getElementById('year-slider');
        const yearValue = document.getElementById('year-value');
        const yearInput = document.getElementById('year-input');
        
        if (yearSlider) {
            yearSlider.addEventListener('input', function() {
                yearValue.textContent = this.value;
                yearInput.value = this.value;
            });
        }
        
        const ratingButtons = document.querySelectorAll('.rating-btn');
        const selectedRatingInput = document.getElementById('selected-rating');
        
        ratingButtons.forEach(button => {
            button.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                
                ratingButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                selectedRatingInput.value = rating;
            });
        });
        
        const clearFiltersBtn = document.getElementById('clear-filters');
        
        clearFiltersBtn.addEventListener('click', function() {
            document.querySelectorAll('.genre-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('selected-genre').value = '';
            
            yearSlider.value = 2025;
            yearValue.textContent = '2025';
            yearInput.value = '2025';
            
            ratingButtons.forEach(btn => btn.classList.remove('active'));
            selectedRatingInput.value = '';
            
            document.querySelector('select[name="sort"]').value = 'popularity';
            document.querySelector('input[name="order"][value="desc"]').checked = true;
        });
        
        async function fetchGenres() {
            try {
                const movieResponse = await fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}`);
                const movieData = await movieResponse.json();
                
                movieData.genres.forEach(genre => {
                    genreMap[genre.id] = genre.name;
                });
                
                populateGenreButtons(movieData.genres);
            } catch (error) {
                console.error('Error fetching genres:', error);
            }
        }
        
        function populateGenreButtons(genres) {
            const genreContainer = document.getElementById('genre-buttons');
            genreContainer.innerHTML = '';
            
            const allBtn = document.createElement('button');
            allBtn.type = 'button';
            allBtn.className = `genre-btn px-3 py-1 rounded-full bg-gray-800 hover:bg-gray-700 text-sm transition ${selectedGenre === '' ? 'active' : ''}`;
            allBtn.textContent = 'All';
            allBtn.setAttribute('data-genre', '');
            genreContainer.appendChild(allBtn);
            
            genres.forEach(genre => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `genre-btn px-3 py-1 rounded-full bg-gray-800 hover:bg-gray-700 text-sm transition ${selectedGenre.split(',').includes(genre.id.toString()) ? 'active' : ''}`;
                btn.textContent = genre.name;
                btn.setAttribute('data-genre', genre.id);
                genreContainer.appendChild(btn);
            });
            
            document.querySelectorAll('.genre-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const genre = this.getAttribute('data-genre');
                    
                    this.classList.toggle('active');
                    
                    const selectedGenres = Array.from(document.querySelectorAll('.genre-btn.active')).map(btn => btn.getAttribute('data-genre')).join(',');
                    document.getElementById('selected-genre').value = selectedGenres;
                });
            });
        }
        
        async function fetchContent() {
            try {
                let apiUrl = '';
                let queryParams = `api_key=${API_KEY}&page=${currentPage}`;
                
                if (searchQuery) {
                    apiUrl = `${BASE_URL}/search/${contentType}`;
                    queryParams += `&query=${encodeURIComponent(searchQuery)}`;
                }
                else if (sortBy === 'trending') {
                    apiUrl = `${BASE_URL}/trending/${contentType}/week`;
                }
                else {
                    apiUrl = `${BASE_URL}/discover/${contentType}`;
                    
                    if (sortBy && sortBy !== 'trending') {
                        queryParams += `&sort_by=${sortBy}.${orderBy}`;
                    }
                    
                    if (selectedGenre) {
                        queryParams += `&with_genres=${selectedGenre}`;
                    }
                    
                    if (selectedYear) {
                        queryParams += `&primary_release_year=${selectedYear}`;
                    }
                    
                    if (selectedRating) {
                        queryParams += `&vote_average.gte=${selectedRating}`;
                    }
                }
                
                const response = await fetch(`${apiUrl}?${queryParams}`);
                const data = await response.json();
                
                displayResults(data.results);
                
                displayPagination(data.page, data.total_pages);
                
            } catch (error) {
                console.error('Error fetching content:', error);
                displayError();
            }
        }
        
        function displayResults(results) {
            const resultsGrid = document.getElementById('results-grid');
            resultsGrid.innerHTML = '';
            
            if (results.length === 0) {
                resultsGrid.innerHTML = `
                    <div class="col-span-full text-center py-20">
                        <i class="ri-film-line text-6xl text-gray-600 mb-4"></i>
                        <h3 class="text-2xl font-medium text-gray-400">No results found</h3>
                        <p class="text-gray-500 mt-2">Try adjusting your filters or search terms</p>
                    </div>
                `;
                return;
            }
            
            results.forEach(item => {
                const title = item.title; 
                const posterPath = item.poster_path;
                const releaseDate = item.release_date;
                const releaseYear = releaseDate ? new Date(releaseDate).getFullYear() : '';
                const id = item.id;
                const voteAverage = item.vote_average;
                
                const genreNames = (item.genre_ids || []).map(id => genreMap[id] || '').filter(Boolean);
                
                const card = document.createElement('div');
                card.className = 'movie-card group rounded-xl overflow-hidden shadow-xl bg-dark border border-gray-800 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/20';
                
                card.innerHTML = `
                    <div class="relative aspect-[2/3] overflow-hidden">
                        <img src="${posterPath ? `${IMAGE_BASE_URL}/w500${posterPath}` : '/api/placeholder/500/750'}" 
                             alt="${title}" 
                             class="card-image w-full h-full object-cover transition-all duration-500">
                        <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-0 transition-opacity duration-300 p-4 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary text-white">
                                    Movie
                                </span>
                                <button class="w-9 h-9 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-300 hover:text-primary border border-gray-700 transition-colors">
                                    <i class="ri-heart-line"></i>
                                </button>
                            </div>
                            <div>
                                <div class="flex gap-2 mb-3">
                                    <span class="rating-pill text-xs px-2 py-1 rounded-full flex items-center">
                                        <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                        <span>${voteAverage.toFixed(1)}</span>
                                    </span>
                                    <span class="genre-pill text-xs px-2 py-1 rounded-full">${releaseYear}</span>
                                </div>
                                <a href="/movie/${id}" class="block">
                                    <div class="play-button w-12 h-12 mx-auto bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-8 scale-75 transition-all duration-300">
                                        <i class="ri-play-fill text-xl"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg tracking-wide mb-1 truncate">${title}</h3>
                        <div class="flex flex-wrap gap-1">
                            ${genreNames.slice(0, 2).map(genre => 
                                `<span class="text-xs text-gray-400 bg-gray-800/80 px-2 py-0.5 rounded">${genre}</span>`
                            ).join('')}
                        </div>
                    </div>
                `;
                
                resultsGrid.appendChild(card);
            });
        }
        
        function displayPagination(currentPage, totalPages) {
            const paginationContainer = document.getElementById('pagination-container');
            paginationContainer.innerHTML = '';
            
            if (totalPages <= 1) {
                return;
            }
            
            const pagination = document.createElement('div');
            pagination.className = 'flex space-x-1';
            
            if (currentPage > 1) {
                const prevButton = createPaginationButton('previous', currentPage - 1, '<i class="ri-arrow-left-s-line"></i>');
                pagination.appendChild(prevButton);
            }
            
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);
            
            if (startPage > 1) {
                const firstButton = createPaginationButton('page', 1, '1');
                pagination.appendChild(firstButton);
                
                if (startPage > 2) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'w-10 h-10 flex items-center justify-center text-gray-400';
                    ellipsis.textContent = '...';
                    pagination.appendChild(ellipsis);
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const pageButton = createPaginationButton('page', i, i.toString(), i === currentPage);
                pagination.appendChild(pageButton);
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'w-10 h-10 flex items-center justify-center text-gray-400';
                    ellipsis.textContent = '...';
                    pagination.appendChild(ellipsis);
                }
                const lastButton = createPaginationButton('page', totalPages, totalPages.toString());
                pagination.appendChild(lastButton);
            }
            
            if (currentPage < totalPages) {
                const nextButton = createPaginationButton('next', currentPage + 1, '<i class="ri-arrow-right-s-line"></i>');
                pagination.appendChild(nextButton);
            }
            
            paginationContainer.appendChild(pagination);
        }
        
        function createPaginationButton(type, page, content, isActive = false) {
            const button = document.createElement('a');
            button.href = type === 'page' ? `?type=${contentType}&page=${page}` : `?type=${contentType}&page=${page}`;
            button.className = `w-10 h-10 flex items-center justify-center rounded-full ${isActive ? 'bg-primary' : 'bg-dark hover:bg-gray-700'} text-white transition`;
            button.innerHTML = content;
            return button;
        }

        async function init() {
            await fetchGenres();
            await fetchContent();
        }
    </script>
</body>
</html>