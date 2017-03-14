<?php

namespace PopCode\UserCrud\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Config;

class PCRoleController extends BaseController
{
    public function index() {
        $roles = Config::get('popcode-usercrud.roles', []);

        return response()->json($roles);
    }
}
