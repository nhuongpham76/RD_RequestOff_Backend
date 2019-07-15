<?php
/**
 * AbstractRepository
 *
 * @author Phong Hunterist <phong@neo-lab.vn>
 */

namespace App\Repositories\Eloquent;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Container\Container as Application;
use App\Validators\Contracts\ValidatorInterface;
use App\Repositories\Contracts\RepositoryCriteriaInterface;
use App\Repositories\Contracts\CriteriaInterface;
use App\Repositories\Contracts\RepositoryInterface;

abstract class AbstractRepository implements RepositoryCriteriaInterface, RepositoryInterface
{

    /**
     * Application
     *
     * @var Application
     */
    protected $app;

    /**
     * Model
     *
     * @var Model
     */
    protected $model;

    /**
     * Field seachable
     *
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * Validator
     *
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = null;

    /**
     * Closure
     *
     * @var \Closure
     */
    protected $scopeQuery = null;

    /**
     * Allow validator
     *
     * @var bool
     */
    protected $allowValidator = true;
    
    /**
     * Collection of Criteria
     *
     * @var Collection
     */
    protected $criteria;

    /**
     * Skip criteria
     *
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * Contructor
     *
     * @param Application $app Application
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->criteria = new Collection();
        $this->makeModel();
        $this->makeValidator();
        $this->boot();
    }

    /**
     * Boot
     *
     * @return mixed
     */
    public function boot()
    {
        //Todo
    }

    /**
     * Reset Model
     *
     * @return Model
     *
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model();

    /**
     * Specify Validator class name of Prettus\Validator\Contracts\ValidatorInterface
     *
     * @return null
     *
     * @throws Exception
     */
    public function validator()
    {
        if (isset($this->rules) && !is_null($this->rules) && is_array($this->rules) && !empty($this->rules)) {
            if (class_exists(\App\Validators\LaravelValidator::class)) {
                $validator = app(\App\Validators\LaravelValidator::class);
                if ($validator instanceof ValidatorInterface) {
                    $validator->setRules($this->rules);
                    return $validator;
                }
            } else {
                throw new Exception(trans('repository::packages.prettus_laravel_validation_required'));
            }
        }
        return null;
    }

    /**
     * Get Model instace
     *
     * @return Model
     *
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());
        if (!$model instanceof Model) {
            throw new RepositoryException(
                "Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }
        return $this->model = $model;
    }

    /**
     * Get instance validator
     *
     * @param str|obj $validator Validator
     *
     * @return null|ValidatorInterface
     *
     * @throws RepositoryException
     */
    public function makeValidator($validator = null)
    {
        $validator = !is_null($validator) ? $validator : $this->validator();
        if (!is_null($validator)) {
            $this->validator = is_string($validator) ? $this->app->make($validator) : $validator;
            if (!$this->validator instanceof ValidatorInterface) {
                throw new RepositoryException(
                    "Class {$validator} must be an instance of App\\Validators\\Contracts\\ValidatorInterface"
                );
            }
            return $this->validator;
        }
        return null;
    }

