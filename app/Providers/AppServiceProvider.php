<?php

namespace App\Providers;

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

            \Illuminate\Support\Facades\Gate::define('is-approver', function($user) {
        return in_array($user->role, ['gm', 'direksi']);
    });

        
        \Illuminate\Support\Facades\Gate::define('is-superadmin', function($user) {
        return $user->role === 'superadmin';
    });
    
    }
    
}
