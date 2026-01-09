<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // TAMBAHKAN BARIS INI
use App\Models\User; // Pastikan baris ini merujuk ke App\Models\User

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
   Gate::define('is-superadmin', function (User $user) {
        return $user->role === 'superadmin';
    });
            \Illuminate\Support\Facades\Gate::define('is-approver', function($user) {
        return in_array($user->role, ['gm', 'direksi']);
    });

    
    
        

    }
    
}
