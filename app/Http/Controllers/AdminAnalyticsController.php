<?php

namespace App\Http\Controllers;

use App\Services\UserMetricsService;
use App\Services\SubscriptionMetricsService;
use App\Services\TmdbMetricsService;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminAnalyticsController extends Controller
{
    protected $userMetricsService;
    protected $subscriptionMetricsService;
    protected $tmdbMetricsService;

    public function __construct(
        UserMetricsService $userMetricsService,
        SubscriptionMetricsService $subscriptionMetricsService,
        TmdbMetricsService $tmdbMetricsService
    ) {
        // $this->middleware(['auth', 'role:admin']);
        $this->userMetricsService = $userMetricsService;
        $this->subscriptionMetricsService = $subscriptionMetricsService;
        $this->tmdbMetricsService = $tmdbMetricsService;
    }

    public function index()
    {
        try {
            $userMetrics = $this->userMetricsService->getMetrics();
            $subscriptionMetrics = $this->subscriptionMetricsService->getMetrics();
            $tmdbMetrics = $this->tmdbMetricsService->getMetrics();

            $totalComments = Comment::count();
            $avgCommentRating = Comment::whereNotNull('rating')->avg('rating') ?? 0;
            $newCommentsLastMonth = Comment::where('created_at', '>=', now()->subMonth())->count();

            $userGrowth = $this->userMetricsService->getUserGrowth(30);

            $data = array_merge(
                $userMetrics,
                $subscriptionMetrics,
                $tmdbMetrics,
                [
                    'total_comments' => $totalComments,
                    'avg_comment_rating' => $avgCommentRating,
                    'new_comments_last_month' => $newCommentsLastMonth,
                    'user_growth_labels' => $userGrowth['labels'],
                    'user_growth_data' => $userGrowth['data'],
                ]
            );

            Log::info('Analytics Metrics', $data);

            return view('admin.index', $data);
        } catch (\Exception $e) {
            Log::error('Error in AdminAnalyticsController::index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('admin.index', [
                'total_users' => 0,
                'new_users_last_month' => 0,
                'total_subscriptions' => 0,
                'new_subscriptions_last_month' => 0,
                'total_subscription_revenue' => 0,
                'revenue_growth' => 0,
                'total_comments' => 0,
                'avg_comment_rating' => 0,
                'new_comments_last_month' => 0,
                'total_movies' => 0,
                'total_series' => 0,
                'content_distribution' => ['movies' => 0, 'series' => 0],
                'most_watched_movies' => [],
                'genre_distribution' => [],
                'user_growth_labels' => [],
                'user_growth_data' => [],
            ]);
        }
    }

    public function exportReport(Request $request)
    {
        try {
            $userMetrics = $this->userMetricsService->getMetrics();
            $subscriptionMetrics = $this->subscriptionMetricsService->getMetrics();
            $tmdbMetrics = $this->tmdbMetricsService->getMetrics();

            $totalComments = Comment::count();
            $avgCommentRating = Comment::whereNotNull('rating')->avg('rating') ?? 0;
            $newCommentsLastMonth = Comment::where('created_at', '>=', now()->subMonth())->count();

            $days = $request->query('days', 30);
            $userGrowth = $this->userMetricsService->getUserGrowth($days);

            $data = array_merge(
                $userMetrics,
                $subscriptionMetrics,
                $tmdbMetrics,
                [
                    'total_comments' => $totalComments,
                    'avg_comment_rating' => $avgCommentRating,
                    'new_comments_last_month' => $newCommentsLastMonth,
                    'user_growth_labels' => $userGrowth['labels'],
                    'user_growth_data' => $userGrowth['data'],
                    'date_range' => $days,
                ]
            );

            $pdf = Pdf::loadView('admin.reports', $data);
            return $pdf->download('analytics-report-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error in AdminAnalyticsController::exportReport', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Failed to generate report. Please try again.');
        }
    }

    public function updateAnalytics(Request $request)
    {
        try {
            $days = $request->input('days', 30);
            $userGrowth = $this->userMetricsService->getUserGrowth($days);

            return response()->json([
                'user_growth_labels' => $userGrowth['labels'],
                'user_growth_data' => $userGrowth['data'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error in AdminAnalyticsController::updateAnalytics', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to update analytics'], 500);
        }
    }
}