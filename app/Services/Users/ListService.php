<?php

namespace App\Services\Users;

use App\Services\Common\CommonService;
use App\Repositories\UserRepositoryInterface;
use App\Models\User;

class ListService
{

    /**
     * UserRepositoryInterface
     *
     * @var UserRepositoryInterface
     */
    protected $repository;

    /**
     * CommonService
     *
     * @var CommonService
     */
    protected $commonService;

    /**
     * Constructor.
     *
     * @param UserRepositoryInterface $repository    UserRepositoryInterface
     * @param CommonService           $commonService CommonService
     *
     * @return $this
     */
    public function __construct(
        UserRepositoryInterface $repository,
        CommonService $commonService
    ) {
        $this->repository = $repository;
        $this->commonService = $commonService;
    }

    /**
     * Create user
     *
     * @param array $search Search
     * @param array $sort   Sort
     *
     * @return User
     */
    public function handle(array $search, array $sort)
    {
        $sortColumns = $columns = [
            'team_id',
            'code',
            'name',
            'email',
            'phone',
            'address',
            'role_id',
            'created_at'
        ];

        list($sortColumn, $sortDirection) = array_values(
            $this->commonService->getSortData($sort, $sortColumns, 'updated_at')
        );

        return $this->repository->listUser([
            'search' => $search,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'columns' => $columns,
            'sort' => $sort,
        ]);
    }
}
