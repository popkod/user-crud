<?php

Illuminate\Support\Facades\Route::resource('/users', Config::get('popcode-usercrud.controller', 'fdsfdsfPopcode\UserCrud\Controllers\UserController::class'));
