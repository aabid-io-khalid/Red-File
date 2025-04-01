{{-- resources/views/admin/layout.blade.php --}}
@extends('components.layouts.admin')

{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Ycode Admin' }}</title> --}}
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    
    {{-- Alpine.js for interactivity --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Custom Styles --}}
    {{-- <style>
        [x-cloak] { display: none !important; }
        /* Additional custom styles */
    </style>  --}}
    
    @stack('styles')
</head>
<body class="bg-gray-900 text-white font-sans">
    {{-- Background with overlay --}}
    <div class="fixed inset-0 z-[-1]">
        <img src="{{ asset('assets/img/banner.png') }}" alt="background" class="object-cover w-full h-full opacity-20">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/80 to-gray-900"></div>
    </div>

    <div class="flex">
        {{-- Sidebar (from previous dashboard design) --}}
        <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gray-800/70 backdrop-blur-md z-50">
            {{-- Sidebar content from previous design --}}
            <div class="flex flex-col h-full justify-between p-4">
                <div class="mb-6">
                    <a href="#" class="text-2xl font-bold text-primary flex items-center">
                        <i class="ri-movie-2-line mr-2"></i>
                        <span>Ycode Admin</span>
                    </a>
                </div>
                
                <nav class="flex-grow">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.index') }}" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-dashboard-line text-lg mr-3 text-primary"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.index') }}" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-movie-line text-lg mr-3 text-primary"></i>
                                <span>Manage Movies</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.series') }}" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                                <i class="ri-film-line text-lg mr-3 text-primary"></i>
                                <span>Manage Series</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                {{-- Logout Section --}}
                <div class="border-t border-gray-700 pt-4">
                    {{-- <a href="{{ route('admin.logout') }}" class="flex items-center p-3 text-gray-100 rounded-lg hover:bg-gray-700/50 group transition-all">
                        <i class="ri-logout-box-r-line text-lg mr-3 text-primary"></i>
                        <span>Logout</span>
                    </a> --}}
                </div>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <main class="lg:ml-64 flex-1 p-6 w-full">
            @yield('content')
        </main>
    </div>

    {{-- Global Notifications --}}
    <div id="notifications" class="fixed top-4 right-4 z-[100] space-y-2">
        @if(session('success'))
            <div class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg">
                {{ session('error') }}
            </div>
        @endif
    </div>

    @stack('scripts')
</body>
</html>