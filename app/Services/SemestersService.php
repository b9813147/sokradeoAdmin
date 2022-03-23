<?php

namespace App\Services;

use App\Repositories\SemestersRepository;

class SemestersService extends BaseService
{
    protected $repository;

    /**
     * SemestersService constructor.
     * @param SemestersRepository $SemestersRepository
     */
    public function __construct(SemestersRepository $SemestersRepository)
    {
        $this->repository = $SemestersRepository;
    }

    /**
     * 當前學期
     * @param int $group_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function currentSemester(int $group_id)
    {
        return $this->repository->currentSemester($group_id);
    }

}
