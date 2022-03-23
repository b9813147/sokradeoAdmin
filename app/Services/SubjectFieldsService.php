<?php

namespace App\Services;

use App\Repositories\SubjectFieldsRepository;
use Yish\Generators\Foundation\Service\Service;

class SubjectFieldsService extends Service
{
    /**
     * @var SubjectFieldsRepository
     */
    protected $repository;

    /**
     * SubjectFieldsService constructor.
     * @param SubjectFieldsRepository $subjectFieldsRepository
     */
    public function __construct(SubjectFieldsRepository $subjectFieldsRepository)
    {
        $this->repository = $subjectFieldsRepository;
    }

    public function getAreaLang()
    {
        return $this->repository->getAreaLang();
    }
}
