<?php

return [
    // register the /users route
    'register_default_routes' => false,

    'user_meta' => false,

    'table' => 'users',
    'model' => PopCode\UserCrud\Models\User::class,
    'meta_table' => 'user_metas',
    'meta_model' => PopCode\UserCrud\Models\UserMeta::class,
    'controller' => PopCode\UserCrud\Controllers\UserController::class,
    'fillable' => [
        'name',
        'email',
        'password'
    ],
    'validation_rules' => [
        'default' => [
            'name' => 'string|max:250',
            'email' => 'required|unique:users',
            'password' => 'required',
        ],
        'create' => [
            'password' => 'required|confirmed',
        ],
    ],
];
