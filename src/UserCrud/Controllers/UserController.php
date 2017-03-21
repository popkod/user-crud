<?php

namespace PopCode\UserCrud\Controllers;

use Illuminate\Routing\Controller as BaseController;
use PopCode\UserCrud\Interfaces\UserControllerInterface;
use PopCode\UserCrud\Factories\UserMetaFactory;

class UserController extends BaseController implements UserControllerInterface
{

    /**
     * @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\User
     */
    protected $model;

    /**
     * @var bool|\Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\UserMeta
     */
    protected $metaModel = false;

    public function __construct($model = null, $metaModel = null) {
        if ($model) {
            $this->model = $model;
        } else {
            $this->model = \Config::get('popcode-usercrud.model', \PopCode\UserCrud\Models\User::class);
        }

        if (is_null($metaModel)) {
            if (\Config::get('popcode-usercrud.user_meta', false)) {
                $this->metaModel = \Config::get('popcode-usercrud.meta_model', \PopCode\UserCrud\Models\UserMeta::class);
            } else {
                $this->metaModel = false;
            }
        } else {
            $this->metaModel = $metaModel;
        }

        if (is_string($this->model)) {
            $modelClassName = $this->model;
            $this->model = new $modelClassName;
        }

        if (is_string($this->metaModel)) {
            $modelClassName = $this->metaModel;
            $this->metaModel = new $modelClassName;
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

        $errors = $this->hasError($data, 'update');
        if ($errors) {
            return $this->errorResponseGenerator($data, $errors, 'update');
        }

        $user = $this->updateUser($id, $data);

        return $this->responseGenerator($user, 'update');
    }

    public function destroy($id) {
        $succeed = $this->destroyUser($id);

        if ($succeed) {
            return $this->responseGenerator(null, 'destroy');
        }
        return $this->errorResponseGenerator(['id' => $id], [], 'destroy');
    }



    protected function indexUsers() {
        if ($this->metaModel) {
            $users = $this->model->with('meta')->get();
        } else {
            $users = $this->model->get();
        }
        return $users;
    }

    protected function showUser($id) {
        if ($this->metaModel) {
            $user = $this->model->with('meta')->where('id', '=', $id)->first();
        } else {
            $user = $this->model->where('id', '=', $id)->first();
        }
        return $user;
    }

    protected function storeUser($userData) {
        if (class_exists('\\Hash') && isset($userData['password'])) {
            $userData['password'] = \Hash::make($userData['password']);
        }
        /* @var \PopCode\UserCrud\Models\User $user */
        $user = $this->model->newInstance($userData);
        $user->save();

        if ($this->metaModel) {
            if (isset($userData['meta'])) {
                (new UserMetaFactory($this->metaModel, $user->id))->create($userData['meta'])->save();
            }
            $user->load('meta');
        }

        return $user;
    }

    protected function updateUser($id, $userData) {
        /* @var \PopCode\UserCrud\Models\User $user */
        $user = $this->model->where('id', '=', $id)->first();

        if (class_exists('\\Hash') && isset($userData['password'])) {
            $userData['password'] = \Hash::make($userData['password']);
        }
        $user->fill($userData);
        $user->save();

        if ($this->metaModel) {
            $user->meta()->delete();
            if (isset($userData['meta'])) {
                (new UserMetaFactory($this->metaModel, $user->id))->create($userData['meta'])->save();
            }
            $user->load('meta');
        }

        return $user;
    }

    protected function destroyUser($id) {
        /* @var \PopCode\UserCrud\Models\User $user */
        $user = $this->model->where('id', '=', $id)->first();
        return $user->delete() === true;
    }


    protected function hasError($userData, $type = null) {
        $errorMessages = [];

        $validator = $this->model->validate($userData, $type);

        if ($validator->fails()) {
            $errorMessages = $validator->messages()->toArray();
        }

        if ($this->metaModel) {
            $metaValidator = $this->metaModel->validate($userData, $type);

            if ($metaValidator->fails()) {
                $errorMessages = array_merge($errorMessages, $metaValidator->messages()->toArray());
            }
        }

        if (!empty($errorMessages)) {
            return $errorMessages;
        }

        return false;
    }



    protected function responseGenerator($responseData, $type = null) {
        if (is_null($responseData)) {
            return null;
        }

        return response()->json($responseData);

//        if (\Request::ajax() || \Request::wantsJson()) {
//            return response()->json($responseData);
//        }
//
//        // TODO return view by type
//        return $responseData;
    }

    protected function errorResponseGenerator($data, $messages, $type = null, $status = 400) {
        return response()->json(['error' => $messages], $status);

//        if (\Request::ajax() || \Request::wantsJson()) {
//            return response()->json(['error' => $messages], $status);
//        }
//
//        // TODO return view by type
//        return $messages;
    }
}
