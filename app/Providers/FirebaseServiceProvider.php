<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('firebase.database', function ($app) {
            $serviceAccountPath = config('services.firebase.credentials');
            $databaseUrl = config('services.firebase.database_url');

            $factory = (new Factory)
                ->withServiceAccount($serviceAccountPath)
                ->withDatabaseUri($databaseUrl);

            return $factory->createDatabase();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
