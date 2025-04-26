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
    <title>PELIXS - {{ $media['title'] ?? 'Details' }}</title>
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
        
        .rating-pill { background: rgba(255, 215, 0, 0.2); border: 1px solid rgba(255, 215, 0, 0.4); }
        .genre-pill { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px); }
        .banner-overlay { background: linear-gradient(to bottom, rgba(11, 11, 11, 0) 0%, rgba(11, 11, 11, 0.8) 60%, rgba(11, 11, 11, 1) 100%); }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(229, 9, 20, 0.5); }
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
                        <form id="search-form" action="/browse" method="get" class="flex items-center">
                            <div class="relative">
                                <input id="search-input" type="text" name="search" placeholder="Search movies & shows..." 
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



    <main class="pt-16">
        <div class="relative h-[70vh] w-full">
            @if($noInfoAvailable || !$media)
                <div class="absolute inset-0 flex items-center justify-center bg-dark">
                    <p class="text-gray-400 text-xl">No banner available</p>
                </div>
            @elseif($media && isset($media['poster']))
                <img src="{{ $media['poster'] }}" alt="{{ $media['title'] ?? 'Media' }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 banner-overlay"></div>
            @else
                <div class="absolute inset-0 flex items-center justify-center bg-dark">
                    <p class="text-gray-400 text-xl">No banner available</p>
                </div>
            @endif
        </div>

        <div class="container mx-auto px-4 py-6 -mt-16 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        @if($media)
                            @if(isset($media['rating']) && $media['rating'])
                                <div class="rating-pill text-sm px-3 py-1.5 rounded-full flex items-center">
                                    <i class="ri-star-fill text-yellow-500 mr-1"></i>
                                    <span>{{ number_format($media['rating'], 1) }}</span>
                                </div>
                            @endif
                            @if(isset($media['year']))
                                <div class="genre-pill text-sm px-3 py-1.5 rounded-full">
                                    {{ $media['year'] }}
                                </div>
                            @endif
                            @if(isset($media['type']) && $media['type'] === 'show')
                                <div class="genre-pill text-sm px-3 py-1.5 rounded-full flex items-center">
                                    <i class="ri-film-line mr-1"></i>
                                    <span>{{ $media['number_of_seasons'] ?? 0 }} season{{ ($media['number_of_seasons'] ?? 0) > 1 ? 's' : '' }}</span>
                                </div>
                            @endif
                            <div class="bg-primary text-white text-sm px-3 py-1.5 rounded-full">
                                HD
                            </div>
                        @else
                            <p class="text-gray-400">No information available</p>
                        @endif
                    </div>

                    <div class="mb-8">
                        @if($media)
                            <h2 class="text-3xl font-bold mb-4">{{ $media['title'] ?? 'Untitled' }}</h2>
                            @if(isset($media['description']))
                                <p class="text-gray-300 mb-4">{{ $media['description'] }}</p>
                            @endif
                        @else
                            <h2 class="text-3xl font-bold mb-4">No Title Available</h2>
                            <p class="text-gray-300 mb-4">No description available.</p>
                        @endif

                        <div class="flex flex-wrap gap-4">
                            <button class="bg-primary hover:bg-primary/90 px-6 py-3 rounded-lg font-medium flex items-center">
                                <i class="ri-play-fill mr-2 text-xl"></i> Watch Now
                            </button>
                            <button class="bg-gray-800 hover:bg-gray-700 px-6 py-3 rounded-lg font-medium flex items-center">
                                <i class="ri-share-line mr-2"></i> Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark mt-16 py-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <p class="text-center text-gray-400 text-sm">© 2025 PELIXS. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>