<?php

return [
    'table' => 'users',
    'model' => PopCode\UserCrud\Models\User::class,
    'controller' => PopCode\UserCrud\Controllers\UserController::class,
    'fillable' => [
        'email',
        'password'
    ],
    // register the /users route
    'register_default_routes' => false,
];
