<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PELIXS - {{ $media['title'] ?? 'Details' }}</title>
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
        .rating-pill { background: rgba(255, 215, 0, 0.2); border: 1px solid rgba(255, 215, 0, 0.4); }
        .genre-pill { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px); }
        .banner-overlay { background: linear-gradient(to bottom, rgba(11, 11, 11, 0) 0%, rgba(11, 11, 11, 0.8) 60%, rgba(11, 11, 11, 1) 100%); }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(229, 9, 20, 0.5); }
    </style>
</head>
<body class="bg-darker text-white font-sans">
    <header class="bg-dark py-4 px-6 shadow-lg fixed top-0 w-full z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-primary tracking-wider">PELIXS</h1>
            <nav class="hidden md:flex space-x-6">
                <a href="/home" class="hover:text-primary transition">Home</a>
                <a href="/browse" class="hover:text-primary transition">Browse</a>
                <a href="/movies" class="hover:text-primary transition">Movies</a>
                <a href="/shows" class="hover:text-primary transition">TV Shows</a>
                <a href="/anime" class="hover:text-primary transition">Anime</a>
                <a href="/browse?list=favorites" class="hover:text-primary transition">My List</a>
            </nav>
            <div class="flex items-center space-x-4">
                <form id="search-form" action="/browse" method="get" class="flex items-center">
                    <input id="search-input" type="text" name="search" placeholder="Search movies & shows..." class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm focus:outline-none border border-gray-700 hidden">
                    <button type="button" id="search-toggle" class="text-xl p-2 rounded-full hover:bg-gray-800 transition">
                        <i class="ri-search-line"></i>
                    </button>
                </form>
                <button class="text-xl p-2 rounded-full hover:bg-gray-800 transition"><i class="ri-notification-3-line"></i></button>
                <div class="w-8 h-8 bg-primary rounded-full"></div>
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