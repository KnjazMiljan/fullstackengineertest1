<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\HumanityAPI\HumanityAPICore;

class HumanityAPIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(HumanityAPICore::class, function() {
            return new HumanityAPICore(
                env('HUMANITY_APP_CLIENT_ID'),
                env('HUMANITY_APP_CLIENT_SECRET'),
                env('HUMANITY_APP_GRANT_TYPE'),
                env('HUMANITY_APP_USERNAME'),
                env('HUMANITY_APP_PASSWORD'),
                env('HUMANITY_APP_ACCESS_TOKEN'),
                env('HUMANITY_APP_REFRESH_TOKEN'),
                env('HUMANITY_APP_EXPIRY_TIMESTAMP')
            );
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
