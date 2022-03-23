<?php

namespace App\Services;

use App\Repositories\ScoreRepository;

class ScoreService extends BaseService
{
    protected $repository;

    /**
     * ScoreService constructor.
     * @param ScoreRepository $repository
     */
    public function __construct(ScoreRepository $repository)
    {
        $this->repository = $repository;
    }
}
