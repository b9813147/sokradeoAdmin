<?php

namespace App\Repositories;

use App\Models\Grade;
use Illuminate\Database\Query\Builder;

class GradeRepository extends BaseRepository
{
    /**
     * @var Grade
     * @var $model Builder
     */
    protected $model;

    /**
     * GradeRepository constructor.
     * @param $model
     */
    public function __construct(Grade $model)
    {
        $this->model = $model;
    }

}
