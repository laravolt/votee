<?php

namespace Laravolt\Votee;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class VoteeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // use this if your package has views
        $this->loadViewsFrom(realpath(__DIR__.'/../resources/views'), 'votee');

        // use this if your package has routes
        $this->setupRoutes($this->app->router);

        // use this if your package needs a config file
         $this->publishes([
                 __DIR__.'/../config/votee.php' => config_path('votee.php'),
                 __DIR__.'/../migrations/create_voteable_tables.php' => database_path('migrations/' . '2015_08_17_101000_create_voteable_tables.php'),
         ]);

        // use the vendor configuration file as fallback
         $this->mergeConfigFrom(
             __DIR__.'/../config/votee.php', 'votee'
         );
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Laravolt\Votee\Http\Controllers'], function($router)
        {
            require __DIR__.'/Http/routes.php';
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerVotee();

        // use this if your package has a config file
         config([
                 'config/votee.php',
         ]);
    }

    private function registerVotee()
    {
        $this->app->bind('votee',function($app){
            return new Votee($app);
        });
    }
}
