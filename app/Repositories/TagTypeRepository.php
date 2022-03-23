<?php

namespace App\Repositories;

use App\Models\TagType;

class TagTypeRepository extends BaseRepository
{
    protected $model;

    /**
     * @param TagType $model
     */
    public function __construct(TagType $model)
    {
        $this->model = $model;
    }


}
