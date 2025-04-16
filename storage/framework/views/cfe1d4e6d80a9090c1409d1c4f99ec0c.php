<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.js"></script>
    <title>PELIXS - Movies</title>
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
                    <a href="/home" class="nav-link <?php echo e(request()->is('home') ? 'active' : ''); ?>">Home</a>
                    <a href="/browse" class="nav-link <?php echo e(request()->is('browse') ? 'active' : ''); ?>">Browse</a>
                    <a href="/movies" class="nav-link <?php echo e(request()->is('movies') ? 'active' : ''); ?>">Movies</a>
                    <a href="/shows" class="nav-link <?php echo e(request()->is('shows') ? 'active' : ''); ?>">TV Shows</a>
                    <a href="/anime" class="nav-link <?php echo e(request()->is('anime') ? 'active' : ''); ?>">Anime</a>
                    <?php if(auth()->guard()->check()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('access-community-chat')): ?>
                            <a href="<?php echo e(url('/community')); ?>" class="nav-link <?php echo e(request()->is('community') ? 'active' : ''); ?>">Community</a>
                            <a href="/mylist" class="nav-link <?php echo e(request()->is('mylist') ? 'active' : ''); ?>">My List</a>
                        <?php endif; ?>
                        <a href="<?php echo e(url('/subscription')); ?>" class="nav-link <?php echo e(request()->is('subscription') ? 'active' : ''); ?>">Subscription</a>
                    <?php else: ?>
                        <a href="<?php echo e(url('/login')); ?>" class="nav-link">Community</a>
                    <?php endif; ?>
                </nav>
                <!-- Search Bar & Auth -->
                <div class="flex items-center space-x-5">
                    <div class="relative flex items-center">
                        <form id="search-form" action="/movies" method="get" class="flex items-center">
                            <?php if(request()->has('list')): ?>
                                <input type="hidden" name="list" value="<?php echo e(is_array(request()->query('list')) ? implode(',', request()->query('list')) : request()->query('list')); ?>">
                            <?php endif; ?>
                            <div class="relative">
                                <input id="search-input" type="text" name="search" placeholder="Search movies..." 
                                       value="<?php echo e($search); ?>"
                                       class="search-input pl-10 pr-4 py-2 rounded-full text-sm focus:outline-none w-52">
                                <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition">
                                    <i class="ri-search-line"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(url('/login')); ?>"
                           class="auth-button text-white px-5 py-2 rounded-full flex items-center">
                            <i class="ri-login-box-line mr-2"></i> Log In
                        </a>
                    <?php else: ?>
                        <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline-flex">
                            <?php echo csrf_field(); ?>
                            <button type="submit"
                                    class="logout-button text-white px-5 py-2 rounded-full flex items-center">
                                <i class="ri-logout-box-r-line mr-2"></i> Log Out
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
</body>
</html>

    <main class="pt-24 px-4 md:px-6 pb-16">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-3xl font-bold mb-4 md:mb-0">
                    <?php if($search): ?> Search Results for "<?php echo e($search); ?>"
                    <?php elseif($sort == 'trending'): ?> Trending Movies
                    <?php elseif($sort == 'release_date' && $order == 'desc'): ?> New Releases
                    <?php elseif($sort == 'vote_average' && $order == 'desc'): ?> Top Rated Movies
                    <?php else: ?> Browse Movies <?php endif; ?>
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
                    <form id="filter-form" action="/movies" method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 py-4">
                        <?php if(request()->has('list')): ?>
                            <input type="hidden" name="list" value="<?php echo e(is_array(request()->query('list')) ? implode(',', request()->query('list')) : request()->query('list')); ?>">
                        <?php endif; ?>
                        <?php if($search): ?>
                            <input type="hidden" name="search" value="<?php echo e($search); ?>">
                        <?php endif; ?>
                        <div>
                            <label class="block text-gray-400 mb-2">Genres</label>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button type="button" class="genre-btn px-3 py-1 rounded-full bg-gray-800 hover:bg-gray-700 transition text-sm <?php echo e(in_array((string)$genre['id'], array_map('strval', $selectedGenres)) ? 'active' : ''); ?>"
                                            data-genre="<?php echo e($genre['id']); ?>"><?php echo e($genre['name']); ?></button>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <input type="hidden" name="genres[]" id="selected-genres" value="">
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2">Year</label>
                            <div class="flex flex-col space-y-2">
                                <input type="range" min="1900" max="2025" value="<?php echo e($year ?: '2025'); ?>"
                                       class="year-slider w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer" id="year-slider">
                                <div class="flex justify-between">
                                    <span>1900</span>
                                    <span id="year-value"><?php echo e($year ?: '2025'); ?></span>
                                    <span>2025</span>
                                </div>
                                <dekfault
                                <input type="hidden" name="year" id="year-input" value="<?php echo e($year ?: '2025'); ?>">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2">Minimum Rating</label>
                            <div class="flex items-center space-x-2">
                                <?php $__currentLoopData = [1, 2, 3, 4, 5, 6, 7, 8, 9]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button type="button" class="rating-btn w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 transition <?php echo e($rating == $r ? 'active' : ''); ?>"
                                            data-rating="<?php echo e($r); ?>"><?php echo e($r); ?></button>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <input type="hidden" name="rating" id="selected-rating" value="<?php echo e($rating); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2">Sort By</label>
                            <select name="sort" class="w-full bg-gray-800 text-white px-4 py-2 rounded-lg focus:outline-none border разбавляют border-gray-700">
                                <option value="popularity" <?php echo e($sort == 'popularity' ? 'selected' : ''); ?>>Popularity</option>
                                <option value="release_date" <?php echo e($sort == 'release_date' ? 'selected' : ''); ?>>Release Date</option>
                                <option value="vote_average" <?php echo e($sort == 'vote_average' ? 'selected' : ''); ?>>Rating</option>
                                <option value="trending" <?php echo e($sort == 'trending' ? 'selected' : ''); ?>>Trending</option>
                            </select>
                            <label class="block text-gray-400 mt-4 mb-2">Order</label>
                            <div class="flex space-x-3">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="order" value="desc" class="form-radio text-primary" <?php echo e($order == 'desc' ? 'checked' : ''); ?>>
                                    <span class="ml-2">Descending</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="order" value="asc" class="form-radio text-primary" <?php echo e($order == 'asc' ? 'checked' : ''); ?>>
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
                <?php $__empty_1 = true; $__currentLoopData = $content; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isTmdb = isset($item['poster_path']);
                        $isLocal = $item['is_local'] ?? false;
                        $id = is_numeric($item['id']) ? (int)$item['id'] : null;
                        $title = $isTmdb ? ($item['title'] ?? $item['name'] ?? 'Untitled') : ($item['title'] ?? 'Untitled');
                        if (is_array($title)) {
                            \Illuminate\Support\Facades\Log::warning('Title is an array', ['item' => $item]);
                            $title = 'Invalid Title';
                        }
                        $posterPath = $isTmdb
                            ? ($item['poster_path'] ? "https://image.tmdb.org/t/p/w500{$item['poster_path']}" : '/api/placeholder/500/750')
                            : ($item['poster'] ?? '/api/placeholder/500/750');
                        if (is_array($posterPath)) {
                            \Illuminate\Support\Facades\Log::warning('Poster path is an array', ['item' => $item]);
                            $posterPath = '/api/placeholder/500/750';
                        }
                        $releaseYear = $isTmdb
                            ? (isset($item['release_date']) && is_string($item['release_date']) ? (new DateTime($item['release_date']))->format('Y') : 'N/A')
                            : ($item['year'] ?? 'N/A');
                        if (is_array($releaseYear)) {
                            \Illuminate\Support\Facades\Log::warning('Release year is an array', ['item' => $item]);
                            $releaseYear = 'N/A';
                        }
                        $voteAverage = $isTmdb ? ($item['vote_average'] ?? 'N/A') : ($item['rating'] ?? 'N/A');
                        if (is_array($voteAverage)) {
                            \Illuminate\Support\Facades\Log::warning('Vote average is an array', ['item' => $item]);
                            $voteAverage = 'N/A';
                        }
                        $genres = $item['genre_ids'] ?? [];
                        if (!is_array($genres)) {
                            \Illuminate\Support\Facades\Log::warning('Genres is not an array', ['item' => $item]);
                            $genres = [];
                        }
                    ?>
                    <?php if($id && is_string($title) && is_string($posterPath)): ?>
                        <a href="<?php echo e($isLocal ? route('movies.local', $id) : route('movies.show', $id)); ?>"
                           class="movie-card group rounded-xl overflow-hidden shadow-xl bg-dark border border-gray-800 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/20">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="<?php echo e($posterPath); ?>" alt="<?php echo e($title); ?>"
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
                                                <span><?php echo e(is_numeric($voteAverage) ? number_format($voteAverage, 1) : $voteAverage); ?></span>
                                            </span>
                                            <span class="genre-pill text-xs px-2 py-1 rounded-full"><?php echo e($releaseYear); ?></span>
                                        </div>
                                        <div class="play-button w-12 h-12 mx-auto bg-primary rounded-full flex items-center justify-center text-white shadow-lg transform translate-y-8 scale-75 transition-all duration-300">
                                            <i class="ri-play-fill text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg tracking-wide mb-1 truncate"><?php echo e($title); ?></h3>
                                <div class="flex flex-wrap gap-1">
                                    <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genreId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="text-xs text-gray-400 bg-gray-800/80 px-2 py-0.5 rounded">
                                            <?php echo e($genreMap[$genreId] ?? 'Unknown'); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </a>
                    <?php else: ?>
                        <?php
                            \Illuminate\Support\Facades\Log::warning('Skipping invalid movie', ['item' => $item]);
                        ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center py-20">
                        <i class="ri-film-line text-6xl text-gray-600 mb-4"></i>
                        <h3 class="text-2xl font-medium text-gray-400">No movies found</h3>
                        <p class="text-gray-500 mt-2">Try adjusting your filters or search terms.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($totalPages > 1): ?>
                <div class="mt-10 flex justify-center items-center">
                    <nav class="flex items-center space-x-1">
                        <?php if($page > 1): ?>
                            <a href="<?php echo e(url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => $page - 1]))); ?>"
                               class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 text-primary transition">
                                <i class="ri-arrow-left-s-line"></i>
                            </a>
                        <?php endif; ?>
                        <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            if ($startPage > 1) {
                                echo '<a href="' . url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => 1])) . '" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 text-white transition">1</a>';
                                if ($startPage > 2) {
                                    echo '<span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>';
                                }
                            }
                        ?>
                        <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                            <a href="<?php echo e(url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => $i]))); ?>"
                               class="w-10 h-10 flex items-center justify-center rounded-full <?php echo e($i == $page ? 'bg-primary' : 'bg-gray-800 hover:bg-gray-700'); ?> text-white transition"><?php echo e($i); ?></a>
                        <?php endfor; ?>
                        <?php
                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>';
                                }
                                echo '<a href="' . url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => $totalPages])) . '" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 text-white transition">' . $totalPages . '</a>';
                            }
                        ?>
                        <?php if($page < $totalPages): ?>
                            <a href="<?php echo e(url()->current() . '?' . http_build_query(array_merge(request()->all(), ['page' => $page + 1]))); ?>"
                               class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 text-primary transition">
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
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
        document.getElementById('toggle-filters').addEventListener('click', function() {
            const filterDropdown = document.getElementById('filter-dropdown');
            const filterIcon = document.getElementById('filter-icon');
            const filterText = document.getElementById('filter-text');
            filterDropdown.classList.toggle('open');
            filterIcon.classList.toggle('rotate-180');
            filterText.textContent = filterDropdown.classList.contains('open') ? 'Hide Filters' : 'Show Filters';
        });

        const yearSlider = document.getElementById('year-slider');
        const yearValue = document.getElementById('year-value');
        const yearInput = document.getElementById('year-input');
        yearSlider.addEventListener('input', function() {
            yearValue.textContent = this.value || '2025';
            yearInput.value = this.value;
        });

        const ratingButtons = document.querySelectorAll('.rating-btn');
        ratingButtons.forEach(button => {
            button.addEventListener('click', function() {
                ratingButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('selected-rating').value = this.dataset.rating;
            });
        });

        const selectedGenres = <?php echo json_encode($selectedGenres, 15, 512) ?>;
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

        document.getElementById('clear-filters').addEventListener('click', function() {
            document.getElementById('filter-form').reset();
            document.querySelectorAll('.genre-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.rating-btn').forEach(btn => btn.classList.remove('active'));
            selectedGenres.length = 0;
            document.querySelectorAll('input[name="genres[]"]').forEach(input => input.remove());
            yearSlider.value = '2025';
            yearValue.textContent = '2025';
            yearInput.value = '2025';
            document.querySelector('select[name="sort"]').value = 'popularity';
            document.querySelector('input[name="order"][value="desc"]').checked = true;
        });

        const profileToggle = document.getElementById('profile-toggle');
        const profileDropdown = document.getElementById('profile-dropdown');
        profileToggle.addEventListener('click', function() {
            profileDropdown.classList.toggle('show');
        });
        document.addEventListener('click', function(event) {
            if (!profileToggle.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html><?php /**PATH C:\Users\Youcode\Herd\file-rouge\resources\views/Front-office/movies.blade.php ENDPATH**/ ?>