<?php
namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    /**
     * List user
     *
     * @param array $searchData Search data
     * @param array $sortData   Sort data
     * @param array $params     Parameter
     *
     * @return array
     */
    public function listUser(array $searchData, array $sortData, array $params = []);
}
