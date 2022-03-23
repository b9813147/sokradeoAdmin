<?php

namespace App\Repositories;

use App\Models\Application;

class ApplicationRepository extends BaseRepository
{
    protected $model;

    /**
     * @param Application $model
     */
    public function __construct(Application $model)
    {
        $this->model = $model;
    }

}
