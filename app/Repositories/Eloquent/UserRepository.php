<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use DB;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Field seachable
     *
     * @var array
     */
    protected $fieldSearchable = [
        'address_start_id' => ['column' => 'plans.address_start_id', 'operator' => '=', 'type' => 'normal'],
        'address_end_id' => ['column' => 'plans.address_end_id', 'operator' => '=', 'type' => 'normal'],
        'time_start' => ['column' => 'plans.time_start', 'operator' => '=', 'type' => 'date'],
    ];

    /**
     * List plan
     *
     * @param array $searchData Search data
     * @param array $sortData   Sort data
     * @param array $params     Parameter
     *
     * @return array
     */
    public function listUser(array $searchData, array $sortData, array $params = [])
    {
        $results = $this->queryGetPlan()
            ->search($searchData)
            ->orderBy($sortData['sort_column'], $sortData['sort_direction'])
            ->all($this->getColumnsForList());

        $results = $results->toArray();

        $this->updateDataResult($results);

        return $results;
    }
}
