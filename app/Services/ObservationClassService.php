<?php

namespace App\Services;

use App\Repositories\ObservationClassRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ObservationClassService extends BaseService
{
    protected $repository;

    public function __construct(ObservationClassRepository $observationClassRepository)
    {
        $this->repository = $observationClassRepository;
    }

    /**
     * 取得課程資訊及使用者
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function getObservationClassWithUsers($id)
    {
        return $this->repository->getObservationClassWithUsers($id);
    }
}
