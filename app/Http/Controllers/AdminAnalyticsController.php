<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class AdminAnalyticsController extends Controller
{
    protected $traktClientId;

    public function __construct()
    {
        $this->traktClientId = config('services.trakt.client_id'); 
    }

    public function index()
    {
        $totalUsers = User::count();
        $newUsersLastMonth = User::where('created_at', '>=', now()->subMonth())->count();

        $traktMetrics = $this->fetchTraktMetrics();

        return view('admin.analytics', array_merge([
            'totalUsers' => $totalUsers,
            'newUsersLastMonth' => $newUsersLastMonth,
        ], $traktMetrics));
    }

    private function fetchTraktMetrics()
    {
        try {
            $popularMoviesResponse = Http::withHeaders([
                'trakt-api-version' => '2',
                'trakt-api-key' => $this->traktClientId,
            ])->get("https://api.trakt.tv/movies/popular");

            $popularMovies = $popularMoviesResponse->json() ?? [];

            $mostViewedContent = [];
            $totalRevenue = 0; 
            $genreDistribution = [];
            $genreTracker = [];

            foreach ($popularMovies as $movie) {
                $mostViewedContent[] = [
                    'title' => $movie['title'],
                    'poster_url' => $movie['images']['poster']['full'] ?? null,
                    'views' => $movie['watchers'] ?? 0, 
                    'revenue' => 0 
                ];

                foreach ($movie['genres'] as $genre) {
                    $genreTracker[$genre] = ($genreTracker[$genre] ?? 0) + 1;
                }
            }

            foreach ($genreTracker as $genre => $count) {
                $genreDistribution[$genre] = $count;
            }

            $userGrowthData = $this->simulateUser Growth();
            $userGrowthLabels = array_keys($userGrowthData);
            $userGrowthData = array_values($userGrowthData);

            return [
                'totalMovies' => count($popularMovies),
                'totalRevenue' => $totalRevenue,
                'mostViewedContent' => collect($mostViewedContent)->sortByDesc('views')->take(5),
                'maxViews' => max(collect($mostViewedContent)->pluck('views')->max(), 1),
                'genreDistribution' => $genreDistribution,
                'userGrowthLabels' => $userGrowthLabels,
                'userGrowthData' => $userGrowthData,
                
                'avgWatchTime' => 2.5, 
                'dailyActiveUsers' => rand(1000, 5000),
                'subscriptionRate' => rand(30, 70), 
                'retentionRate' => rand(40, 80), 
            ];
        } catch (\Exception $e) {
            \Log::error('Trakt Metrics Fetch Error: ' . $e->getMessage());
            return [
                'totalMovies' => 0,
                'totalRevenue' => 0,
                'mostViewedContent' => [],
                'maxViews' => 1,
                'genreDistribution' => [],
                'userGrowthLabels' => [],
                'userGrowthData' => [],
                'avgWatchTime' => 0,
                'dailyActiveUsers' => 0,
                'subscriptionRate' => 0,
                'retentionRate' => 0,
            ];
        }
    }

    private function simulateUser Growth($days = 30)
    {
        $userGrowth = [];
        $baseGrowth = rand(10, 50); 
        $endDate = now();
        $startDate = now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dailyUsers = $baseGrowth + rand(-5, 5);
            $userGrowth[$date->format('Y-m-d')] = max($dailyUsers, 0);
        }

        return $userGrowth;
    }

    public function updateAnalytics(Request $request)
    {
        $days = $request->input('days', 30);
        $userGrowthData = $this->simulateUser Growth($days);
        $userGrowthLabels = array_keys($userGrowthData);
        $userGrowthData = array_values($userGrowthData);

        return response()->json([
            'userGrowthLabels' => $userGrowthLabels,
            'userGrowthData' => $userGrowthData,
        ]);
    }
}