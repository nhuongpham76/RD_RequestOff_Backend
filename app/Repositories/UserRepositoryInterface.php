<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Create user
     *
     * @param array $data Data
     *
     * @return User
     */
    public function createUser(array $data);

    /**
     * Update user
     *
     * @param int   $id   User id
     * @param array $data Data
     * @param array $rule Rule
     *
     * @return User
     */
    public function updateUser(int $id, array $data, array $rule);

    /**
     * Show user
     *
     * @param int $id User id
     *
     * @return User
     */
    public function showUser(int $id);

    /**
     * List user
     *
     * @param array $data Data
     *
     * @return User
     */
    public function listUser(array $data);
}
