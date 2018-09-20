<?php

namespace KABBOUCHI\TableView;

use Illuminate\Support\ServiceProvider;

class TableViewServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'tableView');

        $this->publishes([
            __DIR__.'/../config/tableView.php' => config_path('tableView.php'),
        ], 'tableView');

        $this->publishes([
            __DIR__.'/../views' => base_path('resources/views/vendor/tableView'),
        ], 'tableView');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tableView.php', 'tableView'
        );
    }
}
