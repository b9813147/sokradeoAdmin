<?php

namespace App\Repositories;

use App\Models\Districts;
use Illuminate\Database\Query\Builder;

class DistrictRepository extends BaseRepository
{
    /**
     * @var Districts
     * @var $model Builder
     */
    protected $model;

    /**
     * DistrictRepository constructor.
     * @param $model
     */
    public function __construct(Districts $model)
    {
        $this->model = $model;
    }

    /**
     * 學區資訊
     * @param int $districtId
     * @param int $localesId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function districtInfo(int $districtId, int $localesId)
    {
        return $this->model->query()->with([
            'districtLang' => function ($q) use ($localesId) {
                $q->where('locales_id', $localesId);
            },
            'districtUser'
        ])->findOrFail($districtId);
    }

    /**
     * 學區資訊
     * @param int $localesId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function districtInfoAll(int $localesId)
    {
        return $this->model->query()->with([
            'districtLang' => function ($q) use ($localesId) {
                $q->where('locales_id', $localesId);
            },
            'districtUser'
        ])->get();
    }
}
