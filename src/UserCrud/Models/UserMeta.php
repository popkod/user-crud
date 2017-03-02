<?php

namespace PopCode\UserCrud\Models;

use Config;
use Validator;
use Eloquent;

class UserMeta extends Eloquent
{
    protected static $sFillable = null;

    protected static $sTable = null;

    protected static $validationRules = [];

    protected static $sUserClass;

    protected $table = 'user_metas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function __construct(array $attributes = []) {
        $this->initTableName()
            ->initFillables();
        parent::__construct($attributes);
    }


    protected function initTableName() {
        if (is_null(static::$sTable)) {
            static::$sTable = Config::get('popcode-usercrud.meta_table');
        }
        $this->table = static::$sTable;

        return $this;
    }

    protected function initFillables() {
        if (is_null(static::$sFillable)) {
            static::$sFillable = Config::get(
                'popcode-usercrud.meta_fillable',
                [
                    'user_id',
                    'key',
                    'value',
                ]
            );
        }

        $this->fillable = static::$sFillable;

        return $this;
    }

    /**
     * @param mixed $data
     * @param string $type
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validate($data, $type = null) {
        if (is_null($type) || !property_exists(static::class, 'validationRules' . ucfirst($type))) {
            return Validator::make($data, static::$validationRules, []);
        }

        $property = 'validationRules' . ucfirst($type);

        return Validator::make($data, static::$$property);
    }

    public function user() {
        if (is_null(static::$sUserClass)) {
            static::$sUserClass = Config::get('popcode-usercrud.model', 'Popcode\\UserCrud\\Models\\User');
        }
        return $this->belongsTo(static::$sUserClass, 'user_id', 'id');
    }
}
