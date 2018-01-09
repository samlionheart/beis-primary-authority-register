<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CloudFoundryStatsService;

class TestResultsServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('trs', function ($app) {
            return new TestResultsService();
        });
    }
}
