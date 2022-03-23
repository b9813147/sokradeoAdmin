<?php

namespace App\Repositories;

use App\Models\TbaCommentAttache;

class TbaCommentAttachmentRepository extends BaseRepository
{
    protected $model;

    /**
     * @param TbaCommentAttache $model
     */
    public function __construct(TbaCommentAttache $model)
    {
        $this->model = $model;
    }

}
