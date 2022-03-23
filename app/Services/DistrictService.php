<?php

namespace App\Services;

use App\Repositories\DistrictRepository;
use Yish\Generators\Foundation\Service\Service;

class DistrictService extends Service
{
    protected $repository;

    /**
     * DistrictService constructor.
     * @param $repository
     */
    public function __construct(DistrictRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * 學區資訊
     * @param int $districtId
     * @param int $localesId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getDistrictInfo(int $districtId, int $localesId)
    {
        return $this->repository->districtInfo($districtId, $localesId);
    }

    /**
     * 取全部學區資訊
     * @param int $localesId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getDistrictInfoAll(int $localesId)
    {
        return $this->repository->districtInfoAll($localesId);
    }
}
