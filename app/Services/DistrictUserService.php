<?php

namespace App\Services;

use App\Repositories\DistrictUserRepository;
use App\Types\Group\DutyType;
use Yish\Generators\Foundation\Service\Service;

class DistrictUserService extends BaseService
{
    protected $repository;

    /**
     * DistrictUserService constructor.
     * @param $repository
     */
    public function __construct(DistrictUserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $districtId
     * @param int $userId
     * @param int $member_status
     * @return mixed
     */
    public function getDistrictUser(int $districtId, int $userId, int $member_status = 1)
    {
        return $this->repository->findWhere(['districts_id' => $districtId, 'user_id' => $userId, 'member_status' => $member_status, 'member_duty' => DutyType::Admin])->first();
    }

    /**
     *
     * @param int $districtId
     * @param int $locale_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getDistrictUserInfo(int $districtId,int $locale_id)
    {
        return $this->repository->districtUserInfo($districtId,$locale_id);
    }

    /**
     * @param int $districtsId
     * @param int $userId
     * @param string $member_duty
     * @param string $member_status
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function updateDistrictUserInfo(int $districtsId, int $userId, string $member_duty, string $member_status, string $name)
    {
        return $this->repository->updateDistrictUserInfo($districtsId, $userId, $member_duty, $member_status, $name);
    }
}
