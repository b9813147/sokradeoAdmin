<?php

namespace App\Repositories;


use App\Models\Record;

class RecordRepository extends BaseRepository
{
    protected $model;

    /**
     * RecordRepository constructor.
     * @param $model
     */
    public function __construct(Record $model)
    {
        $this->model = $model;
    }

}
