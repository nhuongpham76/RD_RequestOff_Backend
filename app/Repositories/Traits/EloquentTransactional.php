<?php

namespace App\Repositories\Traits;

use Closure;

trait EloquentTransactional
{

    /**
     * Resolve Database Connection
     *
     * @return mixed
     */
    protected function resolveDatabase()
    {
        return app('db');
    }

    /**
     * Begin Database Transaction
     *
     * @return mixed
     */
    public function beginTransaction()
    {
        $database = $this->resolveDatabase();
        $database->beginTransaction();
    }

    /**
     * Rollback Database Transaction
     *
     * @return mixed
     */
    public function rollback()
    {
        $database = $this->resolveDatabase();
        $database->rollback();
    }

    /**
     * Commit Database Transaction
     *
     * @return mixed
     */
    public function commit()
    {
        $database = $this->resolveDatabase();
        $database->commit();
    }

    /**
     * Callback Database Transaction
     *
     * @param Closure $closure Closure transaction
     *
     * @return mixed
     */
    public function transaction(Closure $closure)
    {
        $database = $this->resolveDatabase();
        return $database->transaction($closure);
    }
}
