<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserMetricsService
{
    public function getMetrics()
    {
        try {
            $totalUsers = User::count();
            $newUsersLastMonth = User::where('created_at', '>=', now()->subMonth())->count();

            Log::info('User metrics fetched', [
                'total_users' => $totalUsers,
                'new_users_last_month' => $newUsersLastMonth,
            ]);

            return [
                'total_users' => $totalUsers,
                'new_users_last_month' => $newUsersLastMonth,
            ];
        } catch (\Exception $e) {
            Log::error('Error in UserMetricsService::getMetrics', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'total_users' => 0,
                'new_users_last_month' => 0,
            ];
        }
    }

    public function getUserGrowth($days = 30)
    {
        try {
            $userGrowth = [];
            $startDate = now()->subDays($days);
            $endDate = now();

            $usersByDate = User::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date')
                ->toArray();

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                $userGrowth[$dateStr] = $usersByDate[$dateStr] ?? 0;
            }

            Log::info('User growth data fetched', ['days' => $days, 'data' => $userGrowth]);

            return [
                'labels' => array_keys($userGrowth),
                'data' => array_values($userGrowth),
            ];
        } catch (\Exception $e) {
            Log::error('Error in UserMetricsService::getUserGrowth', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'labels' => [],
                'data' => [],
            ];
        }
    }
}