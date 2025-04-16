

<?php $__env->startSection('title', 'Analytics Dashboard - PELIXS Admin'); ?>

<?php $__env->startSection('page-title', 'Analytics Dashboard'); ?>

<?php $__env->startSection('body-attributes'); ?>
    x-data="analyticsData()"
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Analytics Dashboard</h1>
            <div class="flex space-x-4">
                <select id="dateRangeSelector" class="bg-dark-800 text-white px-4 py-2 rounded-lg border border-dark-700">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">Last Year</option>
                </select>
                <button id="exportReportBtn" @click="exportReport()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                    Export Report
                </button>
            </div>
        </div>

        <!-- Key Metrics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="stats-card bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-slide-up animate-delay-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Total Users</h3>
                        <p class="text-2xl font-bold text-white" x-text="formatNumber(stats.users)"><?php echo e(number_format($total_users ?? 0)); ?></p>
                    </div>
                    <div class="bg-primary/20 p-3 rounded-lg">
                        <i class="ri-user-line text-3xl text-primary"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm">
                    <span class="text-green-400">+<?php echo e(number_format($new_users_last_month ?? 0)); ?></span>
                    <span class="text-gray-400"> this month</span>
                </div>
            </div>

            <!-- Total Subscriptions -->
            <div class="stats-card bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-slide-up animate-delay-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Total Subscriptions</h3>
                        <p class="text-2xl font-bold text-white" x-text="formatNumber(stats.subscriptions)"><?php echo e(number_format($total_subscriptions ?? 0)); ?></p>
                    </div>
                    <div class="bg-secondary/20 p-3 rounded-lg">
                        <i class="ri-vip-crown-line text-3xl text-secondary"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm">
                    <span class="text-green-400">+<?php echo e(number_format($new_subscriptions_last_month ?? 0)); ?></span>
                    <span class="text-gray-400"> this month</span>
                </div>
            </div>

            <!-- Total Series -->
            <div class="stats-card bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-slide-up animate-delay-300">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Total Series</h3>
                        <p class="text-2xl font-bold text-white" x-text="formatNumber(stats.series)"><?php echo e(number_format($total_series ?? 0)); ?></p>
                    </div>
                    <div class="bg-purple-600/20 p-3 rounded-lg">
                        <i class="ri-film-line text-3xl text-purple-600"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-400">
                    From TMDB Database
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="stats-card bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-slide-up animate-delay-400">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Total Revenue</h3>
                        <p class="text-2xl font-bold text-white" x-text="'$' + formatNumber(stats.revenue)"><?php echo e('$' . number_format($total_subscription_revenue ?? 0, 2)); ?></p>
                    </div>
                    <div class="bg-accent/20 p-3 rounded-lg">
                        <i class="ri-money-dollar-circle-line text-3xl text-accent"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm">
                    <span class="text-green-400" x-text="stats.revenueGrowth + '%'"><?php echo e(number_format($revenue_growth ?? 0, 1)); ?>%</span>
                    <span class="text-gray-400"> vs last month</span>
                </div>
            </div>

            <!-- Total Comments -->
            <div class="stats-card bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-slide-up animate-delay-500">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Total Comments</h3>
                        <p class="text-2xl font-bold text-white" x-text="formatNumber(stats.comments)"><?php echo e(number_format($total_comments ?? 0)); ?></p>
                    </div>
                    <div class="bg-blue-500/20 p-3 rounded-lg">
                        <i class="ri-chat-3-line text-3xl text-blue-500"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm">
                    <span class="text-green-400">+<?php echo e(number_format($new_comments_last_month ?? 0)); ?></span>
                    <span class="text-gray-400"> this month</span>
                </div>
            </div>

            <!-- Popular Movies -->
            <div class="stats-card bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-slide-up animate-delay-600">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-400 text-sm uppercase">Popular Movies</h3>
                        <p class="text-2xl font-bold text-white" x-text="formatNumber(stats.movies)"><?php echo e(number_format($total_movies ?? 0)); ?></p>
                    </div>
                    <div class="bg-primary/20 p-3 rounded-lg">
                        <i class="ri-movie-line text-3xl text-primary"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-400">
                    From TMDB Database
                </div>
            </div>
        </div>

        <!-- Charts and Detailed Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- User Growth Chart -->
            <div class="bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-fade-in animate-delay-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-white">User Growth</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-xs rounded-md bg-dark-700 hover:bg-dark-600 transition-colors user-growth-filter" data-period="week" id="filter-week">Week</button>
                        <button class="px-3 py-1 text-xs rounded-md bg-primary text-white user-growth-filter" data-period="month" id="filter-month">Month</button>
                        <button class="px-3 py-1 text-xs rounded-md bg-dark-700 hover:bg-dark-600 transition-colors user-growth-filter" data-period="year" id="filter-year">Year</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>

            <!-- Content Distribution Chart -->
            <div class="bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-fade-in animate-delay-400">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-white">Content Distribution</h3>
                    <div class="text-xs text-gray-400">
                        <span class="inline-block w-3 h-3 rounded-full bg-primary mr-1"></span> Movies
                        <span class="inline-block w-3 h-3 rounded-full bg-secondary ml-3 mr-1"></span> Series
                    </div>
                </div>
                <div class="flex items-center justify-center h-64">
                    <canvas id="contentDistributionChart"></canvas>
                </div>
                <div class="mt-4 text-gray-400 text-sm">
                    <p>Movies: <?php echo e($content_distribution['movies'] ?? 0); ?></p>
                    <p>Series: <?php echo e($content_distribution['series'] ?? 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Genre Distribution and Most Popular Movies -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Genre Distribution - UPDATED: Increased height and improved visibility -->
            <div class="bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-fade-in animate-delay-500">
                <h3 class="text-xl font-semibold text-white mb-4">Genre Distribution</h3>
                <div class="h-80">
                    <canvas id="genreDistributionChart"></canvas>
                </div>
            </div>

            <!-- Most Popular Movies -->
            <div class="bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-fade-in animate-delay-600">
                <h3 class="text-xl font-semibold text-white mb-4">Most Popular Movies</h3>
                <?php if(empty($most_watched_movies)): ?>
                    <p class="text-gray-400">No movie data available. Check TMDB API connectivity.</p>
                <?php else: ?>
                    <table class="w-full">
                        <thead>
                            <tr class="text-gray-400 border-b border-dark-700">
                                <th class="text-left py-2">Title</th>
                                <th class="text-right py-2">Popularity</th>
                                <th class="text-right py-2">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $most_watched_movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b border-dark-700/50 hover:bg-dark-700/20">
                                    <td class="py-2 flex items-center">
                                        <img 
                                            src="<?php echo e($movie['poster_url']); ?>" 
                                            alt="<?php echo e($movie['title']); ?>" 
                                            class="w-10 h-15 object-cover rounded mr-3"
                                            onerror="this.src='https://via.placeholder.com/92x138'"
                                        >
                                        <span><?php echo e($movie['title']); ?></span>
                                    </td>
                                    <td class="text-right py-2"><?php echo e(number_format($movie['popularity'] ?? 0, 1)); ?></td>
                                    <td class="text-right py-2"><?php echo e(number_format($movie['vote_average'] ?? 0, 1)); ?>/10</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Activity -->
            <div class="lg:col-span-2 bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-fade-in animate-delay-700">
                <h3 class="text-xl font-semibold text-white mb-6">Recent Activity</h3>
                <div class="space-y-4">
                    <template x-for="(activity, index) in recentActivity" :key="index">
                        <div class="flex items-start space-x-4 p-3 rounded-lg transition-colors hover:bg-dark-700/50">
                            <div class="mt-1" :class="getActivityIconClass(activity.type)">
                                <i :class="getActivityIcon(activity.type)"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium" x-text="activity.message"></p>
                                <p class="text-xs text-gray-400 mt-1" x-text="activity.time"></p>
                            </div>
                            <div>
                                <span class="text-xs px-2 py-1 rounded-full" :class="getActivityStatusClass(activity.status)" x-text="activity.status"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="text-center mt-4">
                    <button class="text-sm text-primary hover:text-primary/80 transition-colors">View All Activity</button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-dark-800/60 backdrop-blur-sm rounded-xl border border-dark-700/50 p-6 animate-fade-in animate-delay-800">
                <h3 class="text-xl font-semibold text-white mb-6">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="/admin/movies" class="flex flex-col items-center justify-center p-4 bg-dark-700/50 hover:bg-dark-700 transition-all rounded-lg border border-dark-700">
                        <div class="bg-primary/20 p-3 rounded-full mb-2">
                            <i class="ri-add-circle-line text-xl text-primary"></i>
                        </div>
                        <span class="text-sm">Add Movie</span>
                    </a>
                    <a href="/admin/series" class="flex flex-col items-center justify-center p-4 bg-dark-700/50 hover:bg-dark-700 transition-all rounded-lg border border-dark-700">
                        <div class="bg-secondary/20 p-3 rounded-full mb-2">
                            <i class="ri-add-circle-line text-xl text-secondary"></i>
                        </div>
                        <span class="text-sm">Add Series</span>
                    </a>
                    <a href="/admin/users" class="flex flex-col items-center justify-center p-4 bg-dark-700/50 hover:bg-dark-700 transition-all rounded-lg border border-dark-700">
                        <div class="bg-purple-600/20 p-3 rounded-full mb-2">
                            <i class="ri-user-line text-xl text-purple-600"></i>
                        </div>
                        <span class="text-sm">See Users</span>
                    </a>
                </div>

                <!-- Comment Analytics -->
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-400 mb-4">Comment Analytics</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-dark-700 rounded-lg p-4">
                            <h5 class="text-gray-400 text-xs uppercase mb-2">Avg. Rating</h5>
                            <p class="text-lg font-bold text-white"><?php echo e(number_format($avg_comment_rating ?? 0, 1)); ?> / 5</p>
                        </div>
                        <div class="bg-dark-700 rounded-lg p-4">
                            <h5 class="text-gray-400 text-xs uppercase mb-2">Comments This Month</h5>
                            <p class="text-lg font-bold text-white"><?php echo e(number_format($new_comments_last_month ?? 0)); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        canvas {
            width: 100% !important;
            height: 100% !important;
        }
        .animate-slide-up {
            animation: slideUp 0.5s ease-out;
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-delay-100 { animation-delay: 0.1s; }
        .animate-delay-200 { animation-delay: 0.2s; }
        .animate-delay-300 { animation-delay: 0.3s; }
        .animate-delay-400 { animation-delay: 0.4s; }
        .animate-delay-500 { animation-delay: 0.5s; }
        .animate-delay-600 { animation-delay: 0.6s; }
        .animate-delay-700 { animation-delay: 0.7s; }
        .animate-delay-800 { animation-delay: 0.8s; }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Analytics Data Function
        function analyticsData() {
            return {
                stats: {
                    users: <?php echo e($total_users ?? 0); ?>,
                    subscriptions: <?php echo e($total_subscriptions ?? 0); ?>,
                    series: <?php echo e($total_series ?? 0); ?>,
                    revenue: <?php echo e($total_subscription_revenue ?? 0); ?>,
                    revenueGrowth: <?php echo e($revenue_growth ?? 0); ?>,
                    comments: <?php echo e($total_comments ?? 0); ?>,
                    movies: <?php echo e($total_movies ?? 0); ?>

                },
                charts: {},
                recentActivity: [],
                
                init() {
                    this.loadRecentActivity();
                    this.setupDateRangeSelector();
                    this.initCharts();
                    this.setupFilterButtons();
                },

                loadRecentActivity() {
                    this.recentActivity = [
                        { type: 'user', message: 'New user registered: John Smith', time: '5 minutes ago', status: 'New' },
                        { type: 'movie', message: 'Movie added: "The Last Adventure"', time: '1 hour ago', status: 'Added' },
                        { type: 'series', message: 'Series updated: "Dark Nights Season 2"', time: '3 hours ago', status: 'Updated' },
                        { type: 'payment', message: 'Payment received: $24.99 from user #5423', time: '5 hours ago', status: 'Completed' },
                        { type: 'comment', message: 'New comment on "Inception"', time: 'Yesterday', status: 'New' }
                    ];
                },

                initCharts() {
                    // Wait for DOM to be fully loaded
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', () => this.renderCharts());
                    } else {
                        this.renderCharts();
                    }
                },

                renderCharts() {
                    // Create User Growth Chart
                    const userGrowthCtx = document.getElementById('userGrowthChart');
                    if (userGrowthCtx) {
                        this.charts.userGrowth = new Chart(userGrowthCtx, {
                            type: 'line',
                            data: {
                                labels: <?php echo json_encode($user_growth_labels ?? [], 15, 512) ?>,
                                datasets: [{
                                    label: 'New Users',
                                    data: <?php echo json_encode($user_growth_data ?? [], 15, 512) ?>,
                                    borderColor: '#6d28d9',
                                    backgroundColor: 'rgba(109, 40, 217, 0.2)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { 
                                        beginAtZero: true,
                                        ticks: { color: '#d1d5db' },
                                        grid: { color: 'rgba(255,255,255,0.1)' }
                                    },
                                    x: { 
                                        ticks: { color: '#d1d5db' },
                                        grid: { display: false }
                                    }
                                },
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    }

                    // Create Content Distribution Chart
                    const contentDistCtx = document.getElementById('contentDistributionChart');
                    if (contentDistCtx) {
                        this.charts.contentDist = new Chart(contentDistCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Movies', 'Series'],
                                datasets: [{
                                    data: [<?php echo e($content_distribution['movies'] ?? 0); ?>, <?php echo e($content_distribution['series'] ?? 0); ?>],
                                    backgroundColor: ['#6d28d9', '#4f46e5'],
                                    borderColor: '#1e293b',
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { color: '#d1d5db' }
                                    }
                                }
                            }
                        });
                    }

                    // Create Genre Distribution Chart - UPDATED: Better color scheme with more distinct colors
                    const genreDistCtx = document.getElementById('genreDistributionChart');
                    if (genreDistCtx) {
                        const genres = <?php echo json_encode(array_column($genre_distribution ?? [], 'name') ?? [], 512) ?>;
                        const counts = <?php echo json_encode(array_column($genre_distribution ?? [], 'count') ?? [], 512) ?>;
                        
                        if (genres.length > 0 && counts.length > 0) {
                            this.charts.genreDist = new Chart(genreDistCtx, {
                                type: 'pie',
                                data: {
                                    labels: genres,
                                    datasets: [{
                                        data: counts,
                                        backgroundColor: [
                                            '#ef4444', // Red
                                            '#3b82f6', // Blue
                                            '#10b981', // Green
                                            '#f59e0b', // Orange
                                            '#8b5cf6', // Purple
                                            '#ec4899', // Pink
                                            '#06b6d4', // Cyan
                                            '#14b8a6', // Teal
                                            '#f43f5e', // Rose
                                            '#6366f1', // Indigo
                                            '#a855f7', // Violet
                                            '#d946ef'  // Fuchsia
                                        ],
                                        borderColor: '#1e293b',
                                        borderWidth: 2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'right',
                                            labels: { 
                                                color: '#d1d5db',
                                                font: {
                                                    size: 12
                                                },
                                                padding: 15
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                            titleColor: '#ffffff',
                                            bodyColor: '#d1d5db',
                                            borderColor: '#374151',
                                            borderWidth: 1,
                                            padding: 10
                                        }
                                    }
                                }
                            });
                        } else {
                            genreDistCtx.parentNode.innerHTML = '<p class="text-gray-400 text-center mt-8">No genre data available</p>';
                        }
                    }
                },

                setupFilterButtons() {
                    const buttons = document.querySelectorAll('.user-growth-filter');
                    buttons.forEach(button => {
                        button.addEventListener('click', (e) => {
                            // Remove active class from all buttons
                            buttons.forEach(btn => {
                                btn.classList.remove('bg-primary', 'text-white');
                                btn.classList.add('bg-dark-700', 'hover:bg-dark-600');
                            });
                            
                            // Add active class to clicked button
                            e.target.classList.remove('bg-dark-700', 'hover:bg-dark-600');
                            e.target.classList.add('bg-primary', 'text-white');
                            
                            // Get the period
                            const period = e.target.getAttribute('data-period');
                            this.updateChartPeriod(period);
                        });
                    });
                },

                updateChartPeriod(period) {
                    // Make AJAX request to get data for the selected period
                    fetch(`/admin/analytics/period/${period}`)
                        .then(response => response.json())
                        .then(data => {
                            if (this.charts.userGrowth) {
                                this.charts.userGrowth.data.labels = data.labels;
                                this.charts.userGrowth.data.datasets[0].data = data.values;
                                this.charts.userGrowth.update();
                            }
                        })
                        .catch(error => console.error('Error updating chart period:', error));
                },

                setupDateRangeSelector() {
                    const selector = document.getElementById('dateRangeSelector');
                    if (selector) {
                        selector.addEventListener('change', () => {
                            const days = selector.value;
                            this.updateDateRange(days);
                        });
                    }
                },

                updateDateRange(days) {
                    fetch(`/admin/analytics/update?days=${days}`)
                        .then(response => response.json())
                        .then(data => {
                            // Update all relevant stats
                            this.stats.users = data.total_users || 0;
                            this.stats.revenue = data.total_revenue || 0;
                            this.stats.revenueGrowth = data.revenue_growth || 0;
                            
                            // Update user growth chart
                            if (this.charts.userGrowth) {
                                this.charts.userGrowth.data.labels = data.user_growth_labels;
                                this.charts.userGrowth.data.datasets[0].data = data.user_growth_data;
                                this.charts.userGrowth.update();
                            }
                        })
                        .catch(error => console.error('Error updating date range:', error));
                },

                exportReport() {
                    const days = document.getElementById('dateRangeSelector').value;
                    window.location.href = `/admin/analytics/export?days=${days}`;
                },

                formatNumber(num) {
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                },

                getActivityIcon(type) {
                    const icons = {
                        user: 'ri-user-add-line',
                        movie: 'ri-movie-line',
                        series: 'ri-film-line',
                        payment: 'ri-money-dollar-circle-line',
                        comment: 'ri-chat-3-line'
                    };
                    return icons[type] || 'ri-information-line';
                },

                getActivityIconClass(type) {
                    const classes = {
                        user: 'text-primary bg-primary/20 p-2 rounded-full',
                        movie: 'text-secondary bg-secondary/20 p-2 rounded-full',
                        series: 'text-purple-600 bg-purple-600/20 p-2 rounded-full',
                        payment: 'text-accent bg-accent/20 p-2 rounded-full',
                        comment: 'text-blue-500 bg-blue-500/20 p-2 rounded-full'
                    };
                    return classes[type] || 'text-gray-400 bg-gray-700/20 p-2 rounded-full';
                },

                getActivityStatusClass(status) {
                    const classes = {
                        New: 'bg-blue-500/20 text-blue-400 border border-blue-500/50',
                        Added: 'bg-green-500/20 text-green-400 border border-green-500/50',
                        Updated: 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/50',
                        Completed: 'bg-accent/20 text-accent border border-accent/50'
                    };
                    return classes[status] || 'bg-gray-700/20 text-gray-400 border border-gray-700/50';
                }
            };
        }

        // Initialize any non-Alpine.js related JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // This ensures Chart.js has loaded before any other initialization
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded properly!');
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('components.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Youcode\Herd\file-rouge\resources\views/admin/index.blade.php ENDPATH**/ ?>