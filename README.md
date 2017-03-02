# LARAVEL INSTALLATION #

run 'composer install popcode/user-crud'

Open up config/app.php and add the following to the providers key.
PopCode\UserCrud\Providers\UserCrudServiceProvider::class

# CONFIGURATION #
to create default configuration in your config folder run:
- php artisan vendor:publish --provider="PopCode\UserCrud\Providers\UserCrudServiceProvider" --tag=config

in the config file you can enable default routes (/api/users) on key "register_default_routes"

you can enable user meta in the created popcode-usercrud.php file by set true value for key 'user_meta'
after that you can create the migration file to the user_metas table by calling the command:
- php artisan vendor:publish --provider="PopCode\UserCrud\Providers\UserCrudServiceProvider" --tag=migrations
- call 'php artisan migrate' to create the table

please run composer dump autoload after migrations are created to prevent any accidentally further creations

