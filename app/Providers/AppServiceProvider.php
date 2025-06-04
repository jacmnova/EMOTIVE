<?php

namespace App\Providers;

use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

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
        Gate::define('sa', function (User $user): bool {
            return $user->sa == 1;
        });

        Gate::define('admin', function (User $user): bool {
            return $user->admin == 1;
        });

        Gate::define('gestor', function (User $user): bool {
            return $user->gestor == 1;
        });

        Gate::define('usuario', function ($user) {
            return $user->usuario == 1;
        });

        // Ativa ou desativa a right_sidebar baseado no tipo de usuário
        if (Auth::check() && Auth::user()->sa) {
            Config::set('adminlte.right_sidebar', true);
        } else {
            Config::set('adminlte.right_sidebar', false);
        }
    }

}
