<?php

namespace App\Services\Users;

use App\Services\Common\CommonService;
use App\Validators\UserValidator;
use App\Repositories\UserRepositoryInterface;
use App\Models\User;

class DestroyService
{

    /**
     * UserRepositoryInterface
     *
     * @var UserRepositoryInterface
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param UserRepositoryInterface $repository UserRepositoryInterface
     *
     * @return $this
     */
    public function __construct(
        UserRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Create user
     *
     * @param int $id User id
     *
     * @return User
     */
    public function handle(int $id)
    {
        return $this->repository->delete($id);
    }
}
