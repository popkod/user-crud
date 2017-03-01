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

    protected static $sFillable = null;

    protected static $sTable = null;

    protected static $validationRules = [];

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

    protected $dates = [
        'deleted_at',
    ];

    public function __construct(array $attributes = []) {
        $this->initTableName()
            ->initFillables();
        parent::__construct($attributes);
    }


    /**
     * Perform the actual delete query on this model instance.
     *
     * @return bool
     */
    protected function runSoftDelete()
    {
        $query = $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey());

        $this->{$this->getDeletedAtColumn()} = $time = $this->freshTimestamp();

        return $query->update([
            $this->getDeletedAtColumn() => $this->fromDateTime($time),
            $this->email => $this->email . '#deleted-' . $this->id
        ]);
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore()
    {
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

    /**
     * @param mixed $data
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

    public static function registerRestoreGuard() {
        static::registerModelEvent('restoring', function($user) {
            $email = substr($user->email, 0, strpos($user->email, '#'));
            return static::where('email', '=', $email)->count() === 0;
        });
    }
}
