<?php

Illuminate\Support\Facades\Route::resource('/api/users', Config::get('popcode-usercrud.controller', 'fdsfdsfPopcode\UserCrud\Controllers\UserController::class'));
