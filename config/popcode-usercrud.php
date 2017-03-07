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
    'roles' => [
        'registered' => 1,
        'admin'      => 2,
    ],
    'default_admin' => [
        'name'     => 'Default Admin',
        'email'    => '',
        'password' => 'initial',
        'role'     => 2,
    ],
    'default_admin_metas' => [
        ['key' => 'created_by', 'value' => 'Core System'],
    ],

    'role_controller' => PopCode\UserCrud\Controllers\RoleController::class,
];
