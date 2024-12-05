<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

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
        $this->setDatabaseConnection();
        if (auth()->check()) {
            setPermissionsTeamId(1);
        }/*
        Gate::after(function ($user, $ability) {
            return $user->hasRole('Administrator');
        });*/
        Cashier::useCustomerModel(User::class);
    }

    function setDatabaseConnection(): void
    {
        try {
            DB::connection('primary')->getPdo();
            Config::set('database.default', 'mariadb_primary');
        } catch (\Exception $e) {
            Config::set('database.default', 'mariadb_secondary');
        }
    }
}
