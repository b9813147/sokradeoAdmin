<?php

namespace App\Repositories;

use App\Models\GradeLang;
use Illuminate\Database\Query\Builder;

class GradeLangRepository extends BaseRepository
{
    /**
     * @var GradeLang
     * @var $model Builder
     */
    protected $model;

    /**
     * GradeLangRepository constructor.
     * @param $model
     */
    public function __construct(GradeLang $model)
    {
        $this->model = $model;
    }


}
