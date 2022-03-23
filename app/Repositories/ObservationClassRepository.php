<?php

namespace App\Repositories;

use App\Models\ObservationClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ObservationClassRepository extends BaseRepository
{
    protected $model;

    public function __construct(ObservationClass $observationClass)
    {
        $this->model = $observationClass;
    }

    /**
     * 取得課程資訊及使用者
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function getObservationClassWithUsers($id)
    {
        return $this->model->query()->with([
            'observationUsers' => function ($q) {
                $q->with('user');
            }
        ])->findOrFail($id);
    }
}
