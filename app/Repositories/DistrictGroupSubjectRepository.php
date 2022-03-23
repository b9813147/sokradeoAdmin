<?php

namespace App\Repositories;

use App\Models\DistrictGroupSubject;
use Illuminate\Database\Query\Builder;

class DistrictGroupSubjectRepository extends BaseRepository
{
    /**
     * @var DistrictGroupSubject
     *  @var $model Builder
     */
    protected $model;

    /**
     * DistrictGroupSubjectRepository constructor.
     * @param $model
     */
    public function __construct(DistrictGroupSubject $model)
    {
        $this->model = $model;
    }

    /**
     * @param object $DistrictSubjectIds
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function districtSubjectList(object $DistrictSubjectIds)
    {
        return $this->model->query()->whereIn('district_subjects_id', $DistrictSubjectIds)->with([
            'districtSubject'   => function ($q) {
                $q->select('id', 'subject', 'order');
            },
            'groupSubjectField' => function ($q) {
                $q->select('id', 'alias');
            }
        ])->get();
    }

}
