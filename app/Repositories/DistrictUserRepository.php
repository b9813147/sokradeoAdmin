<?php

namespace App\Repositories;

use App\Models\DistrictUser;
use Illuminate\Database\Query\Builder;

class DistrictUserRepository extends BaseRepository
{
    /**
     * @var DistrictUser
     * @var $model Builder
     */
    protected $model;

    /**
     * DistrictUserRepository constructor.
     * @param $model
     */
    public function __construct(DistrictUser $model)
    {
        $this->model = $model;
    }

    /**
     * 學區使用者資訊
     * @param int $districtId
     * @param int $locale_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function districtUserInfo(int $districtId, int $locale_id)
    {
        return $this->model->query()->with([
            'user'         => function ($q) {
                $q->select('name', 'id', 'habook');
            },
            'districtLang' => function ($q) use ($locale_id) {
                $q->where('locales_id', $locale_id);
                $q->select('name', 'districts_id');
            }
        ])->where('districts_id', $districtId)->get();
    }

    /**
     * 學區使用者更新
     *
     * @param int $districtsId
     * @param int $userId
     * @param string $member_duty
     * @param string $member_status
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function updateDistrictUserInfo(int $districtsId, int $userId, string $member_duty, string $member_status, string $name)
    {
        $districtUserInfo = $this->model->query()->with('user', 'district')->where(['districts_id' => $districtsId, 'user_id' => $userId]);
        $districtUserInfo->update(['member_duty' => $member_duty, 'member_status' => $member_status]);
        $districtUserInfo->first()->user->update(['name' => $name]);

        return $districtUserInfo->first();
    }
}
