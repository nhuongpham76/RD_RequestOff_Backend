<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Status registed
     */
    const STATUS_REGISTED = 1;

    /**
     * Status using
     */
    const STATUS_USING = 2;

    /**
     * Status stopped
     */
    const STATUS_STOPPED = 3;

    /**
     * Role employee
     */
    const ROLE_EMPLOYEE = 1;

    /**
     * Role manager
     */
    const ROLE_MANAGER = 2;

    /**
     * Role hr
     */
    const ROLE_HR = 3;

    /**
     * Status
     *
     * @var array
     */
    public static $status = [
        self::STATUS_REGISTED,
        self::STATUS_USING,
        self::STATUS_STOPPED,
    ];

    /**
     * Status trans
     *
     * @var array
     */
    public static $statusTrans = [
        self::STATUS_REGISTED => "labels.user.status_registed",
        self::STATUS_USING => "labels.user.status_using",
        self::STATUS_STOPPED => "labels.user.status_stopped",
    ];

    /**
     * The database table used by the model.
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
        'team_id',
        'code',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role_id',
        'status'
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
     * User belongsTo Team
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * User belongsTo Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
