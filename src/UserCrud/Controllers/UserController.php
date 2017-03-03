<?php

namespace PopCode\UserCrud\Controllers;

use Illuminate\Routing\Controller as BaseController;
use PopCode\UserCrud\Interfaces\UserControllerInterface;

class UserController extends BaseController implements UserControllerInterface
{

    /**
     * @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\User
     */
    protected $model;

    public function __construct($model = null) {
        if ($model) {
            $this->model = $model;
        } else {
            $this->model = \Config::get('popcode-usercrud.model', \PopCode\UserCrud\Models\User::class);
        }

        if (is_string($this->model)) {
            $modelClassName = $this->model;
            $this->model = new $modelClassName;
        }
    }

    public function index() {
        $users = $this->indexUsers();

        return $this->responseGenerator($users, 'index');
    }

    public function create() {
        return $this->responseGenerator(null, 'create');
    }

    public function store() {
        $data = \Request::all();

        $errors = $this->hasError($data, 'store');
        if ($errors) {
            return $this->errorResponseGenerator($data, $errors, 'store');
        }

        $user = $this->storeUser($data);

        return $this->responseGenerator($user, 'store');
    }

    public function show($id) {
        $user = $this->showUser($id);

        return $this->responseGenerator($user, 'show');
    }

    public function edit($id) {
        $user = $this->showUser($id);

        return $this->responseGenerator($user, 'show');
    }

    public function update($id) {
        $data = \Request::all();

        $errors = $this->hasError($data, 'store');
        if ($errors) {
            return $this->errorResponseGenerator($data, $errors, 'store');
        }

        $user = $this->updateUser($id, $data);

        return $this->responseGenerator($user, 'update');
    }

    public function destroy($id) {
        $succeed = $this->destroyUser($id);

        if ($succeed) {
            return $this->responseGenerator([], 'destroy');
        }
        return $this->errorResponseGenerator(['id' => $id], [], 'destroy');
    }



    protected function indexUsers() {
        return $this->model->all();
    }

    protected function showUser($id) {
        $user = $this->model->where('id', '=', $id)->first();
        return $user;
    }

    protected function storeUser($userData) {
        /* @var \PopCode\UserCrud\Models\User $user */
        $user = $this->model->newInstance($userData);
        $user->save();

        return $user;
    }

    protected function updateUser($id, $userData) {
        /* @var \PopCode\UserCrud\Models\User $user */
        $user = $this->model->where('id', '=', $id)->first();

        $user->fill($userData);
        $user->save();

        return $user;
    }

    protected function destroyUser($id) {
        /* @var \PopCode\UserCrud\Models\User $user */
        $user = $this->model->where('id', '=', $id)->first();
        return $user->delete() === true;
    }


    protected function hasError($userData, $type = null) {
        $validator = $this->model->validate($userData, $type);

        if ($validator->fails()) {
            return $validator->messages();
        }

        return false;
    }



    protected function responseGenerator($responseData, $type = null) {
        if (\Request::ajax() || \Request::wantsJson()) {
            return response()->json($responseData);
        }

        // TODO return view by type
        return $responseData;
    }

    protected function errorResponseGenerator($data, $messages, $type = null, $status = 400) {
        if (\Request::ajax() || \Request::wantsJson()) {
            return response()->json(['error' => $messages], $status);
        }

        // TODO return view by type
        return $messages;
    }
}
