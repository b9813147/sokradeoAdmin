<?php

namespace App\Services;

use App\Repositories\NotificationMessageRepository;
use Yish\Generators\Foundation\Service\Service;

class NotificationMessageService extends BaseService
{
    protected $repository;

    /**
     * NotificationMessageService constructor.
     * @param NotificationMessageRepository $repository
     */
    public function __construct(NotificationMessageRepository $repository)
    {
        $this->repository = $repository;
    }

}
