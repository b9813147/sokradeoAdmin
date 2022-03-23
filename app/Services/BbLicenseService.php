<?php

namespace App\Services;

use App\Repositories\BbLicenseRepository;

class BbLicenseService extends BaseService
{
    protected $repository;

    /**
     * @param BbLicenseRepository $repository
     */
    public function __construct(BbLicenseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     *
     * @param array $code
     * @param array $where
     * @param array $data
     * @return bool
     */
    public function createLicenseForGroup(array $code, array $where, array $data): bool
    {

        return $this->repository->createLicenseForGroup($code, $where, $data);
    }

    /**
     * @param string $code
     * @param array $where
     * @return bool
     */
    public function deleteLicenseForGroup(string $code, array $where): bool
    {
        return $this->repository->deleteLicenseForGroup($code, $where);
    }
}
