<?php

namespace App\Services;

use App\Repositories\RecordRepository;

class RecordService extends BaseService
{
    protected $repository;

    /**
     * RecordService constructor.
     * @param RecordRepository $repository
     */
    public function __construct(RecordRepository $repository)
    {
        $this->repository = $repository;
    }

}
