<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('admin', function (User $user): bool {
            return $user->admin == 1;
        });

        Gate::define('gestor', function (User $user): bool {
            return $user->gestor == 1;
        });

        Gate::define('usuario', function ($user) {
            return $user->usuario == 1;
        });

    }
}
