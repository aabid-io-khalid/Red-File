{{-- analytics/index.blade.php --}}
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
            <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                Export Report
            </button>
        </div>
    </div>

    {{-- Key Metrics Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
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
        <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-400 text-sm uppercase">Total Views</h3>
                    <p class="text-2xl font-bold text-white">{{ number_format($totalViews) }}</p>
                </div>
                <i class="ri-eye-line text-3xl text-primary"></i>
            </div>
            <div class="mt-2 text-sm">
                <span class="text-green-500">+{{ number_format($viewsLastMonth) }} </span>
                <span class="text-gray-400">this month</span>
            </div>
        </div>
        <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-400 text-sm uppercase">Total Content</h3>
                    <p class="text-2xl font-bold text-white">
                        {{ number_format($totalMovies + $totalSeries) }}
                    </p>
                </div>
                <i class="ri-movie-line text-3xl text-primary"></i>
            </div>
            <div class="mt-2 text-sm">
                <span class="text-gray-400">
                    {{ number_format($totalMovies) }} Movies, 
                    {{ number_format($totalSeries) }} Series
                </span>
            </div>
        </div>
        <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-400 text-sm uppercase">Revenue</h3>
                    <p class="text-2xl font-bold text-white">${{ number_format($totalRevenue) }}</p>
                </div>
                <i class="ri-money-dollar-circle-line text-3xl text-primary"></i>
            </div>
            <div class="mt-2 text-sm">
                <span class="text-green-500">+{{ number_format($revenueLastMonth) }} </span>
                <span class="text-gray-400">this month</span>
            </div>
        </div>
    </div>

    {{-- Charts and Detailed Analytics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- User Growth Chart --}}
        <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
            <h3 class="text-xl font-semibold text-white mb-4">User Growth</h3>
            <div id="userGrowthChart" class="h-80"></div>
        </div>

        {{-- Content Popularity --}}
        <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
            <h3 class="text-xl font-semibold text-white mb-4">Most Viewed Content</h3>
            <table class="w-full">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left py-2">Title</th>
                        <th class="text-right py-2">Views</th>
                        <th class="text-right py-2">Popularity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mostViewedContent as $content)
                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/20">
                        <td class="py-2 flex items-center">
                            <img 
                                src="{{ $content->poster_url }}" 
                                alt="{{ $content->title }}" 
                                class="w-10 h-15 object-cover rounded mr-3"
                            >
                            <span>{{ $content->title }}</span>
                        </td>
                        <td class="text-right py-2">{{ number_format($content->views) }}</td>
                        <td class="text-right py-2">
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div 
                                    class="bg-primary h-2 rounded-full" 
                                    style="width: {{ min(($content->views / $maxViews) * 100, 100) }}%"
                                ></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Genre and Category Analytics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- Genre Distribution --}}
        <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
            <h3 class="text-xl font-semibold text-white mb-4">Genre Distribution</h3>
            <div id="genreDistributionChart" class="h-80"></div>
        </div>

        {{-- User Engagement --}}
        <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-6 border border-gray-700">
            <h3 class="text-xl font-semibold text-white mb-4">User Engagement</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-700 rounded-lg p-4">
                    <h4 class="text-gray-400 text-sm uppercase mb-2">Avg. Watch Time</h4>
                    <p class="text-xl font-bold text-white">{{ number_format($avgWatchTime, 2) }} hrs</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-4">
                    <h4 class="text-gray-400 text-sm uppercase mb-2">Daily Active Users</h4>
                    <p class="text-xl font-bold text-white">{{ number_format($dailyActiveUsers) }}</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-4">
                    <h4 class="text-gray-400 text-sm uppercase mb-2">Subscription Rate</h4>
                    <p class="text-xl font-bold text-white">{{ number_format($subscriptionRate, 2) }}%</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-4">
                    <h4 class="text-gray-400 text-sm uppercase mb-2">Retention Rate</h4>
                    <p class="text-xl font-bold text-white">{{ number_format($retentionRate, 2) }}%</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: @json($userGrowthLabels),
            datasets: [{
                label: 'New Users',
                data: @json($userGrowthData),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: 'white' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                x: {
                    ticks: { color: 'white' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            }
        }
    });

    // Genre Distribution Chart
    const genreDistributionCtx = document.getElementById('genreDistributionChart').getContext('2d');
    new Chart(genreDistributionCtx, {
        type: 'pie',
        data: {
            labels: @json(array_keys($genreDistribution)),
            datasets: [{
                data: @json(array_values($genreDistribution)),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 206, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)',
                    'rgb(255, 159, 64)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { 
                    labels: { color: 'white' },
                    position: 'bottom'
                }
            }
        }
    });

    document.getElementById('dateRangeSelector').addEventListener('change', function() {
        const selectedDays = this.value;
        fetch(`/admin/analytics/update?days=${selectedDays}`)
            .then(response => response.json())
            .then(data => {
                updateCharts(data);
            });
    });

    function updateCharts(data) {
        userGrowthChart.data.labels = data.userGrowthLabels;
        userGrowthChart.data.datasets[0].data = data.userGrowthData;
        userGrowthChart.update();

        genreDistributionChart.data.labels = Object.keys(data.genreDistribution);
        genreDistributionChart.data.datasets[0].data = Object.values(data.genreDistribution);
        genreDistributionChart.update();
    }
</script>
@endpush