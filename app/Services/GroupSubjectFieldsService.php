<?php

namespace App\Services;

use App\Helpers\Custom\GlobalPlatform;
use App\Repositories\GroupSubjectFieldsRepository;

class GroupSubjectFieldsService extends BaseService
{
    /**
     * @var GroupSubjectFieldsRepository
     */
    protected $repository;

    /**
     * GroupSubjectFieldsService constructor.
     * @param GroupSubjectFieldsRepository $groupSubjectFieldsRepository
     */
    public function __construct(GroupSubjectFieldsRepository $groupSubjectFieldsRepository)
    {
        $this->repository = $groupSubjectFieldsRepository;
    }

    /**
     *  取得學科資料與學科欄位
     *
     * @param int $groupsId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getSubject(int $groupsId)
    {
        return $this->repository->getSubject($groupsId);
    }

    /**
     * @param int $district
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getGroupSubjects(int $district)
    {
        $DistrictFindGroupIds = GlobalPlatform::DistrictFindGroupIds($district);
        return $this->repository->groupSubjects($DistrictFindGroupIds);
    }
}
