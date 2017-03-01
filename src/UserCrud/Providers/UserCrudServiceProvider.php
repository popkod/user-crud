<?php

namespace PopCode\UserCrud\Providers;

use Illuminate\Support\ServiceProvider;
use PopCode\UserCrud\Models\User;
use Config;

class UserCrudServiceProvider extends ServiceProvider
{

    public function boot() {
        
        $root = __DIR__ . '/../../';

        // enable create configuration
        $this->publishes([
            $root . 'config/popcode-usercrud.php' => config_path('popcode-usercrud.php'),
        ]);

        if (Config::get('popcode-usercrud.register_default_routes')) {
            // register routes
            $this->loadRoutesFrom($root . 'routes/popcode-usercrud-routes.php');
        }

        $this->loadMigrationsFrom($root . 'migrations');

        // merge configuration
        $this->mergeConfigFrom($root . 'config/popcode-usercrud.php', 'popcode-usercrud');

        // register guard - email field
        User::registerRestoreGuard();
    }
}
