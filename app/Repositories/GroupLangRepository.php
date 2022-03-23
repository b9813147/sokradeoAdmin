<?php

namespace App\Repositories;


use App\Models\GroupLang;
use Illuminate\Database\Query\Builder;

class GroupLangRepository extends BaseRepository
{
    /**
     *
     * @var GroupLang
     * @var $model Builder
     */
    protected $model;

    /**
     * GroupLangRepository constructor.
     * @param $model
     */
    public function __construct(GroupLang $model)
    {
        $this->model = $model;
    }
}
