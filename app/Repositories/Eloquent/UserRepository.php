<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\Traits\EloquentTransactional;
use App\Validators\Exceptions\ValidatorException;
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
        'phone' => ['column' => 'users.phone', 'operator' => '=', 'type' => 'normal'],
        'role_id' => ['column' => 'users.role_id', 'operator' => '=', 'type' => 'normal'],
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
        $validator = app('validator')->make($data, $this->validator->getRules(UserValidator::RULE_CREATE));

        if ($validator->fails()) {
            throw new ValidatorException($validator->errors());
        }

        $data['password'] = $data['password_encrypted'];
        $this->setAllowValidator(false);

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
        $validator = app('validator')->make($data, $this->validator->getRules(UserValidator::RULE_CREATE));

        if ($validator->fails()) {
            throw new ValidatorException($validator->errors());
        }

        $data['password'] = $data['password_encrypted'];
        $this->setAllowValidator(false);

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
                'users.role_id',
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
            ->with($this->getDataRelation())
            ->orderBy($data['sortColumn'], $data['sortDirection'])
            ->orderBy('id', 'desc')
            ->paginate($data['sort']['per_page'], $data['columns']);
    }

    /**
     * Get data relation with user.
     *
     * @return array
     */
    private function getDataRelation()
    {
        $with['team'] = function ($query) {
            return $query->select([
                'teams.id',
                'teams.name',
            ]);
        };

        $with['role'] = function ($query) {
            return $query->select([
                'roles.id',
                'roles.name',
            ]);
        };

        return $with;
    }
}
