<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <title>PELIXS - Subscription Success</title>
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
</head>
<body class="bg-darker text-white font-sans">
    <!-- Header -->
    <header class="bg-dark py-4 px-6 shadow-lg fixed top-0 w-full z-50">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logo -->
            <h1 class="text-3xl font-bold text-primary tracking-wider">PELIXS</h1>

            <!-- Navigation -->
            <nav class="hidden md:flex space-x-6">
                <a href="/home" class="hover:text-primary transition">Home</a>
                <a href="/browse" class="hover:text-primary transition">Browse</a>
                <a href="/movies" class="hover:text-primary transition">Movies</a>
                <a href="/shows" class="hover:text-primary transition">TV Shows</a>
                <a href="/anime" class="hover:text-primary transition">Anime</a>
                <a href="/mylist" class="hover:text-primary transition">My List</a>
                <a href="/community" class="hover:text-primary transition">Community</a>
            </nav>

            <!-- Profile & Notifications -->
            <div class="flex items-center space-x-4">
                <button class="text-xl p-2 rounded-full hover:bg-gray-800 transition">
                    <i class="ri-notification-3-line"></i>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profile-toggle" class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                        <i class="ri-user-line text-white"></i>
                    </button>
                    <div id="profile-dropdown" class="profile-dropdown absolute right-0 top-full mt-2 w-48 bg-dark border border-gray-700 rounded-lg shadow-lg hidden">
                        <ul class="py-1">
                            <li>
                                <a href="/profile" class="block px-4 py-2 hover:bg-gray-800 transition flex items-center">
                                    <i class="ri-user-line mr-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a href="/settings" class="block px-4 py-2 hover:bg-gray-800 transition flex items-center">
                                    <i class="ri-settings-3-line mr-2"></i> Settings
                                </a>
                            </li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                                   class="block px-4 py-2 hover:bg-gray-800 transition text-red-500 hover:text-red-400 flex items-center">
                                    <i class="ri-logout-box-r-line mr-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="min-h-screen pt-24 pb-16 flex items-center">
        <div class="container mx-auto px-4 text-center">
            <div class="bg-dark p-10 rounded-2xl max-w-2xl mx-auto shadow-lg border border-gray-800">
                <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ri-check-line text-4xl"></i>
                </div>
                
                <h1 class="text-3xl font-bold mb-4">Subscription Successful!</h1>
                <p class="text-xl text-gray-300 mb-8">Thank you for subscribing to PELIXS Premium. Your account has been activated.</p>
                
                <div class="bg-gray-900 p-6 rounded-xl mb-8">
                    <h2 class="text-xl font-semibold mb-4">Subscription Details</h2>
                    <div class="flex justify-between py-2 border-b border-gray-800">
                        <span class="text-gray-400">Plan</span>
                        <span>PELIXS Premium</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-800">
                        <span class="text-gray-400">Price</span>
                        <span>$2.00 / month</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-800">
                        <span class="text-gray-400">Status</span>
                        <span class="text-green-500">Active</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-400">Next billing date</span>
                        <span>{{ \Carbon\Carbon::now()->addMonth()->format('M d, Y') }}</span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/home" class="bg-primary hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">
                        Start Watching
                    </a>
                    <a href="/profile" class="bg-gray-800 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">
                        Manage Subscription
                    </a>
                </div>
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
        // Toggle profile dropdown
        document.getElementById('profile-toggle').addEventListener('click', function() {
            document.getElementById('profile-dropdown').classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#profile-toggle') && !event.target.closest('#profile-dropdown')) {
                document.getElementById('profile-dropdown').classList.add('hidden');
            }
        });
    </script>
</body>
</html>