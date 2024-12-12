<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use PDO;

class AppServiceProvider extends ServiceProvider
{
    private array $options = [
        PDO::ATTR_TIMEOUT => 1,
    ];
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
            DB::connection('mariadb_primary')->getPdo();
            Config::set('database.default', 'mariadb_primary');
            Log::info('Bruker primær database: mariadb_primary');
        } catch (\Exception | QueryException $e) {
            Log::error('Feil i primær tilkobling: ' . $e->getMessage());

            try {
                DB::connection('mariadb_secondary')->getPdo();
                Config::set('database.default', 'mariadb_secondary');
                Log::info('Bruker sekundær database: mariadb_secondary');
            } catch (\Exception $e) {
                Log::error('Feil i sekundær tilkobling: ' . $e->getMessage());
                Config::set('database.default', 'mariadb');
                Log::info('Fallback til standard database: mariadb');
            }
        }
    }
}
