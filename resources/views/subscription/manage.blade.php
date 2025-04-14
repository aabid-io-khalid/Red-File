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
    
    <title>PELIXS - Manage Subscription</title>
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
