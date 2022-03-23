<?php

namespace App\Repositories;

use App\Models\QrCode;
use Illuminate\Database\Query\Builder;

class QrCodeRepository extends BaseRepository
{
    /**
     * @var QrCode
     * @var $model Builder
     */
    protected $model;

    /**
     * QrCodeRepository constructor.
     * @param QrCode $qrCode
     */
    public function __construct(QrCode $qrCode)
    {
        $this->model = $qrCode;
    }

    /**
     * 取得頻道 QrCode
     *
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getQrCode($group_id)
    {
        return $this->model->query()->where('group_id', $group_id)->get();
    }

}
