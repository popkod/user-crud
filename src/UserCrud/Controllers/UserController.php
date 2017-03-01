<?php

namespace PopCode\UserCrud\Controllers;

use Config;
use Request;
use Illuminate\Routing\Controller as BaseController;
use PopCode\UserCrud\Interfaces\UserControllerInterface;

class UserController extends BaseController implements UserControllerInterface
{

    protected $model;

    public function __construct() {
        $this->model = Config::get('popcode-usercrud.model', \PopCode\UserCrud\Models\User::class);
    }

    public function index() {
        /* @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\User $model */
        $model = new $this->model;
        $users = $model->all();

        return $this->responseGenerator($users, 'index');
    }

    public function create() {
        return $this->responseGenerator(null, 'create');
    }

    public function store() {
        $data = Request::all();

        /* @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\User $model */
        $model = new $this->model;
        $validator = $model->validate($data);

        if ($validator->fails()) {
            return $this->errorResponseGenerator($data, $validator->messages(), 'store');
        }

        $user = new $this->model;
        $user->fill($data)
            ->save();

        return $this->responseGenerator($user, 'store');
    }

    public function show($id) {
        /* @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\User $model */
        $model = new $this->model;
        $user = $model->find($id);

        return $this->responseGenerator($user, 'show');
    }

    public function edit($id) {
        /* @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\User $model */
        $model = new $this->model;
        $user = $model->find($id);

        return $this->responseGenerator($user, 'show');
    }

    public function update($id) {
        $data = Request::all();

        /* @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\User $model */
        $model = new $this->model;
        $validator = $model->validate($data);

        if ($validator->fails()) {
            return $this->errorResponseGenerator($data, $validator->messages(), 'store');
        }

        $user = $model->find($id);
        $user->fill($data)
            ->save();

        return $this->responseGenerator($user, 'update');
    }

    public function destroy($id) {
        /* @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\User $model */
        $model = new $this->model;
        $user = $model->find($id);
        $user->email .= '_deleted';
        $user->delete();
    }


    protected function responseGenerator($responseData, $type = null) {
        if (Request::ajax() || Request::wantsJson()) {
            return response()->json($responseData);
        }

        // TODO return view by type
        return $responseData;
    }

    protected function errorResponseGenerator($data, $messages, $type = null, $status = 400) {
        if (Request::ajax() || Request::wantsJson()) {
            return response()->json(['error' => $messages], $status);
        }

        // TODO return view by type
        return $messages;
    }
}
