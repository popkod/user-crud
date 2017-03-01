# LARAVEL INSTALLATION #

composer install popcode/user-crud

Open up config/app.php and add the following to the providers key.
PopCode\UserCrud\Providers\UserCrudServiceProvider::class

# CONFIGURATION #
php artisan vendor:publish --provider="PopCode\UserCrud\Providers\UserCrudServiceProvider"
