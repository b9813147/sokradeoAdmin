<?php


namespace App\Services;


use Yish\Generators\Foundation\Service\Service;

abstract class BaseService extends Service
{

    protected $repository;

    /**
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function findWhere(array $where = [], array $columns = ['*'])
    {
        return $this->repository->findWhere($where, $columns);
    }

    /**
     * @param string $column
     * @param array $values
     * @param array|string[] $columns
     * @return mixed
     */
    public function findWhereIn(string $column, array $values = [], array $columns = ['*'])
    {
        return $this->repository->findWhereIn($column, $values, $columns);
    }

    /**
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function firstWhere(array $where = [], array $columns = ['*'])
    {
        return $this->repository->firstWhere($where, $columns);
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function deleteWhere(array $where = [])
    {
        return $this->repository->deleteWhere($where);
    }

    /**
     * @param array $where
     * @param array $attributes
     * @return mixed
     */
    public function updateWhere(array $where, array $attributes)
    {
        return $this->repository->updateWhere($where, $attributes);
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->repository->updateOrCreate($attributes, $values);
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function updateOrInsert(array $attributes, array $values = [])
    {
        return $this->repository->updateOrInsert($attributes, $values);
    }

    /**
     * @param int $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function find($id, array $columns = ['*'])
    {
        return $this->repository->find($id, $columns);
    }

    /**
     * @param array $where
     * @return bool
     */
    public function exists(array $where = []): bool
    {
        return $this->repository->exists($where);
    }

    /**
     * 調整排序
     * @param string $method
     * @param array $where
     * @param string $column
     * @param int $order
     * @param int $new_order
     * @return bool
     */
    public function adjustSort(string $method, array $where, string $column, int $order, int $new_order): bool
    {
        return $this->repository->adjustSort($method, $where, $column, $order, $new_order);
    }
}
