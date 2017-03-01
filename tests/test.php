<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Popcode\UserCrud\Controllers\UserController;

/*
 * test cases:
 * - create user
 * - list users
 * - update user
 * - delete user
 * - list deleted users
 * - restore user
 * - restore if another user with same email exists
 */
