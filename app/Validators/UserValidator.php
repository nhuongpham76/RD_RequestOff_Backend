<?php

namespace App\Validators;

use App\Models\User;
use App\Validators\Contracts\ValidatorInterface;
use App\Validators\Traits\ExtendValidator;

class UserValidator extends LaravelValidator
{

    use ExtendValidator;

    /**
     * Rule validator
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'team_id' => 'required|exists:teams,id',
            'name' => 'required|max:255',
            'code' => 'required|max:20|unique:users,code',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|max:255',
            'password_confirmation' => 'required_with:password|same:password',
            'phone' => 'required|phone_number|max:20',
            'address' => 'required|max:255',
            'role_id' => 'required|exists:roles,id',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'team_id' => 'required|exists:teams,id',
            'name' => 'required|max:255',
            'password' => 'min:6|max:255',
            'password_confirmation' => 'required_with:password|same:password',
            'phone' => 'required|phone_number|max:20',
            'address' => 'required|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|integer|in:' . User::STATUS_REGISTED
                . User::STATUS_USING . ',' . User::STATUS_STOPPED,
        ]
    ];
}
