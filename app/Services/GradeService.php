<?php

namespace App\Services;

use App\Repositories\GradeRepository;
use Yish\Generators\Foundation\Service\Service;

class GradeService extends Service
{
    protected $repository;

    public function __construct(GradeRepository $gradeRepository)
    {
        $this->repository = $gradeRepository;
    }
}
