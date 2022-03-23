<?php

namespace App\Services;

use App\Repositories\TbaStatisticRepository;

class TbaStatisticService extends BaseService
{
    protected $repository;

    /**
     * @param TbaStatisticRepository $repository
     */
    public function __construct(TbaStatisticRepository $repository)
    {
        $this->repository = $repository;
    }

}
