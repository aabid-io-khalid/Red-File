<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        \Log::info("AuthServiceProvider boot method started");
        $this->registerPolicies();

        Gate::before(function (User $user, $ability) {
            \Log::info("Gate before check for User {$user->id}, ability: {$ability}");
        });

        Gate::define('add-to-my-list', function (User $user) {
            \Log::info("Running Gate 'add-to-my-list' for User {$user->id}");
            if (!$user->relationLoaded('roles')) {
                $user->load('roles');
            }
            $hasPremiumOrAdmin = $user->hasRole('premium') || $user->hasRole('admin');
            \Log::info("Inside Gate 'add-to-my-list' for User {$user->id}: hasRole('premium') or hasRole('admin') returns " . json_encode($hasPremiumOrAdmin));
            \Log::info("User {$user->id} roles inside Gate: ", $user->roles->pluck('name')->toArray());
            return $hasPremiumOrAdmin;
        });

        Gate::define('download-content', function (User $user) {
            return $user->hasRole('premium') || $user->hasRole('admin');
        });

        Gate::define('post-comment', function (User $user) {
            return true;
        });

        Gate::define('access-community-chat', function (User $user) {
            return $user->hasRole('premium') || $user->hasRole('admin');
        });

        \Log::info("AuthServiceProvider boot method completed");
    }
}