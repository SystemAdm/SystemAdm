<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
            try {
                DB::connection('secondary')->getPdo();
                Config::set('database.default', 'mariadb_secondary');
            } catch (\Exception $e) {
                Config::set('database.default', 'mariadb');
            }
        }
    }
}
