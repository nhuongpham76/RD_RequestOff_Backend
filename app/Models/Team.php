<?php

namespace App\Models;

use App\Models\AbstractModel;

class Team extends AbstractModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'teams';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
