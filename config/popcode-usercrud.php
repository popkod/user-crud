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
            'email' => 'required|email',
            'password' => 'confirmed',
        ],
        'store' => [
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ],
    ],
    'roles' => [
        1 => [
            'id' => 1,
            'label' => 'registered',
            'title' => 'Registered',
        ],
        2 => [
            'id' => 2,
            'label' => 'admin',
            'title' => 'Administrator',
        ],
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

    // string[] list of fields that always needs to be listed
    'meta_fields' => [],
];
