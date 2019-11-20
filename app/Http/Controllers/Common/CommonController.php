<?php

namespace App\Http\Controllers\Common;

use App\Models\Team;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;

class CommonController extends Controller
{

    /**
     * List status of user
     *
     * @return Response
     */
    public function getStatusUser()
    {
        $status = formatResponseArray(transArr(User::$statusTrans));

        return $this->responseSuccess('', $status);
    }

    /**
     * List team
     *
     * @return Response
     */
    public function getListTeam()
    {
        $teams = formatResponseArray(Team::pluck('name', 'id')->toArray());

        return $this->responseSuccess('', $teams);
    }

    /**
     * List role
     *
     * @return Response
     */
    public function getListRole()
    {
        $roles = formatResponseArray(Role::pluck('name', 'id')->toArray());

        return $this->responseSuccess('', $roles);
    }
}
