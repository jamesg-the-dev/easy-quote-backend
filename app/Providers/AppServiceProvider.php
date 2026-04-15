<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register authorization policies
        $this->registerPolicies();
    }

    /**
     * Register application policies.
     */
    protected function registerPolicies(): void
    {
        // Bind User model to UserPolicy
        // This allows use of $this->authorize() in controllers
        \Illuminate\Support\Facades\Gate::policy(User::class, UserPolicy::class);
    }
}
