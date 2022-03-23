<?php

namespace App\Services;

use App\Repositories\DistrictGroupSubjectRepository;
use Yish\Generators\Foundation\Service\Service;

class DistrictGroupSubjectService extends BaseService
{
    protected $repository;

    /**
     * DistrictGroupSubjectService constructor.
     * @param $repository
     */
    public function __construct(DistrictGroupSubjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param object $DistrictSubjectIds
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getDistrictSubjectList(object $DistrictSubjectIds)
    {
        return $this->repository->districtSubjectList($DistrictSubjectIds);
    }
}
