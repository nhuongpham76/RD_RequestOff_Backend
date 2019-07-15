<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface RepositoryCriteriaInterface
{

    /**
     * Push Criteria for filter the query
     *
     * @param mixed $criteria Criteria
     *
     * @return $this
     */
    public function pushCriteria($criteria);

    /**
     * Pop Criteria
     *
     * @param mixed $criteria Criteria
     *
     * @return $this
     */
    public function popCriteria($criteria);

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria();

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria CriteriaInterface
     *
     * @return mixed
     */
    public function getByCriteria(CriteriaInterface $criteria);

    /**
     * Skip Criteria
     *
     * @param bool $status Status
     *
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * Reset all Criterias
     *
     * @return $this
     */
    public function resetCriteria();
}
