<?php

namespace App\Repositories;

use App\Models\Semester;
use Carbon\Carbon;
use Yish\Generators\Foundation\Repository\Repository;

class SemestersRepository extends BaseRepository
{
    protected $model;

    /**
     * SemestersRepository constructor.
     * @param $model
     */
    public function __construct(Semester $model)
    {
        $this->model = $model->query();
    }

    /**
     * 當前學期
     * @param int $group_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function currentSemester(int $group_id)
    {
        return $this->model->select('month', 'day')->where('group_id', $group_id)->get();
    }
}
