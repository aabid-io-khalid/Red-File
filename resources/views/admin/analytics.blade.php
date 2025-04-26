@extends('components.layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Analytics Dashboard</h1>
            <div class="flex space-x-4">
                <select id="dateRangeSelector" class="bg-gray-800 text-white px-4 py-2 rounded-lg">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">Last Year</option>
                </select>
                <button id="exportReportBtn" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                    Export Report
                </button>
            </div>
        </div>

        {{-- Key Metrics Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            {{-- Total Users --}}
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Total Users</h3>
                        <p class="text-2xl font-bold text-white">{{ number_format($totalUsers) }}</p>
                    </div>
                    <i class="ri-user-line text-3xl text-primary"></i>
                </div>
                <div class="mt-2 text-sm">
                    <span class="text-green-500">+{{ number_format($newUsersLastMonth) }} </span>
                    <span class="text-gray-400">this month</span>
                </div>
            </div>

            {{-- Total Subscriptions --}}
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Total Subscriptions</h3>
                        <p class="text-2xl font-bold text-white">{{ number_format($totalSubscriptions) }}</p>
                    </div>
                    <i class="ri-vip-crown-line text-3xl text-primary"></i>
                </div>
                <div class="mt-2 text-sm">
                    <span class="text-green-500">+{{ number_format($newSubscriptionsLastMonth) }} </span>
                    <span class="text-gray-400">this month</span>
                </div>
            </div>

            {{-- Total Comments --}}
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Total Comments</h3>
                        <p class="text-2xl font-bold text-white">{{ number_format($totalComments) }}</p>
                    </div>
                    <i class="ri-chat-3-line text-3xl text-primary"></i>
                </div>
                <div class="mt-2 text-sm">
                    <span class="text-green-500">+{{ number_format($newCommentsLastMonth) }} </span>
                    <span class="text-gray-400">this month</span>
                </div>
            </div>

            {{-- Popular Movies --}}
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Popular Movies</h3>
                        <p class="text-2xl font-bold text-white">{{ number_format($totalMovies) }}</p>
                    </div>
                    <i class="ri-movie-line text-3xl text-primary"></i>
                </div>
                <div class="mt-2 text-sm text-gray-400">
                    From TMDB Database
                </div>
            </div>
        </div>

        {{-- Charts and Detailed Analytics --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- User Growth Chart --}}
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700 max-w-full">
                <h3 class="text-xl font-semibold text-white mb-4">User Growth</h3>
                @if (empty($userGrowthLabels))
                    <p class="text-gray-400">No user growth data available.</p>
                @else
                    <canvas id="userGrowthChart" class="w-full max-w-2xl mx-auto"></canvas>
                @endif
            </div>

            {{-- Most Popular Movies --}}
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700 max-w-full">
                <h3 class="text-xl font-semibold text-white mb-4">Most Popular Movies</h3>
                @if ($totalMovies == 0)
                    <p class="text-gray-400">No movie data available. Check TMDB API connectivity.</p>
                @else
                    <table class="w-full">
                        <thead>
                            <tr class="text-gray-400 border-b border-gray-700">
                                <th class="text-left py-2">Title</th>
                                <th class="text-right py-2">Popularity</th>
                                <th class="text-right py-2">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mostWatchedMovies as $movie)
                                <tr class="border-b border-gray-700/50 hover:bg-gray-700/20">
                                    <td class="py-2 flex items-center">
                                        <img 
                                            src="{{ $movie['poster_url'] }}" 
                                            alt="{{ $movie['title'] }}" 
                                            class="w-10 h-15 object-cover rounded mr-3"
                                            onerror="this.src='https://via.placeholder.com/92x138'"
                                        >
                                        <span>{{ $movie['title'] }}</span>
                                    </td>
                                    <td class="text-right py-2">{{ number_format($movie['popularity'], 1) }}</td>
                                    <td class="text-right py-2">{{ number_format($movie['vote_average'], 1) }}/10</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Genre and Comment Analytics --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            {{-- Genre Distribution --}}
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700 max-w-full">
                <h3 class="text-xl font-semibold text-white mb-4">Genre Distribution</h3>
                @if (empty($genreDistribution))
                    <p class="text-gray-400">No genre data available. Check TMDB API connectivity.</p>
                @else
                    <canvas id="genreDistributionChart" class="w-full max-w-2xl mx-auto"></canvas>
                @endif
            </div>

            {{-- Comment Analytics --}}
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700 max-w-full">
                <h3 class="text-xl font-semibold text-white mb-4">Comment Analytics</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h4 class="text-gray-400 text-sm uppercase mb-2">Avg. Rating</h4>
                        <p class="text-xl font-bold text-white">{{ $avgCommentRating }} / 5</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h4 class="text-gray-400 text-sm uppercase mb-2">Total Comments</h4>
                        <p class="text-xl font-bold text-white">{{ number_format($totalComments) }}</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h4 class="text-gray-400 text-sm uppercase mb-2">Comments This Month</h4>
                        <p class="text-xl font-bold text-white">{{ number_format($newCommentsLastMonth) }}</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h4 class="text-gray-400 text-sm uppercase mb-2">Subscription Revenue</h4>
                        <p class="text-xl font-bold text-white">${{ number_format($totalSubscriptionRevenue, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        canvas {
            max-width: 100% !important;
            height: 320px !important;
            display: block !important;
            position: relative !important;
        }
        .chart-container {
            max-width: 100%;
            overflow: hidden;
        }
    </style>
@endpush

@push('scripts')
    <script>
        console.log('Checking for Chart.js...');
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded!');
        } else {
            console.log('Chart.js loaded successfully.');
        }

        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM fully loaded. Initializing charts...');
            try {
                // -- User Growth --
                const ugCtx = document.getElementById('userGrowthChart');
                console.log('UG Canvas:', ugCtx);
                const ugLabels = @json($userGrowthLabels ?? []);
                const ugData = @json($userGrowthData ?? []);
                console.log('UG Data:', ugLabels, ugData);
                if (ugCtx && ugLabels.length) {
                    new Chart(ugCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: ugLabels,
                            datasets: [{
                                label: 'New Users',
                                data: ugData,
                                borderColor: 'rgb(75,192,192)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { color: 'white' }, grid: { color: 'rgba(255,255,255,0.1)' } },
                                x: { ticks: { color: 'white' }, grid: { color: 'rgba(255,255,255,0.1)' } }
                            }
                        }
                    });
                    console.log('User Growth chart initialized.');
                } else {
                    console.warn('User Growth chart not initialized: Canvas or data missing');
                }

                // -- Genre Distribution --
                const gdCtx = document.getElementById('genreDistributionChart');
                console.log('GD Canvas:', gdCtx);
                const gdLabels = @json(array_column($genreDistribution ?? [], 'name'));
                const gdData = @json(array_column($genreDistribution ?? [], 'count'));
                console.log('GD Data:', gdLabels, gdData);
                if (gdCtx && gdLabels.length) {
                    new Chart(gdCtx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: gdLabels,
                            datasets: [{
                                data: gdData,
                                backgroundColor: [
                                    'rgb(255,99,132)', 'rgb(54,162,235)', 'rgb(255,206,86)',
                                    'rgb(75,192,192)', 'rgb(153,102,255)', 'rgb(255,159,64)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: { legend: { labels: { color: 'white' }, position: 'bottom' } }
                        }
                    });
                    console.log('Genre Distribution chart initialized.');
                } else {
                    console.warn('Genre Distribution chart not initialized: Canvas or data missing');
                }

                const dr = document.getElementById('dateRangeSelector');
                console.log('Date Range Selector:', dr);
                if (dr) {
                    dr.addEventListener('change', function() {
                        const selectedDays = this.value;
                        console.log('Fetching user growth for', selectedDays, 'days');
                        fetch(`/admin/analytics/update?days=${selectedDays}`)
                            .then(response => response.json())
                            .then(data => {
                                if (typeof userGrowthChart !== 'undefined') {
                                    userGrowthChart.data.labels = data.userGrowthLabels;
                                    userGrowthChart.data.datasets[0].data = data.userGrowthData;
                                    userGrowthChart.update();
                                    console.log('User Growth Chart updated with', selectedDays, 'days');
                                }
                            })
                            .catch(error => console.error('Error updating user growth chart:', error));
                    });
                }
            } catch (error) {
                console.error('Chart initialization error:', error);
            }
        });
    </script>
@endpush