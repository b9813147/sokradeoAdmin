<?php

namespace App\Repositories;

use App\Models\NotificationMessage;
use Yish\Generators\Foundation\Repository\Repository;

class NotificationMessageRepository extends BaseRepository
{
    protected $model;

    //

    /**
     * NotificationMessageRepository constructor.
     * @param NotificationMessage $model
     */
    public function __construct(NotificationMessage $model)
    {
        $this->model = $model;
    }
}
