<?php

namespace App\Services;

use App\Repositories\RatingRepository;

class RatingService extends BaseService
{
    protected $repository;

    /**
     * RatingService constructor.
     * @param RatingRepository $repository
     */
    public function __construct(RatingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $column
     * @param string $value
     * @param string $sort
     * @param string[] $columns
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getByForSort(string $column, string $value, $sort = 'DESC', $columns = ['*'])
    {
        return $this->repository->getByForSort($column, $value, $sort, $columns);
    }

    /**
     * @param array $where
     * @param string $sort
     * @param string[] $columns
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getByUseStatistics(array $where, string $sort = 'DESC', $columns = ['*'])
    {
        return $this->repository->getByUseStatistics($where, $sort, $columns);
    }

    /**
     * @param array $newIndex
     * @param array $oldIndex
     * @param int $group_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function changeSort(array $newIndex, array $oldIndex, int $group_id)
    {
        return $this->repository->changeSort($newIndex, $oldIndex, $group_id);
    }

    /**
     * @param array $newIndex
     * @param array $oldIndex
     * @param int $districtId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function changeSortByDistrict(array $newIndex, array $oldIndex, int $districtId)
    {
        return $this->repository->changeSortByDistrict($newIndex, $oldIndex, $districtId);
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function getTypeMax(array $where)
    {
        return $this->repository->getTypeMax($where);
    }
}
