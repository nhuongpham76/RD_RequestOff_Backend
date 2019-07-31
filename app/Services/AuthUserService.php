<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AuthUserService
{
    use Authenticatable;

    /**
     * Constructor.
     *
     * @param string $authType Auth type
     *
     * @return void
     */
    function __construct($authType = 'customer')
    {
        if ('customer' === strtolower($authType)) {
            Config::set('jwt.user', Customer::class);
            Config::set('auth.providers', ['users' => [
                'driver' => 'eloquent',
                'model' => Customer::class,
            ]]);
        } else {
            Config::set('jwt.user', User::class);
            Config::set('auth.providers', ['users' => [
                'driver' => 'eloquent',
                'model' => User::class,
            ]]);
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param array $columns Columns
     *
     * @return Auth
     *
     * @throws AuthenticationException
     */
    public function getAuthUser(array $columns = ['*'])
    {
        if (!Auth::user()) {
            throw new AuthenticationException(trans('auth.auth_fail'));
        }

        return Auth::user()->setVisible($columns);
    }
}
