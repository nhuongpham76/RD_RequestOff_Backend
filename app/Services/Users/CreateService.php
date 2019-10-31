<?php

namespace App\Services\Users;

use App\Validators\UserValidator;
use App\Repositories\UserRepositoryInterface;
use App\Models\User;

class CreateService
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
    public function handle(array $data = [])
    {
        $data['password'] = $this->getSaltPassword($data['password']);

        return $this->repository->createUser($data);
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
}
