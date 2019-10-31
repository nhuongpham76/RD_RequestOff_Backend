<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\Traits\EloquentTransactional;
use App\Validators\UserValidator;
use DB;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{

    use EloquentTransactional;

    /**
     * Specify Model class name
     *
     * @return User
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Specify validator class name
     *
     * @return mixed
     */
    public function validator()
    {
        return UserValidator::class;
    }

    /**
     * Attribute seachable
     *
     * @var array
     */
    protected $fieldSearchable = [
        'team_id' => ['column' => 'users.team_id', 'operator' => '=', 'type' => 'normal'],
        'code' => ['column' => 'users.code', 'operator' => 'ilike', 'type' => 'normal'],
        'status' => ['column' => 'users.status', 'operator' => '=', 'type' => 'normal'],
        'name' => ['column' => 'users.name', 'operator' => 'ilike', 'type' => 'normal'],
        'address' => ['column' => 'users.address', 'operator' => 'ilike', 'type' => 'normal'],
        'email' => ['column' => 'users.email', 'operator' => 'ilike', 'type' => 'normal'],
    ];

    /**
     * Create user
     *
     * @param array $data Data
     *
     * @return User
     */
    public function createUser(array $data)
    {
        $this->validator->setRules([], UserValidator::RULE_CREATE);

        return $this->create($data);
    }

    /**
     * Update user
     *
     * @param int   $id   User id
     * @param array $data Data
     * @param array $rule Rule
     *
     * @return User
     */
    public function updateUser(int $id, array $data, array $rule)
    {
        $this->validator->setRules($rule, UserValidator::RULE_UPDATE);

        return $this->update($data, $id);
    }

    /**
     * Show user
     *
     * @param int $id User id
     *
     * @return User
     */
    public function showUser(int $id)
    {
        return User::join('teams', 'teams.id', '=', 'users.team_id')
            ->find($id, [
                'teams.name as team',
                'users.name',
                'users.code',
                'users.email',
                'users.phone',
                'users.address',
                'users.role',
                'users.created_at',
            ]);
    }

    /**
     * List user
     *
     * @param array $data Data
     *
     * @return User
     */
    public function listUser(array $data)
    {
        return $this->search($data['search'])
            ->orderBy($data['sortColumn'], $data['sortDirection'])
            ->orderBy('id', 'desc')
            ->paginate($data['sort']['per_page'], $data['columns']);
    }
}
