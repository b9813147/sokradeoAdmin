<?php

namespace App\Services;

use App\Models\GradeLang;
use App\Repositories\BaseRepository;
use App\Repositories\GradeLangRepository;
use Yish\Generators\Foundation\Service\Service;

class GradeLangService extends Service
{
    protected $repository;

    /**
     * GradeLangService constructor.
     * @param GradeLangRepository $gradeLangRepository
     */
    public function __construct(GradeLangRepository $gradeLangRepository)
    {
        $this->repository = $gradeLangRepository;
    }


}
