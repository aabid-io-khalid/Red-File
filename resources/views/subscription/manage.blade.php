<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.js"></script>
    <title>PELIXS - Manage Subscription</title>
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
                <!-- Auth -->
                <div class="flex items-center space-x-5">
                    <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit" class="logout-button text-white px-5 py-2 rounded-full flex items-center">
                            <i class="ri-logout-box-r-line mr-2"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>


    <main class="min-h-screen pt-24 pb-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <!-- Alert Messages -->
                @if(session('success'))
                <div class="bg-green-800/50 border border-green-600 text-green-100 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="ri-check-line mr-2 text-xl"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif
                
                @if(session('error'))
                <div class="bg-red-800/50 border border-red-600 text-red-100 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="ri-error-warning-line mr-2 text-xl"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
                @endif
                
                @if(session('info'))
                <div class="bg-blue-800/50 border border-blue-600 text-blue-100 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="ri-information-line mr-2 text-xl"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
                @endif
                
                <!-- Page Header -->
                <div class="border-b border-gray-800 pb-4 mb-6">
                    <h1 class="text-3xl font-bold">Manage Subscription</h1>
                </div>
                
                <!-- Subscription Details -->
                <div class="bg-dark rounded-xl p-6 mb-8">
                    <h2 class="text-xl font-semibold mb-6 flex items-center">
                        <i class="ri-vip-crown-line mr-2 text-primary"></i> Your Subscription
                    </h2>
                    
                    @if(!$subscription || $subscription->status !== 'active')
                        <div class="bg-gray-800/50 p-6 rounded-lg text-center mb-6">
                            <p class="text-gray-300 mb-4">You don't have an active subscription.</p>
                            <a href="{{ route('subscription') }}" class="inline-block bg-primary hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Get PELIXS Premium
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Plan</span>
                                <span>{{ $subscription->plan_name }}</span>
                            </div>
                            
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Status</span>
                                <span class="@if($subscription->isCanceled()) text-yellow-500 @else text-green-500 @endif">
                                    @if($subscription->isCanceled())
                                        Canceled (access until 
                                            @if($subscription->current_period_ends_at)
                                                {{ $subscription->current_period_ends_at->format('M d, Y') }}
                                            @else
                                                N/A
                                            @endif
                                        )
                                    @else
                                        Active
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Price</span>
                                <span>${{ number_format($subscription->amount, 2) }} / month</span>
                            </div>
                            
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Current period started</span>
                                <span>
                                    @if($subscription->current_period_starts_at)
                                        {{ $subscription->current_period_starts_at->format('M d, Y') }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex justify-between py-2 @if(!$subscription->isCanceled()) border-b border-gray-700 @endif">
                                <span class="text-gray-400">Next billing date</span>
                                <span>
                                    @if($subscription->current_period_ends_at)
                                        {{ $subscription->current_period_ends_at->format('M d, Y') }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            
                            @if(!$subscription->isCanceled())
                                <div class="pt-6">
                                    <form action="{{ route('subscription.cancel-subscription') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription? You will still have access until the end of your current billing period.');">
                                        @csrf
                                        <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition flex items-center justify-center">
                                            <i class="ri-close-circle-line mr-2"></i> Cancel Subscription
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Payment History -->
                @if($subscription && $subscription->status === 'active')
                    <div class="bg-dark rounded-xl p-6 mb-8">
                        <h2 class="text-xl font-semibold mb-6 flex items-center">
                            <i class="ri-bill-line mr-2 text-primary"></i> Payment History
                        </h2>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left border-b border-gray-700">
                                        <th class="pb-2">Date</th>
                                        <th class="pb-2">Amount</th>
                                        <th class="pb-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-800">
                                        <td class="py-3">
                                            @if($subscription->current_period_starts_at)
                                                {{ $subscription->current_period_starts_at->format('M d, Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="py-3">${{ number_format($subscription->amount, 2) }}</td>
                                        <td class="py-3"><span class="text-green-500">Paid</span></td>
                                    </tr>
                                    @if($subscription->created_at->diffInDays($subscription->current_period_starts_at) > 30)
                                        <tr class="border-b border-gray-800">
                                            <td class="py-3">
                                                @if($subscription->current_period_starts_at)
                                                    {{ $subscription->current_period_starts_at->copy()->subMonth()->format('M d, Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="py-3">${{ number_format($subscription->amount, 2) }}</td>
                                            <td class="py-3"><span class="text-green-500">Paid</span></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                
                <!-- Need Help Section -->
                <div class="bg-dark rounded-xl p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="ri-customer-service-2-line mr-2 text-primary"></i> Need Help?
                    </h2>
                    <p class="text-gray-300 mb-4">If you have any questions about your subscription or billing, our support team is here to help.</p>
                    <a href="/support" class="inline-block text-primary hover:text-red-600 font-semibold">Contact Support</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
