<?php

namespace PopCode\UserCrud\Providers;

use Illuminate\Support\ServiceProvider;
use PopCode\UserCrud\Models\User;
use Config;
use Carbon\Carbon;

class UserCrudServiceProvider extends ServiceProvider
{
    protected $root;

    public function boot() {

        $root = __DIR__ . '/../../../';

        // enable create configuration
        $this->publishes(
            [
                $root . 'config/popcode-usercrud.php' => config_path('popcode-usercrud.php'),
            ],
            'config'
        );

        if (Config::get('popcode-usercrud.register_default_routes')) {
            // register routes
            $this->loadRoutesFrom($root . 'routes/popcode-usercrud-routes.php');
        }

        // add soft deletes to users
        $datePrefix = Carbon::now()->format('Y_m_d_His');
        if (!class_exists('AddSoftDeletesToUsers')) {
            $this->publishes(
                [
                    $root . 'migrations/2017_03_02_124652_add_soft_deletes_to_users.php' => database_path('migrations/' . $datePrefix . '_add_soft_deletes_to_users.php'),
                    $root . 'migrations/2017_03_02_124652_add_role_to_users.php' => database_path('migrations/' . $datePrefix . '_add_role_to_users.php'),
                ],
                'migrations'
            );
        }

        // enable create migrations
        if (Config::get('popcode-usercrud.user_meta')) {
            if (!class_exists('CreateUserMetaTable')) {
                $this->publishes(
                    [
                        $root . 'migrations/2017_03_02_124652_create_user_meta_table.php' => database_path('migrations/' . $datePrefix . '_create_user_meta_table.php'),
                    ],
                    'migrations'
                );
            }
        }

        if (!class_exists('PopcodeUserCrudCallFirstUserSeeder')) {
            $fresherDatePrefix = Carbon::now()->addSeconds(1)->format('Y_m_d_His');
            $this->publishes(
                [
                    $root . 'migrations/2017_03_02_124653_popcode_user_crud_call_first_user_seeder.php' => database_path('migrations/' . $fresherDatePrefix . '_popcode_user_crud_call_first_user_seeder.php'),
                    $root . 'seeds/PopcodeUserCrudFirstUserSeeder.php'                                  => database_path('seeds/PopcodeUserCrudFirstUserSeeder.php'),
                ],
                'migrations'
            );
        }

        // merge configuration
        $this->mergeConfigFrom($root . 'config/popcode-usercrud.php', 'popcode-usercrud');

        // register guard - email field
        $userClass = Config::get('popcode-usercrud.model', User::class);
        if (method_exists($userClass, 'registerRestoreGuear')) {
            $userClass::registerRestoreGuard();
        }

    }
}
