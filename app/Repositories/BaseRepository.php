<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Yish\Generators\Foundation\Repository\Repository;

abstract class BaseRepository extends Repository
{
    /**
     * @var $model Builder
     */
    protected $model;

    /**
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function findWhere(array $where = [], array $columns = ['*'])
    {
        return $this->model->where($where)->get($columns);
    }

    /**
     * @param string $column
     * @param array $values
     * @param array $columns
     * @return mixed
     */
    public function findWhereIn(string $column, array $values = [], array $columns = ['*'])
    {
        return $this->model->whereIn($column, $values)->get($columns);
    }

    /**
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function firstWhere(array $where = [], array $columns = ['*'])
    {
        return $this->model->where($where)->first($columns);
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function deleteWhere(array $where = [])
    {
        return $this->model->where($where)->delete();
    }

    /**
     * @param array $where
     * @param array $attributes
     * @return mixed
     */
    public function updateWhere(array $where, array $attributes)
    {
        return $this->model->where($where)->update($attributes);
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function updateOrInsert(array $attributes, array $values = [])
    {
        return $this->model->updateOrInsert($attributes, $values);
    }

    /**
     * @param array $where
     * @return bool
     */
    public function exists(array $where = []): bool
    {
        return $this->model->where($where)->exists();
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
        $isSuccessful = true;
        try {
            switch ($method) {
                case 'create':
                    $this->model->where($where)->where($column, '>=', $new_order)->increment($column);
                    break;
                case 'update':
                    if ($order > $new_order) {
                        $this->model->where($where)->where($column, '>', 0)->whereBetween($column, [$new_order, $order])->increment($column);
                    }
                    if ($order < $new_order) {
                        $this->model->where($where)->where($column, '>', 0)->whereBetween($column, [$order, $new_order])->decrement($column);
                    }
                    break;
                case 'destroy':
                    $this->model->where($where)->where($column, '>', $order)->decrement($column);
                    break;
            }
        } catch (\Exception $exception) {
            $isSuccessful = false;
        }
        return $isSuccessful;
    }

}
