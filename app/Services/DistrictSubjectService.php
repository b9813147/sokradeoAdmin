<?php

namespace App\Services;

use App\Repositories\DistrictSubjectRepository;
use Yish\Generators\Foundation\Service\Service;

class DistrictSubjectService extends BaseService
{
    protected $repository;

    /**
     * DistrictSubjectService constructor.
     * @param $repository
     */
    public function __construct(DistrictSubjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $districtId
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getDistrictSubjectList(int $districtId, $order = 'DESC')
    {
        return $this->repository->districtSubjectList($districtId, $order);
    }

    /**
     * * 取最大值Order
     *
     * @param int $districtId
     * @return int|mixed
     */
    public function getMaxOrder(int $districtId): int
    {
        return $this->repository->getMaxOrder($districtId);
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

}
