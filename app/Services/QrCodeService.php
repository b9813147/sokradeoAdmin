<?php

namespace App\Services;

use App\Repositories\QrCodeRepository;
use Yish\Generators\Foundation\Service\Service;

class QrCodeService extends Service
{
    protected $repository;

    /**
     * QrCodeService constructor.
     * @param $repository
     */
    public function __construct(QrCodeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 取得頻道 QrCode
     *
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getQrCode($group_id)
    {
        return $this->repository->getQrCode($group_id);
    }


}
