<?php

namespace App\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    /**
     * Status object
     *
     * @var array
     */
    public static $statusObject = [
        User::STATUS_USING => 'label.status.using',
    ];

    /**
     * Role object
     *
     * @var array
     */
    public static $roleObject = [
        User::ROLE_SUPER_ADMIN => 'label.role.super_admin',
        User::ROLE_ADMIN => 'label.role.admin',
        User::ROLE_DRIVER => 'label.role.manager',
    ];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'created_at' => $this->created_at,
            'status' => $this->getStatusAttribute($this->status),
            'role' => $this->getRoleAttribute($this->role),
//            'permissions' => array_map(function ($permission) {
//                return $permission['name'];
//            }, $this->getAllPermissions()->toArray()),
//            'avatar' => 'http://i.pravatar.cc',
        ];
    }

    /**
     * Get object status
     *
     * @param int $value Value of status
     *
     * @return array
     */
    private function getStatusAttribute($value)
    {
        $value = $value ?? User::STATUS_USING;

        return [
            'value' => $value,
            'text' => trans(self::$statusObject[$value])
        ];
    }

    /**
     * Get object status
     *
     * @param int $value Value of status
     *
     * @return array
     */
    private function getRoleAttribute($value)
    {
        $value = $value ?? User::ROLE_SUPER_ADMIN;

        return [
            'value' => $value,
            'text' => trans(self::$roleObject[$value])
        ];
    }
}
