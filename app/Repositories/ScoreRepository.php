<?php

namespace App\Repositories;

use App\Models\Score;
use Yish\Generators\Foundation\Repository\Repository;

class ScoreRepository extends BaseRepository
{
    protected $model;

    /**
     * ScoreRepository constructor.
     * @param Score $model
     */
    public function __construct(Score $model)
    {
        $this->model = $model;
    }
}
