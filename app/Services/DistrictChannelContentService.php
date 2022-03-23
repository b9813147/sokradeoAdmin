<?php

namespace App\Services;

use App\Repositories\DistrictChannelContentRepository;
use Yish\Generators\Foundation\Service\Service;

class DistrictChannelContentService extends BaseService
{
    protected $repository;

    /**
     * DistrictChannelContentService constructor.
     * @param $repository
     */
    public function __construct(DistrictChannelContentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 學區課例分類
     * @param int $districtId
     * @param int $localesId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getDistrictChannelContent(int $districtId, int $localesId)
    {
        return $this->repository->districtChannelContentInfo($districtId, $localesId);
    }

    /**
     * @param int $districtId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getDistrictChannelSubjectInfo(int $districtId)
    {
        return $this->repository->districtChannelSubjectInfo($districtId);
    }
}
