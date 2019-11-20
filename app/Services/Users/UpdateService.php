<?php

namespace App\Services\Users;

use App\Validators\UserValidator;
use App\Repositories\UserRepositoryInterface;
use App\Models\User;

class UpdateService
{

    /**
     * UserRepositoryInterface
     *
     * @var UserRepositoryInterface
     */
    protected $repository;

    /**
     * UserValidator
     *
     * @var UserValidator
     */
    private $validator;

    /**
     * Constructor.
     *
     * @param UserRepositoryInterface $repository UserRepositoryInterface
     * @param UserValidator           $validator  UserValidator
     *
     * @return $this
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Create user
     *
     * @param array $data Data
     *
     * @return User
     */
    public function handle(int $id, array $data = [])
    {
        if ($data['password']) {
            $data['password_encrypted'] = $this->getSaltPassword($data['password']);
        }

        $rule = $this->getRule($id);

        return $this->repository->updateUser($id, $data, $rule);
    }

    /**
     * Get salt password
     *
     * @param string $password String need hash
     *
     * @return string
     */
    private function getSaltPassword(string $password = "") : string
    {
        return sha1($password . config('define.default_salt_password'));
    }

    /**
     * Get rule validate user
     *
     * @param int $id User id
     *
     * @return array
     */
    private function getRule(int $id)
    {
        return [
            'email' => 'required|email|max:255||unique:users,email,' . $id . ',id',
            'code' => 'required|max:20|unique:users,code,' . $id . ',id',
        ];
    }
}
