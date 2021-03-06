<?php

namespace HDSSolutions\Finpar;

use HDSSolutions\Laravel\Modules\ModuleServiceProvider;

class InventoryModuleServiceProvider extends ModuleServiceProvider {

    protected array $middlewares = [
        \HDSSolutions\Finpar\Http\Middleware\InventoryMenu::class,
    ];

    private $commands = [
        // \HDSSolutions\Finpar\Commands\Mix::class,
    ];

    public function bootEnv():void {
        // enable config override
        $this->publishes([
            module_path('config/inventory.php') => config_path('inventory.php'),
        ], 'inventory.config');

        // load routes
        $this->loadRoutesFrom( module_path('routes/inventory.php') );
        // load views
        $this->loadViewsFrom( module_path('resources/views'), 'inventory' );
        // load translations
        $this->loadTranslationsFrom( module_path('resources/lang'), 'inventory' );
        // load migrations
        $this->loadMigrationsFrom( module_path('database/migrations') );
        // load seeders
        $this->loadSeedersFrom( module_path('database/seeders') );
    }

    public function register() {
        // register helpers
        if (file_exists($helpers = realpath(__DIR__.'/helpers.php')))
            //
            require_once $helpers;
        // register singleton
        app()->singleton('inventory', fn() => new Inventory);
        // register commands
        $this->commands( $this->commands );
        // merge configuration
        $this->mergeConfigFrom( module_path('config/inventory.php'), 'inventory' );
    }

}
