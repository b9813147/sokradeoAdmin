<?php

namespace App\Services;

use App\Repositories\TbaAnalysisEventRepository;

class TbaAnalysisEventService extends BaseService
{
    protected $repository;

    public function __construct(TbaAnalysisEventRepository $analysisEventRepository)
    {
        $this->repository = $analysisEventRepository;
    }

    /**
     * @param int $tbaId
     * @param $groups
     * @return bool
     */
    public function createEventGroups(int $tbaId, &$groups): bool
    {
        return $this->repository->createEventGroups($tbaId, $groups);
    }
}
