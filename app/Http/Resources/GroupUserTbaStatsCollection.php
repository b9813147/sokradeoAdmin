<?php

namespace App\Http\Resources;

use App\Models\TbaAnnex;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateUser;
use App\Models\TbaStatistic;
use App\Services\SemestersService;
use Illuminate\Http\Resources\Json\ResourceCollection;
use PHPUnit\Exception;

/** @see \App\Models\Tba */
class GroupUserTbaStatsCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($q) {
            $q->name     = $q->name . '(' . $q->habook_id . ')';
            $tba_ids     = explode(',', $q->tba_ids);
            $q->tbaAnnex = $this->tbaAnnex($tba_ids);
//            $q->private_mark = $tbaEvaluateEvent->where('user_id', $q->user_id)->get()->count();
            $q->total_mark  = $this->tbaEvaluateUser()->where('tba_evaluate_users.identity', '!=', 'G')->whereIn('tba_evaluate_users.tba_id', $tba_ids)->get()->count();
            $q->public_mark = $this->tbaEvaluateUser()->where('tba_evaluate_users.user_id', $q->user_id)->get()->count();
            return $q;
        });
    }

    /**
     * 雙綠燈計算方式
     * @param $tba_ids
     * @return int
     */
    protected function tbaStatistics($tba_ids)
    {
        $tbaStatistics = TbaStatistic::query()->selectRaw("
        MAX(CASE WHEN type = 47 THEN CAST(idx AS signed) ELSE 0 END) AS T,
        MAX(CASE WHEN type = 48 THEN CAST(idx AS signed) ELSE 0 END) AS P,tba_id
        ")->whereIn('tba_id', $tba_ids)->groupBy('tba_id')->get();

        $result = 0;
        try {
            $tbaStatistics->each(function ($q) use (&$result) {
                if ($q->T >= 70 && $q->P >= 70) {
                    $result++;
                }
            });
            return $result;
        } catch (Exception $exception) {
            return $result;
        }
    }

    /**
     * @param $tba_ids
     * @return mixed
     */
    protected function tbaAnnex($tba_ids)
    {
        $select = "COUNT(CASE WHEN type = 'Material' THEN tba_id END)   AS material,
                   COUNT(CASE WHEN type = 'LessonPlan' THEN tba_id END) AS lessonPlan";
        return TbaAnnex::query()->selectRaw($select)->whereIn('tba_id', $tba_ids)->get()->first()->toArray();
    }

    // 計算 標記
    protected function tbaEvaluateUser()
    {
        return TbaEvaluateUser::query()->join('tba_evaluate_events as tee', 'tee.tba_evaluate_user_id', 'tba_evaluate_users.id');
    }
}
