<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

    use Notifiable, SoftDeletes;
    /**
     * Role super admin
     *
     * @var int
     */
    const ROLE_SUPER_ADMIN = 1;
    /**
     * Role admin of company.
     *
     * @var int
     */
    const ROLE_ADMIN = 2;
    /**
     * Role driver of company.
     *
     * @var int
     */
    const ROLE_DRIVER = 3;
    /**
     * Status using.
     *
     * @var int
     */
    const STATUS_USING = 1;
    /**
     * Status stop.
     *
     * @var int
     */
    const STATUS_STOP = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'company_id',
        'address',
        'phone_number',
        'username',
        'status',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'Y-m-d H:i:s',
    ];

    /**
     * Status object
     *
     * @var array
     */
    public static $statusObject = [
        self::STATUS_USING => 'label.drivers.using',
        self::STATUS_STOP => 'label.drivers.stop',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Set password attribute
     *
     * @return mixed
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            return $this->attributes['password'] = bcrypt($value);
        }

        return $this;
    }

    /**
     * Plan belongsTo Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