    /**
     * Get Searchable Fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Search by basic where clause to the query.
     *
     * @param array  $searchData Data to search
     * @param string $searchAble Attribute name
     * @param bool   $isSearchOr Search "or"
     *
     * @return $this
     */
    public function search($searchData, string $searchAble = 'fieldSearchable', bool $isSearchOr = false)
    {
        $condition = $isSearchOr ? 'orWhere' : 'where';
        foreach ($searchData as $field => $value) {
            if (!empty($value)) {
                $searchable = $this->$searchAble[$field];
                if (!empty($searchable)) {
                    $column = array_key_exists('column', $searchable) ? $searchable['column'] : $field;
                    $operator = array_key_exists('operator', $searchable) ? $searchable['operator'] : '=';
                    $type = array_key_exists('type', $searchable) ? $searchable['type'] : 'normal';
                } else {
                    $column = $field;
                    $operator = '=';
                    $type = 'normal';
                }
                if (isset($searchable['column_type'])) {
                    $column = \DB::raw($column . '::' . $searchable['column_type']);
                }
                if ('in' == $operator) {
                    $value = is_string($value) ? explode(",", $value) : $value;
                    $value = array_filter($value, function ($element) {
                        return !(is_null($element) || $element === '');
                    });
                    if ($value) {
                        $this->model = $this->model->{$condition . 'In'}($column, $value);
                    }
                } else {
                    if ('date' == $type) {
                        $this->model = $this->model->{$condition . 'Date'}($column, $operator, $value);
                    } else {
                        if ('like' == $operator || 'ilike' == $operator) {
                            $value = '%' . $value . '%';
                        }
                        $this->model = $this->model->$condition($column, $operator, $value);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Sort by basic where clause to the query.
     *
     * @param array  $sortData   Data to sort
     * @param array  $sortFields Fields to sort
     * @param string $default    Default fieds
     *
     * @return $this
     */
    public function sort(array $sortData = [], array $sortFields = [], string $default = '')
    {
        $sortColumn = $sortData['sort_column'];

        if (!in_array($sortColumn, $sortFields)) {
            $sortColumn = $default;
        }

        if (isset($this->fieldSortable[$sortColumn])) {
            $sortable = $this->fieldSortable[$sortColumn];
        } else {
            $sortable['column'] = $sortColumn;
        }

        if (array_key_exists('column', $sortable)) {
            $sortColumn = $sortable['column'];
        }

        return $this->orderBy($sortColumn, $sortData['sort_direction'] ?? 'desc');
    }

    /**
     * Query Scope
     *
     * @param \Closure $scope Closure
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;
        return $this;
    }

    /**
     * Retrieve data array for populate field select
     *
     * @param string      $column Column
     * @param string|null $key    Key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function lists($column, $key = null)
    {
        $this->applyCriteria();
        return $this->model->lists($column, $key);
    }

    /**
     * Retrieve all data of repository
     *
     * @param array $columns Column
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        if ($this->model instanceof Builder) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }
        $this->resetModel();
        $this->resetScope();
        return $this->parserResult($results);
    }

    /**
     * Retrieve first data of repository
     *
     * @param array $columns Column
     *
     * @return mixed
     */
    public function first($columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $results = $this->model->first($columns);
        $this->resetModel();
        return $this->parserResult($results);
    }

    /**
     * First data by multiple fields and throw exception if not found
     *
     * @param array $where   List condition
     * @param array $columns Columns
     *
     * @return mixed
     */
    public function firstOrFailWhere(array $where, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyConditions($where);
        $model = $this->model->select($columns)->firstOrFail();
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param int    $limit   Limit data
     * @param array  $columns Column
     * @param string $method  Method execute
     * @param int    $page    Page numbers
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate", $page = 10)
    {
        $this->applyCriteria();
        $this->applyScope();
        $limit = is_null($limit) ? $page : $limit;
        $results = $this->model->{$method}($limit, $columns);
        $results->appends(app('request')->query());
        $this->resetModel();
        return $this->parserResultPaginate($results);
    }

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param int   $limit   Limit records
     * @param array $columns Column
     *
     * @return mixed
     */
    public function simplePaginate($limit = null, $columns = ['*'])
    {
        return $this->paginate($limit, $columns, "simplePaginate");
    }

    /**
     * Merge custom data to paginate response
     *
     * @param array $response Response data
     * @param array $custom   Custom data
     *
     * @return array
     */
    public function mergePaginate($response, $custom)
    {
        if (!empty($custom)) {
            $response = array_merge($response, $custom);
        }
        unset($response['data']);

        return $response;
    }

    /**
     * Find data by id
     *
     * @param int|str $id      Id
     * @param array   $columns Column
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->findOrFail($id, $columns);
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * Find data by field and value
     *
     * @param str   $field   Field
     * @param str   $value   Value
     * @param array $columns Column
     *
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where   List condition
     * @param array $columns Columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyConditions($where);
        $model = $this->model->get($columns);
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * Find data by multiple values in one field
     *
     * @param str   $field   Field
     * @param array $values  Value
     * @param array $columns Column
     *
     * @return mixed
     */
    public function findWhereIn($field, array $values, $columns = ['*'])
    {
        $model = $this->model->whereIn($field, $values)->get($columns);
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * First data by multiple fields
     *
     * @param array $where   List condition
     * @param array $columns Columns
     *
     * @return mixed
     */
    public function firstWhere(array $where, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyConditions($where);
        $model = $this->model->select($columns)->first();
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * Find data by excluding multiple values in one field
     *
     * @param str   $field   Field
     * @param array $values  Values
     * @param array $columns Columns
     *
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, $columns = ['*'])
    {
        $model = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * Save a new entity in repository
     *
     * @param array $attributes Data to create
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function create(array $attributes)
    {
        if (!is_null($this->validator) && $this->allowValidator) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.
            //$attributes = $this->model->newInstance()->forceFill($attributes)->toArray();
            $this->validator->with($attributes)->passesOrFail(ValidatorInterface::RULE_CREATE);
        }
        $model = $this->model->newInstance($attributes);
        $model->save();
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * Update a entity in repository by id
     *
     * @param array   $attributes Data
     * @param int|str $id         Id
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function update(array $attributes, $id)
    {
        $this->applyCriteria();
        $this->applyScope();
        if (!is_null($this->validator) && $this->allowValidator) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.
            $this->validator->with($attributes)->setId($id)->passesOrFail(ValidatorInterface::RULE_UPDATE);
        }
        $model = $this->model->findOrFail($id);
        $model->fill($attributes);
        $model->save();
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes Data
     * @param array $values     Value
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $this->applyCriteria();
        $this->applyScope();
        if (!is_null($this->validator)) {
            $this->validator->with($attributes)->passesOrFail(ValidatorInterface::RULE_UPDATE);
        }
        $model = $this->model->updateOrCreate($attributes, $values);
        $this->resetModel();
        return $this->parserResult($model);
    }

    /**
     * Delete a entity in repository by id
     *
     * @param int|str $id          Id
     * @param bool    $forceDelete Force delete
     *
     * @return int
     */
    public function delete($id, bool $forceDelete = false)
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->find($id);
        if ($forceDelete) {
            $deleted = $model->forceDelete();
        } else {
            $deleted = $model->delete();
        }
        $this->resetModel();
        return $deleted;
    }

    /**
     * Delete multiple entities by given id.
     *
     * @param array   $field Field
     * @param int|str $ids   Ids
     *
     * @return int
     */
    public function deletes($field, $ids)
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereIn($field, $ids);
        $this->resetModel();
        $deleted = $model->delete();
        return $deleted;
    }

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where List conditions
     *
     * @return int
     */
    public function deleteWhere(array $where)
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyConditions($where);
        $deleted = $this->model->delete();
        $this->resetModel();
        return $deleted;
    }

    /**
     * Check if entity has relation
     *
     * @param string $relation Relation
     *
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);
        return $this;
    }

    /**
     * Load relations
     *
     * @param array|string $relations Relation
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }

    /**
     * Load relation with closure
     *
     * @param string  $relation Relation
     * @param closure $closure  Closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);
        return $this;
    }

    /**
     * Set hidden fields
     *
     * @param array $fields Fields
     *
     * @return $this
     */
    public function hidden(array $fields)
    {
        $this->model->setHidden($fields);
        return $this;
    }

    /**
     * Order by
     *
     * @param str $column    Column
     * @param str $direction Direction
     *
     * @return \App\Repositories\Eloquent\AbstractRepository
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);
        return $this;
    }

    /**
     * Set visible fields
     *
     * @param array $fields Fields
     *
     * @return $this
     */
    public function visible(array $fields)
    {
        $this->model->setVisible($fields);
        return $this;
    }

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;
        return $this;
    }

    /**
     * Group by
     *
     * @param array|string $columns Columns
     *
     * @return \App\Repositories\Eloquent\AbstractRepository
     */
    public function groupBy($columns)
    {
        $this->model = $this->model->groupBy($columns);
        return $this;
    }
    
    /**
     * Having clause
     *
     * @param string|\Closure $column   Column
     * @param string          $operator Operator
     * @param mixed           $value    Value
     *
     * @return \App\Repositories\Eloquent\AbstractRepository
     */
    public function having($column, $operator, $value)
    {
        $this->model = $this->model->having($column, $operator, $value);
        return $this;
    }
    
    /**
     * Push Criteria for filter the query
     *
     * @param Criteria $criteria Criteria
     *
     * @return $this
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function pushCriteria($criteria)
    {
        if (is_string($criteria)) {
            $criteria = new $criteria;
        }
        if (!$criteria instanceof CriteriaInterface) {
            throw new RepositoryException(
                "Class " . get_class($criteria) . " must be an instance of CriteriaInterface"
            );
        }
        $this->criteria->push($criteria);
        return $this;
    }

    /**
     * Pop Criteria
     *
     * @param Criteria $criteria Criteria
     *
     * @return $this
     */
    public function popCriteria($criteria)
    {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }
            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }
            return get_class($item) === get_class($criteria);
        });
        return $this;
    }

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria CriteriaInterface
     *
     * @return mixed
     */
    public function getByCriteria(CriteriaInterface $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);
        $results = $this->model->get();
        $this->resetModel();
        return $this->parserResult($results);
    }

    /**
     * Skip Criteria
     *
     * @param bool $status Status
     *
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;
        return $this;
    }

    /**
     * Reset all Criterias
     *
     * @return $this
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();
        return $this;
    }

    /**
     * Apply criteria in current Query
     *
     * @return $this
     */
    protected function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }
        $criteria = $this->getCriteria();
        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof CriteriaInterface) {
                    $this->model = $c->apply($this->model, $this);
                }
            }
        }
        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope()
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }
        return $this;
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where List conditions
     *
     * @return void
     */
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * Wrapper result data
     *
     * @param mixed $result Data
     *
     * @return mixed
     */
    public function parserResult($result)
    {
        return $result;
    }

    /**
     * Parser result pagination
     *
     * @param Collection $result Result pagination
     *
     * @return array
     */
    public function parserResultPaginate($result)
    {
        $response = $result->toArray();
        $response['items'] = $response['data'];
        unset($response['data']);

        return $response;
    }

    /**
     * Set allow validate
     *
     * @param bool $allowValidate Check allow validator
     *
     * @return \App\Repositories\Eloquent\AbstractRepository
     */
    public function setAllowValidator($allowValidate)
    {
        $this->allowValidator = $allowValidate;
        return $this;
    }

    /**
     * Get allow validator
     *
     * @return mixed
     */
    public function getAllowValidator()
    {
        return $this->allowValidator;
    }

    /**
     * Insert data
     *
     * @param array $data Data
     *
     * @return bool
     */
    public function insert($data)
    {
        return $this->model->insert($data);
    }
    
    /**
     * Limit get data from query
     *
     * @param int $limit Limit
     *
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->model = $this->model->limit($limit);
        
        return $this;
    }

    /**
     * Get validator
     *
     * @return $this
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Chunk the results of the query.
     *
     * @param int      $limit    limit get
     * @param callable $callback Function call back
     *
     * @return bool
     *
     * @throws RepositoryException
     */
    public function chunk(int $limit, callable $callback)
    {
        $this->applyCriteria();
        $this->applyScope();

        $offset = 0;

        do {
            $results = $this->model->offset($offset)->limit($limit)->get();

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if (call_user_func($callback, $results) === false) {
                $this->resetModel();
                return false;
            }

            $offset += $limit;
        } while (true);

        $this->resetModel();
        return true;
    }
}
