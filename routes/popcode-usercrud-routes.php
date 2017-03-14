<?php

Illuminate\Support\Facades\Route::get(
    '/api/users/roles',
    Config::get('popcode-usercrud.role_controller', Popcode\UserCrud\Controllers\RoleController::class) . '@index'
);

Illuminate\Support\Facades\Route::resource(
    '/api/users',
    Config::get('popcode-usercrud.controller', Popcode\UserCrud\Controllers\UserController::class),
    ['except' => ['show']]
);

Illuminate\Support\Facades\Route::get(
    '/api/users/{id}',
    Config::get('popcode-usercrud.controller', Popcode\UserCrud\Controllers\UserController::class) . '@show'
)->where(['id' => '[0-9]+']);
