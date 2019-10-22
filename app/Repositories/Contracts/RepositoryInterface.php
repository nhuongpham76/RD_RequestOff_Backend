<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{

    /**
     * Retrieve data array for populate field select
     *
     * @param string      $column Column
     * @param string|null $key    Key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function lists($column, $key = null);

    /**
     * Retrieve all data of repository
     *
     * @param array $columns Column
     *
     * @return mixed
     */
    public function all($columns = ['*']);

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
    public function paginate($limit = null, $columns = ['*'], $method = "paginate", $page = 10);

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param int   $limit   Limit records
     * @param array $columns Column
     *
     * @return mixed
     */
    public function simplePaginate($limit = null, $columns = ['*']);

    /**
     * Find data by id
     *
     * @param int|str $id      Id
     * @param array   $columns Column
     *
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * Find data by field and value
     *
     * @param str   $field   Field
     * @param str   $value   Value
     * @param array $columns Column
     *
     * @return mixed
     */
    public function findByField($field, $value, $columns = ['*']);

    /**
     * Find data by multiple fields
     *
     * @param array $where   List condition
     * @param array $columns Columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*']);

    /**
     * Find data by multiple values in one field
     *
     * @param str   $field   Field
     * @param array $values  Value
     * @param array $columns Column
     *
     * @return mixed
     */
    public function findWhereIn($field, array $values, $columns = ['*']);

    /**
     * Find data by excluding multiple values in one field
     *
     * @param str   $field   Field
     * @param array $values  Values
     * @param array $columns Columns
     *
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, $columns = ['*']);

    /**
     * Save a new entity in repository
     *
     * @param array $attributes Data to create
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function create(array $attributes);

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
    public function update(array $attributes, $id);

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
    public function updateOrCreate(array $attributes, array $values = []);

    /**
     * Delete a entity in repository by id
     *
     * @param int|str $id          Id
     * @param bool    $forceDelete Force delete
     *
     * @return int
     */
    public function delete($id, bool $forceDelete = false);

    /**
     * Order by
     *
     * @param str $column    Column
     * @param str $direction Direction
     *
     * @return \App\Repositories\Eloquent\AbstractRepository
     */
    public function orderBy($column, $direction = 'asc');

    /**
     * Load relations
     *
     * @param array|string $relations Relation
     *
     * @return $this
     */
    public function with($relations);

    /**
     * Load relation with closure
     *
     * @param string  $relation Relation
     * @param closure $closure  Closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure);

    /**
     * Set hidden fields
     *
     * @param array $fields Fields
     *
     * @return $this
     */
    public function hidden(array $fields);

    /**
     * Set visible fields
     *
     * @param array $fields Fields
     *
     * @return $this
     */
    public function visible(array $fields);

    /**
     * Query Scope
     *
     * @param \Closure $scope Closure
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope);

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope();

    /**
     * Get Searchable Fields
     *
     * @return array
     */
    public function getFieldsSearchable();
}
