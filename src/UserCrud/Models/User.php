<?php

namespace PopCode\UserCrud\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;
use Validator;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    public $token = '';

    protected static $sFillable = null;

    protected static $sTable = null;

    protected static $sUserModelClass;

    protected static $preprocessedMeta;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public function __construct(array $attributes = []) {
        $this->initTableName()
            ->initFillables()
            ->initPreprocessedMeta();
        parent::__construct($attributes);
    }

    public function meta() {
        if (is_null(static::$sUserModelClass)) {
            static::$sUserModelClass = Config::get('popcode-usercrud.meta_model', 'Popcode\\UserCrud\\Models\\UserMeta');
        }
        return $this->hasMany(static::$sUserModelClass);
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return bool
     */
    protected function runSoftDelete() {
        $query = $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey());

        $this->{$this->getDeletedAtColumn()} = $time = $this->freshTimestamp();

        return $query->update([
            $this->getDeletedAtColumn() => $this->fromDateTime($time),
            'email'                     => $this->email . '#deleted-' . $this->id,
        ]);
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore() {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = null;
        $this->email = substr($this->email, 0, strpos($this->email, '#'));

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }


    protected function initTableName() {
        if (is_null(static::$sTable)) {
            static::$sTable = Config::get('popcode-usercrud.table');
        }
        $this->table = static::$sTable;

        return $this;
    }

    protected function initFillables() {
        if (is_null(static::$sFillable)) {
            static::$sFillable = Config::get(
                'popcode-usercrud.fillable',
                [
                    'name',
                    'email',
                    'password',
                ]
            );
        }

        $this->fillable = static::$sFillable;

        return $this;
    }

    protected function initPreprocessedMeta() {
        if (is_null(static::$preprocessedMeta) && $fields = Config::get('popcode-usercrud.meta_fields')) {
            $keyArray = [];
            foreach ($fields as $field) {
                $keyArray[$field] = null;
            }
            static::$preprocessedMeta = $keyArray;
        }
        return $this;
    }

    /**
     * @param mixed $data
     * @param string $type
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validate($data, $type = null) {
        $rules = Config::get('popcode-usercrud.validation_rules.default', []);
        if ($type) {
            $rules = array_merge($rules, Config::get('popcode-usercrud.validation_rules.' . $type, []));
        }

        return Validator::make($data, $rules);
    }

    public static function registerRestoreGuard() {
        static::registerModelEvent('restoring', function ($user) {
            $email = substr($user->email, 0, strpos($user->email, '#'));
            return static::where('email', '=', $email)->count() === 0;
        });
    }


    public function toArray() {
        $array = parent::toArray();

        if (isset($array['meta']) && is_array($array['meta'])) {
            $preprocessed = static::$preprocessedMeta;
            foreach ($array['meta'] as $meta) {
                $preprocessed[$meta['key']] = $meta['value'];
            };

            $array['meta'] = $preprocessed;
        }

        if (!isset($array['role'])) {
            $array['role'] = 1;
        }

        $array['role_obj'] = Config::get('popcode-usercrud.roles.' . $array['role']);

        $array['token'] = $this->token;

        return $array;
    }
}
