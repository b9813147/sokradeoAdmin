<?php

namespace App\Services;

use App\Repositories\ApplicationRepository;

class ApplicationService extends BaseService
{
    protected $repository;

    /**
     * @param ApplicationRepository $repository
     */
    public function __construct(ApplicationRepository $repository)
    {
        $this->repository = $repository;
    }

}
