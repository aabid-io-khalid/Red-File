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
    <title>PELIXS - My List</title>
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
                        <form id="search-form" action="/browse" method="get" class="flex items-center">
                            <div class="relative">
                                <input id="search-input" type="text" name="search" placeholder="Search..." 
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
                <?php if(!empty($movies) && count($movies) > 0): ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
                        <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="/movie/<?php echo e($movie['id']); ?>" class="block">
                                <div class="movie-card relative group cursor-pointer">
                                    <div class="aspect-[2/3] overflow-hidden rounded-xl relative">
                                        <img src="<?php echo e($movie['poster_path'] ? 'https://image.tmdb.org/t/p/w300' . $movie['poster_path'] : 'https://via.placeholder.com/300x450?text=No+Image'); ?>" alt="<?php echo e($movie['title']); ?>" class="card-image w-full h-full object-cover transition-all duration-300">
                                        <div class="card-overlay absolute inset-0 bg-black bg-opacity-50 opacity-0 transition-opacity flex items-center justify-center">
                                            <button class="play-button bg-primary text-white px-4 py-2 rounded-full transform translate-y-4 scale-75 transition-all duration-300">
                                                <i class="ri-play-fill text-2xl"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <h3 class="text-sm font-medium truncate"><?php echo e($movie['title']); ?></h3>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="rating-pill text-xs px-2 py-1 rounded-full text-yellow-400">
                                                <?php echo e(number_format($movie['vote_average'], 1)); ?> / 10
                                            </span>
                                            <span class="text-xs text-gray-400"><?php echo e($movie['release_date']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16 text-gray-400">
                        <i class="ri-movie-line text-6xl mb-4 block"></i>
                        <p>No favorite movies added.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TV Shows Section (Initially Hidden) -->
            <div id="shows-section" class="hidden">
                <?php if(!empty($tvShows) && count($tvShows) > 0): ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
                        <?php $__currentLoopData = $tvShows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $show): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="/shows/<?php echo e($show['id']); ?>" class="block">
                                <div class="movie-card relative group cursor-pointer">
                                    <div class="aspect-[2/3] overflow-hidden rounded-xl relative">
                                        <img src="<?php echo e($show['poster_path'] ? 'https://image.tmdb.org/t/p/w300' . $show['poster_path'] : 'https://via.placeholder.com/300x450?text=No+Image'); ?>" alt="<?php echo e($show['name']); ?>" class="card-image w-full h-full object-cover transition-all duration-300">
                                        <div class="card-overlay absolute inset-0 bg-black bg-opacity-50 opacity-0 transition-opacity flex items-center justify-center">
                                            <button class="play-button bg-primary text-white px-4 py-2 rounded-full transform translate-y-4 scale-75 transition-all duration-300">
                                                <i class="ri-play-fill text-2xl"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <h3 class="text-sm font-medium truncate"><?php echo e($show['name']); ?></h3>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="rating-pill text-xs px-2 py-1 rounded-full text-yellow-400">
                                                <?php echo e(number_format($show['vote_average'], 1)); ?> / 10
                                            </span>
                                            <span class="text-xs text-gray-400"><?php echo e($show['first_air_date']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16 text-gray-400">
                        <i class="ri-tv-line text-6xl mb-4 block"></i>
                        <p>No favorite TV shows added.</p>
                    </div>
                <?php endif; ?>
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
<?php /**PATH C:\Users\Youcode\Herd\file-rouge\resources\views/Front-office/mylist.blade.php ENDPATH**/ ?>