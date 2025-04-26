<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Log;

class SubscriptionMetricsService
{
    public function getMetrics()
    {
        try {
            $totalSubscriptions = Subscription::where('status', 'active')
                ->orWhere(function ($query) {
                    $query->where('status', 'canceled')->where('current_period_ends_at', '>=', now());
                })->count();

            $newSubscriptionsLastMonth = Subscription::where('created_at', '>=', now()->subMonth())
                ->where('status', 'active')
                ->orWhere(function ($query) {
                    $query->where('status', 'canceled')->where('current_period_ends_at', '>=', now());
                })->count();

            $totalSubscriptionRevenue = Subscription::where('status', 'active')
                ->orWhere(function ($query) {
                    $query->where('status', 'canceled')->where('current_period_ends_at', '>=', now());
                })->sum('amount');

            $previousRevenue = Subscription::where('status', 'active')
                ->where('created_at', '<', now()->subMonth())
                ->orWhere(function ($query) {
                    $query->where('status', 'canceled')
                          ->where('current_period_ends_at', '>=', now()->subMonth())
                          ->where('created_at', '<', now()->subMonth());
                })->sum('amount');

            $revenueGrowth = $previousRevenue ? (($totalSubscriptionRevenue - $previousRevenue) / $previousRevenue * 100) : 0;

            Log::info('Subscription metrics fetched', [
                'total_subscriptions' => $totalSubscriptions,
                'new_subscriptions_last_month' => $newSubscriptionsLastMonth,
                'total_subscription_revenue' => $totalSubscriptionRevenue,
                'revenue_growth' => $revenueGrowth,
            ]);

            return [
                'total_subscriptions' => $totalSubscriptions,
                'new_subscriptions_last_month' => $newSubscriptionsLastMonth,
                'total_subscription_revenue' => $totalSubscriptionRevenue,
                'revenue_growth' => $revenueGrowth,
            ];
        } catch (\Exception $e) {
            Log::error('Error in SubscriptionMetricsService::getMetrics', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'total_subscriptions' => 0,
                'new_subscriptions_last_month' => 0,
                'total_subscription_revenue' => 0,
                'revenue_growth' => 0,
            ];
        }
    }
}