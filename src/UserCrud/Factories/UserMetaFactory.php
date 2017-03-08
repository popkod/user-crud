<?php

namespace PopCode\UserCrud\Factories;

use Illuminate\Support\Collection;

class UserMetaFactory
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\UserMeta
     */
    protected $model;

    protected $userId;

    protected $entities;

    public function __construct($model, $userId) {
        $this->model = $model;
        $this->userId = $userId;
    }

    public function create($items) {
        $createdEntities = [];
        array_walk($items, function($itemValue, $itemKey) use (&$createdEntities) {
            $createdEntities[] = $this->model->newInstance([
                'user_id' => $this->userId,
                'key'     => $itemKey,
                'value'   => $itemValue,
            ]);
        });

        $this->entities = collect($createdEntities);

        return $this;
    }

    public function get() {
        return $this->entities;
    }

    public function save() {
        if (!($this->entities instanceof Collection)) {
            return false;
        }

        $succeed = true;

        $this->entities->each(function($entity) use (&$succeed) {
            /* @var \Illuminate\Database\Eloquent\Model|\PopCode\UserCrud\Models\UserMeta $entity */
            $succeed = $entity->save() && $succeed;
        });

        return $succeed;
    }
}
