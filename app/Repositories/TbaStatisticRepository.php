<?php

namespace App\Repositories;

use App\Models\TbaStatistic;
use App\Types\Tba\StatisticType;
use Illuminate\Support\Facades\DB;

class TbaStatisticRepository extends BaseRepository
{
    protected $model;

    /**
     * @param TbaStatistic $model
     */
    public function __construct(TbaStatistic $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $tbaId
     * @param $stats
     * @return bool
     */
    public function createStats(int $tbaId, $stats): bool // 註:因為此功能會修改stats值 故該參數不使用參考
    {
        $isSuccessful = true;
        $timestamp    = date("Y-m-d H:i:s");

        try {
            $this->model->query()->where('tba_id', $tbaId)->delete();
            foreach ($stats as $i => $v) {
                $stats[$i]['tba_id']     = $tbaId;
                $stats[$i]['type']       = StatisticType::getConstant($v['type']);
                $stats[$i]['created_at'] = $timestamp;
                $stats[$i]['updated_at'] = $timestamp;
            }
            // 排除不存在的Type
            $stats = collect($stats)->where('type', '!=', false)->toArray();
            $this->model->query()->insert($stats);
        } catch (\Exception $exception) {
            $isSuccessful = false;
        }

        return $isSuccessful;
    }


}
